<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 1/17/17
 * @Time: 2:55 PM
 */
class Megabook_Model_AdsCampaignGroup extends Mava_Model {
    public function getById($id){
        if((int)$id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__ads_campaign_group WHERE `id`='".(int)$id ."'");
        }else{
            return false;
        }
    }

    public function getList($skip = 0, $limit = 10){
        $db = $this->_getDb();
        $campaigns = $db->fetchAll('SELECT * FROM #__ads_campaign_group ORDER BY `sort_order` ASC LIMIT '. $skip .','. $limit);
        $count = $db->fetchRow("SELECT COUNT(*) AS 'total' FROM #__ads_campaign_group");
        return array(
            'rows' => $campaigns,
            'total' => $count['total']
        );
    }

    public function getAll(){
        return $this->_getDb()->fetchAll("SELECT * FROM #__ads_campaign_group ORDER BY `sort_order` ASC");
    }
}