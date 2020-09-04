<?php
class Football_Model_Result extends Mava_Model {
	public function updateResultFootballTip($date){
		$football_tips = $this->_getDb()->fetchAll("SELECT * FROM #__football_tip WHERE `tip_date` = '". $date ."'");
		$tip_correct_ids = [];
        // kiểm tra để lấy tip_id đúng
		if($football_tips && count($football_tips) > 0){
			foreach ($football_tips as $fb_tip) {
				if(isset($fb_tip['tip']) && Mava_String::isJson($fb_tip['tip'])){
                	$tips = @json_decode($fb_tip['tip'], true);
                	if(count($tips) > 0){
                	    $corrects = [];
                		foreach ($tips as $tip) {
                            $is_correct = 1;
                            // kiểm tra trận đấu đã thi đấu đc > 90 phút chưa
                			if(time() - $tip['timestamp'] > 90*60){
                                $visitor = Mava_Visitor::getInstance();
                                $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
                                // call api match detail để kiểm tra kết quả
                                $data = [
                                    'token' => $token,
                                    'match_id' => $tip['fixture_id']
                                ];
                                $result = Mava_API::call('football/match-detail', $data);
                                if($result['error'] == 0){
                                    $match_detail = $result['data'];
                                    // kiểm tra trận đấu đã kết thúc chưa
                                    if(isset($match_detail['general']) && isset($match_detail['general'][0]) && $match_detail['general'][0]->statusShort == 'FT'){
                                        $sum = (int)$match_detail['general'][0]->goalsHomeTeam + (int)$match_detail['general'][0]->goalsAwayTeam;
                                        if($tip['taixiu'] == 'Tài'){
                                            if($sum <= $tip['num']){
                                                $is_correct = 0;
                                            }
                                        }else{
                                            if($sum > $tip['num']){
                                                $is_correct = 0;
                                            }
                                        }
                                    }
                                }
                			}
                            $corrects[] = $is_correct;
                            if(in_array(1,$corrects)){
                                $tip_correct_ids[$fb_tip['id']] = 1;
                                break;
                            }else {
                                $tip_correct_ids[$fb_tip['id']] = 0;
                            }
                		}
                	}else{
                		//todo
                	}
	            }else{
	                //todo
	            }
			}
		}
        $data_update_formated = [];
        if(count($tip_correct_ids) > 0){
            foreach ($tip_correct_ids as $key=>$value){
                $data_update_formated[] = "('". $key ."','". (int)$value ."')";
            }
            $result = $this->_getDb()->query('
                INSERT INTO #__football_tip(`id`,`is_correct`) VALUES'. implode(',', $data_update_formated) .' 
                ON DUPLICATE KEY UPDATE 
                `id`=VALUES(`id`),
                `is_correct`=VALUES(`is_correct`)
            ');
            if($result){
                return [
                    'error' => 0,
                    'result' => count($tip_correct_ids)
                ];
            }else{
                return [
                    'error' => 1,
                    'result' => $result
                ];
            }
        }else{
            return [
                'error' => 0,
                'result' => 0
            ];
        }
    }

    public function refundCoinForUser($date){
        $logs = $this->_getDb()->fetchAll("
            SELECT
            fb_log.id,
            fb_log.token,
            fb_log.price
            FROM #__football_tip fb_tip
            INNER JOIN (
                SELECT id, tip_id, token, price, is_refunded
                FROM #__football_tip_logs
                WHERE is_refunded = 0
            ) fb_log ON fb_log.`tip_id` = fb_tip.`id`
            WHERE fb_tip.`tip_date` = '". $date ."' AND fb_tip.`is_correct` = 0");

        $log_ids = [];
        $data_user_update = [];
        if($logs && count($logs) > 0){
            foreach ($logs as $log){
                $log_ids[] = $log['id'];
                if(isset($data_user_update[$log['token']])){
                    $data_user_update[$log['token']] = $data_user_update[$log['token']] + $log['price'];
                }else{
                    $data_user_update[$log['token']] = $log['price'];
                }
            }
        }
        $data_user_update_formated = [];
        if(count($data_user_update) > 0){
            foreach ($data_user_update as $key=>$value){
                $data_user_update_formated[] = "('". $key ."','". (int)$value ."')";
            }
            // hoàn coin cho user
            $update_user_result = $this->_getDb()->query('
                INSERT INTO #__user(`token`,`coin`) VALUES'. implode(',', $data_user_update_formated) .' 
                ON DUPLICATE KEY UPDATE 
                `token`=VALUES(`token`),
                `coin`=`coin` + VALUES(`coin`)
            ');

            if($update_user_result){
                if(count($log_ids) > 0){ // update log thành đã xử lý hoàn coin
                    $update_log_result = $this->_getDb()->query("UPDATE #__football_tip_logs SET `is_refunded` = 1 WHERE `id` IN (". Mava_String::doImplode($log_ids) .")");
                    if($update_log_result){
                        return [
                            'error' => 0,
                            'result' => count($data_user_update)
                        ];
                    }else{
                        return [
                            'error' => 1,
                            'result' => $update_log_result
                        ];
                    }
                }
            }else{
                return [
                    'error' => 1,
                    'result' => $update_user_result
                ];
            }
        }else{
            return [
                'error' => 0,
                'result' => 0
            ];
        }
    }
}