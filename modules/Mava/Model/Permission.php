<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 10/23/15
 * @Time: 10:37 AM
 */
class Mava_Model_Permission extends Mava_Model {
    public function getPermissionById($id){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__permission WHERE `perm_id`=". (int)$id);
        }else{
            return false;
        }
    }

    public function getAllPermission(){
        return $this->_getDb()->fetchAll("SELECT * FROM #__permission");
    }

    public function getPermissionGroupById($id){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__permission_group WHERE `group_id`=". (int)$id);
        }else{
            return false;
        }
    }

    public function getAllPermissionGroup(){
        return $this->_getDb()->fetchAll("SELECT * FROM #__permission_group ORDER BY `sort_order` ASC");
    }

    public function getPermissionInGroup($group_id){
        return $this->_getDb()->fetchAll("SELECT p.* FROM #__permission p, #__permission_group_relation gr WHERE p.`perm_id`=gr.`perm_id` AND gr.`group_id`='". (int)$group_id ."' ORDER BY gr.`sort_order` ASC");
    }

    public function permissionKeyExisted($key){
        if($key != ""){
            $count = $this->_getDb()->fetchRow("SELECT count(*) as 'total' FROM #__permission WHERE `perm_key`='". htmlspecialchars($key) ."'");
            if($count['total'] > 0){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function addPermissionRelation($group_id, $perm_id, $sort_order){
        if($group_id > 0 && $perm_id > 0){
            return $this->_getDb()->insert('#__permission_group_relation', array(
                'perm_id' => $perm_id,
                'group_id' => $group_id,
                'sort_order' => $sort_order
            ));
        }else{
            return false;
        }
    }

    public function updatePermissionGroup($group_id, $perm_id){
        if($group_id > 0 && $perm_id > 0){
            return $this->_getDb()->update('#__permission_group_relation', array(
                'group_id' => (int)$group_id
            ), "perm_id=". (int)$perm_id);
        }else{
            return false;
        }
    }

    public function updatePermissionSortOrder($group_id, $perm_id, $sort_order){
        if($group_id > 0 && $perm_id > 0){
            return $this->_getDb()->update('#__permission_group_relation', array(
                'sort_order' => $sort_order
            ),'group_id='. (int)$group_id ." AND perm_id=". (int)$perm_id);
        }else{
            return false;
        }
    }

    public function getPermissionRelation($perm_id){
        if($perm_id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__permission_group_relation WHERE `perm_id`='". (int)$perm_id ."'");
        }else{
            return false;
        }
    }

    public function deletePermissionRelation($perm_id){
        if($perm_id > 0){
            $this->_getDb()->delete('#__permission_group_relation','perm_id='. (int)$perm_id);
            $this->_getDb()->delete('#__user_group_permission','perm_id='. (int)$perm_id);
            $this->_getDb()->delete('#__user_permission','perm_id='. (int)$perm_id);
            return true;
        }else{
            return false;
        }
    }

    public function deletePermissionInGroup($group_id){
        $permissions = $this->_getDb()->fetchAll("SELECT perm_id FROM #__permission_group_relation WHERE `group_id`=". (int)$group_id);
        if(is_array($permissions) && count($permissions) > 0){
            $perm_ids = array();
            foreach($permissions as $item){
                $perm_ids[] = $item['perm_id'];
            }

            if(count($perm_ids) > 0){
                $this->_getDb()->delete('#__permission_group_relation',"perm_id IN ('". implode("','", $perm_ids) ."')");
                $this->_getDb()->delete('#__user_permission',"perm_id IN ('". implode("','", $perm_ids) ."')");
                $this->_getDb()->delete('#__user_group_permission',"perm_id IN ('". implode("','", $perm_ids) ."')");
            }
            return true;
        }else{
            return false;
        }
    }

    public function deleteUserGroupPermission($user_group_id){
        return $this->_getDb()->delete('#__user_group_permission',"group_id=". (int)$user_group_id);
    }

    public function getUserGroupPermission($user_group_id){
        return $this->_getDb()->fetchAll("SELECT * FROM #__user_group_permission WHERE `group_id`=". (int)$user_group_id);
    }

    public function getUserPermission($user_id){
        return $this->_getDb()->fetchAll("SELECT * FROM #__user_permission WHERE `user_id`=". (int)$user_id);
    }

    public function getUserPermissionAllowed($user_id, $user_group_id = 0){
        if($user_group_id == 0){
            $userModel = $this->getModelFromCache('Mava_Model_User');
            $user = $userModel->getUserById($user_id);
            if($user){
                $user_group_id = $user['user_group_id'];
            }
        }

        if($user_group_id > 0){
            $groupPermission = $this->getUserGroupPermission($user_group_id);
            $allPermission = $this->getAllPermission();
            $userPermission = $this->_getDb()->fetchAll("SELECT * FROM #__user_permission WHERE `user_id`=". (int)$user_id);
            $permissions = array();
            if(is_array($allPermission) && count($allPermission) > 0){
                foreach($allPermission as $item){
                    $permissions[$item['perm_id']] = $item['perm_key'];
                }
            }

            $user_permissions = array();
            if(is_array($groupPermission) && count($groupPermission) > 0){
                foreach($groupPermission as $item){
                    $user_permissions[$item['perm_id']] = $item['perm_value'];
                }
            }

            if(is_array($userPermission) && count($userPermission) > 0){
                foreach($userPermission as $item){
                    if($item['perm_value'] != 'inherit'){
                        $user_permissions[$item['perm_id']] = $item['perm_value'];
                    }
                }
            }

            $user_permission_final = array();
            foreach($user_permissions as $k => $v){
                if($v == 'allowed'){
                    $user_permission_final[] = $permissions[$k];
                }
            }

            return $user_permission_final;
        }else{
            return array();
        }
    }

    public function changeUserGroupPermission($user_group_id, $perm_id, $perm_value){
        $check_exist = $this->_getDb()->fetchRow("SELECT * FROM #__user_group_permission WHERE group_id=". (int)$user_group_id .' AND perm_id='. (int)$perm_id);
        if(is_array($check_exist) && $check_exist['perm_id']==$perm_id && $check_exist['group_id'] == $user_group_id){
            return $this->_getDb()->update('#__user_group_permission', array('perm_value' => $perm_value), 'group_id='. (int)$user_group_id .' AND perm_id='. (int)$perm_id);
        }else{
            return $this->_getDb()->insert('#__user_group_permission', array(
                'perm_id' => (int)$perm_id,
                'group_id' => (int)$user_group_id,
                'perm_value' => $perm_value
            ));
        }
    }

    public function changeUserPermission($user_id, $perm_id, $perm_value){
        $check_exist = $this->_getDb()->fetchRow("SELECT * FROM #__user_permission WHERE user_id=". (int)$user_id .' AND perm_id='. (int)$perm_id);
        if(is_array($check_exist) && $check_exist['perm_id']==$perm_id && $check_exist['user_id'] == $user_id){
            return $this->_getDb()->update('#__user_permission', array('perm_value' => $perm_value), 'user_id='. (int)$user_id .' AND perm_id='. (int)$perm_id);
        }else{
            return $this->_getDb()->insert('#__user_permission', array(
                'perm_id' => (int)$perm_id,
                'user_id' => (int)$user_id,
                'perm_value' => $perm_value
            ));
        }
    }
}