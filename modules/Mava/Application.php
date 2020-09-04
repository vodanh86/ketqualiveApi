<?php
/**
 * Created by JetBrains PhpStorm.
 * User: thend
 * Date: 3/14/14
 * Time: 10:25 PM
 * To change this template use File | Settings | File Templates.
 */

class Mava_Application {

    protected $_initialized = false;
    protected $_running = false;

    protected $_config = array();

    public static $_controller;

    public static $_action;

    public static $_addon_controller;

    public static $_addon_action;

    public static $_view;

    public static $_view_params = array();

    protected static $_debug;

    public static $time = 0;

    public static $host = 'localhost';

    public static $secure = false;

    public static $externalDataPath = 'data';

    protected static $_memoryLimit = null;

    protected static $_classCache = array();

    protected static $_initConfig = array(
        'undoMagicQuotes' => true,
        'setMemoryLimit' => true,
        'resetOutputBuffering' => true
    );

    public function __construct()
    {
    }

    public function showDebugOutput()
    {
        return (Mava_Url::getParam('_debug') && Mava_Application::debugMode());
    }

    public static function parseQueryString($string)
    {
        parse_str($string, $output);
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
        {
            Mava_Application::undoMagicQuotes($output);
        }
        return $output;
    }

    public function run(){
        if($this->_running){
            return;
        }

        self::get('session')->start();

        $showDebugOutput = $this->showDebugOutput();

        $requestControllerAction = Mava_Router::getControllerAction();
        if(!$requestControllerAction){
            throw new Mava_Exception("No response from Mava_Router::getControllerAction");
        }else if (!($requestControllerAction instanceof Mava_ControllerResponse_Abstract))
        {
            throw new Mava_Exception("Invalid response from Mava_Router::getControllerAction");
        }

        self::$_controller = $requestControllerAction->controllerName;
        self::$_action = $requestControllerAction->controllerAction;

        if(class_exists($requestControllerAction->controllerName)){
            // call action to get view
            $requestControllerAction->controllerName = Mava_Application::resolveDynamicClass($requestControllerAction->controllerName, 'controller');
            Mava_Event::fire('before_load_controller',array(&$requestControllerAction->controllerName));

            $controller = new $requestControllerAction->controllerName();

            if(!method_exists($controller,$requestControllerAction->controllerAction)){
                if(!method_exists($controller,'indexAction')){
                    throw new Mava_Exception("indexAction not found in ". $requestControllerAction->controllerName ." controller");
                }else{
                    $action = 'indexAction';
                }
            }else{
                $action = $requestControllerAction->controllerAction;
            }

            if(method_exists($controller, '_setup')){
                $setupResponse = $controller->_setup();
            }else{
                $setupResponse = false;
            }

            Mava_Event::fire('before_call_action',array($requestControllerAction->controllerName,&$action));

            self::$_addon_controller = $requestControllerAction->controllerName;
            self::$_addon_action = $action;

            if($setupResponse){
                $controllerResponse = $setupResponse;
            }else{
                $controllerResponse = $controller->$action();
            }
            if (!$controllerResponse)
            {
                throw new Mava_Exception("No controller response from ". $requestControllerAction->controllerName ."::". $action);
            }
            else if (!($controllerResponse instanceof Mava_ControllerResponse_Abstract))
            {
                throw new Mava_Exception("Invalid controller response from ". $requestControllerAction->controllerName ."::". $action);
            }

            if($controllerResponse instanceof Mava_ControllerResponse_View){
                $view = new Mava_View();
                if($controllerResponse->layout!=""){
                    $view->setLayout($controllerResponse->layout);
                }

                self::$_view = $controllerResponse->viewName;
                self::$_view_params = $controllerResponse->params;

                /* check admin */
                if($controller instanceof Mava_AdminController){
                    $visitor = Mava_Visitor::getInstance();
                    if(!$visitor->hasPermission('view_admin_page')){
                        $controllerResponse->viewName = 'Admin_View_AccessDenied';
                        $controllerResponse->params = array();
                        $controllerResponse->layout = 'blank';
                        Mava_Application::set('seo/title',__('access_denied'));
                        $view->setLayout($controllerResponse->layout);
                    }
                }
                $pageContent = $view->getView($controllerResponse->viewName,$controllerResponse->params);
                if ($showDebugOutput)
                {
                    $pageContent = $this->renderDebugOutput();
                    $view->setLayout('debug');
                }
                $view->setPageContent($pageContent);
                $view->deploy();
            }else if($controllerResponse instanceof Mava_ControllerResponse_Json){
                @header("Content-Type: application/json");
                /* check admin */
                if($controller instanceof Mava_AdminController){
                    $visitor = Mava_Visitor::getInstance();
                    if(!$visitor->hasPermission('view_admin_page')){
                        $controllerResponse->data = array(
                            'status' => -1,
                            'message' => __('access_denied')
                        );
                    }
                }
                echo json_encode($controllerResponse->data);
            }else if($controllerResponse instanceof Mava_ControllerResponse_Redirect){
                if($controllerResponse->redirectLink!=""){
                    $redirectUrl = $controllerResponse->redirectLink;
                }else{
                    $redirectUrl = Mava_Url::getDomainUrl();
                }
                @header("Location: ". $redirectUrl);
            }
        }else{
            throw new Mava_Exception('Controller not found ('. $requestControllerAction->controllerName .')');
        }
        $this->_running = true;
    }

