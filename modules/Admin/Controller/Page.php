<?php

class Admin_Controller_Page extends Mava_AdminController
{

    public function indexAction()
    {
        Mava_Application::set('seo/title', __('list_static_page'));

        $search_tearm = Mava_Url::getParam('q');
        $page = max((int)Mava_Url::getParam('page'), 1);
        //$limit = max(Mava_Application::get('options')->defaultAdminROP,50);
        $limit = 20;

        $skip = ($page - 1) * $limit;
        $pageOffset = max(Mava_Application::get('options')->defaultAdminPaginationOffset, 5);

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/page/index'),
            'text' => __('list_static_page')
        );

        $pageModel = $this->_getPageModel();

        $list_page = $pageModel->getListPage($skip, $limit, $search_tearm);

        // check max page
        $maxPage = max(ceil($list_page['total'] / $limit), 1);
        if ($page > $maxPage) {
            return $this->responseRedirect(Mava_Url::buildLink('admin/page/index', array(
                'page' => $maxPage
            )));
        }

        return $this->responseView('Admin_View_Page_Index', array(
            'search_tearm' => $search_tearm,
            'list_page' => $list_page['rows'],
            'breadcrumbs' => $breadcrumbs,
            'total' => $list_page['total'],
            'total_page' => $maxPage,
            'skip' => $skip,
            'limit' => $limit,
            'page_offset' => $pageOffset,
            'page' => $page,
        ));
    }

    public function addAction()
    {
        Mava_Application::set('seo/title', __('add_new_static_page'));

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/page/index'),
            'text' => __('static_page')
        );

        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_new_static_page')
        );

        $error_message = '';
        $postData = array();

        if (Mava_Url::isPost()) {
            $postData = $_POST;

            $slug = Mava_Url::getParam('slug');
            $group_id = Mava_Url::getParam('group_id');
            $layout = Mava_Url::getParam('layout');
            $publish_time = Mava_Url::getParam('publish_time');
            $unpublish_time = Mava_Url::getParam('unpublish_time');
            $sort_order = Mava_Url::getParam('sort_order');
            $show_title = Mava_Url::getParam('show_title');

            if ($slug == '') {
                $error_message = __('slug_empty');
            } else {
                $slug = Mava_String::unsignString($slug, '-', true);
                $pageModel = $this->_getPageModel();
                $check = $pageModel->checkPageSlugExist($slug);
                //slug trong bảng page chưa tồn tại
                if (!$check) {
                    $dataInsert = array(
                        'slug' => $slug,
                        'group_id' => $group_id,
                        'layout' => $layout,
                        'publish_time' => $publish_time,
                        'unpublish_time' => $unpublish_time,
                        'sort_order' => $sort_order,
                        'show_title' => $show_title,
                        'created_by' => Mava_Visitor::getUserId(),
                    );

                    $page_id = $pageModel->insert($dataInsert);

                    if ($page_id > 0) {

                        //check slug và insert vào bảng #__slug
                        $slugModel = $this->_getSlugModel();
                        $check_slug_exitst = $slugModel->checkSlugExist($slug);

                        if (!$check_slug_exitst) {
                            $slugDataInsert = array(
                                'slug' => $slug,
                                'app' => 'Page',
                                'controller' => 'Page',
                                'action' => 'View',
                                'params' => '{"page_id":' . $page_id . '}',
                                'key' => md5($slug)
                            );
                            $slugModel->insert($slugDataInsert);
                        }

                        Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/index'));
                    } else {
                        $error_message = __('could_not_add_static_page');
                    }
                } else {
                    $error_message = __('slug_x_exist', array('name' => $slug));
                }
            }
        }

        $pageGroupModel = $this->_getPageGroupModel();
        $list_page_group = $pageGroupModel->getAllPageGroup();

        return $this->responseView('Admin_View_Page_Add', array(
            'breadcrumbs' => $breadcrumbs,
            'error_message' => $error_message,
            'list_page_group' => $list_page_group,
            'postData' => $postData,
        ));
    }

    public function editAction()
    {
        $id = (int)Mava_Url::getParam('page_id');
        if ($id > 0) {
            $pageModel = $this->_getPageModel();
            $pageData = $pageModel->getPageById($id);
            if ($pageData > 0) {
                Mava_Application::set('seo/title', __('edit_static_page'));

                $breadcrumbs = array();
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/index/index'),
                    'text' => __('admin_page')
                );
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/page/index'),
                    'text' => __('static_page')
                );

                $breadcrumbs[] = array(
                    'url' => '',
                    'text' => __('edit_static_page')
                );

                $error_message = '';
                $postData = $pageData;

                if (Mava_Url::isPost()) {
                    $slug = Mava_Url::getParam('slug');
                    $group_id = Mava_Url::getParam('group_id');
                    $layout = Mava_Url::getParam('layout');
                    $publish_time = Mava_Url::getParam('publish_time');
                    $unpublish_time = Mava_Url::getParam('unpublish_time');
                    $sort_order = Mava_Url::getParam('sort_order');
                    $show_title = Mava_Url::getParam('show_title');

                    if ($slug == '') {
                        $error_message = __('slug_empty');
                    } else {
                        $slug = Mava_String::unsignString($slug, '-', true);
                        $check = false;
                        if ($slug != $pageData['slug']) {
                            $check = $pageModel->checkPageSlugExist($slug);
                        }

                        if (!$check) {
                            $dataUpdate = array(
                                'slug' => $slug,
                                'group_id' => $group_id,
                                'layout' => $layout,
                                'publish_time' => $publish_time,
                                'unpublish_time' => $unpublish_time,
                                'sort_order' => $sort_order,
                                'show_title' => $show_title,
                            );

                            $pageModel->update($dataUpdate, "`id` = '".(int)$id."' ");

                            //check slug và insert vào bảng #__slug
                            $slugModel = $this->_getSlugModel();
                            $check_slug_exitst = $slugModel->checkSlugExist($slug);

                            if (!$check_slug_exitst) {
                                $slugDataInsert = array(
                                    'slug' => $slug,
                                    'app' => 'Page',
                                    'controller' => 'Page',
                                    'action' => 'View',
                                    'params' => '{"page_id":' . $id . '}',
                                    'key' => md5($slug)
                                );
                                $slugModel->insert($slugDataInsert);
                            }
                            Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/index'));
                        } else {
                            $error_message = __('slug_x_exist', array('name' => $slug));
                        }
                    }
                }

                $pageGroupModel = $this->_getPageGroupModel();
                $list_page_group = $pageGroupModel->getAllPageGroup();

                return $this->responseView('Admin_View_Page_Edit', array(
                    'breadcrumbs' => $breadcrumbs,
                    'error_message' => $error_message,
                    'list_page_group' => $list_page_group,
                    'postData' => $postData,
                ));
            } else {
                Mava_Url::redirect(Mava_Url::buildLink('/admin/page/index'));
            }
        } else {
            Mava_Url::redirect(Mava_Url::buildLink('/admin/page/index'));
        }
    }

    public function deleteAction()
    {
        $page_id = (int)Mava_Url::getParam('page_id');
        if ($page_id > 0) {
            $pageModel = $this->_getPageModel();
            $page = $pageModel->getPageById($page_id);
            if ($page) {
                $pageDW = $this->_getPageWriter();
                $pageDW->setExistingData($page_id);
                $pageDW->delete();
            }
        }
        Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/index'));
    }

    public function add_contentAction()
    {
        $page_id = (int)Mava_Url::getParam('page_id');
        if ($page_id > 0) {
            $pageModel = $this->_getPageModel();
            $pageInfo = $pageModel->getPageById($page_id);
            if ($pageInfo) {
                Mava_Application::set('seo/title', __('add_page_content'));

                $breadcrumbs = array();
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/index/index'),
                    'text' => __('admin_page')
                );
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/page/index'),
                    'text' => __('static_page')
                );

                $breadcrumbs[] = array(
                    'url' => '',
                    'text' => __('add_page_content')
                );

                $error_message = '';
                //get list language

                $languageModel = $this->_getLanguageModel();
                $languages = Mava_Application::getOptions()->contentLanguages;
                if(is_array($languages) && count($languages) > 0) {
                    $list_language = $languageModel->getLanguageByCodes($languages);
                    //get page Data
                    $data = array();
                    $pageDataModel = $this->_getPageDataModel();
                    if (is_array($list_language) && count($list_language) > 0) {
                        foreach ($list_language as $lang) {
                            $pageData = $pageDataModel->getPageDataByLang($page_id, $lang['language_code']);
                            $data[$lang['language_id']] = $pageData;
                        }
                    }


                    if (Mava_Url::isPost()) {
                        if (is_array($list_language) && count($list_language) > 0) {
                            foreach ($list_language as $lang) {
                                //get data POST
                                $short_title = Mava_Url::getParam('short_title_' . $lang['language_id']);
                                $long_title = Mava_Url::getParam('long_title_' . $lang['language_id']);
                                $content_css = Mava_Url::getParam('content_css_' . $lang['language_id']);
                                $content_js = Mava_Url::getParam('content_js_' . $lang['language_id']);
                                $content_type = Mava_Url::getParam('content_type_' . $lang['language_id']);

                                if ($content_type == 'html') {
                                    $content_html = Mava_Url::getParam('content_html_' . $lang['language_id']);
                                } else {
                                    $content_html = Mava_Url::getParam('content_text_' . $lang['language_id']);
                                }

                                if (!1) {       //
                                    $error_message = __('short_title_empty');
                                } else {
                                    $dataUpdate = array(
                                        'page_id' => $page_id,
                                        'language_code' => $lang['language_code'],
                                        'short_title' => $short_title,
                                        'long_title' => $long_title,
                                        'content_css' => $content_css,
                                        'content_js' => $content_js,
                                        'content_html' => $content_html
                                    );
                                    $data[$lang['language_id']] = $dataUpdate;
                                    //check pagedata exist
                                    $check = $pageDataModel->checkPageDataExist($page_id, $lang['language_code']);
                                    if ($check) {
                                        $pageDataModel->updateData($dataUpdate, " `page_id`   = '" . (int)$page_id . "' AND `language_code` = '" . addslashes($lang['language_code']) . "' ");
                                    } else {
                                        $pageDataModel->insertData($dataUpdate);
                                    }
                                }
                            }
                        }
                        //Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/index'));
                    }
                    return $this->responseView('Admin_View_Page_AddContent', array(
                        'breadcrumbs' => $breadcrumbs,
                        'error_message' => $error_message,
                        'data' => $data,
                        'list_language' => $list_language,
                        'page_id' => $page_id,
                    ));
                }else{
                    Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/index'));
                }
            } else {
                Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/index'));
            }
        } else {
            Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/index'));
        }
    }

    public function groupAction()
    {
        Mava_Application::set('seo/title', __('page_group'));

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('page_group')
        );

        $pageGroupModel = $this->_getPageGroupModel();

        $groups = $pageGroupModel->getAllPageGroup();

        return $this->responseView('Admin_View_Page_Group', array(
            'groups' => $groups,
            'breadcrumbs' => $breadcrumbs
        ));
    }

    public function group_addAction()
    {
        Mava_Application::set('seo/title', __('add_page_group'));

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/page/group'),
            'text' => __('page_group')
        );

        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_page_group')
        );

        $error_message = '';

        if (Mava_Url::isPost()) {
            $title = Mava_Url::getParam('groupTitle');
            if ($title == '') {
                $error_message = __('group_title_empty');
            } else {
                $pageGroupDW = $this->_getPageGroupDataWriter();
                $pageGroupDW->bulkSet(array(
                    'title' => $title
                ));
                if ($pageGroupDW->save()) {
                    Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/group'));
                } else {
                    $error_message = __('could_not_add_page_group');
                }
            }
        }

        return $this->responseView('Admin_View_Page_GroupAdd', array(
            'breadcrumbs' => $breadcrumbs,
            'error_message' => $error_message
        ));
    }

    public function group_editAction()
    {
        Mava_Application::set('seo/title', __('edit_page_group'));

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/page/group'),
            'text' => __('page_group')
        );

        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('edit_group')
        );

        $error_message = '';
        $groupID = (int)Mava_Url::getParam('groupID');
        $groupModel = $this->_getPageGroupModel();
        if ($groupID > 0) {
            if ($group = $groupModel->getPageGroupById($groupID)) {
                if (Mava_Url::isPost()) {
                    $title = Mava_Url::getParam('groupTitle');
                    if ($title == '') {
                        $error_message = __('group_title_empty');
                    } else {
                        $userGroupDW = $this->_getPageGroupDataWriter();
                        $userGroupDW->setExistingData($group['id']);
                        $userGroupDW->bulkSet(array(
                            'title' => $title,
                        ));
                        if ($userGroupDW->save()) {
                            Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/group'));
                        } else {
                            $error_message = __('could_not_edit_page_group');
                        }
                    }
                }

                return $this->responseView('Admin_View_Page_GroupEdit', array(
                    'breadcrumbs' => $breadcrumbs,
                    'error_message' => $error_message,
                    'group' => $group,
                    'groupID' => $groupID
                ));
            } else {

                Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/group'));
            }
        } else {
            Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/group'));
        }

    }

    public function group_deleteAction()
    {
        $groupID = (int)Mava_Url::getParam('groupID');
        if ($groupID > 0) {
            $pageGroupModel = $this->_getPageGroupModel();
            $group = $pageGroupModel->getPageGroupById($groupID);
            if ($group) {
                $groupDW = $this->_getPageGroupDataWriter();
                $groupDW->setExistingData($groupID);
                $groupDW->delete();
            }
        }
        Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/group'));
    }

    public function slugAction()
    {
        Mava_Application::set('seo/title', __('slug'));

        $search_tearm = Mava_Url::getParam('q');
        $page = max((int)Mava_Url::getParam('page'), 1);
        //$limit = max(Mava_Application::get('options')->defaultAdminROP,50);
        $limit = 20;

        $skip = ($page - 1) * $limit;
        $pageOffset = max(Mava_Application::get('options')->defaultAdminPaginationOffset, 5);

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('slug')
        );

        $slugModel = $this->_getSlugModel();

        $list_slug = $slugModel->getListSlug($skip, $limit, $search_tearm);

        // check max page
        $maxPage = max(ceil($list_slug['total'] / $limit), 1);
        if ($page > $maxPage) {
            return $this->responseRedirect(Mava_Url::buildLink('admin/page/slug', array(
                'page' => $maxPage
            )));
        }

        return $this->responseView('Admin_View_Page_Slug', array(
            'search_tearm' => $search_tearm,
            'list_slug' => $list_slug['rows'],
            'breadcrumbs' => $breadcrumbs,
            'total' => $list_slug['total'],
            'total_page' => $maxPage,
            'skip' => $skip,
            'limit' => $limit,
            'page_offset' => $pageOffset,
            'page' => $page,
        ));
    }

    public function slug_deleteAction()
    {
        $slug_id = (int)Mava_Url::getParam('slug_id');
        if ($slug_id > 0) {
            $slugModel = $this->_getSlugModel();
            $slug = $slugModel->getSlugById($slug_id);
            if ($slug) {
                $slugDW = $this->_getSlugDataWriter();
                $slugDW->setExistingData($slug_id);
                $slugDW->delete();
            }
        }
        Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/slug'));
    }

    public function slug_addAction()
    {
        Mava_Application::set('seo/title', __('add_slug'));

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/page/slug'),
            'text' => __('slug')
        );

        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_slug')
        );

        $error_message = '';
        $postData = array();

        if (Mava_Url::isPost()) {
            $postData = $_POST;

            $slug = Mava_Url::getParam('slug');
            $app = Mava_Url::getParam('app');
            $controller = Mava_Url::getParam('controller');
            $action = Mava_Url::getParam('action');
            $params = Mava_Url::getParam('params');

            if ($slug == '') {
                $error_message = __('slug_empty');
            } else {
                $slug = Mava_String::unsignString($slug, '-');
                $slugModel = $this->_getSlugModel();
                $check = $slugModel->checkSlugExist($slug);

                if (!$check) {
                    $slugDW = $this->_getSlugDataWriter();
                    $key = md5($slug);
                    $slugDW->bulkSet(array(
                        'slug' => $slug,
                        'app' => $app,
                        'controller' => $controller,
                        'action' => $action,
                        'params' => $params,
                        'key' => $key
                    ));
                    if ($slugDW->save()) {
                        Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/slug'));
                    } else {
                        $error_message = __('could_not_add_slug');
                    }
                } else {
                    $error_message = __('slug_x_exist', array('name' => $slug));
                }
            }
        }

        return $this->responseView('Admin_View_Page_SlugAdd', array(
            'breadcrumbs' => $breadcrumbs,
            'error_message' => $error_message,
            'postData' => $postData
        ));
    }

    public function slug_editAction()
    {
        $id = (int)Mava_Url::getParam('slug_id');
        if ($id > 0) {
            $slugModel = $this->_getSlugModel();
            $slugData = $slugModel->getSlugById($id);
            if ($slugData) {
                Mava_Application::set('seo/title', __('edit_slug'));

                $breadcrumbs = array();
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/index/index'),
                    'text' => __('admin_page')
                );
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/page/slug'),
                    'text' => __('slug')
                );

                $breadcrumbs[] = array(
                    'url' => '',
                    'text' => __('edit_slug')
                );

                $error_message = '';
                $postData = array();

                if (Mava_Url::isPost()) {
                    $postData = $_POST;

                    $slug = Mava_Url::getParam('slug');
                    $app = Mava_Url::getParam('app');
                    $controller = Mava_Url::getParam('controller');
                    $action = Mava_Url::getParam('action');
                    $params = Mava_Url::getParam('params');

                    if ($slug == '') {
                        $error_message = __('slug_empty');
                    } else {
                        $slug = Mava_String::unsignString($slug, '-');
                        $slugModel = $this->_getSlugModel();
                        $check = false;
                        if ($slug != $slugData['slug']) {
                            $check = $slugModel->checkSlugExist($slug);
                        }

                        if (!$check) {
                            $slugDW = $this->_getSlugDataWriter();
                            $slugDW->setExistingData($id);
                            $key = md5($slug);
                            $slugDW->bulkSet(array(
                                'slug' => $slug,
                                'app' => $app,
                                'controller' => $controller,
                                'action' => $action,
                                'params' => $params,
                                'key' => $key
                            ));
                            if ($slugDW->save()) {
                                Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/slug'));
                            } else {
                                $error_message = __('could_not_add_slug');
                            }
                        } else {
                            $error_message = __('slug_x_exist', array('name' => $slug));
                        }
                    }
                }

                return $this->responseView('Admin_View_Page_SlugEdit', array(
                    'breadcrumbs' => $breadcrumbs,
                    'error_message' => $error_message,
                    'postData' => $postData,
                    'slugData' => $slugData,
                ));
            } else {
                Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/slug'));
            }
        } else {
            Mava_Url::redirect(Mava_Url::getPageLink('/admin/page/slug'));
        }
    }


    /**
     * @return Mava_Model_PageGroup
     */
    protected function _getPageGroupModel()
    {
        return $this->getModelFromCache('Mava_Model_PageGroup');
    }

    /**
     * @return Mava_DataWriter_PageGroup
     * @throws Mava_Exception
     */
    protected function _getPageGroupDataWriter()
    {
        return Mava_DataWriter::create('Mava_DataWriter_PageGroup');
    }

    /**
     * @return Mava_Model_Slug
     */
    protected function _getSlugModel()
    {
        return $this->getModelFromCache('Mava_Model_Slug');
    }

    /**
     * @return Mava_DataWriter_Slug
     * @throws Mava_Exception
     */
    protected function _getSlugDataWriter()
    {
        return Mava_DataWriter::create('Mava_DataWriter_Slug');
    }

    /**
     * @return Mava_Model_Page
     */
    protected function _getPageModel()
    {
        return $this->getModelFromCache('Mava_Model_Page');
    }

    /**
     * @return Mava_Model_PageData
     */
    protected function _getPageDataModel()
    {
        return $this->getModelFromCache('Mava_Model_PageData');
    }

    /**
     * @return Mava_Model_Language
     */
    protected function _getLanguageModel()
    {
        return $this->getModelFromCache('Mava_Model_Language');
    }

    /**
     * @return Mava_DataWriter_Page
     * @throws Mava_Exception
     */
    protected function _getPageWriter()
    {
        return Mava_DataWriter::create('Mava_DataWriter_Page');
    }

    /**
     * @return Mava_DataWriter_PageData
     * @throws Mava_Exception
     */
    protected function _getPageDataWriter()
    {
        return Mava_DataWriter::create('Mava_DataWriter_PageData');
    }
}