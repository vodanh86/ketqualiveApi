<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 10/22/15
 * @Time: 2:09 PM
 */
class Admin_Controller_Permission extends Mava_AdminController {

    public function group_deleteAction(){
        $groupID = (int)Mava_Url::getParam('groupID');
        if($groupID > 0){
            $permissionModel = $this->_getPermissionModel();
            $group = $permissionModel->getPermissionGroupById($groupID);
            if($group){
                $permissionGroupDW = $this->_getPermissionGroupDataWriter();
                $permissionGroupDW->setExistingData($groupID);
                if($permissionGroupDW->delete()){
                    $permissionModel->deletePermissionInGroup($groupID);
                    return $this->responseJson(array(
                        'status' => 1,
                        'message' => __('permission_group_deleted')
                    ));
                }else{
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('can_not_delete_permission_group')
                    ));
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('permission_group_not_found')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('permission_group_not_found')
            ));
        }
    }

    public function group_editAction(){
        $groupID = (int)Mava_Url::getParam('groupID');
        $error_message = '';
        Mava_Application::set('seo/title', __('edit_permission_group'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/permission/index'),
            'text' => __('admin_permission')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('edit_permission_group')
        );
        $permissionModel = $this->_getPermissionModel();
        $group = array();
        if($groupID > 0){
            $group = $permissionModel->getPermissionGroupById($groupID);
            if($group){
                if(Mava_Url::isPost()){
                    $title = Mava_Url::getParam('groupTitle');
                    $sortOrder = (int)Mava_Url::getParam('groupSortOrder');
                    if($title == ''){
                        $error_message = __('group_title_empty');
                    }else{
                        $permissionGroupDW = $this->_getPermissionGroupDataWriter();
                        $permissionGroupDW->setExistingData($group['group_id']);
                        $permissionGroupDW->bulkSet(array(
                            'title' => $title,
                            'sort_order' => $sortOrder
                        ));
                        if($permissionGroupDW->save()){
                            Mava_Url::redirect(Mava_Url::getPageLink('admin/permission/index', array('group_edited' => 1)));
                        }else{
                            $error_message = __('could_not_edit_permission_group');
                        }
                    }
                }
            }else{
                return $this->responseError(__('permission_group_not_found'), Mava_Error::NOT_FOUND);
            }
        }else{
            return $this->responseError(__('permission_group_not_found'), Mava_Error::NOT_FOUND);
        }
        return $this->responseView('Admin_View_Permission_Group_Edit', array(
            'breadcrumbs' => $breadcrumbs,
            'error_message' => $error_message,
            'group' => $group
        ));
    }

    public function group_addAction(){
        Mava_Application::set('seo/title',__('add_permission_group'));

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/permission/index'),
            'text' => __('admin_permission')
        );

        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_permission_group')
        );

        $error_message = '';

        if(Mava_Url::isPost()){
            $title = Mava_Url::getParam('groupTitle');
            $order = Mava_Url::getParam('groupSortOrder');
            if($title == ''){
                $error_message = __('group_title_empty');
            }else{
                $permissionGroupDW = $this->_getPermissionGroupDataWriter();
                $permissionGroupDW->bulkSet(array(
                    'title' => $title,
                    'sort_order' => $order
                ));
                if($permissionGroupDW->save()){
                    Mava_Url::redirect(Mava_Url::getPageLink('admin/permission/index', array('group_added' => 1)));
                }else{
                    $error_message = __('could_not_add_permission_group');
                }
            }
        }

        return $this->responseView('Admin_View_Permission_Group_Add',array(
            'breadcrumbs' => $breadcrumbs,
            'error_message' => $error_message
        ));
    }

    public function deleteAction(){
        $permissionID = (int)Mava_Url::getParam('permissionID');
        if($permissionID > 0){
            $permissionModel = $this->_getPermissionModel();
            $permission = $permissionModel->getPermissionById($permissionID);
            if($permission){
                $permissionDW = $this->_getPermissionDataWriter();
                $permissionDW->setExistingData($permissionID);
                if($permissionDW->delete()){
                    $permissionModel->deletePermissionRelation($permissionID);
                    return $this->responseJson(array(
                        'status' => 1,
                        'message' => __('permission_deleted')
                    ));
                }else{
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('can_not_delete_permission')
                    ));
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('permission_not_found')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('permission_not_found')
            ));
        }
    }

    public function editAction(){
        $permissionID = (int)Mava_Url::getParam('permissionID');
        $error_message = '';
        Mava_Application::set('seo/title', __('edit_permission'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/permission/index'),
            'text' => __('admin_permission')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('edit_permission')
        );
        $permissionModel = $this->_getPermissionModel();
        $permission = array();
        if($permissionID > 0){
            $permission = $permissionModel->getPermissionById($permissionID);
            if($permission){
                $permissionRelation = $permissionModel->getPermissionRelation($permission['perm_id']);
                if($permissionRelation){
                    $permission['group_id'] = $permissionRelation['group_id'];
                    $permission['sort_order'] = $permissionRelation['sort_order'];
                }
                if(Mava_Url::isPost()){
                    $title = Mava_Url::getParam('permissionTitle');
                    $key = Mava_Url::getParam('permissionKey');
                    $groupID = (int)Mava_Url::getParam('permissionGroup');
                    $sortOrder = (int)Mava_Url::getParam('permissionSortOrder');
                    if($title == ''){
                        $error_message = __('permission_title_empty');
                    }else if($key == ''){
                        $error_message = __('permission_key_empty');
                    }else if($key != $permission['perm_key'] && $permissionModel->permissionKeyExisted($key)){
                        $error_message = __('permission_key_existed');
                    }else if($groupID == 0){
                        $error_message = __('choose_permission_group');
                    }else if(!$group = $permissionModel->getPermissionGroupById($groupID)){
                        $error_message = __('permission_group_not_found');
                    }else{
                        $permissionDW = $this->_getPermissionDataWriter();
                        $permissionDW->setExistingData($permission['perm_id']);
                        $permissionDW->bulkSet(array(
                            'title' => $title,
                            'perm_key' => $key
                        ));
                        if($permissionDW->save()){
                            if($groupID != $permission['group_id']){
                                $permissionModel->updatePermissionGroup($groupID, $permissionID);
                            }
                            if($sortOrder != $permission['sort_order']){
                                $permissionModel->updatePermissionSortOrder($groupID, $permissionID, $sortOrder);
                            }
                            Mava_Url::redirect(Mava_Url::getPageLink('admin/permission/index', array('edited' => 1)));
                        }else{
                            $error_message = __('could_not_edit_permission');
                        }
                    }
                }
            }else{
                return $this->responseError(__('permission_not_found'), Mava_Error::NOT_FOUND);
            }
        }else{
            return $this->responseError(__('permission_not_found'), Mava_Error::NOT_FOUND);
        }
        $permissionGroup = $permissionModel->getAllPermissionGroup();
        return $this->responseView('Admin_View_Permission_Edit', array(
            'breadcrumbs' => $breadcrumbs,
            'error_message' => $error_message,
            'permissionGroup' => $permissionGroup,
            'permission' => $permission
        ));
    }

    public function addAction(){
        Mava_Application::set('seo/title',__('add_permission'));

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/permission/index'),
            'text' => __('admin_permission')
        );

        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_permission')
        );

        $error_message = '';

        $permissionModel = $this->_getPermissionModel();
        if(Mava_Url::isPost()){
            $title = Mava_Url::getParam('permissionTitle');
            $key = Mava_Url::getParam('permissionKey');
            $groupID = Mava_Url::getParam('permissionGroup');
            $order = Mava_Url::getParam('permissionSortOrder');
            if($title == ''){
                $error_message = __('permission_title_empty');
            }else if($key == ''){
                $error_message = __('permission_key_empty');
            }else if($permissionModel->permissionKeyExisted($key)){
                $error_message = __('permission_key_existed');
            }else if($groupID == 0){
                $error_message = __('choose_permission_group');
            }else if(!$group = $permissionModel->getPermissionGroupById($groupID)){
                $error_message = __('permission_group_not_found');
            }else{
                $permissionDW = $this->_getPermissionDataWriter();
                $permissionDW->bulkSet(array(
                    'title' => $title,
                    'perm_key' => $key
                ));
                if($permissionDW->save()){
                    $permissionModel->addPermissionRelation($group['group_id'], $permissionDW->get('perm_id'), $order);
                    Mava_Url::redirect(Mava_Url::getPageLink('admin/permission/index', array('added' => 1)));
                }else{
                    $error_message = __('could_not_add_permission');
                }
            }
        }

        $permissionGroup = $permissionModel->getAllPermissionGroup();
        return $this->responseView('Admin_View_Permission_Add',array(
            'breadcrumbs' => $breadcrumbs,
            'error_message' => $error_message,
            'permissionGroup' => $permissionGroup
        ));
    }

    public function indexAction(){
        Mava_Application::set('seo/title',__('admin_permission'));

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('admin_permission')
        );

        $permissionModel = $this->_getPermissionModel();

        $groups = $permissionModel->getAllPermissionGroup();
        $permissionGroups = array();
        if(is_array($groups) && count($groups) > 0){
            foreach($groups as $item){
                $item['permissions'] = $permissionModel->getPermissionInGroup($item['group_id']);
                $permissionGroups[] = $item;
            }
        }

        return $this->responseView('Admin_View_Permission_List',array(
            'permissionGroups' => $permissionGroups,
            'breadcrumbs' => $breadcrumbs
        ));
    }

    /**
     * @return Mava_Model_Permission
     */
    protected function _getPermissionModel(){
        return $this->getModelFromCache("Mava_Model_Permission");
    }

    /**
     * @return Mava_DataWriter_Permission
     */
    protected function _getPermissionDataWriter(){
        return Mava_DataWriter::create("Mava_DataWriter_Permission");
    }

    /**
     * @return Mava_DataWriter_PermissionGroup
     */
    protected function _getPermissionGroupDataWriter(){
        return Mava_DataWriter::create("Mava_DataWriter_PermissionGroup");
    }


}