<?php

class Page_Controller_Page extends Mava_Controller
{
    public function viewAction()
    {
        $page_id = (int)Mava_Application::get('page_id', 'static_page');
        if ($page_id > 0) {
            $pageModel = $this->_getPageModel();
            $page = $pageModel->getPageById($page_id);
            if ($page) {
                $pageDataModel = $this->_getPageDataModel();
                $language_code = Mava_Visitor::getLanguageCode();
                $pageData = $pageDataModel->getPageDataByLang($page_id, $language_code);
                if ($pageData) {
                    Mava_Application::set('seo', array(
                        'title' => $pageData['short_title'],
                    ));

                    $layout = $page['layout'];
                    $show_title = $page['show_title'];
                    $page_same_group = array();

                    if($page['group_id'] > 0){
                        $pageGroupModel = $this->_getPageGroupModel();
                        $group = $pageGroupModel->getPageGroupById($page['group_id']);
                        if($group){
                            $page_same_group = $pageDataModel->getSamePageGroup($language_code, $page_id, $page['group_id']);
                        }
                    }

                    return $this->responseView('Page_View_View', array(
                        'layout'    =>  $layout,
                        'show_title'    =>  $show_title,
                        'short_title'    =>  $pageData['short_title'],
                        'long_title'    =>  $pageData['long_title'],
                        'content_css'    =>  $pageData['content_css'],
                        'content_js'    =>  $pageData['content_js'],
                        'content_html'    =>  $pageData['content_html'],
                        'page_same_group'    =>  $page_same_group,
                        'page_id'    =>  $page_id,
                    ));
                } else {
                    return $this->responseView('Mava_View_Error', array(
                        'error_code' => Mava_Error::NOT_FOUND
                    ));
                }
            } else {
                return $this->responseView('Mava_View_Error', array(
                    'error_code' => Mava_Error::NOT_FOUND
                ));
            }
        } else {
            return $this->responseView('Mava_View_Error', array(
                'error_code' => Mava_Error::NOT_FOUND
            ));
        }
    }

    /**
     * @return Mava_Model_Page
     */
    protected function _getPageModel()
    {
        return $this->getModelFromCache('Mava_Model_Page');
    }

    /**
     * @return Mava_Model_PageGroup
     */
    protected function _getPageGroupModel()
    {
        return $this->getModelFromCache('Mava_Model_PageGroup');
    }

    /**
     * @return Mava_Model_PageData
     */
    protected function _getPageDataModel()
    {
        return $this->getModelFromCache('Mava_Model_PageData');
    }
}