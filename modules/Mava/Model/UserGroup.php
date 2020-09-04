<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 10/21/15
 * @Time: 10:13 AM
 */
class Mava_Model_UserGroup extends Mava_Model {
    /**
     * @param $id
     * @return bool
     */
    public function hasMember($id){
        if($id > 0){
            $member = $this->_getDb()->fetchRow('SELECT count(*) as "total" FROM #__user WHERE `user_group_id`='. (int)$id);
            if($member['total'] > 0){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     * @param $id
     * @return array|bool
     */
    public function getUserGroupById($id){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__user_group WHERE `group_id`=". (int)$id);
        }else{
            return false;
        }
    }

    /**
     * @return array|bool
     */
    public function getAllUserGroup(){
        return $this->_getDb()->fetchAll("SELECT * FROM #__user_group ORDER BY `sort_order` ASC");
    }
}