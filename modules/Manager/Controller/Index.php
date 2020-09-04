<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 5/10/2019
 * Time: 2:01 PM
 */

class Manager_Controller_Index extends Manager_Controller
{
    public function dashboardAction()
    {
        Mava_Application::set('seo/title', __('Bảng điều khiển'));
        Mava_Application::set('menu_selected', 'dashboard');
        $statisticalUser = $this->_getManagerModel()->getStatisticalUser();
        $statisticalCoin = $this->_getManagerModel()->getStatisticalCoin();
        $viewParams = [
			'user' => $statisticalUser,
			'coin' => $statisticalCoin,
        ];
        return $this->responseView('Manager_View_Dashboard', $viewParams);
    }

    public function userAction()
    {
        Mava_Application::set('seo/title', __('Danh sách thành viên'));
        Mava_Application::set('menu_selected', 'user');

        $searchTerm   = Mava_Url::getParam('q');
        $page         = max((int) Mava_Url::getParam('page'), 1);
        $limit        = 10;
        $skip         = ($page - 1) * $limit;
        $sortBy       =  Mava_Url::getParam('sort_by');
        $sortDir      =  Mava_Url::getParam('sort_dir');
        $users   = $this->_getManagerModel()->getListUser($skip, $limit, $searchTerm, $sortBy, $sortDir);
        $viewParams = [
            'searchTerm' => $searchTerm,
            'page' => $page,
            'limit' => $limit,
            'total' => $users['total'],
            'users' => $users['rows'],
        ];
        return $this->responseView('Manager_View_ListUser', $viewParams);
    }

    public function userVipAction()
    {
        Mava_Application::set('seo/title', __('Thành viên VIP'));
        Mava_Application::set('menu_selected', 'user');

        $searchTerm   = Mava_Url::getParam('q');
        $page         = max((int) Mava_Url::getParam('page'), 1);
        $limit        = 10;
        $skip         = ($page - 1) * $limit;
        $sortBy       =  Mava_Url::getParam('sort_by');
        $sortDir      =  Mava_Url::getParam('sort_dir');
        $users   = $this->_getManagerModel()->getUserVip($skip, $limit, $searchTerm, $sortBy, $sortDir);
        $viewParams = [
            'searchTerm' => $searchTerm,
            'page' => $page,
            'limit' => $limit,
            'total' => $users['total'],
            'users' => $users['rows'],
        ];
        return $this->responseView('Manager_View_ListVip', $viewParams);
    }

    public function userSupervipAction()
    {
        Mava_Application::set('seo/title', __('Thành viên SUPERVIP'));
        Mava_Application::set('menu_selected', 'user');

        $searchTerm   = Mava_Url::getParam('q');
        $page         = max((int) Mava_Url::getParam('page'), 1);
        $limit        = 10;
        $skip         = ($page - 1) * $limit;
        $sortBy       =  Mava_Url::getParam('sort_by');
        $sortDir      =  Mava_Url::getParam('sort_dir');
        $users   = $this->_getManagerModel()->getUserSupervip($skip, $limit, $searchTerm, $sortBy, $sortDir);
        $viewParams = [
            'searchTerm' => $searchTerm,
            'page' => $page,
            'limit' => $limit,
            'total' => $users['total'],
            'users' => $users['rows'],
        ];
        return $this->responseView('Manager_View_ListSupervip', $viewParams);
    }

    public function chargeCoinAction()
    {
        Mava_Application::set('seo/title', __('Danh sách nạp coin'));
        Mava_Application::set('menu_selected', 'coin_charge');

        $searchTerm   = Mava_Url::getParam('q');
        $page         = max((int) Mava_Url::getParam('page'), 1);
        $limit        = 10;
        $skip         = ($page - 1) * $limit;
        $sortBy       =  Mava_Url::getParam('sort_by');
        $sortDir      =  Mava_Url::getParam('sort_dir');
        $logs   = $this->_getManagerModel()->getChargeCoin($skip, $limit, $searchTerm, $sortBy, $sortDir);
        $viewParams = [
            'searchTerm' => $searchTerm,
            'page' => $page,
            'limit' => $limit,
            'total' => $logs['total'],
            'logs' => $logs['rows'],
        ];
        return $this->responseView('Manager_View_ChargeCoin', $viewParams);
    }

