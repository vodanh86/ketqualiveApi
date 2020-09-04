<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 4/9/2019
 * Time: 8:18 PM
 */
class API_Controller_Loto extends API_Controller {
    public function demoAction(){
        return $this->responseSuccess("OK", ['name' => 'Hoanh', 'age' => 18]);
    }

    public function suggestAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['region_code']) || trim($postData['region_code']) == "") {
                return $this->responseError("Khu vực là bắt buộc", []);
            }
            if(!isset($postData['package']) || trim($postData['package']) == "") {
                return $this->responseError("Gói là bắt buộc", []);
            }
            $today_loto = Mava_Application::getConfig('loto_schedule/T'. (date('w')+1));
            if(!key_exists(trim($postData['region_code']), $today_loto)){
                return $this->responseError("Hôm nay khu vực này không quay thưởng", []);
            }
            $result = $this->_getLotoModel()->lotoSuggest(
                $postData['token'],
                $postData['region_code'],
                $postData['package']   ,
                $postData['platform_type'],
                $postData['platform_version']
            );
            if($result['error'] === 1) {
                return $this->responseError($result['message'], []);
            }else {
                $buyModel = $this->_getLotoSuggestLogModel();
                $buy = $buyModel->getBuyStatus($postData['token'], $postData['region_code'], date('d-m-Y'));
                $result['data']['buy_pack_1'] = $buy['pack_1'] ? 1 : 0;
                $result['data']['buy_pack_2'] = $buy['pack_2'] ? 1 : 0;
                $result['data']['buy_pack_3'] = $buy['pack_3'] ? 1 : 0;

                $result['data']['user'] = $this->_getUserModel()->_getInfoByToken($postData['token']);
                return $this->responseSuccess($result['message'], $result['data']);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function luckyAction() {
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            $result = $this->_getLotoModel()->lotoLucky($postData['token']);
            if($result){
                $buyModel = $this->_getLotoSuggestLogModel();
                $buy = $buyModel->getBuyStatus($postData['token'], $postData['region_code'], date('d-m-Y'));
                $result['buy_pack_1'] = $buy['pack_1']?1:0;
                $result['buy_pack_2'] = $buy['pack_2']?1:0;
                $result['buy_pack_3'] = $buy['pack_3']?1:0;
                if((int)$postData['include_province'] === 1){
                    $today_loto = Mava_Application::getConfig('loto_schedule/T'. (date('w')+1));
                    $result['provinces'] = [];
                    foreach($today_loto as $k => $v){
                        $result['provinces'][] = [
                            'code' => $k,
                            'title' => $v[2]
                        ];
                    }
                }
                return $this->responseSuccess("", $result);
            }else{
                return $this->responseError("Có lỗi xảy ra", []);
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
                $buyModel = $this->_getLotoSuggestLogModel();
                return $this->responseSuccess("",
                    $buyModel->getBuyStatus($postData['token'], $postData['region_code'], date('d-m-Y'))
                );
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function suggestHistoryAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            $result = $this->_getLotoModel()->getSuggestHistory($postData['token']);
            if($result){
                return $this->responseSuccess("", $result);
            }else{
                return $this->responseError("Có lỗi xảy ra", []);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function getAction()
    {
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['region_code']) || trim($postData['region_code']) == "") {
                return $this->responseError("Khu vực là bắt buộc", []);
            }
            if(!isset($postData['date']) || trim($postData['date']) == "") {
                return $this->responseError("Ngày là bắt buộc", []);
            }
            $result = $this->_getLotoModel()->getResult($postData);
            if($result['error'] === 0){
                return $this->responseSuccess($result['message'], $result['data']);
            }else{
                return $this->responseError($result, []);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function getNextAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['region_code']) || trim($postData['region_code']) == "") {
                return $this->responseError("Khu vực là bắt buộc", []);
            }
            if(!isset($postData['loto_id']) || (int)$postData['loto_id'] == 0) {
                return $this->responseError("Không tìm thấy kết quả", []);
            }
            $result = $this->_getLotoModel()->getNext($postData);
            if($result['error'] === 0){
                return $this->responseSuccess($result['message'], $result['data']);
            }else{
                return $this->responseError($result, []);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function getPrevAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['region_code']) || trim($postData['region_code']) == "") {
                return $this->responseError("Khu vực là bắt buộc", []);
            }
            if(!isset($postData['loto_id']) || (int)$postData['loto_id'] == 0) {
                return $this->responseError("Không tìm thấy kết quả", []);
            }
            $result = $this->_getLotoModel()->getPrev($postData);
            if($result['error'] === 0){
                return $this->responseSuccess($result['message'], $result['data']);
            }else{
                return $this->responseError($result, []);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function getLatestAction(){
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['region_code']) || trim($postData['region_code']) == "") {
                return $this->responseError("Khu vực là bắt buộc", []);
            }
            $result = $this->_getLotoModel()->getLatest($postData);
            if($result['error'] === 0){
                return $this->responseSuccess($result['message'], $result['data']);
            }else{
                return $this->responseError($result, []);
            }
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function prayOneAction()
    {
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['number']) || trim($postData['number']) == "") {
                return $this->responseError("Số là bắt buộc", []);
            }
            $result = $this->_getLotoModel()->prayOneNumber($postData);
            if(!$result){
                return $this->responseError("Không có kết quả bạn yêu cầu", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function prayAllAction()
    {
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['date']) || trim($postData['date']) == "") {
                return $this->responseError("Ngày là bắt buộc", []);
            }
            if(!isset($postData['region_code']) || trim($postData['region_code']) == "") {
                return $this->responseError("Khu vực là bắt buộc", []);
            }
            $result = $this->_getLotoModel()->prayAllNumber($postData);
            if(!$result){
                return $this->responseError("Không có kết quả bạn yêu cầu", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function prayOneWayAction()
    {
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['date']) || trim($postData['date']) == "") {
                return $this->responseError("Ngày là bắt buộc", []);
            }
            $result = $this->_getLotoModel()->prayOneWayNumber($postData);
            if(!$result){
                return $this->responseError("Không có kết quả bạn yêu cầu", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function prayDoubleAction()
    {
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['date']) || trim($postData['date']) == "") {
                return $this->responseError("Ngày là bắt buộc", []);
            }
            $result = $this->_getLotoModel()->prayDoubleNumber($postData);
            if(!$result){
                return $this->responseError("Không có kết quả bạn yêu cầu", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function praySpecialAction()
    {
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['date']) || trim($postData['date']) == "") {
                return $this->responseError("Ngày là bắt buộc", []);
            }
            $result = $this->_getLotoModel()->praySpecialNumber($postData);
            if(!$result){
                return $this->responseError("Không có kết quả bạn yêu cầu", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function hardyAction()
    {
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['region_code']) || trim($postData['region_code']) == "") {
                return $this->responseError("Khu vực là bắt buộc", []);
            }
            $result = $this->_getLotoModel()->hardyNumber($postData);
            if(!$result){
                return $this->responseError("Không có kết quả bạn yêu cầu", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function frequencyAction() {
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['region_code']) || trim($postData['region_code']) == "") {
                return $this->responseError("Khu vực là bắt buộc", []);
            }
            if(!isset($postData['type']) || trim($postData['type']) == "") {
                return $this->responseError("Bộ số là bắt buộc", []);
            }
            if(!isset($postData['limit']) || trim($postData['limit']) == "") {
                return $this->responseError("Số ngày là bắt buộc", []);
            }
            $result = $this->_getLotoModel()->frequencyNumber($postData);
            if(!$result){
                return $this->responseError("Không có kết quả bạn yêu cầu", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function totalAction() {
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['region_code']) || trim($postData['region_code']) == "") {
                return $this->responseError("Khu vực là bắt buộc", []);
            }elseif(!isset($postData['start_date']) || trim($postData['start_date']) == "") {
                return $this->responseError("Ngày bắt đầu là bắt buộc", []);
            }elseif(!isset($postData['end_date']) || trim($postData['end_date']) == "") {
                return $this->responseError("Ngày kết thúc là bắt buộc", []);
            }elseif(!isset($postData['sum']) || trim($postData['sum']) == "") {
                return $this->responseError("Tổng muốn xem là bắt buộc", []);
            }else{
                $result = $this->_getLotoModel()->totalNumber($postData);
                if(!$result){
                    return $this->responseError("Không có kết quả bạn yêu cầu", []);
                }
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function timesAction() {
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['region_code']) || trim($postData['region_code']) == "") {
                return $this->responseError("Khu vực là bắt buộc", []);
            }
            if(!isset($postData['type']) || trim($postData['type']) == "") {
                return $this->responseError("Loại thông kê là bắt buộc", []);
            }
            if(!isset($postData['limit']) || trim($postData['limit']) == "") {
                return $this->responseError("Số lần quay là bắt buộc", []);
            }
            $result = $this->_getLotoModel()->timesNumber($postData);
            if(!$result){
                return $this->responseError("Không có kết quả bạn yêu cầu", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function fallenAction() {
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['region_code']) || trim($postData['region_code']) == "") {
                return $this->responseError("Khu vực là bắt buộc", []);
            }
            $result = $this->_getLotoModel()->fallenNumber($postData);
            if(!$result){
                return $this->responseError("Không có kết quả bạn yêu cầu", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    public function specialAction() {
        if(Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['region_code']) || trim($postData['region_code']) == "") {
                return $this->responseError("Khu vực là bắt buộc", []);
            }
            $result = $this->_getLotoModel()->specialNumber($postData);
            if(!$result){
                return $this->responseError("Không có kết quả bạn yêu cầu", []);
            }
            return $this->responseSuccess("", $result);
        } else {
            return $this->responseError("Có lỗi xảy ra", []);
        }
    }

    /**
     * @return API_Model_Loto
     */
    protected function _getLotoModel()
    {
        return $this->getModelFromCache('API_Model_Loto');
    }

    /**
     * @return API_Model_LotoSuggestLogs
     */
    protected function _getLotoSuggestLogModel()
    {
        return $this->getModelFromCache('API_Model_LotoSuggestLogs');
    }

    /**
     * @return API_Model_User
     */
    protected function _getUserModel()
    {
        return $this->getModelFromCache('API_Model_User');
    }

    /**
     * @return Loto_Model_Province
     */
    protected function _getProvinceModel()
    {
        return $this->getModelFromCache('Loto_Model_Province');
    }
}