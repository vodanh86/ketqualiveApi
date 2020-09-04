<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 3/18/14
 * Time: 2:05 PM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Visitor {
    protected static $_instance;
    protected $_user = array();
    protected $_isSuperAdmin = null;
    protected $_language = 0;
    protected $_language_code = false;
    protected $_currency = 0;
    protected $_currency_code = false;

    protected function __construct()
    {
    }

    /**
     * @return Mava_Visitor
     */
    public static final function getInstance()
    {
        if (!self::hasInstance())
        {
            self::setup(0); // setup sets the instance
        }

        return self::$_instance;
    }

    public static final function setInstance(Mava_Visitor $v)
    {
        self::$_instance = $v;
    }

    public static function hasInstance()
    {
        return (self::$_instance ? true : false);
    }

    /**
     * @return int
     */
    public static function getUserId()
    {
        return (int)Mava_Session::get('user_id');
    }

    /**
     * @return string
     */
    public static function getLanguageCode()
    {
        $object = self::getInstance();

        return $object->_language_code;
    }

    /**
     * @return string
     */
    public static function getLanguageId()
    {
        $object = self::getInstance();

        return $object->_language['language_id'];
    }

    /**
     * @return string
     */
    public static function getLanguageTitle()
    {
        $object = self::getInstance();

        return $object->_language['title'];
    }

    /**
     * @return string
     */
    public static function getCurrencyTitle()
    {
        $object = self::getInstance();

        return $object->_currency['title'];
    }
    /**
     * @return mixed
     */
    public static function getCurrentLanguage()
    {
        $object = self::getInstance();

        return $object->_language;
    }

    public function getLanguage()
    {
        return $this->_language;
    }

    public function hasPermission($permission)
    {
        if($permission!=""){
            if($this->isSuperAdmin()){
                return true;
            }else if(isset($this->_user['permissions']) && is_array($this->_user['permissions'])){
                return in_array($permission,$this->_user['permissions']);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function getPermissions()
    {
        if(isset($this->_user['permissions']) && is_array($this->_user['permissions'])){
            return $this->_user['permissions'];
        }else{
            return array();
        }
    }

    public function isSuperAdmin()
    {
        if ($this->_isSuperAdmin === null)
        {
            $superAdmins = preg_split(
                '#\s*,\s*#', Mava_Application::get('config/superAdmins'),
                -1, PREG_SPLIT_NO_EMPTY
            );
            $this->_isSuperAdmin = in_array($this->_user['user_id'], $superAdmins);
        }

        return $this->_isSuperAdmin;
    }

    public static function setup($userId)
    {
        $userId = (int)$userId;

        /* @var $userModel Mava_Model_User */
        $userModel = Mava_Model::create('Mava_Model_User');

        $class = Mava_Application::resolveDynamicClass('Mava_Visitor');

        /* @var $object Mava_Visitor */
        $object = new $class();
        if ($userId && $user = $userModel->getUserById($userId))
        {
            $object->_user = $user;
            if($user['is_banned'] > time()){
                $userModel->logout();
            }
        }
        else
        {
            $object->_user = $userModel->getGuestUser();
        }

        $db = Mava_Application::getDb();
        $cookie_user_lang = Mava_Helper_Cookie::getCookie('user_language_code');

        if($cookie_user_lang){
            $language_code_install = $cookie_user_lang;
        }else{
            if(is_login()){
                $user_id = Mava_Visitor::getUserId();
                $user_language_code = $db->fetchRow(" SELECT l.`language_code` FROM `#__user` u,#__language l WHERE l.`language_id`=u.`language_id` AND u.`user_id` = '".(int)$user_id."' ");
                if($user_language_code){
                    $language_code_install = $user_language_code['language_code'];
                }else{
                    $language_code_install = Mava_Application::getConfig('defaultLanguage');
                }
            }else{
                $language_code_install = Mava_Application::getConfig('defaultLanguage');
            }
        }

        $cookie_user_currency = Mava_Helper_Cookie::getCookie('user_currency_code');

        if($cookie_user_currency){
            $currency_code_install = $cookie_user_currency;
        }else{
            if(is_login()){
                $user_id = Mava_Visitor::getUserId();
                $user_currency_code = $db->fetchRow(" SELECT c.`currency_code` FROM `#__user` u,#__currency c WHERE c.`currency_code`=u.`currency_code` AND u.`user_id` = '".(int)$user_id."' ");
                if($user_currency_code){
                    $currency_code_install = $user_currency_code['currency_code'];
                }else{
                    $currency_code_install = Mava_Application::getConfig('defaultCurrency');
                }
            }else{
                $currency_code_install = Mava_Application::getConfig('defaultCurrency');
            }
        }

        //$object->setVisitorLanguage($object->_user['language_code']);
        $object->setVisitorLanguage($language_code_install);
        $object->setVisitorCurrency($currency_code_install);



        //set language
        $lang_code = Mava_Url::getParam('_lang');

        if($lang_code!=''){
            $is_public = 'yes';
            if(is_login()){
                $user_id = Mava_Visitor::getUserId();
                //check supper user
                $superAdmins = preg_split(
                    '#\s*,\s*#', Mava_Application::get('config/superAdmins'),
                    -1, PREG_SPLIT_NO_EMPTY
                );
                if(in_array($user_id, $superAdmins)){
                    $is_public = '';
                }
            }
            $language = Mava_Language::getByCode($lang_code ,$is_public);
            if($language){
                if(is_login()){
                    // update db
                    $user_id = Mava_Visitor::getUserId();
                    $db->query(" UPDATE #__user SET `language_code` = '".addslashes($lang_code)."' WHERE `user_id` = '".(int)$user_id."' ");
                }
                //redirect
                //Mava_Session::set('otm', __('language_now_change_to_x', array('name' => $language['title'])));
                Mava_Helper_Cookie::setCookie('user_language_code', $lang_code);
                $redirect_url = Mava_Url::removeParam('',array('_lang'));
                Mava_Url::redirect($redirect_url);
            }
        }

        //set currency
        $currency_code = Mava_Url::getParam('_currency');
        $db = Mava_Application::getDb();

        if($currency_code!=''){
            $currency = Mava_Currency::getByCode($currency_code);
            if($currency){
                if(is_login()){
                    // update db
                    $user_id = Mava_Visitor::getUserId();
                    $db->query(" UPDATE #__user SET `currency_code` = '".addslashes($currency_code)."' WHERE `user_id` = '".(int)$user_id."' ");
                }
                //redirect
                Mava_Session::set('otm', __('currency_now_change_to_x', array('name' => $currency['title'])));
                Mava_Helper_Cookie::setCookie('user_currency_code', $currency_code);
                $redirect_url = Mava_Url::removeParam('',array('_currency'));
                Mava_Url::redirect($redirect_url);
            }
        }

        $object->_setUpUserPermission();

        date_default_timezone_set($object->_user['timezone']);

        self::$_instance = $object;

        Mava_Event::fire('visitor_setup', array(&self::$_instance));

        return self::$_instance;
    }

    public function setVisitorLanguage($language_code)
    {
        $language = Mava_Language::getByCode($language_code);
        if($language){
            if($this->_language_code == false){
                $this->_language_code = $language['language_code'];
                $this->_language = $language;
            }

            // setup language
            $phrases = Mava_Application::getCache('language_phrase_'. $language['language_id']);
            if(!is_array($phrases)){
                $phrases = Mava_Language::getAllPhraseInLanguage($language['language_id']);
                if(!is_array($phrases)){
                    $phrases = array();
                }
                Mava_Application::setCache('language_phrase_'. $language['language_id'],$phrases,86400*30);
            }
            Mava_Application::set('phrases',$phrases);
        }
        return true;
    }

    public function setVisitorCurrency($currency_code)
    {
        $currency = Mava_Currency::getByCode($currency_code);
        if($currency){
            if($this->_currency_code == false){
                $this->_currency_code = $currency['currency_code'];
                $this->_currency = $currency;
            }
        }
        return true;
    }

    protected function _setUpUserPermission(){
        //TODO
    }

    public function get($name)
    {
        if (array_key_exists($name, $this->_user))
        {
            return $this->_user[$name];
        }
        else
        {
            return false;
        }
    }

    public function set($name, $value)
    {
        if (array_key_exists($name, $this->_user))
        {
            $this->_user[$name] = $value;
        }
    }
}