    public function consumeCoinAction()
    {
        Mava_Application::set('seo/title', __('Danh sách dùng coin'));
        Mava_Application::set('menu_selected', 'coin_consume');

        $searchTerm   = Mava_Url::getParam('q');
        $page         = max((int) Mava_Url::getParam('page'), 1);
        $limit        = 10;
        $skip         = ($page - 1) * $limit;
        $sortBy       =  Mava_Url::getParam('sort_by');
        $sortDir      =  Mava_Url::getParam('sort_dir');
        $arrTypeLog = [
            'nhansovip' => 'Nhận số VIP',
            'nhantipbongda' => 'Nhận TIP bóng đá',
            'muavip' => 'Mua VIP',
        ];
        $logs   = $this->_getManagerModel()->getConsumeCoin($skip, $limit, $searchTerm, $sortBy, $sortDir);
        $viewParams = [
            'searchTerm' => $searchTerm,
            'page' => $page,
            'limit' => $limit,
            'arrTypeLog' => $arrTypeLog,
            'total' => $logs['total'],
            'logs' => $logs['rows'],
        ];
        return $this->responseView('Manager_View_ConsumeCoin', $viewParams);
    }

    public function upgradeVipAction(){
        Mava_Application::set('seo/title', __('Nâng cấp VIP'));
        Mava_Application::set('menu_selected', 'upgrade');
        $userId = (int)Mava_Url::getParam('user_id');
        $num = (int)Mava_Url::getParam('num');
        if (Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['user_id']) || trim($postData['user_id']) == '' || (int)$postData['user_id'] <= 0){
                $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng ID thành viên')
                ];
            }elseif(!isset($postData['num']) || trim($postData['num']) == '' || (int)$postData['num'] <= 0){
                    $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng số ngày VIP')
                ];
            }else {
                Mava_Url::redirect(Mava_Url::getPageLink('manager/confirm-upgrade-vip?user_id='.$postData['user_id'].'&num='.$postData['num']));
            }
        }
        $viewParams['userId'] = $userId;
        $viewParams['num'] = $num;
        return $this->responseView('Manager_View_UpgradeVip', $viewParams);
    }

    public function confirmUpgradeVipAction(){
        Mava_Application::set('seo/title', __('Xác nhận nâng cấp VIP'));
        Mava_Application::set('menu_selected', 'upgrade');
        $userId = (int)Mava_Url::getParam('user_id');
        $num = (int)Mava_Url::getParam('num');
        if (Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['user_id']) || trim($postData['user_id']) == '' || (int)$postData['user_id'] <= 0){
                $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng ID thành viên')
                ];
            }elseif(!isset($postData['num']) || trim($postData['num']) == '' || (int)$postData['num'] <= 0){
                    $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng số ngày VIP')
                ];
            }else {
                $result = $this->_getManagerModel()->upgradeVip($postData);
                if($result['error'] == 0){
                       Mava_Url::redirect(Mava_Url::getPageLink('manager/finish-upgrade-vip?user_id='.$postData['user_id']));
                }else {
                    $viewParams = [
                        'error' => 1,
                        'message' => $result['message']
                    ];
                }
            }
        }
        $user = $this->_getManagerModel()->getUserById($userId);
        $viewParams['userId'] = $userId;
        $viewParams['num'] = $num;
        $viewParams['user'] = $user;
        return $this->responseView('Manager_View_ConfirmUpgradeVip', $viewParams);
    }

    public function finishUpgradeVipAction(){
        Mava_Application::set('seo/title', __('Nâng cấp VIP thành công'));
        Mava_Application::set('menu_selected', 'upgrade');
        $userId = (int)Mava_Url::getParam('user_id');
        $user = $this->_getManagerModel()->getUserById($userId);
        $viewParams['user'] = $user;
        return $this->responseView('Manager_View_FinishUpgradeVip', $viewParams);
    }

    public function upgradeSupervipAction(){
        Mava_Application::set('seo/title', __('Nâng cấp SUPERVIP'));
        Mava_Application::set('menu_selected', 'upgrade');
        $userId = (int)Mava_Url::getParam('user_id');
        if (Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['user_id']) || trim($postData['user_id']) == '' || (int)$postData['user_id'] <= 0){
                $viewParams = [
                    'error' => 1,
                    'message' =>  __('Vui lòng nhập đúng ID thành viên')
                ];
            }else {
                Mava_Url::redirect(Mava_Url::getPageLink('manager/confirm-upgrade-supervip?user_id='.$postData['user_id']));
            }
        }
        $viewParams['userId'] = $userId;
        return $this->responseView('Manager_View_UpgradeSupervip', $viewParams);
    }

     public function confirmUpgradeSupervipAction(){
        Mava_Application::set('seo/title', __('Xác nhận nâng cấp Supervip'));
        Mava_Application::set('menu_selected', 'upgrade');
        $userId = (int)Mava_Url::getParam('user_id');
        if (Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['user_id']) || trim($postData['user_id']) == '' || (int)$postData['user_id'] <= 0){
                $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng ID thành viên')
                ];
            }else{
                $result = $this->_getManagerModel()->upgradeSupervip($postData);
                if($result['error'] == 0){
                       Mava_Url::redirect(Mava_Url::getPageLink('manager/finish-upgrade-supervip?user_id='.$postData['user_id']));
                }else {
                    $viewParams = [
                        'error' => 1,
                        'message' => $result['message']
                    ];
                }
            }
        }
        $user = $this->_getManagerModel()->getUserById($userId);
        $viewParams['userId'] = $userId;
        $viewParams['user'] = $user;
        return $this->responseView('Manager_View_ConfirmUpgradeSupervip', $viewParams);
    }

    public function finishUpgradeSupervipAction(){
        Mava_Application::set('seo/title', __('Nâng cấp Supervip thành công'));
        Mava_Application::set('menu_selected', 'upgrade');
        $userId = (int)Mava_Url::getParam('user_id');
        $user = $this->_getManagerModel()->getUserById($userId);
        $viewParams['user'] = $user;
        return $this->responseView('Manager_View_FinishUpgradeSupervip', $viewParams);
    }

    public function activityAction(){
        Mava_Application::set('seo/title', __('Hoạt động'));
        Mava_Application::set('menu_selected', 'activity');
        $page         = max((int) Mava_Url::getParam('page'), 1);
        $limit        = 10;
        $skip         = ($page - 1) * $limit;
        $activity = $this->_getManagerActivityModel()->getActivities($skip, $limit);
        $arrType = [
            'nangvip' => "Nâng Vip",
            'nangsupervip' => "Nâng Supervip",
            'congcoin' => "Cộng coin",
            'datlaimatkhau' => "Đặt lại mật khẩu",
            'khoataikhoan' => "Khóa tài khoản"
        ];
        $accounts = Mava_Application::get('config/manager');
        $arrAcc = [];
        foreach ($accounts as $acc) {
            $arrAcc[$acc['id']] = $acc['username'];
        }
        $error = Mava_Url::getParam('error');
        $viewParams = [
            'arrAcc' => $arrAcc,
            'activity' => $activity['rows'],
            'total' => $activity['total'],
            'page' => $page,
            'limit' => $limit,
            'arrType' => $arrType,
            'error' => $error
        ];
        return $this->responseView('Manager_View_Activity', $viewParams);
    }

    public function rollBackActivityAction(){
        $activityId = (int)Mava_Url::getParam('id');
        $result = $this->_getManagerModel()->rollBackActivity($activityId);
        if($result['error'] == 0){
           Mava_Url::redirect(Mava_Url::getPageLink('manager/activity',['error' => 0]));
        }else {
            Mava_Url::redirect(Mava_Url::getPageLink('manager/activity',['error' => 1]));
        }
    }

    public function increaseCoinAction(){
        Mava_Application::set('seo/title', __('Cộng COIN'));
        $userId = (int)Mava_Url::getParam('user_id');
        $coin = (int)Mava_Url::getParam('coin');
        if (Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['user_id']) || trim($postData['user_id']) == '' || (int)$postData['user_id'] <= 0){
                $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng ID thành viên')
                ];
            }elseif(!isset($postData['coin']) || trim($postData['coin']) == '' || (int)$postData['coin'] <= 0){
                    $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng số coin')
                ];
            }else {
                Mava_Url::redirect(Mava_Url::getPageLink('manager/confirm-increase-coin?user_id='.$postData['user_id'].'&coin='.$postData['coin']));
            }
        }
        $viewParams['userId'] = $userId;
        $viewParams['coin'] = $coin;
        return $this->responseView('Manager_View_IncreaseCoin', $viewParams);
    }

    public function confirmIncreaseCoinAction(){
        Mava_Application::set('seo/title', __('Xác nhận cộng COIN'));
        $userId = (int)Mava_Url::getParam('user_id');
        $coin = (int)Mava_Url::getParam('coin');
        if (Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['user_id']) || trim($postData['user_id']) == '' || (int)$postData['user_id'] <= 0){
                $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng ID thành viên')
                ];
            }elseif(!isset($postData['coin']) || trim($postData['coin']) == '' || (int)$postData['coin'] <= 0){
                    $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng số coin')
                ];
            }else {
                $result = $this->_getManagerModel()->increaseCoin($postData);
                if($result['error'] == 0){
                       Mava_Url::redirect(Mava_Url::getPageLink('manager/finish-increase-coin?user_id='.$postData['user_id']));
                }else {
                    $viewParams = [
                        'error' => 1,
                        'message' => $result['message']
                    ];
                }
            }
        }
        $user = $this->_getManagerModel()->getUserById($userId);
        $viewParams['userId'] = $userId;
        $viewParams['coin'] = $coin;
        $viewParams['user'] = $user;
        return $this->responseView('Manager_View_ConfirmIncreaseCoin', $viewParams);
    }

    public function finishIncreaseCoinAction(){
        Mava_Application::set('seo/title', __('Cộng COIN thành công'));
        $userId = (int)Mava_Url::getParam('user_id');
        $user = $this->_getManagerModel()->getUserById($userId);
        $viewParams['user'] = $user;
        return $this->responseView('Manager_View_FinishIncreaseCoin', $viewParams);
    }

    public function resetPasswordAction(){
        Mava_Application::set('seo/title', __('Đặt lại mật khẩu'));
        $userId = (int)Mava_Url::getParam('user_id');
        $password = (int)Mava_Url::getParam('password');
        if (Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['user_id']) || trim($postData['user_id']) == '' || (int)$postData['user_id'] <= 0){
                $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng ID thành viên')
                ];
            }elseif(!isset($postData['password']) || trim($postData['password']) == '' || strlen($postData['password']) < Mava_Application::get('config/passwordMinLength') ||
                strlen($postData['password']) > Mava_Application::get('config/passwordMaxLength')){
                    $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng định dạng mật khẩu mới ').'('.Mava_Application::get('config/passwordMinLength').' - '.Mava_Application::get('config/passwordMaxLength').' kí tự)'
                ];
            }else {
                Mava_Url::redirect(Mava_Url::getPageLink('manager/confirm-reset-password?user_id='.$postData['user_id'].'&password='.$postData['password']));
            }
        }
        $viewParams['userId'] = $userId;
        $viewParams['password'] = $password;
        return $this->responseView('Manager_View_ResetPassword', $viewParams);
    }

    public function confirmResetPasswordAction(){
        Mava_Application::set('seo/title', __('Xác nhận đặt lại mật khẩu'));
        $userId = (int)Mava_Url::getParam('user_id');
        $password = (int)Mava_Url::getParam('password');
        if (Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['user_id']) || trim($postData['user_id']) == '' || (int)$postData['user_id'] <= 0){
                $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng ID thành viên')
                ];
            }elseif(!isset($postData['password']) || trim($postData['password']) == '' || strlen($postData['password']) < Mava_Application::get('config/passwordMinLength') ||
                strlen($postData['password']) > Mava_Application::get('config/passwordMaxLength')){
                    $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng định dạng mật khẩu mới ').'('.Mava_Application::get('config/passwordMinLength').' - '.Mava_Application::get('config/passwordMaxLength').' kí tự)'
                ];
            }else {
                $result = $this->_getManagerModel()->resetPassword($postData);
                if($result['error'] == 0){
                       Mava_Url::redirect(Mava_Url::getPageLink('manager/finish-reset-password?user_id='.$postData['user_id']));
                }else {
                    $viewParams = [
                        'error' => 1,
                        'message' => $result['message']
                    ];
                }
            }
        }
        $user = $this->_getManagerModel()->getUserById($userId);
        $viewParams['userId'] = $userId;
        $viewParams['password'] = $password;
        $viewParams['user'] = $user;
        return $this->responseView('Manager_View_ConfirmResetPassword', $viewParams);
    }

    public function finishResetPasswordAction(){
        Mava_Application::set('seo/title', __('Đặt lại mật khẩu thành công'));
        $userId = (int)Mava_Url::getParam('user_id');
        $user = $this->_getManagerModel()->getUserById($userId);
        $viewParams['user'] = $user;
        return $this->responseView('Manager_View_FinishResetPassword', $viewParams);
    }

    public function lockAccountAction(){
        Mava_Application::set('seo/title', __('Khóa tài khoản'));
        $userId = (int)Mava_Url::getParam('user_id');
        $day = (int)Mava_Url::getParam('day');
        if (Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['user_id']) || trim($postData['user_id']) == '' || (int)$postData['user_id'] <= 0){
                $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng ID thành viên')
                ];
            }elseif(!isset($postData['day']) || trim($postData['day']) == '' || (int)$postData['day'] <= 0){
                    $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng số ngày để khóa')
                ];
            }else {
                Mava_Url::redirect(Mava_Url::getPageLink('manager/confirm-lock-account?user_id='.$postData['user_id'].'&day='.$postData['day']));
            }
        }
        $viewParams['userId'] = $userId;
        $viewParams['day'] = $day;
        return $this->responseView('Manager_View_LockAccount', $viewParams);
    }

    public function confirmLockAccountAction(){
        Mava_Application::set('seo/title', __('Xác nhận khóa tài khoản'));
        $userId = (int)Mava_Url::getParam('user_id');
        $day = (int)Mava_Url::getParam('day');
        if (Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['user_id']) || trim($postData['user_id']) == '' || (int)$postData['user_id'] <= 0){
                $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng ID thành viên')
                ];
            }elseif(!isset($postData['day']) || trim($postData['day']) == '' || (int)$postData['day'] <= 0){
                    $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng số ngày để khóa')
                ];
            }else {
                $result = $this->_getManagerModel()->lockAccount($postData);
                if($result['error'] == 0){
                       Mava_Url::redirect(Mava_Url::getPageLink('manager/finish-lock-account?user_id='.$postData['user_id']));
                }else {
                    $viewParams = [
                        'error' => 1,
                        'message' => $result['message']
                    ];
                }
            }
        }
        $user = $this->_getManagerModel()->getUserById($userId);
        $viewParams['userId'] = $userId;
        $viewParams['day'] = $day;
        $viewParams['user'] = $user;
        return $this->responseView('Manager_View_ConfirmLockAccount', $viewParams);
    }

    public function finishLockAccountAction(){
        Mava_Application::set('seo/title', __('Khóa tài khoản thành công'));
        $userId = (int)Mava_Url::getParam('user_id');
        $user = $this->_getManagerModel()->getUserById($userId);
        $viewParams['user'] = $user;
        return $this->responseView('Manager_View_FinishLockAccount', $viewParams);
    }

    public function listFootballTipAction(){
        Mava_Application::set('seo/title', __('Danh sách kèo'));
        Mava_Application::set('menu_selected', 'football_tip');
        $searchTerm   = Mava_Url::getParam('q');
        $page         = max((int) Mava_Url::getParam('page'), 1);
        $limit        = 10;
        $skip         = ($page - 1) * $limit;
        $sortBy       =  Mava_Url::getParam('sort_by');
        $sortDir      =  Mava_Url::getParam('sort_dir');

        $tips   = $this->_getManagerModel()->getListFootballTip($skip, $limit, $searchTerm, $sortBy, $sortDir);
        $viewParams = [
            'searchTerm' => $searchTerm,
            'page' => $page,
            'limit' => $limit,
            'total' => $tips['total'],
            'tips' => $tips['rows'],
        ];
        return $this->responseView('Manager_View_ListFootballTip', $viewParams);
    }

    public function listMatchDayAction(){
        Mava_Application::set('seo/title', __('Danh sách trận đấu'));
        Mava_Application::set('menu_selected', 'football_tip');

        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');

        // call API to get result
        $data = [
            'token' => $token,
            'leagueIds' => [],
            'date' => date('d/m/Y', time())
        ];
        $result = Mava_API::call('football/match-day', $data);
        if($result['error'] == 0){
            $viewParams['matchs'] = $result['data'];
        }else{
            $viewParams['matchs'] = [];
        }
        $viewParams['date'] = date('d/m/Y', time());
        return $this->responseView('Manager_View_ListMatchDay', $viewParams);
    }

    public function footballTipAction(){
        Mava_Application::set('seo/title', __('Nhập kèo bóng đá'));
        Mava_Application::set('menu_selected', 'football_tip');
        $fixture_id = (int)Mava_Url::getParam('fixture');
        $fixtureData = [];
        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
        // call API to get result
        $data = [
            'token' => $token,
            'leagueIds' => [],
            'date' => date('d/m/Y', time())
        ];
        $result = Mava_API::call('football/match-day', $data);

        if($result['error'] == 0){
            $matchs = $result['data'];
        }else{
            $matchs = [];
        }
        if(count($matchs) > 0){
            foreach ($matchs as $match){
                if(isset($match['fixtures']) && count($match['fixtures']) > 0){
                    foreach ($match['fixtures'] as $fixture){
                        if($fixture['fixture_id'] == $fixture_id && $fixture['statusShort'] == 'NS'){
                            $fixtureData = [
                                'fixture_id' => $fixture['fixture_id'],
                                'time' => date('H:i d-m-Y' , $fixture['event_timestamp']),
                                'timestamp' => $fixture['event_timestamp'],
                                'home' => $fixture['homeTeam']['team_name'],
                                'away' => $fixture['awayTeam']['team_name'],
                            ];
                        }
                    }
                }
            }
        }
        // reset $_POST sau khi call api
        unset($_POST['source']);
        $viewParams = [];
        if (Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['pack']) || trim($postData['pack']) == '' || !in_array($postData['pack'], [1,2,3])){
                $viewParams['error'] = 1;
                $viewParams['message'] = __('Vui lòng nhập đúng gói');
            }elseif(!isset($postData['tip_date']) || trim($postData['tip_date']) == ''){
                $viewParams['error'] = 1;
                $viewParams['message'] = __('Vui lòng nhập đúng ngày');
            }elseif(!isset($postData['taixiu']) || trim($postData['taixiu']) == ''
                    || !isset($postData['num']) || trim($postData['num']) == ''
                    || !isset($postData['ft']) || trim($postData['ft']) == ''){
                $viewParams['error'] = 1;
                $viewParams['message'] = __('Vui lòng nhập đúng đủ thông tin kèo');
            }elseif(count($fixtureData) <= 0){
                $viewParams['error'] = 1;
                $viewParams['message'] = __('Trận đấu không tồn tại hoặc đã và đang diễn ra');
            }else{
                $tip = [
                    'fixture_id' => (int)$fixtureData['fixture_id'],
                    'time' => $fixtureData['time'],
                    'timestamp' => $fixtureData['timestamp'],
                    'home' => $fixtureData['home'],
                    'away' => $fixtureData['away'],
                    'taixiu' => $postData['taixiu'],
                    'num' => (int)$postData['num'],
                    'ft' => $postData['ft']
                ];
                $checkExist = $this->_getManagerModel()->getFootballTipOfFixture($postData['pack'], $postData['tip_date'], (int)$fixtureData['fixture_id']);
                if($checkExist){
                    $viewParams['error'] = 1;
                    $viewParams['message'] = __('Kèo này đã được thêm vào');
                }else{
                    $result = $this->_getManagerModel()->createFootballTip((int)$postData['pack'], $postData['tip_date'], $tip);
                    if($result['error'] == 0){
                        Mava_Url::redirect(Mava_Url::getPageLink('manager/list-football-tip'));
                    }else {
                        $viewParams['error'] = 1;
                        $viewParams['message'] = $result['message'];
                    }
                }
            }
        }
        $viewParams['fixture_id'] = $fixture_id;
        $viewParams['fixtureData'] = $fixtureData;
        return $this->responseView('Manager_View_FootballTip', $viewParams);
    }

    public function listLotoTipAction(){
        Mava_Application::set('seo/title', __('Danh sách lô tô tip'));
        Mava_Application::set('menu_selected', 'loto_tip');
        $searchTerm   = Mava_Url::getParam('q');
        $page         = max((int) Mava_Url::getParam('page'), 1);
        $limit        = 10;
        $skip         = ($page - 1) * $limit;
        $sortBy       =  Mava_Url::getParam('sort_by');
        $sortDir      =  Mava_Url::getParam('sort_dir');
        $all_simple_province = get_all_simple_province();
        $province = [];
        foreach ($all_simple_province as $prv) {
            $province[$prv['code']] = $prv['title'];
        }
        $tips   = $this->_getManagerModel()->getListLotoTip($skip, $limit, $searchTerm, $sortBy, $sortDir);
        $viewParams = [
            'searchTerm' => $searchTerm,
            'page' => $page,
            'limit' => $limit,
            'province' => $province,
            'total' => $tips['total'],
            'tips' => $tips['rows'],
        ];
        return $this->responseView('Manager_View_ListLotoTip', $viewParams);
    }

    public function lotoTipAction(){
        Mava_Application::set('seo/title', __('Thêm lô tô tip'));
        Mava_Application::set('menu_selected', 'loto_tip');
        $viewParams = [];
        if (Mava_Url::isPost()) {
            $postData = Mava_Url::getParams();
            if(!isset($postData['pack']) || trim($postData['pack']) == '' || !in_array($postData['pack'], array(1,2,3))) {
                $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng chọn đúng gói')
                ];
            }elseif(!isset($postData['region_code']) || trim($postData['region_code']) == '') {
                $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng chọn tỉnh thành')
                ];
            }elseif(!isset($postData['tip_date']) || trim($postData['tip_date']) == '') {
                $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng ngày')
                ];
            } elseif(!isset($postData['num_1']) || trim($postData['num_1']) == ''
                || !isset($postData['num_2']) || trim($postData['num_2']) == ''
                || !isset($postData['num_3']) || trim($postData['num_3']) == ''){
                $viewParams = [
                    'error' => 1,
                    'message' => __('Vui lòng nhập đúng, đầy đủ số')
                ];
            }else {
                $result = $this->_getManagerModel()->createLotoTip($postData);
                if($result['error'] == 0){
                    Mava_Url::redirect(Mava_Url::getPageLink('manager/list-loto-tip'));
                }else {
                    $viewParams = [
                        'error' => 1,
                        'message' => $result['message']
                    ];
                }
            }
        }
        $viewParams['default_date'] = date('d-m-Y', time());
        $default_province = Mava_Application::getConfig('loto_schedule/T'. (date('w')+1));
        $tt = $default_province['tt'];
        unset($default_province['tt']);
        array_unshift($default_province, $tt);
        $viewParams['default_province'] = $default_province;

        return $this->responseView('Manager_View_LotoTip', $viewParams);
    }

    protected function _getManagerModel(){
        return $this->getModelFromCache('Manager_Model_Manager');
    }

    protected function _getManagerActivityModel(){
        return $this->getModelFromCache('Manager_Model_ManagerActivity');
    }
}