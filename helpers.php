<?php

function url($path, $params = []){
    return Mava_Url::getPageLink($path, $params);
}

function setupCallAPI($method, $url, $data){
    $curl = curl_init();

    switch ($method){
        case "POST":
            curl_setopt($curl, CURLOPT_POST, true);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
            break;
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'token: '. Mava_Application::get('config/xboom_info')['token'],
        'Content-Type: application/json',
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // execute
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function getErrorMessage($code){
    switch ($code) {
        case 1:
            $message = 'Thành công';
            break;
        case 2:
            $message = 'Tham số truyền lên không đúng';
            break;
        case 3:
            $message = 'Lỗi xác thực tài khoản';
            break;
        case 4:
            $message = 'Thẻ lỗi hoặc đã tồn tại';
            break;
        case 5:
            $message = 'Lỗi khác';
            break;
        case 6:
            $message = 'Truy cập bị từ chối';
            break;
        default:
            $message = 'Lỗi không xác định';
            break;
    }
    return $message;
} 

function array_filter_key($data, $key){
    $result = [];
    foreach($data as $k => $v){
        if(in_array($k, $key)){
            $result[$k] = $v;
        }
    }
    return $result;
}

function get_thongkenhanh_result_html($nums, $province_code = 'tt', $start_time, $end_time){
   /* @var $resultModel Loto_Model_Result */
    $statsModel = Mava_Model::create('Loto_Model_Stats');
    return $statsModel->getThongKeNhanhResultHtml($nums, $province_code, $start_time, $end_time);  
}

function get_thongkechukygantheotinh_result_html($nums, $province_code = 'tt', $all = 1){
   /* @var $resultModel Loto_Model_Result */
    $statsModel = Mava_Model::create('Loto_Model_Stats');
    return $statsModel->getChuKyGanTheoTinhResultHtml($nums, $province_code, $all);  
}

function get_thongkechuky_result_html($nums){
   /* @var $resultModel Loto_Model_Result */
    $statsModel = Mava_Model::create('Loto_Model_Stats');
    return $statsModel->getChuKyResultHtml($nums);  
}

function get_thongkechukydanlo_result_html($nums, $start_time, $end_time, $pair){
   /* @var $resultModel Loto_Model_Result */
    $statsModel = Mava_Model::create('Loto_Model_Stats');
    return $statsModel->getChuKyDanLoResultHtml($nums, $start_time, $end_time, $pair);  
}

function get_thongkelogan_result_html($result){
   /* @var $resultModel Loto_Model_Result */
    $statsModel = Mava_Model::create('Loto_Model_Stats');
    return $statsModel->getLoGanResultHtml($result);
}

function get_result_html($start_time, $end_time, $province = 'tt', $silent = false){
    /* @var $resultModel Loto_Model_Result */
    $resultModel = Mava_Model::create('Loto_Model_Result');
    return $resultModel->getResultHtml($start_time, $end_time, $province, $silent);
}

function get_result_home_html($date, $province = 'tt', $silent = false){
    /* @var $resultModel Loto_Model_Result */
    $resultModel = Mava_Model::create('Loto_Model_Result');
    return $resultModel->getResultHomeHtml($date, $province, $silent);
}

function get_soicautheoso_result_html($number, $result){
   /* @var $resultModel Loto_Model_Result */
    $statsModel = Mava_Model::create('Loto_Model_Stats');
    return $statsModel->getSoiCauTheoSoResultHtml($number, $result);
}

function get_chitietsoicautheoso_result_html($number, $position_1, $position_2, $result){
    /* @var $resultModel Loto_Model_Result */
    $statsModel = Mava_Model::create('Loto_Model_Stats');
    return $statsModel->getChiTietCauLotoTheoSoResultHtml($number, $position_1, $position_2, $result);
}

function get_soicautheotinh_result_html($province_slug, $date, $result){
   /* @var $resultModel Loto_Model_Result */
    $statsModel = Mava_Model::create('Loto_Model_Stats');
    return $statsModel->getSoiCauTheoTinhResultHtml($province_slug, $date, $result);
}

function get_chitietsoicautheotinh_result_html($number, $position_1, $position_2, $result, $province){
    /* @var $resultModel Loto_Model_Result */
    $statsModel = Mava_Model::create('Loto_Model_Stats');
    return $statsModel->getChiTietCauLotoTheoTinhResultHtml($number, $position_1, $position_2, $result, $province);
}

function get_soicaubachthu_result_html($date, $result){
   /* @var $resultModel Loto_Model_Result */
    $statsModel = Mava_Model::create('Loto_Model_Stats');
    return $statsModel->getSoiCauBachThuResultHtml($date, $result);
}

function get_chitietsoicaubachthu_result_html($number, $position_1, $position_2, $result){
    /* @var $resultModel Loto_Model_Result */
    $statsModel = Mava_Model::create('Loto_Model_Stats');
    return $statsModel->getChiTietCauLotoBachThuResultHtml($number, $position_1, $position_2, $result);
}

function get_soicauvehainhay_result_html($date, $result){
   /* @var $resultModel Loto_Model_Result */
    $statsModel = Mava_Model::create('Loto_Model_Stats');
    return $statsModel->getSoiCauVeHaiNhayResultHtml($date, $result);
}

function get_chitietsoicauvehainhay_result_html($number, $position_1, $position_2, $result){
    /* @var $resultModel Loto_Model_Result */
    $statsModel = Mava_Model::create('Loto_Model_Stats');
    return $statsModel->getChiTietCauLotoVeHaiNhayResultHtml($number, $position_1, $position_2, $result);
}

function get_soicaudacbiet_result_html($date, $result){
   /* @var $resultModel Loto_Model_Result */
    $statsModel = Mava_Model::create('Loto_Model_Stats');
    return $statsModel->getSoiCauDacBietResultHtml($date, $result);
}

function get_chitietsoicaudacbiet_result_html($number, $position_1, $position_2, $result){
    /* @var $resultModel Loto_Model_Result */
    $statsModel = Mava_Model::create('Loto_Model_Stats');
    return $statsModel->getChiTietCauDacBietResultHtml($number, $position_1, $position_2, $result);
}

function get_all_province(){
    $provinceModel = Mava_Model::create('Loto_Model_Province');
    return $provinceModel->getAll();
}

function get_all_simple_province(){
    $provinceModel = Mava_Model::create('Loto_Model_Province');
    return $provinceModel->getSimpleList();
}

function dd($obj){
    echo '<xmp>';
    print_r($obj); die;
}

function get_thongketansuatlo_result_html($result){
    /* @var $resultModel Loto_Model_Stats */
    $resultModel = Mava_Model::create('Loto_Model_Stats');
    return $resultModel->getTanSuatLoResultHtml($result);
}

function get_thongketheotong_result_html($result){
    /* @var $resultModel Loto_Model_Stats */
    $resultModel = Mava_Model::create('Loto_Model_Stats');
    return $resultModel->getTongSoResultHtml($result);
}

function get_thongkelovenhieuveit_result_html($result, $type){
    /* @var $resultModel Loto_Model_Stats */
    $resultModel = Mava_Model::create('Loto_Model_Stats');
    return $resultModel->getLoVeNhieuVeItResultHtml($result, $type);
}

function get_thongkeloroi_result_html($result){
    /* @var $resultModel Loto_Model_Stats */
    $resultModel = Mava_Model::create('Loto_Model_Stats');
    return $resultModel->getLoRoiResultHtml($result);
}

function get_thongkegiaidacbiet_result_html($result){
    /* @var $resultModel Loto_Model_Stats */
    $resultModel = Mava_Model::create('Loto_Model_Stats');
    return $resultModel->getGiaiDacBietResultHtml($result);
}

function get_doveso_result_html($date, $province = 'tt', $number){
    /* @var $resultModel Loto_Model_Result */
    $resultModel = Mava_Model::create('Loto_Model_Result');
    return $resultModel->getDoVeSoResultHtml($date, $province, $number);
}


if(!function_exists('get_list_category_sorted')){

    $sort_category_result = array();
    /**
     * @param $start_level
     * @param $parent_id
     * @param $categories
     * @param int $reject_id
     * @return array
     */
    function get_list_category_sorted($start_level, $parent_id, $categories, $reject_id = 0){
        global $sort_category_result;
        if(is_array($categories) && count($categories) > 0){
            foreach($categories as $item){
                if($item['parent_id'] == $parent_id && $item['category_id'] != $reject_id){
                    $item['level'] = $start_level;
                    $sort_category_result[] = $item;
                    $child = sc_has_child($item['category_id'], $categories);
                    if($child === true){
                        get_list_category_sorted($start_level+1, $item['category_id'], $categories, $reject_id);
                    }
                }
            }
            return $sort_category_result;
        }else{
            return array();
        }
    }

    function sc_has_child($parent_id, $categories){
        foreach($categories as $item){
            if($item['parent_id'] == $parent_id){
                return true;
            }
        }
        return false;
    }
}

function get_short_name($fullname, $default = ''){
    //TODO xử lý tên theo quốc gia
    if($fullname != ""){
        $nameSplit = explode(' ', $fullname);
        if(count($nameSplit) > 3){
            $name = array_slice($nameSplit, -2, 2);
            return implode(' ', $name);
        }else{
            return end($nameSplit);
        }
    }else{
        return $default;
    }
}

function ads_track_orders($order_id, $item_count, $total_amount){
    $window_id = Mava_Helper_Cookie::getCookie('_mb_window_id');
    $local_campaign_id = (int)Mava_Helper_Cookie::getCookie('_mb_campaign_id');
    $local_link_id = (int)Mava_Helper_Cookie::getCookie('_mb_link_id');
    $campaignModel = Mava_Model::create('Megabook_Model_AdsCampaign');
    $linkModel = Mava_Model::create('Megabook_Model_AdsCampaignLinks');
    if($window_id != "" && $local_campaign_id > 0 && $campaign = $campaignModel->getById($local_campaign_id)){
        if($campaign['deleted'] == 'no' && $local_link_id > 0 && $link = $linkModel->getById($local_link_id)){
            $mongo_user = Mava_Data::gets(Mava_Data::TABLE_USERS, array(
                'window_id' => array('$eq' => $window_id),
                'campaign_id' => array('$eq' => $local_campaign_id),
                'link_id' => array('$eq' => $local_link_id)
            ),0,1);

            if(is_array($mongo_user) && count($mongo_user) > 0){
                $mongo_user = $mongo_user[0];

                $order_user_name = (is_login()?(string)Mava_Visitor::getInstance()->get('custom_title'):(isset($_COOKIE['user_name'])?$_COOKIE['user_name']:__('guest_x', array('id' => $window_id))));

                // track order record
                Mava_Data::add(Mava_Data::TABLE_ORDER, array(
                    'user_id' => (string)$mongo_user['_id'],
                    'window_id' => (string)$window_id,
                    'campaign_id' => (int)$local_campaign_id,
                    'link_id' => (int)$local_link_id,
                    'link_url' => (string)$link['url'],
                    'order_time' => time(),
                    'megabook_user_name' => $order_user_name,
                    'megabook_user_id' => (int)Mava_Visitor::getUserId(),
                    'order_id' => (int)$order_id,
                    'item_count' => (int)$item_count,
                    'total_amount' => (int)$total_amount
                ));

                $mongo_user['order_list'][] = $order_id;
                Mava_Data::update(Mava_Data::TABLE_USERS, $mongo_user['_id'], array(
                    'order' => $mongo_user['order']+1,
                    'revenue' => $mongo_user['revenue']+$total_amount,
                    'order_list' => $mongo_user['order_list']
                ));
                // Thống kê đặt hàng
                $order_stats = Mava_Data::gets(Mava_Data::TABLE_STATS, array(
                    'campaign_id' => array('$eq' => (int)$local_campaign_id),
                    'link_id' => array('$eq' => (int)$local_link_id),
                    'day' => array('$eq' => (int)date('Ymd')),
                    'type' => array('$eq' => 'order')
                ),0,1);

                if(is_array($order_stats) && count($order_stats) > 0){
                    $order_stats = $order_stats[0];
                    $hours = $order_stats['hour'];
                    $hours[(int)date('H')]++;
                    $order_stats['count']++;
                    Mava_Data::update(Mava_Data::TABLE_STATS, $order_stats['_id'], array(
                        'hour' => (array)$hours,
                        'count' => (int)$order_stats['count'],
                    ));
                }else{
                    $hours = array();
                    for($i=0;$i<24;$i++){
                        $hours[$i] = 0;
                    }
                    $hours[(int)date('H')] = 1;
                    Mava_Data::add(Mava_Data::TABLE_STATS, array(
                        'type' => 'order',
                        'campaign_id' => (int)$local_campaign_id,
                        'link_id' => (int)$local_link_id,
                        'time' => (int)time(),
                        'year' => (int)date('Y'),
                        'month' => (int)date('Ym'),
                        'day' => (int)date('Ymd'),
                        'hour' => (array)$hours,
                        'count' => 1,
                    ));
                }

                //Thống kê doanh thu
                $revenue_stats = Mava_Data::gets(Mava_Data::TABLE_STATS, array(
                    'campaign_id' => array('$eq' => (int)$local_campaign_id),
                    'link_id' => array('$eq' => (int)$local_link_id),
                    'day' => array('$eq' => (int)date('Ymd')),
                    'type' => array('$eq' => 'revenue')
                ),0,1);

                if(is_array($revenue_stats) && count($revenue_stats) > 0){
                    $revenue_stats = $revenue_stats[0];
                    $hours = $revenue_stats['hour'];
                    $hours[(int)date('H')] += (int)$total_amount;
                    $revenue_stats['count'] += (int)$total_amount;
                    Mava_Data::update(Mava_Data::TABLE_STATS, $revenue_stats['_id'], array(
                        'hour' => (array)$hours,
                        'count' => (int)$revenue_stats['count'],
                    ));
                }else{
                    $hours = array();
                    for($i=0;$i<24;$i++){
                        $hours[$i] = 0;
                    }
                    $hours[(int)date('H')] = (int)$total_amount;
                    Mava_Data::add(Mava_Data::TABLE_STATS, array(
                        'type' => 'revenue',
                        'campaign_id' => (int)$local_campaign_id,
                        'link_id' => (int)$local_link_id,
                        'time' => (int)time(),
                        'year' => (int)date('Y'),
                        'month' => (int)date('Ym'),
                        'day' => (int)date('Ymd'),
                        'hour' => (array)$hours,
                        'count' => (int)$total_amount,
                    ));
                }
                // Cập nhật lượt đặt hàng + giá trị
                $campaignDW = Mava_DataWriter::create('Megabook_DataWriter_AdsCampaign');
                $campaignDW->setExistingData($local_campaign_id);
                $campaignDW->bulkSet(array(
                    'order_count' => $campaign['order_count']+1,
                    'total_revenue' => $campaign['total_revenue'] + $total_amount
                ));
                $campaignDW->save();

                $linkDW = Mava_DataWriter::create('Megabook_DataWriter_AdsCampaignLinks');
                $linkDW->setExistingData($local_link_id);
                $linkDW->bulkSet(array(
                    'order_count' => $link['order_count']+1,
                    'total_revenue' => $link['total_revenue'] + $total_amount
                ));
                $linkDW->save();
                if(is_login()){
                    Mava_Data::update(Mava_Data::TABLE_USERS, $mongo_user['_id'], array(
                        'megabook_user_name' => (string)Mava_Visitor::getInstance()->get('custom_title'),
                        'megabook_user_id' => (int)Mava_Visitor::getUserId()
                    ));
                }
            }
        }
    }
}

function ads_tracking(){
    // init session
    $window_id = Mava_Helper_Cookie::getCookie('_mb_window_id');
    $local_campaign_id = (int)Mava_Helper_Cookie::getCookie('_mb_campaign_id');
    $local_link_id = (int)Mava_Helper_Cookie::getCookie('_mb_link_id');
    $campaign_id = (int)Mava_Url::getParam('_mcid');
    $campaignModel = Mava_Model::create('Megabook_Model_AdsCampaign');
    $linkModel = Mava_Model::create('Megabook_Model_AdsCampaignLinks');
    if($campaign_id > 0 && $campaign = $campaignModel->getById($campaign_id)){
        if($campaign['deleted'] == 'no'){
            $original_link = Mava_Url::removeParam(Mava_Url::getCurrentAddress(),array('_mcid'));
            $link = $linkModel->getByUrl($original_link);
            if(is_array($link) && $link['deleted'] == 'no'){
                $link_id = $link['id'];
                if($window_id == ""){
                    $window_id = uniqid();
                    Mava_Helper_Cookie::setCookie('_mb_window_id', $window_id, 86400*30);
                }

                if($local_campaign_id == 0 || $local_campaign_id != $campaign_id){
                    // Tạo mới visit
                    $local_campaign_id = $campaign_id;
                    Mava_Helper_Cookie::setCookie('_mb_campaign_id', $local_campaign_id, 86400*30);
                }

                if($local_link_id == 0 || $local_link_id != $link_id){
                    // Tạo mới visit
                    $local_link_id = $link_id;
                    Mava_Helper_Cookie::setCookie('_mb_link_id', $local_link_id, 86400*30);
                }

                $mongo_user = Mava_Data::gets(Mava_Data::TABLE_USERS, array(
                    'window_id' => array('$eq' => $window_id),
                    'campaign_id' => array('$eq' => $local_campaign_id),
                    'link_id' => array('$eq' => $local_link_id),
                ),0,1);

                if(is_array($mongo_user) && count($mongo_user) > 0){
                    $create_new = false;
                }else{
                    $create_new = true;
                }

                if($create_new == true){
                    $page_visit = array();
                    $referer_url = Mava_Session::get('referer_url');
                    if($referer_url != ""){
                        $url_parse = parse_url($referer_url);
                        $title = $url_parse['host'];
                    }else{
                        $title = __('unknown_page');
                    }
                    $page_visit[] = array(
                        'url' => $referer_url,
                        'title' => $title,
                        'time' => time()
                    );
                    $user_id = Mava_Data::add(Mava_Data::TABLE_USERS, array(
                        'megabook_user_name' => (is_login()?(string)Mava_Visitor::getInstance()->get('custom_title'):__('guest_x', array('id' => $window_id))),
                        'megabook_user_id' => (int)Mava_Visitor::getUserId(),
                        'type' => 'new',
                        'first_visit' => (int)time(),
                        'last_visit' => (int)time(),
                        'window_id' => (string)$window_id,
                        'campaign_id' => (int)$local_campaign_id,
                        'link_id' => (int)$local_link_id,
                        'referer' => (string)$referer_url,
                        'page_visit' => (array)$page_visit,
                        'order' => 0,
                        'order_list' => array(),
                        'revenue' => 0
                    ));

                    // Thống kê campaign
                    $traffic_stats = Mava_Data::gets(Mava_Data::TABLE_STATS, array(
                        'campaign_id' => array('$eq' => (int)$local_campaign_id),
                        'link_id' => array('$eq' => (int)$local_link_id),
                        'day' => array('$eq' => (int)date('Ymd')),
                        'type' => array('$eq' => 'click')
                    ),0,1);

                    if(is_array($traffic_stats) && count($traffic_stats) > 0){
                        $traffic_stats = $traffic_stats[0];
                        $hours = $traffic_stats['hour'];
                        $hours[(int)date('H')]++;
                        $traffic_stats['count']++;
                        Mava_Data::update(Mava_Data::TABLE_STATS, $traffic_stats['_id'], array(
                            'hour' => (array)$hours,
                            'count' => (int)$traffic_stats['count'],
                        ));
                    }else{
                        $hours = array();
                        for($i=0;$i<24;$i++){
                            $hours[$i] = 0;
                        }
                        $hours[(int)date('H')] = 1;
                        Mava_Data::add(Mava_Data::TABLE_STATS, array(
                            'type' => 'click',
                            'campaign_id' => (int)$local_campaign_id,
                            'link_id' => (int)$local_link_id,
                            'time' => (int)time(),
                            'year' => (int)date('Y'),
                            'month' => (int)date('Ym'),
                            'day' => (int)date('Ymd'),
                            'hour' => (array)$hours,
                            'count' => 1,
                        ));
                    }

                    // Cập nhật lượt click
                    $campaignDW = Mava_DataWriter::create('Megabook_DataWriter_AdsCampaign');
                    $campaignDW->setExistingData($local_campaign_id);
                    $campaignDW->set('click_count',$campaign['click_count']+1);
                    $campaignDW->save();

                    $linkDW = Mava_DataWriter::create('Megabook_DataWriter_AdsCampaignLinks');
                    $linkDW->setExistingData($local_link_id);
                    $linkDW->set('click_count',$link['click_count']+1);
                    $linkDW->save();
                }
            }
        }
    }
    // add page visit
    if($window_id != "" && $local_campaign_id > 0 && $campaign = $campaignModel->getById($local_campaign_id)){
        if($campaign['deleted'] == 'no' && $local_link_id > 0 && $link = $linkModel->getById($local_link_id)){
            $mongo_user = Mava_Data::gets(Mava_Data::TABLE_USERS, array(
                'window_id' => array('$eq' => $window_id),
                'campaign_id' => array('$eq' => $local_campaign_id),
                'link_id' => array('$eq' => $local_link_id)
            ),0,1);

            if(is_array($mongo_user) && count($mongo_user) > 0){
                $mongo_user = $mongo_user[0];
                $page_visit = $mongo_user['page_visit'];
                $page_visit[] = array(
                    'url' => Mava_Url::getCurrentAddress(),
                    'title' => Mava_Application::get('seo/title'),
                    'time' => time()
                );
                Mava_Data::update(Mava_Data::TABLE_USERS, $mongo_user['_id'], array(
                    'last_visit' => (int)time(),
                    'page_visit' => (array)$page_visit
                ));
            }
        }
    }
}

function get_menus(){
    /* @var Megabook_Model_Menu $menuModel */
    $menuModel = Mava_Model::create('Megabook_Model_Menu');
    return $menuModel->getAll();
}

function get_book_subject(){
    /* @var Megabook_Model_BookSubject $subjectModel */
    $subjectModel = Mava_Model::create('Megabook_Model_BookSubject');
    return $subjectModel->getAll();
}

function get_book_class(){
    $classModel = Mava_Model::create('Megabook_Model_BookClass');
    return $classModel->getAll();
}

function get_book_set(){
    $setModel = Mava_Model::create('Megabook_Model_BookSet');
    return $setModel->getAll();
}

if(!function_exists('dprint')){
    function dprint($obj){
        echo '<xmp>';
        print_r($obj);
        echo '</xmp>';
    }
}

if(!function_exists('thumb_url')){
    function thumb_url($path, $width, $height, $crop = 1){
        //return Mava_Url::getPageLink($path);
        return Mava_Url::getPageLink('thumb_'. $width .'_'. $height .'_'. $crop .'/'. $path);
    }
}

if(!function_exists('get_all_language')){
    function get_all_language(){
        /* @var $languageModel Mava_Model_Language */
        $languageModel = Mava_Model::create('Mava_Model_Language');
        return $languageModel->getListLanguage();
    }
}

if(!function_exists('get_all_currency')){
    function get_all_currency(){
        /* @var $currencyModel Mava_Model_Currency */
        $currencyModel = Mava_Model::create('Mava_Model_Currency');
        return $currencyModel->getAll();
    }
}

if(!function_exists('get_banners')){
    function get_banners($position, $skip = 0, $limit = 10, $create_position = false, $position_title = ''){
        $language_code = Mava_Visitor::getLanguageCode();
        /* @var Index_Model_Banner $bannerModel */
        $bannerModel = Mava_Model::create('Index_Model_Banner');
        if($create_position == true){
            $position_check = $bannerModel->getPositionByKey($position);
            if(!$position_check){
                $positionDW = Mava_DataWriter::create('Index_DataWriter_BannerPosition');
                $positionDW->bulkSet(array(
                    'title' => $position_title,
                    'position' => $position
                ));
                $positionDW->save();
            }
        }
        $banners = $bannerModel->getBanners($skip,$limit,$position,true);
        $results = array();
        if(is_array($banners['rows'])){
            foreach($banners['rows'] as $item){
                if(isset($item['_data'][$language_code])){
                    $image = json_decode($item['_data'][$language_code]['image'], true);
                    if(is_array($image) && count($image) > 0){
                        $image = json_decode($image[0], true);
                    }
                    $results[] = array(
                        'id' => $item['id'],
                        'position_id' => $item['position_id'],
                        'title' => $item['_data'][$language_code]['title'],
                        'subtitle' => $item['_data'][$language_code]['subtitle'],
                        'href' => $item['_data'][$language_code]['href'],
                        'background' => $item['_data'][$language_code]['background'],
                        'image' => image_url($image['image']),
                        'image_width' => $image['width'],
                        'image_height' => $image['height']
                    );
                }
            }
        }
        return $results;
    }
}

if(!function_exists('text_loop')){
    /**
     * @param $text
     * @param $times
     * @return string
     */
    function text_loop($text, $times){
        $result = "";
        for($i=0;$i<$times;$i++){
            $result .= $text;
        }
        return $result;
    }
}

if(!function_exists('count_notify')){
    function count_notify(){
        /* @var Mava_Model_Notification $notifyModel */
        $notifyModel = Mava_Model::create('Mava_Model_Notification');
        $count = $notifyModel->getUnreadNotify();
        return min((int)$count, 99);
    }
}

if(!function_exists('add_notify')){
    function add_notify($userId, $type, $content, $href = ''){
        /* @var Mava_Model_Notification $notifyModel */
        $notifyModel = Mava_Model::create('Mava_Model_Notification');
        $notifyModel->add($userId, $type, $content, $href);
        return true;
    }
}

if(!function_exists('mask_email')){
    function mask_email($email){
        $email_split = explode('@', $email);
        if(count($email_split) == 2){
            return '******'. substr($email_split[0], sizeof($email_split[0])-4, 3) .'@'. $email_split[1];
        }else{
            return '******';
        }
    }
}

if(!function_exists('thumbs')){
    function thumbs($pathToImages, $pathToThumbs, $thumbWidth, $thumbHeight, $logo = false){
        $file_name=$pathToImages;
        $crop_height=$thumbHeight;
        $crop_width=$thumbWidth;
        $file_type= explode('.', $file_name);
        $file_type = $file_type[count($file_type) -1];
        $file_type=strtolower($file_type);

        $original_image_size = getimagesize($file_name);
        $original_width = $original_image_size[0];
        $original_height = $original_image_size[1];

        if($file_type=='jpg')
        {
            $original_image_gd = imagecreatefromjpeg($file_name);
        }

        if($file_type=='gif')
        { $original_image_gd = imagecreatefromgif($file_name);
        }

        if($file_type=='png')
        {
            $original_image_gd = imagecreatefrompng($file_name);
        }

        $cropped_image_gd = imagecreatetruecolor($crop_width, $crop_height);
        $wm = $original_width /$crop_width;
        $hm = $original_height /$crop_height;
        $h_height = $crop_height/2;
        $w_height = $crop_width/2;

        if($original_width > $original_height )
        {
            $adjusted_width =$original_width / $hm;
            $half_width = $adjusted_width / 2;
            $int_width = $half_width - $w_height;

            imagecopyresampled($cropped_image_gd ,$original_image_gd ,-$int_width,0,0,0, $adjusted_width, $crop_height, $original_width , $original_height );
        }
        elseif(($original_width < $original_height ) || ($original_width == $original_height ))
        {
            $adjusted_height = $original_height / $wm;
            $half_height = $adjusted_height / 2;
            $int_height = $half_height - $h_height;

            imagecopyresampled($cropped_image_gd , $original_image_gd ,0,-$int_height,0,0, $crop_width, $adjusted_height, $original_width , $original_height );
        }
        else {
            imagecopyresampled($cropped_image_gd , $original_image_gd ,0,0,0,0, $crop_width, $crop_height, $original_width , $original_height );
        }
        imagejpeg($cropped_image_gd,"{$pathToThumbs}",90);
        if($logo == true){
            $logo_file = BASEDIR ."/logo_mask.png";
            $image_file = "{$pathToThumbs}";
            $targetfile = "{$pathToThumbs}";
            $photo = @imagecreatefromjpeg($image_file);
            $fotoW = @imagesx($photo);
            $fotoH = @imagesy($photo);
            $logoImage = @imagecreatefrompng($logo_file);
            $logoW = @imagesx($logoImage);
            $logoH = @imagesy($logoImage);
            $photoFrame = @imagecreatetruecolor($fotoW,$fotoH);
            $dest_x = $fotoW - $logoW;
            $dest_y = $fotoH - $logoH;
            @imagecopyresampled($photoFrame, $photo, 0, 0, 0, 0, $fotoW, $fotoH, $fotoW, $fotoH);
            if($fotoW<350 || $fotoH<250){
                $dest_x += 300;
            }
            @imagecopy($photoFrame, $logoImage, $dest_x, $dest_y, 0, 0, $logoW, $logoH);
            @imagejpeg($photoFrame, $targetfile,90);
        }
    }
}

if(!function_exists('print_birthday')){
    function print_birthday($birthday){
        if($birthday > 0){
                $age = (date("md", $birthday) > date("md")
                    ? ((date("Y") - date("Y", $birthday)) - 1)
                    : (date("Y") - date("Y", $birthday)));
                return date('d/m/Y', $birthday) .' ('. Mava_Phrase::getPhrase('x_age', array('count' => $age)) .')';
        }else{
            return '';
        }
    }
}

if(!function_exists('__')){
    function __($key, $params = array(), $returnKeyIfNotExist = true){
        if(Mava_Url::getParam('_phrase') == 1 && Mava_Visitor::getInstance()->isSuperAdmin()){
            return '['. $key .']'. Mava_Phrase::getPhrase($key, $params, $returnKeyIfNotExist);
        }else if(Mava_Url::getParam('_phrase') == 2 && Mava_Visitor::getInstance()->isSuperAdmin()){
            return '['. $key .']';
        }else{
            return Mava_Phrase::getPhrase($key, $params, $returnKeyIfNotExist);
        }
    }
}

if(!function_exists('get_city_title')){
    function get_city_title($cityId){
        /* @var Mava_Model_City $cityModel */
        $cityModel = Mava_Model::create('Mava_Model_City');
        $city = $cityModel->getCityById($cityId);
        if($city){
            return $city['title'];
        }else{
            return '';
        }
    }
}


if(!function_exists('get_all_city')){
    function get_all_city(){
        /* @var Mava_Model_City $cityModel */
        $cityModel = Mava_Model::create('Mava_Model_City');
        $city = $cityModel->getAllCity();
        if($city){
            return $city;
        }else{
            return array();
        }
    }
}


if(!function_exists('print_time')){
    function print_time($timestamp, $long_phrase = true){
        $day_short_phrase = array('CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7');
        $day_long_phrase = array('Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ 5', 'Thứ 6', 'Thứ 7');

        $monday = strtotime('monday this week');

        if (date('Ymd', $timestamp) == date('Ymd')) {
            return date('H:i', $timestamp);
        } else if (date('Ymd', $timestamp) > date('Ymd')) {
            return date('d/m/Y', $timestamp);
        } else if ($timestamp > $monday) {
            $timestamp = strtotime("midnight", $timestamp);
            $day = intval(floor(($timestamp - $monday) / 86400)) + 1;
            if ($long_phrase == true) {
                return $day_long_phrase[$day];
            } else {
                return $day_short_phrase[$day];
            }
        } else if (date('Y', $timestamp) < date('Y')) {
            return date('d/m/Y', $timestamp);
        } else {
            return date('d', $timestamp) . ' tháng ' . date('m', $timestamp);
        }
    }
}

if(!function_exists('get_user_lead')){
    function get_user_lead(){
        $data = array();
        $gender = Mava_Visitor::getInstance()->get('gender');
        if($gender != ""){
            $data[] = Mava_Phrase::getPhrase($gender);
        }

        $birthday = (int)Mava_Visitor::getInstance()->get('birthday');
        if($birthday > 0){
            $birthday = (date("md", $birthday) > date("md")
                ? ((date("Y") - date("Y", $birthday)) - 1)
                : (date("Y") - date("Y", $birthday)));
            $data[] = Mava_Phrase::getPhrase('x_age', array('count' => $birthday));
        }

        $city = (int)Mava_Visitor::getInstance()->get('city_id');
        if($city > 0){
            $cityModel = Mava_Model::create('Mava_Model_City');
            $city = $cityModel->getCityById($city);
            if($city){
                $data[] = $city['title'];
            }
        }

        if(count($data) > 0){
            return htmlspecialchars(implode(' - ', $data));
        }else{
            return '';
        }
    }
}

if(!function_exists('print_dow')){
    function print_dow($dow){
        $dow = explode(',', $dow);
        if(count($dow) > 1){
            $dow = Mava_Phrase::getPhrase('dow_x') .' '. implode(', '. Mava_Phrase::getPhrase('dow_x') .' ', $dow);
        }else{
            $dow = Mava_Phrase::getPhrase('dow_x') . $dow[0];
        }
        return $dow;
    }
}

if(!function_exists('get_fullname')){
    function get_fullname(){
        return htmlspecialchars(Mava_Visitor::getInstance()->get('custom_title'));
    }
}

if(!function_exists('date_to_time')){
    /**
     * @param $date
     * @return int
     */
    function date_to_time($date = '', $separator = '/'){
        if($date == ''){
            return 0;
        }
        $date_seg = explode(' ', $date);
        $date_day = explode($separator, $date_seg[0]);
        if(isset($date_seg[1]) && $date_seg[1] != ''){
            $date_hour = explode(':', $date_seg[1]);
        }else{
            $date_hour = array(0,0,0);
        }

        if(count($date_hour) != 3){
            $date_hour = array(0,0,0);
        }

        if(count($date_day) != 3){
            $date_day = array(0,0,0);
        }

        return mktime($date_hour[0],$date_hour[1],$date_hour[2],$date_day[1],$date_day[0],$date_day[2]);
    }
}

if(!function_exists('is_debug')){
    function is_debug(){
        return Mava_Application::debugMode();
    }
}

function get_static_domain(){
    if(Mava_Application::getConfig('static_domain') != ""){
        return Mava_Application::getConfig('static_domain');
    }else{
        return Mava_Application::getOptions()->staticDomain;
    }
}

if(!function_exists('image_url')){
    function image_url($filePath){
        return get_static_domain() .'/'. str_replace('\\','/',$filePath);
    }
}

if(!function_exists('upload_image')){
    function upload_image($folder, $inputName){
        $folder = 'data/images/'. $folder;
        if($inputName != "" && isset($_FILES[$inputName]) && $_FILES[$inputName]['tmp_name'] != ""){
            $time = time();
            $file_ext = explode('.',$_FILES[$inputName]['name']);
            $type = strtolower($file_ext[count($file_ext)-1]);
            if(in_array($type,array('jpg','gif','jpeg','png','bmp'))){
                $dir = mkdir_by_date($time, BASEDIR . '/' . $folder);
                $file_name = md5($_FILES[$inputName]['name']) .'_'. time() .'.'. $type;
                $file_path = BASEDIR . '/' . $folder . '/' . $dir . '/' . $file_name;
                if(move_uploaded_file($_FILES[$inputName]['tmp_name'], $file_path)){
                    return array(
                        'error' => 0,
                        'image' => $folder . '/' . $dir . '/' . $file_name
                    );
                }else{
                    return array(
                        'error' => 1,
                        'message' => Mava_Phrase::getPhrase('can_not_upload_image')
                    );
                }
            }else{
                return array(
                    'error' => 1,
                    'message' => Mava_Phrase::getPhrase('invalid_image_type')
                );
            }
        }else{
            return array(
                'error' => 1,
                'message' => Mava_Phrase::getPhrase('image_file_empty')
            );
        }
    }
}

if(!function_exists('upload_multiple_image')){
    function upload_multiple_image($folder, $inputName){
        $result = array();
        $folder = 'data/images/'. $folder;
        if($inputName != "" && isset($_FILES[$inputName]) && $_FILES[$inputName]['tmp_name'] != ""){
            if(count($_FILES[$inputName]['tmp_name']) > 0){
                $count = 0;
                foreach($_FILES[$inputName]['tmp_name'] as $item){
                    $time = time();
                    $file_ext = explode('.',$_FILES[$inputName]['name'][$count]);
                    $type = strtolower($file_ext[count($file_ext)-1]);
                    if(in_array($type,array('jpg','gif','jpeg','png','bmp'))){
                        $dir = mkdir_by_date($time, BASEDIR . '/' . $folder);
                        $file_name = md5($_FILES[$inputName]['name'][$count]) .'_'. time() .'.'. $type;
                        $file_path = BASEDIR . '/' . $folder . '/' . $dir . '/' . $file_name;
                        list($width,$height) = getimagesize($item);
                        if(move_uploaded_file($item, $file_path)){
                            $result[] = array(
                                'image' => $folder . '/' . $dir . '/' . $file_name,
                                'width' => $width,
                                'height' => $height
                            );
                            $count++;
                        }
                    }
                }
            }
        }
        return $result;
    }
}

if(!function_exists('mkdir_by_date')){
    function mkdir_by_date($date, $dir = '.', $create_if_not_exist = true) {
        list($y, $m, $d) = explode('-', date('Y-m-d', $date));
        if($create_if_not_exist){
            !is_dir("$dir/$y") && mkdir("$dir/$y", 0777);
            !is_dir("$dir/$y/$m") && mkdir("$dir/$y/$m", 0777);
            !is_dir("$dir/$y/$m/$d") && mkdir("$dir/$y/$m/$d", 0777);
        }
        return "$y/$m/$d";
    }
}

if(!function_exists('mkdir_by_id')){
    function mkdir_by_id($dir = '.',$id){
        $id = sprintf("%09d", $id);
        $dir1 = substr($id, 0, 3);
        $dir2 = substr($id, 3, 2);
        $dir3 = substr($id, 5, 2);
        $dir4 = substr($id, 7, 2);
        !is_dir("$dir/$dir1") && mkdir("$dir/$dir1", 0777);
        !is_dir("$dir/$dir1/$dir2") && mkdir("$dir/$dir1/$dir2", 0777);
        !is_dir("$dir/$dir1/$dir2/$dir3") && mkdir("$dir/$dir1/$dir2/$dir3", 0777);
        !is_dir("$dir/$dir1/$dir2/$dir3/$dir4") && mkdir("$dir/$dir1/$dir2/$dir3/$dir4", 0777);
        return $dir1.'/'.$dir2.'/'.$dir3.'/'.$dir4;
    }
}

if(!function_exists('get_option_file_url')){
    function get_option_file_url($filename){
        return get_static_domain() . '/data/images/option/' . $filename;
    }
}

if(!function_exists('is_login')){
    function is_login(){
        return (Mava_Visitor::getUserId() > 0)?true:false;
    }
}

if(!function_exists('get_avatar_url')){
    function get_avatar_url($size = 'small', $uid = 0){
        if($uid == 0){
            $uid = Mava_Visitor::getUserId();
        }
        $size = in_array($size, array('big', 'middle', 'small', 'org')) ? $size : 'middle';
        $uid = abs(intval($uid));
        $uid = sprintf("%09d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);
        $dir4 = substr($uid, 7, 2);
        $avatar_path = '/data/images/avatar/' . $dir1.'/'.$dir2.'/'.$dir3.'/'. $dir4 ."_avatar_$size.jpg";
        if(!file_exists(BASEDIR . $avatar_path)){
            return get_option_file_url(Mava_Application::getOptions()->no_avatar);
        }
        return get_static_domain() . $avatar_path;
    }
}

if(!function_exists('user_has_avatar')){
    function user_has_avatar($uid = 0){
        if($uid == 0){
            $uid = Mava_Visitor::getUserId();
        }
        $size = 'small';
        $uid = abs(intval($uid));
        $uid = sprintf("%09d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);
        $dir4 = substr($uid, 7, 2);
        $avatar_path = '/data/images/avatar/' . $dir1.'/'.$dir2.'/'.$dir3.'/'. $dir4 ."_avatar_$size.jpg";
        if(!file_exists(BASEDIR . $avatar_path)){
            return false;
        }else{
            return true;
        }
    }
}

if(!function_exists('ip')){
    function ip(){
        return $_SERVER['REMOTE_ADDR'];
    }
}