<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 4/9/2019
 * Time: 8:18 PM
 */
class API_Controller_Football extends API_Controller {
    public function tipAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['package']) || trim($postData['package']) == "") {
                return $this->responseError("Gói là bắt buộc", []);
            }else{
                $result = $this->_getFootballModel()->footballTip($postData['token'], $postData['package'], $postData['platform_type'], $postData['platform_version']);
                if($result['error'] === 0) {
                    $buyModel = $this->_getFootballTipLogsModel();
                    $buy = $buyModel->getBuyStatus($postData['token'], date('d-m-Y'));
                    $result['data']['buy_pack_1'] = $buy['pack_1'] ? 1 : 0;
                    $result['data']['buy_pack_2'] = $buy['pack_2'] ? 1 : 0;
                    $result['data']['buy_pack_3'] = $buy['pack_3'] ? 1 : 0;

                    $result['data']['user'] = $this->_getUserModel()->_getInfoByToken($postData['token']);
                    return $this->responseSuccess("", $result['data']);
                }else{
                    return $this->responseError($result['message'], []);
                }
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function checkBuyTipAction() {
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if($postData['token'] === "" || $postData['region_code'] === ""){
                return $this->responseError("Yêu cầu không hợp lệ :(");
            }else{
                $buyModel = $this->_getFootballTipLogsModel();
                return $this->responseSuccess("",
                    $buyModel->getBuyStatus($postData['token'], date('d-m-Y'))
                );
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function tipHistoryAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            $result = $this->_getFootballModel()->getTipHistory($postData['token']);
            if($result === false){
                return $this->responseError("Có lỗi xảy ra", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function matchDayAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['date']) || trim($postData['date']) == "") {
                return $this->responseError("Ngày là bắt buộc", []);
            }
            $result = $this->_getFootballModel()->getMatchDay($postData);
            if(!$result) {
                return $this->responseError("Không có kết quả bạn yêu cầu", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra #1", []);
        }
    }

    public function matchDetailAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['match_id']) || trim($postData['match_id']) == "") {
                return $this->responseError("ID trận đấu là bắt buộc", []);
            }
            $result = $this->_getFootballModel()->getMatchDetail($postData);
            if(!$result) {
                return $this->responseError("Không có kết quả bạn yêu cầu", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function leagueAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['league_id']) || trim($postData['league_id']) == "") {
                return $this->responseError("ID giải đấu là bắt buộc", []);
            }
            $result = $this->_getFootballModel()->getLeagueDetail($postData);
            if(!$result) {
                return $this->responseError("Không có kết quả bạn yêu cầu", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    /**
     * @return API_Model_Football
     */
    protected function _getFootballModel()
    {
        return $this->getModelFromCache('API_Model_Football');
    }

    /**
     * @return API_Model_FootballTipLogs
     */
    protected function _getFootballTipLogsModel()
    {
        return $this->getModelFromCache('API_Model_FootballTipLogs');
    }
    /**
     * @return API_Model_FootballTip
     */
    protected function _getFootballTipModel()
    {
        return $this->getModelFromCache('API_Model_FootballTip');
    }
    /**
     * @return API_Model_User
     */
    protected function _getUserModel()
    {
        return $this->getModelFromCache('API_Model_User');
    }
}