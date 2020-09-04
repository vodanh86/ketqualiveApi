<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 6/29/16
 * @Time: 11:27 AM
 */
class Admin_Controller_Banner extends Mava_AdminController {
    public function indexAction(){
        Mava_Application::set('seo/title', __('banner'));
        $page = max((int)Mava_Url::getParam('page'),1);
        $limit = 50;
        $skip = ($page-1)*$limit;
        $position_id = (int)Mava_Url::getParam('positionID');
        $bannerModel = $this->_getBannerModel();
        $position = $bannerModel->getAllPosition();
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/banner/index'),
            'text' => __('all_banner')
        );
        $languages = Mava_Application::getOptions()->contentLanguages;
        $banners = $bannerModel->getBanners($skip, $limit, $position_id, true);
        $params = array();
        if($position_id > 0){
            $params['positionID'] = $position_id;
        }
        $pagination = Mava_View::buildPagination(Mava_Url::getPageLink('admin/banner/index', $params),ceil($banners['total']/$limit),$page);
        if($position_id > 0 && $position = $bannerModel->getPositionById($position_id)){
            $breadcrumbs[] = array(
                'url' => '',
                'text' => $position['title'] .' ('. $position['position'] .')'
            );
        }else{
            $position = false;
        }
        return $this->responseView('Admin_View_Banner_List', array(
            'position' => $position,
            'page' => $page,
            'limit' => $limit,
            'total' => $banners['total'],
            'pagination' => $pagination,
            'banners' => $banners['rows'],
            'languages' => $languages,
            'breadcrumbs' => $breadcrumbs
        ));
    }

    public function addAction(){
        Mava_Application::set('seo/title', __('add_banner'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/banner/index'),
            'text' => __('all_banner')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_banner')
        );
        $error_message = '';
        $bannerModel = $this->_getBannerModel();
        $position = $bannerModel->getAllPosition();
        $languageModel = $this->_getLanguageModel();
        $languages = Mava_Application::getOptions()->contentLanguages;
        if(is_array($languages) && count($languages) > 0){
            $languages = $languageModel->getLanguageByCodes($languages);
            if(Mava_Url::isPost()){
                $postData = Mava_Url::getParams();
                if(!isset($postData['bannerPosition']) || $postData['bannerPosition'] == 0){
                    $error_message = __('please_choose_banner_position');
                }else if(!isset($postData['bannerImage']) || !is_array($postData['bannerImage']) || count($postData['bannerImage']) == 0){
                    $error_message = __('please_upload_banner_image');
                }else{
                    $bannerDW = $this->_getBannerDataWriter();
                    $bannerDW->bulkSet(array(
                        'position_id' => $postData['bannerPosition'],
                        'sort_order' => $postData['bannerSortOrder']
                    ));
                    if($bannerDW->save()){
                        foreach($languages as $item){
                            if(isset($postData['bannerImage']) && isset($postData['bannerImage'][$item['language_code']]) && $postData['bannerImage'][$item['language_code']] != ""){
                                $bannerDataDW = $this->_getBannerDataDataWriter();
                                $bannerDataDW->bulkSet(array(
                                    'banner_id' => $bannerDW->get('id'),
                                    'title' => (isset($postData['bannerTitle']) && isset($postData['bannerTitle'][$item['language_code']])?$postData['bannerTitle'][$item['language_code']]:''),
                                    'subtitle' => (isset($postData['bannerSubtitle']) && isset($postData['bannerSubtitle'][$item['language_code']])?$postData['bannerSubtitle'][$item['language_code']]:''),
                                    'href' => (isset($postData['bannerHref']) && isset($postData['bannerHref'][$item['language_code']])?$postData['bannerHref'][$item['language_code']]:''),
                                    'background' => (isset($postData['bannerBackground']) && isset($postData['bannerBackground'][$item['language_code']])?$postData['bannerBackground'][$item['language_code']]:''),
                                    'image' => (isset($postData['bannerImage']) && isset($postData['bannerImage'][$item['language_code']]) && is_array($postData['bannerImage'][$item['language_code']]))?json_encode($postData['bannerImage'][$item['language_code']]):'[]',
                                    'language_code' => $item['language_code']
                                ));
                                $bannerDataDW->save();
                            }
                        }
                        Mava_Url::redirect(Mava_Url::getPageLink('admin/banner/index', array('added' => 1)));
                    }else{
                        $error_message = __('can_not_add_banner');
                    }
                }
            }
            return $this->responseView('Admin_View_Banner_Add', array(
                'languages' => $languages,
                'position' => $position,
                'breadcrumbs' => $breadcrumbs,
                'error_message' => $error_message
            ));
        }else{
            return $this->responseError(__('content_language_option_not_set'),Mava_Error::SERVER_ERROR);
        }
    }

    public function editAction(){
        Mava_Application::set('seo/title', __('edit_banner'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/banner/index'),
            'text' => __('all_banner')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('edit_banner')
        );
        $banner_id = Mava_Url::getParam('id');
        $error_message = '';
        $bannerModel = $this->_getBannerModel();
        if($banner_id > 0 && $banner = $bannerModel->getById($banner_id, true)){
            $position = $bannerModel->getAllPosition();
            $languageModel = $this->_getLanguageModel();
            $languages = Mava_Application::getOptions()->contentLanguages;
            if(is_array($languages) && count($languages) > 0){
                $languages = $languageModel->getLanguageByCodes($languages);
                if(Mava_Url::isPost()){
                    $postData = Mava_Url::getParams();
                    if(!isset($postData['bannerPosition']) || $postData['bannerPosition'] == 0){
                        $error_message = __('please_choose_banner_position');
                    }else if(!isset($postData['bannerImage']) || !is_array($postData['bannerImage']) || count($postData['bannerImage']) == 0){
                        $error_message = __('please_upload_banner_image');
                    }else{
                        $bannerDW = $this->_getBannerDataWriter();
                        $bannerDW->setExistingData($banner_id);
                        $bannerDW->bulkSet(array(
                            'position_id' => $postData['bannerPosition'],
                            'sort_order' => $postData['bannerSortOrder']
                        ));
                        if($bannerDW->save()){
                            $bannerModel->deleteData($banner_id);
                            foreach($languages as $item){
                                if(isset($postData['bannerImage']) && isset($postData['bannerImage'][$item['language_code']]) && $postData['bannerImage'][$item['language_code']] != ""){
                                    $bannerDataDW = $this->_getBannerDataDataWriter();
                                    $bannerDataDW->bulkSet(array(
                                        'banner_id' => $banner_id,
                                        'title' => (isset($postData['bannerTitle']) && isset($postData['bannerTitle'][$item['language_code']])?$postData['bannerTitle'][$item['language_code']]:''),
                                        'subtitle' => (isset($postData['bannerSubtitle']) && isset($postData['bannerSubtitle'][$item['language_code']])?$postData['bannerSubtitle'][$item['language_code']]:''),
                                        'href' => (isset($postData['bannerHref']) && isset($postData['bannerHref'][$item['language_code']])?$postData['bannerHref'][$item['language_code']]:''),
                                        'background' => (isset($postData['bannerBackground']) && isset($postData['bannerBackground'][$item['language_code']])?$postData['bannerBackground'][$item['language_code']]:''),
                                        'image' => (isset($postData['bannerImage']) && isset($postData['bannerImage'][$item['language_code']]) && is_array($postData['bannerImage'][$item['language_code']]))?json_encode($postData['bannerImage'][$item['language_code']]):'[]',
                                        'language_code' => $item['language_code']
                                    ));
                                    $bannerDataDW->save();
                                }
                            }
                            Mava_Url::redirect(Mava_Url::getPageLink('admin/banner/index', array('updated' => 1)));
                        }else{
                            $error_message = __('can_not_edit_banner');
                        }
                    }
                }
                return $this->responseView('Admin_View_Banner_Edit', array(
                    'banner' => $banner,
                    'languages' => $languages,
                    'position' => $position,
                    'breadcrumbs' => $breadcrumbs,
                    'error_message' => $error_message
                ));
            }else{
                return $this->responseError(__('content_language_option_not_set'),Mava_Error::SERVER_ERROR);
            }
        }else{
            return $this->responseError(__('banner_not_found'),Mava_Error::SERVER_ERROR);
        }
    }

    public function deleteAction(){
        $banner_id = (int)Mava_Url::getParam('id');
        $bannerModel = $this->_getBannerModel();
        if($banner_id > 0 && $banner = $bannerModel->getById($banner_id)){
            $bannerModel->deleteData($banner_id);
            $bannerDW = $this->_getBannerDataWriter();
            $bannerDW->setExistingData($banner_id);
            if($bannerDW->delete()){
                return $this->responseJson(array(
                    'status' => 1,
                    'message' => __('banner_deleted')
                ));
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('can_not_delete_banner')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('banner_not_found')
            ));
        }
    }

    public function delete_positionAction(){
        $positionID = (int)Mava_Url::getParam('positionID');
        $bannerModel = $this->_getBannerModel();
        if($positionID > 0 && $position = $bannerModel->getPositionById($positionID)){
            $bannerPositionDW = $this->_getBannerPositionDataWriter();
            $bannerPositionDW->setExistingData($positionID);
            if($bannerPositionDW->delete()){
                return $this->responseJson(array(
                    'status' => 1,
                    'message' => __('banner_position_deleted')
                ));
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('can_not_delete_banner_position')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('banner_position_not_found')
            ));
        }
    }

    public function edit_positionAction(){
        Mava_Application::set('seo/title', __('edit_banner_position'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/banner/index'),
            'text' => __('banner')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/banner/position'),
            'text' => __('banner_position')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('edit_banner_position')
        );
        $positionID = (int)Mava_Url::getParam('positionID');
        $bannerModel = $this->_getBannerModel();
        if($positionID > 0 && $position = $bannerModel->getPositionById($positionID)){
            $error_message = '';
            if(Mava_Url::isPost()){
                $positionTitle = Mava_Url::getParam('positionTitle');
                $positionPosition = Mava_Url::getParam('positionCode');
                if($positionTitle == ""){
                    $error_message = __('banner_position_title_empty');
                }else if($positionPosition == ""){
                    $error_message = __('banner_position_position_empty');
                }else{
                    $bannerPositionDW = $this->_getBannerPositionDataWriter();
                    $bannerPositionDW->setExistingData($positionID);
                    $bannerPositionDW->bulkSet(array(
                        'title' => $positionTitle,
                        'position' => $positionPosition
                    ));
                    if($bannerPositionDW->save()){
                        Mava_Url::redirect(Mava_Url::getPageLink('admin/banner/position', array('edited' => 1)));
                    }else{
                        $error_message = __('can_not_edit_banner_position');
                    }
                }
            }
            return $this->responseView('Admin_View_Banner_Position_Edit', array(
                'position' => $position,
                'breadcrumbs' => $breadcrumbs,
                'error_message' => $error_message
            ));
        }else{
            return $this->responseError(__('banner_position_not_found'),Mava_Error::NOT_FOUND);
        }
    }

    public function add_positionAction(){
        Mava_Application::set('seo/title', __('add_banner_position'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/banner/index'),
            'text' => __('banner')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/banner/position'),
            'text' => __('banner_position')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_banner_position')
        );
        $error_message = '';
        if(Mava_Url::isPost()){
            $positionTitle = Mava_Url::getParam('positionTitle');
            $positionPosition = Mava_Url::getParam('positionCode');
            if($positionTitle == ""){
                $error_message = __('banner_position_title_empty');
            }else if($positionPosition == ""){
                $error_message = __('banner_position_position_empty');
            }else{
                $bannerPositionDW = $this->_getBannerPositionDataWriter();
                $bannerPositionDW->bulkSet(array(
                    'title' => $positionTitle,
                    'position' => $positionPosition
                ));
                if($bannerPositionDW->save()){
                    Mava_Url::redirect(Mava_Url::getPageLink('admin/banner/position', array('added' => 1)));
                }else{
                    $error_message = __('can_not_add_banner_position');
                }
            }
        }
        return $this->responseView('Admin_View_Banner_Position_Add', array(
            'breadcrumbs' => $breadcrumbs,
            'error_message' => $error_message
        ));
    }

    public function positionAction(){
        Mava_Application::set('seo/title', __('banner_position'));
        $bannerModel = $this->_getBannerModel();
        $positions = $bannerModel->getAllPosition();
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/banner/index'),
            'text' => __('banner')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('banner_position')
        );
        $languages = Mava_Application::getOptions()->contentLanguages;
        if(is_array($languages) && count($languages) > 0){
            return $this->responseView('Admin_View_Banner_Position_List', array(
                'positions' => $positions,
                'languages' => $languages,
                'breadcrumbs' => $breadcrumbs
            ));
        }else{
            return $this->responseError(__('content_language_option_not_set'),Mava_Error::SERVER_ERROR);
        }
    }

    /**
     * @return Index_DataWriter_BannerPosition
     * @throws Mava_Exception
     */
    protected function _getBannerPositionDataWriter(){
        return Mava_DataWriter::create('Index_DataWriter_BannerPosition');
    }
    /**
     * @return Index_DataWriter_Banner
     * @throws Mava_Exception
     */
    protected function _getBannerDataWriter(){
        return Mava_DataWriter::create('Index_DataWriter_Banner');
    }

    /**
     * @return Index_DataWriter_BannerData
     * @throws Mava_Exception
     */
    protected function _getBannerDataDataWriter(){
        return Mava_DataWriter::create('Index_DataWriter_BannerData');
    }

    /**
     * @return Index_Model_Banner
     */
    protected function _getBannerModel(){
        return $this->getModelFromCache('Index_Model_Banner');
    }

    /**
     * @return Mava_Model_Language
     */
    protected function _getLanguageModel(){
        return $this->getModelFromCache('Mava_Model_Language');
    }
}