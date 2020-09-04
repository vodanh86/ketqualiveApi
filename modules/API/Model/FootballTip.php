<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_FootballTip extends Mava_Model
{
    /**
     * @param int $id
     * @return bool | array
     */
    public function getById($id){
        return $this->_getDb()->fetchRow("SELECT * FROM #__football_tip WHERE `id`=" . (int)$id);
    }

    public function getTipByDay($day, $package) {
        $result = $this->_getDb()->fetchRow("SELECT * FROM #__football_tip WHERE `tip_date`='". $day ."' AND `pack`='". $package ."'");
        if($result) {
            if(Mava_String::isJson($result['tip'])){
                $result['tip'] = @json_decode($result['tip'], true);
            }else{
                $result['tip'] = [];
            }
            return $result;
        }else{
            return false;
        }
    }

    public function increment_reg_count($id){
        return $this->_getDb()->query("UPDATE #__football_tip SET `reg_count`=`reg_count`+1 WHERE `id`='". (int)$id ."'");
    }

    /**
     * @return API_Model_FootballTipLogs
     */
    protected function _getFootballTipLogsModel()
    {
        return $this->getModelFromCache('API_Model_FootballTipLogs');
    }

    /**
     * @return API_DataWriter_FootballTip
     */
    protected function _getFootballTipDataWriter()
    {
        return Mava_DataWriter::create('API_DataWriter_FootballTip');
    }
}