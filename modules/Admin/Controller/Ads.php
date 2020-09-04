<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 1/17/17
 * @Time: 1:36 PM
 */
class Admin_Controller_Ads extends Mava_AdminController {
    public function indexAction(){
        Mava_Application::set('seo/title', __('admin_ads'));
        // thống kê quảng cáo
        $campaignModel = $this->_getAdsCampaignModel();
        $campaigns = $campaignModel->getList(0, 10);
        $start_date = Mava_Url::getParam('start_date');
        $end_date = Mava_Url::getParam('end_date');
        if($start_date == ''){
            $start_date = date('d/m/y');
        }
        if($end_date == ''){
            $end_date = date('d/m/Y');
        }

        list($start_day, $start_month, $start_year) = explode('/', $start_date);
        list($end_day, $end_month, $end_year) = explode('/', $end_date);

        $start_time = mktime(0,0,0,$start_month,$start_day,$start_year);
        $end_time = mktime(0,0,0,$end_month,$end_day,$end_year);
        if($end_time < $start_time){
            $end_time = $start_time + 86399;
        }

        $total_click = 0;
        $clicks = array();
        $total_order = 0;
        $orders = array();
        $xAxis = array();
        if($end_time == $start_time){
            // by hour
            for($i=0;$i<24;$i++){
                $xAxis[(int)$i] = $i .':00';
                $clicks[(int)$i] = 0;
                $orders[(int)$i] = 0;
            }
            $click_data = Mava_Data::gets(Mava_Data::TABLE_STATS, array(
                'day' => array('$eq' => (int)date('Ymd', $start_time)),
                'type' => array('$eq' => 'click')
            ),0,0);
            if(is_array($click_data) && count($click_data) > 0){
                foreach($click_data as $item){
                    if(isset($item['hour']) && is_array($item['hour']) && count($item['hour']) > 0){
                        foreach($item['hour'] as $h => $c){
                            $clicks[(int)$h] += (int)$c;
                            $total_click += (int)$c;
                        }
                    }
                }
            }
            $order_data = Mava_Data::gets(Mava_Data::TABLE_STATS, array(
                'day' => array('$eq' => (int)date('Ymd', $start_time)),
                'type' => array('$eq' => 'order')
            ),0,0);
            if(is_array($order_data) && count($order_data) > 0){
                foreach($order_data as $item){
                    if(isset($item['hour']) && is_array($item['hour']) && count($item['hour']) > 0){
                        foreach($item['hour'] as $h => $c){
                            $orders[(int)$h] += (int)$c;
                            $total_order += (int)$c;
                        }
                    }
                }
            }
        }else if($end_time-$start_time < 86400*30){
            // by day
            $tmp_start_time = $start_time;
            $tmp_end_time = $end_time;
            while($tmp_start_time <= $tmp_end_time){
                $xAxis[] = date('d/m', $tmp_start_time);
                $clicks[date('d/m', $tmp_start_time)] = 0;
                $orders[date('d/m', $tmp_start_time)] = 0;
                $tmp_start_time += 86400;
            }

            $click_data = Mava_Data::gets(Mava_Data::TABLE_STATS, array(
                'day' => array('$gte' => (int)date('Ymd', $start_time),'$lte' => (int)date('Ymd', $end_time)),
                'type' => array('$eq' => 'click')
            ),0,0);
            if(is_array($click_data) && count($click_data) > 0){
                foreach($click_data as $item){
                    $clicks[date('d/m', $item['time'])] += $item['count'];
                    $total_click += $item['count'];
                }
            }

            $order_data = Mava_Data::gets(Mava_Data::TABLE_STATS, array(
                'day' => array('$gte' => (int)date('Ymd', $start_time),'$lte' => (int)date('Ymd', $end_time)),
                'type' => array('$eq' => 'order')
            ),0,0);
            if(is_array($order_data) && count($order_data) > 0){
                foreach($order_data as $item){
                    $orders[date('d/m', $item['time'])] += $item['count'];
                    $total_order += $item['count'];
                }
            }
        }else{
            // by month
            $xAxis = array();
            $tmp_start_time = $start_time;
            $tmp_end_time = $end_time;
            while($tmp_start_time <= $tmp_end_time){
                $xAxis[] = date('m/Y', $tmp_start_time);
                $clicks[date('m/Y', $tmp_start_time)] = 0;
                $orders[date('m/Y', $tmp_start_time)] = 0;
                $tmp_start_time += 86400*30;  // TODO need fix day of month
            }

            $click_data = Mava_Data::gets(Mava_Data::TABLE_STATS, array(
                'month' => array('$gte' => (int)date('Ym', $start_time),'$lte' => (int)date('Ym', $end_time)),
                'type' => array('$eq' => 'click')
            ),0,0);
            if(is_array($click_data) && count($click_data) > 0){
                foreach($click_data as $item){
                    $clicks[date('m/Y', $item['time'])] += $item['count'];
                    $total_click += $item['count'];
                }
            }

            $order_data = Mava_Data::gets(Mava_Data::TABLE_STATS, array(
                'month' => array('$gte' => (int)date('Ym', $start_time),'$lte' => (int)date('Ym', $end_time)),
                'type' => array('$eq' => 'order')
            ),0,0);

            if(is_array($order_data) && count($order_data) > 0){
                foreach($order_data as $item){
                    $orders[date('m/Y', $item['time'])] += $item['count'];
                    $total_order += $item['count'];
                }
            }
        }

        $viewParams = array(
            'xAxis' => $xAxis,
            'clicks' => array_values($clicks),
            'total_click' => $total_click,
            'orders' => array_values($orders),
            'total_order' => $total_order,
            'campaigns' => $campaigns['rows'],
            'start_date' => date('d/m/Y', $start_time),
            'end_date' => date('d/m/Y', $end_time)
        );
        return $this->responseView('Admin_View_Ads_Stats', $viewParams);
    }

