<?php
class Mava_Model_Slug extends Mava_Model {

    /**
     * @return array|bool
     */
    public function getAllSlug(){
        return $this->_getDb()->fetchAll("SELECT * FROM #__slug ORDER BY `id` DESC ");
    }

    /**
     * @param $id
     * @return bool
     * @throws Mava_Exception
     */
    public function getSlugById($id){
        if($id > 0) {
            $db = $this->_getDb();
            $slug = $db->query("SELECT * FROM #__slug WHERE `id`=". (int)$id);
            if($slug->num_rows > 0) {
                return $slug->row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function getSlugByKey($key){
        if($key!='') {
            $db = $this->_getDb();
            $slug = $db->query("SELECT * FROM #__slug WHERE `key`= '".addslashes($key)."' ");
            if($slug->num_rows > 0) {
                return $slug->row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function checkSlugExist($slug){
        if($slug!='') {
            $db = $this->_getDb();
            $slug = $db->query("SELECT COUNT(*) as 'total' FROM #__slug WHERE  `slug` = '".addslashes($slug)."' ");

            if($slug->row['total'] > 0) {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * @param $skip
     * @param $limit
     * @param string $searchTerm
     * @return array
     * @throws Mava_Exception
     */
    public function getListSlug($skip, $limit, $searchTerm = ''){
        $db = $this->_getDb();
        $where = "WHERE 1=1 ";
        if($searchTerm != ""){
            $searchTerm = $db->quoteLike($searchTerm);
            $where .= " AND `slug` LIKE '%". $searchTerm ."%'
            OR `app` LIKE '%". $searchTerm ."%'
            OR `controller` LIKE '%". $searchTerm ."%' ";
        }

        $sql = " SELECT * FROM #__slug ". $where ." ORDER BY `id` DESC LIMIT ". $skip .",". $limit ." ";
        $list_slug = $db->query($sql);
        $count = $db->query("SELECT count(*) as 'total' FROM #__slug ". $where);
        return array(
            'rows' => $list_slug->rows,
            'total' => $count->row['total']
        );
    }

    public function insert($data=array()){
        if(is_array($data) && count($data)){
            $db = $this->_getDb();
            return $db->insert('#__slug', $data);
        }else{
            return false;
        }
    }
}