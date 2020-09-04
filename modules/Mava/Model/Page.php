<?php
class Mava_Model_Page extends Mava_Model {

    /**
     * @param $id
     * @return bool
     * @throws Mava_Exception
     */
    public function getPageById($id){
        $ckey = md5("Mava_Model_Page_getPageById_".$id);
        $ctime = 15*86400;
        $data = Mava_Application::getCache($ckey);
        if($data){
            return $data;
        }else{
            if($id > 0) {
                $db = $this->_getDb();
                $page = $db->query("SELECT * FROM #__pages WHERE `id`=". (int)$id);
                if($page->num_rows > 0) {
                    Mava_Application::setCache($ckey, $page->row, $ctime);
                    return $page->row;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }

    public function checkPageSlugExist($slug){
        if($slug!='') {
            $db = $this->_getDb();
            $slug = $db->query("SELECT COUNT(*) as 'total' FROM #__pages WHERE  `slug` = '".addslashes($slug)."' ");

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
     * @param array $data
     * @return bool|int
     */
    public function insert($data=array()){
        if(is_array($data) && count($data) > 0){
            $db = $this->_getDb();
            return $db->insert('#__pages', $data);
        }else{
            return false;
        }
    }

    public function update($data=array(), $cond=''){
        if(is_array($data) && count($data) > 0){
            $db = $this->_getDb();
            return $db->update('#__pages', $data, $cond);
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
    public function getListPage($skip, $limit, $searchTerm = ''){
        $db = $this->_getDb();
        $where = "WHERE 1=1 ";
        if($searchTerm != ""){
            $searchTerm = $db->quoteLike($searchTerm);
            $where .= " AND p.`slug` LIKE '%". $searchTerm ."%' ";
        }

        $sql = " SELECT p.*, pg.title as 'title' FROM #__pages p LEFT JOIN #__page_group pg ON p.`group_id` = pg.`id` ". $where ." ORDER BY p.`id` DESC LIMIT ". $skip .",". $limit ." ";
        $list_page = $db->query($sql);
        $count = $db->query("SELECT count(*) as 'total' FROM #__pages p LEFT JOIN #__page_group pg ON p.`group_id` = pg.`id` ". $where);
        return array(
            'rows' => $list_page->rows,
            'total' => $count->row['total']
        );
    }
}