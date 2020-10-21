<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 8/1/16
 * @Time: 4:42 PM
 */
class Admin_Controller_Novel extends Mava_AdminController {

    public function indexAction(){
        Mava_Application::set('seo', array(
            'title' => __('admin_novels')
        ));
        $page = max((int)Mava_Url::getParam('page'),1);
        $limit = 50;
        $skip = ($page-1)*$limit;
        $subVideo = $this->_getVideoModel();
        $videos = $subVideo->getList($skip, $limit);
        $baseUrl = Mava_Url::removeParam(Mava_Url::getCurrentAddress(),array('page'));
        $pagination = Mava_View::buildPagination($baseUrl,ceil($videos['total']/$limit),$page);
        
        $viewParams = array(
            'videos' => $videos['rows'],
            'total' => $videos['total'],
            'page' => $page,
            'skip' => $skip,
            'limit' => $limit,
            'pagination' => $pagination
        );
        return $this->responseView('Admin_View_Novel_List', $viewParams);
    }

    public function uploadAction(){
        return $this->responseView('Admin_View_Novel_Upload');
    }

    public function catAction(){
        Mava_Application::set('seo', array(
            'title' => __('admin_cats')
        ));
        $page = max((int)Mava_Url::getParam('page'),1);
        $limit = 50;
        $skip = ($page-1)*$limit;
        $subVideo = $this->_getVideoModel();
        $videos = $subVideo->getPageList($skip, $limit);
        $baseUrl = Mava_Url::removeParam(Mava_Url::getCurrentAddress(),array('page'));
        $pagination = Mava_View::buildPagination($baseUrl,ceil($videos['total']/$limit),$page);
        $viewParams = array(
            'videos' => $videos['items'],
            'total' => $videos['total'],
            'page' => $page,
            'skip' => $skip,
            'limit' => $limit,
            'pagination' => $pagination
        );
        return $this->responseView('Admin_View_Novel_List', $viewParams);
    }

