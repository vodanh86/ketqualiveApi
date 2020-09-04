<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_BuyVipLogs extends Mava_Model
{
    /**
     * @param int $id
     * @return bool | array
     */
    public function getById($id)
    {
        return $this->_getDb()->fetchRow("SELECT * FROM #__buy_vip_logs WHERE `id`=" . (int)$id);
    }

    public function saveBuyVipLogs($data, $e){
        $buyVipLogDW = $this->_getBuyVipLogsDataWriter();
        $buyVipLogDW->bulkSet(array(
            'token' => $data['token'],
            'num' => $data['num'],
            'price' => $data['price'],
            'total_amount' => $data['total_amount'],
            'coin' => $data['coin'],
            'error' => $e
        ));
        $buyVipLogDW->save();
    }

    /**
     * @return API_DataWriter_BuyVipLogs
     */
    protected function _getBuyVipLogsDataWriter()
    {
        return Mava_DataWriter::create('API_DataWriter_BuyVipLogs');
    }
}