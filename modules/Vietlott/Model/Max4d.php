<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class Vietlott_Model_Max4d extends Mava_Model
{
    public function getMaxId() {
        $result = $this->_getDb()->fetchRow("SELECT MAX(id) as max_id FROM #__vietlott_max4d_result");
        return (int)$result['max_id'];
    }

    public function getMinId() {
        $result = $this->_getDb()->fetchRow("SELECT MIN(id) as min_id FROM #__vietlott_max4d_result");
        return (int)$result['min_id'];
    }
}