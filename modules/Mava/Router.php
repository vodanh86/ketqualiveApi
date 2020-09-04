<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 3/15/14
 * Time: 2:01 PM
 * To change this template use File | Settings | File Templates.
 */

class Mava_Router {
    public static function getControllerAction(){
        $routeConfig = Mava_Application::get('config/router');
        $path = trim(Mava_Url::getRequestPath(),'/');
        $controller = '';
        $action = '';
        $isMatch = false;
        if(sizeof($routeConfig) > 0){
            foreach($routeConfig as $key => $controllerAction){
                if($isMatch===false){
                    $key = preg_quote($key,'/');
                    $key = str_replace(array(
                        '\:',
                        '(',
                        ')'
                    ),array(
                        ':',
                        '{',
                        '}'
                    ),$key);
                    $org_key = $key;
                    $key = preg_replace('#\\\{([-a-z_]+):number\\\}#','([0-9]+)',$key);
                    $key = preg_replace('#\\\{([-a-z_]+):any\\\}#','(.+)',$key);

                    if(preg_match_all('#^'.$key.'$#',$path,$math_value)){
                        if(is_array($controllerAction)){
                            if(isset($controllerAction['params'])){
                                foreach($controllerAction['params'] as $k => $v){
                                    Mava_Url::setParam($k, $v);
                                }
                            }
                            $controllerAction = $controllerAction['action'];
                        }
                        // match
                        preg_match_all('#\\\{([-a-z_]+):(any|number)\\\}#',$org_key,$math_key);
                        unset($math_value[0]);
                        $result = array();
                        $count = 0;
                        foreach($math_value as $value_item){
                            $controllerAction = str_replace('('. $math_key[1][$count] .':c)', ucfirst($value_item[0]), $controllerAction);
                            $controllerAction = str_replace('('. $math_key[1][$count] .':l)', strtolower($value_item[0]), $controllerAction);
                            $controllerAction = str_replace('('. $math_key[1][$count] .':u)', strtoupper($value_item[0]), $controllerAction);
                            $controllerAction = str_replace('('. $math_key[1][$count] .')', trim($value_item[0]), $controllerAction);
                            Mava_Url::setParam($math_key[1][$count],$value_item[0]);
                            $count++;
                        }
                        $requestControllerAction = explode('/',$controllerAction);
                        $controller = $requestControllerAction[0];
                        $action = self::actionBeautifier($requestControllerAction[1]) .'Action';
                        $isMatch = true;
                    }
                }
            }
        }else{
            throw new Mava_Exception("No route config found!");
        }

        if (!$isMatch && trim($path) != "") {
            $slug = trim(Mava_Url::getRequestPath(), '/');
            $key = md5($slug);
            $slugModel = new Mava_Model_Slug();
            $slugInfo = $slugModel->getSlugByKey($key);
            if($slug != '') {
                if ($slugInfo) {
                    $controller = $slugInfo['controller'] . '_Controller_' . $slugInfo['controller'];
                    $action = $slugInfo['action'];
                    if($controller!=''){
                        $controller = ucfirst($controller);
                    }
                    if ($action != '') {
                        $action = strtolower($action);
                        $action .= 'Action';
                    } else {
                        $action = 'indexAction';
                    }
                    if (class_exists($controller)) {
                        if (method_exists($controller, $action)) {
                            if (Mava_String::isJson($slugInfo['params'])) {
                                $params = json_decode($slugInfo['params'], true);
                                if (isset($params['page_id']) && $params['page_id'] > 0) {
                                    $page_id = $params['page_id'];
                                    Mava_Application::set('page_id', $page_id, 'static_page');
                                }
                            }
                        }
                    }
                }
            }
            if($controller == ''){
                $check_path = explode('/', $path);
                if (sizeof($check_path) > 0) {
                    foreach ($check_path as &$item) {
                        $cleanPath = explode('-', $item);
                        $cleanedPath = array();
                        foreach ($cleanPath as $cp) {
                            $cleanedPath[] = ucfirst($cp);
                        }
                        $item = implode('', $cleanedPath);
                    }
                }

                if (sizeof($check_path) == 1) {
                    $controller = ucfirst($check_path[0]) . '_Controller_' . ucfirst($check_path[0]);
                    $action = '';
                } else if (sizeof($check_path) == 2) {
                    $controller = ucfirst($check_path[0]) . '_Controller_' . ucfirst($check_path[0]);
                    $action = self::actionBeautifier($check_path[1]) . 'Action';
                } else if (sizeof($check_path) == 3) {
                    $controller = ucfirst($check_path[0]) . '_Controller_' . ucfirst($check_path[1]);
                    $action = self::actionBeautifier($check_path[2]) . 'Action';
                }else{
                    if(is_debug()) {
                        throw new Mava_Exception("No route config match!");
                    }else{
                        throw new Mava_Exception(__('server_error'));
                    }
                }
            }
        } else if ($controller == '') {
            $controller = Mava_Application::get('config/default_controller');
            $action = Mava_Application::get('config/default_action');
        }
        $responseController = new Mava_ControllerResponse_Controller();
        $responseController->controllerName = $controller;
        $responseController->controllerAction = $action;
        return $responseController;
    }

    public static function actionBeautifier($action) {
        $action = explode("-", $action);
        $result = "";
        for($i = 0;$i<count($action);$i++){
            if($i==0){
                $result .= strtolower($action[$i]);
            }else{
                $result .= ucfirst($action[$i]);
            }
        }
        return $result;
    }
}