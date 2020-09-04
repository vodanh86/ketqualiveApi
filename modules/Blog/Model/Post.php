<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 5/9/15
 * @Time: 10:55 AM
 */
class Blog_Model_Post extends Mava_Model {
    public function getPostById($postId){
        if($postId > 0){
            $post = $this->_getDb()->query("
                SELECT p.*,c.`title` as 'category_title' FROM #__blog_post p, #__blog_category c WHERE p.`category_id`=c.`category_id` AND p.`deleted`=0 AND p.`post_id`='". $postId ."'
            ");
            if($post->num_rows > 0){
                return $post->row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function getListPost($skip, $limit, $category_id = 0, $searchTerm = '', $exclude_ids = []){
        $where = '';
        if($searchTerm != ""){
            $searchTerm = $this->_getDb()->quoteLike($searchTerm);
            $where = " AND (p.`title` LIKE '%". $searchTerm ."%' OR p.`lead` LIKE '%". $searchTerm ."%' OR p.`content` LIKE '%". $searchTerm ."%' OR p.`post_id`='". (int)$searchTerm ."')";
        }

        if($category_id > 0){
            $where .= " AND p.`category_id`=". (int)$category_id;
        }

        if(count($exclude_ids) > 0){
            $where .= ' AND p.`category_id` NOT IN('. Mava_String::doImplode($exclude_ids) .')';
        }

        $post = $this->_getDb()->query("SELECT p.*,c.`title` as 'category_title',u.`custom_title` as 'created_name' FROM #__blog_post p, #__blog_category c, #__user u WHERE p.`category_id`=c.`category_id` AND p.`created_by`=u.`user_id` AND p.`deleted`=0". $where ." ORDER BY p.`post_id` DESC LIMIT ". $skip .",". $limit);
        $count = $this->_getDb()->query("SELECT count(*) as 'total' FROM #__blog_post p, #__blog_category c, #__user u WHERE p.`category_id`=c.`category_id` AND p.`created_by`=u.`user_id` AND p.`deleted`=0". $where);
        return array(
            'rows' => $post->rows,
            'total' => $count->row['total']
        );
    }

    public function countPostInCategory($categoryId){
        if($categoryId > 0){
            $count = $this->_getDb()->query("SELECT count(*) as 'total' FROM #__blog_post WHERE `category_id`=". (int)$categoryId);
            return $count->row['total'];
        }else{
            return 0;
        }
    }
}