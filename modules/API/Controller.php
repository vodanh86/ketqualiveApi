<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 4/9/2019
 * Time: 8:19 PM
 */
class API_Controller {

    protected $_modelCache = array();

    public function _setup(){
        Mava_Url::delParam('_client_id');
        Mava_Url::delParam('_client_key');
        // TODO check request is valid
        return false;
    }

    public function responseError($message = "", $data = array()){
        Mava_Log::info($message);
        Mava_Log::info(json_encode($data));
        Mava_Log::info(Mava_Url::getCurrentAddress());
        $controllerResponse = new Mava_ControllerResponse_Json();
        $controllerResponse->data = [
            'error' => 1,
            'message' => $message,
            'data' => $data
        ];
        return $controllerResponse;
    }

    public function responseSuccess($message = "", $data = array()){
        Mava_Log::info($message);
        //Mava_Log::info(json_encode($data));
        Mava_Log::info(Mava_Url::getCurrentAddress());
        $controllerResponse = new Mava_ControllerResponse_Json();
        $controllerResponse->data = [
            'error' => 0,
            'message' => $message,
            'data' => $data
        ];
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