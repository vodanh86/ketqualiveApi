<?php

class Manager_Controller extends Mava_Controller {
    public function __construct(){
        $managerId = Mava_Application::get("session")->get('manager_id');
        if((int)$managerId <= 0){
            Mava_Url::redirect(Mava_Url::getPageLink('manager/login'));
        }
    }

    public function responseView($viewName,$params = array(),$layout = 'manager'){
        $controllerResponse = new Mava_ControllerResponse_View();
        $controllerResponse->viewName = $viewName;
        $controllerResponse->params = $params;
        $controllerResponse->layout = $layout;

        return $controllerResponse;
    }
}