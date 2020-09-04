<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_Football extends Mava_Model
{

    public function footballTip($token, $package, $platform_type = '', $platform_version = ''){
        $user = $this->_getUserModel()->_getByToken($token);
        if ($user) {
            if (in_array($package, [1, 2, 3])) {
                        // check tip data exist
                        $tipModel = $this->_getFootballTipModel();
                        if ($tip = $tipModel->getTipByDay(date('d-m-Y'), $package)) {
                            // check user buy tip before
                            $tipLogModel = $this->_getFootballTipLogsModel();
                            $buy_before = $tipLogModel->checkBuy($token, date('d-m-Y'), $package);
                            if ($buy_before) {
                                $buy_before['price_formatted'] = number_format((int)$buy_before['price'],0,',','.');
                                return [
                                    'error' => 0,
                                    'message' => 'Bạn đã mua gói này trước đó',
                                    'data' => $buy_before
                                ];
                            } else {
                                // calculate pricing for normal account
                                $packagesPrice = Mava_Application::getConfig('football_package_price');
                                switch ($package) {
                                    case 1:
                                        $price = 0;
                                        break;
                                    case 2:
                                        $price = (int)$packagesPrice['package_2'];
                                        break;
                                    case 3:
                                        $price = (int)$packagesPrice['package_3'];
                                        break;
                                    default:
                                        return [
                                            'error' => 1,
                                            'message' => 'Không tìm thấy gói dịch vụ'
                                        ];
                                        break;
                                }
                                // check coin remain condition
                                if ($user['coin'] < $price) {
                                    return [
                                        'error' => 1,
                                        'message' => 'Không đủ ngân lượng, vui lòng nạp thêm'
                                    ];
                                } else {
                                    // charge coin if need
                                    $charge_success = false;
                                    if ($this->_getUserModel()->changeCoin($token, -$price)) {
                                        //save coin log
                                        $coinLog = [
                                            'token' => $token,
                                            'coin_before' => $user['coin'],
                                            'coin_change' => $price,
                                            'coin_after' => $user['coin'] - $price,
                                            'type' => 'nhantipbongda',
                                        ];
                                        $log_id = $this->_getCoinLogsModel()->saveLog($coinLog);
                                        if ($log_id) {
                                            $charge_success = true;
                                        } else {
                                            $charge_success = false;
                                        }
                                    }
                                    // check charge status
                                    if ($charge_success === true || $price === 0) {
                                        $logData = array(
                                            'token' => $token,
                                            'tip_id' => $tip['id'],
                                            'price' => $price,
                                            'platform_type' => $platform_type,
                                            'platform_version' => $platform_version
                                        );
                                        // buy package
                                        $suggest = $this->_getFootballTipLogsModel()->saveLog($logData);
                                        if ($suggest) {
                                            $tipModel->increment_reg_count($tip['id']);
                                            $suggest['price_formatted'] = number_format((int)$price,0,',','.');
                                            return [
                                                'error' => 0,
                                                'message' => 'Nhận TIP thành công',
                                                'data' => $suggest
                                            ];
                                        } else {
                                            return [
                                                'error' => 1,
                                                'message' => 'Nhận TIP thất bại, xin liên hệ Hotline'
                                            ];
                                        }
                                    } else {
                                        return [
                                            'error' => 1,
                                            'message' => 'Thanh toán không thành công'
                                        ];
                                    }
                                }
                            }
                        } else {
                            return [
                                'error' => 1,
                                'message' => 'Các chuyên gia đang phân tích, xin quay lại sau (#302)'
                            ];
                        }
            } else {
                return [
                    'error' => 1,
                    'message' => 'Không tìm thấy gói dịch vụ'
                ];
            }
        } else {
            return [
                'error' => 1,
                'message' => 'Vui lòng đăng nhập lại'
            ];
        }
    }

    public function getTipHistory($token){
        $user = $this->_getUserModel()->_getByToken($token);
        if(!$user) {
            return false;
        }
        return $this->_getFootballTipLogsModel()->getLogByToken($token);
    }

    public function getMatchDay($data){
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if(!$user) {
            return false;
        }
        return $this->_getFootballCacheModel()->getMatchDay($data);
    }

    public function getMatchDetail($data){
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if(!$user) {
            return false;
        }
        return $this->_getFootballCacheModel()->getMatchDetail($data);
    }

    public function getLeagueDetail($data){
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if(!$user) {
            return false;
        }
        return $this->_getFootballCacheModel()->getLeagueDetail($data);
    }

    /**
     * @return API_Model_User
     */
    protected function _getUserModel()
    {
        return $this->getModelFromCache('API_Model_User');
    }

    /**
     * @return API_Model_FootballTip
     */
    protected function _getFootballTipModel()
    {
        return $this->getModelFromCache('API_Model_FootballTip');
    }

    /**
     * @return API_Model_FootballTipLogs
     */
    protected function _getFootballTipLogsModel()
    {
        return $this->getModelFromCache('API_Model_FootballTipLogs');
    }

    /**
     * @return API_Model_CoinLogs
     */
    protected function _getCoinLogsModel()
    {
        return $this->getModelFromCache('API_Model_CoinLogs');
    }

    /**
     * @return API_Model_FootballCache
     */
    protected function _getFootballCacheModel()
    {
        return $this->getModelFromCache('API_Model_FootballCache');
    }

}