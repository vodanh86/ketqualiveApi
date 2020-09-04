<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 1/17/17
 * @Time: 2:55 PM
 */
class Megabook_Model_AdsCampaign extends Mava_Model {
    public function getById($id){
        if((int)$id > 0){
            return $this->_getDb()->fetchRow("SELECT c.*,g.title AS 'group_title',g.`color` AS 'group_color' FROM #__ads_campaign c LEFT JOIN #__ads_campaign_group g ON c.group_id=g.id WHERE c.`id`='".(int)$id ."'");
        }else{
            return false;
        }
    }

    public function unGroupCampaign($group_id){
        return $this->_getDb()->query("UPDATE #__ads_campaign SET `group_id`='0' WHERE `group_id`='". (int)$group_id ."'");
    }

    public function getList($skip = 0, $limit = 10, $sort_by = 'id', $sort_dir = 'desc', $search_term = '', $group_id = 0){
        if(!in_array($sort_by, array('id','click_count','order_count','total_revenue'))){
            $sort_by = 'c.`id`';
        }

        if(strtolower($sort_dir) != 'asc'){
            $sort_dir = 'desc';
        }
        $db = $this->_getDb();

        $where = '';
        if($search_term != ''){
            $where .= " AND (c.`id`='". (int)$search_term ."' OR c.`title` LIKE '%". $db->quoteLike($search_term) ."%' OR c.`note` LIKE '%". $db->quoteLike($search_term) ."%')";
        }

        if($group_id > 0){
            $where .= " AND c.`group_id`='". (int)$group_id ."'";
        }

        $campaigns = $db->fetchAll("SELECT c.*,g.title AS 'group_title',g.`color` AS 'group_color' FROM #__ads_campaign c LEFT JOIN #__ads_campaign_group g ON c.group_id=g.id WHERE c.`deleted`='no' ". $where ." ORDER BY ". $sort_by ." ". $sort_dir .' LIMIT '. $skip .','. $limit);
        $count = $db->fetchRow("SELECT COUNT(*) AS 'total' FROM #__ads_campaign c LEFT JOIN #__ads_campaign_group g ON c.group_id=g.id WHERE c.`deleted`='no'". $where);
        return array(
            'rows' => $campaigns,
            'total' => $count['total']
        );
    }
}