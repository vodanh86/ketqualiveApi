<?php
class Megabook_Model_LinkStats extends Mava_Model {
    public function getById($id = 0){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__agency_link_stats WHERE `id`='". (int)$id ."'");
        }else{
            return false;
        }
    }

    /**
     * @return Megabook_DataWriter_LinkStats
     */
    protected function _getLinkStatsDataWriter(){
        return Mava_DataWriter::create('Megabook_DataWriter_LinkStats');
    }

    public function increOrder($amount, $linkHash, $agencyId){
        $db = $this->_getDb();
        $checkLinkExist = $db->fetchRow("SELECT * FROM #__agency_link_stats WHERE `agency_id`='". (int)$agencyId ."' AND `link_hash`='". htmlspecialchars($linkHash) ."'");
        if($checkLinkExist){
            $linkDW = $this->_getLinkStatsDataWriter();
            $linkDW->setExistingData($checkLinkExist['id']);
            $linkDW->bulkSet(array(
                    'order_count' => $checkLinkExist['order_count']+1,
                    'total_revenue' => $checkLinkExist['total_revenue']+$amount
                ));
            $linkDW->save();
        }
    }

    public function increLinkVisitor($link, $linkHash, $agencyId){
        $db = $this->_getDb();
        $checkLinkExist = $db->fetchRow("SELECT * FROM #__agency_link_stats WHERE `agency_id`='". (int)$agencyId ."' AND `link_hash`='". htmlspecialchars($linkHash) ."'");
        if($checkLinkExist){
            $linkDW = $this->_getLinkStatsDataWriter();
            $linkDW->setExistingData($checkLinkExist['id']);
            $linkDW->bulkSet(array(
                    'visitor' => $checkLinkExist['visitor']+1
                ));
            $linkDW->save();
        }else{
            $linkDW = $this->_getLinkStatsDataWriter();
            $linkDW->bulkSet(array(
                    'agency_id' => $agencyId,
                    'link' => $link,
                    'link_hash' => $linkHash,
                    'visitor' => 1,
                    'pageview' => 0,
                    'order_count' => 0,
                    'total_revenue' => 0
                ));
            $linkDW->save();
        }
    }

    public function increLinkPageview($link, $linkHash, $agencyId){
        $db = $this->_getDb();
        $checkLinkExist = $db->fetchRow("SELECT * FROM #__agency_link_stats WHERE `agency_id`='". (int)$agencyId ."' AND `link_hash`='". htmlspecialchars($linkHash) ."'");
        if($checkLinkExist){
            $linkDW = $this->_getLinkStatsDataWriter();
            $linkDW->setExistingData($checkLinkExist['id']);
            $linkDW->bulkSet(array(
                    'pageview' => $checkLinkExist['pageview']+1
                ));
            $linkDW->save();
        }else{
            $linkDW = $this->_getLinkStatsDataWriter();
            $linkDW->bulkSet(array(
                    'agency_id' => $agencyId,
                    'link' => $link,
                    'link_hash' => $linkHash,
                    'visitor' => 1,
                    'pageview' => 1,
                    'order_count' => 0,
                    'total_revenue' => 0
                ));
            $linkDW->save();
        }
    }


    public function getList($skip = 0, $limit = 10, $agencyId = 0){
        $db = $this->_getDb();
        $where = '';
        if($agencyId > 0){
            $where .= 'WHERE `agency_id`="'. (int)$agencyId .'"';
        }

        $items = $db->fetchAll("SELECT * FROM #__agency_link_stats ". $where ." ORDER BY `visitor` DESC LIMIT ". $skip .",". $limit);
        $count = $db->fetchRow("SELECT COUNT(*) AS 'total' FROM #__agency_link_stats ". $where);
        return array(
            'items' => $items,
            'total' => $count['total']
        );
    }

    public function countVisitorByAgency($agencyId){
        $count = $this->_getDb()->fetchRow("SELECT SUM(`visitor`) AS 'total' FROM #__agency_link_stats WHERE `agency_id`='". (int)$agencyId ."'");
        return $count['total'];
    }

    public function countPageViewByAgency($agencyId){
        $count = $this->_getDb()->fetchRow("SELECT SUM(`pageview`) AS 'total' FROM #__agency_link_stats WHERE `agency_id`='". (int)$agencyId ."'");
        return $count['total'];
    }
}