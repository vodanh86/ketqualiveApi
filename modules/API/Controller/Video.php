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

    public function deleteAction(){
        $userID = (int)Mava_Url::getParam('videoID');
        if($userID > 0){
            $userModel = $this->_getVideoModel();
            $user = $userModel->getById($userID, false);
            if($user){
                $check = $userModel->deleteById($userID);
                if($check){
                    return $this->responseJson(array(
                        'status' => 1,
                        'message' => __('user_deleted')
                    ));
                }else{
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('can_not_delete_user')
                    ));
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('user_not_found')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('user_not_found')
            ));
        }
    }
}