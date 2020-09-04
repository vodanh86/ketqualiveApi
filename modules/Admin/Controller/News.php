<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 10/28/15
 * @Time: 4:23 PM
 */
class Admin_Controller_News extends Mava_AdminController {
    public function indexAction(){
        Mava_Application::set('seo/title',__('all_news'));
        $categoryId = (int)Mava_Url::getParam('categoryId');
        $searchTerm = Mava_Url::getParam('q');
        $page = max((int)Mava_Url::getParam('page'), 1);

        $offset = Mava_Application::getOptions()->adminPaginationOffset;
        $limit = Mava_Application::getOptions()->defaultAdminROP;
        $skip = ($page-1)*$limit;

        $newsModel = $this->_getNewsModel();
        $posts = $newsModel->getList($skip, $limit, $categoryId, $searchTerm);
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $category = array();
        $inCategory = 0;
        $categoryBreadcrumb = '';
        if($categoryId > 0){
            $category = $newsModel->getNewsCategoryById($categoryId, true);
            if($category){
                $inCategory = 1;
                $categoryBreadcrumb = array(
                    'url' => '',
                    'text' => $category['_data'][Mava_Visitor::getLanguageCode()]['title']
                );
            }
        }

        $breadcrumbs[] = array(
            'url' => (($inCategory==1||$searchTerm!="")?Mava_Url::buildLink('admin/blog/index'):''),
            'text' => __('all_news')
        );

        if($categoryBreadcrumb != ''){
            $breadcrumbs[] = $categoryBreadcrumb;
        }

        $added = (int)Mava_Url::getParam('added');
        $updated = (int)Mava_Url::getParam('updated');
        $deleted = (int)Mava_Url::getParam('deleted');
        return $this->responseView('Admin_View_News_List',array(
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

    public function delete_categoryAction(){
        $categoryID = (int)Mava_Url::getParam('categoryId');
        if($categoryID > 0){
            $newsModel = $this->_getNewsModel();
            $category = $newsModel->getNewsCategoryById($categoryID);
            if($category){
                if($newsModel->newsCategoryHasChild($categoryID) === true){
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('delete_child_category_first')
                    ));
                }else{
                    if($newsModel->hasNewsInCategory($categoryID) === true){
                        return $this->responseJson(array(
                            'status' => -1,
                            'message' => __('delete_news_first')
                        ));
                    }else{
                        $newsCategoryDW = $this->_getNewsCategoryDataWriter();
                        $newsCategoryDW->setExistingData($categoryID);
                        if($newsCategoryDW->delete()){
                            $newsModel->deleteNewsCategoryData($categoryID);
                            return $this->responseJson(array(
                                'status' => 1,
                                'message' => __('news_category_deleted')
                            ));
                        }else{
                            return $this->responseJson(array(
                                'status' => -1,
                                'message' => __('can_not_delete_news_category')
                            ));
                        }
                    }
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('news_category_not_found')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('news_category_not_found')
            ));
        }
    }

    public function edit_categoryAction(){
        Mava_Application::set('seo/title', __('edit_category'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/news/category'),
            'text' => __('news_category')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('edit_category')
        );
        $categoryID = (int)Mava_Url::getParam('categoryID');
        $newsModel = $this->_getNewsModel();
        if($categoryID > 0 && $category = $newsModel->getNewsCategoryById($categoryID, true)){
            $error_message = '';
            $categories = $newsModel->getAllCategory(true, $newsModel::TYPE_CATEGORY_DROPDOWN_PARENT, $categoryID);
            $languageModel = $this->_getLanguageModel();
            $languages = Mava_Application::getOptions()->contentLanguages;
            if(is_array($languages) && count($languages) > 0){
                $languages = $languageModel->getLanguageByCodes($languages);
                if(Mava_Url::isPost()){
                    $parentID = (int)Mava_Url::getParam('categoryParent');
                    $categorySortOrder = (int)Mava_Url::getParam('categorySortOrder');
                    $categoryTitle = Mava_Url::getParam('categoryTitle');
                    $categoryDescription = Mava_Url::getParam('categoryDescription');
                    if($parentID > 0 && !$categoryParent = $newsModel->getNewsCategoryById($parentID)){
                        $error_message = __('news_category_not_found');
                    }else if(!is_array($categoryTitle) || count($categoryTitle) == 0){
                        $error_message = __('news_category_title_empty');
                    }else{
                        $newsCategoryDW = $this->_getNewsCategoryDataWriter();
                        $newsCategoryDW->setExistingData($categoryID);
                        $newsCategoryDW->bulkSet(array(
                            'parent_id' => $parentID,
                            'sort_order' => $categorySortOrder
                        ));
                        if($newsCategoryDW->save()){
                            $newsModel->deleteNewsCategoryData($categoryID);
                            foreach($languages as $item){
                                if(isset($categoryTitle[$item['language_code']]) && $categoryTitle[$item['language_code']] != ""){
                                    $newsCategoryDataDW = $this->_getNewsCategoryDataDataWriter();
                                    $newsCategoryDataDW->bulkSet(array(
                                        'category_id' => $newsCategoryDW->get('category_id'),
                                        'title' => $categoryTitle[$item['language_code']],
                                        'descriptions' => $categoryDescription[$item['language_code']],
                                        'language_code' => $item['language_code']
                                    ));

                                    $newsCategoryDataDW->save();
                                }
                            }
                            Mava_Url::redirect(Mava_Url::getPageLink('admin/news/category', array('edited' => 1)));
                        }else{
                            $error_message = __('can_not_add_news_category');
                        }
                    }
                }
                return $this->responseView('Admin_View_News_Category_Edit', array(
                    'languages' => $languages,
                    'categories' => $categories,
                    'category' => $category,
                    'breadcrumbs' => $breadcrumbs,
                    'error_message' => $error_message
                ));
            }else{
                return $this->responseError(__('content_language_option_not_set'),Mava_Error::SERVER_ERROR);
            }
        }else{
            return $this->responseError(__('news_category_not_found'),Mava_Error::NOT_FOUND);
        }
    }

    public function add_categoryAction(){
        Mava_Application::set('seo/title', __('add_category'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/news/category'),
            'text' => __('news_category')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_category')
        );
        $error_message = '';
        $newsModel = $this->_getNewsModel();
        $categories = $newsModel->getAllCategory(true, $newsModel::TYPE_CATEGORY_DROPDOWN_PARENT);
        $languageModel = $this->_getLanguageModel();
        $languages = Mava_Application::getOptions()->contentLanguages;
        if(is_array($languages) && count($languages) > 0){
            $languages = $languageModel->getLanguageByCodes($languages);
            if(Mava_Url::isPost()){
                $parentID = (int)Mava_Url::getParam('categoryParent');
                $categorySortOrder = (int)Mava_Url::getParam('categorySortOrder');
                $categoryTitle = Mava_Url::getParam('categoryTitle');
                $categoryDescription = Mava_Url::getParam('categoryDescription');
                if($parentID > 0 && !$category = $newsModel->getNewsCategoryById($parentID)){
                    $error_message = __('news_category_not_found');
                }else if(!is_array($categoryTitle) || count($categoryTitle) == 0){
                    $error_message = __('news_category_title_empty');
                }else{
                    $newsCategoryDW = $this->_getNewsCategoryDataWriter();
                    $newsCategoryDW->bulkSet(array(
                        'parent_id' => $parentID,
                        'sort_order' => $categorySortOrder
                    ));
                    if($newsCategoryDW->save()){
                        foreach($languages as $item){
                            if(isset($categoryTitle[$item['language_code']]) && $categoryTitle[$item['language_code']] != ""){
                                $newsCategoryDataDW = $this->_getNewsCategoryDataDataWriter();
                                $newsCategoryDataDW->bulkSet(array(
                                    'category_id' => $newsCategoryDW->get('category_id'),
                                    'title' => $categoryTitle[$item['language_code']],
                                    'descriptions' => $categoryDescription[$item['language_code']],
                                    'language_code' => $item['language_code']
                                ));

                                $newsCategoryDataDW->save();
                            }
                        }
                        Mava_Url::redirect(Mava_Url::getPageLink('admin/news/category', array('added' => 1)));
                    }else{
                        $error_message = __('can_not_add_news_category');
                    }
                }
            }
            return $this->responseView('Admin_View_News_Category_Add', array(
                'languages' => $languages,
                'categories' => $categories,
                'breadcrumbs' => $breadcrumbs,
                'error_message' => $error_message
            ));
        }else{
            return $this->responseError(__('content_language_option_not_set'),Mava_Error::SERVER_ERROR);
        }
    }

    public function categoryAction(){
        Mava_Application::set('seo/title', __('news_category'));
        $newsModel = $this->_getNewsModel();
        $categories = $newsModel->getAllCategory(true);
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );$breadcrumbs[] = array(
            'url' => '',
            'text' => __('news_category')
        );
        $languages = Mava_Application::getOptions()->contentLanguages;
        if(is_array($languages) && count($languages) > 0){
            return $this->responseView('Admin_View_News_Category_List', array(
                'categories' => $categories,
                'languages' => $languages,
                'breadcrumbs' => $breadcrumbs
            ));
        }else{
            return $this->responseError(__('content_language_option_not_set'),Mava_Error::SERVER_ERROR);
        }
    }

    /**
     * @return News_DataWriter_NewsCategoryData
     * @throws Mava_Exception
     */
    protected function _getNewsCategoryDataDataWriter(){
        return Mava_DataWriter::create('News_DataWriter_NewsCategoryData');
    }

    /**
     * @return News_DataWriter_NewsCategory
     * @throws Mava_Exception
     */
    protected function _getNewsCategoryDataWriter(){
        return Mava_DataWriter::create('News_DataWriter_NewsCategory');
    }

    /**
     * @return News_Model_News
     */
    protected function _getNewsModel(){
        return $this->getModelFromCache('News_Model_News');
    }

    /**
     * @return Mava_Model_Language
     */
    protected function _getLanguageModel(){
        return $this->getModelFromCache('Mava_Model_Language');
    }
}