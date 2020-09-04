<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 5/5/15
 * @Time: 2:26 PM
 */
class Document_Controller_Index extends Mava_Controller {
    public function __construct(){
        Mava_Application::set('body_id','document_page');
        Mava_Application::set('menu_selected', Mava_Url::getPageLink('tai-lieu'));
    }

    public function detailAction(){
        Mava_Application::set('body_id','no_fixed_menu');
        $postId = (int)Mava_Url::getParam('doc_id');
        if($postId > 0){
            $postModel = $this->_getPostModel();
            $post = $postModel->getPostById($postId);
            if($post){
                $categoryModel = $this->_getCategoryModel();
                $category = $categoryModel->getCategoryById($post['category_id']);
                if($post['category_id'] > 0){
                    $detail_url = Mava_Url::getPageLink('tai-lieu/'. Mava_String::unsignString($post['category_title'],'-') .'/'. Mava_String::unsignString($post['title'],'-') .'-p'. $post['post_id']);
                }else{
                    $detail_url = Mava_Url::getPageLink('tai-lieu/view/'. Mava_String::unsignString($post['title'],'-') .'-p'. $post['post_id']);
                }
                Mava_Application::set('seo', array(
                    'title' => $post['title'] .' - '. $post['category_title'],
                    'description' => $post['lead'],
                    'keywords' => __('document_category_keywords_tag', $category),
                    'canonical' => $detail_url
                ));
                $categories = $categoryModel->getAllCategory();
                $postDW = $this->_getPostDataWriter();
                $postDW->setExistingData($post['post_id']);
                $postDW->set('view_count', (int)$post['view_count']+1);
                $postDW->save();
                return $this->responseView('Document_View_Detail', array(
                    'post' => $post,
                    'categories' => $categories
                ));
            }else{
                return $this->responseError(__('document_not_found'), Mava_Error::NOT_FOUND);
            }
        }else{
            return $this->responseRedirect(Mava_Url::getPageLink('tai-lieu'));
        }
    }

    public function categoryAction(){
        $category_slug = Mava_Url::getParam('slug');
        $categoryModel = $this->_getCategoryModel();
        $postModel = $this->_getPostModel();
        $categories = $categoryModel->getAllCategory();
        $category = false;
        if($category_slug != "" && is_array($categories) && count($categories) > 0){
            foreach($categories as $item){
                if(Mava_String::unsignString($item['title'], '-') == $category_slug){
                    $category = $item;
                }
            }
        }
        $page = max((int)Mava_Url::getParam('page'), 1);
        $limit = 20;
        $offset = 3;
        $skip = ($page-1)*$limit;

        if($category){
            Mava_Application::set('seo/title', $category['title'] .' - '. __('news'));
            Mava_Application::set('seo/description', __('document_category_description_tag', $category));
            Mava_Application::set('seo/keywords', __('document_category_keywords_tag', $category));
            Mava_Application::set('seo/canonical', Mava_Url::getPageLink('tai-lieu/'. Mava_String::unsignString($category['title'], '-')));
            $posts = $postModel->getListPost($skip, $limit, $category['category_id']);
            return $this->responseView('Document_View_Category', array(
                'category' => $category,
                'posts' => $posts['rows'],
                'total' => $posts['total'],
                'page' => $page,
                'skip' => $skip,
                'limit' => $limit,
                'offset' => $offset
            ));
        }else{
            return $this->responseRedirect(Mava_Url::getPageLink('tai-lieu'));
        }
    }

    public function indexAction(){
        $postModel = $this->_getPostModel();
        $cmd = Mava_Url::getParam('cmd');
        if($cmd == 'download'){
            $doc_id = Mava_Url::getParam('doc_id');
            if($doc_id > 0 && $post = $postModel->getPostById($doc_id)){
                preg_match('%https://drive\.google\.com/file/d/([^\/]+)/(.*?)%', $post['content'], $driver_id);
                if(isset($driver_id[1]) && strlen($driver_id[1]) > 20){
                    $postDW = $this->_getPostDataWriter();
                    $postDW->setExistingData($post['post_id']);
                    $postDW->set('download_count', (int)$post['download_count']+1);
                    $postDW->save();
                    return $this->responseRedirect('https://docs.google.com/uc?id='. $driver_id[1] .'&export=download');
                }
            }
        }

        $page = max((int)Mava_Url::getParam('page'), 1);
        $limit = 4;
        $offset = 3;
        $skip = ($page-1)*$limit;
        $categoryModel = $this->_getCategoryModel();
        $categories = $categoryModel->getAllCategory();

        Mava_Application::set('seo', array(
            'title' => __('document_title_tag'),
            'description' => __('document_description_tag'),
            'keywords' => __('document_keywords_tag'),
            'canonical' => Mava_Url::getPageLink('tai-lieu')
        ));
        $cats = array();
        if(is_array($categories) && count($categories) > 0){
            foreach($categories as $item){
                $item['docs'] = $postModel->getListPost($skip, $limit, $item['category_id']);
                $cats[] = $item;
            }
        }
        return $this->responseView('Document_View_Index', array(
            'categories' => $cats,
            'page' => $page,
            'skip' => $skip,
            'limit' => $limit,
            'offset' => $offset
        ));
    }

    /**
     * @return Document_Model_Post
     */
    protected function _getPostModel(){
        return $this->getModelFromCache('Document_Model_Post');
    }

    /**
     * @return Document_Model_Category
     */
    protected function _getCategoryModel(){
        return $this->getModelFromCache('Document_Model_Category');
    }

    /**
     * @return Document_DataWriter_Post
     * @throws Mava_Exception
     */
    protected function _getPostDataWriter()
    {
        return Mava_DataWriter::create('Document_DataWriter_Post');
    }
}