<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_LotoSuggestLogs extends Mava_Model
{
    /**
     * @param int $id
     * @return bool | array
     */
    public function getById($id)
    {
        return $this->_getDb()->fetchRow("SELECT l.*,t.tip_date,t.pack,t.num_1,t.num_2,t.num_3,r.title as 'region_title' FROM #__loto_suggest_logs l, #__loto_tip t, #__loto_province r WHERE r.code=t.region_code AND l.tip_id=t.id AND l.id=" . (int)$id);
    }

    public function saveLog($data){
        $sgLogDW = $this->_getLotoSuggestLogsDataWriter();
        $sgLogDW->bulkSet($data);
        if($sgLogDW->save()){
            return $this->getById($sgLogDW->get('id'));
        }else{
            Mava_Log::error($sgLogDW->getErrors());
            return false;
        }
    }

    public function getLogByToken($token){
        $result = $this->_getDb()->query("SELECT l.*,t.tip_date,t.pack,t.num_1,t.num_2,t.num_3,r.title as 'region_title' FROM #__loto_suggest_logs l, #__loto_tip t, #__loto_province r WHERE l.`token`='". $token ."' AND r.code=t.region_code AND l.tip_id=t.id ORDER BY l.id DESC");
        if($result->num_rows > 0){
            $result_formatted = [];
            foreach($result->rows as $item){
                $item['price_formatted'] = number_format((int)$item['price'],0,',','.');
                if ((int)$item['pack'] !== 3) {
                    $item['num_1'] = sprintf('%02d', $item['num_1']);
                    $item['num_2'] = sprintf('%02d', $item['num_2']);
                    $item['num_3'] = sprintf('%02d', $item['num_3']);
                }
                $result_formatted[] = $item;
            }
            return $result_formatted;
        }else{
            return [];
        }
    }

    public function checkBuy($token, $day, $region, $package){
        return $this->_getDb()->fetchRow("SELECT l.*,t.tip_date,t.pack,t.num_1,t.num_2,t.num_3,r.title as 'region_title' FROM #__loto_suggest_logs l, #__loto_tip t, #__loto_province r WHERE l.tip_id=t.id AND r.code=t.region_code AND t.pack='". $package ."' AND t.region_code='". $region ."' AND t.tip_date='". $day ."' AND l.token='". $token ."'");
    }

    public function getBuyStatus($token, $region, $day){
        $pack_1 = $this->_getDb()->fetchRow("SELECT COUNT(*) AS 'total' FROM #__loto_suggest_logs l, #__loto_tip t WHERE l.tip_id=t.id AND t.pack='1' AND t.region_code='". $region ."' AND t.tip_date='". $day ."' AND l.token='". $token ."'");
        $pack_2 = $this->_getDb()->fetchRow("SELECT COUNT(*) AS 'total' FROM #__loto_suggest_logs l, #__loto_tip t WHERE l.tip_id=t.id AND t.pack='2' AND t.region_code='". $region ."' AND t.tip_date='". $day ."' AND l.token='". $token ."'");
        $pack_3 = $this->_getDb()->fetchRow("SELECT COUNT(*) AS 'total' FROM #__loto_suggest_logs l, #__loto_tip t WHERE l.tip_id=t.id AND t.pack='3' AND t.region_code='". $region ."' AND t.tip_date='". $day ."' AND l.token='". $token ."'");
        return [
            'pack_1' => $pack_1['total']>0,
            'pack_2' => $pack_2['total']>0,
            'pack_3' => $pack_3['total']>0,
        ];
    }

    /**
     * @return API_DataWriter_LotoSuggestLogs
     */
    protected function _getLotoSuggestLogsDataWriter()
    {
        return Mava_DataWriter::create('API_DataWriter_LotoSuggestLogs');
    }
}