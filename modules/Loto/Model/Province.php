<?php

class Loto_Model_Province extends Mava_Model {
    public function getById($id){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__loto_province WHERE `id`='". $id ."'");
        }else{
            return false;
        }
    }
    
    public function getByCode($code){
        if($code != ""){
            return $this->_getDb()->fetchRow("SELECT * FROM #__loto_province WHERE `code`='". $code ."'");
        }else{
            return false;
        }
    }
    
    public function getBySlug($slug){
        if($slug != ""){
            return $this->_getDb()->fetchRow("SELECT * FROM #__loto_province WHERE `slug`='". $slug ."'");
        }else{
            return false;
        }
    }

    public function getSimpleList(){
        $result = $this->_getDb()->fetchAll("SELECT code,title FROM #__loto_province ORDER BY `sort_order` ASC");
        if($result){
            return $result;
        }else{
            return [];
        }
    }
    public function getSimpleListInRegion($region){
        $result = $this->_getDb()->fetchAll("SELECT code,title FROM #__loto_province WHERE `region`='". $region ."' ORDER BY `sort_order` ASC");
        if($result){
            return $result;
        }else{
            return [];
        }
    }
    
    public function getAll(){
        $result = array(
            'bac' => array(),
            'trung' => array(),
            'name' => array()
        );
        $provinces = $this->_getDb()->fetchAll("SELECT * FROM #__loto_province ORDER BY `sort_order` ASC");
        if(is_array($provinces) && count($provinces) > 0){
            foreach($provinces as $item){
                $result[$item['region']][] = $item;
            }    
        }
        return $result;
    }
}