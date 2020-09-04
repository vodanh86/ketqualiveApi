<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 1/17/17
 * @Time: 2:55 PM
 */
class Megabook_Model_AdsCampaignLinks extends Mava_Model {
    public function getById($id){
        if((int)$id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__ads_campaign_links WHERE `id`='".(int)$id ."'");
        }else{
            return false;
        }
    }

    public function getByUrl($url){
        $url_hash = md5(Mava_String::makeStringToLower($url));
        return $this->_getDb()->fetchRow("SELECT * FROM #__ads_campaign_links WHERE `url_hash`='". $url_hash ."'");
    }

    public function getList($campaign_id, $skip = 0, $limit = 10){
        $db = $this->_getDb();
        $campaigns = $db->fetchAll("SELECT * FROM #__ads_campaign_links WHERE `campaign_id`='". (int)$campaign_id ."' AND `deleted`='no' ORDER BY `id` ASC LIMIT ". $skip .','. $limit);
        $count = $db->fetchRow("SELECT COUNT(*) AS 'total' FROM #__ads_campaign_links WHERE `campaign_id`='". (int)$campaign_id ."' AND `deleted`='no'");
        return array(
            'rows' => $campaigns,
            'total' => $count['total']
        );
    }

    public function getAll($campaign_id){
        return $this->_getDb()->fetchAll("SELECT * FROM #__ads_campaign_links WHERE `campaign_id`='". (int)$campaign_id ."' AND `deleted`='no' ORDER BY `id` ASC");
    }
}