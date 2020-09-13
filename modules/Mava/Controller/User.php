<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 3/19/14
 * Time: 11:49 AM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Controller_User extends Mava_Controller {
    public function signupAction(){
        Mava_Application::set('body_id','signup_page');
        Mava_Application::set('menu_selected','signup');
        Mava_Application::set('seo/title',__('register'));
        $visitor = Mava_Visitor::getInstance();
        if($visitor->get('user_id') > 0){
            return $this->responseRedirect(Mava_Url::getDomainUrl());
        }
        $options = Mava_Application::getOptions();
        if($options->register_allow == 0){
            return $this->responseError('Chức năng đăng ký đã khóa. Bạn vẫn có thể <a href="'. Mava_Url::getPageLink('login') .'">đăng nhập</a> nếu đã có tài khoản hoặc trở về <a href="'. Mava_Url::getDomainUrl() .'">trang chủ</a> để xem các nội dung dành cho khách vãng lai.', Mava_Error::ACCESS_DENIED);
        }
        $viewParams = array(
            'error' => 0,
            'login_message' => Mava_Application::getSession()->get('login_message'),
            'login_return_url' => Mava_Application::getSession()->get('login_return_url')
        );
        $visitor = Mava_Visitor::getInstance();

        $viewParams['password'] = Mava_Url::getParam('signup_password');
        $viewParams['repassword'] = Mava_Url::getParam('signup_retype_password');
        $viewParams['fullname'] = Mava_Url::getParam('signup_fullname');
        $viewParams['email'] = Mava_Url::getParam('signup_email');
        $viewParams['phone'] = Mava_Url::getParam('signup_phone');
        $viewParams['gender'] = Mava_Url::getParam('signup_gender');

        if(Mava_Url::isPost()){
            $timezone = $visitor->get('timezone');
            $language_id = $visitor->get('language_id');
            if(!in_array($viewParams['gender'],array('male','female'))){
                $viewParams['gender'] = '';
            }
            $validate = $this->_validateRegister($viewParams['password'],$viewParams['repassword'],$viewParams['email'],$viewParams['phone']);
            if($validate['status']==1){
                $active_code = md5($viewParams['email'] .'_'. time());
                $is_active = 0;
                if($options->register_active_type == 'none'){
                    $is_active = 1;
                    $active_code = '';
                }else if($options->register_active_type == 'phone'){
                    $active_code = rand(100000,999999);
                }
                $userModel = $this->_getUserModel();
                $userID = $userModel->insert(
                    array(
                        'password'  => $viewParams['password'],
                        'email'     => $viewParams['email'],
                        'phone'     => $viewParams['phone'],
                        'gender'     => $viewParams['gender'],
                        'custom_title'     => $viewParams['fullname'],
                        'language_id'     => $language_id,
                        'timezone'     => $timezone,
                        'token'         => $viewParams['phone'],
                        'active_code' => $active_code,
                        'is_active' => $is_active
                    )
                );
                if($userID){
                    if($options->register_active_type == 'email'){
                        $emailQueueDW = $this->_getEmailQueueDataWriter();
                        $emailQueueDW->bulkSet(array(
                            'type' => Mava_Model_EmailQueue::TYPE_ACTIVE,
                            'email' => $viewParams['email'],
                            'content' => json_encode(array(
                                'title' => __('email_active_account_title'),
                                'body' => __('email_active_account_body',array(
                                    'name' => $viewParams['fullname'],
                                    'email' => $viewParams['email'],
                                    'phone' => $viewParams['phone'],
                                    'link' => Mava_Url::getPageLink('active_account', array(
                                        'code' => $active_code,
                                        'uid' => $userID
                                    ))
                                ))
                            )),
                            'created_date' => time()
                        ));
                        $emailQueueDW->save();
                        $viewParams['email_queue_id'] = $emailQueueDW->get('queue_id');
                    }else if($options->register_active_type == 'phone'){
                        $phoneQueueDW = $this->_getPhoneQueueDataWriter();
                        $phoneQueueDW->bulkSet(array(
                            'type' => Mava_Model_PhoneQueue::TYPE_ACTIVE,
                            'number' => $viewParams['phone'],
                            'content' => __('phone_active_account_body', array(
                                'active_code' => $active_code
                            )),
                            'created_date' => time()
                        ));
                        $phoneQueueDW->save();
                    }else if($options->register_active_type == 'admin'){
                        $activeQueueDW = $this->_getActiveQueueDataWriter();
                        $activeQueueDW->bulkSet(array(
                            'user_id' => $userID,
                            'created_date' => time()
                        ));
                        $activeQueueDW->save();
                    }else{
                        $this->_doLogin('email',$viewParams['email'],$viewParams['password']);
                        if(Mava_String::isUrl($viewParams['login_return_url'])){
                            Mava_Session::delete('login_return_url');
                            Mava_Session::delete('login_message');
                            return $this->responseRedirect($viewParams['login_return_url']);
                        }
                    }
                    $viewParams['active'] = $options->register_active_type;
                    $viewParams['uid'] = $userID;
                    $viewParams['hash_email'] = md5($userID .'_'. $viewParams['email']);
                    $viewParams['hash_phone'] = md5($userID .'_'. $viewParams['phone']);

                    return $this->responseView('Mava_View_RegisterSuccess', $viewParams);

                }else{
                    $viewParams['error'] = 1;
                    $viewParams['errorMessage'] = __('could_not_register_now');
                }
            }else{
                $viewParams['error'] = 1;
                $viewParams['errorMessage'] = $validate['message'];
            }
        }

        return $this->responseView('Mava_View_Register',$viewParams,'mini');
    }

    public function loginAction(){
        Mava_Application::set('menu_selected','login');
        Mava_Application::set('body_id','login_page');
        Mava_Application::set('seo/title',__('login'));

        $visitor = Mava_Visitor::getInstance();
        if($visitor->get('user_id') > 0){
            return $this->responseRedirect(Mava_Url::getDomainUrl());
        }

        $viewParams = array(
            'error' => 0,
            'login_message' => Mava_Application::getSession()->get('login_message'),
            'login_return_url' => Mava_Application::getSession()->get('login_return_url')
        );

        if(Mava_Url::isPost()){
            $login_type = Mava_Url::getParam('login_type');
            $login_text = Mava_Url::getParam('login_text');
            $password = Mava_Url::getParam('login_password');
            $remember = (int)Mava_Url::getParam('remember_login');
            $viewParams['login_text'] = $login_text;
            if(!in_array($login_type,array('email','phone'))){
                if(Mava_String::isEmail($login_text)){
                    $login_type = 'email';
                }else{
                    $login_type = 'phone';
                }
            }
            if(strlen(trim($login_text))==0){
                $viewParams['error'] = 1;
                $viewParams['errorMessage'] = __('please_enter_'. $login_type);
            }else if(strlen(trim($password))==0){
                $viewParams['error'] = 1;
                $viewParams['errorMessage'] = __('please_enter_password');
            }else{
                $checkLogin = $this->_doLogin($login_type,$login_text,$password);
                if($checkLogin['status']==1){
                    if($remember == 1){
                        $time = time();
                        $uid = Mava_Visitor::getUserId();
                        Mava_Helper_Cookie::setCookie('hodela_remember_key', base64_encode($uid ."_". $time ."_". md5($time ."_". $uid .'_hodela')), 30*86400);
                    }
                    if(Mava_String::isUrl($viewParams['login_return_url'])){
                        Mava_Application::getSession()->delete('login_return_url');
                        Mava_Application::getSession()->delete('login_message');
                        return $this->responseRedirect($viewParams['login_return_url']);
                    }else{
                        return $this->responseRedirect(Mava_Url::getPageLink('/'));
                    }
                }else if($checkLogin['code'] == Mava_Model_User::LOGIN_NOT_ACTIVE){
                    Mava_Application::set('seo/title', __('account_not_active'));
                    $viewParams = array();
                    if(Mava_Application::getOptions()->register_active_type == 'phone'){
                        $viewParams = array(
                            'uid' => $checkLogin['user_id'],
                            'hash_phone' => md5($checkLogin['user_id'] .'_'. $checkLogin['phone']),
                            'phone' => $checkLogin['phone'],
                            'active_type' => 'phone'
                        );
                    }else if(Mava_Application::getOptions()->register_active_type == 'email'){
                        $viewParams = array(
                            'uid' => $checkLogin['user_id'],
                            'hash_email' => md5($checkLogin['user_id'] .'_'. $checkLogin['email']),
                            'email' => $checkLogin['email'],
                            'active_type' => 'email'
                        );
                    }else{
                        $viewParams = array(
                            'uid' => $checkLogin['user_id'],
                            'active_type' => 'admin'
                        );
                    }
                    return $this->responseView('Mava_View_AccountInActive', $viewParams);
                }elseif($checkLogin['code'] == Mava_Model_User::LOGIN_BANNED){
                    return $this->responseView('Mava_View_AccountIsBanned', array(
                            'banned_time' => $checkLogin['is_banned'],
                            'banned_reason' => $checkLogin['banned_reason']
                        ));
                }else{
                    $viewParams['error'] = 1;
                    $viewParams['errorMessage'] = $checkLogin['message'];
                }
            }
        }
        return $this->responseView('Mava_View_Login',$viewParams,'mini');
    }


    public function ajaxLoginAction(){
        $login_type = 'email';
        $login_text = Mava_Url::getParam('email');
        if(!Mava_String::isEmail($login_text) && Mava_String::isPhoneNumber($login_text)){
            $login_type = 'phone';
        }
        $password = Mava_Url::getParam('password');
        $remember = Mava_Url::getParam('remember');
        $checkLogin = $this->_doLogin($login_type,$login_text,$password);
        if($checkLogin['status']==1){
            if($remember){
                $time = time();
                $uid = Mava_Visitor::getUserId();
                Mava_Helper_Cookie::setCookie('hodela_remember_key', base64_encode($uid ."_". $time ."_". md5($time ."_". $uid .'_hodela')), 30*86400);
            }
            Mava_Session::set('otm', __('login_success'));
            return $this->responseJson(array(
                'status' => 1,
                'user_id' => Mava_Visitor::getUserId(),
                'avatar' => get_avatar_url('small', Mava_Visitor::getUserId()),
                'fullname' => get_fullname()
            ));
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => $checkLogin['message']
            ));
        }
    }

    public function ajaxSignupAction(){
        $options = Mava_Application::getOptions();
        if($options->register_allow == 0){
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('register_function_is_disabled')
            ));
        }else{
            $viewParams['password'] = Mava_Url::getParam('password');
            $viewParams['repassword'] = Mava_Url::getParam('confirm_password');
            $viewParams['fullname'] = Mava_Url::getParam('fullname');
            $viewParams['email'] = Mava_Url::getParam('email');
            $viewParams['phone'] = Mava_Url::getParam('phone');
            $viewParams['gender'] = Mava_Url::getParam('gender');
            $visitor = Mava_Visitor::getInstance();
            $timezone = $visitor->get('timezone');
            $language_id = $visitor->getLanguageId();
            if(!in_array($viewParams['gender'],array('male','female'))){
                $viewParams['gender'] = '';
            }
            $validate = $this->_validateRegister($viewParams['password'],$viewParams['repassword'],$viewParams['email'],$viewParams['phone'],$viewParams['fullname']);
            if($validate['status']==1){
                $active_code = md5($viewParams['email'] .'_'. time());
                $is_active = 0;
                if($options->register_active_type == 'none'){
                    $is_active = 1;
                    $active_code = '';
                }else if($options->register_active_type == 'phone'){
                    $active_code = rand(100000,999999);
                }
                $userModel = $this->_getUserModel();
                $userID = $userModel->insert(
                    array(
                        'password'  => $viewParams['password'],
                        'email'     => $viewParams['email'],
                        'phone'     => $viewParams['phone'],
                        'gender'     => $viewParams['gender'],
                        'custom_title'     => $viewParams['fullname'],
                        'language_id'     => $language_id,
                        'timezone'     => $timezone,
                        'token'         => $viewParams['phone'],
                        'active_code' => $active_code,
                        'is_active' => $is_active
                    )
                );
                if($userID){
                    if($options->register_active_type == 'email'){
                        $emailQueueDW = $this->_getEmailQueueDataWriter();
                        $emailQueueDW->bulkSet(array(
                            'type' => Mava_Model_EmailQueue::TYPE_ACTIVE,
                            'email' => $viewParams['email'],
                            'content' => json_encode(array(
                                'title' => __('email_active_account_title'),
                                'body' => __('email_active_account_body',array(
                                    'name' => $viewParams['fullname'],
                                    'email' => $viewParams['email'],
                                    'phone' => $viewParams['phone'],
                                    'link' => Mava_Url::getPageLink('active_account', array(
                                        'code' => $active_code,
                                        'uid' => $userID
                                    ))
                                ))
                            )),
                            'created_date' => time()
                        ));
                        $emailQueueDW->save();
                        $viewParams['email_queue_id'] = $emailQueueDW->get('queue_id');
                    }else if($options->register_active_type == 'phone'){
                        $phoneQueueDW = $this->_getPhoneQueueDataWriter();
                        $phoneQueueDW->bulkSet(array(
                            'type' => Mava_Model_PhoneQueue::TYPE_ACTIVE,
                            'number' => $viewParams['phone'],
                            'content' => __('phone_active_account_body', array(
                                'active_code' => $active_code
                            )),
                            'created_date' => time()
                        ));
                        $phoneQueueDW->save();
                    }else if($options->register_active_type == 'admin'){
                        $activeQueueDW = $this->_getActiveQueueDataWriter();
                        $activeQueueDW->bulkSet(array(
                            'user_id' => $userID,
                            'created_date' => time()
                        ));
                        $activeQueueDW->save();
                    }
                    $this->_doLogin('email',$viewParams['email'],$viewParams['password']);
                    Mava_Session::set('otm', __('register_success'));
                    return $this->responseJson(array(
                        'status' => 1,
                        'user_id' => Mava_Visitor::getUserId(),
                        'avatar' => get_avatar_url('small', Mava_Visitor::getUserId()),
                        'fullname' => get_fullname()
                    ));
                }else{
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('could_not_register_now')
                    ));
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => $validate['message']
                ));
            }
        }
    }

    public function loginWithFacebookAction(){
        $visitor = Mava_Visitor::getInstance();
        if($visitor->get('user_id') > 0){
            return $this->responseRedirect(Mava_Url::getDomainUrl());
        }
        $connect = $this->_processConnectFacebook(Mava_Url::getPageLink('login_with_facebook'));
        if(isset($connect['redirect'])){
            return $this->responseRedirect($connect['redirect']);
        }else if(isset($connect['error'])){
            return $this->responseError($connect['error']);
        }else if(isset($connect['user_id']) && $connect['user_id'] > 0){
            Mava_Application::get("session")->set('user_id',$connect['user_id']);
            Mava_Application::get("session")->set('user_facebook',1);
            Mava_Visitor::setup($connect['user_id']);
            $time = time();
            $uid = Mava_Visitor::getUserId();
            Mava_Helper_Cookie::setCookie('hodela_remember_key', base64_encode($uid ."_". $time ."_". md5($time ."_". $uid .'_hodela')), 30*86400);
            return $this->responseRedirect(Mava_Url::getPageLink('login_facebook_success'));
        }else{
            return $this->responseError(__('facebook_login_error'));
        }
    }

    public function loginFacebookSuccessAction(){
        return $this->responseView('Mava_View_LoginSuccess');
    }

    protected function _processConnectFacebook($redirectUrl){
        $code = Mava_Url::getParam('code');
        $state = Mava_Url::getParam('state');
        if($code!="" && $state!=""){
            if($state==Mava_Application::getSession()->get('fbCsrfState')){
                Mava_Application::getSession()->delete('fbCsrfState');
                $accessToken = Mava_Helper_Facebook::getAccessTokenFromCode($code,$redirectUrl);
                if(isset($accessToken['access_token']) && $accessToken['access_token']!=""){
                    $userInfo = Mava_Helper_Facebook::getUserInfo($accessToken['access_token'],'me', array('fields' => 'id,name,email,gender,birthday,age_range,hometown,locale,location,relationship_status,work,website'));
                    if(isset($userInfo['error'])){
                        return array(
                            'error' => __('facebook_login_error')
                        );
                    }else{
                        $userModel = $this->_getUserModel();
                        $userConnectId = $userModel->connectFacebookAccount($userInfo, $accessToken['access_token']);
                        if($userConnectId){
                            return array(
                                'user_id' => $userConnectId,
                                'fb_info' => $userInfo
                            );
                        }else{
                            return array(
                                'error' => __('facebook_login_error')
                            );
                        }
                    }
                }else{
                    return array(
                        'error' => __('facebook_login_can_not_get_access_token')
                    );
                }
            }else{
                return array(
                    'error' => __('facebook_login_invalid_state')
                );
            }
        }else{
            $url = Mava_Helper_Facebook::getFacebookRequestUrl($redirectUrl);
            return array(
                'redirect' => $url
            );
        }
    }

    public function logoutAction(){
        Mava_Application::set('seo/title', __('logout_success'));
        $this->_doLogout();
        return $this->responseView('Mava_View_LogoutSuccess');
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
        }else if(!Mava_String::isPhoneNumber($phone)){
            return array(
                'status' => -1,
                'message' => __('phone_invalid')
            );
        }else if(Mava_Model_User::checkPhoneExist($phone)){
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

    public function _doLogin($login_type, $login_text, $password){
        if(!in_array($login_type,array('email','phone'))){
            $login_type = 'email';
        }
        $userModel = $this->_getUserModel();
        $checkLogin = array();
        switch($login_type){
            case 'email':
                    $checkLogin = $userModel->loginByEmail($login_text, $password);
                break;
            case 'phone':
                    $checkLogin = $userModel->loginByPhone($login_text, $password);
                break;
        }
        if($checkLogin['status'] == 1){
            Mava_Helper_Cookie::deleteCookie('rai'); // recent agency access
            Mava_Application::get("session")->set('user_id',$checkLogin['user_id']);
            Mava_Visitor::setup($checkLogin['user_id']);
        }
        return $checkLogin;
    }

    public function activeAccountAction(){
        $uid = Mava_Url::getParam('uid');
        $code = Mava_Url::getParam('code');
        if($uid > 0 && $code != ""){
            $userModel = $this->_getUserModel();
            $user = $userModel->getUserById($uid, false);
            if($user){
                if($user['is_active'] == 1){
                    Mava_Application::set('seo/title', __('account_activated'));
                    return $this->responseView('Mava_View_AccountActivated');
                }else{
                    if($user['active_code'] == $code){
                        $userDW = $this->_getUserDataWriter();
                        $userDW->setExistingData($uid);
                        $userDW->bulkSet(array(
                            'is_active' => 1,
                            'active_code' => '',
                            'email_verified' => 1
                        ));
                        if($userDW->save()){
                            Mava_Application::set('seo/title', __('account_activated'));
                            return $this->responseView('Mava_View_AccountActivated');
                        }else{
                            return $this->responseError(__('can_not_active_your_account'), Mava_Error::INVALID_REQUEST);
                        }
                    }else{
                        return $this->responseError(__('can_not_active_your_account'), Mava_Error::INVALID_REQUEST);
                    }
                }
            }else{
                return $this->responseError(__('account_not_existed'), Mava_Error::NOT_FOUND);
            }
        }else{
            return $this->responseError(__('can_not_active_your_account'), Mava_Error::INVALID_REQUEST);
        }
    }

    public function activePhoneAction(){
        //TODO kich hoat tai khoan bang sms code
        echo 'todo';
    }

    public function forgotpasswordAction(){
        Mava_Application::set('seo/title',__('forgotpassword_page_title'));
        $error_msg = '';
        if(Mava_Url::isPost()){
            $email = Mava_Url::getParam('email');
            if($email != '' && Mava_String::isEmail($email)){
                $userModel = $this->_getUserModel();
                if($userModel->checkEmailExist($email)){
                    $user = $userModel->getUserByEmail($email);
                    if($user){
                        $forgotpassword_code = md5($user['user_id'] .'_'. time());
                        $userDW = $this->_getUserDataWriter();
                        $userDW->setExistingData($user['user_id']);
                        $userDW->set('forgotpassword_token', base64_encode(time() .'_'. $forgotpassword_code));
                        $userDW->save();
                        $emailQueueModel = $this->_getEmailQueueModel();
                        $emailQueueModel->deleteDuplicateEmailQueue(Mava_Model_EmailQueue::TYPE_FORGOT, $email);
                        $emailQueueDw = $this->_getEmailQueueDataWriter();
                        $emailQueueDw->bulkSet(array(
                            'type' => Mava_Model_EmailQueue::TYPE_FORGOT,
                            'email' => $email,
                            'content' => json_encode(array(
                                'title' => __('email_forgotpassword_title'),
                                'body' => __('email_forgotpassword_body',array(
                                    'hour' => Mava_Application::getOptions()->forgotpassword_hour_expired,
                                    'email' => $email,
                                    'name' => $user['custom_title'],
                                    'link' => Mava_Url::getPageLink('forgotpassword_confirm', array(
                                        'forgot_code' => $forgotpassword_code,
                                        'uid' => $user['user_id']
                                    ))
                                ))
                            )),
                            'created_date' => time()
                        ));
                        $emailQueueDw->save();
                        return $this->responseView('Mava_View_ForgotPasswordSent', array(
                            'email' => $email,
                            'email_queue_id' => $emailQueueDw->get('queue_id')
                        ));
                    }else{
                        $error_msg = __('account_not_available');
                    }
                }else{
                    $error_msg = __('email_not_existed');
                }
            }else{
                $error_msg = __('email_invalid');
            }
        }
        return $this->responseView('Mava_View_ForgotPassword', array(
            'error_msg' => $error_msg
        ));
    }

    public function forgotpasswordConfirmAction(){
        Mava_Application::set('seo/title', __('change_password'));
        $uid = Mava_Url::getParam('uid');
        $code = Mava_Url::getParam('forgot_code');
        if($uid > 0 && $code != ""){
            $userModel = $this->_getUserModel();
            $user = $userModel->getUserById($uid, false);
            if($user){
                $forgot_code = explode('_', base64_decode($user['forgotpassword_token']));
                if(is_array($forgot_code) && count($forgot_code) == 2){
                    if((int)$forgot_code[0]+(Mava_Application::getOptions()->forgotpassword_hour_expired*3600) > time()){
                        if($forgot_code[1] == $code){
                            $errorMessage = '';
                            if(Mava_Url::isPost()){
                                $newPassword = Mava_Url::getParam('newPassword');
                                $confirmNewPassword = Mava_Url::getParam('confirmNewPassword');
                                if(
                                    strlen($newPassword) < Mava_Application::get('config/passwordMinLength') ||
                                    strlen($newPassword) > Mava_Application::get('config/passwordMaxLength')
                                ){
                                    $errorMessage = __('password_invalid');
                                }else if($newPassword != $confirmNewPassword){
                                    $errorMessage = __('repassword_invalid');
                                }else{
                                    $userDW = $this->_getUserDataWriter();
                                    $userDW->setExistingData($user['user_id']);
                                    $userDW->bulkSet(array(
                                        'password' => $userModel->generalPassword($newPassword, $user['unique_token']),
                                        'forgotpassword_token' => ''
                                    ));
                                    if($userDW->save()){
                                        Mava_Application::set('seo/title', __('password_changed'));
                                        return $this->responseView('Mava_View_ChangePasswordSuccess', array(
                                            'user' => $user,
                                            'forgot_code' => $code,
                                            'errorMessage' => $errorMessage
                                        ));
                                    }else{
                                        $errorMessage = __('can_not_change_password_now');
                                    }
                                }
                            }
                            return $this->responseView('Mava_View_ChangePassword', array(
                                'user' => $user,
                                'forgot_code' => $code,
                                'errorMessage' => $errorMessage
                            ));
                        }else{
                            return $this->responseError(__('can_not_change_password_now'), Mava_Error::INVALID_REQUEST);
                        }
                    }else{
                        return $this->responseError(__('request_forgot_password_expired'), Mava_Error::ACCESS_DENIED);
                    }
                }else{
                    return $this->responseError(__('can_not_change_password_now'), Mava_Error::INVALID_REQUEST);
                }
            }else{
                return $this->responseError(__('account_not_existed'), Mava_Error::NOT_FOUND);
            }
        }else{
            return $this->responseError(__('can_not_change_password_now'), Mava_Error::INVALID_REQUEST);
        }
    }

    public function resendActiveAccountAction(){
        Mava_Application::set('seo/title', __('active_email_sent'));
        $uid = Mava_Url::getParam('uid');
        $hash_email = Mava_Url::getParam('hash_email');
        if($uid > 0 && $hash_email != ""){
            $userModel = $this->_getUserModel();
            $user = $userModel->getUserById($uid, false);
            if($user){
                if($user['is_active'] == 1){
                    Mava_Application::set('seo/title', __('account_activated'));
                    return $this->responseView('Mava_View_AccountActivated');
                }else{
                    if($hash_email == md5($uid .'_'. $user['email'])){
                        if($user['active_code'] == ''){
                            $active_code = md5($user['email'] .'_'. time());
                            $userDW = $this->_getUserDataWriter();
                            $userDW->setExistingData($uid);
                            $userDW->set('active_code', $active_code);
                            $userDW->save();
                            $user['active_code'] = $active_code;
                        }
                        $emailQueueModel = $this->_getEmailQueueModel();
                        $emailQueue = $emailQueueModel->getSingleEmailQueue(Mava_Model_EmailQueue::TYPE_ACTIVE, $user['email']);
                        if($emailQueue && $emailQueue['queue_id'] > 0) {
                            $email_queue_id = $emailQueue['queue_id'];
                        }else{
                            $emailQueueDW = $this->_getEmailQueueDataWriter();
                            $emailQueueDW->bulkSet(array(
                                'type' => Mava_Model_EmailQueue::TYPE_ACTIVE,
                                'email' => $user['email'],
                                'content' => json_encode(array(
                                    'title' => __('email_active_account_title'),
                                    'body' => __('email_active_account_body',array(
                                        'name' => $user['custom_title'],
                                        'email' => $user['email'],
                                        'phone' => $user['phone'],
                                        'link' => Mava_Url::getPageLink('active_account', array(
                                            'code' => $user['active_code'],
                                            'uid' => $uid
                                        ))
                                    ))
                                )),
                                'created_date' => time()
                            ));
                            $emailQueueDW->save();
                            $email_queue_id = $emailQueueDW->get('queue_id');
                        }
                        return $this->responseView('Mava_View_ActiveEmailSent', array(
                            'email_queue_id' => $email_queue_id,
                            'email' => $user['email'],
                            'uid' => $uid,
                            'hash_email' => $hash_email
                        ));
                    }else{
                        return $this->responseError(__('can_not_resend_active_mail'), Mava_Error::INVALID_REQUEST);
                    }
                }
            }else{
                return $this->responseError(__('account_not_existed'), Mava_Error::ACCESS_DENIED);
            }
        }else{
            return $this->responseError(__('can_not_resend_active_mail'), Mava_Error::INVALID_REQUEST);
        }
    }

    public function resendActivePhoneAction(){
        //TODO gửi lại mã kích hoạt qua sms
        die('todo');
    }

    public function _doLogout(){
        $userModel = $this->_getUserModel();
        $userModel->logout();
        return true;
    }

    /**
     * @return Mava_Model_User
     */
    protected function _getUserModel(){
        return $this->getModelFromCache('Mava_Model_User');
    }

    /**
     * @return Mava_DataWriter_EmailQueue
     * @throws Mava_Exception
     */
    protected function _getEmailQueueDataWriter(){
        return Mava_DataWriter::create('Mava_DataWriter_EmailQueue');
    }

    /**
     * @return Mava_Model_EmailQueue
     * @throws Mava_Exception
     */
    protected function _getEmailQueueModel(){
        return Mava_DataWriter::create('Mava_Model_EmailQueue');
    }

    /**
     * @return Mava_DataWriter_User
     * @throws Mava_Exception
     */
    protected function _getUserDataWriter(){
        return Mava_DataWriter::create('Mava_DataWriter_User');
    }

    /**
     * @return Mava_DataWriter_PhoneQueue
     * @throws Mava_Exception
     */
    protected function _getPhoneQueueDataWriter(){
        return Mava_DataWriter::create('Mava_DataWriter_PhoneQueue');
    }

    /**
     * @return Mava_DataWriter_AccountActiveQueue
     * @throws Mava_Exception
     */
    protected function _getActiveQueueDataWriter(){
        return Mava_DataWriter::create('Mava_DataWriter_AccountActiveQueue');
    }
}