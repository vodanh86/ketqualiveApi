<?php

class Loto_Controller_Stats extends Mava_Controller {
    public function indexAction(){
        return $this->responseRedirect('/');
    }
    
    public function tongHopChuKyDanDacBietAction(){
        Mava_Application::set('seo/title', 'Thống kê chu kỳ dàn đặc biệt');
        $nums = Mava_Url::getParam('nums');
        $start_time = Mava_Url::getParam('start_time');
        $end_time = Mava_Url::getParam('end_time');
        $result_html = '';
        if($nums != ""){
            $nums = explode(',',preg_replace('/(,|\-)+/',',',str_replace(' ',',',$nums)));
            Mava_Application::set('seo/title', 'Thống kê chu kỳ dàn đặc biệt bộ số '. implode('-', $nums) .' từ ngày '. $start_time .' đến ngày '. $end_time);
            for($i=0;$i<count($nums);$i++){
                $nums[$i] = sprintf('%02d', substr($nums[$i],-2,2));
            }
            //TODO
            //$result_html = get_thongkenhanh_result_html(array_unique($nums), $province['code'], $start_time, $end_time);
            $result_html = '';
        }
        return $this->responseView('Loto_View_Stats_ThongKeChuKyDanDacBiet',array(
            'result_html' => $result_html,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'nums' => $nums
        ));    
    }
    
    public function tongHopChuKyDacBietAction(){
        Mava_Application::set('seo/title', 'Tổng hợp chu kỳ đặc biệt');
        $start_time = Mava_Url::getParam('start_time');
        $province_slug = 'ket-qua-xo-so-'. Mava_Url::getParam('province_slug');
        $provinceModel = $this->_getProvinceModel();
        $province = $provinceModel->getBySlug($province_slug);
        if(!$province){
            $province = $provinceModel->getById(1);
        }
        $result_html = '';
        if($start_time != ""){
            //TODO
            //$result_html = get_thongkenhanh_result_html(array_unique($nums), $province['code'], $start_time, $end_time);
            $result_html = '';
        }
        return $this->responseView('Loto_View_Stats_TongHopChuKyDacBiet',array(
            'start_time' => $start_time,
            'result_html' => $result_html,
            'province_id' => $province['id']
        ));    
    }
    
    public function thongKeNhanhAction(){
        Mava_Application::set('seo/title', 'Thống kê nhanh');
        $nums = Mava_Url::getParam('nums');
        $start_time = Mava_Url::getParam('start_time');
        $end_time = Mava_Url::getParam('end_time');
        $province_slug = 'ket-qua-xo-so-'. Mava_Url::getParam('province_slug');
        $provinceModel = $this->_getProvinceModel();
        $province = $provinceModel->getBySlug($province_slug);
        if(!$province){
            $province = $provinceModel->getById(1);
        }
        $result_html = '';
        if($nums != ""){
            $nums = explode(',',preg_replace('/(,|\-)+/',',',str_replace(' ',',',$nums)));
            Mava_Application::set('seo/title', 'Thống kê nhanh xổ số '. $province['title'] .' bộ số '. implode('-', $nums) .' từ ngày '. $start_time .' đến ngày '. $end_time);
            for($i=0;$i<count($nums);$i++){
                $nums[$i] = sprintf('%02d', substr($nums[$i],-2,2));
            }
            $result_html = get_thongkenhanh_result_html(array_unique($nums), $province['code'], $start_time, $end_time);
        }
        return $this->responseView('Loto_View_Stats_ThongKeNhanh',array(
            'result_html' => $result_html,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'province_id' => $province['id'],
            'nums' => $nums
        ));    
    }
    