    public function campaignGroupsAction(){
        Mava_Application::set('seo/title', __('ads_campaign_group'));
        $page = max((int)Mava_Url::getParam('page'),1);
        $limit = 50;
        $skip = ($page-1)*$limit;
        $campaignGroupModel = $this->_getAdsCampaignGroupModel();
        $campaigns = $campaignGroupModel->getList($skip, $limit);
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/ads/index'),
            'text' => __('admin_ads')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('ads_campaign_group')
        );
        $params = array();
        $pagination = Mava_View::buildPagination(Mava_Url::getPageLink('admin/ads/campaign-groups', $params),ceil($campaigns['total']/$limit),$page);

        return $this->responseView('Admin_View_Ads_CampaignGroup', array(
            'page' => $page,
            'skip' => $skip,
            'limit' => $limit,
            'total' => $campaigns['total'],
            'pagination' => $pagination,
            'campaign_groups' => $campaigns['rows'],
            'breadcrumbs' => $breadcrumbs
        ));
    }

    public function addCampaignGroupAction(){
        Mava_Application::set('seo/title', __('add_campaign_group'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/ads/index'),
            'text' => __('admin_ads')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/ads/campaign-groups'),
            'text' => __('ads_campaign_group')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_campaign_group')
        );
        $error_message = '';
        if(Mava_Url::isPost()){
            $postData = Mava_Url::getParams();
            if(!isset($postData['campaignGroupTitle']) || $postData['campaignGroupTitle'] == ''){
                $error_message = __('campaign_group_title_empty');
            }else{
                $campaignGroupDW = $this->_getAdsCampaignGroupDataWriter();
                $campaignGroupDW->bulkSet(array(
                    'title' => $postData['campaignGroupTitle'],
                    'color' => $postData['campaignGroupColor'],
                    'sort_order' => $postData['campaignGroupSortOrder'],
                ));
                if($campaignGroupDW->save()){
                    Mava_Url::redirect(Mava_Url::getPageLink('admin/ads/campaign-groups', array('added' => $campaignGroupDW->get('id'), 'added' => 1)));
                }else{
                    $error_message = __('can_not_add_campaign_group');
                }
            }
        }
        return $this->responseView('Admin_View_Ads_AddCampaignGroup', array(
            'breadcrumbs' => $breadcrumbs,
            'error_message' => $error_message
        ));
    }

    public function editCampaignGroupAction(){
        $campaign_id = (int)Mava_Url::getParam('id');
        $campaignGroupModel = $this->_getAdsCampaignGroupModel();
        if($campaign_id > 0 && $campaign = $campaignGroupModel->getById($campaign_id)){
            Mava_Application::set('seo/title', __('edit_campaign_group') .' #'. $campaign_id);
            $breadcrumbs = array();
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('admin/index/index'),
                'text' => __('admin_page')
            );
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('admin/ads/index'),
                'text' => __('admin_ads')
            );
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('admin/ads/campaign-groups'),
                'text' => __('ads_campaign')
            );
            $breadcrumbs[] = array(
                'url' => '',
                'text' => __('edit_campaign_group')
            );
            $viewParams = array(
                'breadcrumbs' => $breadcrumbs,
                'campaign' => $campaign,
            );
            $error_message = '';
            if(Mava_Url::isPost()){
                $postData = Mava_Url::getParams();
                if(!isset($postData['campaignGroupTitle']) || $postData['campaignGroupTitle'] == ''){
                    $error_message = __('campaign_group_title_empty');
                }else{
                    $campaignGroupDW = $this->_getAdsCampaignGroupDataWriter();
                    $campaignGroupDW->setExistingData($campaign_id);
                    $campaignGroupDW->bulkSet(array(
                        'title' => $postData['campaignGroupTitle'],
                        'color' => $postData['campaignGroupColor'],
                        'sort_order' => $postData['campaignGroupSortOrder'],
                    ));

                    if($campaignGroupDW->save()){
                        return $this->responseRedirect(Mava_Url::getPageLink('admin/ads/campaign-groups', array('updated' => $campaign_id)));
                    }else{
                        $error_message = __('can_not_edit_campaign_group');
                    }
                }
            }
            $viewParams['error_message'] = $error_message;
            return $this->responseView('Admin_View_Ads_EditCampaignGroup', $viewParams);
        }else{
            return $this->responseError(__('campaign_group_not_found'), Mava_Error::NOT_FOUND);
        }
    }


    public function deleteCampaignGroupAction(){
        $campaign_id = (int)Mava_Url::getParam('id');
        $campaignGroupModel = $this->_getAdsCampaignGroupModel();
        if($campaign_id > 0 && $campaign = $campaignGroupModel->getById($campaign_id)){
            $campaignGroupDW = $this->_getAdsCampaignGroupDataWriter();
            $campaignGroupDW->setExistingData($campaign_id);
            // unGroup campaign
            $campaignModel = $this->_getAdsCampaignModel();
            $campaignModel->unGroupCampaign($campaign_id);
            if($campaignGroupDW->delete()){
                return $this->responseJson(array(
                    'status' => 1,
                    'message' => __('campaign_group_deleted')
                ));
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('can_not_delete_campaign_group')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('campaign_group_not_found')
            ));
        }
    }

    public function campaignsAction(){
        Mava_Application::set('seo/title', __('ads_campaign'));
        $search_term = Mava_Url::getParam('q');
        $group_id = (int)Mava_Url::getParam('group_id');
        $sort_by = Mava_Url::getParam('sort_by');
        $sort_dir = Mava_Url::getParam('sort_dir');
        $page = max((int)Mava_Url::getParam('page'),1);
        $limit = 50;
        $skip = ($page-1)*$limit;
        $campaignModel = $this->_getAdsCampaignModel();
        $campaigns = $campaignModel->getList($skip, $limit, $sort_by, $sort_dir, $search_term, $group_id);
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/ads/index'),
            'text' => __('admin_ads')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/ads/campaigns'),
            'text' => __('ads_campaign')
        );
        $params = array();
        if($sort_by != ""){
            $params['sort_by'] = $sort_by;
        }
        if($sort_by != ""){
            $params['sort_dir'] = $sort_dir;
        }
        $pagination = Mava_View::buildPagination(Mava_Url::getPageLink('admin/ads/campaigns', $params),ceil($campaigns['total']/$limit),$page);
        $campaignGroupModel = $this->_getAdsCampaignGroupModel();
        $groups = $campaignGroupModel->getAll();
        return $this->responseView('Admin_View_Ads_Campaign', array(
            'sort_by' => $sort_by,
            'sort_dir' => $sort_dir,
            'search_term' => $search_term,
            'group_id' => $group_id,
            'groups' => $groups,
            'page' => $page,
            'skip' => $skip,
            'limit' => $limit,
            'total' => $campaigns['total'],
            'pagination' => $pagination,
            'campaigns' => $campaigns['rows'],
            'breadcrumbs' => $breadcrumbs
        ));
    }

    public function addCampaignAction(){
        Mava_Application::set('seo/title', __('add_campaign'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/ads/index'),
            'text' => __('admin_ads')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/ads/campaigns'),
            'text' => __('ads_campaign')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_campaign')
        );
        $error_message = '';
        if(Mava_Url::isPost()){
            $postData = Mava_Url::getParams();
            if(!isset($postData['campaignTitle']) || $postData['campaignTitle'] == ''){
                $error_message = __('campaign_name_empty');
            }else{
                $campaignDW = $this->_getAdsCampaignDataWriter();
                $campaignDW->bulkSet(array(
                    'title' => $postData['campaignTitle'],
                    'group_id' => $postData['campaignGroupID'],
                    'note' => $postData['campaignNote'],
                    'click_count' => 0,
                    'order_count' => 0,
                    'total_revenue' => 0,
                    'created_by' => Mava_Visitor::getUserId(),
                    'created_time' => time(),
                    'deleted' => 'no'
                ));
                if($campaignDW->save()){
                    Mava_Url::redirect(Mava_Url::getPageLink('admin/ads/view-campaign', array('id' => $campaignDW->get('id'), 'added' => 1)));
                }else{
                    $error_message = __('can_not_add_campaign');
                }
            }
        }
        $campaignGroupModel = $this->_getAdsCampaignGroupModel();
        $groups = $campaignGroupModel->getAll();
        return $this->responseView('Admin_View_Ads_AddCampaign', array(
            'breadcrumbs' => $breadcrumbs,
            'campaign_groups' => $groups,
            'error_message' => $error_message
        ));
    }

    public function editCampaignAction(){
        $campaign_id = (int)Mava_Url::getParam('id');
        $campaignModel = $this->_getAdsCampaignModel();
        if($campaign_id > 0 && $campaign = $campaignModel->getById($campaign_id)){
            Mava_Application::set('seo/title', __('edit_campaign') .' #'. $campaign_id);
            $breadcrumbs = array();
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('admin/index/index'),
                'text' => __('admin_page')
            );
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('admin/ads/index'),
                'text' => __('admin_ads')
            );
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('admin/ads/campaigns'),
                'text' => __('ads_campaign')
            );
            $breadcrumbs[] = array(
                'url' => '',
                'text' => __('edit_campaign')
            );
            $campaignGroupModel = $this->_getAdsCampaignGroupModel();
            $groups = $campaignGroupModel->getAll();
            $viewParams = array(
                'breadcrumbs' => $breadcrumbs,
                'campaign' => $campaign,
                'campaign_groups' => $groups,
            );
            $error_message = '';
            if(Mava_Url::isPost()){
                $postData = Mava_Url::getParams();
                if(!isset($postData['campaignTitle']) || $postData['campaignTitle'] == ''){
                    $error_message = __('campaign_title_empty');
                }else{
                    $campaignDW = $this->_getAdsCampaignDataWriter();
                    $campaignDW->setExistingData($campaign_id);
                    $campaignDW->bulkSet(array(
                        'title' => $postData['campaignTitle'],
                        'group_id' => $postData['campaignGroupID'],
                        'note' => $postData['campaignNote']
                    ));

                    if($campaignDW->save()){
                        return $this->responseRedirect(Mava_Url::getPageLink('admin/ads/campaigns', array('updated' => $campaign_id)));
                    }else{
                        $error_message = __('can_not_edit_campaign');
                    }
                }
            }
            $viewParams['error_message'] = $error_message;
            return $this->responseView('Admin_View_Ads_EditCampaign', $viewParams);
        }else{
            return $this->responseError(__('campaign_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    public function deleteCampaignAction(){
        $campaign_id = (int)Mava_Url::getParam('id');
        $campaignModel = $this->_getAdsCampaignModel();
        if($campaign_id > 0 && $campaign = $campaignModel->getById($campaign_id)){
            $campaignDW = $this->_getAdsCampaignDataWriter();
            $campaignDW->setExistingData($campaign_id);
            $campaignDW->bulkSet(array(
                'deleted' => 'yes'
            ));
            if($campaignDW->save()){
                return $this->responseJson(array(
                    'status' => 1,
                    'message' => __('campaign_deleted')
                ));
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('can_not_delete_campaign')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('campaign_not_found')
            ));
        }
    }

    public function viewCampaignAction(){
        $campaign_id = (int)Mava_Url::getParam('id');
        $link_id = (int)Mava_Url::getParam('link_id');
        $campaignModel = $this->_getAdsCampaignModel();
        if($campaign_id > 0 && $campaign = $campaignModel->getById($campaign_id)){
            Mava_Application::set('seo/title', __('view_campaign') .' #'. $campaign_id);
            $breadcrumbs = array();
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('admin/index/index'),
                'text' => __('admin_page')
            );
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('admin/ads/index'),
                'text' => __('admin_ads')
            );
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('admin/ads/campaigns'),
                'text' => __('ads_campaign')
            );
            $breadcrumbs[] = array(
                'url' => '',
                'text' => __('view_campaign')
            );

            $start_date = Mava_Url::getParam('start_date');
            $end_date = Mava_Url::getParam('end_date');
            if($start_date == ''){
                $start_date = date('d/m/y');
            }
            if($end_date == ''){
                $end_date = date('d/m/Y');
            }

            list($start_day, $start_month, $start_year) = explode('/', $start_date);
            list($end_day, $end_month, $end_year) = explode('/', $end_date);

            $start_time = mktime(0,0,0,$start_month,$start_day,$start_year);
            $end_time = mktime(0,0,0,$end_month,$end_day,$end_year);
            if($end_time < $start_time){
                $end_time = $start_time + 86399;
            }

            $total_click = 0;
            $clicks = array();
            $total_order = 0;
            $orders = array();
            $xAxis = array();
            if($end_time == $start_time){
                // by hour
                for($i=0;$i<24;$i++){
                    $xAxis[(int)$i] = $i .':00';
                    $clicks[(int)$i] = 0;
                    $orders[(int)$i] = 0;
                }
                $cond = array(
                    'day' => array('$eq' => (int)date('Ymd', $start_time)),
                    'type' => array('$eq' => 'click'),
                    'campaign_id' => array('$eq' => (int)$campaign_id)
                );
                if($link_id > 0){
                    $cond['link_id'] = array('$eq' => (int)$link_id);
                }
                $click_data = Mava_Data::gets(Mava_Data::TABLE_STATS, $cond,0,0);
                if(is_array($click_data) && count($click_data) > 0){
                    foreach($click_data as $item){
                        if(isset($item['hour']) && is_array($item['hour']) && count($item['hour']) > 0){
                            foreach($item['hour'] as $h => $c){
                                $clicks[(int)$h] += (int)$c;
                                $total_click += (int)$c;
                            }
                        }
                    }
                }

                $cond = array(
                    'day' => array('$eq' => (int)date('Ymd', $start_time)),
                    'type' => array('$eq' => 'order'),
                    'campaign_id' => array('$eq' => (int)$campaign_id)
                );
                if($link_id > 0){
                    $cond['link_id'] = array('$eq' => (int)$link_id);
                }
                $order_data = Mava_Data::gets(Mava_Data::TABLE_STATS, $cond,0,0);
                if(is_array($order_data) && count($order_data) > 0){
                    foreach($order_data as $item){
                        if(isset($item['hour']) && is_array($item['hour']) && count($item['hour']) > 0){
                            foreach($item['hour'] as $h => $c){
                                $orders[(int)$h] += (int)$c;
                                $total_order += (int)$c;
                            }
                        }
                    }
                }
            }else if($end_time-$start_time < 86400*30){
                // by day
                $tmp_start_time = $start_time;
                $tmp_end_time = $end_time;
                while($tmp_start_time <= $tmp_end_time){
                    $xAxis[] = date('d/m', $tmp_start_time);
                    $clicks[date('d/m', $tmp_start_time)] = 0;
                    $orders[date('d/m', $tmp_start_time)] = 0;
                    $tmp_start_time += 86400;
                }

                $cond = array(
                    'day' => array('$gte' => (int)date('Ymd', $start_time),'$lte' => (int)date('Ymd', $end_time)),
                    'type' => array('$eq' => 'click'),
                    'campaign_id' => array('$eq' => (int)$campaign_id)
                );
                if($link_id > 0){
                    $cond['link_id'] = array('$eq' => (int)$link_id);
                }

                $click_data = Mava_Data::gets(Mava_Data::TABLE_STATS, $cond,0,0);
                if(is_array($click_data) && count($click_data) > 0){
                    foreach($click_data as $item){
                        $clicks[date('d/m', $item['time'])] += $item['count'];
                        $total_click += $item['count'];
                    }
                }

                $cond = array(
                    'day' => array('$gte' => (int)date('Ymd', $start_time),'$lte' => (int)date('Ymd', $end_time)),
                    'type' => array('$eq' => 'order'),
                    'campaign_id' => array('$eq' => (int)$campaign_id)
                );
                if($link_id > 0){
                    $cond['link_id'] = array('$eq' => (int)$link_id);
                }

                $order_data = Mava_Data::gets(Mava_Data::TABLE_STATS, $cond,0,0);
                if(is_array($order_data) && count($order_data) > 0){
                    foreach($order_data as $item){
                        $orders[date('d/m', $item['time'])] += $item['count'];
                        $total_order += $item['count'];
                    }
                }
            }else{
                // by month
                $xAxis = array();
                $tmp_start_time = $start_time;
                $tmp_end_time = $end_time;
                while($tmp_start_time <= $tmp_end_time){
                    $xAxis[] = date('m/Y', $tmp_start_time);
                    $clicks[date('m/Y', $tmp_start_time)] = 0;
                    $orders[date('m/Y', $tmp_start_time)] = 0;
                    $tmp_start_time += 86400*30;  // TODO need fix day of month
                }

                $cond = array(
                    'month' => array('$gte' => (int)date('Ym', $start_time),'$lte' => (int)date('Ym', $end_time)),
                    'type' => array('$eq' => 'click'),
                    'campaign_id' => array('$eq' => (int)$campaign_id)
                );
                if($link_id > 0){
                    $cond['link_id'] = array('$eq' => (int)$link_id);
                }
                $click_data = Mava_Data::gets(Mava_Data::TABLE_STATS, $cond,0,0);
                if(is_array($click_data) && count($click_data) > 0){
                    foreach($click_data as $item){
                        $clicks[date('m/Y', $item['time'])] += $item['count'];
                        $total_click += $item['count'];
                    }
                }

                $cond = array(
                    'month' => array('$gte' => (int)date('Ym', $start_time),'$lte' => (int)date('Ym', $end_time)),
                    'type' => array('$eq' => 'order'),
                    'campaign_id' => array('$eq' => (int)$campaign_id)
                );
                if($link_id > 0){
                    $cond['link_id'] = array('$eq' => (int)$link_id);
                }
                $order_data = Mava_Data::gets(Mava_Data::TABLE_STATS, $cond,0,0);

                if(is_array($order_data) && count($order_data) > 0){
                    foreach($order_data as $item){
                        $orders[date('m/Y', $item['time'])] += $item['count'];
                        $total_order += $item['count'];
                    }
                }
            }

            $linkModel = $this->_getAdsCampaignLinksModel();
            $links = $linkModel->getAll($campaign_id);

            $cond = array(
                'campaign_id' => array('$eq' => (int)$campaign_id)
            );
            if($link_id > 0){
                $cond['link_id'] = array('$eq' => (int)$link_id);
            }
            $total_order = Mava_Data::count(Mava_Data::TABLE_ORDER, $cond);
            $page = max((int)Mava_Url::getParam('page'),1);
            $limit = 50;
            $skip = ($page-1)*$limit;
            $order_lists = Mava_Data::gets(Mava_Data::TABLE_ORDER, $cond, $skip, $limit, array('order_time' => -1));
            $viewParams = array(
                'order_lists' => $order_lists,
                'xAxis' => $xAxis,
                'clicks' => array_values($clicks),
                'total_click' => $total_click,
                'orders' => array_values($orders),
                'total_order' => $total_order,
                'start_date' => date('d/m/Y', $start_time),
                'end_date' => date('d/m/Y', $end_time),
                'breadcrumbs' => $breadcrumbs,
                'campaign' => $campaign,
                'link_id' => $link_id,
                'links' => $links,
                'page' => $page,
                'skip' => $skip,
                'limit' => $limit
            );
            return $this->responseView('Admin_View_Ads_ViewCampaign', $viewParams);
        }else{
            return $this->responseError(__('campaign_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    public function addCampaignLinkAction(){
        $url = Mava_Url::getParam('url');
        $note = Mava_Url::getParam('note');
        $campaign_id = (int)Mava_Url::getParam('campaign_id');
        $campaignModel = $this->_getAdsCampaignModel();
        if($campaign_id > 0 && $campaign = $campaignModel->getById($campaign_id)){
            if($url != "" && Mava_String::isUrl($url)){
                $campaignLinkDW = $this->_getAdsCampaignLinksDataWriter();
                $campaignLinkDW->bulkSet(array(
                    'campaign_id' => $campaign_id,
                    'note' => $note,
                    'url' => $url,
                    'url_hash' => md5(Mava_String::makeStringToLower($url)),
                    'click_count' => 0,
                    'order_count' => 0,
                    'total_revenue' => 0,
                    'created_by' => Mava_Visitor::getUserId(),
                    'created_time' => time(),
                    'deleted' => 'no'
                ));

                if($campaignLinkDW->save()){
                    return $this->responseJson(array(
                        'status' => 1,
                        'link' => array(
                            'id' => $campaignLinkDW->get('id'),
                            'note' => $note,
                            'url' => Mava_Url::addParam($url, array('_mcid' => $campaign_id)),
                            'click_count' => 0,
                            'order_count' => 0,
                            'total_revenue' => 0
                        ),
                        'message' => __('campaign_link_added')
                    ));
                }else{
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('can_not_add_campaign_link')
                    ));
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('campaign_link_url_invalid')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('campaign_not_found')
            ));
        }
    }

    public function getCampaignLinkInfoAction(){
        $link_id = Mava_Url::getParam('link_id');
        $linkModel = $this->_getAdsCampaignLinksModel();
        if($link_id > 0 && $link = $linkModel->getById($link_id)){
            return $this->responseJson(array(
                'status' => 1,
                'link' => array(
                    'id' => $link['id'],
                    'note' => htmlspecialchars($link['note']),
                    'url' => $link['url'],
                    'click_count' => $link['click_count'],
                    'order_count' => $link['order_count'],
                    'total_revenue' => $link['total_revenue']
                ),
                'message' => __('ok')
            ));
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('campaign_link_not_found')
            ));
        }
    }

    public function editCampaignLinkAction(){
        $link_id = Mava_Url::getParam('link_id');
        $note = Mava_Url::getParam('note');
        $linkModel = $this->_getAdsCampaignLinksModel();
        if($link_id > 0 && $link = $linkModel->getById($link_id)){
            $linkDW = $this->_getAdsCampaignLinksDataWriter();
            $linkDW->setExistingData($link_id);
            $linkDW->bulkSet(array(
                'note' => $note
            ));
            $linkDW->save();
            return $this->responseJson(array(
                'status' => 1,
                'link' => array(
                    'id' => $link['id'],
                    'note' => htmlspecialchars($note),
                    'url' => Mava_Url::addParam($link['url'], array('_mcid' => $link['campaign_id'])),
                    'click_count' => $link['click_count'],
                    'order_count' => $link['order_count'],
                    'total_revenue' => $link['total_revenue']
                ),
                'message' => __('campaign_link_updated')
            ));
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('campaign_link_not_found')
            ));
        }
    }

    public function deleteCampaignLinkAction(){
        $link_id = Mava_Url::getParam('link_id');
        $linkModel = $this->_getAdsCampaignLinksModel();
        if($link_id > 0 && $link = $linkModel->getById($link_id)){
            $linkDW = $this->_getAdsCampaignLinksDataWriter();
            $linkDW->setExistingData($link_id);
            $linkDW->bulkSet(array(
                'deleted' => 'yes'
            ));
            $linkDW->save();
            return $this->responseJson(array(
                'status' => 1,
                'message' => __('campaign_link_deleted')
            ));
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('campaign_link_not_found')
            ));
        }
    }

    /**
     * @return Megabook_DataWriter_AdsCampaignLinks
     * @throws Mava_Exception
     */
    protected function _getAdsCampaignLinksDataWriter()
    {
        return Mava_DataWriter::create('Megabook_DataWriter_AdsCampaignLinks');
    }

    /**
     * @return Megabook_Model_AdsCampaignLinks
     */
    protected function _getAdsCampaignLinksModel(){
        return $this->getModelFromCache('Megabook_Model_AdsCampaignLinks');
    }

    /**
     * @return Megabook_Model_AdsCampaign
     */
    protected function _getAdsCampaignModel(){
        return $this->getModelFromCache('Megabook_Model_AdsCampaign');
    }

    /**
     * @return Megabook_Model_AdsCampaignGroup
     */
    protected function _getAdsCampaignGroupModel(){
        return $this->getModelFromCache('Megabook_Model_AdsCampaignGroup');
    }

    /**
     * @return Megabook_DataWriter_AdsCampaign
     * @throws Mava_Exception
     */
    protected function _getAdsCampaignDataWriter()
    {
        return Mava_DataWriter::create('Megabook_DataWriter_AdsCampaign');
    }

    /**
     * @return Megabook_DataWriter_AdsCampaignGroup
     * @throws Mava_Exception
     */
    protected function _getAdsCampaignGroupDataWriter()
    {
        return Mava_DataWriter::create('Megabook_DataWriter_AdsCampaignGroup');
    }
}