<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 5/1/15
 * @Time: 10:51 AM
 */
class Profile_Controller_Profile extends Mava_Controller {
    public function __construct(){
        Mava_Application::set('seo/robots', "nofollow,noindex");
        Mava_Application::set('menu_selected', 'profile');
        if(!is_login()){
            Mava_Url::redirectLogin(__('login_to_access_profile'));
        }
    }

    public function get_notificationAction(){
        $limit = Mava_Application::getOptions()->notify_row;
        $skip = 0;
        $notifyModel = $this->_getNotificationModel();
        $notifications = $notifyModel->getUserNotify((int)Mava_Visitor::getUserId(), $skip, $limit);
        if(count($notifications['rows']) > 0){
            foreach($notifications['rows'] as $item){
                $data[] = array(
                    'icon' => 'icon-notify-'. str_replace('_','-', $item['type']),
                    'href' => $item['href'],
                    'content' => $item['content'],
                    'readed' => $item['read'],
                    'time' => print_time($item['created_date'])
                );
            }
            return $this->responseJson(array(
                'status' => 1,
                'msg' => __('no_notification_found'),
                'data' => $data
            ));
        }else{
            return $this->responseJson(array(
                'status' => 1,
                'msg' => __('no_notification_found'),
                'data' => array()
            ));
        }
    }

    public function count_notificationAction(){
        $notifyModel = $this->_getNotificationModel();
        $count = $notifyModel->getUnreadNotify();
        return $this->responseJson(array(
            'status' => 1,
            'message' => __('success'),
            'count' => $count
        ));
    }