    public function thongKeChuKyGanTheoTinhAction(){
        Mava_Application::set('seo/title', 'Thống kê chu kỳ gan theo tỉnh');
        $nums = Mava_Url::getParam('nums');
        $province_slug = 'ket-qua-xo-so-'. Mava_Url::getParam('province_slug');
        $provinceModel = $this->_getProvinceModel();
        $province = $provinceModel->getBySlug($province_slug);
        if(!$province){
            $province = $provinceModel->getById(1);
        }
        $only_special = (int)Mava_Url::getParam('type') != 1 ? 0 : 1;
        $result_html = '';
        if($nums != ""){
            $nums = explode(',',preg_replace('/(,|\-)+/',',',str_replace(' ',',',$nums)));
            Mava_Application::set('seo/title', 'Thống kê chu kỳ gan '. ($province['id']==1?'xổ số Truyền thống':$province['title']) .' bộ số '. implode('-', $nums));
            for($i=0;$i<count($nums);$i++){
                $nums[$i] = sprintf('%02d', substr($nums[$i],-2,2));
            }
            $result_html = get_thongkechukygantheotinh_result_html(array_unique($nums), $province['code'], $only_special);
        }
        return $this->responseView('Loto_View_Stats_ThongKeChuKyGanTheoTinh',array(
            'result_html' => $result_html,
            'province_id' => $province['id'],
            'only_special' => $only_special,
            'nums' => $nums
        ));    
    }
    
