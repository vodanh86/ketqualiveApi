<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/25/16
 * @Time: 1:02 AM
 */
class Megabook_Model_Notification extends Mava_Model {
    public static function add($agencyId, $type, $text, $link){
        /* @var $notifyDW Megabook_DataWriter_Notification */
        $notifyDW = Mava_DataWriter::create('Megabook_DataWriter_Notification');
        $notifyDW->bulkSet(array(
                'agency_id' => $agencyId,
                'type' => $type,
                'text' => $text,
                'link' => $link,
                'created_date' => time(),
                'has_read' => 0,
                'has_seen' => 0
            ));
        $notifyDW->save();
    }

    public function getById($id = 0){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__agency_notifications WHERE `id`='". (int)$id ."'");
        }else{
            return false;
        }
    }

    public function markAllAsSeen($agencyId){
        // TODO tạm thời để đã xem và đã đọc
        $this->_getDb()->update('#__agency_notifications', array('has_seen' => 1, 'has_read' => 1), '`agency_id`="'. (int)$agencyId .'"');
    }

    public function markAllAsRead($agencyId){
        $this->_getDb()->update('#__agency_notifications', array('has_seen' => 1, 'has_read' => 1), '`agency_id`="'. (int)$agencyId .'"');
    }

    public function countNotSeen($agencyId){
        $count = $this->_getDb()->fetchRow("SELECT COUNT(*) AS 'total' FROM #__agency_notifications WHERE `agency_id`='". $agencyId ."' AND `has_seen`=0");
        return $count['total'];
    }

    public function getNotifyPreview($agencyId, $limit = 10){
        $notify = $this->_getDb()->fetchAll("SELECT * FROM #__agency_notifications WHERE `agency_id`='". $agencyId ."' ORDER BY `has_read` ASC,`has_seen` ASC,`created_date` DESC LIMIT 0,". $limit);
        if($notify){
            $result = '';
            foreach($notify as $item){
                $result .= '<li class="'. ($item['has_read']==1?'mbd-read':'mbd-unread') .'"><a href="'. $item['link'] .'">'. $item['text'] .'<span class="mbd-time">'. date('d/m/Y H:i:s', $item['created_date']) .'</span></a></li>';
            }
            return $result;
        }else{
            return '<li class="mbd-no-notify"><p>'. __('no_notify_found') .'</li>';
        }
    }


    public function getList($skip = 0, $limit = 10, $agencyId = 0){
        $db = $this->_getDb();
        $where = '';
        if($agencyId > 0){
            $where .= 'WHERE `agency_id`="'. (int)$agencyId .'"';
        }

        $items = $db->fetchAll("SELECT * FROM #__agency_notifications ". $where ." ORDER BY `created_date` DESC LIMIT ". $skip .",". $limit);
        $count = $db->fetchRow("SELECT COUNT(*) AS 'total' FROM #__agency_notifications ". $where);
        return array(
            'items' => $items,
            'total' => $count['total']
        );
    }
}