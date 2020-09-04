<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 5/10/2019
 * Time: 1:58 PM
 */

class Manager_Controller_Manager extends Mava_Controller
{
    public function loginAction(){
        Mava_Application::set('seo/title', __('Đăng nhập hệ thống vận hành'));
        $viewParams = [];
        $managerId = Mava_Application::get("session")->get('manager_id');
        if((int)$managerId > 0){
            Mava_Url::redirect(Mava_Url::getPageLink('manager/dashboard'));
        }

        if(Mava_Url::isPost()){
            $postData                = Mava_Url::getParams();
            $viewParams['username']  = $postData['username'];
            $viewParams['password']  = $postData['password'];

            if(strlen(trim($postData['username'])) == 0){
                $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập tài khoản đăng nhập')
                ];
            }elseif(strlen($postData['password']) == 0){
                $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập mật khẩu')
                ];
            }else{
                $checkLogin = $this->_doLogin($postData['username'],$postData['password']);
                if($checkLogin['error'] == 0){
                    Mava_Url::redirect(Mava_Url::getPageLink('manager/dashboard'));
                }else {
                    $viewParams = [
                        'error' => 1,
                        'message' => $checkLogin['message']
                    ];
                }
            }
        }

        return $this->responseView('Manager_View_Login',$viewParams,'manager_mini');
    }

    public function logoutAction(){
        $this->_doLogout();
        Mava_Url::redirect(Mava_Url::getPageLink('manager/login'));
    }

    public function _doLogin($username, $password){
        $checkLogin = $this->_getManagerModel()->login($username, $password);
        if($checkLogin['error'] == 0){
            Mava_Application::get("session")->set('manager_id',$checkLogin['manager']['id']);
        }
        return $checkLogin;
    }

    public function _doLogout(){
        Mava_Application::get("session")->set('manager_id',0);
        return true;
    }

    protected function _getManagerModel(){
        return $this->getModelFromCache('Manager_Model_Manager');
    }

}