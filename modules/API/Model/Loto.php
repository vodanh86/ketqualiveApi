<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_Loto extends Mava_Model
{

    public function lotoSuggest($token, $region_code, $package, $platform_type = '', $platform_version = '')
    {
        $user = $this->_getUserModel()->_getByToken($token);
        if ($user) {
            if (in_array($package, [1, 2, 3])) {
                // check valid region
                $provinceModel = $this->_getProvinceModel();
                if ($province = $provinceModel->getByCode($region_code)) {
                    if(
                        Mava_Application::getConfig('environment') !== 'dev' && (
                        ($province['region'] === "bac" && intval(date('H')) > 18 && intval(date('i')) > 50) ||
                        ($province['region'] === "trung" && intval(date('H')) > 17 && intval(date('i')) > 50) ||
                        ($province['region'] === "nam" && intval(date('H')) > 16 && intval(date('i')) > 50)
                        )){
                        return [
                            'error' => 1,
                            'message' => 'Các chuyên gia đang phân tích, xin quay lại vào ngày mai (#201)'
                        ];
                    }else{
                        // check tip data exist
                        $tipModel = $this->_getLotoTipModel();
                        if ($tip = $tipModel->getTipByDay(date('d-m-Y'), $region_code, $package)) {
                            // check user buy tip before
                            $tipLogModel = $this->_getLotoSuggestLogsModel();
                            $buy_before = $tipLogModel->checkBuy($token, date('d-m-Y'), $region_code, $package);
                            if ($buy_before) {
                                $buy_before['price_formatted'] = number_format((int)$buy_before['price'],0,',','.');
                                if ((int)$buy_before['pack'] !== 3) {
                                    $buy_before['num_1'] = sprintf('%02d', $buy_before['num_1']);
                                    $buy_before['num_2'] = sprintf('%02d', $buy_before['num_2']);
                                    $buy_before['num_3'] = sprintf('%02d', $buy_before['num_3']);
                                }
                                return [
                                    'error' => 0,
                                    'message' => 'Bạn đã mua gói này trước đó',
                                    'data' => $buy_before
                                ];
                            } else {
                                // calculate pricing for normal account
                                $packagesPrice = Mava_Application::getConfig('loto_package_price');
                                switch ($package) {
                                    case 1:
                                        // free for super vip and vip
                                        if($user['is_supervip'] === 1 || $user['expired_vip'] > time()){
                                            $price = 0;
                                        }else{
                                            $price = (int)$packagesPrice['package_1'];
                                        }
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
                                            'type' => 'nhansovip',
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
                                        $suggest = $this->_getLotoSuggestLogsModel()->saveLog($logData);
                                        if ($suggest) {
                                            $tipModel->increment_reg_count($tip['id']);
                                            $suggest['price_formatted'] = number_format((int)$price,0,',','.');
                                            if ((int)$suggest['pack'] !== 3) {
                                                $suggest['num_1'] = sprintf('%02d', $suggest['num_1']);
                                                $suggest['num_2'] = sprintf('%02d', $suggest['num_2']);
                                                $suggest['num_3'] = sprintf('%02d', $suggest['num_3']);
                                            }
                                            return [
                                                'error' => 0,
                                                'message' => 'Mua số VIP thành công',
                                                'data' => $suggest
                                            ];
                                        } else {
                                            return [
                                                'error' => 1,
                                                'message' => 'Mua số VIP thất bại, xin liên hệ Hotline'
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
                                'message' => 'Các chuyên gia đang phân tích, xin quay lại vào ngày mai (#202)'
                            ];
                        }
                    }

                } else {
                    return [
                        'error' => 1,
                        'message' => 'Không tìm thấy khu vực bạn yêu cầu'
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

    public function lotoLucky($token)
    {
        $user = $this->_getUserModel()->_getByToken($token);
        if (!$user) {
            return false;
        }
        return $this->_getLLotoLuckyModel()->getLotoLucky();
    }

    public function getSuggestHistory($token)
    {
        $user = $this->_getUserModel()->_getByToken($token);
        if (!$user) {
            return false;
        }
        return $this->_getLotoSuggestLogsModel()->getLogByToken($token);
    }

    public function getResult($data)
    {
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if ($user) {
            return $this->_getLotoResultModel()->getResultByDate($data['date'], $data['region_code']);
        }else{
            return [
                'error' => 1,
                'message' => 'Vui lòng đăng nhập lại'
            ];
        }
    }

    public function getLatest($data)
    {
        $cache_key = md5("API_Model_Loto-getLatest-". $data['region_code']);
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if ($user) {
            $resultModel = $this->_getLotoResultModel();
            if($resultModel->hasLive(date('d/m/Y'), $data['region_code'])){
                $cache_second = 5;
            }else{
                $cache_second = 30;
            }
            if($result = Mava_Application::getCache($cache_key)){
                Mava_Log::info('getLatest from cache');
                return $result;
            }else{
                Mava_Log::info('getLatest from database');
                $result = $resultModel->getLatest($data['region_code'], (int)$data['include_province']===1?true:false);
                Mava_Application::setCache($cache_key, $result, $cache_second);
                return $result;
            }
        }else{
            return [
                'error' => 1,
                'message' => 'Vui lòng đăng nhập lại'
            ];
        }
    }


    public function getNext($data)
    {
        $cache_key = md5("API_Model_Loto-getNext-". $data['region_code'] .'-'. (int)$data['loto_id']);
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if ($user) {
            $resultModel = $this->_getLotoResultModel();
            if($result = Mava_Application::getCache($cache_key)){
                Mava_Log::info('getNext from cache');
                return $result;
            }else{
                Mava_Log::info('getNext from database');
                $result = $resultModel->getNext($data['region_code'], $data['loto_id']);
                if($result['error'] == 0 && $result['data']['has_live'] == 1){
                    $cache_second = 5;
                }else{
                    $cache_second = 30;
                }
                Mava_Application::setCache($cache_key, $result, $cache_second);
                return $result;
            }
        }else{
            return [
                'error' => 1,
                'message' => 'Vui lòng đăng nhập lại'
            ];
        }
    }


    public function getPrev($data)
    {
        $cache_key = md5("API_Model_Loto-getPrev-". $data['region_code'] .'-'. (int)$data['loto_id']);
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if ($user) {
            $resultModel = $this->_getLotoResultModel();
            if($result = Mava_Application::getCache($cache_key)){
                Mava_Log::info('getPrev from cache');
                return $result;
            }else{
                Mava_Log::info('getPrev from database');
                $result = $resultModel->getPrev($data['region_code'], $data['loto_id']);
                $cache_second = 30;
                Mava_Application::setCache($cache_key, $result, $cache_second);
                return $result;
            }
        }else{
            return [
                'error' => 1,
                'message' => 'Vui lòng đăng nhập lại'
            ];
        }
    }


    public function prayOneNumber($data)
    {
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if (!$user) {
            return false;
        }
        return $this->_getLotoResultModel()->prayOneNumberResult($data);
    }

    public function prayAllNumber($data)
    {
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if (!$user) {
            return false;
        }
        return $this->_getLotoResultModel()->prayAllNumberResult($data);
    }

    public function prayOneWayNumber($data)
    {
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if (!$user) {
            return false;
        }
        return $this->_getLotoResultModel()->prayOneWayNumberResult($data);
    }

    public function prayDoubleNumber($data)
    {
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if (!$user) {
            return false;
        }
        return $this->_getLotoResultModel()->prayDoubleNumberResult($data);
    }

    public function praySpecialNumber($data)
    {
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if (!$user) {
            return false;
        }
        return $this->_getLotoResultModel()->praySpecialNumberResult($data);
    }

    public function hardyNumber($data)
    {
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if (!$user) {
            return false;
        }
        return $this->_getLotoResultModel()->hardyNumberResult($data['region_code']);
    }

    public function frequencyNumber($data)
    {
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if (!$user) {
            return false;
        }
        return $this->_getLotoResultModel()->frequencyNumberResult($data);
    }

    public function totalNumber($data)
    {
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if (!$user) {
            return false;
        }
        return $this->_getLotoResultModel()->totalNumberResult($data);
    }

    public function timesNumber($data)
    {
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if (!$user) {
            return false;
        }
        return $this->_getLotoResultModel()->timesNumberResult($data);
    }

    public function fallenNumber($data)
    {
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if (!$user) {
            return false;
        }
        return $this->_getLotoResultModel()->fallenNumberResult($data);
    }

    public function specialNumber($data)
    {
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if (!$user) {
            return false;
        }
        return $this->_getLotoResultModel()->specialNumberResult($data);
    }

    /**
     * @return API_Model_User
     */
    protected function _getUserModel()
    {
        return $this->getModelFromCache('API_Model_User');
    }

    /**
     * @return API_Model_LotoSuggestLogs
     */
    protected function _getLotoSuggestLogsModel()
    {
        return $this->getModelFromCache('API_Model_LotoSuggestLogs');
    }

    /**
     * @return API_Model_LotoLucky
     */
    protected function _getLLotoLuckyModel()
    {
        return $this->getModelFromCache('API_Model_LotoLucky');
    }

    /**
     * @return API_Model_CoinLogs
     */
    protected function _getCoinLogsModel()
    {
        return $this->getModelFromCache('API_Model_CoinLogs');
    }

    /**
     * @return API_Model_LotoResult
     */
    protected function _getLotoResultModel()
    {
        return $this->getModelFromCache('API_Model_LotoResult');
    }


    /**
     * @return Loto_Model_Province
     */
    protected function _getProvinceModel()
    {
        return $this->getModelFromCache('Loto_Model_Province');
    }


    /**
     * @return API_DataWriter_LotoTip
     * @throws Mava_Exception
     */
    protected function _getLotoTipDataWriter()
    {
        return Mava_DataWriter::create('API_DataWriter_LotoTip');
    }

    /**
     * @return API_Model_LotoTip
     */
    protected function _getLotoTipModel()
    {
        return $this->getModelFromCache('API_Model_LotoTip');
    }
}