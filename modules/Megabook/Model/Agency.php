<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/25/16
 * @Time: 1:02 AM
 */
class Megabook_Model_Agency extends Mava_Model {
    public function getById($id = 0){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__agency WHERE `id`='". (int)$id ."'");
        }else{
            return false;
        }
    }

    public function getByCode($code){
        if($code != ""){
            return $this->_getDb()->fetchRow("SELECT * FROM #__agency WHERE `agency_code`='". htmlspecialchars(strtolower($code)) ."'");
        }else{
            return false;
        }
    }

    public function hasCode($code, $excludeAgencyId = 0){
        $where = '';
        if($excludeAgencyId > 0){
            $where = " AND `id`<>'". $excludeAgencyId ."'";
        }
        $count = $this->_getDb()->fetchRow("SELECT COUNT(*) AS 'total' FROM #__agency WHERE `agency_code`='". htmlspecialchars($code) ."'". $where);
        if($count['total'] > 0){
            return true;
        }else{
            return false;
        }
    }

    public function getUserAgency($userId = 0){
        if($userId == 0){
            $userId = Mava_Visitor::getUserId();
        }
        $db = $this->_getDb();
        $where = 'WHERE a.`user_id`="'. $userId .'" AND a.`user_id`=u.`user_id` AND `status`<>"'. Megabook_DataWriter_Agency::STATUS_DELETED .'" ';
        return $db->fetchAll("SELECT a.*,u.`custom_title` AS 'created_by' FROM #__agency a, #__user u ". $where ." ORDER BY a.`created_date` DESC");
    }

    public function getList($skip = 0, $limit = 10, $status = '', $search_term = ''){
        $db = $this->_getDb();
        $where = 'WHERE a.`user_id`=u.`user_id` ';
        if($search_term != ""){
            $search_term = trim($search_term);
            $where .= 'AND
            (
            a.`id` LIKE "%'. $db->quoteLike($search_term) .'%"
            OR a.`user_id` LIKE "%'. $db->quoteLike($search_term) .'%"
            OR a.`title` LIKE "%'. $db->quoteLike($search_term) .'%"
            OR a.`email` LIKE "%'. $db->quoteLike($search_term) .'%"
            OR a.`phone` LIKE "%'. $db->quoteLike($search_term) .'%"
            OR a.`address` LIKE "%'. $db->quoteLike($search_term) .'%"
            OR a.`bank_fullname` LIKE "%'. $db->quoteLike($search_term) .'%"
            OR a.`bank_name` LIKE "%'. $db->quoteLike($search_term) .'%"
            OR a.`bank_id_string` LIKE "%'. $db->quoteLike($search_term) .'%"
            OR a.`created_ip` LIKE "%'. $db->quoteLike($search_term) .'%"
            )';
        }
        if(in_array($status, array('pending','activated','suspended','deleted'))){
            $where .= ' AND `status`="'. $status .'"';
        }else{
            $where .= '';
        }
        $agency = $db->fetchAll("SELECT a.*,u.`custom_title` AS 'created_by' FROM #__agency a, #__user u ". $where ." ORDER BY a.`created_date` DESC LIMIT ". $skip .','. $limit);
        $count = $db->fetchRow("SELECT COUNT(*) AS 'total' FROM #__agency a, #__user u ". $where);
        return array(
            'items' => $agency,
            'total' => $count['total']
        );
    }
}