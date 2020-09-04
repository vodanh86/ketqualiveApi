<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 4/19/17
 * @Time: 11:18 AM
 */
class Megabook_Model_BookCodeSet extends Mava_Model {
    public function useCode($code_id){
        $db = $this->_getDb();
        $db->update('#__book_code', array(
            'used_by' => Mava_Visitor::getUserId(),
            'used_email' => Mava_Visitor::getInstance()->get('email'),
            'used_time' => time(),
            'status' => 'used'
        ),"`id`='". $code_id ."'");
    }

    public function getCodeByCode($code){
        return $this->_getDb()->fetchRow("SELECT * FROM #__book_code WHERE `code`='". $code ."'");
    }

    public function generalCodeList($chars = 0, $size = 12, $count = 1000, $prefix = ''){
        $results = array();
        for($i=0;$i<$count;$i++){
            $results[] = Mava_String::generalHash($chars, $size, $prefix);
        }
        return $results;
    }

    public function getCodeList($set_id, $skip = 0, $limit = 100){
        return $this->_getDb()->fetchAll("SELECT * FROM #__book_code WHERE `set_id`='". $set_id ."' ORDER BY `id` DESC LIMIT ". $skip .",". $limit);
    }

    public function getBookActiveCodeList($user_id){
        $codes = $this->_getDb()->fetchAll("SELECT * FROM #__book_code WHERE `used_by`='". $user_id ."' ORDER BY `used_time` DESC");
        if($codes && count($codes) > 0){
            $product_ids = array();
            foreach($codes as $item){
                $product_ids[] = $item['product_id'];
            }

            $productModel = $this->_getProductModel();
            $products = $productModel->getProductByIds($product_ids);
            return $products['rows'];
        }else{
            return array();
        }
    }

    /**
     * @return Product_Model_Product
     */
    protected function _getProductModel(){
        return $this->getModelFromCache('Product_Model_Product');
    }

    public function getAllCodeList($set_id){
        return $this->_getDb()->fetchAll("SELECT * FROM #__book_code WHERE `set_id`='". $set_id ."' ORDER BY `id` DESC");
    }

    public function deleteCodeInSet($set_id){
        $this->_getDb()->query("UPDATE #__book_code SET `status`='deleted' WHERE `set_id`='". (int)$set_id ."'");
    }

    public function addExistedList($codes, $set_id, $product_id){
        if(is_array($codes) && $set_id > 0 && $product_id > 0){
            $values = array();
            foreach($codes as $item){
                $values[] = "('". addslashes($item) ."','". (int)$set_id ."','". (int)$product_id ."','0','0','new')";
            }

            $this->_getDb()->query("INSERT INTO #__book_code(`code`,`set_id`,`product_id`,`used_by`,`used_time`,`status`) VALUES". implode(',', $values));
        }
    }

    public function getById($id){
        if((int)$id > 0){
            return $this->_getDb()->fetchRow("SElECT * FROM #__book_code_set WHERE `id`='". (int)$id ."'");
        }else{
            return false;
        }
    }

    public function getAll(){
        return $this->_getDb()->fetchAll("SELECT * FROM #__book_code_set WHERE `deleted`='no' ORDER BY `id` DESC");
    }

    public function getList($skip = 0, $limit = 10, $search_term = ''){
        $db = $this->_getDb();
        $where = " WHERE `deleted`='no'";
        if($search_term != ""){
            $where .= " AND (`id`='". (int)$search_term ."' OR `title` LIKE '%". $db->quoteLike($search_term) ."%')";
        }

        $items = $db->fetchAll("SELECT * FROM #__book_code_set ". $where ." LIMIT ". $skip .",". $limit);
        $count = $db->fetchRow("SELECT COUNT(*) AS 'total' FROM #__book_code_set ". $where);
        return array(
            'items' => $items,
            'total' => $count['total']
        );
    }
}