<?php

class API_Model_User extends Mava_Model_User
{
    public function _getByToken($token){
		return $this->_getDb()->fetchRow("SELECT * FROM #__user WHERE `token`='". $token ."'");
	}

    public function _getById($id){
        return $this->_getDb()->fetchRow("SELECT * FROM #__user WHERE `user_id`=". (int)$id);
    }

    public function getListUserByToken($token){
        return $this->_getDb()->fetchAll("SELECT * FROM #__user WHERE `token` IN (". Mava_String::doImplode($token) .")");
    }

    public function _getInfoByToken($token){
        $user = $this->_getByToken($token);
        $result = $this->_getDb()->fetchRow("
            SELECT
                u1.*,
                fle.followers
            FROM
                (SELECT
                u.*,
                cl.coin_charge,
                flw.following
                FROM #__user u
                LEFT JOIN (
                    SELECT token as tk, SUM(coin_change) as coin_charge
                    FROM #__coin_logs
                    WHERE type='napthe'
                ) cl ON cl.`tk` = u.`token`
                LEFT JOIN (
                    SELECT token as flwtk, GROUP_CONCAT(ufl.`user_id`) as following
                    FROM #__user_follow ufl
                    WHERE `token`='".$token."'
                ) flw ON flw.`flwtk` = u.`token`) u1
                LEFT JOIN (
                    SELECT ufl.`user_id` as ufl_user_id, GROUP_CONCAT(token) as followers
                    FROM #__user_follow ufl
                    WHERE ufl.`user_id`='".$user['user_id']."'
                ) fle ON fle.`ufl_user_id` = u1.`user_id`
            WHERE u1.`token`='". $token ."'
            ");
        if($result) {
            if(isset($result['following']) && $result['following'] != null && $result['following'] != ''){
                $result['following'] = explode(',', $result['following']);
            } else {
                $result['following'] = [];
            }
            if(isset($result['followers']) && $result['followers'] != null && $result['followers'] != ''){
                $arrToken = explode(',', $result['followers']);
                $usersByToken = $this->getListUserByToken($arrToken);
                $result['followers'] = [];
                if($usersByToken && count($usersByToken) > 0) {
                    foreach ($usersByToken as $u) {
                        $result['followers'][] = $u['user_id'];
                    }
                }
            } else {
                $result['followers'] = [];
            }
            // enable payment or not
            $result['active_pay'] = (int)Mava_Application::getConfig('active_pay');
        }
        return $result;
    }

	public function _doLogin($token){
		$user = $this->_getInfoByToken($token);
		if($user) {
			return $user;
		}
		$userDW = $this->_getUserDataWriter();
		$userDW->bulkSet(array(
			'token' => $token
		));
		if($userDW->save()) {
            $userDWAfterSave = $this->_getUserDataWriter();
            $userDWAfterSave->setExistingData($userDW->get('user_id'));
            $userDWAfterSave->bulkSet(array(
                'custom_title' => 'khach'.$userDW->get('user_id'),
                'unique_token' => $this->getUniqueToken()
            ));
            if($userDWAfterSave->save()) {
                return $this->_getInfoByToken($token);
            } else {
                $this->_deleteByToken($token);
                return false;
            }
		} else {
			return false;
		}
	}

    public function loginByEmail($email, $password){
        $user = $this->getUserByEmail($email, false);
        if ($user && isset($user['token']) && isset($user['unique_token']) && isset($user['password']) && isset($user['user_id'])){
            if ($this->generalPassword($password, $user['unique_token']) == $user['password']){
                if(!$this->_getInfoByToken($user['token'])){
                    return array(
                        'error' => 1,
                        'message' => 'Không tìm thấy thành viên'
                    );
                }
                return array(
                    'error' => 0,
                    'user' => $this->_getInfoByToken($user['token'])
                );
            } else {
                return array(
                    'error' => 1,
                    'message' => 'Mật khẩu không đúng'
                );
            }
        } else {
            return array(
                'error' => 1,
                'message' => 'Email không tồn tại'
            );
        }
    }

    public function loginByPhone($phone, $password){
        $user = $this->getUserByPhone($phone, false);
        if ($user && isset($user['token']) && isset($user['unique_token']) && isset($user['password']) && isset($user['user_id'])){
            if ($this->generalPassword($password, $user['unique_token']) == $user['password']){
                if(!$this->_getInfoByToken($user['token'])){
                    return array(
                        'error' => 1,
                        'message' => 'Không tìm thấy thành viên'
                    );
                }
                return array(
                    'error' => 0,
                    'user' => $this->_getInfoByToken($user['token'])
                );
            } else {
                return array(
                    'error' => 1,
                    'message' => 'Mật khẩu không đúng'
                );
            }
        } else {
            return array(
                'error' => 1,
                'message' => 'Số điện thoại không tồn tại'
            );
        }
    }

    public function _deleteByToken($token){
        return $this->_getDb()->delete('#__user','token='. "'".$token."'");
    }

    public function checkEditCustomTitle($name, $user){
        // is not change
        if($user['custom_title'] == $name){
            return true;
        }
        // is empty
        if($user['custom_title'] == ''){
            return true;
        }
        // is supervip
        if($user['is_supervip'] == 1){
            return true;
        } else {
            // is vip
            if($user['expired_vip'] - time() > 0){
                // > 1 ngay
                if(time() - $user['last_edit_name'] > 86400){
                    return true;
                }
            } else {
                // is normal && > 30 ngay
                if(time() - $user['last_edit_name'] > 30*86400){
                    return true;
                }
            }
        }
        return false;
    }

    public function _editProfile($token, $data){
        $user = $this->_getByToken($token);
        if($user) {
            if(isset($data['custom_title'])){
                if(!$this->checkEditCustomTitle($data['custom_title'], $user)){
                    return [
                        'error' => 1,
                        'message' => 'Không thể thay đổi tên',
                        'data' => []
                    ];
                }
                $data['last_edit_name'] = time();
            }
            if(isset($data['email'])){
                $userByEmail = $this->getUserByEmail($data['email']);
                if($userByEmail && $userByEmail['user_id'] != $user['user_id']){
                    return [
                        'error' => 1,
                        'message' => 'Địa chỉ email đã tồn tại',
                        'data' => []
                    ];
                }
            }
            if(isset($data['phone'])){
                $userByPhone = $this->getUserByPhone($data['phone']);
                if($userByPhone && $userByPhone['user_id'] != $user['user_id']){
                    return [
                        'error' => 1,
                        'message' => 'Số điện thoại đã tồn tại',
                        'data' => []
                    ];
                }
            }
            if(isset($data['password'])){
                $data['password'] = $this->generalPassword($data['password'], $user['unique_token']);
            }
            $userDW = $this->_getUserDataWriter();
            $userDW->setExistingData($user['user_id']);
            $userDW->bulkSet($data);
            if($userDW->save()) {
                if(!$this->_getInfoByToken($user['token'])){
                    return [
                        'error' => 0,
                        'message' => 'Không tìm thấy thành viên',
                        'data' => []
                    ];
                }
                return [
                    'error' => 0,
                    'data' => $this->_getInfoByToken($user['token'])
                ];
            } else {
                return [
                    'error' => 1,
                    'message' => 'Không lưu được thông tin',
                    'data' => $userDW->getErrors()
                ];
            }
        } else {
            return [
                'error' => 1,
                'message' => 'Không tìm thấy thành viên',
                'data' => []
            ];
        }
    }

    public function setSuperVip($token){
        $user = $this->_getByToken($token);
        $userDW = $this->_getUserDataWriter();
        $userDW->setExistingData($user['user_id']);
        $userDW->bulkSet(array(
            'is_supervip' => 1
        ));
        $userDW->save();
    }

    public function _chargeCoin($data) {
        $user = $this->_getByToken($data['token']);
        if($user) {
            $exAPIData = array(
                'token' => $data['token'],
                'telco' => $data['telcoId'], // Mã nhà mạng : VTT, VINA, VMS
                'code' => $data['card_number'], // mã thẻ
                'serial' => $data['card_serial'], // serial thẻ
                'amount' => $data['card_value'] // giá trị thẻ
            );
            $responseExAPI = $this->_processChargeCard($exAPIData);

            if($responseExAPI['error'] == 0) {
                $this->_getRequestCoinLogsModel()->saveRequestCoinLogs($data['token'], $data, $exAPIData, $responseExAPI, 0);
                return [
                    'error' => 0,
                    'message' => 'Thẻ đang chờ xử lý',
                    'data' => [
                        'code' => $responseExAPI['code']
                    ]
                ];
            } else {
                $this->_getRequestCoinLogsModel()->saveRequestCoinLogs($data['token'], $data, $exAPIData, $responseExAPI, 1);
                return [
                    'error' => 1,
                    'message' => 'Nạp coin thất bại, vui lòng kiểm tra lại thông tin thẻ và thử lại'
                ];
            }
        } else {
            return [
                'error' => 1,
                'message' => 'Vui lòng đăng nhập lại'
            ];
        }
    }

    /**
     * external API call charge card
     * @return array
     */
    public function _processChargeCard($data){
        // tạo transaction
        $transactionData = [
            'token' => $data['token'],
            'card_number' => $data['code'],
            'card_serial' => $data['serial'],
            'card_value' => $data['amount']
        ];
        $transaction = $this->_getTransactionsModel()->saveTransaction($transactionData);
        if($transaction) {
            // call api gạch thẻ
            $dataCard = [
                "telco" => $data['telco'],
                "amount" => $data['amount'],
                "code" => $data['code'],
                "serial" => $data['serial'],
                "scratchCallbackUrl" => get_static_domain().'callback-card?code='.$transaction['code']
            ];
            $xboonInfo = Mava_Application::get('config/xboom_info');
            $urlAPICard = $xboonInfo['url_api'] . $xboonInfo['path_url_charge_card'];
            $callAPICard = setupCallAPI('POST', $urlAPICard, json_encode($dataCard));
            if($callAPICard) {
                $responseAPICard = json_decode($callAPICard, true);
                if($responseAPICard['code'] == 1) { // nạp thẻ thành công->chờ xử lý thẻ
                    return [
                        'error' => 0,
                        'message' => 'Thẻ đang chờ xử lý',
                        'code' => $transaction['code']
                    ];
                } else {
                    return [
                        'error' => 1,
                        'message' => getErrorMessage($responseAPICard['code'])
                    ];
                }
            } else {
                return [
                    'error' => 1,
                    'message' => 'Kết nối thất bại'
                ];
            }
        } else {
            return [
                'error' => 1,
                'message' => 'Không tạo được transaction'
            ];
        }
    }

    public function checkCardStatus($data) {
        $user = $this->_getByToken($data['token']);
        if($user) {
            return $this->_getTransactionsModel()->checkTransactionStatus($data);
        }else {
            return [
                'error' => 0,
                'message' => 'Vui lòng đăng nhập lại'
            ];
        }
    }

    public function updateCardStatus($code, $status) {
        return $this->_getTransactionsModel()->updateTransactionStatus($code, $status);
    }

    public function _buyVip($data){
        $user = $this->_getByToken($data['token']);
        if($user) {
            $price = Mava_Application::get('config/price_buy_vip');
            $totalAmount = $data['num'] * $price;
            $log = array(
                'token' => $user['token'],
                'num'   => $data['num'],
                'price' => $price,
                'total_amount' => $totalAmount,
                'coin' => $user['coin'],
            );
            if($user['coin'] < $totalAmount) {
                $this->_getBuyVipLogsModel()->saveBuyVipLogs($log, 1);
                return false;
            }
            $userDW = $this->_getUserDataWriter();
            $userDW->setExistingData($user['user_id']);
            if($user['expired_vip'] < time()){
                $user['expired_vip'] = time();
            }
            $userDW->bulkSet(array(
                'coin' => $user['coin'] - $totalAmount,
                'expired_vip' => $user['expired_vip'] + $data['num'] * 86400
            ));
            if($userDW->save()) {
                //save coin log
                $coinLog = [
                    'token' => $user['token'],
                    'coin_before'=> $user['coin'],
                    'coin_change'=> $totalAmount,
                    'coin_after'=> $user['coin'] - $totalAmount,
                    'type'=> 'muavip',
                ];
                $this->_getCoinLogsModel()->saveLog($coinLog);
                $this->_getBuyVipLogsModel()->saveBuyVipLogs($log, 0);
                $user = $this->getUserById($user['user_id'], false);
                return array(
                 'expired_vip' => date('d/m/Y H:i', $user['expired_vip'])
                );
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function _feedback($data){
        $fbTotalInDay = $this->_getFeedbackModel()->getTotalFeedbackByToken($data['token']);
        $maxFb = Mava_Application::get('config/max_feedback');
        if($fbTotalInDay < $maxFb) {
            return $feedback = $this->_getFeedbackModel()->saveFeedback($data);
        } else {
            return false;
        }
    }

    public function changeCoin($token, $coin) {
        $user = $this->_getByToken($token);
        if(!$user) {
            return false;
        }
        $userDW = $this->_getUserDataWriter();
        $userDW->setExistingData($user['user_id']);
        $userDW->bulkSet(array(
            'coin' => $user['coin'] + $coin
        ));
        if($userDW->save()) {
            return true;
        }
        return false;
    }

    public function getUser($data){
        $user = $this->_getByToken($data['token']);
        if(!$user) {
            return [
                'error' => 1,
                'message' => 'Không tìm thấy thành viên (token)'
            ];
        }
        $userById = $this->_getById($data['id']);
        if(!$userById) {
            return [
                'error' => 1,
                'message' => 'Không tìm thấy thành viên'
            ];
        }
        $userInfo = $this->_getInfoByToken($userById['token']);
        if(!$userInfo) {
            return [
                'error' => 1,
                'message' => 'Không tìm thấy thành viên'
            ];
        }
        unset($userInfo['token']);
        return [
            'error' => 0,
            'data' => $userInfo
        ];
    }

    public function getUsers($data){
        $user = $this->_getByToken($data['token']);
        if(!$user) {
            return false;
        }
        $users = $this->_getDb()->query("
            SELECT 
            u.user_id, 
            u.custom_title, 
            u.birthday, 
            u.email, 
            u.avatar,
            u.cover, 
            u.coin, 
            u.gender, 
            u.expired_vip, 
            u.is_supervip, 
            u.phone,
            cl.coin_charge
            FROM #__user u
            LEFT JOIN (
                SELECT token as tk, SUM(coin_change) as coin_charge
                FROM #__coin_logs
                WHERE type='napthe'
            ) cl ON cl.`tk` = u.`token`
            WHERE u.`user_id` IN (". Mava_String::doImplode($data['ids']) .")");
        return $users->rows;
    }

    public function doFollow($data){
        $user = $this->_getByToken($data['token']);
        if(!$user) {
            return [
                'error' => 1,
                'message' => 'Không tìm thấy thành viên'
            ];
        }
        $userById = $this->_getById($data['user_id']);
        if(!$userById) {
            return [
                'error' => 1,
                'message' => 'Không tìm thấy thành viên mà bạn muốn theo dõi'
            ];
        }
        return $this->_getUserFollowModel()->addFollow($data);
    }

    public function unFollow($data){
        $user = $this->_getByToken($data['token']);
        if(!$user) {
            return [
                'error' => 1,
                'message' => 'Không tìm thấy thành viên'
            ];
        }
        return $this->_getUserFollowModel()->unFollow($data);
    }

    public function getFollowing($data){
        $user = $this->_getByToken($data['token']);
        if(!$user) {
            return false;
        }
        if(isset($data['user_id']) && $data['user_id'] != ''){
            $userByUserId = $this->_getById($data['user_id']);
            if($userByUserId){
                $data['token'] = $userByUserId['token'];
            }
        }
        return $this->_getUserFollowModel()->getFollowing($data);
    }

    public function getFollower($data){
        $user = $this->_getByToken($data['token']);
        if(!$user) {
            return false;
        }
        if(!isset($data['user_id']) || $data['user_id'] == ''){
            $data['user_id'] = $user['user_id'];
        }
        return $this->_getUserFollowModel()->getFollower($data);
    }

    public function getChargeLogs($token){
        $user = $this->_getByToken($token);
        if(!$user) {
            return false;
        }
        return $this->_getRequestCoinLogsModel()->getLogByToken($token);
    }

    public function getCoinLogs($data){
        $user = $this->_getByToken($data['token']);
        if(!$user) {
            return false;
        }
        return $this->_getCoinLogsModel()->getLogByToken($data);
    }

    public function sendCoin($data){
        $userSend = $this->_getByToken($data['token']);
        $userReceive = $this->_getById($data['user_id']);
        if(!$userSend || !$userReceive) {
            return false;
        }
        // send coin + receive coin
        if($this->changeCoin($userSend['token'], -$data['coin']) && $this->changeCoin($userReceive['token'], $data['coin'])){
            //save send coin log
            $coinSendLog = [
                'token' => $userSend['token'],
                'coin_before'=> $userSend['coin'],
                'coin_change'=> $data['coin'],
                'coin_after'=> $userSend['coin'] - $data['coin'],
                'type'=> 'chuyencoin',
            ];
            $this->_getCoinLogsModel()->saveLog($coinSendLog);
            //save receive coin log
            $coinReceiveLog = [
                'token' => $userReceive['token'],
                'coin_before'=> $userReceive['coin'],
                'coin_change'=> $data['coin'],
                'coin_after'=> $userReceive['coin'] + $data['coin'],
                'type'=> 'nhancoin',
            ];
            $this->_getCoinLogsModel()->saveLog($coinReceiveLog);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return API_DataWriter_User
     */
    protected function _getUserDataWriter()
    {
    	return Mava_DataWriter::create('API_DataWriter_User');
    }

    /**
     * @return API_Model_RequestCoinLogs
     */
    protected function _getRequestCoinLogsModel()
    {
        return $this->getModelFromCache('API_Model_RequestCoinLogs');
    }

    /**
     * @return API_Model_BuyVipLogs
     */
    protected function _getBuyVipLogsModel()
    {
        return $this->getModelFromCache('API_Model_BuyVipLogs');
    }

    /**
     * @return API_Model_Feedback
     */
    protected function _getFeedbackModel()
    {
        return $this->getModelFromCache('API_Model_Feedback');
    }

    /**
     * @return API_Model_UserFollow
     */
    protected function _getUserFollowModel()
    {
        return $this->getModelFromCache('API_Model_UserFollow');
    }

    /**
     * @return API_Model_CoinLogs
     */
    protected function _getCoinLogsModel()
    {
        return $this->getModelFromCache('API_Model_CoinLogs');
    }

    /**
     * @return API_Model_Transactions
     */
    protected function _getTransactionsModel()
    {
        return $this->getModelFromCache('API_Model_Transactions');
    }
}