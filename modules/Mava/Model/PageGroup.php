<?php

class Mava_Model_PageGroup extends Mava_Model {

    const TYPE_ONE_COL = 'one_col'; //one colunm
    const TYPE_TWO_COL = 'two_col'; //two colunm
    const TYPE_FLUID = 'fluid'; //fluid
    /**
     * @return array|bool
     */
    public function getAllPageGroup(){
        return $this->_getDb()->fetchAll("SELECT * FROM #__page_group ORDER BY `id` DESC ");
    }

    public function getPageGroupById($groupId){
        if($groupId > 0) {
            $db = $this->_getDb();
            $pageGroup = $db->query("SELECT * FROM #__page_group WHERE `id`=". (int)$groupId);
            if($pageGroup->num_rows > 0) {
                return $pageGroup->row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}