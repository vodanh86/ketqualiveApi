<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 6/19/14
 * Time: 10:15 AM
 * To change this template use File | Settings | File Templates.
 */
class Admin_Controller_AddOns extends Mava_AdminController {
    public function addAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        // seo
        Mava_Application::set('seo/title',__('add_addon'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/add-ons/index'),
            'text' => __('admin_addons')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_addon')
        );
        return $this->responseView('Admin_View_Addon_Add',array(
            'breadcrumbs' => $breadcrumbs
        ));
    }

    public function indexAction(){
        $page = max((int)Mava_Url::getParam('page'),1);
        $limit = max(Mava_Application::get('options')->defaultAdminROP,50);
        $skip = ($page-1)*$limit;
        $pageOffset = max(Mava_Application::get('options')->defaultAdminPaginationOffset,5);
        $addOnModel = $this->_getAddOnModel();
        $addOn = $addOnModel->getAllAddon();

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('admin_addons')
        );

        // check max page
        $maxPage = max(ceil(sizeof($addOn)/$limit),1);
        if($page > $maxPage){
            return $this->responseRedirect(Mava_Url::buildLink('admin/add-ons/index',array(
                'page' => $maxPage
            )));
        }

        // seo
        Mava_Application::set('seo/title',__('admin_addons'));

        return $this->responseView('Admin_View_Addon_List',array(
            'breadcrumbs' => $breadcrumbs,
            'addons' => $addOn,
            'total' => sizeof($addOn),
            'total_page' => $maxPage,
            'skip' => $skip,
            'limit' => $limit,
            'page_offset' => $pageOffset,
            'page' => $page
        ));
    }

    /**
     * @return Mava_Model_Addon
     */
    protected function _getAddOnModel(){
        return $this->getModelFromCache('Mava_Model_Addon');
    }
}