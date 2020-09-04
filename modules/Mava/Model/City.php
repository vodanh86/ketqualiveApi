<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 5/1/15
 * @Time: 11:11 AM
 */
class Mava_Model_City extends Mava_Model {
    public function getCityById($city_id){
        if($city_id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__city WHERE `city_id`=". (int)$city_id);
        }else{
            return false;
        }
    }

    public function getAllCity(){
        return $this->_getDb()->fetchAll("SELECT * FROM #__city ORDER BY `alias` ASC");
    }
}