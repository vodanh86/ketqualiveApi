<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 4/9/14
 * Time: 12:20 AM
 * To change this template use File | Settings | File Templates.
 */
class Admin_Controller_Phrase extends Mava_AdminController {
    public function indexAction(){
        $languageID = (int)Mava_Url::getParam('languageID');
        $page = max((int)Mava_Url::getParam('page'),1);
        $limit = max(Mava_Application::get('options')->defaultAdminROP,50);
        $skip = ($page-1)*$limit;
        $pageOffset = max(Mava_Application::get('options')->defaultAdminPaginationOffset,5);
        $languageModel = $this->_getLanguageModel();
        $languages = $languageModel->getListLanguage();
        $phrase = array();
        if($languageID > 0){
            $current_language = $languageModel->getLanguageById($languageID);
            if($current_language){
                $phrase = $languageModel->getPhraseListByLanguageID($languageID, $skip, $limit);
            }else{
                return $this->responseError(__('language_not_found'),Mava_Error::NOT_FOUND);
            }
        }else{
            // master language
            $current_language = array(
                'id' => 0,
                'title' => __('master_language')
            );
            $phrase = $languageModel->getPhraseListInMaster($skip, $limit);
        }

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
            'text' => $current_language['title'] .': '. __('phrase')
        );

        // check max page
        $maxPage = max(ceil($phrase['total']/$limit),1);
        if($page > $maxPage){
            return $this->responseRedirect(Mava_Url::buildLink('admin/phrase/index',array(
                'languageID' => $languageID,
                'page' => $maxPage
            )));
        }

        // seo
        Mava_Application::set('seo/title',$current_language['title'] .': '. __('phrase'));

