<?php

class API_Controller_User extends API_Controller {

	public function loginAction(){
        if(Mava_Url::isPost()) {
    		$postData = Mava_Url::getParams();
            if(!isset($postData['token']) || trim($postData['token']) == "") {
                return $this->responseError("Token là bắt buộc", []);
            }
    		$userModel = $this->_getUserModel();
    		$user = $userModel->_doLogin($postData['token']);
    		if(!$user) {
    			return $this->responseError("Đăng nhập thất bại", []);
    		}
    		return $this->responseSuccess("Đăng nhập thành công", $user);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
	}

    public function loginByEmailAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['email']) || trim($postData['email']) == "") {
                return $this->responseError("Email là bắt buộc", []);
            } elseif (!Mava_String::isEmail($postData['email'])) {
                return $this->responseError("Email không hợp lệ", []);
            } elseif (!isset($postData['password']) || trim($postData['password']) == "") {
                return $this->responseError("Mật khẩu là bắt buộc", []);
            } else {
                $result = $this->_getUserModel()->loginByEmail($postData['email'], $postData['password']);
                if($result['error'] == 1) {
                    return $this->responseError($result['message'], []);
                }
                return $this->responseSuccess("Đăng nhập thành công", $result['user']);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function loginByPhoneAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['phone']) || trim($postData['phone']) == "") {
                return $this->responseError("Số điện thoại là bắt buộc", []);
            } elseif (!Mava_String::isPhoneNumber($postData['phone'])) {
                return $this->responseError("Số điện thoại không hợp lệ", []);
            } elseif (!isset($postData['password']) || trim($postData['password']) == "") {
                return $this->responseError("Mật khẩu là bắt buộc", []);
            } else {
                $result = $this->_getUserModel()->loginByPhone($postData['phone'], $postData['password']);
                if($result['error'] == 1) {
                    return $this->responseError($result['message'], []);
                }
                return $this->responseSuccess("Đăng nhập thành công", $result['user']);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

	public function editAction(){
		if(Mava_Url::isPost()) {
			$postData = Mava_Url::getParams();
			if(isset($postData['custom_title']) && trim($postData['custom_title']) == "") {
				return $this->responseError("Tên là bắt buộc", []);
			} elseif (isset($postData['phone']) && !Mava_String::isPhoneNumber($postData['phone'])) {
				return $this->responseError("Số điện thoại không hợp lệ", []);
			} elseif (isset($postData['email']) && !Mava_String::isEmail($postData['email'])) {
				return $this->responseError("Email không hợp lệ", []);
			} elseif(isset($postData['password']) && (strlen($postData['password']) < Mava_Application::get('config/passwordMinLength') ||
                strlen($postData['password']) > Mava_Application::get('config/passwordMaxLength'))) {
				return $this->responseError("Mật khẩu không hợp lệ", []);
			}
			$token = $postData['token'];
			$postData = array_filter_key($postData, [
			    "cover",
                "avatar",
                "custom_title",
                "phone",
                "email",
                "password",
                "birthday",
                "gender",
            ]);
			$user = $this->_getUserModel()->_editProfile($token, $postData);
			if($user['error'] == 0) {
				return $this->responseSuccess("Cập nhật thành công", $user['data']);
			}else{
                return $this->responseError($user['message'], $user['data']);
            }
		} else {
			return $this->responseError("Không có dữ liệu", []);
		}
	}

	public function chargeAction(){
        return $this->responseSuccess('OK', ['code' => '123']);
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['telcoId']) || trim($postData['telcoId']) == "") {
                return $this->responseError("Loại thẻ là bắt buộc", []);
            } elseif(!isset($postData['card_number']) || trim($postData['card_number']) == "") {
                return $this->responseError("Mã số thẻ là bắt buộc", []);
            } elseif(!isset($postData['card_serial']) || trim($postData['card_serial']) == "") {
                return $this->responseError("Số serial là bắt buộc", []);
            } elseif(!isset($postData['card_value']) || trim($postData['card_value']) == "") {
                return $this->responseError("Giá trị thẻ là bắt buộc", []);
            } else {
                $result = $this->_getUserModel()->_chargeCoin($postData);
                if($result['error'] == 1) {
                    return $this->responseError($result['message'], []);
                }
                return $this->responseSuccess($result['message'], $result['data']);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function checkCardStatusAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['code']) || trim($postData['code']) == "") {
                return $this->responseError("Mã giao dịch là bắt buộc", []);
            } else {
                $result = $this->_getUserModel()->checkCardStatus($postData);
                if($result['error'] == 1) {
                    return $this->responseError($result['message'], []);
                }
                return $this->responseSuccess($result['message'], $result['data']);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function callBackCardAction(){
        $code = Mava_Url::getParam('code');
        $status = Mava_Url::getParam('status');
        $this->_getUserModel()->updateCardStatus($code, $status);
        return $this->responseSuccess('success', []);
    }

    public function buyvipAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['num']) || trim($postData['num']) == "") {
                return $this->responseError("Số ngày là bắt buộc", []);
            }
            $result = $this->_getUserModel()->_buyVip($postData);
            if(!$result) {
                return $this->responseError("Tài khoản không đủ coin, vui lòng nạp thêm", []);
            }
            return $this->responseSuccess("Mua VIP thành công", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function feedbackAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['title']) || trim($postData['title']) == "") {
                return $this->responseError("Tiêu đề là bắt buộc", []);
            }
            if(!isset($postData['content']) || trim($postData['content']) == "") {
                return $this->responseError("Nội dung là bắt buộc", []);
            }
            $postData = array_filter_key($postData,[
                'token',
                'title',
                'content',
                'platform_type',
                'platform_version'
            ]);
            $result = $this->_getUserModel()->_feedback($postData);
            if(!$result) {
                return $this->responseError("Chúng tôi đã nhận được khá nhiều phản hồi từ bạn và đang xử lý, vui lòng chờ đợi", []);
            }
            return $this->responseSuccess("Gửi phản hồi thành công", []);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function getsAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['ids']) || !is_array($postData['ids'])) {
                return $this->responseError("Ids không đúng định dạng", []);
            }
            $result = $this->_getUserModel()->getUsers($postData);
            if($result === false){
                return $this->responseError("Có lỗi xảy ra", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function getAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['id']) || trim($postData['id']) == "") {
                return $this->responseError("User ID là bắt buộc", []);
            }
            $result = $this->_getUserModel()->getUser($postData);
            if($result['error'] == 0) {
                return $this->responseSuccess("", $result['data']);
            }else{
                return $this->responseError($result['message'], []);
            }
        } else {
            return $this->responseError("Không có dữ liệu", []);
        }
    }

    public function getByTokenAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['token']) || trim($postData['token']) == "") {
                return $this->responseError("User token là bắt buộc", []);
            }
            $result = $this->_getUserModel()->_getInfoByToken($postData['token']);
            if($result) {
                return $this->responseSuccess("", $result);
            }else{
                return $this->responseError("Không tìm thấy thành viên", []);
            }
        } else {
            return $this->responseError("Không có dữ liệu", []);
        }
    }

    public function doFollowAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['user_id']) || trim($postData['user_id']) == "") {
                return $this->responseError("User Id là bắt buộc", []);
            }
            $result = $this->_getUserModel()->doFollow($postData);
            if($result['error'] == 0) {
                return $this->responseSuccess("Đã theo dõi người này", []);
            }else {
                return $this->responseError($result['message'], []);
            }
        } else {
            return $this->responseError("Không có dữ liệu", []);
        }
    }

    public function unFollowAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['user_id']) || trim($postData['user_id']) == "") {
                return $this->responseError("User Id là bắt buộc", []);
            }
            $result = $this->_getUserModel()->unFollow($postData);
            if($result['error'] == 0) {
                return $this->responseSuccess("Đã bỏ theo dõi người này", []);
            }else {
                return $this->responseError($result['message'], []);
            }
        } else {
            return $this->responseError("Không có dữ liệu", []);
        }
    }

    public function followingAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            $result = $this->_getUserModel()->getFollowing($postData);
            if($result === false){
                return $this->responseError("Có lỗi xảy ra", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function followerAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            $result = $this->_getUserModel()->getFollower($postData);
            if($result === false){
                return $this->responseError("Có lỗi xảy ra", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function chargeLogsAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            $result = $this->_getUserModel()->getChargeLogs($postData['token']);
            if($result === false){
                return $this->responseError("Có lỗi xảy ra", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function coinLogsAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            $result = $this->_getUserModel()->getCoinLogs($postData);
            if($result === false){
                return $this->responseError("Có lỗi xảy ra", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function sendCoinAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['user_id']) || trim($postData['user_id']) == "") {
                return $this->responseError("Người nhận là bắt buộc", []);
            }
            if(!isset($postData['coin']) || trim($postData['coin']) == "") {
                return $this->responseError("Số coin là bắt buộc", []);
            }
            $result = $this->_getUserModel()->sendCoin($postData);
            if(!$result){
                return $this->responseError("Chuyển coin thất bại", []);
            }
            return $this->responseSuccess("Chuyển coin thành công", []);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function addCoinAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['transaction_id']) || trim($postData['transaction_id']) == "") {
                return $this->responseError("Mã giao dịch là bắt buộc", []);
            }
            if(!isset($postData['number']) || trim($postData['number']) == "") {
                return $this->responseError("Số điện thoại là bắt buộc", []);
            }
            if(!isset($postData['amount']) || trim($postData['amount']) == "") {
                return $this->responseError("Số tiền là bắt buộc", []);
            }
            if(!isset($postData['time']) || trim($postData['time']) == "") {
                return $this->responseError("thời gian là bắt buộc", []);
            }
            $postData["coin"] = $this->moneyToCoin($postData["amount"]);
            $result = $this->_getUserModel()->increaseCoin($postData);
            if(!$result){
                return $this->responseError("cộng tiền thất bại", []);
            }
            return $this->responseSuccess("cộng tiền thành công", []);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function moneyToCoin($amount){
        $topup = Mava_Application::get('config/topup');
        $max = 0;
        foreach($topup as $key=>$value){
            $max = $key;
            if ($value > $amount) {
                break;
            }
        }
        return $amount/$max*($topup[$max]);
    }

    public function uploadImageAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['token']) || trim($postData['token']) == "") {
                return $this->responseError("Token là bắt buộc", []);
            }
            $user = $this->_getUserModel()->_getByToken($postData['token']);
            if(!$user) {
                return $this->responseError("Có lỗi xảy ra", []);
            }
            if (isset($_FILES['image']) && $_FILES['image']['tmp_name'] != "") {
                $image = upload_image('upload', 'image');
                if ($image['error'] == 0) {
                    if(isset($postData['field'])){
                        if($postData['field'] === "cover"){
                            $dataUpdate= [
                                'cover' => $image['image']
                            ];
                            $this->_getUserModel()->_editProfile($postData['token'], $dataUpdate);
                        }
                        if($postData['field'] === "avatar"){
                            $dataUpdate= [
                                'avatar' => $image['image']
                            ];
                            $this->_getUserModel()->_editProfile($postData['token'], $dataUpdate);
                        }
                    }
                    return $this->responseSuccess("", $image);
                } else {
                    return $this->responseError($image['message'], []);
                }
            } else {
                return $this->responseError("Vui lòng chọn ảnh", []);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    /**
     * @return API_Model_User
     */
    protected function _getUserModel()
    {
    	return $this->getModelFromCache('API_Model_User');
    }
}
