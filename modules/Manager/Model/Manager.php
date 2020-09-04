<?php

class Manager_Model_Manager extends Mava_Model
{
	public function login($username, $password) {
		$accounts = Mava_Application::get('config/manager');
		foreach ($accounts as $account){
			if($username == $account['username'] && md5($password) == $account['password']){
				return array(
					'error' => 0,
					'manager' => $account
				);
			}
		}
		return array(
			'error' => 1,
			'message' => 'Tài khoản hoặc mật khẩu không đúng'
		);
	}

    public function getUserById($id){
        return $this->_getDb()->fetchRow("SELECT * FROM #__user WHERE `user_id`=". (int)$id);
    }

    public function getUsers(){
        $users = $this->_getDb()->fetchAll("
            SELECT user_id, custom_title, token, expired_vip, is_supervip, coin
            FROM #__user
        ");
        return $users;
    }

	public function getStatisticalUser(){
		$users = $this->_getDb()->fetchAll("
			SELECT user_id, token, expired_vip, is_supervip, coin
			FROM #__user
		");
		$statisticalUser = [
			'total' => count($users),
			'vip' => 0,
			'supervip' => 0,
			'total_coin' => 0,
		];
		foreach ($users as $user) {
			if($user['expired_vip'] > time()){
				$statisticalUser['vip']++;
			}
			if($user['is_supervip'] == 1){
				$statisticalUser['supervip']++;
			}
			$statisticalUser['total_coin'] = $statisticalUser['total_coin'] + $user['coin'];
		}
		return $statisticalUser;
	}

	public function getStatisticalCoin(){
        $curentTime = date('d-m-Y',time());
		$coin = $this->_getDb()->fetchRow("
			SELECT SUM(coin_change) as coin_change
			FROM #__coin_logs
			WHERE type!='chuyencoin' AND type!='nhancoin' AND FROM_UNIXTIME(created_at, '%d-%m-%Y') ='". $curentTime ."'
		");
		$chargedCoin = $this->_getDb()->fetchRow("
			SELECT SUM(coin_change) as charged_coin
			FROM #__coin_logs
			WHERE type='napthe' AND FROM_UNIXTIME(created_at, '%d-%m-%Y') ='". $curentTime ."'
		");
		$statisticalCoin = [
			'coin_change' => $coin['coin_change'],
			'charged_coin' => $chargedCoin['charged_coin'],
			'consumed_coin' => $coin['coin_change'] - $chargedCoin['charged_coin']
		];
		return $statisticalCoin;
	}

    public function getListUser($skip = 0, $limit = 10, $searchTerm = "", $sortBy, $sortDir){
        $db = $this->_getDb();
        $where = "1=1";
        if($searchTerm != ""){
            $where = "(`custom_title` LIKE '%". $db->quoteLike($searchTerm) ."%' OR `user_id` LIKE '%". $db->quoteLike($searchTerm) ."%')";
        }
        if($sortBy != "" && $sortDir !=""){
            $order = " `".$sortBy."` ".strtoupper($sortDir)." ";
        }else{
            $order = " `user_id` DESC";
        }
        $items = $db->fetchAll("
            SELECT
                user_id,
                custom_title,
                coin,
                email,
                phone,
                expired_vip,
                is_supervip,
                lock_account,
                gender
            FROM #__user
            WHERE ". $where ."
            ORDER BY
                ".$order."
            LIMIT
            ". $skip .",". $limit ."
        ");
        $count = $db->fetchRow("
			SELECT COUNT(*) AS 'total'
			FROM #__user
			WHERE ". $where ."
        ");
        return array(
            'rows' => $items,
            'total' => $count['total']
        );
    }

	public function getUserVip($skip = 0, $limit = 10, $searchTerm = "", $sortBy, $sortDir){
        $db = $this->_getDb();
        $where = " AND 1=1";
        if($searchTerm != ""){
            $where = " AND (`custom_title` LIKE '%". $db->quoteLike($searchTerm) ."%' OR `user_id` LIKE '%". $db->quoteLike($searchTerm) ."%')";
        }
        if($sortBy != "" && $sortDir !=""){
            $order = " `".$sortBy."` ".strtoupper($sortDir)." ";
        }else{
             $order = " `user_id` DESC";
        }
        $items = $db->fetchAll("
            SELECT
                user_id,
                custom_title,
                coin,
                email,
                phone,
                expired_vip,
                is_supervip,
                lock_account,
                gender
            FROM #__user
            WHERE  expired_vip > ". time() ." ". $where ."
            ORDER BY
                ".$order."
            LIMIT
            ". $skip .",". $limit ."
        ");
        $count = $db->fetchRow("
			SELECT COUNT(*) AS 'total'
			FROM #__user
			WHERE  expired_vip > ". time() ." ". $where ."
        ");
        return array(
            'rows' => $items,
            'total' => $count['total']
        );
    }

    public function getUserSupervip($skip = 0, $limit = 10, $searchTerm = "", $sortBy, $sortDir){
        $db = $this->_getDb();
        $where = " AND 1=1";
        if($searchTerm != ""){
            $where = " AND (`custom_title` LIKE '%". $db->quoteLike($searchTerm) ."%' OR `user_id` LIKE '%". $db->quoteLike($searchTerm) ."%')";
        }
        if($sortBy != "" && $sortDir !=""){
            $order = " `".$sortBy."` ".strtoupper($sortDir)." ";
        }else{
             $order = " `user_id` DESC";
        }
        $items = $db->fetchAll("
            SELECT
                user_id,
                custom_title,
                coin,
                email,
                phone,
                expired_vip,
                is_supervip,
                lock_account,
                gender
            FROM #__user
            WHERE  is_supervip = 1 ". $where ."
            ORDER BY
                ".$order."
            LIMIT
            ". $skip .",". $limit ."
        ");
        $count = $db->fetchRow("
			SELECT COUNT(*) AS 'total'
			FROM #__user
			WHERE  is_supervip = 1 ". $where ."
        ");
        return array(
            'rows' => $items,
            'total' => $count['total']
        );
    }

    public function getChargeCoin($skip = 0, $limit = 10, $searchTerm = "", $sortBy, $sortDir){
        $db = $this->_getDb();
        $where = " AND 1=1";
        if($searchTerm != ""){
            $where = " AND (u.`custom_title` LIKE '%". $db->quoteLike($searchTerm) ."%' OR u.`user_id` LIKE '%". $db->quoteLike($searchTerm) ."%')";
        }
        if($sortBy != "" && $sortDir !=""){
            $order = " u.`".$sortBy."` ".strtoupper($sortDir)." ";
        }else{
             $order = " cl.`created_at` DESC";
        }
        $items = $db->fetchAll("
            SELECT
                u.user_id,
                u.custom_title,
                u.email,
                u.phone,
                cl.coin_change,
                cl.coin_before,
                cl.coin_after,
                cl.created_at
            FROM #__coin_logs cl
            LEFT JOIN #__user u
            ON u.`token` = cl.`token`
            WHERE  cl.`type` = 'napthe' ". $where ."
            ORDER BY
                ".$order."
            LIMIT
            ". $skip .",". $limit ."
        ");
        $count = $db->fetchRow("
			SELECT COUNT(*) AS 'total'
			FROM #__coin_logs cl
			LEFT JOIN #__user u
            ON u.`token` = cl.`token`
			WHERE  cl.`type` = 'napthe' ". $where ."
        ");
        return array(
            'rows' => $items,
            'total' => $count['total']
        );
    }

    public function getConsumeCoin($skip = 0, $limit = 10, $searchTerm = "", $sortBy, $sortDir){
        $db = $this->_getDb();
        $where = " AND 1=1";
        if($searchTerm != ""){
            $where = " AND (u.`custom_title` LIKE '%". $db->quoteLike($searchTerm) ."%' OR u.`user_id` LIKE '%". $db->quoteLike($searchTerm) ."%')";
        }
        if($sortBy != "" && $sortDir !=""){
            $order = " u.`".$sortBy."` ".strtoupper($sortDir)." ";
        }else{
            $order = " cl.`created_at` DESC";
        }
        $items = $db->fetchAll("
            SELECT
                u.user_id,
                u.custom_title,
                u.email,
                u.phone,
                cl.type,
                cl.coin_change,
                cl.coin_before,
                cl.coin_after,
                cl.created_at
            FROM #__coin_logs cl
            LEFT JOIN #__user u
            ON u.`token` = cl.`token`
            WHERE  cl.`type` NOT IN ('chuyencoin', 'nhancoin', 'napthe') ". $where ."
            ORDER BY
                ".$order."
            LIMIT
            ". $skip .",". $limit ."
        ");
        $count = $db->fetchRow("
			SELECT COUNT(*) AS 'total'
			FROM #__coin_logs cl
			LEFT JOIN #__user u
            ON u.`token` = cl.`token`
			WHERE  cl.`type` NOT IN ('chuyencoin', 'nhancoin') ". $where ."
        ");
        return array(
            'rows' => $items,
            'total' => $count['total']
        );
    }

    public function updateUser($userId, $data){
        $userDW = $this->_getUserDataWriter();
        $userDW->setExistingData($userId);
        $data = array_filter_key($data, [
                "cover",
                "avatar",
                "coin",
                "custom_title",
                "phone",
                "email",
                "password",
                "birthday",
                "gender",
                "is_supervip",
                "expired_vip",
                "lock_account"
            ]);
        $userDW->bulkSet($data);
        if($userDW->save()) {
            return [
                'error' => 0,
                'data' => $this->getUserById($userId)
            ];
        } else {
            return [
                'error' => 1,
                'message' => 'Không lưu được thông tin',
                'data' => $userDW->getErrors()
            ];
        }
    }

    public function upgradeVip($data){
        $user = $this->getUserById($data['user_id']);
        if(!$user){
            return [
                'error' => 1,
                'message' => 'Không tồn tại thành viên'
            ];
        }
        $expiredVip = $user['expired_vip'];
        if($user['expired_vip'] < time()){
            $expiredVip = time();
        }
        $dataUpdate = [
            'expired_vip' => $expiredVip + $data['num'] * 86400
        ];
        $result = $this->updateUser($data['user_id'], $dataUpdate);
        if($result['error'] == 0){
            $user = $this->getUserById($data['user_id']);
            // save activity log
            $activityLog = [
                'manager_id' => (int)Mava_Application::get("session")->get('manager_id'),
                'activity' => json_encode([
                    'user_id' => $data['user_id'],
                    'custom_title' => $user['custom_title'],
                    'num' => $data['num'],
                    'type' => 'nangvip',
                ])
            ];
            $this->_getManagerActivityModel()->saveActivityLog($activityLog);
            return [
                'error' => 0,
                'message' => 'Nâng cấp VIP thành công',
                'data' => [
                    'user_id' => $data['user_id'],
                    'expired_vip' => date('d/m/Y H:i', $user['expired_vip'])
                ]
            ];
        }
        return $result;
    }

    public function upgradeSupervip($data){
        $user = $this->getUserById($data['user_id']);
        if(!$user){
            return [
                'error' => 1,
                'message' => 'Không tồn tại thành viên'
            ];
        }
        
        $dataUpdate = [
            'is_supervip' => 1
        ];
        $result = $this->updateUser($data['user_id'], $dataUpdate);
        if($result['error'] == 0){
            // save activity log
            $activityLog = [
                'manager_id' => (int)Mava_Application::get("session")->get('manager_id'),
                'activity' => json_encode([
                    'user_id' => $data['user_id'],
                    'custom_title' => $user['custom_title'],
                    'type' => 'nangsupervip',
                ])
            ];
            $this->_getManagerActivityModel()->saveActivityLog($activityLog);
            return [
                'error' => 0,
                'message' => 'Nâng cấp Supper VIP thành công',
                'data' => [
                    'user_id' => $data['user_id'],
                    'is_supervip' => 1
                ]
            ];
        }
        return $result;
    }

    public function rollBackActivity($activityId){
        $act = $this->_getManagerActivityModel()->getById($activityId);
        if(!$act){
            return [
                'error' => 1,
                'message' => 'Không tìm thấy hoạt động',
            ];
        }
        $activity = json_decode($act['activity'], true);
        // rollback user data
        $user = $this->getUserById((int)$activity['user_id']);
        if(!$user){
            return [
                'error' => 1,
                'message' => 'Không tìm thấy thành viên',
            ];
        }
        $dataUpdate = [];
        switch ($activity['type']) {
            case 'nangvip':
                $dataUpdate = [
                    'expired_vip' => $user['expired_vip'] - (int)$activity['num'] * 86400
                ];
                break;
            case 'nangsupervip':
                $dataUpdate = [
                    'is_supervip' => 0
                ];
                break;
            case 'congcoin':
                $dataUpdate = [
                    'coin' => $user['coin'] - (int)$activity['coin']
                ];
                break;
            case 'khoataikhoan':
                $dataUpdate = [
                    'lock_account' => $user['lock_account'] - (int)$activity['day'] * 86400
                ];
                break;
        }
        $userUpdate = $this->updateUser((int)$activity['user_id'], $dataUpdate);
        if($userUpdate['error'] == 0){
            // delete activity log
            $this->_getManagerActivityModel()->deleteActivityLog($activityId);
            return [
                'error' => 0,
                'message' => 'Hủy thành công',
            ];
        }else{
            return [
                'error' => 1,
                'message' => 'Hủy không thành công',
            ];
        }
    }

    public function increaseCoin($data){
        $user = $this->getUserById($data['user_id']);
        if(!$user){
            return [
                'error' => 1,
                'message' => 'Không tồn tại thành viên'
            ];
        }
        $coin = $user['coin'];
        $dataUpdate = [
            'coin' => $coin + $data['coin']
        ];
        $result = $this->updateUser($data['user_id'], $dataUpdate);
        if($result['error'] == 0){
            $user = $this->getUserById($data['user_id']);
            // save activity log
            $activityLog = [
                'manager_id' => (int)Mava_Application::get("session")->get('manager_id'),
                'activity' => json_encode([
                    'user_id' => $data['user_id'],
                    'custom_title' => $user['custom_title'],
                    'coin' => $data['coin'],
                    'type' => 'congcoin',
                ])
            ];
            $this->_getManagerActivityModel()->saveActivityLog($activityLog);
            return [
                'error' => 0,
                'message' => 'Cộng coin thành công',
                'data' => [
                    'user_id' => $data['user_id'],
                    'coin' => $user['coin']
                ]
            ];
        }
        return $result;
    }

    public function resetPassword($data){
        $user = $this->getUserById($data['user_id']);
        if(!$user){
            return [
                'error' => 1,
                'message' => 'Không tồn tại thành viên'
            ];
        }
        $password = $this->_getMavaUserModel()->generalPassword($data['password'], $user['unique_token']);
        $dataUpdate = [
            'password' => $password
        ];
        $result = $this->updateUser($data['user_id'], $dataUpdate);
        if($result['error'] == 0){
            $user = $this->getUserById($data['user_id']);
            // save activity log
            $activityLog = [
                'manager_id' => (int)Mava_Application::get("session")->get('manager_id'),
                'activity' => json_encode([
                    'user_id' => $data['user_id'],
                    'custom_title' => $user['custom_title'],
                    'password' => $data['password'],
                    'type' => 'datlaimatkhau',
                ])
            ];
            $this->_getManagerActivityModel()->saveActivityLog($activityLog);
            return [
                'error' => 0,
                'message' => 'Đặt lại mật khẩu thành công',
                'data' => [
                    'user_id' => $data['user_id'],
                    'password' => $user['password']
                ]
            ];
        }
        return $result;
    }

    public function lockAccount($data){
        $user = $this->getUserById($data['user_id']);
        if(!$user){
            return [
                'error' => 1,
                'message' => 'Không tồn tại thành viên'
            ];
        }
        $lock = $user['lock_account'];
        if($user['lock_account'] < time()){
            $lock = time();
        }
        $dataUpdate = [
            'lock_account' => $lock + $data['day'] * 86400
        ];
        $result = $this->updateUser($data['user_id'], $dataUpdate);
        if($result['error'] == 0){
            $user = $this->getUserById($data['user_id']);
            // save activity log
            $activityLog = [
                'manager_id' => (int)Mava_Application::get("session")->get('manager_id'),
                'activity' => json_encode([
                    'user_id' => $data['user_id'],
                    'custom_title' => $user['custom_title'],
                    'day' => $data['day'],
                    'type' => 'khoataikhoan',
                ])
            ];
            $this->_getManagerActivityModel()->saveActivityLog($activityLog);
            return [
                'error' => 0,
                'message' => 'Khóa tài khoản thành công',
                'data' => [
                    'user_id' => $data['user_id'],
                    'lock_account' => date('d/m/Y H:i', $user['lock_account'])
                ]
            ];
        }
        return $result;
    }

    public function getListFootballTip($skip = 0, $limit = 10, $searchTerm = "", $sortBy, $sortDir){
        $db = $this->_getDb();
        $where = "1=1";
        if($searchTerm != ""){
            $where = "(`pack` LIKE '%". $db->quoteLike($searchTerm) ."%' OR `tip_date` LIKE '%". $db->quoteLike($searchTerm) ."%')";
        }
        if($sortBy != "" && $sortDir !=""){
            $order = " `".$sortBy."` ".strtoupper($sortDir)." ";
        }else{
            $order = " `id` DESC";
        }
        $items = $db->fetchAll("
            SELECT
                *
            FROM #__football_tip
            WHERE ". $where ."
            ORDER BY
                ".$order."
            LIMIT
            ". $skip .",". $limit ."
        ");
        $count = $db->fetchRow("
			SELECT COUNT(*) AS 'total'
			FROM #__football_tip
			WHERE ". $where ."
        ");
        return array(
            'rows' => $items,
            'total' => $count['total']
        );
    }

    public function createFootballTip($pack, $tip_date, $tip){
        //kiểm tra tồn tại football tip
        $fbTip = $this->getFootballTipByDate($pack, $tip_date);
        if($fbTip){
            // tiến hành update
            return $this->updateFootballTip($fbTip['id'], $tip);
        }else{
            // tiến hành tạo mới
            return $this->saveFootballTip($pack, $tip_date, $tip);
        }
    }

    public function saveFootballTip($pack, $tip_date, $tip){
        $footballTipDW = $this->_getFootballTipDataWriter();
        $tip = [json_encode($tip)];
        $tip = '['.implode(',',$tip).']';
        $footballTipDW->bulkSet([
            'pack' => $pack,
            'tip_date' => $tip_date,
            'tip' => $tip,
            'reg_count' => 0
        ]);
        if($footballTipDW->save()){
            return [
                'error' => 0,
                'data' => $footballTipDW->get('id')
            ];
        }
        Mava_Log::error($footballTipDW->getErrors());
        return [
            'error' => 1,
            'message' => 'Không thể lưu dữ liệu',
            'data' => $footballTipDW->getErrors()
        ];
    }

    public function updateFootballTip($id, $tip){
	    $footballTipData = $this->getFootballTipById($id);
        if(Mava_String::isJson($footballTipData['tip'])){
            $tipData = @json_decode($footballTipData['tip'], true);
            array_push($tipData, $tip);
            $result = [];
            if(count($tipData) > 0) {
                foreach ($tipData as $data){
                    $result[] = json_encode($data);
                }
            }
        }else{
            $result = [json_encode($tip)];
        }
        $result = '['.implode(',',$result).']';
        $footballTipDW = $this->_getFootballTipDataWriter();
        $footballTipDW->setExistingData($id);
        $footballTipDW->bulkSet([
            'tip' => $result
        ]);
        if($footballTipDW->save()){
            return [
                'error' => 0,
                'data' => $footballTipDW->get('id')
            ];
        }
        Mava_Log::error($footballTipDW->getErrors());
        return [
            'error' => 1,
            'message' => 'Không thể lưu dữ liệu',
            'data' => $footballTipDW->getErrors()
        ];
    }

    public function getFootballTipById($id){
        return $this->_getDb()->fetchRow("SELECT * FROM #__football_tip WHERE `id`='". (int)$id ."'");
    }

    public function getFootballTipByDate($pack, $tip_date){
        return $this->_getDb()->fetchRow("SELECT * FROM #__football_tip WHERE `tip_date`='". $tip_date ."' AND `pack`='". (int)$pack ."'");
    }

    public function getFootballTipOfFixture($pack, $tip_date, $fixture_id){
	    $result = $this->_getDb()->fetchRow("SELECT * FROM #__football_tip WHERE `tip_date`='". $tip_date ."' AND `pack`='". (int)$pack ."'");
	    if($result && Mava_String::isJson($result['tip'])){
            $tipData = @json_decode($result['tip'], true);
            if(count($tipData) > 0) {
                foreach ($tipData as $data){
                    if((int)$data['fixture_id'] == $fixture_id){
                        return true;
                        break;
                    }
                }
            }
        }
        return false;
    }

    public function getLotoResultByDate($date, $province) {
        return $this->_getDb()->fetchRow("SELECT * FROM #__loto_result WHERE `result_date`='". date_to_time($date,'-') ."' AND `province`='". $province ."'");
    }

    public function getLotoTipByDate($date, $region_code, $pack){
        return $this->_getDb()->fetchRow("SELECT * FROM #__loto_tip WHERE `tip_date`='". $date ."' AND `region_code`='". $region_code ."' AND `pack`='". (int)$pack ."'");
    }

    public function getListLotoTip($skip = 0, $limit = 10, $searchTerm = "", $sortBy, $sortDir){
        $db = $this->_getDb();
        $where = "1=1";
        if($searchTerm != ""){
            $where = "(`pack` LIKE '%". $db->quoteLike($searchTerm) ."%' OR `tip_date` LIKE '%". $db->quoteLike($searchTerm) ."%')";
        }
        if($sortBy != "" && $sortDir !=""){
            $order = " `".$sortBy."` ".strtoupper($sortDir)." ";
        }else{
            $order = " `id` DESC";
        }
        $items = $db->fetchAll("
            SELECT
                *
            FROM #__loto_tip
            WHERE ". $where ."
            ORDER BY
                ".$order."
            LIMIT
            ". $skip .",". $limit ."
        ");
        $count = $db->fetchRow("
			SELECT COUNT(*) AS 'total'
			FROM #__loto_tip
			WHERE ". $where ."
        ");
        return array(
            'rows' => $items,
            'total' => $count['total']
        );
    }

    public function createLotoTip($data){
	    // kiểm tra đã quay thưởng chưa
        $loto = $this->getLotoResultByDate($data['tip_date'], $data['region_code']);
        if($loto){
            return [
                'error' => 1,
                'message' => 'Ngày '.$data['tip_date'].' tỉnh thành này đã quay thưởng',
            ];
        }
        //kiểm tra tồn tại loto tip
        $lotoTip = $this->getLotoTipByDate($data['tip_date'], $data['region_code'], $data['pack']);
        if($lotoTip){
            if(isset($lotoTip['reg_count']) && $lotoTip['reg_count'] > 0){
                return [
                    'error' => 1,
                    'message' => 'Lô tô tip đã tồn tại',
                ];
            }else{
                // tiến hành update
                return $this->updateLotoTip($lotoTip['id'], $data);
            }
        }else{
            // tiến hành tạo mới
            return $this->saveLotoTip($data);
        }
    }

    public function saveLotoTip($data){
        $lotoTipDW = $this->_getLotoTipDataWriter();
        $lotoTipDW->bulkSet([
            'tip_date' => $data['tip_date'],
            'region_code' => $data['region_code'],
            'pack' => $data['pack'],
            'num_1' => $data['num_1'],
            'num_2' => $data['num_2'],
            'num_3' => $data['num_3'],
            'reg_count' => 0
        ]);
        if($lotoTipDW->save()){
            return [
                'error' => 0,
                'data' => $lotoTipDW->get('id')
            ];
        }
        Mava_Log::error($lotoTipDW->getErrors());
        return [
            'error' => 1,
            'message' => 'Không thể lưu dữ liệu',
            'data' => $lotoTipDW->getErrors()
        ];
    }

    public function updateLotoTip($id, $data){
        $lotoTipDW = $this->_getLotoTipDataWriter();
        $lotoTipDW->setExistingData($id);
        $lotoTipDW->bulkSet([
            'num_1' => $data['num_1'],
            'num_2' => $data['num_2'],
            'num_3' => $data['num_3']
        ]);
        if($lotoTipDW->save()){
            return [
                'error' => 0,
                'data' => $lotoTipDW->get('id')
            ];
        }
        Mava_Log::error($lotoTipDW->getErrors());
        return [
            'error' => 1,
            'message' => 'Không thể cập nhật dữ liệu',
            'data' => $lotoTipDW->getErrors()
        ];
    }

    protected function _getUserDataWriter(){
        return Mava_DataWriter::create('Mava_DataWriter_User');
    }

    protected function _getManagerActivityModel(){
        return $this->getModelFromCache('Manager_Model_ManagerActivity');
    }

    protected function _getMavaUserModel(){
        return $this->getModelFromCache('Mava_Model_User');
    }

    protected function _getFootballTipDataWriter()
    {
        return Mava_DataWriter::create('API_DataWriter_FootballTip');
    }

    protected function _getLotoTipDataWriter()
    {
        return Mava_DataWriter::create('API_DataWriter_LotoTip');
    }
}