    public function deleteAction(){
        $userID = (int)Mava_Url::getParam('videoID');
        if($userID > 0){
            $userModel = $this->_getVideoModel();
            $user = $userModel->getById($userID, false);
            if($user){
                $check = $userModel->deleteById($userID);
                if($check){
                    return $this->responseJson(array(
                        'status' => 1,
                        'message' => __('novel_deleted')
                    ));
                }else{
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('can_not_delete_novel')
                    ));
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('novel_not_found')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('novel_not_found')
            ));
        }
    }

    public function deleteChapterAction(){
        $userID = (int)Mava_Url::getParam('chapterId');
        if($userID > 0){
            $userModel = $this->_getVideoModel();
            $user = $userModel->getChapterById($userID, false);
            if($user){
                $check = $userModel->deleteChapterById($userID);
                if($check){
                    return $this->responseJson(array(
                        'status' => 1,
                        'message' => __('chapter_deleted')
                    ));
                }else{
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('can_not_delete_chapter')
                    ));
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('chapter_not_found')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('chapter_not_found')
            ));
        }
    }

    public function editAction(){
        Mava_Application::set('seo/title',__('edit_novel'));
        $languageID = (int)Mava_Url::getParam('novelId');
        $languageModel = $this->_getVideoModel();
        if($languageID > 0){
            $language = $languageModel->getById($languageID);
            if(!$language){
                return $this->responseError(__('novel_not_found'),Mava_Error::NOT_FOUND);
            }else{
                $breadcrumbs = array();
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/index/index'),
                    'text' => __('admin_page')
                );
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/novel/index'),
                    'text' => __('admin_novel')
                );
                $breadcrumbs[] = array(
                    'url' => '',
                    'text' => __('edit_novel')
                );
                return $this->responseView('Admin_View_Novel_Edit',array(
                    'breadcrumbs' => $breadcrumbs,
                    'language' => $language
                ));
            }
        }else{
            return $this->responseError(__('language_not_found'),Mava_Error::NOT_FOUND);
        }
    }

    public function viewAction(){
        Mava_Application::set('seo/title',__('vie_novel'));
        $languageID = (int)Mava_Url::getParam('novelId');
        $languageModel = $this->_getVideoModel();
        if($languageID > 0){
            $breadcrumbs = array();
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('admin/index/index'),
                'text' => __('admin_page')
            );
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('admin/novel/index'),
                'text' => __('admin_novel')
            );
            $breadcrumbs[] = array(
                'url' => '',
                'text' => __('edit_novel')
            );

            $page = max((int)Mava_Url::getParam('page'),1);
            $limit = 50;
            $skip = ($page-1)*$limit;
            $videos = $languageModel->getViewById($skip, $limit, $languageID);
            $baseUrl = Mava_Url::removeParam(Mava_Url::getCurrentAddress(),array('page'));
            $pagination = Mava_View::buildPagination($baseUrl,ceil($videos['total']/$limit),$page);
            
            $viewParams = array(
                'breadcrumbs' => $breadcrumbs,
                'videos' => $videos['rows'],
                'total' => $videos['total'],
                'page' => $page,
                'novelId' => $languageID,
                'skip' => $skip,
                'limit' => $limit,
                'pagination' => $pagination
            );
            return $this->responseView('Admin_View_Novel_View', $viewParams);

        }else{
            return $this->responseError(__('language_not_found'),Mava_Error::NOT_FOUND);
        }
    }

    public function do_editAction(){
        $novelId = Mava_Url::getParam('novelId');
        $novelName = Mava_Url::getParam('novelName');
        $novelDescription = Mava_Url::getParam('novelDescription');
        $novelImage = Mava_Url::getParam('novelImage');
        $novelAuthor = Mava_Url::getParam('novelAuthor');
        $novelCategory = Mava_Url::getParam('novelCategory');
        $novelStart = Mava_Url::getParam('novelStart');
        $novelView = Mava_Url::getParam('novelView');
        $languageModel = $this->_getVideoModel();
        $data = array();
        $language = $languageModel->getById($novelId);
        if($novelId==0 || !$language){
            $data = array(
                'status' => -1,
                'message' => __('language_not_found')
            );
        }else if($novelName==""){
            $data = array(
                'status' => -1,
                'message' => __('novelName_empty')
            );
        }else{
            $check = $languageModel->editNovel(
                $novelId,
                $novelName,
                $novelDescription,
                $novelImage,
                $novelAuthor,
                $novelCategory,
                $novelStart,
                $novelView             
            );
            if($check){
                $data = array(
                    'status' => 1,
                    'message' => __('language_edit_success')
                );
            }else{
                $data = array(
                    'status' => -1,
                    'message' => __('language_edit_failed')
                );
            }
        }
        return $this->responseJson($data);
    }

    public function addAction(){
        Mava_Application::set('seo/title',__('add_novel'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/novel/index'),
            'text' => __('admin_novel')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_novel')
        );

        $error_message = '';

        $userModel = $this->_getVideoModel();
        $userData = array();
        if(Mava_Url::isPost()){
            $videoData = array(
                'name' => str_replace('"', '\"', Mava_Url::getParam('name')),
                'description' => str_replace('"', '\"', Mava_Url::getParam('description')),
                'image' => str_replace('"', '\"', Mava_Url::getParam('image')),
                'author' => str_replace('"', '\"', Mava_Url::getParam('author')),
                'category_id' => Mava_Url::getParam('category_id'),
            );

            $userID = $userModel->insert($videoData);

            if($userID > 0){
                Mava_Url::redirect(Mava_Url::getPageLink('admin/novel/index', array('added' => 1)));
            }else{
                $error_message = __('can_not_add_novel');
            }
        }
        return $this->responseView('Admin_View_Novel_Add', array(
            'breadcrumbs' => $breadcrumbs,
            'novel' => $userData,
            'error_message' => $error_message,
        ));
    }

    public function addChapterAction(){
        Mava_Application::set('seo/title',__('add_chapter'));
        $novelId = (int)Mava_Url::getParam('novelId');
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/novel/view', array("novelId" => $novelId)),
            'text' => __('admin_novel')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_chapter')
        );

        $error_message = '';

        $userModel = $this->_getVideoModel();
        $userData = array();
        if(Mava_Url::isPost()){
            $videoData = array(
                'name' => str_replace('"', '\"', Mava_Url::getParam('name')),
                'link' => str_replace('"', '\"', Mava_Url::getParam('link')),
                'order' => Mava_Url::getParam('order'),
                'novel_id' => $novelId,
            );

            $userID = $userModel->insertChapter($videoData);

            if($userID > 0){
                Mava_Url::redirect(Mava_Url::getPageLink('admin/novel/view', array("novelId" => $novelId, 'added' => 1)));
            }else{
                $error_message = __('can_not_add_novel');
            }
        }
        return $this->responseView('Admin_View_Novel_AddChapter', array(
            'breadcrumbs' => $breadcrumbs,
            'novel' => $userData,
            'novelId' => $novelId,
            'error_message' => $error_message,
        ));
    }

    /**
     * @return API_Model_Video
     */
    protected function _getVideoModel()
    {
        return $this->getModelFromCache('Novel_Model_Novel');
    }

    /**
     * @return Index_DataWriter_Subscribe
     * @throws Mava_Exception
     */
    protected function _getSubscribeDataWriter(){
        return Mava_DataWriter::create('Index_DataWriter_Subscribe');
    }
}