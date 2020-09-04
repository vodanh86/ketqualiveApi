<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 10/29/15
 * @Time: 10:41 AM
 */
class News_Model_News extends Mava_Model {
    const TYPE_CATEGORY_DROPDOWN_PARENT = 1;
    const TYPE_CATEGORY_LIST = 2;
    /**
     * @param $id
     * @param bool $meta
     * @return array|bool
     */
    public function getNewsById($id, $meta = false){
        if($id > 0){
            $news = $this->_getDb()->fetchRow("SELECT * FROM #__news WHERE `news_id`=". (int)$id);
            if($meta === true){
                $newsData = $this->_getDb()->fetchAll("SELECT * FROM #__news_data WHERE `news_id`=". (int)$id);
                if(is_array($newsData) && count($newsData) > 0){
                    $data = array();
                    foreach($newsData as $item){
                        $data[$item['language_code']] = $item;
                    }
                    if(is_array($news)){
                        $news['_data'] = $data;
                    }
                }
            }
            return $news;
        }else{
            return false;
        }
    }

    public function getList($skip = 0, $limit = 10, $categoryId = 0, $searchTerm = ''){
        $languageCode = Mava_Visitor::getLanguageCode();
        $db = $this->_getDb();
        $where = "n.`category_id`=c.`category_id`
                AND n.`news_id`=nd.`news_id`
                AND c.`category_id`=cd.`category_id`
                AND nd.`language_code`='". $languageCode ."'
                AND cd.`language_code`='". $languageCode ."'";
        if($categoryId > 0){
            $where .= " AND c.`category_id`='". $categoryId ."'";
        }
        if($searchTerm != ""){
            $where .= " AND nd.`title` LIKE '%". $db->quoteLike($searchTerm) ."%'";
        }
        $post = $db->fetchAll("
            SELECT
                n.*,
                nd.`title`,
                nd.`sapo`,
                nd.`content`,
                cd.`title` AS 'category_title',
                cd.`descriptions` AS 'category_descriptions'
            FROM
                #__news n,
                #__news_data nd,
                #__news_category c,
                #__news_category_data cd
            WHERE
                ". $where ."
            ORDER BY
                n.`updated_date` DESC
            LIMIT ". $skip .",". $limit ."
        ");
        $count = $db->fetchRow("
            SELECT
                COUNT(*) AS 'total'
            FROM
                #__news n,
                #__news_data nd,
                #__news_category c,
                #__news_category_data cd
            WHERE
                ". $where ."
        ");
        return array(
            'rows' => $post,
            'total' => $count['total']
        );
    }

    /**
     * @param $id
     * @param bool $meta
     * @return array|bool
     */
    public function getNewsCategoryById($id, $meta = false){
        if($id > 0){
            $category = $this->_getDb()->fetchRow("SELECT * FROM #__news_category WHERE `category_id`=". (int)$id);
            if($meta === true){
                $categoryData = $this->_getDb()->fetchAll("SELECT * FROM #__news_category_data WHERE `category_id`=". (int)$id);
                if(is_array($categoryData) && count($categoryData) > 0){
                    $data = array();
                    foreach($categoryData as $item){
                        $data[$item['language_code']] = $item;
                    }
                    if(is_array($category)){
                        $category['_data'] = $data;
                    }
                }
            }
            return $category;
        }else{
            return false;
        }
    }

    public function newsCategoryHasChild($category_id){
        if($category_id > 0){
            $count = $this->_getDb()->fetchRow("SELECT count(*) as 'total' FROM #__news_category WHERE parent_id=". (int)$category_id);
            if($count['total'] > 0){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function hasNewsInCategory($category_id){
        if($category_id > 0){
            $count = $this->_getDb()->fetchRow("SELECT count(*) as 'total' FROM #__news WHERE category_id=". (int)$category_id);
            if($count['total'] > 0){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * @param $id
     * @return array|bool
     */
    public function getNewsDataById($id){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__news_data WHERE `data_id`=". (int)$id);
        }else{
            return false;
        }
    }

    /**
     * @param $id
     * @return array|bool
     */
    public function getNewsCategoryDataById($id){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__news_category_data WHERE `data_id`=". (int)$id);
        }else{
            return false;
        }
    }

    public function deleteNewsCategoryData($category_id){
        return $this->_getDb()->delete('#__news_category_data','category_id='. (int)$category_id);
    }

    public function getAllCategory($meta = false, $type = self::TYPE_CATEGORY_LIST, $reject_id = 0){
        $categories = $this->_getDb()->fetchAll("SELECT * FROM #__news_category ORDER BY `sort_order` ASC");
        if(is_array($categories) && count($categories) > 0){
            $allCategory = array();
            foreach($categories as $item){
                $allCategory[$item['category_id']] = $item;
            }
            if($meta === true){
                $categoriesData = $this->_getDb()->fetchAll("SELECT * FROM #__news_category_data");
                if(is_array($categoriesData) && count($categoriesData) > 0){
                    foreach($categoriesData as $item){
                        $allCategory[$item['category_id']]['_data'][$item['language_code']] = $item;
                    }
                }
            }
            if($type == self::TYPE_CATEGORY_LIST){
                $categories = get_list_category_sorted(0, 0, $allCategory);
            }else if($type == self::TYPE_CATEGORY_DROPDOWN_PARENT){
                $categories = get_list_category_sorted(0, 0, $allCategory, $reject_id);
            }
            return $categories;
        }else{
            return false;
        }
    }
}