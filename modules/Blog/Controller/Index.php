<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 5/5/15
 * @Time: 2:26 PM
 */
class Blog_Controller_Index extends Mava_Controller {
    public function __construct(){
        Mava_Application::set('body_id','blog_page');
        Mava_Application::set('menu_selected', Mava_Url::getPageLink('documents'));
    }

    public function detailAction(){
        $postId = (int)Mava_Url::getParam('post_id');
        if($postId > 0){
            $postModel = $this->_getPostModel();
            $post = $postModel->getPostById($postId);
            if($post){
                $categoryModel = $this->_getCategoryModel();
                $category = $categoryModel->getCategoryById($post['category_id']);
                if($post['category_id'] > 0){
                    $detail_url = Mava_Url::getPageLink('documents/'. Mava_String::unsignString($post['category_title'],'-') .'/'. Mava_String::unsignString($post['title'],'-') .'-p'. $post['post_id']);
                }else{
                    $detail_url = Mava_Url::getPageLink('documents/view/'. Mava_String::unsignString($post['title'],'-') .'-p'. $post['post_id']);
                }
                Mava_Application::set('seo', array(
                    'title' => $post['title'] .' - '. $post['category_title'],
                    'description' => $post['lead'],
                    'keywords' => __('blog_category_keywords_tag', $category),
                    'canonical' => $detail_url
                ));
                $categories = $categoryModel->getAllCategory();
                return $this->responseView('Blog_View_Detail', array(
                    'post' => $post,
                    'categories' => $categories
                ));
            }else{
                return $this->responseError(__('post_not_found'), Mava_Error::NOT_FOUND);
            }
        }else{
            return $this->responseRedirect(Mava_Url::getPageLink('documents'));
        }
    }

    public function indexAction(){
        $category_slug = Mava_Url::getParam('slug');
        $page = max((int)Mava_Url::getParam('page'), 1);
        $limit = 10;
        $offset = 3;
        $skip = ($page-1)*$limit;
        $categoryModel = $this->_getCategoryModel();
        $categories = $categoryModel->getAllCategory();

        $category = false;
        if($category_slug != "" && is_array($categories) && count($categories) > 0){
            foreach($categories as $item){
                if(Mava_String::unsignString($item['title'], '-') == $category_slug){
                    $category = $item;
                }
            }
        }


        Mava_Application::set('seo', array(
            'title' => __('blog_title_tag'),
            'description' => __('blog_description_tag'),
            'keywords' => __('blog_keywords_tag'),
            'canonical' => Mava_Url::getPageLink('blog')
        ));
        $excludes = [5];
        if($category_slug != "" && $category != false){
            Mava_Application::set('seo/title', $category['title'] .' - '. __('news'));
            Mava_Application::set('seo/description', __('blog_category_description_tag', $category));
            Mava_Application::set('seo/keywords', __('blog_category_keywords_tag', $category));
            Mava_Application::set('seo/canonical', Mava_Url::getPageLink('documents/'. Mava_String::unsignString($category['title'], '-')));
            $categoryId = $category['category_id'];
            $excludes = [];
        }else{
            $categoryId = 0;
        }
        $postModel = $this->_getPostModel();
        $posts = $postModel->getListPost($skip, $limit, $categoryId, '', $excludes);
        return $this->responseView('Blog_View_Index', array(
            'categories' => $categories,
            'posts' => $posts['rows'],
            'total' => $posts['total'],
            'page' => $page,
            'skip' => $skip,
            'limit' => $limit,
            'offset' => $offset,
            'categoryId' => $categoryId,
            'category' => $category
        ));
    }

    /**
     * @return Blog_Model_Post
     */
    protected function _getPostModel(){
        return $this->getModelFromCache('Blog_Model_Post');
    }

    /**
     * @return Blog_Model_Category
     */
    protected function _getCategoryModel(){
        return $this->getModelFromCache('Blog_Model_Category');
    }
}