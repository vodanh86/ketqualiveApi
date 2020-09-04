<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/25/16
 * @Time: 1:02 AM
 */
class Megabook_Model_WithdrawRequest extends Mava_Model {
    public function getById($id = 0){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__agency_withdraw_request WHERE `id`='". (int)$id ."'");
        }else{
            return false;
        }
    }

    public function getList($skip = 0, $limit = 10, $status = '', $agencyId = 0){
        $db = $this->_getDb();
        $where = 'WHERE a.`id`=r.`agency_id` AND u.`user_id`=r.`user_id` ';
        if(in_array($status, array('pending','reviewed','paid','reject'))){
            $where .= ' AND r.`status`="'. $status .'"';
        }else{
            $where .= '';
        }
        if($agencyId > 0){
            $where .= ' AND r.`agency_id`="'. (int)$agencyId .'"';
        }
        $agency = $db->fetchAll("SELECT r.*,u.`custom_title` AS 'created_by',a.`title` AS 'agency_title' FROM #__agency a, #__user u, #__agency_withdraw_request r ". $where ." ORDER BY r.`created_date` DESC LIMIT ". $skip .','. $limit);
        $count = $db->fetchRow("SELECT COUNT(*) AS 'total' FROM #__agency a, #__user u, #__agency_withdraw_request r ". $where);
        return array(
            'items' => $agency,
            'total' => $count['total']
        );
    }

    public function countByAgency($agencyId){
        $count = $this->_getDb()->fetchRow("SELECT SUM(`amount`) AS 'total' FROM #__agency_withdraw_request WHERE `agency_id`='". (int)$agencyId ."' AND `status`<>'". Megabook_DataWriter_WithdrawRequest::STATUS_REJECT ."'");
        return $count['total'];
    }
}