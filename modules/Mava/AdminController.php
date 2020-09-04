<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 3/14/14
 * Time: 10:25 PM
 * To change this template use File | Settings | File Templates.
 */
abstract class Mava_AdminController {

    protected $_modelCache = array();

    public function responseView($viewName,$params = array(),$layout = 'admin'){
        $controllerResponse = new Mava_ControllerResponse_View();
        $controllerResponse->viewName = $viewName;
        $controllerResponse->params = $params;
        $controllerResponse->layout = $layout;

        return $controllerResponse;
    }

    public function responseError($message, $errorCode = Mava_Error::SERVER_ERROR){
        $controllerResponse = new Mava_ControllerResponse_View();
        $controllerResponse->viewName = 'Admin_View_Error';
        $controllerResponse->params = array(
            'message' => $message,
            'error_code' => $errorCode
        );
        $controllerResponse->layout = 'blank';
        return $controllerResponse;
    }

    public function responseJson($data = array()){
        $controllerResponse = new Mava_ControllerResponse_Json();
        $controllerResponse->data = $data;
        return $controllerResponse;
    }

    public function responseRedirect($url = ''){
        $controllerResponse = new Mava_ControllerResponse_Redirect();
        $controllerResponse->redirectLink = $url;
        return $controllerResponse;
    }


    /**
     * @param $class
     * @return Mava_Model
     * @throws Mava_Exception
     */
    public function getModelFromCache($class)
    {
        if (!isset($this->_modelCache[$class]))
        {
            $this->_modelCache[$class] = Mava_Model::create($class);
        }

        return $this->_modelCache[$class];
    }
}