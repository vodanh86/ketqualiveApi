<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_Max4dResult extends Mava_Model
{
	public function getList($data) {
        return $result = $this->_getDb()->fetchAll("SELECT * FROM #__vietlott_max4d_result WHERE `id`<". $data['max_id'] ." ORDER BY id DESC LIMIT 0,". $data['limit'] ."");
    }

    public function getLatest() {
        $result = $this->_getDb()->fetchRow("SELECT * FROM #__vietlott_max4d_result ORDER BY id DESC");
        if($result){
            $result = $this->_formatData($result);
            return [
                'error' => 0,
                'message' => '',
                'data' => $result
            ];
        }else{
            return [
                'error' => 1,
                'message' => 'Không tìm thấy dữ liệu'
            ];
        }
    }

    public function getNext($id) {
        $result = $this->_getDb()->fetchRow("SELECT * FROM #__vietlott_max4d_result WHERE `id`>'". (int)$id ."' ORDER BY id ASC");
        if($result){
            $result = $this->_formatData($result);
            return [
                'error' => 0,
                'message' => '',
                'data' => $result
            ];
        }else{
            return [
                'error' => 1,
                'message' => 'Không tìm thấy dữ liệu'
            ];
        }
    }

    public function getPrev($id) {
        $result = $this->_getDb()->fetchRow("SELECT * FROM #__vietlott_max4d_result WHERE `id`<'". (int)$id ."' ORDER BY id DESC");
        if($result){
            $result = $this->_formatData($result);
            return [
                'error' => 0,
                'message' => '',
                'data' => $result
            ];
        }else{
            return [
                'error' => 1,
                'message' => 'Không tìm thấy dữ liệu'
            ];
        }
    }

    protected function _formatData($result){
        $result['g1'] = str_split($result['g1']);
        $result['g2'] = explode("-",$result['g2']);
        $g2 = [];
        foreach($result['g2'] as $item){
            $g2[] = str_split($item);
        }
        $result['g2'] = $g2;

        $result['g3'] = explode("-",$result['g3']);
        $g3 = [];
        foreach($result['g3'] as $item){
            $g3[] = str_split($item);
        }
        $result['g3'] = $g3;

        $result['kk1'] = str_split($result['kk1']);
        $result['kk2'] = str_split($result['kk2']);
        return $result;
    }

}