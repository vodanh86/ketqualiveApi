<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_FootballTipLogs extends Mava_Model
{
    /**
     * @param int $id
     * @return bool | array
     */
    public function getById($id)
    {
        return $this->_getDb()->fetchRow("SELECT l.*,t.tip_date,t.pack,t.tip FROM #__football_tip_logs l, #__football_tip t WHERE l.tip_id=t.id AND l.`id`=" . (int)$id);
    }

    public function saveLog($data){
        $tipLogDW = $this->_getFootballTipLogsDataWriter();
        $tipLogDW->bulkSet($data);
        if($tipLogDW->save()){
            $tip = $this->getById($tipLogDW->get('id'));
            if($tip && Mava_String::isJson($tip['tip'])){
                $tip['tip'] = @json_decode($tip['tip'], true);
                return $tip;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function getLogByToken($token){
        $result = $this->_getDb()->query("SELECT l.*,t.tip_date,t.pack,t.tip FROM #__football_tip_logs l, #__football_tip t WHERE l.tip_id=t.id AND l.`token`='". $token ."' ORDER BY l.id DESC");
        if($result->num_rows > 0){
            $result_formatted = [];
            foreach($result->rows as $item){
                $item['price_formatted'] = number_format((int)$item['price'],0,',','.');
                if(Mava_String::isJson($item['tip'])){
                    $item['tip'] = @json_decode($item['tip'], true);
                }
                $result_formatted[] = $item;
            }
            return $result_formatted;
        }else{
            return [];
        }
    }

    public function checkBuy($token, $day, $package){
        $result = $this->_getDb()->fetchRow("SELECT l.*,t.tip_date,t.pack,t.tip FROM #__football_tip_logs l, #__football_tip t WHERE l.tip_id=t.id AND t.pack='". $package ."' AND t.tip_date='". $day ."' AND l.token='". $token ."'");
        if($result){
            if(Mava_String::isJson($result['tip'])){
                $result['tip'] = @json_decode($result['tip'], true);
            }
            return $result;
        }else{
            return false;
        }
    }

    public function getBuyStatus($token, $day){
        $pack_2 = $this->_getDb()->fetchRow("SELECT COUNT(*) AS 'total' FROM #__football_tip_logs l, #__football_tip t WHERE l.tip_id=t.id AND t.pack='2' AND t.tip_date='". $day ."' AND l.token='". $token ."'");
        $pack_3 = $this->_getDb()->fetchRow("SELECT COUNT(*) AS 'total' FROM #__football_tip_logs l, #__football_tip t WHERE l.tip_id=t.id AND t.pack='3' AND t.tip_date='". $day ."' AND l.token='". $token ."'");
        return [
            'pack_1' => true,
            'pack_2' => $pack_2['total']>0,
            'pack_3' => $pack_3['total']>0,
        ];
    }
    /**
     * @return API_DataWriter_FootballTipLogs
     */
    protected function _getFootballTipLogsDataWriter()
    {
        return Mava_DataWriter::create('API_DataWriter_FootballTipLogs');
    }
}