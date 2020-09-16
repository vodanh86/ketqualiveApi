<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_CoinLogs extends Mava_Model
{
    /**
     * @param int $id
     * @return bool | array
     */
    public function getById($id)
    {
        return $this->_getDb()->fetchRow("SELECT * FROM #__coin_logs WHERE `id`=" . (int)$id);
    }

    public function saveLog($data){
        $coinLogDW = $this->_getCoinLogsDataWriter();
        $coinLogDW->bulkSet($data);
        if($coinLogDW->save()){
            return $coinLogDW->get('id');
        }
        return false;
    }

    public function getLogByToken($data){
        $result = $this->_getDb()->query("SELECT * FROM #__coin_logs WHERE `token`='". $data['token'] ."' AND id<". $data['max_id'] ." ORDER BY id DESC LIMIT 0,". $data['limit'] ."");
        return $result->rows;
    }

    public function getTotalCoinCharge($token) {
        $result = $this->_getDb()->fetchRow("SELECT SUM(coin_change) as total FROM #__coin_logs WHERE `token`='". $token ."' AND (type='napthe' OR type='napmomo'");
        return (int)$result['total'];
    }

    /**
     * @return API_DataWriter_CoinLogs
     */
    protected function _getCoinLogsDataWriter()
    {
        return Mava_DataWriter::create('API_DataWriter_CoinLogs');
    }
}