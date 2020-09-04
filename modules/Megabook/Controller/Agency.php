<?php
class Megabook_Controller_Agency extends Mava_Controller {
    public function __construct(){
        if(!is_login()){
            Mava_Url::redirectLogin(__('please_login_first'));
        }
        $agency_id = Mava_Url::getParam('agency_id');
        $agencyModel = $this->_getAgencyModel();
        if(
            Mava_Url::getCurrentAddress() != Mava_Url::getPageLink('dashboard')
            && Mava_Url::getCurrentAddress() != Mava_Url::getPageLink('dang-ky-dai-ly')
        ){
            if($agency_id > 0 && $agency = $agencyModel->getById($agency_id)){
                Mava_Application::set('agency', $agency);
                if(
                    in_array($agency['status'], array(
                            Megabook_DataWriter_Agency::STATUS_SUSPENDED,
                            Megabook_DataWriter_Agency::STATUS_DELETED
                        ))
                    && Mava_Url::getCurrentAddress() != Mava_Url::getPageLink('dashboard/'. $agency['id'])
                ){
                    Mava_Url::redirect(Mava_Url::getPageLink('dashboard/'. $agency['id']));
                }
            }else{
                throw new Mava_Exception(__('agency_not_found'));
            }
        }

    }

    public function indexAction(){
        $agency = Mava_Application::get('agency');
        if($agency){
            if($agency['status'] == Megabook_DataWriter_Agency::STATUS_SUSPENDED){
                return $this->responseError(__('agency_suspended_notice'), Mava_Error::INVALID_REQUEST);
            }elseif($agency['status'] == Megabook_DataWriter_Agency::STATUS_DELETED){
                return $this->responseError(__('agency_deleted_notice'), Mava_Error::INVALID_REQUEST);
            }else{
                Mava_Application::set('selected_menu', 'overview');
                Mava_Application::set('seo/title', __('dashboard_of_x_agency',array('name' => $agency['title'])));
                $linkStatsModel = $this->_getLinkStatsModel();
                $orderModel = $this->_getOrderModel();
                $orders = $orderModel->getList(0, 10, '', '', $agency['id']);
                return $this->responseView('Megabook_View_Agency_Index', array(
                    'orders' => $orders['rows'],
                    'agency' => $agency,
                    'visitor_count' => $linkStatsModel->countVisitorByAgency($agency['id']),
                    'total_order_count' => $orderModel->countTotalOrderByAgency($agency['id']),
                    'order_count' => $orderModel->countOrderByAgency($agency['id']),
                    'revenue_total' => $orderModel->countRevenueByAgency($agency['id']),
                ), 'dashboard');
            }
        }else{
            return $this->responseError(__('agency_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    /**
     * @return Product_Model_Order
     */
    protected function _getOrderModel(){
        return $this->getModelFromCache('Product_Model_Order');
    }

    /**
     * @return Megabook_Model_LinkStats
     */
    protected function _getLinkStatsModel(){
        return $this->getModelFromCache('Megabook_Model_LinkStats');
    }

    /**
     * @return Megabook_Model_Agency
     */
    protected function _getAgencyModel(){
        return $this->getModelFromCache('Megabook_Model_Agency');
    }

    public function settingsAction(){
        $agency = Mava_Application::get('agency');
        if($agency){
            Mava_Application::set('seo/title', __('settings') .' - '. $agency['title']);
            Mava_Application::set('selected_menu', 'settings');
            $viewParams = array(
                'error' => '',
                'success' => ''
            );
            if(Mava_Url::isPost()){
                $agency['title'] = Mava_Url::getParam('agencyTitle');
                $agency['agency_code'] = Mava_String::unsignString(Mava_Url::getParam('agencyCode'),'');
                $agency['email'] = Mava_Url::getParam('agencyEmail');
                $agency['phone'] = Mava_Url::getParam('agencyPhone');
                $agency['address'] = Mava_Url::getParam('agencyAddress');
                $agency['bank_name'] = Mava_Url::getParam('agencyBankName');
                $agency['bank_branch'] = Mava_Url::getParam('agencyBankBranch');
                $agency['bank_fullname'] = Mava_Url::getParam('agencyBankFullname');
                $agency['bank_id_string'] = Mava_Url::getParam('agencyBankIdString');
                $agencyModel = $this->_getAgencyModel();
                if(trim($agency['title']) == ''){
                    $viewParams['error'] = __('agency_title_empty');
                }elseif(trim($agency['agency_code']) == ''){
                    $viewParams['error'] = __('agency_code_empty');
                }elseif(strlen(trim($agency['agency_code'])) > 50){
                    $viewParams['error'] = __('agency_code_length_invalid');
                }elseif($agencyModel->hasCode(trim($agency['agency_code']), $agency['id'])){
                    $viewParams['error'] = __('agency_code_existed');
                }else{
                    $agencyDW = $this->_getAgencyDataWriter();
                    $agencyDW->setExistingData($agency['id']);
                    $agencyDW->bulkSet(array(
                        'title' => $agency['title'],
                        'agency_code' => $agency['agency_code'],
                        'email' => $agency['email'],
                        'phone' => $agency['phone'],
                        'address' => $agency['address'],
                        'bank_name' => $agency['bank_name'],
                        'bank_branch' => $agency['bank_branch'],
                        'bank_fullname' => $agency['bank_fullname'],
                        'bank_id_string' => $agency['bank_id_string']
                    ));
                    if($agencyDW->save()){
                        Mava_Application::set('agency', $agency);
                        $viewParams['success'] = __('agency_updated');
                    }
                }
            }
            $viewParams['agency'] = $agency;
            return $this->responseView('Megabook_View_Agency_Settings', $viewParams, 'dashboard');
        }else{
            return $this->responseError(__('agency_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    public function ordersAction(){
        $agency = Mava_Application::get('agency');
        if($agency){
            Mava_Application::set('selected_menu', 'orders');
            $page = max((int)Mava_Url::getParam('page'), 1);
            Mava_Application::set('seo/title', __('orders') . ($page > 1?' - '. __('page_x', array('num' => $page)):''));
            $limit = 10;
            $skip = ($page-1)*$limit;
            $orderModel = $this->_getOrderModel();
            $orders = $orderModel->getList($skip, $limit, '', '', $agency['id']);
            return $this->responseView('Megabook_View_Agency_Orders', array(
                    'agency' => $agency,
                    'page' => $page,
                    'skip' => $skip,
                    'limit' => $limit,
                    'orders' => $orders['rows'],
                    'total' => $orders['total'],
                ), 'dashboard');
        }else{
            return $this->responseError(__('agency_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    public function withdrawAction(){
        $agency = Mava_Application::get('agency');
        if($agency){
            Mava_Application::set('selected_menu', 'withdraw');
            $page = max((int)Mava_Url::getParam('page'), 1);
            Mava_Application::set('seo/title', __('withdraw_request') . ($page > 1?' - '. __('page_x', array('num' => $page)):''));
            $limit = 10;
            $skip = ($page-1)*$limit;
            $withdrawRequestModel = $this->_getWithdrawRequestModel();
            $requests = $withdrawRequestModel->getList($skip, $limit, '', $agency['id']);
            $orderModel = $this->_getOrderModel();
            return $this->responseView('Megabook_View_Agency_WithdrawList', array(
                    'total_income' => $orderModel->countRevenueByAgency($agency['id']),
                    'withdraw_total_amount' => $withdrawRequestModel->countByAgency($agency['id']),
                    'agency' => $agency,
                    'page' => $page,
                    'skip' => $skip,
                    'limit' => $limit,
                    'requests' => $requests['items'],
                    'total' => $requests['total'],
                ), 'dashboard');
        }else{
            return $this->responseError(__('agency_not_found'), Mava_Error::NOT_FOUND);
        }
    }


    public function newWithdrawRequestAction(){
        $agency = Mava_Application::get('agency');
        if($agency){
            if($agency['status'] == Megabook_DataWriter_Agency::STATUS_PENDING){
                return $this->responseRedirect(Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/withdraw'));
            }
            Mava_Application::set('selected_menu', 'withdraw');
            Mava_Application::set('seo/title', __('send_withdraw_request'));
            $viewParams = array(
                'error' => '',
                'success' => '',
                'agency' => $agency
            );
            if(Mava_Url::isPost()){
                $withdrawAmount = (int)Mava_String::unsignString(Mava_Url::getParam('withdrawAmount'), '');
                $agency['bank_name'] = Mava_Url::getParam('agencyBankName');
                $agency['bank_branch'] = Mava_Url::getParam('agencyBankBranch');
                $agency['bank_fullname'] = Mava_Url::getParam('agencyBankFullname');
                $agency['bank_id_string'] = Mava_Url::getParam('agencyBankIdString');
                $minWithdraw = Mava_Application::getOptions()->affiliateWithdrawMinimum;
                if($withdrawAmount > $agency['balance']){
                    $viewParams['error'] = __('agency_balance_not_enough');
                }elseif($withdrawAmount < $minWithdraw){
                    $viewParams['error'] = __('withdraw_minimum_x', array('num' => Mava_String::price_format($minWithdraw)));
                }elseif($agency['bank_name'] == ''){
                    $viewParams['error'] = __('bank_name_empty');
                }elseif($agency['bank_branch'] == ''){
                    $viewParams['error'] = __('bank_branch_empty');
                }elseif($agency['bank_fullname'] == ''){
                    $viewParams['error'] = __('bank_fullname_empty');
                }elseif($agency['bank_id_string'] == ''){
                    $viewParams['error'] = __('bank_id_string_empty');
                }else{
                    // update bank info
                    $agencyDW = $this->_getAgencyDataWriter();
                    $agencyDW->setExistingData($agency['id']);
                    $agencyDW->bulkSet(array(
                            'bank_name' => $agency['bank_name'],
                            'bank_branch' => $agency['bank_branch'],
                            'bank_fullname' => $agency['bank_fullname'],
                            'bank_id_string' => $agency['bank_id_string']
                        ));
                    $agencyDW->save();
                    // sub agency balance + transaction
                    $transactionModel = $this->_getTransactionModel();
                    $checkSub = $transactionModel->sub($agency['id'], $withdrawAmount, __('user_send_withdraw_request'));
                    if($checkSub['status'] == 1){
                        // create withdraw record
                        $withdrawDW = $this->_getWithdrawRequestDataWriter();
                        $withdrawDW->bulkSet(array(
                                'agency_id' => $agency['id'],
                                'user_id' => Mava_Visitor::getUserId(),
                                'amount' => $withdrawAmount,
                                'agency_balance' => $agency['balance'],
                                'created_date' => time(),
                                'created_ip' => ip(),
                                'reject_reason' => '',
                                'status' => $withdrawDW::STATUS_PENDING
                            ));
                        if($withdrawDW->save()){
                            // send admin email
                            $email_notify = Mava_Application::getOptions()->emailReceiveOrderNotify;
                            $body  ='<h2>'.__('withdraw_request_info').'</h2>';
                            $body  .='<ul>
                                <li>'. __('agency_id') .': '. $agency['id'] .'</li>
                                <li>'. __('agency_title') .': '. $agency['title'] .'</li>
                                <li>'. __('withdraw_balance_before') .': '. Mava_String::price_format($agency['balance']) .'</li>
                                <li>'. __('withdraw_amount') .': <b>'. Mava_String::price_format($withdrawAmount) .'</b></li>
                                <li>'. __('request_time') .': '. date('d/m/Y H:i', time()) .'</li>
                                </ul>';
                            $body  .='<h2>'.__('agency_withdraw_bank_info').'</h2>';
                            $body  .='<ul>
                                <li>'. __('bank_name') .': '. $agency['bank_name'] .'</li>
                                <li>'. __('bank_branch') .': '. $agency['bank_branch'] .'</li>
                                <li>'. __('bank_fullname') .': '. $agency['bank_fullname'] .'</li>
                                <li>'. __('bank_id_string') .': '. $agency['bank_id_string'] .'</li>
                                </ul>';
                            $body .= '<p><a href="'. Mava_Url::getPageLink('admin/agency/withdraw-request') .'" style="display: inline-block;padding: 7px 15px;color: #FFF;background: #080;font-size: 13px;font-weight: bold;text-decoration: none;margin: 10px 0;">'. __('view_all_request') .'</a></p>';
                            $body .= '<span style="color: #FFF;display: none;">'. microtime() .'</span>';
                            $emailQueueDw = $this->_getEmailQueueDataWriter();
                            $emailQueueDw->bulkSet(array(
                                    'type' => Mava_Model_EmailQueue::TYPE_GENERAL,
                                    'email' => $email_notify,
                                    'content' => json_encode(array(
                                            'title' => __('_email_title_new_agency_withdraw_request', array('date' => date('d/m/Y',time()))),
                                            'body' => $body,
                                        )),
                                    'created_date' => time()
                                ));
                            $emailQueueDw->save();
                            return $this->responseView('Megabook_View_Agency_WithdrawCreateSuccess', array(
                                    'agency' => $agency
                                ), 'dashboard');
                        }else{
                            $viewParams['error'] = __('could_not_send_withdraw_request');
                        }
                    }else{
                        $viewParams['error'] = $checkSub['message'];
                    }
                }
            }
            return $this->responseView('Megabook_View_Agency_WithdrawCreate', $viewParams, 'dashboard');
        }else{
            return $this->responseError(__('agency_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    public function transactionsAction(){
        $agency = Mava_Application::get('agency');
        if($agency){
            Mava_Application::set('selected_menu', 'transactions');
            $page = max((int)Mava_Url::getParam('page'), 1);
            Mava_Application::set('seo/title', __('transaction_history') . ($page > 1?' - '. __('page_x', array('num' => $page)):''));
            $limit = 10;
            $skip = ($page-1)*$limit;
            $transactionModel = $this->_getTransactionModel();
            $transactions = $transactionModel->getList($skip, $limit, $agency['id']);
            return $this->responseView('Megabook_View_Agency_Transaction', array(
                    'agency' => $agency,
                    'page' => $page,
                    'skip' => $skip,
                    'limit' => $limit,
                    'transactions' => $transactions['items'],
                    'total' => $transactions['total'],
                ), 'dashboard');
        }else{
            return $this->responseError(__('agency_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    /**
     * @return Megabook_DataWriter_WithdrawRequest
     */
    protected function _getWithdrawRequestDataWriter(){
        return Mava_DataWriter::create('Megabook_DataWriter_WithdrawRequest');
    }

    /**
     * @return Megabook_Model_WithdrawRequest
     */
    protected function _getWithdrawRequestModel(){
        return $this->getModelFromCache('Megabook_Model_WithdrawRequest');
    }

    /**
     * @return Megabook_Model_Transaction
     */
    protected function _getTransactionModel(){
        return $this->getModelFromCache('Megabook_Model_Transaction');
    }

    public function linksAction(){
        $agency = Mava_Application::get('agency');
        if($agency){
            Mava_Application::set('selected_menu', 'links');
            $page = max((int)Mava_Url::getParam('page'), 1);
            Mava_Application::set('seo/title', __('link_stats') . ($page > 1?' - '. __('page_x', array('num' => $page)):''));
            $limit = 10;
            $skip = ($page-1)*$limit;
            $linkStatsModel = $this->_getLinkStatsModel();
            $links = $linkStatsModel->getList($skip, $limit, $agency['id']);
            return $this->responseView('Megabook_View_Agency_Links', array(
                    'agency' => $agency,
                    'page' => $page,
                    'skip' => $skip,
                    'limit' => $limit,
                    'links' => $links['items'],
                    'total' => $links['total'],
                ), 'dashboard');
        }else{
            return $this->responseError(__('agency_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    public function notificationsAction(){
        $agency = Mava_Application::get('agency');
        if($agency){
            Mava_Application::set('selected_menu', '');
            $page = max((int)Mava_Url::getParam('page'), 1);
            Mava_Application::set('seo/title', __('notifications') . ($page > 1?' - '. __('page_x', array('num' => $page)):''));
            $limit = 10;
            $skip = ($page-1)*$limit;
            $notificationModel = $this->_getNotificationModel();
            $notifications = $notificationModel->getList($skip, $limit, $agency['id']);
            return $this->responseView('Megabook_View_Agency_Notifications', array(
                    'agency' => $agency,
                    'page' => $page,
                    'skip' => $skip,
                    'limit' => $limit,
                    'notifications' => $notifications['items'],
                    'total' => $notifications['total'],
                ), 'dashboard');
        }else{
            return $this->responseError(__('agency_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    public function markNotifyAsSeenAction(){
        $agency = Mava_Application::get('agency');
        if($agency){
            $notificationModel = $this->_getNotificationModel();
            $notificationModel->markAllAsSeen($agency['id']);
            return $this->responseJson(array(
                    'status' => 1,
                    'message' => __('success')
                ));
        }else{
            return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('agency_not_found')
                ));
        }
    }

    public function markNotifyAsReadAction(){
        $agency = Mava_Application::get('agency');
        if($agency){
            $notificationModel = $this->_getNotificationModel();
            $notificationModel->markAllAsRead($agency['id']);
            return $this->responseJson(array(
                    'status' => 1,
                    'message' => __('success')
                ));
        }else{
            return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('agency_not_found')
                ));
        }
    }

    public function updateQuickNotifyAction(){
        $agency = Mava_Application::get('agency');
        if($agency){
            return $this->responseJson(array(
                    'status' => 1,
                    'count' => count_not_seen_notify($agency['id']),
                    'html' => get_notify_preview($agency['id'], 5)
                ));
        }else{
            return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('agency_not_found')
                ));
        }
    }

    /**
     * @return Megabook_Model_Notification
     */
    protected function _getNotificationModel(){
        return $this->getModelFromCache('Megabook_Model_Notification');
    }

    public function registerAction(){
        if(Mava_Application::getOptions()->activeAffiliate == 1){
            $error = '';
            Mava_Application::set('seo/title', __('register_agency'));
            if(Mava_Url::isPost()){
                $agencyTitle = Mava_Url::getParam('agencyTitle');
                $agencyEmail = Mava_Url::getParam('agencyEmail');
                $agencyPhone = Mava_Url::getParam('agencyPhone');
                $agencyAddress = Mava_Url::getParam('agencyAddress');
                if(trim($agencyTitle) != ""){
                    $agencyDW = $this->_getAgencyDataWriter();
                    $agencyDW->bulkSet(array(
                            'title' => $agencyTitle,
                            'agency_code' => uniqid(),
                            'user_id' => Mava_Visitor::getUserId(),
                            'balance' => 0,
                            'email' => $agencyEmail,
                            'phone' => $agencyPhone,
                            'address' => $agencyAddress,
                            'bank_fullname' => '',
                            'bank_name' => '',
                            'bank_id_string' => '',
                            'bank_branch' => '',
                            'created_date' => time(),
                            'created_ip' => ip(),
                            'status' => $agencyDW::STATUS_PENDING
                        ));
                    if($agencyDW->save()){
                        $agencyId = $agencyDW->get('id');
                        // send agency email
                        Megabook_Model_Mailer::queue($agencyId, __('_email_title_register_agency_title'),
                            Mava_View::getView('Megabook_View_Email_General', array(
                                    'message' => __('_email_title_register_agency_body', array(
                                            'agency_title' => $agencyTitle,
                                            'agency_id' => $agencyId,
                                            'agency_code' => $agencyDW->get('agency_code'),
                                            'agency_dashboard_url' => Mava_Url::getPageLink('dashboard/'. $agencyId)
                                        )
                                ))
                            ));
                        // send admin email
                        $email_notify = Mava_Application::getOptions()->emailReceiveOrderNotify;
                        $body  ='<h2>'.__('agency_info').'</h2>';
                        $body  .='<ul>
                                <li>'. __('agency_id') .': '. $agencyId .'</li>
                                <li>'. __('agency_title') .': '. $agencyTitle .'</li>
                                <li>'. __('agency_email') .': '. $agencyEmail .'</li>
                                <li>'. __('agency_phone') .': '. $agencyPhone .'</li>
                                <li>'. __('agency_address') .': '. $agencyAddress .'</li>
                                <li>'. __('register_time') .': '. date('d/m/Y H:i', time()) .'</li>
                                </ul>';
                        $body .= '<p><a href="'. Mava_Url::getPageLink('admin/agency/index') .'" style="display: inline-block;padding: 7px 15px;color: #FFF;background: #080;font-size: 13px;font-weight: bold;text-decoration: none;margin: 10px 0;">'. __('view_all_agency') .'</a></p>';
                        $body .= '<span style="color: #FFF;display: none;">'. microtime() .'</span>';
                        $emailQueueDw = $this->_getEmailQueueDataWriter();
                        $emailQueueDw->bulkSet(array(
                                'type' => Mava_Model_EmailQueue::TYPE_GENERAL,
                                'email' => $email_notify,
                                'content' => json_encode(array(
                                        'title' => __('_email_title_new_agency_register', array('date' => date('d/m/Y',time()))),
                                        'body' => $body,
                                    )),
                                'created_date' => time()
                            ));
                        $emailQueueDw->save();

                        // send notify
                        Megabook_Model_Notification::add(
                            $agencyId,
                            Megabook_DataWriter_Notification::TYPE_GENERAL,
                            __('register_agency_success'),
                            Mava_Url::getPageLink('dashboard/'. $agencyId)
                        );
                        return $this->responseRedirect(Mava_Url::getPageLink('dashboard/'. $agencyId));
                    }else{
                        $error = __('could_not_register_agency');
                    }
                }else{
                    $error = __('agency_title_empty');
                }
            }
            return $this->responseView('Megabook_View_Agency_Register', array(
                    'error' => $error
                ));
        }else{
            return $this->responseError(__('agency_register_disabled'), Mava_Error::ACCESS_DENIED);
        }
    }

    /**
     * @return Mava_DataWriter_EmailQueue
     * @throws Mava_Exception
     */
    protected function _getEmailQueueDataWriter(){
        return Mava_DataWriter::create('Mava_DataWriter_EmailQueue');
    }

    public function redirectAction(){
        Mava_Application::set('seo/title', __('choose_agency'));
        $myAgency = get_my_agency();
        if(is_array($myAgency) && count($myAgency) > 0){
            if(count($myAgency) > 1){
                return $this->responseView('Megabook_View_Agency_Switcher', array(
                        'agency' => $myAgency
                    ));
            }else{
                return $this->responseRedirect(Mava_Url::getPageLink('dashboard/'. $myAgency[0]['id']));
            }
        }else{
            return $this->responseRedirect(Mava_Url::getPageLink('dang-ky-dai-ly'));
        }
    }

    /**
     * @return Megabook_DataWriter_Agency
     */
    protected function _getAgencyDataWriter(){
        return Mava_DataWriter::create('Megabook_DataWriter_Agency');
    }
}