<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class Vietlott_Model_Power extends Mava_Model
{
    public function getMaxId() {
        $result = $this->_getDb()->fetchRow("SELECT MAX(id) as max_id FROM #__vietlott_power_result");
        return (int)$result['max_id'];
    }
}