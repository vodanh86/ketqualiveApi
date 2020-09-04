<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_MegaResult extends Mava_Model
{
	public function getList($data) {
        $result = $this->_getDb()->fetchAll("SELECT * FROM #__vietlott_mega_result WHERE `id`<". ((int)$data['max_id']>0?(int)$data['max_id']:999999) ." ORDER BY id DESC LIMIT 0,". $data['limit']);
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
                'message' => "Không có kết quả nào"
            ];
        }
    }

    public function getLatestData(){
        $data = [
            'next_date' => '',
            'next_time' => '',
            'jackpot' => ''
        ];
        $latest = $this->_getDb()->fetchRow("SELECT * FROM #__vietlott_mega_result ORDER BY id DESC");
        if($latest){
            $latestDay = date('w', $latest['result_date']) + 1;
            if($latestDay == 1){
                $nextDate = $latest['result_date'] + 3*86400;
            }else{
                $nextDate = $latest['result_date'] + 2*86400;
            }
            $data = [
                'next_date' => (string)$nextDate,
                'next_time' => date('d-m-Y', $nextDate),
                'jackpot' => $latest['jackpot']
            ];
        }
        return $data;
    }
}