        return $this->responseView('Admin_View_Language_Phrase',array(
            'breadcrumbs' => $breadcrumbs,
            'phrases' => $phrase['phrase'],
            'total' => $phrase['total'],
            'total_page' => $maxPage,
            'skip' => $skip,
            'limit' => $limit,
            'page_offset' => $pageOffset,
            'page' => $page,
            'languages' => $languages,
            'current_language' => $current_language,
            'language_id' => $languageID
        ));
    }

    public function addAction(){
        $languageID = (int)Mava_Url::getParam('languageID');
        $languageModel = $this->_getLanguageModel();
        if($languageID > 0){
            $language = $languageModel->getLanguageById($languageID);
            if(!$language){
                return $this->responseError(__('language_not_found'),Mava_Error::NOT_FOUND);
            }
        }else{
            $language = array(
                'language_id' => 0,
                'title' => __('master_language')
            );
        }
        // seo
        Mava_Application::set('seo/title',$language['title'] .': '. __('add_phrase'));

        $addonModel = $this->_getAddonModel();
        $addons = $addonModel->getAllAddon();
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
            'url' => Mava_Url::buildLink('admin/phrase/index',array('languageID' => $languageID)),
            'text' => $language['title'] .': '. __('phrase')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_phrase')
        );
        return $this->responseView('Admin_View_Language_PhraseAdd',array(
            'breadcrumbs' => $breadcrumbs,
            'language' => $language,
            'addon' => $addons
        ));
    }

    public function do_addAction(){
        $languageID = (int)Mava_Url::getParam('languageID');
        $phraseTitle = trim(Mava_Url::getParam('phraseTitle'));
        $phraseText = Mava_Url::getParam('phraseText');
        $addOn = Mava_Url::getParam('addOnID');
        $languageModel = $this->_getLanguageModel();
        $data = array();
        if($phraseTitle==""){
            $data = array(
                'status' => -1,
                'message' => __('phrase_title_empty')
            );
        }else if($languageModel->isPhraseExist($phraseTitle, $languageID)){
            $data = array(
                'status' => -1,
                'message' => __('phrase_title_exist')
            );
        }else{
            $check = $languageModel->addPhrase($languageID, $phraseTitle, $phraseText, $addOn);
            if($check){
               $data = array(
                   'status' => 1,
                   'message' => __('phrase_add_success')
               );
            }else{
                $data = array(
                    'status' => -1,
                    'message' => __('phrase_add_failed')
                );
            }
        }
        return $this->responseJson($data);
    }

    public function editAction(){
        $languageID = (int)Mava_Url::getParam('languageID');
        $phraseID = (int)Mava_Url::getParam('phraseID');
        $languageModel = $this->_getLanguageModel();
        if($languageID > 0){
            $language = $languageModel->getLanguageById($languageID);
            if(!$language){
                return $this->responseError(__('language_not_found'),Mava_Error::NOT_FOUND);
            }
        }else{
            $language = array(
                'language_id' => 0,
                'title' => __('master_language')
            );
        }
        // seo
        Mava_Application::set('seo/title',$language['title'] .': '. __('edit_phrase'));

        $phrase = $languageModel->getPhraseEdit($phraseID, $languageID);
        if(!$phrase){
            return $this->responseError(__('phrase_not_found'),Mava_Error::NOT_FOUND);
        }
        $addonModel = $this->_getAddonModel();
        $addons = $addonModel->getAllAddon();
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
            'url' => Mava_Url::buildLink('admin/phrase/index',array('languageID' => $languageID)),
            'text' => $language['title'] .': '. __('phrase')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('edit_phrase')
        );

        $masterPhrase = array();
        if($phrase['phrase_state']=='custom' || $phrase['phrase_state']=='inherited'){
            $masterPhrase = $languageModel->getPhraseByTitle($phrase['title'],0);
        }
        return $this->responseView('Admin_View_Language_PhraseEdit',array(
            'breadcrumbs' => $breadcrumbs,
            'language' => $language,
            'phrase' => $phrase,
            'master_phrase' => $masterPhrase,
            'addon' => $addons
        ));
    }

    public function deleteAction(){
        $languageID = (int)Mava_Url::getParam('languageID');
        $phraseID = (int)Mava_Url::getParam('phraseID');
        $languageModel = $this->_getLanguageModel();
        $data = array();
        if($phraseID > 0){
            $check = $languageModel->deletePhrase($phraseID,$languageID);
            if($check){
                $data = array(
                    'status' => 1,
                    'message' => __('phrase_delete_success')
                );
            }else{
                $data = array(
                    'status' => -1,
                    'message' => __('phrase_delete_failed')
                );
            }
        }else{
            $data = array(
                'status' => -1,
                'message' => __('phrase_not_found')
            );
        }

        return $this->responseJson($data);
    }

    public function do_editAction(){
        $languageID = (int)Mava_Url::getParam('languageID');
        $phraseID = (int)Mava_Url::getParam('phraseID');
        $phraseTitle = trim(Mava_Url::getParam('phraseTitle'));
        $phraseText = Mava_Url::getParam('phraseText');
        $addOn = Mava_Url::getParam('addOnID');
        $languageModel = $this->_getLanguageModel();
        $data = array();
        if($phraseTitle==""){
            $data = array(
                'status' => -1,
                'message' => __('phrase_title_empty')
            );
        }else if($languageModel->isPhraseExist($phraseTitle, $languageID,array($phraseTitle))){
            $data = array(
                'status' => -1,
                'message' => __('phrase_title_exist')
            );
        }else{
            $phrase = $languageModel->getPhraseEdit($phraseID, $languageID);
            if(!$phrase){
                $data = array(
                    'status' => -1,
                    'message' => __('phrase_not_found')
                );
            }else{
                $check = $languageModel->editPhrase($phrase, $languageID, $phraseTitle, $phraseText, $addOn);
                if($check){
                    $data = array(
                        'status' => 1,
                        'message' => __('phrase_edit_success')
                    );
                }else{
                    $data = array(
                        'status' => -1,
                        'message' => __('phrase_edit_failed')
                    );
                }
            }

        }
        return $this->responseJson($data);
    }

    public function filter_phraseAction(){
        $prefixMatch = (int)Mava_Url::getParam('prefixMatch');
        $filterTitle = trim(Mava_Url::getParam('filterTitle'));
        $languageID = (int)Mava_Url::getParam('languageID');

        $languageModel = $this->_getLanguageModel();
        $phrases = array();
        if($filterTitle!=""){
            $phrases = $languageModel->searchPhraseList($filterTitle, $languageID, $prefixMatch);
        }
        $phraseHTML = Mava_View::getView('Admin_View_Language_PhraseFilter',array(
            'phrases' => $phrases,
            'language_id' => $languageID,
            'filter_title' => $filterTitle
        ));
        return $this->responseJson(array(
            'status' => 1,
            'phraseHTML' => $phraseHTML
        ));
    }

    /**
     * @return Mava_Model_Language
     */
    protected function _getLanguageModel(){
       return $this->getModelFromCache('Mava_Model_Language');
    }
    /**
     * @return Mava_Model_Addon
     */
    protected function _getAddonModel(){
       return $this->getModelFromCache('Mava_Model_Addon');
    }
}