    public function thongKeLoGanAction(){
        $visitor = Mava_Visitor::getInstance();
        Mava_Application::set('seo/title', 'Thống kê lô gan');
        $province_slug = 'ket-qua-xo-so-'. Mava_Url::getParam('province_slug');
        $provinceModel = $this->_getProvinceModel();
        $province = $provinceModel->getBySlug($province_slug);
        if(!$province){
            $province = $provinceModel->getById(1);
        }
        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
        
        $messageError = '';
        $result_html = '';
        if(Mava_Url::getParam('province_slug') != '' && $token != '' && $province['code'] != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'region_code' => $province['code']
            ];
            $result = Mava_API::call('loto/hardy', $data);
            if($result['error'] == 0) {
                Mava_Application::set('seo/title', 'Thống kê lô gan '. ($province['id']==1?'truyền thống':$province['title']));
                $result_html = get_thongkelogan_result_html($result['data']);
            }else {
                $messageError = $result['message'];
            }
            
        }
        return $this->responseView('Loto_View_Stats_ThongKeLoGan',array(
            'result_html' => $result_html,
            'province_id' => $province['id'],
            'messageError' => $messageError
            ));
    }
    
    public function thongKeChuKyAction(){
        Mava_Application::set('seo/title', 'Thống kê chu kỳ');
        $nums = Mava_Url::getParam('nums');
        $result_html = '';
        if($nums != ""){
            $nums = explode(',',preg_replace('/(,|\-)+/',',',str_replace(' ',',',$nums)));
            Mava_Application::set('seo/title', 'Thống kê chu kỳ bộ số '. implode(' ', $nums));
            for($i=0;$i<count($nums);$i++){
                $nums[$i] = sprintf('%02d', substr($nums[$i],-2,2));
            }
            $result_html = get_thongkechuky_result_html(array_unique($nums));
        }
        return $this->responseView('Loto_View_Stats_ThongKeChuKy',array(
            'result_html' => $result_html,
            'nums' => $nums
            ));    
    }
    
    public function thongKeChuKyDanLoToAction(){
        Mava_Application::set('seo/title', 'Thống kê chu kỳ dàn Lô tô');
        $nums = Mava_Url::getParam('nums');
        $pair = (int)Mava_Url::getParam('pair');
        $start_time = Mava_Url::getParam('start_time');
        $end_time = Mava_Url::getParam('end_time');
        $result_html = '';
        if($nums != ""){
            $nums = explode(',',preg_replace('/(,|\-)+/',',',str_replace(' ',',',$nums)));
            Mava_Application::set('seo/title', 'Thống kê chu kỳ dàn Lô tô '. implode('-', $nums));
            for($i=0;$i<count($nums);$i++){
                $nums[$i] = sprintf('%02d', substr($nums[$i],-2,2));
            }
            $result_html = get_thongkechukydanlo_result_html(array_unique($nums), $start_time, $end_time, $pair);
        }
        return $this->responseView('Loto_View_Stats_ThongKeChuKyDanLo',array(
            'result_html' => $result_html,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'pair' => $pair,
            'nums' => $nums
            ));    
    }

    public function thongKeTanSuatLoAction(){
        $visitor = Mava_Visitor::getInstance();
        Mava_Application::set('seo/title', 'Thống kê tần suất lô');
        $province_slug = 'ket-qua-xo-so-'. Mava_Url::getParam('province_slug');
        $num = Mava_Url::getParam('num');
        $volumn = (int)Mava_Url::getParam('volumn');
        $provinceModel = $this->_getProvinceModel();
        $province = $provinceModel->getBySlug($province_slug);
        if(!$province){
            $province = $provinceModel->getById(1);
        }
        $typeInt = 1;
        $typeTxt = 'tất cả';
        if($num == 'dau'){
            $typeInt = 2;
            $typeTxt = 'đầu';
        }
        if($num == 'duoi') {
            $typeInt = 3;
            $typeTxt = 'đuôi';
        }
        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
        
        $messageError = '';
        $result_html = '';
        if(Mava_Url::getParam('province_slug') != '' && $token != '' && $province['code'] != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'region_code' => $province['code'],
                'type' => $typeInt,
                'limit' => $volumn,
            ];
            $result = Mava_API::call('loto/frequency', $data);
            if($result['error'] == 0) {
                Mava_Application::set('seo/title', 'Thống kê tần suất lô '. ($province['id']==1?'truyền thống':$province['title']). ' bộ số ' . $typeTxt . ' với biên độ ' . $volumn . ' ngày');
                $result_html = get_thongketansuatlo_result_html($result['data']);
            }else {
                $messageError = $result['message'];
            }
            
        }
        return $this->responseView('Loto_View_Stats_ThongKeTanSuatLo',array(
            'result_html' => $result_html,
            'province_id' => $province['id'],
            'typeInt' => $typeInt,
            'volumn' => $volumn,
            'messageError' => $messageError
            ));
    }

    public function thongKeTheoTongAction(){
        $visitor = Mava_Visitor::getInstance();
        Mava_Application::set('seo/title', 'Thống kê theo tổng');
        $province_slug = 'ket-qua-xo-so-'. Mava_Url::getParam('province_slug');
        $start_time = Mava_Url::getParam('start_time');
        $end_time = Mava_Url::getParam('end_time');
        $sum = (int)Mava_Url::getParam('sum');
        $provinceModel = $this->_getProvinceModel();
        $province = $provinceModel->getBySlug($province_slug);
        if(!$province){
            $province = $provinceModel->getById(1);
        }

        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');

        $messageError = '';
        $result_html = '';
        if(Mava_Url::getParam('province_slug') != '' && $token != '' && $province['code'] != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'region_code' => $province['code'],
                'start_date' => str_replace('-', '/', $start_time),
                'end_date' => str_replace('-', '/', $end_time),
                'sum' => $sum,
            ];
            $result = Mava_API::call('loto/total', $data);
            if($result['error'] == 0) {
                Mava_Application::set('seo/title', 'Thống kê theo tổng '.($province['id']==1?'truyền thống':$province['title']));
                $result_html = get_thongketheotong_result_html($result['data']);
            }else {
                $messageError = $result['message'];
            }

        }
        return $this->responseView('Loto_View_Stats_ThongKeTheoTong',array(
            'result_html' => $result_html,
            'province_id' => $province['id'],
            'start_time' => $start_time,
            'end_time' => $end_time,
            'sum' => $sum,
            'messageError' => $messageError
            ));
    }

    public function thongKeLoVeNhieuVeItAction(){
        $visitor = Mava_Visitor::getInstance();
        Mava_Application::set('seo/title', 'Thống kê lô về nhiều - về ít');
        $type = Mava_Url::getParam('type');
        $province_slug = 'ket-qua-xo-so-'. Mava_Url::getParam('province_slug');
        $volumn = (int)Mava_Url::getParam('volumn');
        $provinceModel = $this->_getProvinceModel();
        $province = $provinceModel->getBySlug($province_slug);
        if(!$province){
            $province = $provinceModel->getById(1);
        }
        $typeInt = 1;
        $typeTxt = 'về ít';

        if($type == 've-nhieu'){
            $typeInt = 2;
            $typeTxt = 'về nhiều';
        }
        if($type == 'chua-ve') {
            $typeInt = 3;
            $typeTxt = 'chưa về';
        }
        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');

        $messageError = '';
        $result_html = '';
        if(Mava_Url::getParam('province_slug') != '' && $token != '' && $province['code'] != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'region_code' => $province['code'],
                'limit' => $volumn,
                'type' => $typeInt
            ];
            $result = Mava_API::call('loto/times', $data);
            if($result['error'] == 0) {
                Mava_Application::set('seo/title', 'Thống kê lô ' . ($province['id']==1?'truyền thống':$province['title']) . ' ' . $typeTxt . ' với biên độ ' . $volumn . ' ngày');
                $result_html = get_thongkelovenhieuveit_result_html($result['data'], $typeInt);
            }else {
                $messageError = $result['message'];
            }
        }
        return $this->responseView('Loto_View_Stats_ThongKeLoVeNhieuVeIt',array(
            'result_html' => $result_html,
            'province_id' => $province['id'],
            'typeInt' => $typeInt,
            'volumn' => $volumn,
            'messageError' => $messageError
            ));
    }

    public function thongKeLoRoiAction(){
        $visitor = Mava_Visitor::getInstance();
        Mava_Application::set('seo/title', 'Thống kê lô rơi');
        $province_slug = 'ket-qua-xo-so-'. Mava_Url::getParam('province_slug');
        $provinceModel = $this->_getProvinceModel();
        $province = $provinceModel->getBySlug($province_slug);
        if(!$province){
            $province = $provinceModel->getById(1);
        }

        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');

        $messageError = '';
        $result_html = '';
        if(Mava_Url::getParam('province_slug') != '' && $token != '' && $province['code'] != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'region_code' => $province['code']
            ];
            $result = Mava_API::call('loto/fallen', $data);
            if($result['error'] == 0) {
                Mava_Application::set('seo/title', 'Thống kê lô rơi ' . ($province['id']==1?'truyền thống':$province['title']));
                $result_html = get_thongkeloroi_result_html($result['data']);
            }else {
                $messageError = $result['message'];
            }
        }
        return $this->responseView('Loto_View_Stats_ThongKeLoRoi',array(
            'result_html' => $result_html,
            'province_id' => $province['id'],
            'messageError' => $messageError
            ));
    }

    public function thongKeGiaiDacBietAction(){
        $visitor = Mava_Visitor::getInstance();
        Mava_Application::set('seo/title', 'Thống kê giải đặc biệt');
        $province_slug = 'ket-qua-xo-so-'. Mava_Url::getParam('province_slug');
        $provinceModel = $this->_getProvinceModel();
        $province = $provinceModel->getBySlug($province_slug);
        if(!$province){
            $province = $provinceModel->getById(1);
        }

        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');

        $messageError = '';
        $result_html = [
            'result_sum_html' => '',
            'result_equal_html' => '',
            'result_all_html' => '',
            'result_recently_html' =>''
        ];
        if(Mava_Url::getParam('province_slug') != '' && $token != '' && $province['code'] != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'region_code' => $province['code']
            ];
            $result = Mava_API::call('loto/special', $data);
            if($result['error'] == 0) {
                Mava_Application::set('seo/title', 'Thống kê giải đặc biệt ' . ($province['id']==1?'truyền thống':$province['title']));
                $result_html = get_thongkegiaidacbiet_result_html($result['data']);
            }else {
                $messageError = $result['message'];
            }
        }
        return $this->responseView('Loto_View_Stats_ThongKeGiaiDacBiet',array(
            'result_html' => $result_html,
            'province_id' => $province['id'],
            'messageError' => $messageError
        ));
    }

    public function soiCauTheoSoAction(){
        Mava_Application::set('seo/title', 'Soi cầu theo sô');
        $number = Mava_Url::getParam('number');

        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');

        $messageError = '';
        $result_html = '';
        if(Mava_Url::getParam('number') != '' && $token != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'number' => (int)$number
            ];
            $result = Mava_API::call('loto/pray-one', $data);
            if($result['error'] == 0) {
                Mava_Application::set('seo/title', 'Soi cầu theo số ' . $number);
                $result_html = get_soicautheoso_result_html($number, $result['data']);
            }else {
                $messageError = $result['message'];
            }
        }
        return $this->responseView('Loto_View_Stats_SoiCauTheoSo',array(
            'result_html' => $result_html,
            'number' => $number,
            'messageError' => $messageError
        ));
    }

    public function chiTietCauLotoTheoSoAction(){
        Mava_Application::set('seo/title', 'Chi tiết cầu lô tô');
        $number = Mava_Url::getParam('number');
        $position_1 = Mava_Url::getParam('dau');
        $position_2 = Mava_Url::getParam('cuoi');
        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');

        $messageError = '';
        $result_html = '';
        if($number != '' && $token != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'number' => (int)$number
            ];
            $result = Mava_API::call('loto/pray-one', $data);
            if($result['error'] == 0) {
                Mava_Application::set('seo/title', 'Chi tiết cầu lô tô số ' . $number);
                $result_html = get_chitietsoicautheoso_result_html($number, $position_1, $position_2, $result['data']);
            }else {
                $messageError = $result['message'];
            }
        }
        $latest = $this->_getResultModel()->getLatestResult('tt');
        $date = date('d-m-Y', time());
        if($latest){
            // ngày tiếp theo
            $date = date('d-m-Y', $latest['result_date'] + 86400);
        }
        return $this->responseView('Loto_View_Stats_ChiTietCauLotoTheoSo',array(
            'result_html' => $result_html,
            'date' => $date,
            'number' => $number,
            'position_1' => $position_1,
            'position_2' => $position_2,
            'messageError' => $messageError
        ));
    }

    public function soiCauTheoTinhAction(){
        Mava_Application::set('seo/title', 'Soi cầu theo tỉnh');
        $province_slug = 'ket-qua-xo-so-'. Mava_Url::getParam('province_slug');
        $provinceModel = $this->_getProvinceModel();
        $province = $provinceModel->getBySlug($province_slug);
        if(!$province){
            $province = $provinceModel->getById(1);
        }
        $latest = $this->_getResultModel()->getLatestResult();
        $date = Mava_Url::getParam('date') ? Mava_Url::getParam('date') : date('d-m-Y', $latest['result_date'] + 86400);
        $day = date('w',date_to_time($date, '-')) + 1;
        $current_loto = Mava_Application::getConfig('loto_schedule/T'. $day);

        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
        $messageError = '';
        $result_html = '';
        if(Mava_Url::getParam('province_slug') && $date != '' && $token != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'region_code' => $province['code'],
                'date' => str_replace('-', '/', $date),
            ];
            $result = Mava_API::call('loto/pray-all', $data);
            if($result['error'] == 0) {
                Mava_Application::set('seo/title', 'Soi cầu theo tỉnh ' . ($province['id']==1?'truyền thống':$province['title']));
                $result_html = get_soicautheotinh_result_html(Mava_Url::getParam('province_slug'), $date, $result['data']);
            }else {
                $messageError = $result['message'];
            }
        }
        return $this->responseView('Loto_View_Stats_SoiCauTheoTinh',array(
            'result_html' => $result_html,
            'province_slug' => Mava_Url::getParam('province_slug'),
            'date' => $date,
            'current_loto' => $current_loto,
            'messageError' => $messageError
        ));
    }

    public function chiTietCauLotoTheoTinhAction(){
        Mava_Application::set('seo/title', 'Chi tiết cầu lô tô');
        $province_slug = 'ket-qua-xo-so-'. Mava_Url::getParam('province_slug');
        $provinceModel = $this->_getProvinceModel();
        $province = $provinceModel->getBySlug($province_slug);
        if(!$province){
            $province = $provinceModel->getById(1);
        }
        $date = Mava_Url::getParam('date') ? Mava_Url::getParam('date') : date('d-m-Y', time());

        $number = Mava_Url::getParam('number');
        $position_1 = Mava_Url::getParam('dau');
        $position_2 = Mava_Url::getParam('cuoi');
        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');

        $messageError = '';
        $result_html = '';
        if($number != '' && $token != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'region_code' => $province['code'],
                'date' => str_replace('-', '/', $date),
            ];
            $result = Mava_API::call('loto/pray-all', $data);
            if($result['error'] == 0) {
                $result_html = get_chitietsoicautheotinh_result_html($number, $position_1, $position_2, $result['data'], $province);
            }else {
                $messageError = $result['message'];
            }
        }
        return $this->responseView('Loto_View_Stats_ChiTietCauLotoTheoTinh',array(
            'result_html' => $result_html,
            'date' => $date,
            'number' => $number,
            'position_1' => $position_1,
            'position_2' => $position_2,
            'province' => $province,
            'messageError' => $messageError
        ));
    }
    
    public function soiCauBachThuAction(){
        Mava_Application::set('seo/title', 'Soi cầu truyền thống bạch thủ');
        $date = Mava_Url::getParam('date');

        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
        $messageError = '';
        $result_html = '';
        if($date != '' && $token != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'date' => str_replace('-', '/', $date),
            ];
            $result = Mava_API::call('loto/pray-one-way', $data);
            if($result['error'] == 0) {
                Mava_Application::set('seo/title', 'Soi cầu truyền thống bạch thủ ngày ' . $date);
                $result_html = get_soicaubachthu_result_html($date, $result['data']);
            }else {
                $messageError = $result['message'];
            }
        }
        $latest = $this->_getResultModel()->getLatestResult();
        return $this->responseView('Loto_View_Stats_SoiCauBachThu',array(
            'result_html' => $result_html,
            'date' => $date,
            'latest_date' => date('d-m-Y', $latest['result_date'] + 86400),
            'messageError' => $messageError
        ));
    }

    public function chiTietCauLotoBachThuAction(){
        Mava_Application::set('seo/title', 'Chi tiết cầu lô tô');
        $date = Mava_Url::getParam('date') ? Mava_Url::getParam('date') : date('d-m-Y', time());
        $number = Mava_Url::getParam('number');
        $position_1 = Mava_Url::getParam('dau');
        $position_2 = Mava_Url::getParam('cuoi');
        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
        $messageError = '';
        $result_html = '';
        if($number != '' && $token != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'date' => str_replace('-', '/', $date),
            ];
            $result = Mava_API::call('loto/pray-one-way', $data);
            if($result['error'] == 0) {
                Mava_Application::set('seo/title', 'Chi tiết cầu lô tô số ' . $number);
                $result_html = get_chitietsoicaubachthu_result_html($number, $position_1, $position_2, $result['data']);
            }else {
                $messageError = $result['message'];
            }
        }
        return $this->responseView('Loto_View_Stats_ChiTietCauLotoBachThu',array(
            'result_html' => $result_html,
            'date' => $date,
            'number' => $number,
            'position_1' => $position_1,
            'position_2' => $position_2,
            'messageError' => $messageError
        ));
    }

    public function soiCauVeHaiNhayAction(){
        Mava_Application::set('seo/title', 'Soi cầu truyền thống về hai nháy');
        $date = Mava_Url::getParam('date');

        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
        $messageError = '';
        $result_html = '';
        if($date != '' && $token != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'date' => str_replace('-', '/', $date),
            ];
            $result = Mava_API::call('loto/pray-double', $data);
            if($result['error'] == 0) {
                Mava_Application::set('seo/title', 'Soi cầu truyền thống về hai nháy ngày ' . $date);
                $result_html = get_soicauvehainhay_result_html($date, $result['data']);
            }else {
                $messageError = $result['message'];
            }
        }
        $latest = $this->_getResultModel()->getLatestResult();
        return $this->responseView('Loto_View_Stats_SoiCauVeHaiNhay',array(
            'result_html' => $result_html,
            'date' => $date,
            'latest_date' => date('d-m-Y', $latest['result_date'] + 86400),
            'messageError' => $messageError
        ));
    }

    public function chiTietCauLotoVeHaiNhayAction(){
        Mava_Application::set('seo/title', 'Chi tiết cầu lô tô');
        $date = Mava_Url::getParam('date') ? Mava_Url::getParam('date') : date('d-m-Y', time());
        $number = Mava_Url::getParam('number');
        $position_1 = Mava_Url::getParam('dau');
        $position_2 = Mava_Url::getParam('cuoi');
        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
        $messageError = '';
        $result_html = '';
        if($number != '' && $token != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'date' => str_replace('-', '/', $date),
            ];
            $result = Mava_API::call('loto/pray-double', $data);
            if($result['error'] == 0) {
                Mava_Application::set('seo/title', 'Chi tiết cầu lô tô số ' . $number);
                $result_html = get_chitietsoicauvehainhay_result_html($number, $position_1, $position_2, $result['data']);
            }else {
                $messageError = $result['message'];
            }
        }
        return $this->responseView('Loto_View_Stats_ChiTietCauLotoVeHaiNhay',array(
            'result_html' => $result_html,
            'date' => $date,
            'number' => $number,
            'position_1' => $position_1,
            'position_2' => $position_2,
            'messageError' => $messageError
        ));
    }

    public function soiCauDacBietAction(){
        Mava_Application::set('seo/title', 'Soi cầu truyền thống đặc biệt');
        $date = Mava_Url::getParam('date');

        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
        $messageError = '';
        $result_html = '';
        if($date != '' && $token != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'date' => str_replace('-', '/', $date),
            ];
            $result = Mava_API::call('loto/pray-special', $data);
            if($result['error'] == 0) {
                Mava_Application::set('seo/title', 'Soi cầu truyền thống đặc biệt ngày ' . $date);
                $result_html = get_soicaudacbiet_result_html($date, $result['data']);
            }else {
                $messageError = $result['message'];
            }
        }
        $latest = $this->_getResultModel()->getLatestResult();
        return $this->responseView('Loto_View_Stats_SoiCauDacBiet',array(
            'result_html' => $result_html,
            'date' => $date,
            'latest_date' => date('d-m-Y', $latest['result_date'] + 86400),
            'messageError' => $messageError
        ));
    }

    public function chiTietCauDacBietAction(){
        Mava_Application::set('seo/title', 'Chi tiết cầu đặc biệt');
        $date = Mava_Url::getParam('date') ? Mava_Url::getParam('date') : date('d-m-Y', time());
        $number = Mava_Url::getParam('number');
        $position_1 = Mava_Url::getParam('dau');
        $position_2 = Mava_Url::getParam('cuoi');
        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
        $messageError = '';
        $result_html = '';
        if($number != '' && $token != ''){
            // call API to get result
            $data = [
                'token' => $token,
                'date' => str_replace('-', '/', $date),
            ];
            $result = Mava_API::call('loto/pray-special', $data);
            if($result['error'] == 0) {
                Mava_Application::set('seo/title', 'Chi tiết cầu đặc biệt số ' . $number);
                $result_html = get_chitietsoicaudacbiet_result_html($number, $position_1, $position_2, $result['data']);
            }else {
                $messageError = $result['message'];
            }
        }
        return $this->responseView('Loto_View_Stats_ChiTietCauDacbiet',array(
            'result_html' => $result_html,
            'date' => $date,
            'number' => $number,
            'position_1' => $position_1,
            'position_2' => $position_2,
            'messageError' => $messageError
        ));
    }

    protected function _getProvinceModel(){
        return $this->getModelFromCache('Loto_Model_Province');
    }

    protected function _getAPILotoModel(){
        return $this->getModelFromCache('API_Model_Loto');
    }

    /**
     * @return Loto_Model_Result
     */
    protected function _getResultModel(){
        return $this->getModelFromCache('Loto_Model_Result');
    }
}