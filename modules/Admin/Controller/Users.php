<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 5/7/15
 * @Time: 4:28 PM
 */
class Admin_Controller_Users extends Mava_AdminController {
    public function change_group_permissionAction(){
        $userGroupID = (int)Mava_Url::getParam('userGroupID');
        $permID = (int)Mava_Url::getParam('permID');
        $permValue = Mava_Url::getParam('permValue');
        if($userGroupID > 0 && $permID > 0 && in_array($permValue, array('allowed','denied'))){
            $permissionModel = $this->_getPermissionModel();
            $permissionModel->changeUserGroupPermission($userGroupID, $permID, $permValue);
            return $this->responseJson(array(
                'status' => 1,
                'message' => __('saved')
            ));
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('invalid_request')
            ));
        }
    }

    public function change_user_permissionAction(){
        $userID = (int)Mava_Url::getParam('userID');
        $permID = (int)Mava_Url::getParam('permID');
        $permValue = Mava_Url::getParam('permValue');
        if($userID > 0 && $permID > 0 && in_array($permValue, array('inherit','allowed','denied'))){
            $permissionModel = $this->_getPermissionModel();
            $permissionModel->changeUserPermission($userID, $permID, $permValue);
            return $this->responseJson(array(
                'status' => 1,
                'message' => __('saved')
            ));
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('invalid_request')
            ));
        }
    }

    public function group_permissionAction(){
        $groupID = (int)Mava_Url::getParam('groupID');
        Mava_Application::set('seo/title',__('admin_permission'));

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/users/group'),
            'text' => __('user_group')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('user_permission')
        );
        if($groupID > 0){
            $groupModel = $this->_getUserGroupModel();
            if($group = $groupModel->getUserGroupById($groupID)){
                $permissionModel = $this->_getPermissionModel();
                $permGroups = $permissionModel->getAllPermissionGroup();
                $permissionGroups = array();
                $groupPermission = array();
                if(is_array($permGroups) && count($permGroups) > 0){
                    foreach($permGroups as $item){
                        $item['permissions'] = $permissionModel->getPermissionInGroup($item['group_id']);
                        $permissionGroups[] = $item;
                    }
                }

                $groupPerm = $permissionModel->getUserGroupPermission($groupID);

                if(is_array($groupPerm) && count($groupPerm) > 0){
                    foreach($groupPerm as $item){
                        $groupPermission[$item['perm_id']] = $item['perm_value'];
                    }
                }
                return $this->responseView('Admin_View_User_Group_Permission',array(
                    'permissionGroups' => $permissionGroups,
                    'groupPermission' => $groupPermission,
                    'user_group' => $group,
                    'breadcrumbs' => $breadcrumbs
                ));
            }else{
                return $this->responseError(__('user_group_not_found'), Mava_Error::NOT_FOUND);
            }
        }else{
            return $this->responseError(__('user_group_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    public function group_deleteAction(){
        $groupID = (int)Mava_Url::getParam('groupID');
        if($groupID > 0){
            $groupModel = $this->_getUserGroupModel();
            $group = $groupModel->getUserGroupById($groupID);
            if($group){
                if($groupModel->hasMember($groupID) == true){
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('delete_user_first')
                    ));
                }else{
                    $groupDW = $this->_getUserGroupDataWriter();
                    $groupDW->setExistingData($groupID);
                    if($groupDW->delete()){
                        $permissionModel = $this->_getPermissionModel();
                        $permissionModel->deleteUserGroupPermission($groupID);
                        return $this->responseJson(array(
                            'status' => 1,
                            'message' => __('user_group_deleted')
                        ));
                    }else{
                        return $this->responseJson(array(
                            'status' => -1,
                            'message' => __('can_not_delete_user_group')
                        ));
                    }
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('user_group_not_found')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('user_group_not_found')
            ));
        }
    }

    public function group_editAction(){
        Mava_Application::set('seo/title',__('user_group'));

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/users/group'),
            'text' => __('user_group')
        );

        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('edit_group')
        );

        $error_message = '';
        $groupID = (int)Mava_Url::getParam('groupID');
        $groupModel = $this->_getUserGroupModel();
        if($groupID > 0){
            if($group = $groupModel->getUserGroupById($groupID)){
                if(Mava_Url::isPost()){
                    $title = Mava_Url::getParam('groupTitle');
                    $order = Mava_Url::getParam('groupSortOrder');
                    if($title == ''){
                        $error_message = __('group_title_empty');
                    }else{
                        $userGroupDW = $this->_getUserGroupDataWriter();
                        $userGroupDW->setExistingData($group['group_id']);
                        $userGroupDW->bulkSet(array(
                            'group_title' => $title,
                            'sort_order' => $order
                        ));
                        if($userGroupDW->save()){
                            Mava_Url::redirect(Mava_Url::getPageLink('/admin/users/group', array('edited' => 1)));
                        }else{
                            $error_message = __('could_not_edit_user_group');
                        }
                    }
                }

                return $this->responseView('Admin_View_User_Group_Edit',array(
                    'breadcrumbs' => $breadcrumbs,
                    'error_message' => $error_message,
                    'group' => $group
                ));
            }else{
                return $this->responseError(__('user_group_not_found'), Mava_Error::NOT_FOUND);
            }
        }else{
            return $this->responseError(__('user_group_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    public function group_addAction(){
        Mava_Application::set('seo/title',__('user_group'));

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/users/group'),
            'text' => __('user_group')
        );

        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_group')
        );

        $error_message = '';

        if(Mava_Url::isPost()){
            $title = Mava_Url::getParam('groupTitle');
            $order = Mava_Url::getParam('groupSortOrder');
            if($title == ''){
                $error_message = __('group_title_empty');
            }else{
                $userGroupDW = $this->_getUserGroupDataWriter();
                $userGroupDW->bulkSet(array(
                    'group_title' => $title,
                    'sort_order' => $order
                ));
                if($userGroupDW->save()){
                    Mava_Url::redirect(Mava_Url::getPageLink('/admin/users/group', array('added' => 1)));
                }else{
                    $error_message = __('could_not_add_user_group');
                }
            }
        }

        return $this->responseView('Admin_View_User_Group_Add',array(
            'breadcrumbs' => $breadcrumbs,
            'error_message' => $error_message
        ));
    }

    public function groupAction(){
        Mava_Application::set('seo/title',__('user_group'));

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('user_group')
        );

        $userGroupModel = $this->_getUserGroupModel();

        $groups = $userGroupModel->getAllUserGroup();

        return $this->responseView('Admin_View_User_Group_List',array(
            'groups' => $groups,
            'breadcrumbs' => $breadcrumbs
        ));
    }

    public function activeAction(){
        $userId = (int)Mava_Url::getParam('userId');
        if($userId > 0){
            $userModel = $this->_getUserModel();
            $user = $userModel->getUserById($userId);
            if($user){
                $userDW = $this->_getUserDataWriter();
                $userDW->setExistingData($userId);
                $userDW->set('is_active', 1);
                if($userDW->save()){
                    return $this->responseJson(array(
                        'status' => 1,
                        'message' => __('user_updated')
                    ));
                }else{
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('user_not_found')
                    ));
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('user_not_found')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('user_not_found')
            ));
        }
    }

    public function deactiveAction(){
        $userId = (int)Mava_Url::getParam('userId');
        if($userId > 0){
            $userModel = $this->_getUserModel();
            $user = $userModel->getUserById($userId);
            if($user){
                $userDW = $this->_getUserDataWriter();
                $userDW->setExistingData($userId);
                $userDW->set('is_active', 0);
                if($userDW->save()){
                    return $this->responseJson(array(
                        'status' => 1,
                        'message' => __('user_updated')
                    ));
                }else{
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('user_not_found')
                    ));
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('user_not_found')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('user_not_found')
            ));
        }
    }

    public function deleteAction(){
        $userID = (int)Mava_Url::getParam('userID');
        if($userID > 0){
            $userModel = $this->_getUserModel();
            $user = $userModel->getUserById($userID, false);
            if($user){
                $check = $userModel->delete($userID);
                if($check){
                    return $this->responseJson(array(
                        'status' => 1,
                        'message' => __('user_deleted')
                    ));
                }else{
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('can_not_delete_user')
                    ));
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('user_not_found')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('user_not_found')
            ));
        }
    }

    public function editAction(){
        Mava_Application::set('seo/title',__('edit_user'));
        $userID = (int)Mava_Url::getParam('userID');
        $userModel = $this->_getUserModel();
        if($userID > 0 && $user = $userModel->getUserById($userID, false)){
            $breadcrumbs = array();
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('admin/index/index'),
                'text' => __('admin_page')
            );
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('admin/users/index'),
                'text' => __('admin_users')
            );
            $breadcrumbs[] = array(
                'url' => '',
                'text' => __('edit_user')
            );

            $error_message = '';

            $userGroupModel = $this->_getUserGroupModel();
            $userGroups = $userModel->getUserGroupList();
            $cities = get_all_city();
            if(Mava_Url::isPost()){
                $updateData = array();
                $userData = array(
                    'email' => Mava_Url::getParam('userEmail'),
                    'phone' => Mava_Url::getParam('userPhone'),
                    'gender' => Mava_Url::getParam('userGender'),
                    'is_active' => (int)Mava_Url::getParam('userActivated'),
                    'custom_title' => Mava_Url::getParam('userTitle'),
                    'password' => Mava_Url::getParam('userPassword'),
                    'repassword' => Mava_Url::getParam('userRePassword'),
                    'user_group_id' => (int)Mava_Url::getParam('userGroup'),
                    'city_id' => (int)Mava_Url::getParam('userCity'),
                    'birthday' => Mava_Url::getParam('userBirthday')
                );

                if($userData['user_group_id'] != $user['user_group_id']){
                    if(!$group = $userGroupModel->getUserGroupById($userData['user_group_id'])){
                        $updateData['user_group_id'] = Mava_Application::getOptions()->defaultUserGroupID;
                    }else{
                        $updateData['user_group_id'] = $userData['user_group_id'];
                    }
                }
                if($userData['custom_title'] != $user['custom_title']){
                    if($userData['custom_title'] == ''){
                        $updateData['custom_title'] = __('unnamed');
                    }else{
                        $updateData['custom_title'] = $userData['custom_title'];
                    }
                }
                if($userData['email'] != $user['email']){
                    if($userModel->checkEmailExist($userData['email'], $user['user_id'])){
                        $error_message = __('email_existed');
                    }else{
                        $updateData['email'] = $userData['email'];
                    }
                }
                if($userData['phone'] != $user['phone']){
                    if($userModel->checkPhoneExist($userData['phone'], $user['user_id'])){
                        $error_message = __('phone_existed');
                    }else{
                        $updateData['phone'] = $userData['phone'];
                    }
                }
                if($userData['gender'] != $user['gender']){
                    if(!in_array($userData['gender'], array('male','female'))){
                        $updateData['gender'] = '';
                    }else{
                        $updateData['gender'] = $userData['gender'];
                    }
                }

                if($userData['is_active'] != $user['is_active']){
                    if(!in_array($userData['is_active'], array(0,1))){
                        $updateData['is_active'] = 0;
                    }else{
                        $updateData['is_active'] = $userData['is_active'];
                    }
                }

                if($userData['city_id'] != $user['city_id']){
                    $updateData['city_id'] = $userData['city_id'];
                }

                if(date_to_time($userData['birthday']) != $user['birthday']) {
                    if ($userData['birthday'] != "") {
                        $updateData['birthday'] = date_to_time($userData['birthday']);
                    }else{
                         $updateData['birthday'] = 0;
                    }
                }

                if($userData['password'] != ""){
                    if(
                        strlen($userData['password']) < Mava_Application::get('config/passwordMinLength') ||
                        strlen($userData['password']) > Mava_Application::get('config/passwordMaxLength')
                    ){
                        $error_message = __('password_invalid');
                    }else if($userData['password'] != $userData['repassword']){
                        $error_message = __('repassword_invalid');
                    }else{
                        $updateData['password'] = $userModel->generalPassword($userData['password'], $user['unique_token']);
                    }
                }

                if($error_message == ""){
                    $check = $userModel->update($userID, $updateData);

                    if($check){
                        Mava_Url::redirect(Mava_Url::getPageLink('admin/users/index', array('edited' => 1)));
                    }else{
                        $error_message = __('can_not_add_user');
                    }
                }
            }
            return $this->responseView('Admin_View_User_Edit', array(
                'breadcrumbs' => $breadcrumbs,
                'userGroup' => $userGroups,
                'user' => $user,
                'error_message' => $error_message,
                'cities' => $cities
            ));
        }else{
            return $this->responseError(__('user_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    public function addAction(){
        Mava_Application::set('seo/title',__('add_user'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/users/index'),
            'text' => __('admin_users')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_user')
        );

        $error_message = '';

        $userModel = $this->_getUserModel();
        $userGroupModel = $this->_getUserGroupModel();
        $userGroups = $userModel->getUserGroupList();
        $cities = get_all_city();
        $userData = array();
        if(Mava_Url::isPost()){
            $userData = array(
                'email' => Mava_Url::getParam('userEmail'),
                'phone' => Mava_Url::getParam('userPhone'),
                'gender' => Mava_Url::getParam('userGender'),
                'is_active' => (int)Mava_Url::getParam('userActivated'),
                'custom_title' => Mava_Url::getParam('userTitle'),
                'password' => Mava_Url::getParam('userPassword'),
                'repassword' => Mava_Url::getParam('userRePassword'),
                'user_group_id' => (int)Mava_Url::getParam('userGroup'),
                'city_id' => (int)Mava_Url::getParam('userCity'),
                'birthday' => Mava_Url::getParam('userBirthday'),
                'language_id'     => Mava_Application::get('config/defaultLanguage'),
                'timezone'     => Mava_Application::get('config/defaultTimeZone'),
                'active_code' => '',
            );

            $validate = $this->_validateRegister($userData['password'], $userData['repassword'], $userData['email'], $userData['phone']);
            if($validate['status'] == 1){
                if(!$group = $userGroupModel->getUserGroupById($userData['user_group_id'])){
                    $userData['user_group_id'] = Mava_Application::getOptions()->defaultUserGroupID;
                }
                if($userData['custom_title'] == ''){
                    $userData['custom_title'] = __('unnamed');
                }
                if(!in_array($userData['gender'], array('male','female'))){
                    $userData['gender'] = '';
                }

                if(!in_array($userData['is_active'], array(0,1))){
                    $userData['is_active'] = 0;
                }

                if($userData['birthday'] != ""){
                    $userData['birthday'] = date_to_time($userData['birthday']);
                }

                $userID = $userModel->insert($userData);

                if($userID > 0){
                    Mava_Url::redirect(Mava_Url::getPageLink('admin/users/index', array('added' => 1)));
                }else{
                    $error_message = __('can_not_add_user');
                }
            }else{
                $error_message = $validate['message'];
            }
        }
        return $this->responseView('Admin_View_User_Add', array(
            'breadcrumbs' => $breadcrumbs,
            'userGroup' => $userGroups,
            'user' => $userData,
            'error_message' => $error_message,
            'cities' => $cities
        ));
    }

    public function _validateRegister($password,$repassword,$email, $phone){
        if(
            strlen($password) < Mava_Application::get('config/passwordMinLength') ||
            strlen($password) > Mava_Application::get('config/passwordMaxLength')
        ){
            return array(
                'status' => -1,
                'message' => __('password_invalid')
            );
        }else if($password != $repassword){
            return array(
                'status' => -1,
                'message' => __('repassword_invalid')
            );
        }else if(!Mava_String::isEmail($email)){
            return array(
                'status' => -1,
                'message' => __('email_invalid')
            );
        }else if(Mava_Model_User::checkEmailExist($email)){
            return array(
                'status' => -1,
                'message' => __('email_existed')
            );
        }else if($phone != "" && !Mava_String::isPhoneNumber($phone)){
            return array(
                'status' => -1,
                'message' => __('phone_invalid')
            );
        }else if($phone != "" && Mava_Model_User::checkPhoneExist($phone)){
            return array(
                'status' => -1,
                'message' => __('phone_existed')
            );
        }else{
            return array(
                'status' => 1,
                'message'=> __('validated')
            );
        }
    }

    public function detailAction(){
        Mava_Application::set('seo/title',__('detail_user'));
        $userId = (int)Mava_Url::getParam('userID');
        if($userId > 0){
            $userModel = $this->_getUserModel();
            $user = $userModel->getUserById($userId, false);
            if($user) {
                $breadcrumbs = array();
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/index/index'),
                    'text' => __('admin_page')
                );
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/users/index'),
                    'text' => __('admin_users')
                );
                $breadcrumbs[] = array(
                    'url' => '',
                    'text' => __('detail_user')
                );

                $userGroup = $userModel->getUserGroupById($user['user_group_id']);
                $city_title = get_city_title($user['city_id']);

                $permissionModel = $this->_getPermissionModel();
                $permGroups = $permissionModel->getAllPermissionGroup();
                $permissionGroups = array();
                if(is_array($permGroups) && count($permGroups) > 0){
                    foreach($permGroups as $item){
                        $item['permissions'] = $permissionModel->getPermissionInGroup($item['group_id']);
                        $permissionGroups[] = $item;
                    }
                }

                $userGroupPermission = $permissionModel->getUserGroupPermission($user['user_group_id']);
                $userGroup['permissions'] = array();
                if(is_array($userGroupPermission) && count($userGroupPermission) > 0){
                    foreach($userGroupPermission as $item){
                        $userGroup['permissions'][$item['perm_id']] = $item['perm_value'];
                    }
                }
                $userPermissions = $permissionModel->getUserPermission($userId);
                $user['permissions'] = array();
                if(is_array($userPermissions) && count($userPermissions) > 0){
                    foreach($userPermissions as $item){
                        $user['permissions'][$item['perm_id']] = $item['perm_value'];
                    }
                }

                return $this->responseView('Admin_View_User_Detail', array(
                    'breadcrumbs' => $breadcrumbs,
                    'permissionGroups' => $permissionGroups,
                    'userGroup' => $userGroup,
                    'cityTitle' => $city_title,
                    'user' => $user
                ));
            }else{
                return $this->responseError(__('user_not_found'), Mava_Error::NOT_FOUND);
            }
        }else{
            return $this->responseError(__('user_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    public function indexAction(){
        Mava_Application::set('seo/title',__('admin_users'));
        $page = max((int)Mava_Url::getParam('page'), 1);
        $searchTerm = Mava_Url::getParam('q');
        $groupID = (int)Mava_Url::getParam('groupID');
        $userGroupModel = $this->_getUserGroupModel();
        $group_title = '';
        $group_id = 0;
        if($groupID > 0 && $group = $userGroupModel->getUserGroupById($groupID)){
            $group_title = $group['group_title'];
            $group_id = $group['group_id'];
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => ($searchTerm!=""||$group_id>0?Mava_Url::buildLink('admin/users/index'):''),
            'text' => __('admin_users')
        );

        $userModel = $this->_getUserModel();

        $offset = Mava_Application::getOptions()->adminPaginationOffset;
        $limit = Mava_Application::getOptions()->defaultAdminROP;
        $skip = ($page - 1)*$limit;

        $users = $userModel->getListUser($skip, $limit, $searchTerm, $group_id);


        $added = (int)Mava_Url::getParam('added');
        $updated = (int)Mava_Url::getParam('updated');
        $deleted = (int)Mava_Url::getParam('deleted');
        return $this->responseView('Admin_View_User_List',array(
            'searchTerm' => $searchTerm,
            'groupTitle' => $group_title,
            'groupID' => $group_id,
            'users' => $users['rows'],
            'total' => $users['total'],
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

    /**
     * @return Mava_Model_User
     */
    protected function _getUserModel(){
        return $this->getModelFromCache('Mava_Model_User');
    }

    /**
     * @return Mava_Model_UserGroup
     */
    protected function _getUserGroupModel(){
        return $this->getModelFromCache('Mava_Model_UserGroup');
    }

    /**
     * @return Mava_Model_Permission
     */
    protected function _getPermissionModel(){
        return $this->getModelFromCache('Mava_Model_Permission');
    }

    /**
     * @return Mava_DataWriter_User
     * @throws Mava_Exception
     */
    protected function _getUserDataWriter(){
        return Mava_DataWriter::create('Mava_DataWriter_User');
    }

    /**
     * @return Mava_DataWriter_UserGroup
     * @throws Mava_Exception
     */
    protected function _getUserGroupDataWriter(){
        return Mava_DataWriter::create('Mava_DataWriter_UserGroup');
    }
}