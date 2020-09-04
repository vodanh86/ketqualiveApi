<?php
class Mava_Model_Currency extends Mava_Model {
    public function getAll(){
        $currency = $this->_getDb()->fetchAll("SELECT * FROM #__currency ORDER BY `sort_order` ASC");
        if($currency){
            return $currency;
        }else{
            return array();
        }
    }
}