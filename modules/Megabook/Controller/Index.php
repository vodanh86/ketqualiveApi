<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 12/9/16
 * @Time: 2:03 AM
 */
class Megabook_Controller_Index extends Mava_Controller {
    public function phraseMapAction(){
        return '';
        $startId = Mava_Url::getParam('start_id');
        $db = Mava_Application::getDb();
        $phrase = $db->fetchAll("SELECT * FROM #__phrase WHERE `phrase_id` > '". $startId ."'");
        foreach($phrase as $item){
            $db->query("INSERT INTO #__phrase_compiled(`language_id`,`title`,`phrase_text`) VALUES
            (0,'". $item['title'] ."','". $item['phrase_text'] ."'),
            (1,'". $item['title'] ."','". $item['phrase_text'] ."'),
            (2,'". $item['title'] ."','". $item['phrase_text'] ."')
            ");

            $db->query("INSERT INTO #__phrase_map(`language_id`,`title`,`phrase_id`) VALUES
            (0,'". $item['title'] ."','". $item['phrase_id'] ."'),
            (1,'". $item['title'] ."','". $item['phrase_id'] ."'),
            (2,'". $item['title'] ."','". $item['phrase_id'] ."')
            ");
        }
    }

    public function campaignRedirectAction(){
        $campaign_id = (int)Mava_Url::getParam('campaign_id');
        $campaignModel = $this->_getCampaignModel();
        if($campaign_id > 0 && $campaign = $campaignModel->getById($campaign_id)){
            if($campaign['deleted'] == 'no'){
                return $this->responseRedirect(Mava_Url::addParam($campaign['target_link'], array('_mcid' => $campaign_id)));
            }else{
                return $this->responseRedirect($campaign['target_link']);
            }
        }else{
            return $this->responseRedirect(Mava_Url::getDomainUrl());
        }
    }

    /**
     * @return Megabook_Model_AdsCampaign
     */
    protected function _getCampaignModel(){
        return $this->getModelFromCache('Megabook_Model_AdsCampaign');
    }

    public function bestSellerAction(){
        Mava_Application::set('seo/title', __('book_best_seller'));
        Mava_Application::set('body_id','best_seller_page');
        $page = max((int)Mava_Url::getParam('page'),1);
        $limit = 40;
        $skip = ($page-1)*$limit;
        $productModel = $this->_getProductModel();
        $products = $productModel->getProducts($skip, $limit, null, false, 'buy');
        $results = array();
        if(is_array($products['rows'])){
            foreach($products['rows'] as $item){
                $images = get_gender_product_images($item['thumbnails']);
                $item['thumbnails'] = $images;
                $item['slug'] = Mava_String::unsignString($item['name'],'-');
                $results[] = $item;
            }
        }
        return $this->responseView('Megabook_View_BestSeller', array(
            'products' => $results,
            'total' => $products['total'],
            'page' => $page,
            'skip' => $skip,
            'limit' => $limit
        ));
    }

    public function comingSoonAction(){
        Mava_Application::set('seo/title', __('book_coming_soon'));
        Mava_Application::set('body_id','book_coming_soon_page');
        $page = max((int)Mava_Url::getParam('page'),1);
        $limit = 40;
        $skip = ($page-1)*$limit;
        $productModel = $this->_getProductModel();
        $products = $productModel->getComingProducts($skip, $limit);
        $results = array();
        if(is_array($products['rows'])){
            foreach($products['rows'] as $item){
                $images = get_gender_product_images($item['thumbnails']);
                $item['thumbnails'] = $images;
                $item['slug'] = Mava_String::unsignString($item['name'],'-');
                $results[] = $item;
            }
        }
        return $this->responseView('Megabook_View_Coming', array(
            'products' => $results,
            'total' => $products['total'],
            'page' => $page,
            'skip' => $skip,
            'limit' => $limit
        ));
    }

    public function orderDirectAction(){
        Mava_Application::set('seo/title', __('book_order'));
        Mava_Application::set('body_id', 'direct_order');
        return $this->responseView('Megabook_View_OrderDirect', array());
    }

    public function getBookSetAjaxAction(){
        $setModel = $this->_getBookSetModel();
        $sets = $setModel->getAll();
        if(is_array($sets) && count($sets) > 0){
            $results = array();
            foreach($sets as $item){
                $results[] = array(
                    'id' => $item['id'],
                    'name' => $item['title']
                );
            }
            return $this->responseJson(array(
                'status' => 1,
                'sets' => $results
            ));
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('no_book_set')
            ));
        }
    }

