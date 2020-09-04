<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_Vietlott extends Mava_Model
{
	public function getMegaList($data) {
		$user = $this->_getUserModel()->_getByToken($data['token']);
        if($user) {
            return $this->_getMegaResultModel()->getList($data);
        }else{
            return [
                'error' => 1,
                'message' => "Vui lòng đăng nhập lại"
            ];
        }
    }

    public function getMax4dLatest($token) {
        $user = $this->_getUserModel()->_getByToken($token);
        if($user) {
            return $this->_getMax4dResultModel()->getLatest();
        }else{
            return [
                'error' => 1,
                'message' => "Vui lòng đăng nhập lại"
            ];
        }
    }

    public function getMax4dNext($token, $id) {
        $user = $this->_getUserModel()->_getByToken($token);
        if($user) {
            return $this->_getMax4dResultModel()->getNext($id);
        }else{
            return [
                'error' => 1,
                'message' => "Vui lòng đăng nhập lại"
            ];
        }
    }

    public function getMax4dPrev($token, $id) {
        $user = $this->_getUserModel()->_getByToken($token);
        if($user) {
            return $this->_getMax4dResultModel()->getPrev($id);
        }else{
            return [
                'error' => 1,
                'message' => "Vui lòng đăng nhập lại"
            ];
        }
    }

    public function getPowerList($data) {
        $user = $this->_getUserModel()->_getByToken($data['token']);
        if($user) {
            return $this->_getPowerResultModel()->getList($data);
        }else{
            return [
                'error' => 1,
                'message' => "Vui lòng đăng nhập lại"
            ];
        }
    }

    /**
     * @return API_Model_User
     */
    protected function _getUserModel()
    {
        return $this->getModelFromCache('API_Model_User');
    }

    /**
     * @return API_Model_MegaResult
     */
    protected function _getMegaResultModel()
    {
        return $this->getModelFromCache('API_Model_MegaResult');
    }

    /**
     * @return API_Model_Max4dResult
     */
    protected function _getMax4dResultModel()
    {
        return $this->getModelFromCache('API_Model_Max4dResult');
    }

    /**
     * @return API_Model_PowerResult
     */
    protected function _getPowerResultModel()
    {
        return $this->getModelFromCache('API_Model_PowerResult');
    }
}