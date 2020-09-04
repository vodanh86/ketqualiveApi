<?php

class Vietlott_Model_Mega extends Mava_Model {
    public function getAll(){
        return $this->_getDb()->fetchAll("SELECT * FROM #__vietlott_mega_result ORDER BY `id` ASC");
    }

    public function getMaxId() {
        $result = $this->_getDb()->fetchRow("SELECT MAX(id) as max_id FROM #__vietlott_mega_result");
        return (int)$result['max_id'];
    }
}