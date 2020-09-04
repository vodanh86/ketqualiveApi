<?php
class Mava_Model_PageData extends Mava_Model {

    /**
     * @param $id
     * @return bool
     * @throws Mava_Exception
     */
    public function getPageDataById($id){
        if($id > 0) {
            $db = $this->_getDb();
            $rs = $db->query("SELECT * FROM #__page_data WHERE `id`=". (int)$id);
            if($rs->num_rows > 0) {
                return $rs->row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * @param $page_id
     * @param $language_code
     * @return bool
     * @throws Mava_Exception
     */
    public function checkPageDataExist($page_id, $language_code){
        if($page_id > 0 && $language_code!='') {
            $db = $this->_getDb();
            $rs = $db->query("SELECT COUNT(*) as 'total' FROM #__page_data WHERE  `page_id` = '".(int)$page_id."' AND `language_code` = '".addslashes($language_code)."' ");

            if($rs->row['total'] > 0) {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function getPageDataByLang($page_id, $language_code){
        $ckey = md5("Mava_Model_PageData_getPageDataByLang_".$page_id."_".$language_code);
        $ctime = 0;
        $data = Mava_Application::getCache($ckey);
        if($data){
            return $data;
        }else{
            if($page_id > 0 && $language_code!='') {
                $db = $this->_getDb();
                $rs = $db->query("SELECT * FROM #__page_data WHERE  `page_id` = '".(int)$page_id."' AND `language_code` = '".addslashes($language_code)."' ");

                if($rs->num_rows > 0) {
                    Mava_Application::setCache($ckey, $rs->row, $ctime);
                    return $rs->row;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }

    public function getSamePageGroup($language_code, $page_id, $group_id){
        $ckey = md5("Mava_Model_PageData_getSamePageGroup_".$language_code."_".$page_id."_".$group_id);
        $ctime =0;
        $data = Mava_Application::getCache($ckey);
        if($data){
            return $data;
        }else{
            if($page_id > 0 && $group_id > 0 && $language_code!='') {
                $db = $this->_getDb();
                $sql = "SELECT pd.*,p.`slug`,p.`show_title` FROM #__pages p, #__page_data pd WHERE p.`id`=pd.`page_id` AND pd.`language_code`='". $language_code ."' AND p.`group_id`='". (int)$group_id ."' ORDER BY p.`sort_order` ASC ";
                $rs = $db->query($sql);

                if($rs->num_rows > 0) {
                    Mava_Application::setCache($ckey, $rs->rows, $ctime);
                    return $rs->rows;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }

    public function insertData($data=array()){
        if(is_array($data) && count($data) > 0) {
            $db = $this->_getDb();
            $rs = $db->insert("#__page_data", $data);

            if($rs) {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function updateData($data=array(), $cond=''){
        if(is_array($data) && count($data) > 0) {
            $db = $this->_getDb();
            $rs = $db->update("#__page_data", $data, $cond);

            if($rs) {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}