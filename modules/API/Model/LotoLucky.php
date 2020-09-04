<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_LotoLucky extends Mava_Model
{
    /**
     * @param int $id
     * @return bool | array
     */
    public function getById($id){
        return $this->_getDb()->fetchRow("SELECT * FROM #__loto_lucky WHERE `id`=" . (int)$id);
    }

    public function getLotoLucky() {

        $result = $this->_getDb()->fetchRow("SELECT * FROM #__loto_lucky WHERE `lucky_date`='". date('d-m-Y') ."'");
        if($result) {
            $result['lucky_number'] = json_decode($result['lucky_number']);
            return $result;
        }
        return $this->saveLotoLucky();
    }

    public function saveLotoLucky(){
        $ltLuckyDW = $this->_getLotoLuckyDataWriter();
        $randNumbers = array(); 
        for($i = 1; $i <= 5; ){
            unset($num);
            $num = rand(0, 99);
            if(!in_array($num, $randNumbers)){
                $randNumbers[] = sprintf('%02d', $num);
                $i++;
            }
        }
        $ltLuckyDW->bulkSet(array(
            'lucky_date' => date('d-m-Y'),
            'lucky_number' => json_encode($randNumbers)
        ));
        if($ltLuckyDW->save()) {
            $result = $this->getById($ltLuckyDW->get('id'));
            $result['lucky_number'] = json_decode($result['lucky_number']);
            return $result;
        }
        return false;
    }

    /**
     * @return API_DataWriter_LotoLucky
     */
    protected function _getLotoLuckyDataWriter()
    {
        return Mava_DataWriter::create('API_DataWriter_LotoLucky');
    }
}