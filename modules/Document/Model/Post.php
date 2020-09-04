<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 5/9/15
 * @Time: 10:55 AM
 */
class Document_Model_Post extends Mava_Model {
    public function getPostById($postId){
        if($postId > 0){
            return $this->_getDb()->fetchRow("SELECT p.*,c.`title` as 'category_title' FROM #__document_post p, #__document_category c WHERE p.`category_id`=c.`category_id` AND p.`deleted`=0 AND p.`post_id`='". $postId ."'");
        }else{
            return false;
        }
    }

    public function getListPost($skip, $limit, $category_id = 0, $searchTerm = ''){
        $where = '';
        if($searchTerm != ""){
            $searchTerm = $this->_getDb()->quoteLike($searchTerm);
            $where = " AND (p.`title` LIKE '%". $searchTerm ."%' OR p.`lead` LIKE '%". $searchTerm ."%' OR p.`content` LIKE '%". $searchTerm ."%' OR p.`post_id`='". (int)$searchTerm ."')";
        }

        if($category_id > 0){
            $where .= " AND p.`category_id`=". (int)$category_id;
        }

        $post = $this->_getDb()->query("SELECT p.*,c.`title` as 'category_title',u.`custom_title` as 'created_name' FROM #__document_post p, #__document_category c, #__user u WHERE p.`category_id`=c.`category_id` AND p.`created_by`=u.`user_id` AND p.`deleted`=0". $where ." ORDER BY p.`post_id` DESC LIMIT ". $skip .",". $limit);
        $count = $this->_getDb()->query("SELECT count(*) as 'total' FROM #__document_post p, #__document_category c, #__user u WHERE p.`category_id`=c.`category_id` AND p.`created_by`=u.`user_id` AND p.`deleted`=0". $where);
        return array(
            'rows' => $post->rows,
            'total' => $count->row['total']
        );
    }

    public function countPostInCategory($categoryId){
        if($categoryId > 0){
            $count = $this->_getDb()->query("SELECT count(*) as 'total' FROM #__document_post WHERE `category_id`=". (int)$categoryId);
            return $count->row['total'];
        }else{
            return 0;
        }
    }
}