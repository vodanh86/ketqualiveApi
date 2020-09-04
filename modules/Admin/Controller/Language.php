<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 4/8/14
 * Time: 9:13 PM
 * To change this template use File | Settings | File Templates.
 */
class Admin_Controller_Language extends Mava_AdminController {
    public function indexAction(){
        // seo
        Mava_Application::set('seo/title',__('admin_language'));
        $languageModel = $this->_getLanguageModel();
        $languages = $languageModel->getListLanguage();
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('admin_language')
        );
        return $this->responseView('Admin_View_Language_List',array(
            'languages' => $languages,
            'breadcrumbs' => $breadcrumbs
        ));
    }
    public function addAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        // seo
        Mava_Application::set('seo/title',__('add_language'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/language/index'),
            'text' => __('admin_language')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_language')
        );
        return $this->responseView('Admin_View_Language_Add',array(
            'breadcrumbs' => $breadcrumbs
        ));
    }

    public function do_addAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $languageTitle = Mava_Url::getParam('languageTitle');
        $languageCode = Mava_Url::getParam('languageCode');
        $dateFormat = Mava_Url::getParam('dateFormat');
        $timeFormat = Mava_Url::getParam('timeFormat');
        $textDirection = Mava_Url::getParam('textDirection');
        $decimalPoint = Mava_Url::getParam('decimalPoint');
        $thousandsSeparator = Mava_Url::getParam('thousandsSeparator');
        $languageModel = $this->_getLanguageModel();
        $data = array();
        if($languageTitle==""){
            $data = array(
                'status' => -1,
                'message' => __('language_title_empty')
            );
        }else if($languageCode==""){
            $data = array(
                'status' => -1,
                'message' => __('language_code_empty')
            );
        }else if($languageModel->languageCodeExist($languageCode)){
            $data = array(
                'status' => -1,
                'message' => __('language_code_exist')
            );
        }else if($dateFormat==""){
            $data = array(
                'status' => -1,
                'message' => __('date_format_invalid')
            );
        }else if($timeFormat==""){
            $data = array(
                'status' => -1,
                'message' => __('time_format_invalid')
            );
        }else if(!in_array($textDirection,array('LTR','RTL'))){
            $data = array(
                'status' => -1,
                'message' => __('text_direction_invalid')
            );
        }else{
            $check = $languageModel->addLanguage(
                $languageTitle,
                $languageCode,
                $dateFormat,
                $timeFormat,
                $textDirection,
                $decimalPoint,
                $thousandsSeparator
            );
            if($check){
                $data = array(
                    'status' => 1,
                    'message' => __('language_add_success')
                );
            }else{
                $data = array(
                    'status' => -1,
                    'message' => __('language_add_failed')
                );
            }
        }

        return $this->responseJson($data);
    }

    public function editAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        // seo
        Mava_Application::set('seo/title',__('edit_language'));
        $languageID = (int)Mava_Url::getParam('languageID');
        $languageModel = $this->_getLanguageModel();
        if($languageID > 0){
            $language = $languageModel->getLanguageById($languageID);
            if(!$language){
                return $this->responseError(__('language_not_found'),Mava_Error::NOT_FOUND);
            }else{
                $breadcrumbs = array();
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/index/index'),
                    'text' => __('admin_page')
                );
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/language/index'),
                    'text' => __('admin_language')
                );
                $breadcrumbs[] = array(
                    'url' => '',
                    'text' => __('edit_language')
                );
                return $this->responseView('Admin_View_Language_Edit',array(
                    'breadcrumbs' => $breadcrumbs,
                    'language' => $language
                ));
            }
        }else{
            return $this->responseError(__('language_not_found'),Mava_Error::NOT_FOUND);
        }
    }

    public function do_editAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $languageID = Mava_Url::getParam('languageID');
        $languageTitle = Mava_Url::getParam('languageTitle');
        $languageCode = Mava_Url::getParam('languageCode');
        $dateFormat = Mava_Url::getParam('dateFormat');
        $timeFormat = Mava_Url::getParam('timeFormat');
        $textDirection = Mava_Url::getParam('textDirection');
        $decimalPoint = Mava_Url::getParam('decimalPoint');
        $thousandsSeparator = Mava_Url::getParam('thousandsSeparator');
        $languageModel = $this->_getLanguageModel();
        $data = array();
        $language = $languageModel->getLanguageById($languageID);
        if($languageID==0 || !$language){
            $data = array(
                'status' => -1,
                'message' => __('language_not_found')
            );
        }else if($languageTitle==""){
            $data = array(
                'status' => -1,
                'message' => __('language_title_empty')
            );
        }else if($languageCode==""){
            $data = array(
                'status' => -1,
                'message' => __('language_code_empty')
            );
        }else if($languageModel->languageCodeExist($languageCode,$language['language_id'])){
            $data = array(
                'status' => -1,
                'message' => __('language_code_exist')
            );
        }else if($dateFormat==""){
            $data = array(
                'status' => -1,
                'message' => __('date_format_invalid')
            );
        }else if($timeFormat==""){
            $data = array(
                'status' => -1,
                'message' => __('time_format_invalid')
            );
        }else if(!in_array($textDirection,array('LTR','RTL'))){
            $data = array(
                'status' => -1,
                'message' => __('text_direction_invalid')
            );
        }else{
            $check = $languageModel->editLanguage(
                $languageID,
                $languageTitle,
                $languageCode,
                $dateFormat,
                $timeFormat,
                $textDirection,
                $decimalPoint,
                $thousandsSeparator
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

    public function deleteAction(){
        if(!is_debug()){
            $data = array(
                'status' => -1,
                'message' => __('run_on_debug_mode')
            );
        }else{
            $languageID = (int)Mava_Url::getParam('languageID');
            $data = array();
            if($languageID == 0){
                $data = array(
                    'status' => -1,
                    'message' => __('cannot_delete_master_language')
                );
            }else{
                $check = $this->_getLanguageModel()->deleteLanguage($languageID);
                if($check){
                    $data = array(
                        'status' => 1,
                        'message' => __('language_delete_success')
                    );
                }else{
                    $data = array(
                        'status' => -1,
                        'message' => __('language_delete_failed')
                    );
                }
            }
        }
        return $this->responseJson($data);
    }

    /**
     * @return Mava_Model_Language
     */
    protected function _getLanguageModel(){
        return $this->getModelFromCache('Mava_Model_Language');
    }
}