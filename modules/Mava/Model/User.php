<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 3/18/14
 * Time: 1:55 PM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Model_User extends Mava_Model
{

    const LOGIN_PASSWORD_INCORRECT = 1;
    const LOGIN_NOT_ACTIVE = 2;
    const LOGIN_SUCCESS = 3;
    const LOGIN_EMAIL_NOT_EXIST = 4;
    const LOGIN_BANNED = 5;

    public function logout(){
        Mava_Application::get("session")->set('user_id',0);
        Mava_Visitor::setup(0);
        Mava_Helper_Cookie::deleteCookie('hodela_remember_key');
    }

    public function delete($userID){
        if($userID > 0){
            $this->_getDb()->delete('#__user','user_id='. (int)$userID);
            $this->_getDb()->delete('#__user_active_queue','user_id='. (int)$userID);
            $this->_getDb()->delete('#__user_oauthor','user_id='. (int)$userID);
            $this->_getDb()->delete('#__user_permission','user_id='. (int)$userID);
        }
        return true;
    }

    public function getListUser($skip, $limit, $searchTerm = '', $group_id = 0){
        $db = $this->_getDb();
        $where = "WHERE u.`user_group_id`=g.`group_id`";
        if($searchTerm != ""){
            $searchTerm = $db->quoteLike($searchTerm);
            $where .= " AND (u.`email` LIKE '%". $searchTerm ."%'
            OR u.`user_id`='". (int)$searchTerm ."'
            OR u.`phone` LIKE '%". $searchTerm ."%'
            OR LOWER(u.`custom_title`) LIKE '%". $searchTerm ."%')";
        }

        if($group_id > 0){
            $where .= " AND u.`user_group_id`=". (int)$group_id;
        }
        $users = $db->query("
        SELECT u.*,g.group_title FROM #__user u, #__user_group g ". $where ." ORDER BY u.`user_id` DESC LIMIT ". $skip .",". $limit ."
        ");
        $count = $db->query("SELECT count(*) as 'total' FROM #__user u, #__user_group g ". $where);
        return array(
            'rows' => $users->rows,
            'total' => $count->row['total']
        );
    }

    public function getUserGroupList(){
        $db = $this->_getDb();
        $userGroup = $db->query("SELECT * FROM #__user_group ORDER BY `sort_order` ASC");
        return $userGroup->rows;
    }

    /**
     * @param int $groupId
     * @return bool | array
     * @throws Mava_Exception
     */
    public function getUserGroupById($groupId){
        if($groupId > 0) {
            $db = $this->_getDb();
            $userGroup = $db->query("SELECT * FROM #__user_group WHERE `group_id`=". (int)$groupId);
            if($userGroup->num_rows > 0) {
                return $userGroup->row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function getPublicField()
    {
        return array(
            'user_id',
            'token',
            'email',
            'phone',
            'gender',
            'city_id',
            'birthday',
            'custom_title',
            'language_id',
            'timezone',
            'user_group_id',
            'register_date',
            'last_activity',
            'is_banned',
            'email_verified',
            'phone_verified'
        );
    }

    public function insert($data)
    {
        if (
            strlen($data['password']) >= Mava_Application::get('config/passwordMinLength') &&
            strlen($data['password']) <= Mava_Application::get('config/passwordMaxLength') &&
            strlen($data['email']) != ""
        ) {
            $uniqueToken = $this->getUniqueToken();
            $userGroupID = (int)Mava_Application::get("options")->defaultUserGroupID;
            $this->_getDb()->query("
            INSERT INTO #__user(
            `password`,
            `unique_token`,
            `email`,
            `phone`,
            `gender`,
            `custom_title`,
            `language_id`,
            `timezone`,
            `active_code`,
            `is_active`,
            `user_group_id`,
            `city_id`,
            `birthday`,
            `register_date`,
            `last_activity`
            ) VALUE(
                '" . $this->generalPassword($data['password'], $uniqueToken) . "',
                '" . $uniqueToken . "',
                '" . addslashes($data['email']) . "',
                '" . addslashes($data['phone']) . "',
                '" . addslashes($data['gender']) . "',
                '" . addslashes($data['custom_title']) . "',
                '" . addslashes($data['language_id']) . "',
                '" . addslashes($data['timezone']) . "',
                '" . addslashes($data['active_code']) . "',
                '" . intval($data['is_active']) . "',
                '" . $userGroupID . "',
                '" . (isset($data['city_id'])?(int)$data['city_id']:0) . "',
                '" . (isset($data['birthday'])?(int)$data['birthday']:0) . "',
                '" . time() . "',
                '" . time() . "'
            )");

            $userID = $this->_getDb()->getLastId();
            if ($userID > 0) {
                $agencyModel = $this->_getAgencyModel();
                $agencyModel->createUserDefaultAgency($userID);
                return $userID;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @return LandingPage_Model_Agency
     */
    protected function _getAgencyModel(){
        return $this->getModelFromCache('LandingPage_Model_Agency');
    }

    public function update($userID, $data){
        if($userID > 0 && is_array($data) && count($data) > 0){
            return $this->_getDb()->update('#__user', $data, 'user_id='. (int)$userID);
        }else{
            return false;
        }
    }

    public function loginByEmail($email, $password)
    {
        $user = $this->getUserByEmail($email, false);
        if ($user && isset($user['unique_token']) && isset($user['password']) && isset($user['user_id'])) {
            if ($this->generalPassword($password, $user['unique_token']) == $user['password']) {
                if($user['is_active'] == 0){
                    return array(
                        'status' => -1,
                        'user_id' => $user['user_id'],
                        'email' => $user['email'],
                        'phone' => $user['phone'],
                        'code' => self::LOGIN_NOT_ACTIVE,
                        'message' => __('account_not_active')
                    );
                }else if($user['is_banned'] > time()){
                    return array(
                        'status' => -1,
                        'user_id' => $user['user_id'],
                        'banned_reason' => $user['banned_reason'],
                        'is_banned' => $user['is_banned'],
                        'code' => self::LOGIN_BANNED,
                        'message' => __('account_is_banned')
                    );
                }else{
                    return array(
                        'status' => 1,
                        'user_id' => $user['user_id'],
                        'code' => self::LOGIN_SUCCESS,
                        'message' => __('login_success')
                    );
                }
            } else {
                return array(
                    'status' => -1,
                    'user_id' => 0,
                    'code' => self::LOGIN_PASSWORD_INCORRECT,
                    'message' => __('password_incorrect')
                );
            }
        } else {
            return array(
                'status' => -1,
                'user_id' => 0,
                'code' => self::LOGIN_EMAIL_NOT_EXIST,
                'message' => __('email_not_found')
            );
        }
    }

    public function loginByPhone($phone, $password)
    {
        $user = $this->getUserByPhone($phone, false);
        if ($user && isset($user['unique_token']) && isset($user['password']) && isset($user['user_id'])) {
            if ($this->generalPassword($password, $user['unique_token']) == $user['password']) {
                if($user['is_active'] == 0){
                    return array(
                        'status' => -1,
                        'user_id' => $user['user_id'],
                        'email' => $user['email'],
                        'phone' => $user['phone'],
                        'code' => self::LOGIN_NOT_ACTIVE,
                        'message' => __('account_not_active')
                    );
                }elseif($user['is_banned'] > time()){
                    return array(
                        'status' => -1,
                        'user_id' => $user['user_id'],
                        'banned_reason' => $user['banned_reason'],
                        'is_banned' => $user['is_banned'],
                        'code' => self::LOGIN_BANNED,
                        'message' => __('account_is_banned')
                    );
                }else{
                    return array(
                        'status' => 1,
                        'user_id' => $user['user_id'],
                        'code' => self::LOGIN_SUCCESS,
                        'message' => __('login_success')
                    );
                }
            } else {
                return array(
                    'status' => -1,
                    'user_id' => 0,
                    'code' => self::LOGIN_PASSWORD_INCORRECT,
                    'message' => __('password_incorrect')
                );
            }
        } else {
            return array(
                'status' => -1,
                'user_id' => 0,
                'code' => self::LOGIN_EMAIL_NOT_EXIST,
                'message' => __('email_not_found')
            );
        }
    }

    public function filterKey(array $userData)
    {
        $publicField = $this->getPublicField();
        $cleanData = array();
        foreach ($userData as $k => $v) {
            if (in_array($k, $publicField)) {
                $cleanData[$k] = $v;
            }
        }
        return $cleanData;
    }

    public function getUserById($userID, $filter = true)
    {
        if ((int)$userID == 0) {
            return false;
        } else {
            $user = $this->_getDb()->fetchRow("SELECT * FROM #__user WHERE `user_id`='". $userID ."'");
            if($user){
                if($filter){
                    return $this->filterKey($user);
                }else{
                    return $user;
                }
            }else{
                return false;
            }
        }
    }

    public function getUserByEmail($email, $filter = true)
    {
        $user = $this->_getDb()->fetchRow("SELECT * FROM #__user WHERE `email`='". $email ."'");
        if($user){
            if($filter){
                return $this->filterKey($user);
            }else{
                return $user;
            }
        }else{
            return false;
        }
    }

    public function getUserByPhone($phone, $filter = true)
    {
        $user = $this->_getDb()->fetchRow("SELECT * FROM #__user WHERE `phone`='". $phone ."'");
        if($user){
            if($filter){
                return $this->filterKey($user);
            }else{
                return $user;
            }
        }else{
            return false;
        }
    }

    public static function checkEmailExist($email, $exceptUID = array(), $onlyVerified = 0)
    {
        $where = " WHERE `email` ='". $email ."'";
        if(is_array($exceptUID) && count($exceptUID) > 0){
            $where .= " AND `user_id` NOT IN ('". Mava_String::doImplode($exceptUID) ."')";
        }
        if($onlyVerified == 1){
            $where .= " AND `email_verified`='yes'";
        }
        $user = Mava_Application::getDb()->fetchRow("SELECT COUNT(*) AS 'total' FROM #__user ". $where);
        if ($user['total'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function autoLogin(){
        $remember_key = Mava_Helper_Cookie::getCookie('hodela_remember_key');
        if(!is_login() && $remember_key != ""){
            $remember_key = explode('_',base64_decode($remember_key));
            if(is_array($remember_key) && count($remember_key) == 3){
                $user_id = $remember_key[0];
                $time = $remember_key[1];
                $userModel = Mava_Model::create('Mava_Model_User');
                if($user = $userModel->getUserById($user_id, false)){
                    if($remember_key[2] == md5($time .'_'. $user['user_id'] .'_hodela')){
                        Mava_Session::set('user_id',$user_id);
                        Mava_Visitor::setup($user_id);
                    }
                }
            }
        }
    }

    public static function checkPhoneExist($phone, $exceptUID = array(), $onlyVerified = 0)
    {
        $condition = array();
        $condition[] = array('phone', '=', $phone);
        if($onlyVerified == 1){
            $condition[] = array('phone_verified', '=', 1);
        }
        if (sizeof($exceptUID) > 0) {
            if (sizeof($exceptUID) == 1) {
                $condition[] = array('user_id', '<>', $exceptUID);
            } else {
                $condition[] = array('user_id', 'NOT IN', '(' . Mava_String::doImplode($exceptUID) . ')');
            }
        }
        $user = Mava_Application::get('db')->getSingleTableRow('#__user', 'user_id', $condition, 0, 1);
        if ($user->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getGuestUser()
    {
        $guestTimeZone = Mava_Application::get('config/defaultTimeZone');
        $guestLanguage = Mava_Application::get('config/defaultLanguage');
        $checkLanguage = $this->_getDb()->getSingleTableRow('#__language', '*', array(
            array('language_code', '=', $guestLanguage)
        ), 0, 1);
        $guestLanguageID = 0;
        if ($checkLanguage->num_rows > 0) {
            $guestLanguageID = $checkLanguage->row['language_id'];
        }
        $userinfo = array(
            'user_id' => 0,
            'email' => '',
            'gender' => '',
            'balance' => 0,
            'city_id' => 0,
            'birthday' => 0,
            'language_id' => $guestLanguageID,
            'timezone' => $guestTimeZone,
            'is_banned' => 0,
            'email_verified' => 0,
            'phone_verified' => 0
        );

        return $userinfo;
    }

    public function getUniqueToken()
    {
        return md5(time() .'_'. uniqid()) . md5(Mava_String::getRandomHash(32));
    }

    public function generalPassword($password, $uniqueToken)
    {
        return md5($password . $uniqueToken);
    }

    public function connectFacebookAccount($fbUserInfo = array(), $accessToken){
        if(isset($fbUserInfo['id']) && isset($fbUserInfo['email']) && $accessToken != ""){
            $db = $this->_getDb();

            $checkConnect = $db->fetchRow("SELECT * FROM #__user_oauthor WHERE `social_user_id`='". $fbUserInfo['id'] ."' AND `social_key`='facebook'");
            if($checkConnect){
                // connect exist
                $checkUserExist = $this->getUserById($checkConnect['user_id']);
                if($checkUserExist){
                    $db->query("UPDATE #__user_oauthor SET `access_token`='". addslashes($accessToken) ."',`social_name`='". addslashes($fbUserInfo['name']) ."',`info`='". json_encode($fbUserInfo) ."' WHERE `social_user_id`='". $fbUserInfo['id'] ."'");
                    return $checkUserExist['user_id'];
                }else{
                    // user not exist
                    return false;
                }
            }else{
                $user_exist = $db->fetchRow("SELECT * FROM #__user WHERE `email`='". $fbUserInfo['email'] ."'");
                if($user_exist){
                    $userID = $user_exist['user_id'];
                }else{
                    // new connect
                    // add user
                    $uniqueToken = $this->getUniqueToken();
                    $userGroupID = (int)Mava_Application::get("options")->defaultUserGroupID;
                    $guestLanguage = Mava_Visitor::getLanguageCode();
                    $checkLanguage = $this->_getDb()->getSingleTableRow('#__language', '*', array(
                        array('language_code', '=', $guestLanguage)
                    ), 0, 1);
                    $guestLanguage = Mava_Application::get('config/defaultLanguage');
                    if ($checkLanguage->num_rows > 0) {
                        $guestLanguage = $checkLanguage->row['language_code'];
                    }
                    $guestTimeZone = Mava_Visitor::getInstance()->get('timezone');
                    if(!$guestTimeZone){
                        $guestTimeZone = Mava_Application::get('config/defaultTimeZone');
                    }
                    $db->query("
                    INSERT INTO #__user(
                    `unique_token`,
                    `email`,
                    `gender`,
                    `custom_title`,
                    `language_code`,
                    `timezone`,
                    `user_group_id`,
                    `register_date`,
                    `last_activity`
                    ) VALUE(
                        '" . $uniqueToken . "',
                        '" . addslashes($fbUserInfo['email']) . "',
                        '" . addslashes($fbUserInfo['gender']) . "',
                        '" . addslashes($fbUserInfo['name']) . "',
                        '" . addslashes($guestLanguage) . "',
                        '" . addslashes($guestTimeZone) . "',
                        '" . $userGroupID . "',
                        '" . time() . "',
                        '" . time() . "'
                    )");
                    Mava_Application::delCache('user_count');
                    $userID = $db->getLastId();
                }
                $randomPassword = md5(Mava_String::getRandomHash());
                if ($userID > 0) {
                    // add connect
                    $db->query("
                    INSERT INTO #__user_oauthor(
                    `user_id`,
                    `social_user_id`,
                    `social_name`,
                    `temp_password`,
                    `access_token`,
                    `info`,
                    `social_key`
                    ) VALUE(
                        '" . (int)$userID . "',
                        '" . addslashes($fbUserInfo['id']) . "',
                        '" . addslashes($fbUserInfo['name']) . "',
                        '" . addslashes($randomPassword) . "',
                        '" . addslashes($accessToken) . "',
                        '" . json_encode($fbUserInfo) . "',
                        'facebook'
                    )");

                    $connectId = $db->getLastId();
                    if($connectId > 0){
                        // set avatar
                        if(!user_has_avatar($userID)){
                            $path = BASEDIR . '/data/images/avatar/' . mkdir_by_id('data/images/avatar',$userID);
                            $filename = $path . '_avatar_org.jpg';
                            $file_content = file_get_contents('https://graph.facebook.com/'. $fbUserInfo['id'] .'/picture?width=500&height=500');
                            file_put_contents($filename, $file_content);
                            thumbs($filename, $path .'_avatar_big.jpg',200,200);
                            thumbs($filename, $path .'_avatar_middle.jpg',100,100);
                            thumbs($filename, $path .'_avatar_small.jpg',50,50);
                        }
                        return $userID;
                    }else{
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }else{
            return false;
        }
    }
}