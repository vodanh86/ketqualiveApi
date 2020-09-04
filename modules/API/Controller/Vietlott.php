<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 4/9/2019
 * Time: 8:18 PM
 */
class API_Controller_Vietlott extends API_Controller {
    public function megaAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            $result = $this->_getVietlottModel()->getMegaList($postData);
            if($result['error'] == 0){
                return $this->responseSuccess($result['message'], $result['data']);
            }else{
                return $this->responseError($result['message'], []);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function max4dLatestAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            $result = $this->_getVietlottModel()->getMax4dLatest($postData['token']);
            if($result['error'] === 0){
                return $this->responseSuccess($result['message'], $result['data']);
            }else{
                return $this->responseError($result['message'], []);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }


    public function max4dNextAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            $result = $this->_getVietlottModel()->getMax4dNext($postData['token'], $postData['max4d_id']);
            if($result['error'] === 0){
                return $this->responseSuccess($result['message'], $result['data']);
            }else{
                return $this->responseError($result['message'], []);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function max4dPrevAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            $result = $this->_getVietlottModel()->getMax4dPrev($postData['token'], $postData['max4d_id']);
            if($result['error'] === 0){
                return $this->responseSuccess($result['message'], $result['data']);
            }else{
                return $this->responseError($result['message'], []);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function powerAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            $result = $this->_getVietlottModel()->getPowerList($postData);
            if($result['error'] === 0){
                return $this->responseSuccess($result['message'], $result['data']);
            }else{
                return $this->responseError($result['message'], []);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    /**
     * @return API_Model_Vietlott
     */
    protected function _getVietlottModel()
    {
        return $this->getModelFromCache('API_Model_Vietlott');
    }
}