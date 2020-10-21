<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 4/9/2019
 * Time: 8:18 PM
 */
class API_Controller_Novel extends API_Controller {
    public function listAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            $result = $this->_getVideoModel()->getList();
            if($result === false){
                return $this->responseError("Vui lòng đăng nhập lại", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    /**
     * @return API_Model_Video
     */
    protected function _getVideoModel()
    {
        return $this->getModelFromCache('Novel_Model_Novel');
    }

    public function detailAction(){
        $userID = (int)Mava_Url::getParam('novelId');
        if($userID > 0){
            $userModel = $this->_getVideoModel();
            $result = $userModel->getViewById(0, 1000, $userID, false);
            if($result === false){
                return $this->responseError("Vui lòng đăng nhập lại", []);
            }
            return $this->responseSuccess("", $result);
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('user_not_found')
            ));
        }
    }
}