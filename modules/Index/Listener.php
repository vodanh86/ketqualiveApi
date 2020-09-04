<?php
class Index_Listener {
    public static function beforeCallAction($controller,&$action){

    }

    public static function beforeGetView(&$viewName,&$params){
        if($viewName=='Index_View_Hello'){
            $params['name'] = '<b>hoacatmauden</b>';
        }
    }

    public static function afterReturnView($viewName, &$viewContent){
        if($viewName=='Index_View_Hello'){
            $extContent = Mava_View::getView('Index_View_Bye');
            $viewContent .= $extContent;
        }
    }

    public static function loadClassModel($class, array &$extend){
        if($class=='Index_Model_User'){
            $extend[] = 'Index_Model_UserTwo';
        }
    }
    public static function loadClassController($class, array &$extend){
        if($class=='Mava_Controller_Index'){
            $extend[] = 'Index_Controller_IndexTwo';
        }
    }
}
