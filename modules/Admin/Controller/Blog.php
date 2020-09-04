<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 5/9/15
 * @Time: 10:16 AM
 */
class Admin_Controller_Blog extends Mava_AdminController {
    public function deleteAction(){
        $postId = (int)Mava_Url::getParam('postId');
        if($postId > 0){
            $postModel = $this->_getBlogPostModel();
            $post = $postModel->getPostById($postId);
            if($post){
                $postDW = $this->_getBlogPostDataWriter();
                $postDW->setExistingData($postId);
                if($postDW->delete()){
                    return $this->responseJson(array(
                        'status' => 1,
                        'message' => __('post_deleted')
                    ));
                }else{
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('can_not_delete_post')
                    ));
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('post_not_found')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('post_not_found')
            ));
        }
    }
    public function editAction(){
        Mava_Application::set('seo/title',__('edit_post'));
        $postId = (int)Mava_Url::getParam('postId');
        if($postId > 0){
            $postModel = $this->_getBlogPostModel();
            $post = $postModel->getPostById($postId);
            if($post){
                $categoryModel = $this->_getBlogCategoryModel();
                $viewParams = array(
                    'postTitle' => $post['title'],
                    'postLead' => $post['lead'],
                    'postContent' => $post['content'],
                    'categoryId' => $post['category_id'],
                    'postCover' => $post['cover_image'],
                );
                $breadcrumbs = array();
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/index/index'),
                    'text' => __('admin_page')
                );
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/blog/index'),
                    'text' => __('admin_blog_post')
                );
                $breadcrumbs[] = array(
                    'url' => '',
                    'text' => __('edit_post')
                );

                if(Mava_Url::isPost()){
                    $viewParams = Mava_Url::getParams();
                    if($viewParams['postTitle'] == '')
                    {
                        $viewParams['error_message'] = __('please_enter_post_title');
                    }
                    else if($viewParams['postLead'] == '')
                    {
                        $viewParams['error_message'] = __('please_enter_post_lead');
                    }
                    else if($viewParams['postContent'] == '')
                    {
                        $viewParams['error_message'] = __('please_enter_post_content');
                    }
                    else if($viewParams['categoryId'] == '')
                    {
                        $viewParams['error_message'] = __('please_choose_post_category');
                    }
                    else
                    {
                        $category = $categoryModel->getCategoryById($viewParams['categoryId']);
                        if($category){
                            $cover = '';
                            if(isset($_FILES['postCover']) && $_FILES['postCover']['tmp_name'] != ""){
                                $image = upload_image('upload','postCover');
                                if($image['error'] == 0){
                                    $cover = $image['image'];
                                }
                            }
                            $postDW = $this->_getBlogPostDataWriter();
                            $postDW->setExistingData($postId);
                            $postDW->bulkSet(array(
                                'title'         => $viewParams['postTitle'],
                                'lead'          => $viewParams['postLead'],
                                'content'       => $viewParams['postContent'],
                                'updated_by'    => (int)Mava_Visitor::getUserId(),
                                'updated_date'  => time(),
                                'category_id'   => $viewParams['categoryId'],
                                'cover_image'   => ($cover != ""?$cover:$post['cover_image'])
                            ));

                            if($postDW->save()){
                                return $this->responseRedirect(Mava_Url::buildLink('admin/blog/index', array('updated' => $postDW->get('post_id'))));
                            }else{
                                $viewParams['error_message'] = __('can_not_edit_post');
                            }
                        }else{
                            $viewParams['error_message'] = __('category_not_found');
                        }
                    }
                }

                $viewParams['postId'] = $postId;
                $viewParams['categories'] = $categoryModel->getAllCategory();
                $viewParams['breadcrumbs'] = $breadcrumbs;
                return $this->responseView('Admin_View_Blog_Edit',$viewParams);
            }else{
                return $this->responseError(__('post_not_found'), Mava_Error::NOT_FOUND);
            }
        }else{
            return $this->responseError(__('post_not_found'), Mava_Error::NOT_FOUND);
        }
    }
    public function addAction(){
        Mava_Application::set('seo/title',__('add_post'));
        $categoryId = (int)Mava_Url::getParam('categoryId');
        $categoryModel = $this->_getBlogCategoryModel();
        $viewParams = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/blog/index'),
            'text' => __('admin_blog_post')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_post')
        );

        if(Mava_Url::isPost()){
            $viewParams = Mava_Url::getParams();
            if($viewParams['postTitle'] == '')
            {
                $viewParams['error_message'] = __('please_enter_post_title');
            }
            else if($viewParams['postLead'] == '')
            {
                $viewParams['error_message'] = __('please_enter_post_lead');
            }
            else if($viewParams['postContent'] == '')
            {
                $viewParams['error_message'] = __('please_enter_post_content');
            }
            else if($viewParams['categoryId'] == '')
            {
                $viewParams['error_message'] = __('please_choose_post_category');
            }
            else
            {
                $category = $categoryModel->getCategoryById($viewParams['categoryId']);
                if($category){
                    $cover = '';
                    if(isset($_FILES['postCover']) && $_FILES['postCover']['tmp_name'] != ""){
                        $image = upload_image('upload','postCover');
                        if($image['error'] == 0){
                            $cover = $image['image'];
                        }
                    }
                    $postDW = $this->_getBlogPostDataWriter();
                    $postDW->bulkSet(array(
                        'title'         => $viewParams['postTitle'],
                        'lead'          => $viewParams['postLead'],
                        'content'       => $viewParams['postContent'],
                        'created_by'    => (int)Mava_Visitor::getUserId(),
                        'created_date'  => time(),
                        'category_id'   => $viewParams['categoryId'],
                        'cover_image'   => $cover
                    ));

                    if($postDW->save()){
                        return $this->responseRedirect(Mava_Url::buildLink('admin/blog/index', array('added' => $postDW->get('post_id'))));
                    }else{
                        $viewParams['error_message'] = __('can_not_add_post');
                    }
                }else{
                    $viewParams['error_message'] = __('category_not_found');
                }
            }
        }
        if(isset($categoryId) && $categoryId > 0){
            $viewParams['categoryId'] = $categoryId;
        }

        $viewParams['categories'] = $categoryModel->getAllCategory();
        $viewParams['breadcrumbs'] = $breadcrumbs;
        return $this->responseView('Admin_View_Blog_Add',$viewParams);
    }

    public function indexAction(){
        Mava_Application::set('seo/title',__('admin_blog_post'));
        $categoryId = (int)Mava_Url::getParam('categoryId');
        $searchTerm = Mava_Url::getParam('q');
        $page = max((int)Mava_Url::getParam('page'), 1);

        $offset = Mava_Application::getOptions()->adminPaginationOffset;
        $limit = Mava_Application::getOptions()->defaultAdminROP;
        $skip = ($page-1)*$limit;

        $postModel = $this->_getBlogPostModel();
        $posts = $postModel->getListPost($skip, $limit, $categoryId, $searchTerm);
        $categoryModel = $this->_getBlogCategoryModel();
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $category = array();
        $inCategory = 0;
        $categoryBreadcrumb = '';
        if($categoryId > 0){
            $category = $categoryModel->getCategoryById($categoryId);
            if($category){
                $inCategory = 1;
                $categoryBreadcrumb = array(
                    'url' => '',
                    'text' => $category['title']
                );
            }
        }


        $breadcrumbs[] = array(
            'url' => (($inCategory==1||$searchTerm!="")?Mava_Url::buildLink('admin/blog/index'):''),
            'text' => __('admin_blog_post')
        );

        if($categoryBreadcrumb != ''){
            $breadcrumbs[] = $categoryBreadcrumb;
        }

        $added = (int)Mava_Url::getParam('added');
        $updated = (int)Mava_Url::getParam('updated');
        $deleted = (int)Mava_Url::getParam('deleted');
        return $this->responseView('Admin_View_Blog_List',array(
            'searchTerm' => $searchTerm,
            'categoryId' => $categoryId,
            'category' => $category,
            'posts' => $posts['rows'],
            'total' => $posts['total'],
            'page' => $page,
            'skip' => $skip,
            'limit' => $limit,
            'offset' => $offset,
            'breadcrumbs' => $breadcrumbs,
            'deleted' => $deleted,
            'added' => $added,
            'updated' => $updated
        ));
    }

    public function add_categoryAction(){
        Mava_Application::set('seo/title', __('add_category'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/blog/category'),
            'text' => __('admin_blog_categories')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_category')
        );
        if(Mava_Url::isPost()){
            $categoryTitle = Mava_Url::getParam('categoryTitle');
            $categoryDescription = Mava_Url::getParam('categoryDescription');
            $categorySortOrder = Mava_Url::getParam('categorySortOrder');
            if($categoryTitle == ''){
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('please_enter_blog_category_title')
                ));
            }else{
                $categoryDW = $this->_getBlogCategoryDataWriter();
                $categoryDW->bulkSet(array(
                    'title' => $categoryTitle,
                    'description' => $categoryDescription,
                    'sort_order' => (int)$categorySortOrder
                ));
                if($categoryDW->save()){
                    return $this->responseJson(array(
                        'status' => 1,
                        'message' => __('blog_category_added')
                    ));
                }
            }
        }
        return $this->responseView('Admin_View_Blog_Category_Add', array(
            'breadcrumbs' => $breadcrumbs
        ));
    }

    public function categoryAction(){
        Mava_Application::set('seo/title', __('admin_blog_categories'));
        $categoryModel = $this->_getBlogCategoryModel();
        $categories = $categoryModel->getAllCategory();
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );$breadcrumbs[] = array(
            'url' => '',
            'text' => __('admin_blog_categories')
        );
        return $this->responseView('Admin_View_Blog_Category_List', array(
            'categories' => $categories,
            'breadcrumbs' => $breadcrumbs
        ));
    }

    public function edit_categoryAction(){
        $categoryId = (int)Mava_Url::getParam('categoryId');
        if($categoryId > 0){
            $categoryModel = $this->_getBlogCategoryModel();
            $category = $categoryModel->getCategoryById($categoryId);
            if($category){
                Mava_Application::set('seo/title', __('edit_category'));
                $breadcrumbs = array();
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/index/index'),
                    'text' => __('admin_page')
                );
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/blog/category'),
                    'text' => __('admin_blog_categories')
                );
                $breadcrumbs[] = array(
                    'url' => '',
                    'text' => __('edit_category')
                );
                if(Mava_Url::isPost()){
                    $categoryTitle = Mava_Url::getParam('categoryTitle');
                    $categoryDescription = Mava_Url::getParam('categoryDescription');
                    $categorySortOrder = Mava_Url::getParam('categorySortOrder');
                    if($categoryTitle == ''){
                        return $this->responseJson(array(
                            'status' => -1,
                            'message' => __('please_enter_blog_category_title')
                        ));
                    }else{
                        $categoryDW = $this->_getBlogCategoryDataWriter();
                        $categoryDW->setExistingData($category['category_id']);
                        $categoryDW->bulkSet(array(
                            'title' => $categoryTitle,
                            'description' => $categoryDescription,
                            'sort_order' => (int)$categorySortOrder
                        ));
                        if($categoryDW->save()){
                            return $this->responseJson(array(
                                'status' => 1,
                                'message' => __('blog_category_updated')
                            ));
                        }
                    }
                }
                return $this->responseView('Admin_View_Blog_Category_Edit', array(
                    'breadcrumbs' => $breadcrumbs,
                    'category' => $category
                ));
            }else{
                if(Mava_Url::isPost()){
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('blog_category_not_found')
                    ));
                }else{
                    return $this->responseError(__('blog_category_not_found'), Mava_Error::NOT_FOUND);
                }
            }
        }else{
            if(Mava_Url::isPost()){
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('blog_category_not_found')
                ));
            }else{
                return $this->responseError(__('blog_category_not_found'), Mava_Error::NOT_FOUND);
            }
        }
    }

    public function delete_categoryAction(){
        $categoryId = (int)Mava_Url::getParam('categoryId');
        if($categoryId > 0){
            $categoryModel = $this->_getBlogCategoryModel();
            $category = $categoryModel->getCategoryById($categoryId);
            if($category){
                $postModel = $this->_getBlogPostModel();
                $countPost = $postModel->countPostInCategory($categoryId);
                if($countPost > 0){
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('delete_blog_post_first')
                    ));
                }else{
                    $categoryDW = $this->_getBlogCategoryDataWriter();
                    $categoryDW->setExistingData($categoryId);
                    if($categoryDW->delete()){
                        return $this->responseJson(array(
                            'status' => 1,
                            'message' => __('blog_category_deleted')
                        ));
                    }else{
                        return $this->responseJson(array(
                            'status' => -1,
                            'message' => __('can_not_delete_blog_category_nơ')
                        ));
                    }
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('blog_category_not_found')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('blog_category_not_found')
            ));
        }
    }

    /**
     * @return Blog_Model_Category
     */
    protected function _getBlogCategoryModel(){
        return $this->getModelFromCache('Blog_Model_Category');
    }

    /**
     * @return Blog_Model_Post
     */
    protected function _getBlogPostModel(){
        return $this->getModelFromCache('Blog_Model_Post');
    }

    /**
     * @return Blog_DataWriter_Category
     * @throws Mava_Exception
     */
    protected function _getBlogCategoryDataWriter(){
        return Mava_DataWriter::create('Blog_DataWriter_Category');
    }

    /**
     * @return Blog_DataWriter_Post
     * @throws Mava_Exception
     */
    protected function _getBlogPostDataWriter(){
        return Mava_DataWriter::create('Blog_DataWriter_Post');
    }
}