    public function renderDebugOutput()
    {
        return Mava_Debug::getDebugPageWrapperHtml(
            Mava_Debug::getDebugHtml()
        );
    }

    public function initApplication($configFile, $envFile){
        global $_ENV;
        if($this->_initialized){
            return;
        }

        if(Mava_Session::get('reference_url') == '' && isset($_SERVER["HTTP_REFERER"])){
            Mava_Session::set('reference_url', $_SERVER["HTTP_REFERER"]);
        }

        if (self::$_initConfig['undoMagicQuotes'] && function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
        {
            self::undoMagicQuotes($_GET);
            self::undoMagicQuotes($_POST);
            self::undoMagicQuotes($_COOgKIE);
            self::undoMagicQuotes($_REQUEST);
        }
        if (function_exists('get_magic_quotes_runtime') && get_magic_quotes_runtime())
        {
            @set_magic_quotes_runtime(false);
        }

        if (self::$_initConfig['setMemoryLimit'])
        {
            self::setMemoryLimit(128 * 1024 * 1024);
        }

        ignore_user_abort(true);

        if (self::$_initConfig['resetOutputBuffering'])
        {
            @ini_set('output_buffering', false);

            if (!@ini_get('output_handler'))
            {
                $level = ob_get_level();
                while ($level)
                {
                    ob_end_clean();
                    $newLevel = ob_get_level();
                    if ($newLevel >= $level)
                    {
                        break;
                    }
                    $level = $newLevel;
                }
            }
        }

        error_reporting(E_ALL | E_STRICT & ~8192);

        set_error_handler(array("Mava_Error","handlePhpError"));
        set_exception_handler(array("Mava_Error","handleException"));
        register_shutdown_function(array("Mava_Error","handleFatalError"));

        if(file_exists($configFile)){
            $config = array();
            require($configFile);
            if(is_array($config)){
                $this->_config = $config;
                $_ENV['config'] = $config;
            }
        }else{
            throw new Mava_Exception("Couldn't load config file (". $configFile .")");
        }
	
	if(file_exists($envFile)){
	    $env = array();
	    require($envFile);
	    if(is_array($env)){
	        $this_config = array_merge($this->_config, $env);
	        $_ENV['config'] = array_merge($_ENV['config'], $env);
	    }else{
	        throw new Mava_Exception("Couldn't load env file (". $envFile .")");
	    }
	}

        /* session */
        $sessionClass = self::resolveDynamicClass('Mava_Session');
        $_ENV['session'] = new $sessionClass();

        /* init request */
        Mava_Url::initParams();

        /* request */
        Mava_Url::getQueryString();

        if($config['defaultTimeZone']!=""){
            date_default_timezone_set($config['defaultTimeZone']);
        }else{
            date_default_timezone_set('UTC');
        }

        $_ENV['cache'] = new Mava_Cache();
        $_ENV['db'] = new Mava_Database();

        self::$time = time();

        self::$host = (empty($_SERVER['HTTP_HOST']) ? '' : $_SERVER['HTTP_HOST']);

        self::$secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');

        self::setDebugMode($config['debug']);

        $_ENV['options'] = new Mava_Options();

        // check auto login
        Mava_Model_User::autoLogin();

        $this->_initialized = true;
    }

    public static function getCache($key){
        $cacheObj = self::getCacheObject();
        if(trim($key) != ""){
            return $cacheObj->get($key);
        }else{
            return false;
        }
    }

    public static function setCache($key, $val, $life_sencond){
        $cacheObj = self::getCacheObject();
        if(trim($key) != "" && (int)$life_sencond > 0){
            return $cacheObj->set($key, $val, $life_sencond);
        }else{
            return false;
        }
    }

    public static function delCache($key){
        $cacheObj = self::getCacheObject();
        if(trim($key) != ""){
            return $cacheObj->rm($key);
        }else{
            return false;
        }
    }

    public static function get($key, $group = null){
        global $_ENV;
        $k = explode('/', $group === null ? $key : $group.'/'.$key);
        switch (count($k)) {
            case 1: return isset($_ENV[$k[0]]) ? $_ENV[$k[0]] : null; break;
            case 2: return isset($_ENV[$k[0]][$k[1]]) ? $_ENV[$k[0]][$k[1]] : null; break;
            case 3: return isset($_ENV[$k[0]][$k[1]][$k[2]]) ? $_ENV[$k[0]][$k[1]][$k[2]] : null; break;
            case 4: return isset($_ENV[$k[0]][$k[1]][$k[2]][$k[3]]) ? $_ENV[$k[0]][$k[1]][$k[2]][$k[3]] : null; break;
            case 5: return isset($_ENV[$k[0]][$k[1]][$k[2]][$k[3]][$k[4]]) ? $_ENV[$k[0]][$k[1]][$k[2]][$k[3]][$k[4]] : null; break;
        }
        return null;
    }

    public static function set($key, $value, $group = null){
        global $_ENV;
        $k = explode('/', $group === null ? $key : $group.'/'.$key);
        if(in_array($k[0],array('config','options','db','session','cache'))){
            return false;
        }
        switch (count($k)) {
            case 1: $_ENV[$k[0]] = $value; break;
            case 2: $_ENV[$k[0]][$k[1]] = $value; break;
            case 3: $_ENV[$k[0]][$k[1]][$k[2]] = $value; break;
            case 4: $_ENV[$k[0]][$k[1]][$k[2]][$k[3]] = $value; break;
            case 5: $_ENV[$k[0]][$k[1]][$k[2]][$k[3]][$k[4]] =$value; break;
        }
        return true;
    }


    public static function remove($key, $group = null){
        global $_ENV;
        $k = explode('/', $group === null ? $key : $group.'/'.$key);
        if(in_array($k[0],array('config','options','db','session','cache'))){
            return false;
        }
        switch (count($k)) {
            case 1: unset($_ENV[$k[0]]); break;
            case 2: unset($_ENV[$k[0]][$k[1]]); break;
            case 3: unset($_ENV[$k[0]][$k[1]][$k[2]]); break;
            case 4: unset($_ENV[$k[0]][$k[1]][$k[2]][$k[3]]); break;
            case 5: unset($_ENV[$k[0]][$k[1]][$k[2]][$k[3]][$k[4]]); break;
        }
        return true;
    }

    public static function debugMode()
    {
        return self::$_debug;
    }

    public static function setDebugMode($debug)
    {
        self::$_debug = (boolean)$debug;
    }

    public static function undoMagicQuotes(&$array, $depth = 0)
    {
        if ($depth > 10 || !is_array($array))
        {
            return;
        }

        foreach ($array AS $key => $value)
        {
            if (is_array($value))
            {
                self::undoMagicQuotes($array[$key], $depth + 1);
            }
            else
            {
                $array[$key] = stripslashes($value);
            }

            if (is_string($key))
            {
                $new_key = stripslashes($key);
                if ($new_key != $key)
                {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
    }

    public static function setMemoryLimit($limit)
    {
        if (self::$_memoryLimit === null)
        {
            $curLimit = @ini_get('memory_limit');
            switch (substr($curLimit, -1))
            {
                case 'g':
                case 'G':
                    $curLimit = intval($curLimit) * 1024;
                // fall through

                case 'm':
                case 'M':
                    $curLimit = intval($curLimit) * 1024;
                // fall through

                case 'k':
                case 'K':
                    $curLimit = intval($curLimit) * 1024;
            }

            self::$_memoryLimit = intval($curLimit);
        }

        $limit = intval($limit);
        if ($limit > self::$_memoryLimit && self::$_memoryLimit > 0)
        {
            @ini_set('memory_limit', $limit);
            self::$_memoryLimit = $limit;
        }
    }

    public static function getCacheObject()
    {
        $cache = self::get('cache');
        if($cache instanceof Mava_Cache){
            return self::get('cache');
        }else{
            return new Mava_Cache();
        }
    }

    /**
     * @return mixed
     */
    public static function getOptions()
    {
        return self::get('options');
    }

    /**
     * @return Mava_Session
     */
    public static function getSession()
    {
        return self::get('session');
    }

    /**
     * @return Mava_Database
     */
    public static function getDb()
    {
        return self::get('db');
    }

    /**
     * @return array | mixed
     */
    public static function getConfig($key = ""){
        if($key != ""){
            return self::get('config/'. $key);
        }else{
            return self::get('config');
        }
    }

    public static function autoload($class)
    {
        return Mava_Loader::getInstance()->autoload($class);
    }

    public function resetDynamicClassCache()
    {
        self::$_classCache = array();
    }

    public static function resolveDynamicClass($class, $type = '', $fakeBase = false)
    {
        if (!$class)
        {
            return false;
        }

        if (!Mava_Application::autoload($class))
        {
            if ($fakeBase)
            {
                $fakeNeeded = true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $fakeNeeded = false;
        }

        if (!empty(self::$_classCache[$class]))
        {
            return self::$_classCache[$class];
        }

        $createClass = $class;

        $extend = array();
        Mava_Event::fire('load_class', array($class, &$extend), $class);
        if ($type)
        {
            Mava_Event::fire('load_class_' . $type, array($class, &$extend), $class);
        }

        if ($fakeNeeded)
        {
            if (!$extend)
            {
                return false;
            }

            eval('class ' . $class . ' extends ' . $fakeBase . ' {}');
        }

        if ($extend)
        {
            try
            {
                foreach ($extend AS $dynamicClass)
                {
                    if (preg_match('/[;,$\/#"\'\.()]/', $dynamicClass))
                    {
                        continue;
                    }

                    $proxyClass = 'MVCP_' . $dynamicClass;
                    $namespaceEval = '';

                    $nsSplit = strrpos($dynamicClass, '\\');
                    if ($nsSplit !== false && $ns = substr($dynamicClass, 0, $nsSplit))
                    {
                        $namespaceEval = "namespace $ns; ";
                        $proxyClass = 'MVCP_' . substr($dynamicClass, $nsSplit + 1);
                        $createClass = '\\' . $createClass;
                    }
                    eval($namespaceEval . 'class ' . $proxyClass . ' extends ' . $createClass . ' {}');
                    Mava_Application::autoload($dynamicClass);
                    $createClass = $dynamicClass;
                }
            }
            catch (Exception $e)
            {
                self::$_classCache[$class] = $class;
                throw $e;
            }
        }

        self::$_classCache[$class] = $createClass;
        return $createClass;
    }

    public static function isRegistered($index)
    {
        if($index==""){
            return false;
        }
        global $_ENV;
        return isset($_ENV[$index]);
    }
}