    public function getBookSetSectionAjaxAction(){
        $setModel = $this->_getBookSetModel();
        $sections = $setModel->getAllSection();
        if(is_array($sections) && count($sections) > 0){
            $results = array();
            foreach($sections as $item){
                $sets = array();
                $setInSection = $setModel->getBySection($item['id']);
                if($setInSection && count($setInSection) > 0){
                    foreach($setInSection as $s){
                        $sets[] = array(
                            'id' => $s['id'],
                            'name' => $s['title']
                        );
                    }
                }
                $results[] = array(
                    'id' => $item['id'],
                    'name' => $item['title'],
                    'sets' => $sets
                );
            }
            return $this->responseJson(array(
                'status' => 1,
                'sections' => $results
            ));
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('no_book_set')
            ));
        }
    }

    public function getBookAjaxAction(){
        $set_id = (int)Mava_Url::getParam('set_id');
        $setModel = $this->_getBookSetModel();
        if($set_id > 0 && $set = $setModel->getById($set_id)){
            $productModel = $this->_getProductModel();
            $products = $productModel->getProducts(0,100,array('set' => $set_id), false, 'id', 'desc', true);
            $results = array();
            if(isset($products['rows']) && is_array($products['rows']) && count($products['rows']) > 0){
                foreach($products['rows'] as $item){
                    $image = '';
                    if(Mava_String::isJson($item['thumbnails'])){
                        $thumbnails = json_decode($item['thumbnails'], true);
                        if(is_array($thumbnails) && isset($thumbnails[0]) && Mava_String::isJson($thumbnails[0])){
                            $thumb = json_decode($thumbnails[0], true);
                            if(is_array($thumb) && isset($thumb['image'])){
                                $image = thumb_url($thumb['image'], 100, 100, 2);
                            }
                        }
                    }

                    if(($item['discount_time'] > time() || $item['discount_time']==0) && $item['price_discount'] > 0 && $item['price_discount'] < $item['price']) {
                        $price_final = $item['price_discount'];
                        $has_promotion = 1;
                    }else{
                        $price_final = $item['price'];
                        $has_promotion = 0;
                    }
                    $results[] = array(
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'image' => $image,
                        'price' => Mava_String::price_format($item['price']),
                        'price_final' => Mava_String::price_format($price_final),
                        'has_promotion' => $has_promotion
                    );
                }
            }
            return $this->responseJson(array(
                'status' => 1,
                'books' => $results
            ));

        } else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('book_set_not_found')
            ));
        }
    }

    /**
     * @return Megabook_Model_GiftCode
     */
    protected function _getGiftCodeModel(){
        return $this->getModelFromCache('Megabook_Model_GiftCode');
    }

    public function directCheckoutAction(){
        $cart_items = Mava_Helper_Cookie::getCookie('cart_items');
        if(Mava_String::isJson($cart_items)){
            $cart_items = json_decode($cart_items, true);
        }else{
            $cart_items = array();
        }
        $fullname = Mava_Url::getParam('fullname');
        $address = Mava_Url::getParam('receiver_info');
        $phone = Mava_Url::getParam('phone_number');
        $email = Mava_Url::getParam('email');
        $gift_code = Mava_Url::getParam('gift_code');
        $free_time = Mava_Url::getParam('free_time');
        $who_is = Mava_Url::getParam('who_is');
        $city_id = Mava_Url::getParam('city_id');

        if(!is_array($cart_items) || count($cart_items) == 0){
            return $this->responseJson(array(
                'status' => -1,
                'step' => 1,
                'message' => __('no_product_in_cart')
            ));
        }else if($fullname == ''){
            return $this->responseJson(array(
                'status' => -1,
                'step' => 2,
                'message' => __('please_enter_fullname')
            ));
        }else if($address == ''){
            return $this->responseJson(array(
                'status' => -1,
                'step' => 2,
                'message' => __('please_enter_address')
            ));
        }else if($phone == ''){
            return $this->responseJson(array(
                'status' => -1,
                'step' => 2,
                'message' => __('please_enter_phone')
            ));
        }else if(!Mava_String::isPhoneNumber($phone)){
            return $this->responseJson(array(
                'status' => -1,
                'step' => 2,
                'message' => __('phone_invalid')
            ));
        }else{
            $discount_reason = '';
            $products = array();
            $total_quantity = 0;
            $total_amount = 0;
            foreach($cart_items as $item){
                $prod = array(
                    'id' => $item['id'],
                    'title' => str_replace('+',' ', $item['name']),
                    'photo' => $item['photo'],
                    'quantity' => (int)$item['quantity'],
                    'price' => ($item['old_price']>0?$item['old_price']:$item['price']),
                    'discount_price' => ($item['old_price']>0?$item['price']:$item['old_price']),
                    'discount_reason' => $discount_reason,
                    'set_id' => 0
                );
                $products[] = $prod;
                $total_quantity += (int)$item['quantity'];
                $total_amount += ($prod['discount_price'] > 0?$prod['quantity']*$prod['discount_price']:$prod['quantity']*$prod['price']);
            }
            if($email == ''){
                $email = Mava_Visitor::getInstance()->get('email');
            }

            if($gift_code != ""){
                $giftCodeModel = $this->_getGiftCodeModel();
                $gift_code_value = $giftCodeModel->calculateValue($gift_code, $total_quantity, $total_amount);
            }else{
                $gift_code_value = 0;
            }

            $ship_cost = ($total_amount > 250000)?0:($city_id == 18?20000:30000);
            $productOrderModel = $this->_getProductOrderModel();
            $check_add = $productOrderModel->add($products, $phone, $address, $email, $fullname, $gift_code, $free_time, $who_is, $city_id);
            if($check_add['status'] == 1){
                $email_id = 0;
                $email_token = '';
                $email_notify = Mava_Application::getOptions()->emailReceiveOrderNotify;
                if($email_notify != ''){
                    $body  ='<h2>'.__('customer_info').'</h2>';
                    $body  .='<ul>
                                <li>'. __('fullname') .': '. $fullname .'</li>
                                <li>'. __('email') .': '. $email .'</li>
                                <li>'. __('phone') .': '. $phone .'</li>
                                <li>'. __('ship_address') .': '. $address .'</li>
                                <li>'. __('order_time') .': '. date('d/m/Y H:i', time()) .'</li>
                                </ul>';
                    $body  .='<h2>'.__('order_info').'</h2>';
                    $body  .='<table border="1" style="border-collapse: collapse;width: 100%">';
                    $body  .='<tr><th>'. __('stt') .'</th><th>'. __('product') .'</th><th>'. __('price') .'</th><th>'. __('quantity') .'</th><th>'. __('amount') .'</th></tr>';
                    $count = 0;
                    $total_amount = 0;
                    foreach($products as $item){
                        $count++;
                        $body .= '<tr>
                                        <td>'. $count .'</td>
                                        <td>'. htmlspecialchars($item['title']) .'</td>
                                        <td>'. ($item['discount_price'] > 0?'<p><s>'.Mava_String::price_format($item['price']) .'</s></p>'. Mava_String::price_format($item['discount_price']):Mava_String::price_format($item['price'])) .'</td>
                                        <td>'. $item['quantity'] .'</td>
                                        <td>'. ($item['discount_price'] > 0?Mava_String::price_format($item['quantity']*$item['discount_price']):Mava_String::price_format($item['quantity']*$item['price'])) .'</td>
                                    </tr>';
                        $total_amount += ($item['discount_price'] > 0?$item['quantity']*$item['discount_price']:$item['quantity']*$item['price']);
                    }

                    $body .= '<tr><td colspan="4"><b>'. __('total_amount') .'</b></td><td><b>'. Mava_String::price_format($total_amount) .'</b></td></tr>';
                    $body .= '<tr><td colspan="4"><b>'. __('ship_cost') .'</b></td><td><b>'. Mava_String::price_format($ship_cost) .'</b></td></tr>';
                    $body .= '<tr><td colspan="4"><b>'. __('gift_code') .'</b></td><td><b>'. Mava_String::price_format($gift_code_value) .'</b></td></tr>';
                    $body .= '<tr><td colspan="4"><b>'. __('total_amount_payment') .'</b></td><td><b>'. Mava_String::price_format($total_amount + $ship_cost - $gift_code_value) .'</b></td></tr>';
                    $body .='</table>';
                    $body .= '<p><a href="'. Mava_Url::getPageLink('admin/products/orders') .'" style="display: inline-block;padding: 7px 15px;color: #FFF;background: #080;font-size: 13px;font-weight: bold;text-decoration: none;margin: 10px 0;">'. __('view_all_product_order') .'</a></p>';
                    $body .= '<span style="color: #FFF;display: none;">'. microtime() .'</span>';
                    $emailQueueDw = $this->_getEmailQueueDataWriter();
                    $emailQueueDw->bulkSet(array(
                        'type' => Mava_Model_EmailQueue::TYPE_NEW_ORDER,
                        'email' => $email_notify,
                        'content' => json_encode(array(
                            'title' => __('email_new_product_order', array('date' => date('d/m/Y',time()))),
                            'body' => $body,
                        )),
                        'created_date' => time()
                    ));
                    $emailQueueDw->save();

                    $email_id = '';
                    $email_id .= $emailQueueDw->get('queue_id');

                    // gửi thông báo tới thành viên
                    $body = '<p>Chào bạn, Megabook đã nhận được thông tin đơn hàng của bạn tại Megabook.vn</p>
                        <h4>Thông tin đơn hàng '. $check_add['order_id'] .'</h4>
                        <ol>
                            <li>'. __('fullname') .': '. $fullname .'</li>
                            <li>'. __('email') .': '. $email .'</li>
                            <li>'. __('phone') .': '. $phone .'</li>
                            <li>'. __('ship_address') .': '. $address .'</li>
                            <li>'. __('order_time') .': '. date('d/m/Y H:i', time()) .'</li>
                        </ol>
                    ';
                    $body  .='<table border="1" style="border-collapse: collapse;width: 100%;border: 1px solid #ccc;border-color: #ccc;margin-bottom: 15px;">';
                    $body  .='<tr style="background: #30AED6;color: #FFF;"><th>'. __('stt') .'</th><th>'. __('product') .'</th><th>'. __('price') .'</th><th>'. __('quantity') .'</th><th>'. __('amount') .'</th></tr>';
                    $count = 0;
                    $total_amount = 0;
                    foreach($products as $item){
                        $count++;
                        $body .= '<tr>
                                        <td>'. $count .'</td>
                                        <td>'. htmlspecialchars($item['title']) .'</td>
                                        <td>'. ($item['discount_price'] > 0?'<p><s>'.Mava_String::price_format($item['price']) .'</s></p>'. Mava_String::price_format($item['discount_price']):Mava_String::price_format($item['price'])) .'</td>
                                        <td>'. $item['quantity'] .'</td>
                                        <td>'. ($item['discount_price'] > 0?Mava_String::price_format($item['quantity']*$item['discount_price']):Mava_String::price_format($item['quantity']*$item['price'])) .'</td>
                                    </tr>';
                        $total_amount += ($item['discount_price'] > 0?$item['quantity']*$item['discount_price']:$item['quantity']*$item['price']);
                    }
                    $body .= '<tr><td colspan="4"><b>'. __('total_amount') .'</b></td><td><b>'. Mava_String::price_format($total_amount) .'</b></td></tr>';
                    $body .= '<tr><td colspan="4"><b>'. __('ship_cost') .'</b></td><td><b>'. Mava_String::price_format($ship_cost) .'</b></td></tr>';
                    $body .= '<tr><td colspan="4"><b>'. __('gift_code') .'</b></td><td><b>'. Mava_String::price_format($gift_code_value) .'</b></td></tr>';
                    $body .= '<tr><td colspan="4"><b>'. __('total_amount_payment') .'</b></td><td><b>'. Mava_String::price_format($total_amount + $ship_cost - $gift_code_value) .'</b></td></tr>';
                    $body .='</table>
                    <div>Trong vòng 24h tới Megabook sẽ gọi điện lại để xác nhận đơn hàng của bạn.</div>
                    <div>Mọi thắc mắc và liên hệ bạn hãy nhắn tin/gọi điện qua số Hotline của Megabook: 0981.039.959</div>
                    <div>----------</div>
                    <div>Megabook - Chuyên gia sách luyện thi</div>
                    <div>Địa chỉ: Sô 14 - Ngõ 93 - Vũ Hữu - Thanh Xuân - Hà Nội</div>
                    <div>Điện thoại: 0981.039.959</div>';
                    $emailQueueDw = $this->_getEmailQueueDataWriter();
                    $emailQueueDw->bulkSet(array(
                        'type' => Mava_Model_EmailQueue::TYPE_ORDER_CONFIRM,
                        'email' => $email,
                        'content' => json_encode(array(
                            'title' => __('confirm_order_x', array('order_id' => $check_add['order_id'])),
                            'body' => $body,
                        )),
                        'created_date' => time()
                    ));
                    $emailQueueDw->save();
                    $email_id .= '|'. $emailQueueDw->get('queue_id');
                    $email_token = Mava_String::createToken($email_id);
                }
                Mava_Helper_Cookie::deleteCookie('cart_items');
                return $this->responseJson(array(
                    'status' => 1,
                    'message' => __('checkout_success'),
                    'email_id' => $email_id,
                    'email_token' => $email_token,
                    'id' => $check_add['order_id']
                ));
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'step' => 1,
                    'message' => $check_add['message']
                ));
            }
        }
    }

    public function directCheckoutSuccessAction(){
        Mava_Application::set('seo/title', __('book_order_success'));
        return $this->responseView('Megabook_View_OrderDirectSuccess');
    }

    /**
     * @return Mava_DataWriter_EmailQueue
     * @throws Mava_Exception
     */
    protected function _getEmailQueueDataWriter(){
        return Mava_DataWriter::create('Mava_DataWriter_EmailQueue');
    }

    /**
     * @return Product_Model_Order
     */
    protected function _getProductOrderModel(){
        return $this->getModelFromCache('Product_Model_Order');
    }

    /**
     * @return Megabook_Model_BookSet
     */
    protected function _getBookSetModel(){
        return $this->getModelFromCache('Megabook_Model_BookSet');
    }

    /**
     * @return Product_Model_Product
     */
    protected function _getProductModel(){
        return $this->getModelFromCache('Product_Model_Product');
    }
}