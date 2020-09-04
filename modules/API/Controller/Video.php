<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 4/9/2019
 * Time: 8:18 PM
 */
class API_Controller_Video extends API_Controller {
    public function listAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            $result = $this->_getVideoModel()->getList($postData);
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
        return $this->getModelFromCache('API_Model_Video');
    }
}