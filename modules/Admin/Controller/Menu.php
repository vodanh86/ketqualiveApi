<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/25/16
 * @Time: 12:17 AM
 */
class Admin_Controller_Menu extends Mava_AdminController {

    public function indexAction(){
        Mava_Application::set('seo/title', __('menu'));
        $menuModel = $this->_getMenuModel();
        $menus = $menuModel->getAll();
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );$breadcrumbs[] = array(
            'url' => '',
            'text' => __('admin_menu')
        );
        return $this->responseView('Admin_View_Menu_List', array(
            'menus' => $menus,
            'breadcrumbs' => $breadcrumbs
        ));
    }


    public function addAction(){
        Mava_Application::set('seo/title', __('add_menu'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/menu/index'),
            'text' => __('admin_menu')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_menu')
        );
        $menuModel = $this->_getMenuModel();
        $viewParams = array(
            'breadcrumbs' => $breadcrumbs,
            'itemSortOrder' => $menuModel->getMaxSortOrder()+1
        );
        $error_message = '';
        if(Mava_Url::isPost()){
            $itemTitle = Mava_Url::getParam('itemTitle');
            $itemLink = Mava_Url::getParam('itemLink');
            $itemTarget = Mava_Url::getParam('itemTarget');
            $itemTextColor = Mava_Url::getParam('itemTextColor');
            $itemSortOrder = (int)Mava_Url::getParam('itemSortOrder');
            $viewParams['itemTitle'] = $itemTitle;
            $viewParams['itemLink'] = $itemLink;
            $viewParams['itemTarget'] = $itemTarget;
            $viewParams['itemTextColor'] = $itemTextColor;
            $viewParams['itemSortOrder'] = $itemSortOrder;
            if($itemTitle == ""){
                $error_message = __('menu_title_empty');
            }else{
                $menuDW = $this->_getMenuDataWriter();
                $menuDW->bulkSet(array(
                    'title' => $itemTitle,
                    'link' => $itemLink,
                    'open_in_new_tab' => ($itemTarget=='blank'?'yes':'no'),
                    'text_color' => $itemTextColor,
                    'sort_order' => $itemSortOrder
                ));
                if($menuDW->save()){
                    Mava_Url::redirect(Mava_Url::getPageLink('admin/menu/index', array('added' => 1)));
                }else{
                    dprint($menuDW->getErrors());
                    $error_message = __('can_not_add_menu');
                }
            }
        }
        $viewParams['error_message'] = $error_message;
        return $this->responseView('Admin_View_Menu_Add', $viewParams);
    }

    public function editAction(){
        Mava_Application::set('seo/title', __('edit_menu'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/menu/index'),
            'text' => __('admin_menu')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('edit_menu')
        );
        $id = (int)Mava_Url::getParam('id');
        $menuModel = $this->_getMenuModel();
        if($id > 0 && $menu = $menuModel->getById($id)){
            $viewParams = array(
                'breadcrumbs' => $breadcrumbs,
                'itemId' => $menu['id'],
                'itemTitle' => $menu['title'],
                'itemLink' => $menu['link'],
                'itemTarget' =>  ($menu['open_in_new_tab']=='yes'?'blank':''),
                'itemTextColor' => $menu['text_color'],
                'itemSortOrder' => $menu['sort_order']
            );
            $error_message = '';
            if(Mava_Url::isPost()){
                $itemTitle = Mava_Url::getParam('itemTitle');
                $itemLink = Mava_Url::getParam('itemLink');
                $itemTarget = Mava_Url::getParam('itemTarget');
                $itemTextColor = Mava_Url::getParam('itemTextColor');
                $itemSortOrder = (int)Mava_Url::getParam('itemSortOrder');
                $viewParams['itemTitle'] = $itemTitle;
                $viewParams['itemLink'] = $itemLink;
                $viewParams['itemTarget'] = $itemTarget;
                $viewParams['itemTextColor'] = $itemTextColor;
                $viewParams['itemSortOrder'] = $itemSortOrder;
                if($itemTitle == ""){
                    $error_message = __('menu_title_empty');
                }else{
                    $menuDW = $this->_getMenuDataWriter();
                    $menuDW->setExistingData($id);
                    $menuDW->bulkSet(array(
                        'title' => $itemTitle,
                        'link' => $itemLink,
                        'open_in_new_tab' => ($itemTarget=='blank'?'yes':'no'),
                        'text_color' => $itemTextColor,
                        'sort_order' => $itemSortOrder
                    ));
                    if($menuDW->save()){
                        Mava_Url::redirect(Mava_Url::getPageLink('admin/menu/index', array('updated' => 1)));
                    }else{
                        $error_message = __('can_not_edit_menu');
                    }
                }
            }
            $viewParams['error_message'] = $error_message;
            return $this->responseView('Admin_View_Menu_Edit', $viewParams);
        }else{
            return $this->responseError(__('menu_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    public function deleteAction(){
        $id = (int)Mava_Url::getParam('id');
        $menuModel = $this->_getMenuModel();
        if($id > 0 && $menu = $menuModel->getById($id)){
            $menuDW = $this->_getMenuDataWriter();
            $menuDW->setExistingData($id);
            if($menuDW->delete()){
                return $this->responseJson(array(
                    'status' => 1,
                    'message' => __('menu_delete_success')
                ));
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('can_not_delete_menu')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('menu_not_found')
            ));
        }
    }

    /**
     * @return Megabook_Model_Menu
     */
    protected function _getMenuModel(){
        return $this->getModelFromCache('Megabook_Model_Menu');
    }

    /**
     * @return Megabook_DataWriter_Menu
     * @throws Mava_Exception
     */
    protected function _getMenuDataWriter()
    {
        return Mava_DataWriter::create('Megabook_DataWriter_Menu');
    }
}