    public function changepasswordAction(){
        $oldPassword = Mava_Url::getParam('old_password');
        $newPassword = Mava_Url::getParam('new_password');
        $confirmNewPassword = Mava_Url::getParam('confirm_new_password');

        $userModel = $this->_getUserModel();
        $user = $userModel->getUserById((int)Mava_Visitor::getUserId(), false);
        if($user){

            if($user['password'] != "" && $oldPassword == ''){
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('please_enter_old_password')
                ));
            }else if($user['password'] != "" && ($userModel->generalPassword($oldPassword, $user['unique_token']) != $user['password'])){
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('old_password_incorrect')
                ));
            }else if(
                strlen($newPassword) < Mava_Application::get('config/passwordMinLength') ||
                strlen($newPassword) > Mava_Application::get('config/passwordMaxLength')
            ){
                return array(
                    'status' => -1,
                    'message' => __('password_invalid')
                );
            }else if($newPassword != $confirmNewPassword){
                return array(
                    'status' => -1,
                    'message' => __('repassword_invalid')
                );
            }else{
                $userDW = $this->_getUserDataWriter();
                $userDW->setExistingData($user['user_id']);
                $userDW->set('password', $userModel->generalPassword($newPassword, $user['unique_token']));
                if($userDW->save()){
                    return $this->responseJson(array(
                        'status' => 1,
                        'message' => __('password_changed')
                    ));
                }else{
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('can_not_change_password_now')
                    ));
                }
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('please_relogin')
            ));
        }
    }

    public function passwordAction(){
        Mava_Application::set('seo/title', __('change_password'));
        Mava_Application::set('menu_profile', 'password');
        $userModel = $this->_getUserModel();
        $user = $userModel->getUserById((int)Mava_Visitor::getUserId(), false);
        if($user) {
            return $this->responseView('Profile_View_ChangePassword', array(
                'has_password' => ($user['password']!=""?true:false)
            ));
        }else{
            return $this->responseError(__('user_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    public function saveAction(){
        $type = Mava_Url::getParam('type');
        $value = Mava_Url::getParam('value');
        $userDW = $this->_getUserDataWriter();
        $userDW->setExistingData((int)Mava_Visitor::getUserId());
        $field = '';
        $error = '';
        if($type == 'custom_title'){
            if($value == ''){
                $error = __('please_enter_fullname');
            }else{
                $field = 'custom_title';
            }
        }else if($type == 'gender'){
            if(!in_array($value, array('male','female'))){
                $value = '';
            }
            $field = 'gender';
        }else if($type == 'city'){
            if((int)$value > 0){
                $field = 'city_id';
            }else{
                $error = __('please_choose_city');
            }
        }else if($type == 'phone'){
            if(Mava_String::isPhoneNumber($value)){
                $field = 'phone';
            }else{
                $error = __('please_enter_phone');
            }
        }else if($type == 'birthday'){
            if(is_array($value) && count($value) == 3){
                $field = 'birthday';
                $value = date_to_time(implode('/', $value));
            }else{
                $error = __('please_choose_birthday');
            }
        }else{
            $error = __('invalid_user_field_edit');
        }

        if($error == '' && $field != ""){
            $userDW->set($field, $value);
            Mava_Visitor::getInstance()->set($field, $value);
            if($userDW->save()){
                $return_value = $value;
                if($type == 'city'){
                    $return_value = get_city_title($value);
                }else if($type == 'birthday'){
                    $return_value = print_birthday($value);
                }else if($type == 'gender'){
                    $return_value = __($value);
                }
                return $this->responseJson(array(
                    'status' => 1,
                    'value' => htmlspecialchars($return_value),
                    'user_lead' => get_user_lead(),
                    'org_value' => $value,
                    'message' => __('profile_save')
                ));
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('invalid_user_field_edit')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => $error
            ));
        }
    }

    public function accountAction(){
        Mava_Application::set('seo/title', __('account_information'));
        Mava_Application::set('menu_profile', 'account');
        $user = Mava_Visitor::getInstance();
        $cities = get_all_city();
        return $this->responseView('Profile_View_AccountSettings', array(
            'user' => $user,
            'cities' => $cities
        ));
    }

    public function notificationsAction(){
        Mava_Application::set('seo/title', __('notifications'));
        Mava_Application::set('menu_profile', 'notifications');
        $page = max((int)Mava_Url::getParam('page'), 1);
        $offset = Mava_Application::getOptions()->notify_offset;
        $limit = Mava_Application::getOptions()->notify_row;
        $skip = ($page-1)*$limit;
        $notifyModel = $this->_getNotificationModel();
        $notifications = $notifyModel->getUserNotify((int)Mava_Visitor::getUserId(), $skip, $limit);
        return $this->responseView('Profile_View_Notification', array(
            'notifications'  => $notifications['rows'],
            'total' => $notifications['total'],
            'unread' => $notifications['unread'],
            'skip' => $skip,
            'page' => $page,
            'offset' => $offset,
            'limit' => $limit
        ));
    }

    public function mask_all_as_readAction(){
        $notifyModel = $this->_getNotificationModel();
        $notifyModel->maskAllAsRead((int)Mava_Visitor::getUserId());
        return $this->responseJson(array(
            'status' => 1,
            'msg' => __('success')
        ));
    }

    public function ordersAction(){
        Mava_Application::set('seo/title', __('my_orders'));
        Mava_Application::set('menu_profile', 'order');
        Mava_Application::set('body_id', 'profile_orders');
        $orderModel = $this->_getOrderModel();
        $page = max((int)Mava_Url::getParam('page'), 1);
        $limit = 10;
        $skip = ($page-1)*$limit;
        $orders = $orderModel->getUserOrder(Mava_Visitor::getUserId(), $skip, $limit);
        return $this->responseView('Profile_View_Orders', array(
            'page' => $page,
            'limit' => $limit,
            'orders' => $orders['items'],
            'total_order' => $orders['total']
        ));
    }

    public function orderDetailAction(){
        Mava_Application::set('seo/title', __('my_orders'));
        Mava_Application::set('menu_profile', 'order');
        Mava_Application::set('body_id', 'profile_orders');
        $order_id = Mava_Url::getParam('id');
        $orderModel = $this->_getOrderModel();
        $order = $orderModel->getById($order_id, true);
        return $this->responseView('Profile_View_OrderDetail', array(
            'order' => $order
        ));
    }

    public function addressAction(){
        Mava_Application::set('seo/title', __('consignee_address'));
        Mava_Application::set('menu_profile', 'address');
        Mava_Application::set('body_id', 'profile_orders');
        $address = $this->_getAddressModel();
        $address = $address->getUserAddress();
        return $this->responseView('Profile_View_Address', array(
            'address' => $address
        ));
    }

    public function indexAction(){
        Mava_Application::set('seo/title', __('profile'));
        Mava_Application::set('menu_profile', 'home');
        Mava_Application::set('body_id', 'profile_home');

        $userModel = $this->_getUserModel();
        $user = $userModel->getUserById(Mava_Visitor::getUserId());

        $addressModel = $this->_getAddressModel();
        $address = $addressModel->getUserAddress($user['user_id']);

        $orderModel = $this->_getOrderModel();
        $recent_order = $orderModel->getUserOrder(Mava_Visitor::getUserId(), 0, 10);
        return $this->responseView('Profile_View_Index', array(
            'user' => $user,
            'address' => $address,
            'orders' => $recent_order['items'],
            'total_order' => $recent_order['total']
        ));
    }

    /**
     * @return Product_Model_Order
     */
    protected function _getOrderModel(){
        return $this->getModelFromCache('Product_Model_Order');
    }

    public function setDefaultAddressAction(){
        $address_id = Mava_Url::getParam('address_id');
        $addressModel = $this->_getAddressModel();
        if($address_id > 0 && $address_obj = $addressModel->getById($address_id)){
            if($address_obj['user_id'] == Mava_Visitor::getUserId()){
                $addressModel->setAddressDefault($address_id);
                Mava_Session::set('otm', __('address_updated'));
                return $this->responseJson(array(
                    'status' => 1,
                    'message' => __('address_updated')
                ));
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('access_denied')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('address_not_found')
            ));
        }
    }

    public function deleteAddressAction(){
        $address_id = Mava_Url::getParam('address_id');
        $addressModel = $this->_getAddressModel();
        if($address_id > 0 && $address_obj = $addressModel->getById($address_id)){
            if($address_obj['user_id'] == Mava_Visitor::getUserId()){
                $addressDW = $this->_getAddressDataWriter();
                $addressDW->setExistingData($address_id);
                $addressDW->delete();
                Mava_Session::set('otm', __('address_deleted'));
                return $this->responseJson(array(
                    'status' => 1,
                    'message' => __('address_deleted')
                ));
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('access_denied')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('address_not_found')
            ));
        }
    }

    public function saveAddressAction(){
        $address_id = Mava_Url::getParam('address_id');
        $fullname = Mava_Url::getParam('fullname');
        $address = Mava_Url::getParam('address');
        $phone = Mava_Url::getParam('phone');
        $set_default = Mava_Url::getParam('set_default');
        if($address == ""){
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('please_enter_address')
            ));
        }else if($fullname == ""){
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('please_enter_fullname')
            ));
        }else if($phone == ""){
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('please_enter_phone')
            ));
        }else{
            $addressModel = $this->_getAddressModel();
            if($address_id > 0 && $address_obj = $addressModel->getById($address_id)){
                if($address_obj['user_id'] == Mava_Visitor::getUserId()){

                    $addressDW = $this->_getAddressDataWriter();
                    $addressDW->setExistingData($address_id);
                    $addressDW->bulkSet(array(
                        'address' => $address,
                        'fullname' => $fullname,
                        'phone' => $phone
                    ));
                    if($addressDW->save()){
                        if($set_default==1){
                            $addressModel->setAddressDefault($address_id);
                        }
                        Mava_Session::set('otm', __('address_updated'));
                        return $this->responseJson(array(
                            'status' => 1,
                            'message' => __('address_updated')
                        ));
                    }else{
                        return $this->responseJson(array(
                            'status' => -1,
                            'message' => __('can_not_edit_address')
                        ));
                    }
                }else{
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('access_denied')
                    ));
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('address_not_found')
                ));
            }
        }
    }

    public function addAddressAction(){
        $fullname = Mava_Url::getParam('fullname');
        $address = Mava_Url::getParam('address');
        $phone = Mava_Url::getParam('phone');
        $set_default = Mava_Url::getParam('set_default');
        $addressModel = $this->_getAddressModel();
        if($address == ""){
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('please_enter_address')
            ));
        }else if($fullname == ""){
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('please_enter_fullname')
            ));
        }else if($phone == ""){
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('please_enter_phone')
            ));
        }else{
            $addressDW = $this->_getAddressDataWriter();
            $addressDW->bulkSet(array(
                'user_id' => Mava_Visitor::getUserId(),
                'address' => $address,
                'fullname' => $fullname,
                'phone' => $phone
            ));
            if($addressDW->save()){
                if($set_default==1){
                    $addressModel->setAddressDefault($addressDW->get('id'));
                }
                Mava_Session::set('otm', __('address_added'));
                return $this->responseJson(array(
                    'status' => 1,
                    'message' => __('address_added')
                ));
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('can_not_add_address')
                ));
            }
        }
    }

    /**
     * @return Product_DataWriter_Address
     * @throws Mava_Exception
     */
    protected function _getAddressDataWriter(){
        return Mava_DataWriter::create('Product_DataWriter_Address');
    }

    /**
     * @return Product_Model_Address
     */
    protected function _getAddressModel(){
        return $this->getModelFromCache('Product_Model_Address');
    }


    /**
     * @return Mava_Model_Notification
     */
    protected function _getNotificationModel(){
        return $this->getModelFromCache('Mava_Model_Notification');
    }

    /**
     * @return Mava_DataWriter_User
     * @throws Mava_Exception
     */
    protected function _getUserDataWriter(){
        return Mava_DataWriter::create('Mava_DataWriter_User');
    }

    /**
     * @return Mava_Model_User
     */
    protected function _getUserModel(){
        return $this->getModelFromCache('Mava_Model_User');
    }
}