<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 6/29/16
 * @Time: 11:30 AM
 */
class Index_Model_Banner extends Mava_Model {
    public function getById($id, $meta = false){
        if($id > 0){
            $item = $this->_getDb()->fetchRow("SELECT * FROM #__banner WHERE `id`=". (int)$id);
            if($meta === true){
                $itemData = $this->_getDb()->fetchAll("SELECT * FROM #__banner_data WHERE `banner_id`=". (int)$id);
                if(is_array($itemData) && count($itemData) > 0){
                    $data = array();
                    foreach($itemData as $item_data){
                        $data[$item_data['language_code']] = $item_data;
                    }
                    if(is_array($item)){
                        $item['_data'] = $data;
                    }
                }
            }
            return $item;
        }else{
            return false;
        }
    }

    public function getBanners($skip = 0, $limit = 50, $position = null, $meta = false){
        $where = "b.`position_id`=p.`id`";
        if(is_array($position)){
            $where .= " AND b.`position_id` IN(". Mava_String::doImplode($position) .")";
        }else if(is_numeric($position) && $position > 0){
            $where .= " AND b.`position_id`='". (int)$position ."'";
        }else if(is_string($position) && $position != ""){
            $where .= " AND p.`position`='". $position ."'";
        }
        $banners = $this->_getDb()->fetchAll("SELECT b.*,p.`title` as 'position_title',p.`position` FROM #__banner b, #__banner_position p WHERE ". $where ." ORDER BY b.`position_id` ASC, b.`sort_order` ASC LIMIT ". $skip .",". $limit);
        $count = $this->_getDb()->fetchRow("SELECT COUNT(*) AS 'total' FROM #__banner b, #__banner_position p WHERE ". $where);
        if($meta === true && is_array($banners) && count($banners) > 0){
            $rows = array();
            foreach($banners as $banner){
                $bannerData = $this->_getDb()->fetchAll("SELECT * FROM #__banner_data WHERE `banner_id`=". (int)$banner['id']);
                if(is_array($bannerData) && count($bannerData) > 0){
                    $data = array();
                    foreach($bannerData as $item){
                        $data[$item['language_code']] = $item;
                    }
                    $banner['_data'] = $data;
                }else{
                    $banner['_data'] = array();
                }
                $rows[] = $banner;
            }
        }else{
            $rows = $banners;
        }
        return array(
            'rows' => $rows,
            'total' => $count['total']
        );
    }

    public function deleteData($banner_id){
        return $this->_getDb()->delete('#__banner_data','banner_id='. (int)$banner_id);
    }

    public function getDataById($id){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__banner_data WHERE `id`='". $id ."'");
        }else{
            return false;
        }
    }

    public function getPositionById($id){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__banner_position WHERE `id`='". $id ."'");
        }else{
            return false;
        }
    }

    public function getAllPosition(){
        return $this->_getDb()->fetchAll("SELECT * FROM #__banner_position");
    }

    public function getPositionByKey($key){
        if($key != ""){
            return $this->_getDb()->fetchRow("SELECT * FROM #__banner_position WHERE `position`='". $key ."'");
        }else{
            return false;
        }
    }
}