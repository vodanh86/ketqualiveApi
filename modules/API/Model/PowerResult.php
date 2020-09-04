<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_PowerResult extends Mava_Model
{
	public function getList($data) {
        $result = $this->_getDb()->fetchAll("SELECT * FROM #__vietlott_power_result WHERE `id`<". ((int)$data['max_id']>0?(int)$data['max_id']:999999) ." ORDER BY id DESC LIMIT 0,". $data['limit'] ."");
        if($result){
            return [
                'error' => 0,
                'message' => '',
                'data' => [
                    'latest' => $this->getLatestData(),
                    'items' => $result
                ]
            ];
        }else{
            return [
                'error' => 1,
                'message' => 'Không tìm thấy kết quả'
            ];
        }
    }

    public function getLatestData(){
        $data = [
            'next_date' => '',
            'next_time' => '',
            'jackpot_1' => '',
            'jackpot_2' => ''
        ];
        $latest = $this->_getDb()->fetchRow("SELECT * FROM #__vietlott_power_result ORDER BY id DESC");
        if($latest){
            $latestDay = date('w', $latest['result_date']) + 1;
            if($latestDay == 7){
                $nextDate = $latest['result_date'] + 3*86400;
            }else{
                $nextDate = $latest['result_date'] + 2*86400;
            }
            $data = [
                'next_date' => (string)$nextDate,
                'next_time' => date('d-m-Y', $nextDate),
                'jackpot_1' => $latest['jackpot_1'],
                'jackpot_2' => $latest['jackpot_2']
            ];
        }
        return $data;
    }
}