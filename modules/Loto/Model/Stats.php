<?php

class Loto_Model_Stats extends Mava_Model {
    public function getThongKeNhanhResultHtml($nums, $province = 'tt', $start_time = 0, $end_time = 0){
        if($start_time == 0){
            $start_time = date('d-m-Y', time() - 86400*30);
        }
        if($end_time == 0){
            $end_time = date('d-m-y');
        }
        $visibleCount = [];
        $lastVisible = [];
        foreach($nums as $n){
            $visibleCount[$n] = [
                'count' => 0,
                'number' => $n
            ];
            $lastVisible[$n] = [
                'count' => 0,
                'last_visible' => 0
            ];
        }
        $resultModel = $this->_getResultModel();
        $loto = $resultModel->getList($start_time, $end_time, $province);
        if(is_array($loto) && count($loto)){
            foreach($loto as $item){
                $numbers = explode('-',preg_replace('/(\-)+/','-',$item['g0'] .'-'. $item['g1'] .'-'. $item['g2'] .'-'. $item['g3'] .'-'. $item['g4'] .'-'. $item['g5'] .'-'. $item['g6'] .'-'. $item['g7']));
                array_walk($numbers, function(&$num){
                    $num = substr($num, -2, 2);
                });
                $numbers = array_unique($numbers);
                foreach($nums as $n){
                    if(in_array($n, $numbers)){
                        // nếu lô ra cập nhật lại số ngày ra và lần cuối ra
                        $visibleCount[$n]['count']++;
                        
                        $lastVisible[$n]['count'] = 0;
                        $lastVisible[$n]['last_visible'] = $item['result_date'];
                    }else{
                        // tăng số lần không ra
                        $lastVisible[$n]['count']++;
                    }
                }
            }
            
            /*usort($visibleCount, function($a, $b){
                return $a["count"] < $b["count"];
            });*/
            $html = '<table class="table">
                <tr><th>Cặp số</th><th>Ngày về gần nhất</th><th>Số lần về</th><th class="text-right">Số ngày chưa về</th></tr>
            ';
            foreach($visibleCount as $key => $item){
                $html .= '<tr><td class="red bold">'. $item['number'] .'</td><td>'. ($lastVisible[$item['number']]['last_visible'] > 0 ? date('d-m-Y', $lastVisible[$item['number']]['last_visible']):'--') .'</td><td>'. $item['count'] .'</td><td class="red bold" align="right">'. $lastVisible[$item['number']]['count'] .'</td></tr>';
            }
            $html .= '</table>';
            return $html;
        }else{
            return '';
        }
    }
    
//    public function getLoGanResultHtml($nums, $start_time, $end_time, $volumn = 10, $province = 'tt'){
//        $resultModel = $this->_getResultModel();
//        $loto = $resultModel->getList($start_time, $end_time, $province);
//        $maxGan = [];
//        $currentGan = [];
//        foreach($nums as $n){
//            $maxGan[$n] = [
//                'count' => 0,
//                'start_time' => 0,
//                'end_time' => 0
//                ];
//            $currentGan[$n] = [
//                'count' => 0,
//                'start_time' => date_to_time($start_time, '-')
//                ];
//        }
//        if(is_array($loto) && count($loto)){
//            foreach($loto as $item){
//                $numbers = explode('-',preg_replace('/(\-)+/','-',$item['g0'] .'-'. $item['g1'] .'-'. $item['g2'] .'-'. $item['g3'] .'-'. $item['g4'] .'-'. $item['g5'] .'-'. $item['g6'] .'-'. $item['g7']));
//                array_walk($numbers, function(&$num){
//                    $num = substr($num, -2, 2);
//                });
//                $numbers = array_unique($numbers);
//                foreach($nums as $n){
//                    if(in_array($n, $numbers)){
//                        // nếu lô ra cập nhật lại max gan
//                        if($currentGan[$n]['count'] > $maxGan[$n]['count']){
//                            $maxGan[$n]['count'] = $currentGan[$n]['count'];
//                            $maxGan[$n]['start_time'] = $currentGan[$n]['start_time'];
//                            $maxGan[$n]['end_time'] = $item['result_date'];
//                        }
//                        // đưa current gan về khởi điểm
//                        $currentGan[$n]['count'] = 0;
//                        $currentGan[$n]['start_time'] = $item['result_date'];
//
//                    }else{
//                        // tăng số lần không ra của các số khác
//                        $currentGan[$n]['count']++;
//                    }
//                }
//            }
//            foreach($nums as $n){
//                if($currentGan[$n]['count'] > $maxGan[$n]['count']){
//                    $maxGan[$n]['count'] = $currentGan[$n]['count'];
//                    $maxGan[$n]['start_time'] = $currentGan[$n]['start_time'];
//                    $maxGan[$n]['end_time'] = date_to_time($end_time, '-') + 86400;
//                }
//            }
//            array_walk($maxGan, function(&$item, $key){
//                $item['number'] = $key;
//                $item['start_time'] = date('d-m-Y', $item['start_time'] + 86400);
//                $item['end_time'] = date('d-m-Y', $item['end_time'] - 86400);
//            });
//
//            usort($maxGan, function($a, $b){
//                return $a["count"] < $b["count"];
//            });
//            $html = '<table class="table">
//                <tr><th>Cặp số</th><th>Không ra từ ngày</th><th>Đến ngày</th><th class="text-right">Số ngày chưa ra (Độ gan)</th></tr>
//            ';
//            foreach($maxGan as $key => $item){
//                if($item['count'] >= $volumn){
//                    $html .= '<tr><td class="red bold">'. $item['number'] .'</td><td>'. $item['start_time'] .'</td><td>'. $item['end_time'] .'</td><td class="red bold" align="right">'. $item['count'] .'</td></tr>';
//                }
//            }
//            $html .= '</table>';
//            return $html;
//        }else{
//            return '';
//        }
//    }

    public function getLoGanResultHtml($data){
        if(count($data) > 0 ){
            $html = '<table class="table">
                <tr><th>Cặp số</th><th>Ngày ra gần nhất</th><th class="text-right">Số ngày chưa ra (Độ gan)</th></tr>
            ';
            foreach($data as $item){
                $html .= '<tr><td class="red bold">'. $item['number'] .'</td><td>'. $item['end_time'] .'</td><td class="red bold" align="right">'. $item['count'] .'</td></tr>';
            }
            $html .= '</table>';
            return $html;
        }
        return '';
    }
    public function getChuKyResultHtml($nums){
        $resultModel = $this->_getResultModel();
        $loto = $resultModel->getList('1-1-2003', date('d-m-Y'), 'tt');
        $max = [];
        $currentMax = [];
        foreach($nums as $n){
            $max[$n] = [
                'count' => 0,
                'start_time' => 0,
                'end_time' => 0
                ];
            $currentMax[$n] = [
                'count' => 0,
                'start_time' => date_to_time(0, '-')
                ];
        }
        if(is_array($loto) && count($loto)){
            $last_time = 0;
            foreach($loto as $item){
                $numbers = explode('-',preg_replace('/(\-)+/','-',$item['g0'] .'-'. $item['g1'] .'-'. $item['g2'] .'-'. $item['g3'] .'-'. $item['g4'] .'-'. $item['g5'] .'-'. $item['g6'] .'-'. $item['g7']));
                array_walk($numbers, function(&$num){
                    $num = substr($num, -2, 2);
                });
                $numbers = array_unique($numbers);
                foreach($nums as $n){
                    if(in_array($n, $numbers)){
                        // nếu lô ra cập nhật lại max
                        if($currentMax[$n]['count'] > $max[$n]['count']){
                            $max[$n]['count'] = $currentMax[$n]['count'];
                            $max[$n]['start_time'] = $currentMax[$n]['start_time'];
                            $max[$n]['end_time'] = $item['result_date'];
                        }
                        // đưa current gan về khởi điểm    
                        $currentMax[$n]['count'] = 0;
                        $currentMax[$n]['start_time'] = $item['result_date'];
                        
                    }else{
                        // tăng số lần không ra của các số khác
                        $currentMax[$n]['count']++;
                    }
                }
                $last_time = $item['result_date'];
            }
            foreach($nums as $n){
                if($currentMax[$n]['count'] > $max[$n]['count']){
                    $max[$n]['count'] = $currentMax[$n]['count'];
                    $max[$n]['start_time'] = $currentMax[$n]['start_time'];
                    $max[$n]['end_time'] = date_to_time($last_time, '-') + 86400;
                }  
            }
            array_walk($max, function(&$item, $key){
                $item['number'] = $key;
                $item['start_time'] = date('d-m-Y', $item['start_time'] + 86400);
                $item['end_time'] = date('d-m-Y', $item['end_time'] - 86400);
            });

            usort($max, function($a, $b){
                return $a["count"] < $b["count"];
            });
            $html = '<table class="table">
                <tr><th>Cặp số</th><th>Không ra từ ngày</th><th>Đến ngày</th><th class="text-right">Số ngày chưa ra (độ gan)</th></tr>
            ';
            foreach($max as $key => $item){
                $html .= '<tr><td class="red bold">'. $item['number'] .'</td><td>'. $item['start_time'] .'</td><td>'. $item['end_time'] .'</td><td class="red bold" align="right">'. $item['count'] .'</td></tr>';
            }
            $html .= '</table>';
            return $html;
        }else{
            return '';
        }
    }
    
    public function getChuKyDanLoResultHtml($nums, $start_time, $end_time, $pair){
        $resultModel = $this->_getResultModel();
        $loto = $resultModel->getList($start_time, $end_time, 'tt');
        if(is_array($loto) && count($loto)){
            $max = [
                    'count' => 0,
                    'start_time' => date_to_time($start_time, '-'),
                    'end_time' => 0
                ];
            $found_day = [
                    'count' => 0,
                    'start_time' => date_to_time($start_time, '-'),
                    'end_time' => 0
                ];
                $last_time = 0;
            foreach($loto as $item){
                $numbers = explode('-',preg_replace('/(\-)+/','-',$item['g0'] .'-'. $item['g1'] .'-'. $item['g2'] .'-'. $item['g3'] .'-'. $item['g4'] .'-'. $item['g5'] .'-'. $item['g6'] .'-'. $item['g7']));
                array_walk($numbers, function(&$num){
                    $num = substr($num, -2, 2);
                });
                $numbers = array_unique($numbers);
                $found = count(array_intersect($numbers,$nums)) == count($nums);
                if(!$found){
                    $found_day['count']++;    
                    $found_day['end_time'] = $item['result_date']; 
                }else{
                    // không ra thì cập nhật lại max
                    if($found_day['count'] > $max['count']){
                        $max['count'] = $found_day['count'];
                        $max['start_time'] = $found_day['start_time'];
                        $max['end_time'] = $found_day['end_time'];
                    }
                     $found_day['count'] = 0;
                     $found_day['start_time'] = $item['result_date'];
                     $found_day['end_time'] = $item['result_date'];
                }
                $last_time = $item['result_date'];
            }
            if($found_day['count'] > $max['count']){
                $max['count'] = $found_day['count'];
                $max['start_time'] = $found_day['start_time'];
                $max['end_time'] = $last_time;
            }
            
            
            $max['start_time'] = date('d-m-Y', $max['start_time'] + 86400);
            $max['end_time'] = date('d-m-Y', $max['end_time'] - 86400);
            if($max['count'] > 0){
                $html = '<table class="table table-nohead">
                    <tr><td class="text-center">
                        <p>Dàn số: <span class="bold red">'. implode(',',$nums) .'</span></p>
                        <div>Ngưỡng cực đại xuất hiện: <b class="bold red">'. $max['count'] .'</b> ngày tính cả ngày về </div>
                        <p>(Từ ngày '. $max['start_time'] .' đến ngày '. $max['end_time'] .')</p>
                    </td></tr>
                    <tr><td class="text-center">
                        <div>Điểm gan đến '. $end_time .' là: <b class="bold red">'. $found_day['count'] .'</b> ngày</div> 
                        (Không tính ngày về gần nhất, ngày về gần nhất là: '. date('d-m-Y', $found_day['start_time']) .' )</p>
                    </td></tr>
                </table>';
            }else{
                $html = '<table class="table"><tr><td class="text-center red bold">Bộ số chưa xuất hiện cùng nhau trong khoảng thời gian này</td></tr></table>';
            }
            return $html;
        }else{
            return '';
        }
    }
    
    public function getChuKyGanTheoTinhResultHtml($nums, $province_code = 'tt', $all = 1){
        return '';
//        return $this->getLoGanResultHtml($nums, '1-1-2003', date('d-m-Y'), 1, $province_code);
    }

    public function getTanSuatLoResultHtml($data){
        if(count($data) > 0 ){
            $html = '<table class="table">
                <tr><th>Cặp số</th><th>Tổng số ngày về</th><th>Tổng số lần về</th><th class="text-right">Tần suất theo ngày</th></tr>
            ';
            foreach($data as $item){
                $html .= '<tr><td class="red bold">'. $item['number'] .'</td><td>'. $item['number_days'] .'</td><td>'. $item['number_times'] .'</td><td class="red bold" align="right">'. ($item['number_days'] > 0 ? round($item['number_times']/$item['number_days'], 2) : 0) .' lần/ngày</td></tr>';
            }
            $html .= '</table>';
            return $html;
        }
        return '';
    }

    public function getTongSoResultHtml($data){
        if(count($data) > 0 ){
            $html = '<table class="table">
                <tr><th>Cặp số</th><th>Tổng số lần về</th><th>Ngày cuối về</th><th class="text-right">Số ngày chưa về</th></tr>
            ';
            foreach($data as $item){
                $html .= '<tr><td class="red bold">'. $item['number'] .'</td><td>'. $item['number_times'] .'</td><td>'. $item['end_time'] .'</td><td class="red bold" align="right">'. $item['count'] .'</td></tr>';
            }
            $html .= '</table>';
            return $html;
        }
        return '';
    }

    public function getLoVeNhieuVeItResultHtml($data, $type){
        if($type == 3) { // chưa về
            $html = '<div>';
            $html .= '<div class="alert alert-info"><b>Đặc biệt chưa về: </b>';
            if(count($data['special']) > 0 ){
                foreach($data['special'] as $item){
                    $html .= '<span>'. $item['number'] .' </span>';
                }
            }
            $html .='</div>';
            $html .= '<div class="alert alert-info"><b>Lô tô chưa về: </b>';
            if(count($data['loto']) > 0 ){
                foreach($data['loto'] as $item){
                    $html .= '<span>'. $item['number'] .' </span>';
                }
            }
            $html .='</div>';
            $html .= '</div>';
        } else { // về ít, về nhiều
            $html = '<div class="row">';
            if(count($data['special']) > 0 ){
                $html .= '<div class="col-md-6">
                    <table class="table">
                        <tr><th>Đặc biệt</th><th class="text-right">Số lần</th></tr>
                ';
                foreach($data['special'] as $item){
                    $html .= '<tr><td class="red bold">'. $item['number'] .'</td><td class="red bold" align="right">'. $item['number_times'] .'</td></tr>';
                }
                $html .= '</table></div>';
            }
            if(count($data['loto']) > 0 ){
                $html .= '<div class="col-md-6">
                            <table class="table">
                                <tr><th>Lô tô</th><th class="text-right">Số lần</th></tr>
                ';
                foreach($data['loto'] as $item){
                    $html .= '<tr><td class="red bold">'. $item['number'] .'</td><td class="red bold" align="right">'. $item['number_times'] .'</td></tr>';
                }
                $html .= '</table></div>';
            }
            $html .= '</div>';
        }
        return $html;
    }

    public function getLoRoiResultHtml($data){
        if(count($data) > 0 ){
            $html = '<table class="table">
                <tr><th class="text-center">Cặp số</th><th class="text-center">Số ngày về</th></tr>
            ';
            foreach($data as $item){
                $html .= '<tr><td align="center" class="red bold">'. $item['number'] .'</td><td class="red bold" align="center">'. $item['number_days'] .'</td></tr>';
            }
            $html .= '</table>';
            return $html;
        }
        return '';
    }

    public function getGiaiDacBietResultHtml($data){
        $result_sum_html = '';
        $result_equal_html = '';
        $result_all_html = '';
        $result_recently_html = '';
        if(count($data['sum']) > 0 ){
            $result_sum_html = '<table class="table">
                <tr><th class="text-center">Tổng</th><th class="text-center">Số ngày chưa về</th></tr>
            ';
            foreach($data['sum'] as $item){
                $result_sum_html .= '<tr><td align="center" class="red bold">'. $item['number'] .'</td><td align="center">'. $item['count'] .'</td></tr>';
            }
            $result_sum_html .= '</table>';
        }
        if(count($data['equal']) > 0 ){
            $result_equal_html = '<table class="table">
                <tr><th class="text-center">Chạm</th><th class="text-center">Số ngày chưa về</th></tr>
            ';
            foreach($data['equal'] as $item){
                $result_equal_html .= '<tr><td align="center" class="red bold">'. $item['number'] .'</td><td align="center">'. $item['count'] .'</td></tr>';
            }
            $result_equal_html .= '</table>';
        }
        if(count($data['all']) > 0 ){
            $result_all_html = '<table class="table">
                <tr><th class="text-center">Cặp số</th><th class="text-center">Số ngày chưa về</th></tr>
            ';
            foreach($data['all'] as $item){
                $result_all_html .= '<tr><td align="center" class="red bold">'. $item['number'] .'</td><td align="center">'. $item['count'] .'</td></tr>';
            }
            $result_all_html .= '</table>';
        }
        if(count($data['recently']) > 0 ){
            $result_recently_html = '<table class="table">
                <tr><th class="text-center">Giải đặc biệt <br/><i>(30 ngày gần đây)</i></th><th class="text-center">Ngày mở thưởng</th></tr>
            ';
            foreach($data['recently'] as $item){
                $result_recently_html .= '<tr><td align="center"><span>'. substr($item['g0'],0,3) .'</span><span class="red bold">'. substr($item['g0'],3,2) .'</span></td><td align="center">'. $item['day'] .'</td></tr>';
            }
            $result_recently_html .= '</table>';
        }
        return [
            'result_sum_html' => $result_sum_html,
            'result_equal_html' => $result_equal_html,
            'result_all_html' => $result_all_html,
            'result_recently_html' => $result_recently_html
        ];
    }

    public function getSoiCauTheoSoResultHtml($number, $data){
        if(count($data) > 0){
            $latest = $this->_getResultModel()->getLatestResult('tt');
            $date = date('d-m-Y', time());
            if($latest){
                // ngày tiếp theo
                $date = date('d-m-Y', $latest['result_date'] + 86400);
            }
            $html = '<p class="soicau-date">Ngày '.$date.'</p>';
            $html .= '<p>Các cầu tìm thấy cho cặp số '.$number.'</p>';
            foreach ($data as $item) {
                $html .='<div class="result-block">
                    <div class="soicau-count bold"> - Biên độ: '.$item['count'].' ngày</div>';
                if(count($item['result']) > 0){
                    foreach ($item['result'] as $value) {
                        $position = explode('_', $value['position']);
                        if(count($value['result']) > 0 && isset($value['result'][0])){
                            $v = $value['result'][0];
                            $digits = implode(',', str_split($v['g0'])).','.implode(',', str_split($v['g1'])).','.implode(',', str_split($v['g2'])).','.implode(',', str_split($v['g3'])).','.implode(',', str_split($v['g4'])).','.implode(',', str_split($v['g5'])).','.implode(',', str_split($v['g6'])).','.implode(',', str_split($v['g7']));
                            $digits = str_replace('-,', '',$digits);
                            $digits = explode(',',$digits);
                            $href = "chi-tiet-cau-lo-to-so-".$number."-tai-vi-tri-dau-".((int)$position[0] < (int)$position[1]?$position[0]:$position[1]).'-cuoi-'.((int)$position[0] > (int)$position[1]?$position[0]:$position[1]);
                            $html .='<a href="'.$href.'" class="soicau-position"><span>'.((int)$position[0] < (int)$position[1] ? $digits[$position[0]].$digits[$position[1]] : $digits[$position[1]].$digits[$position[0]]).'</span></a>';
                        }
                    }
                }
                $html .= '</div>';
            }
            return $html;
        }
        return '';
    }

    public function getChiTietCauLotoTheoSoResultHtml($number, $position_1, $position_2, $result){
        $html = '';
        $default_number = '--'; // cho giải không có dữ liệu
        $res = [];
        if(count($result) > 0){
            foreach ($result as $item){
                if(count($item['result']) > 0){
                    foreach ($item['result'] as $value){
                        if($value['position'] == $position_1.'_'.$position_2 || $value['position'] == $position_2.'_'.$position_1){
                            $res = $value['result'];
                        }
                    }
                }
            }
        }
        $appear = [];
        if(count($res) > 0){
            foreach ($res as $v){
                $digit = $this->prepareDigitData($v);
                $appear[$v['result_date']]= [
                    'result_time' => $v['result_time'],
                    'n1' => $digit[$position_1],
                    'n2' => $digit[$position_2]
                ];
            }
            foreach ($res as $r){
                $n1 = '';
                $n2 = '';
                if($r){


                    if(isset($appear[$r['result_date']-86400]) && isset($appear[$r['result_date']-86400]['n1']) && isset($appear[$r['result_date']-86400]['n2'])){
                        $n1 = $appear[$r['result_date']-86400]['n1'];
                        $n2 = $appear[$r['result_date']-86400]['n2'];
                    }
                    $digit = $this->prepareDigitData($r);
                    $digit[$position_1] = str_replace($digit[$position_1], '<i class="red">'.$digit[$position_1].'</i>', $digit[$position_1]);
                    $digit[$position_2] = str_replace($digit[$position_2], '<i class="red">'.$digit[$position_2].'</i>', $digit[$position_2]);
                    $data = $this->revertedDigit($digit);
                    foreach ($data as $key=>$value){
                        if(is_array($value) && count($value) > 0){
                            foreach ($value as $k=>$v){
                                $data[$key][$k] = $this->formatStyleNumber($n1, $n2, $v);
                            }
                        }
                    }
                    $data['g0'] = $this->formatStyleNumber($n1, $n2, $data['g0']);
                    $data['g1'] = $this->formatStyleNumber($n1, $n2, $data['g1']);
                }else{
                    return '<div class="alert alert-danger">Không tìm thấy kết quả phù hợp</div>';
                }

                $html .='
                <div class="xs-result-table">
                    <div class="xs-result-head">
                        <div class="xs-result-head-title">
                            <h3>NGÀY '. $r['result_time'] .'</h3>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <td class="xs-rl-normal" width="120">Đặc biệt</td>
                            <td class="xs-rn-normal" colspan="12">'. (isset($data['g0'])?$data['g0']:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải nhất</td>
                            <td class="xs-rn-normal" colspan="12">'. (isset($data['g1'])?$data['g1']:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải nhì</td>
                            <td class="xs-rn-normal" colspan="6">'. (isset($data['g2']) && isset($data['g2'][0])?$data['g2'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="6">'. (isset($data['g2']) && isset($data['g2'][1])?$data['g2'][1]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal" rowspan="2">Giải ba</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][0])?$data['g3'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][1])?$data['g3'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][2])?$data['g3'][2]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][3])?$data['g3'][3]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][4])?$data['g3'][4]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][5])?$data['g3'][5]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải tư</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][0])?$data['g4'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][1])?$data['g4'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][2])?$data['g4'][2]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][3])?$data['g4'][3]:$default_number) .'</td>
                        </tr>
                
                        <tr>
                            <td class="xs-rl-normal" rowspan="2">Giải năm</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][0])?$data['g5'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][1])?$data['g5'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][2])?$data['g5'][2]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][3])?$data['g5'][3]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][4])?$data['g5'][4]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][5])?$data['g5'][5]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải sáu</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][0])?$data['g6'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][1])?$data['g6'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][2])?$data['g6'][2]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải bảy</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][0])?$data['g7'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][1])?$data['g7'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][2])?$data['g7'][2]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][3])?$data['g7'][3]:$default_number) .'</td>
                        </tr>
                    </table>
                
                </div>';
            }
        }
        return $html;
    }

    public function getSoiCauTheoTinhResultHtml($province_slug, $date, $data){
        if(count($data) > 0){
            $html = '<p class="soicau-date">Ngày '.$date.'</p>';
            foreach ($data as $item) {
                $html .='<div class="result-block">
                    <div class="soicau-count bold"> - Biên độ: '.$item['count'].' ngày</div>';
                if(count($item['result']) > 0){
                    foreach ($item['result'] as $value) {
                        $position = explode('_', $value['position']);
                        if(count($value['result']) > 0 && isset($value['result'][0])){
                            $v = $value['result'][0];
                            $digits = implode(',', str_split($v['g0'])).','.implode(',', str_split($v['g1'])).','.implode(',', str_split($v['g2'])).','.implode(',', str_split($v['g3'])).','.implode(',', str_split($v['g4'])).','.implode(',', str_split($v['g5'])).','.implode(',', str_split($v['g6'])).','.implode(',', str_split($v['g7'])).','.implode(',', str_split($v['g8']));
                            $digits = str_replace('-,', '',$digits);
                            $digits = explode(',',$digits);
                            $number = ((int)$position[0] < (int)$position[1] ? $digits[$position[0]].$digits[$position[1]] : $digits[$position[1]].$digits[$position[0]]);
                            $href = "chi-tiet-cau-lo-to-theo-tinh-".$province_slug."-ngay-".$date."-so-".$number."-tai-vi-tri-dau-".((int)$position[0] < (int)$position[1]?$position[0]:$position[1]).'-cuoi-'.((int)$position[0] > (int)$position[1]?$position[0]:$position[1]);
                            $html .='<a href="'.$href.'" class="soicau-position"><span>'.((int)$position[0] < (int)$position[1] ? $digits[$position[0]].$digits[$position[1]] : $digits[$position[1]].$digits[$position[0]]).'</span></a>';
                        }
                    }
                }
                $html .= '</div>';
            }
            return $html;
        }
        return '';
    }

    public function getChiTietCauLotoTheoTinhResultHtml($number, $position_1, $position_2, $result, $province){
        $html = '';
        $default_number = '--'; // cho giải không có dữ liệu
        $res = [];
        if(count($result) > 0){
            foreach ($result as $item){
                if(count($item['result']) > 0){
                    foreach ($item['result'] as $value){
                        $num1 = substr($value['number'], 0, 1);
                        $num2 = substr($value['number'], 1, 1);
                        if(($num1.$num2 == $number || $num2.$num1 == $number) && ($value['position'] == $position_1.'_'.$position_2 || $value['position'] == $position_2.'_'.$position_1)){
                            $res = $value['result'];
                        }
                    }
                }
            }
        }
        $appear = [];
        if(count($res) > 0){
            foreach ($res as $k=>$v){
                $digit = $this->prepareDigitData($v);
                $appear[$k]= [
                    'result_time' => $v['result_time'],
                    'n1' => $digit[$position_1],
                    'n2' => $digit[$position_2]
                ];
            }
            foreach ($res as $k=>$r){
                $n1 = '';
                $n2 = '';
                if($r){
                    
                    if(isset($appear[$k+1]) && isset($appear[$k+1]['n1']) && isset($appear[$k+1]['n2'])){
                        $n1 = $appear[$k+1]['n1'];
                        $n2 = $appear[$k+1]['n2'];
                    }
                    $digit = $this->prepareDigitData($r);
                    $digit[$position_1] = str_replace($digit[$position_1], '<i class="red">'.$digit[$position_1].'</i>', $digit[$position_1]);
                    $digit[$position_2] = str_replace($digit[$position_2], '<i class="red">'.$digit[$position_2].'</i>', $digit[$position_2]);
                    if($province['code'] == 'tt'){
                        $data = $this->revertedDigit($digit);
                    }else{
                        $data = $this->revertedDigitOther($digit);
                    }
                    
                    foreach ($data as $key=>$value){
                        if(is_array($value) && count($value) > 0){
                            foreach ($value as $k=>$v){
                                $data[$key][$k] = $this->formatStyleNumber($n1, $n2, $v);
                            }
                        }
                    }
                    $data['g0'] = $this->formatStyleNumber($n1, $n2, $data['g0']);
                    $data['g1'] = $this->formatStyleNumber($n1, $n2, $data['g1']);
                }else{
                    return '<div class="alert alert-danger">Không tìm thấy kết quả phù hợp</div>';
                }

                switch($province['code']){
                    /************************** TRUYEN THONG **************************/
                    case 'tt':
                        $html .='
                            <div class="xs-result-table">
                                <div class="xs-result-head">
                                    <div class="xs-result-head-title">
                                        <h3>NGÀY '. $r['result_time'] .'</h3>
                                    </div>
                                </div>
                                <table class="table table-bordered">
                                    <tr>
                                        <td class="xs-rl-normal" width="120">Đặc biệt</td>
                                        <td class="xs-rn-normal" colspan="12">'. (isset($data['g0'])?$data['g0']:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rl-normal">Giải nhất</td>
                                        <td class="xs-rn-normal" colspan="12">'. (isset($data['g1'])?$data['g1']:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rl-normal">Giải nhì</td>
                                        <td class="xs-rn-normal" colspan="6">'. (isset($data['g2']) && isset($data['g2'][0])?$data['g2'][0]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="6">'. (isset($data['g2']) && isset($data['g2'][1])?$data['g2'][1]:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rl-normal" rowspan="2">Giải ba</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][0])?$data['g3'][0]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][1])?$data['g3'][1]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][2])?$data['g3'][2]:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][3])?$data['g3'][3]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][4])?$data['g3'][4]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][5])?$data['g3'][5]:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rl-normal">Giải tư</td>
                                        <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][0])?$data['g4'][0]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][1])?$data['g4'][1]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][2])?$data['g4'][2]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][3])?$data['g4'][3]:$default_number) .'</td>
                                    </tr>
                            
                                    <tr>
                                        <td class="xs-rl-normal" rowspan="2">Giải năm</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][0])?$data['g5'][0]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][1])?$data['g5'][1]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][2])?$data['g5'][2]:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][3])?$data['g5'][3]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][4])?$data['g5'][4]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][5])?$data['g5'][5]:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rl-normal">Giải sáu</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][0])?$data['g6'][0]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][1])?$data['g6'][1]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][2])?$data['g6'][2]:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rl-normal">Giải bảy</td>
                                        <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][0])?$data['g7'][0]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][1])?$data['g7'][1]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][2])?$data['g7'][2]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][3])?$data['g7'][3]:$default_number) .'</td>
                                    </tr>
                                </table>
                            
                            </div>';
                        break;
                    /************************** OTHER *********************************/
                    default:
                        $html .= '
                            <div class="xs-result-table">
                                <div class="xs-result-head">
                                    <div class="xs-result-head-title">
                                        <h3>NGÀY '. $r['result_time'] .'</h3>
                                    </div>
                                </div>
                                <table class="table table-bordered">
                                    <tr>
                                        <td class="xs-rl-normal" width="120">Đặc biệt</td>
                                        <td class="xs-rn-normal" colspan="12">'. (isset($data['g0'])?$data['g0']:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rl-normal">Giải nhất</td>
                                        <td class="xs-rn-normal" colspan="12">'. (isset($data['g1'])?$data['g1']:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rl-normal">Giải nhì</td>
                                        <td class="xs-rn-normal" colspan="12">'. (isset($data['g2']) && isset($data['g2'][0])?$data['g2'][0]:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rl-normal">Giải ba</td>
                                        <td class="xs-rn-normal" colspan="6">'. (isset($data['g3']) && isset($data['g3'][0])?$data['g3'][0]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="6">'. (isset($data['g3']) && isset($data['g3'][1])?$data['g3'][1]:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rl-normal" rowspan="2">Giải tư</td>
                                        <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][0])?$data['g4'][0]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][1])?$data['g4'][1]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][2])?$data['g4'][2]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][3])?$data['g4'][3]:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g4']) && isset($data['g4'][4])?$data['g4'][4]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g4']) && isset($data['g4'][5])?$data['g4'][5]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g4']) && isset($data['g4'][6])?$data['g4'][6]:$default_number) .'</td>
                                    </tr>
                            
                                    <tr>
                                        <td class="xs-rl-normal">Giải năm</td>
                                        <td class="xs-rn-normal" colspan="12">'. (isset($data['g5']) && isset($data['g5'][0])?$data['g5'][0]:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rl-normal">Giải sáu</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][0])?$data['g6'][0]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][1])?$data['g6'][1]:$default_number) .'</td>
                                        <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][2])?$data['g6'][2]:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rl-normal">Giải bảy</td>
                                        <td class="xs-rn-normal" colspan="12">'. (isset($data['g7']) && isset($data['g7'][0])?$data['g7'][0]:$default_number) .'</td>
                                    </tr>
                                    <tr>
                                        <td class="xs-rl-normal">Giải tám</td>
                                        <td class="xs-rn-normal" colspan="12">'. (isset($data['g8']) && isset($data['g8'][0])?$data['g8'][0]:$default_number) .'</td>
                                    </tr>
                                </table>
                            </div>';
                        break;
                }
            }
        }

        return $html;
    }

    public function getSoiCauBachThuResultHtml($date, $data){
        if(count($data) > 0){
            $html = '<p class="soicau-date">Ngày '.$date.'</p>';
            foreach ($data as $item) {
                $html .='<div class="result-block">
                    <div class="soicau-count bold"> - Biên độ: '.$item['count'].' ngày</div>';
                if(count($item['result']) > 0){
                    foreach ($item['result'] as $value) {
                        $position = explode('_', $value['position']);
                        if(count($value['result']) > 0 && isset($value['result'][0])){
                            $v = $value['result'][0];
                            $digits = implode(',', str_split($v['g0'])).','.implode(',', str_split($v['g1'])).','.implode(',', str_split($v['g2'])).','.implode(',', str_split($v['g3'])).','.implode(',', str_split($v['g4'])).','.implode(',', str_split($v['g5'])).','.implode(',', str_split($v['g6'])).','.implode(',', str_split($v['g7'])).','.implode(',', str_split($v['g8']));
                            $digits = str_replace('-,', '',$digits);
                            $digits = explode(',',$digits);
                            $number = $digits[$position[0]].$digits[$position[1]];
                            $href = "chi-tiet-cau-lo-to-bach-thu-ngay-".$date."-so-".$number."-tai-vi-tri-dau-".$position[0]."-cuoi-".$position[1];
                            $html .='<a href="'.$href.'" class="soicau-position"><span>'.$number.'</span></a>';
                        }
                    }
                }
                $html .= '</div>';
            }
            return $html;
        }
        return '';
    }

    public function getChiTietCauLotoBachThuResultHtml($number, $position_1, $position_2, $result){
        $html = '';
        $default_number = '--'; // cho giải không có dữ liệu
        $res = [];
        if(count($result) > 0){
            foreach ($result as $item){
                if(count($item['result']) > 0){
                    foreach ($item['result'] as $value){
                        if($value['position'] == $position_1.'_'.$position_2){
                            $res = $value['result'];
                        }
                    }
                }
            }
        }
        $appear = [];
        if(count($res) > 0){
            foreach ($res as $v){
                $digit = $this->prepareDigitData($v);
                $appear[$v['result_date']]= [
                    'result_time' => $v['result_time'],
                    'n1' => $digit[$position_1],
                    'n2' => $digit[$position_2]
                ];
            }
            foreach ($res as $r){
                $n1 = '';
                $n2 = '';
                if($r){


                    if(isset($appear[$r['result_date']-86400]) && isset($appear[$r['result_date']-86400]['n1']) && isset($appear[$r['result_date']-86400]['n2'])){
                        $n1 = $appear[$r['result_date']-86400]['n1'];
                        $n2 = $appear[$r['result_date']-86400]['n2'];
                    }
                    $digit = $this->prepareDigitData($r);
                    $digit[$position_1] = str_replace($digit[$position_1], '<i class="red">'.$digit[$position_1].'</i>', $digit[$position_1]);
                    $digit[$position_2] = str_replace($digit[$position_2], '<i class="red">'.$digit[$position_2].'</i>', $digit[$position_2]);
                    $data = $this->revertedDigit($digit);
                    foreach ($data as $key=>$value){
                        if(is_array($value) && count($value) > 0){
                            foreach ($value as $k=>$v){
                                $data[$key][$k] = $this->formatStyleNumberOneWay($n1, $n2, $v);
                            }
                        }
                    }
                    $data['g0'] = $this->formatStyleNumberOneWay($n1, $n2, $data['g0']);
                    $data['g1'] = $this->formatStyleNumberOneWay($n1, $n2, $data['g1']);
                }else{
                    return '<div class="alert alert-danger">Không tìm thấy kết quả phù hợp</div>';
                }

                $html .='
                <div class="xs-result-table">
                    <div class="xs-result-head">
                        <div class="xs-result-head-title">
                            <h3>NGÀY '. $r['result_time'] .'</h3>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <td class="xs-rl-normal" width="120">Đặc biệt</td>
                            <td class="xs-rn-normal" colspan="12">'. (isset($data['g0'])?$data['g0']:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải nhất</td>
                            <td class="xs-rn-normal" colspan="12">'. (isset($data['g1'])?$data['g1']:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải nhì</td>
                            <td class="xs-rn-normal" colspan="6">'. (isset($data['g2']) && isset($data['g2'][0])?$data['g2'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="6">'. (isset($data['g2']) && isset($data['g2'][1])?$data['g2'][1]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal" rowspan="2">Giải ba</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][0])?$data['g3'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][1])?$data['g3'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][2])?$data['g3'][2]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][3])?$data['g3'][3]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][4])?$data['g3'][4]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][5])?$data['g3'][5]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải tư</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][0])?$data['g4'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][1])?$data['g4'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][2])?$data['g4'][2]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][3])?$data['g4'][3]:$default_number) .'</td>
                        </tr>
                
                        <tr>
                            <td class="xs-rl-normal" rowspan="2">Giải năm</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][0])?$data['g5'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][1])?$data['g5'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][2])?$data['g5'][2]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][3])?$data['g5'][3]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][4])?$data['g5'][4]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][5])?$data['g5'][5]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải sáu</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][0])?$data['g6'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][1])?$data['g6'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][2])?$data['g6'][2]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải bảy</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][0])?$data['g7'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][1])?$data['g7'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][2])?$data['g7'][2]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][3])?$data['g7'][3]:$default_number) .'</td>
                        </tr>
                    </table>
                
                </div>';
            }
        }
        return $html;
    }

    public function getSoiCauVeHaiNhayResultHtml($date, $data){
        if(count($data) > 0){
            $html = '<p class="soicau-date">Ngày '.$date.'</p>';
            foreach ($data as $item) {
                $html .='<div class="result-block">
                    <div class="soicau-count bold"> - Biên độ: '.$item['count'].' ngày</div>';
                if(count($item['result']) > 0){
                    foreach ($item['result'] as $value) {
                        $position = explode('_', $value['position']);
                        if(count($value['result']) > 0 && isset($value['result'][0])){
                            $v = $value['result'][0];
                            $digits = implode(',', str_split($v['g0'])).','.implode(',', str_split($v['g1'])).','.implode(',', str_split($v['g2'])).','.implode(',', str_split($v['g3'])).','.implode(',', str_split($v['g4'])).','.implode(',', str_split($v['g5'])).','.implode(',', str_split($v['g6'])).','.implode(',', str_split($v['g7'])).','.implode(',', str_split($v['g8']));
                            $digits = str_replace('-,', '',$digits);
                            $digits = explode(',',$digits);
                            $number = ((int)$position[0] < (int)$position[1] ? $digits[$position[0]].$digits[$position[1]] : $digits[$position[1]].$digits[$position[0]]);
                            $href = "chi-tiet-cau-lo-to-ve-hai-nhay-ngay-".$date."-so-".$number."-tai-vi-tri-dau-".$position[0]."-cuoi-".$position[1];
                            $html .='<a href="'.$href.'" class="soicau-position"><span>'.$number.'</span></a>';
                        }
                    }
                }
                $html .= '</div>';
            }
            return $html;
        }
        return '';
    }

    public function getChiTietCauLotoVeHaiNhayResultHtml($number, $position_1, $position_2, $result){
        $html = '';
        $default_number = '--'; // cho giải không có dữ liệu
        $res = [];
        if(count($result) > 0){
            foreach ($result as $item){
                if(count($item['result']) > 0){
                    foreach ($item['result'] as $value){
                        $num1 = substr($value['number'], 0, 1);
                        $num2 = substr($value['number'], 1, 1);
                        if(($num1.$num2 == $number || $num2.$num1 == $number) && ($value['position'] == $position_1.'_'.$position_2 || $value['position'] == $position_2.'_'.$position_1)){
                            $res = $value['result'];
                        }
                    }
                }
            }
        }
        $appear = [];
        if(count($res) > 0){
            foreach ($res as $v){
                $digit = $this->prepareDigitData($v);
                $appear[$v['result_date']]= [
                    'result_time' => $v['result_time'],
                    'n1' => $digit[$position_1],
                    'n2' => $digit[$position_2]
                ];
            }
            foreach ($res as $r){
                $n1 = '';
                $n2 = '';
                if($r){


                    if(isset($appear[$r['result_date']-86400]) && isset($appear[$r['result_date']-86400]['n1']) && isset($appear[$r['result_date']-86400]['n2'])){
                        $n1 = $appear[$r['result_date']-86400]['n1'];
                        $n2 = $appear[$r['result_date']-86400]['n2'];
                    }
                    $digit = $this->prepareDigitData($r);
                    $digit[$position_1] = str_replace($digit[$position_1], '<i class="red">'.$digit[$position_1].'</i>', $digit[$position_1]);
                    $digit[$position_2] = str_replace($digit[$position_2], '<i class="red">'.$digit[$position_2].'</i>', $digit[$position_2]);
                    $data = $this->revertedDigit($digit);
                    foreach ($data as $key=>$value){
                        if(is_array($value) && count($value) > 0){
                            foreach ($value as $k=>$v){
                                $data[$key][$k] = $this->formatStyleNumber($n1, $n2, $v);
                            }
                        }
                    }
                    $data['g0'] = $this->formatStyleNumber($n1, $n2, $data['g0']);
                    $data['g1'] = $this->formatStyleNumber($n1, $n2, $data['g1']);
                }else{
                    return '<div class="alert alert-danger">Không tìm thấy kết quả phù hợp</div>';
                }

                $html .='
                <div class="xs-result-table">
                    <div class="xs-result-head">
                        <div class="xs-result-head-title">
                            <h3>NGÀY '. $r['result_time'] .'</h3>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <td class="xs-rl-normal" width="120">Đặc biệt</td>
                            <td class="xs-rn-normal" colspan="12">'. (isset($data['g0'])?$data['g0']:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải nhất</td>
                            <td class="xs-rn-normal" colspan="12">'. (isset($data['g1'])?$data['g1']:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải nhì</td>
                            <td class="xs-rn-normal" colspan="6">'. (isset($data['g2']) && isset($data['g2'][0])?$data['g2'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="6">'. (isset($data['g2']) && isset($data['g2'][1])?$data['g2'][1]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal" rowspan="2">Giải ba</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][0])?$data['g3'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][1])?$data['g3'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][2])?$data['g3'][2]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][3])?$data['g3'][3]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][4])?$data['g3'][4]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][5])?$data['g3'][5]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải tư</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][0])?$data['g4'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][1])?$data['g4'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][2])?$data['g4'][2]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][3])?$data['g4'][3]:$default_number) .'</td>
                        </tr>
                
                        <tr>
                            <td class="xs-rl-normal" rowspan="2">Giải năm</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][0])?$data['g5'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][1])?$data['g5'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][2])?$data['g5'][2]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][3])?$data['g5'][3]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][4])?$data['g5'][4]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][5])?$data['g5'][5]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải sáu</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][0])?$data['g6'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][1])?$data['g6'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][2])?$data['g6'][2]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải bảy</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][0])?$data['g7'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][1])?$data['g7'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][2])?$data['g7'][2]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][3])?$data['g7'][3]:$default_number) .'</td>
                        </tr>
                    </table>
                
                </div>';
            }
        }
        return $html;
    }

     public function getSoiCauDacBietResultHtml($date, $data){
        if(count($data) > 0){
            $html = '<p class="soicau-date">Ngày '.$date.'</p>';
            foreach ($data as $item) {
                $html .='<div class="result-block">
                    <div class="soicau-count bold"> - Biên độ: '.$item['count'].' ngày</div>';
                if(count($item['result']) > 0){
                    foreach ($item['result'] as $value) {
                        $position = explode('_', $value['position']);
                        if(count($value['result']) > 0 && isset($value['result'][0])){
                            $v = $value['result'][0];
                            $digits = implode(',', str_split($v['g0'])).','.implode(',', str_split($v['g1'])).','.implode(',', str_split($v['g2'])).','.implode(',', str_split($v['g3'])).','.implode(',', str_split($v['g4'])).','.implode(',', str_split($v['g5'])).','.implode(',', str_split($v['g6'])).','.implode(',', str_split($v['g7'])).','.implode(',', str_split($v['g8']));
                            $digits = str_replace('-,', '',$digits);
                            $digits = explode(',',$digits);
                            $number = ((int)$position[0] < (int)$position[1] ? $digits[$position[0]].$digits[$position[1]] : $digits[$position[1]].$digits[$position[0]]);
                            $href = "chi-tiet-cau-dac-biet-ngay-".$date."-so-".$number."-tai-vi-tri-dau-".$position[0]."-cuoi-".$position[1];
                            $html .='<a href="'.$href.'" class="soicau-position"><span>'.$number.'</span></a>';
                        }
                    }
                }
                $html .= '</div>';
            }
            return $html;
        }
        return '';
    }

    public function getChiTietCauDacBietResultHtml($number, $position_1, $position_2, $result){
        $html = '';
        $default_number = '--'; // cho giải không có dữ liệu
        $res = [];
        if(count($result) > 0){
            foreach ($result as $item){
                if(count($item['result']) > 0){
                    foreach ($item['result'] as $value){
                        $num1 = substr($value['number'], 0, 1);
                        $num2 = substr($value['number'], 1, 1);
                        if(($num1.$num2 == $number || $num2.$num1 == $number) && ($value['position'] == $position_1.'_'.$position_2 || $value['position'] == $position_2.'_'.$position_1)){
                            $res = $value['result'];
                        }
                    }
                }
            }
        }
        $appear = [];
        if(count($res) > 0){
            foreach ($res as $v){
                $digit = $this->prepareDigitData($v);
                $appear[$v['result_date']]= [
                    'result_time' => $v['result_time'],
                    'n1' => $digit[$position_1],
                    'n2' => $digit[$position_2]
                ];
            }
            foreach ($res as $r){
                $n1 = '';
                $n2 = '';
                if($r){

                    if(isset($appear[$r['result_date']-86400]) && isset($appear[$r['result_date']-86400]['n1']) && isset($appear[$r['result_date']-86400]['n2'])){
                        $n1 = $appear[$r['result_date']-86400]['n1'];
                        $n2 = $appear[$r['result_date']-86400]['n2'];
                    }
                    $digit = $this->prepareDigitData($r);
                    $digit[$position_1] = str_replace($digit[$position_1], '<i class="red">'.$digit[$position_1].'</i>', $digit[$position_1]);
                    $digit[$position_2] = str_replace($digit[$position_2], '<i class="red">'.$digit[$position_2].'</i>', $digit[$position_2]);
                    $data = $this->revertedDigit($digit);
                    $data['g0'] = $this->formatStyleNumber($n1, $n2, $data['g0']);
                }else{
                    return '<div class="alert alert-danger">Không tìm thấy kết quả phù hợp</div>';
                }

                $html .='
                <div class="xs-result-table">
                    <div class="xs-result-head">
                        <div class="xs-result-head-title">
                            <h3>NGÀY '. $r['result_time'] .'</h3>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <td class="xs-rl-normal" width="120">Đặc biệt</td>
                            <td class="xs-rn-normal" colspan="12">'. (isset($data['g0'])?$data['g0']:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải nhất</td>
                            <td class="xs-rn-normal" colspan="12">'. (isset($data['g1'])?$data['g1']:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải nhì</td>
                            <td class="xs-rn-normal" colspan="6">'. (isset($data['g2']) && isset($data['g2'][0])?$data['g2'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="6">'. (isset($data['g2']) && isset($data['g2'][1])?$data['g2'][1]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal" rowspan="2">Giải ba</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][0])?$data['g3'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][1])?$data['g3'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][2])?$data['g3'][2]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][3])?$data['g3'][3]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][4])?$data['g3'][4]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][5])?$data['g3'][5]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải tư</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][0])?$data['g4'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][1])?$data['g4'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][2])?$data['g4'][2]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][3])?$data['g4'][3]:$default_number) .'</td>
                        </tr>
                
                        <tr>
                            <td class="xs-rl-normal" rowspan="2">Giải năm</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][0])?$data['g5'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][1])?$data['g5'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][2])?$data['g5'][2]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][3])?$data['g5'][3]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][4])?$data['g5'][4]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][5])?$data['g5'][5]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải sáu</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][0])?$data['g6'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][1])?$data['g6'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g6']) && isset($data['g6'][2])?$data['g6'][2]:$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải bảy</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][0])?$data['g7'][0]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][1])?$data['g7'][1]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][2])?$data['g7'][2]:$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][3])?$data['g7'][3]:$default_number) .'</td>
                        </tr>
                    </table>
                
                </div>';
            }
        }
        return $html;
    }

    public function revertedDigit($digit){

        $g0 = implode('', array_splice($digit, 0, 5));

        $g1 = implode('',array_splice($digit, 0, 5));

        $g20 = implode('',array_splice($digit, 0, 5));
        $g21 = implode('',array_splice($digit, 0, 5));

        $g30 = implode('',array_splice($digit, 0, 5));
        $g31 = implode('',array_splice($digit, 0, 5));
        $g32 = implode('',array_splice($digit, 0, 5));
        $g33 = implode('',array_splice($digit, 0, 5));
        $g34 = implode('',array_splice($digit, 0, 5));
        $g35 = implode('',array_splice($digit, 0, 5));

        $g40 = implode('',array_splice($digit, 0, 4));
        $g41 = implode('',array_splice($digit, 0, 4));
        $g42 = implode('',array_splice($digit, 0, 4));
        $g43 = implode('',array_splice($digit, 0, 4));

        $g50 = implode('',array_splice($digit, 0, 4));
        $g51 = implode('',array_splice($digit, 0, 4));
        $g52 = implode('',array_splice($digit, 0, 4));
        $g53 = implode('',array_splice($digit, 0, 4));
        $g54 = implode('',array_splice($digit, 0, 4));
        $g55 = implode('',array_splice($digit, 0, 4));

        $g60 = implode('',array_splice($digit, 0, 3));
        $g61 = implode('',array_splice($digit, 0, 3));
        $g62 = implode('',array_splice($digit, 0, 3));

        $g70 = implode('',array_splice($digit, 0, 2));
        $g71 = implode('',array_splice($digit, 0, 2));
        $g72 = implode('',array_splice($digit, 0, 2));
        $g73 = implode('',array_splice($digit, 0, 2));

        return $data = [
            'g0' => $g0,
            'g1' => $g1,
            'g2' => [$g20,$g21],
            'g3' => [$g30,$g31,$g32,$g33,$g34,$g35],
            'g4' => [$g40,$g41,$g42,$g43],
            'g5' => [$g50,$g51,$g52,$g53,$g54,$g55],
            'g6' => [$g60,$g61,$g62],
            'g7' => [$g70,$g71,$g72,$g73]
        ];
    }

    public function revertedDigitOther($digit){

        $g0 = implode('', array_splice($digit, 0, 6));

        $g1 = implode('',array_splice($digit, 0, 5));

        $g2 = implode('',array_splice($digit, 0, 5));

        $g30 = implode('',array_splice($digit, 0, 5));
        $g31 = implode('',array_splice($digit, 0, 5));

        $g40 = implode('',array_splice($digit, 0, 5));
        $g41 = implode('',array_splice($digit, 0, 5));
        $g42 = implode('',array_splice($digit, 0, 5));
        $g43 = implode('',array_splice($digit, 0, 5));
        $g44 = implode('',array_splice($digit, 0, 5));
        $g45 = implode('',array_splice($digit, 0, 5));
        $g46 = implode('',array_splice($digit, 0, 5));

        $g5 = implode('',array_splice($digit, 0, 4));

        $g60 = implode('',array_splice($digit, 0, 4));
        $g61 = implode('',array_splice($digit, 0, 4));
        $g62 = implode('',array_splice($digit, 0, 4));

        $g7 = implode('',array_splice($digit, 0, 3));

        $g8 = implode('',array_splice($digit, 0, 2));

        return $data = [
            'g0' => $g0,
            'g1' => $g1,
            'g2' => [$g2],
            'g3' => [$g30,$g31],
            'g4' => [$g40,$g41,$g42,$g43,$g44,$g45,$g46],
            'g5' => [$g5],
            'g6' => [$g60,$g61,$g62],
            'g7' => [$g7],
            'g8' => [$g8],
        ];
    }

    public function formatStyleNumber($n1, $n2, $number){
        // Xử lý number => lấy 2 số cuối cùng
        $arrNum = array_reverse(str_split($number));
        $count = 0;
        $lenght = 0;
        $flag = false;
        foreach ($arrNum as $k=>$v){
            if(is_numeric($v)){
                $count++;
            }
            if($count == 2 && $flag == false){
                $flag = true;
                $offset = $k;
            }
            if($count == 3){
                $lenght = $k;
                break;
            }
        }

        if($count == 2){
            $twoLastDigit = $number;
            $result = [
                'firstDigit' => '',
                'betweenDigit' => '',
                'twoLastDigit' => $twoLastDigit
            ];
        }else{
            $twoLastDigit = array_splice($arrNum, 0, $offset+1);
            $twoLastDigit = implode('',array_reverse($twoLastDigit));
            $betweenDigit = '';
            $firstDigit = '';
            if(count($arrNum) > 0){
                $betweenDigit = array_splice($arrNum, 0, $lenght - $offset - 1);
                $betweenDigit = implode('',array_reverse($betweenDigit));
            }
            if(count($arrNum) > 0){
                $firstDigit = implode('',array_reverse($arrNum));
            }

            $result = [
                'firstDigit' => $firstDigit,
                'betweenDigit' => $betweenDigit,
                'twoLastDigit' => $twoLastDigit
            ];
        }
        $twoLastDigitFormat = str_replace('<i class="red">', '', str_replace('</i>', '', $result['twoLastDigit']));
        if($n1.$n2 == $twoLastDigitFormat || $n2.$n1 == $twoLastDigitFormat){ // kiểm tra $n1, $n2 trùng 2 số cuối
            $result['twoLastDigit'] = str_replace('<i class="red">', '', $result['twoLastDigit']);
            $result['twoLastDigit'] = '<large class="red bold">'.$twoLastDigitFormat.'</large>';
        }

        return $result['firstDigit'].$result['betweenDigit'].$result['twoLastDigit'];
    }

    public function formatStyleNumberOneWay($n1, $n2, $number){
        // Xử lý number => lấy 2 số cuối cùng
        $arrNum = array_reverse(str_split($number));
        $count = 0;
        $lenght = 0;
        $flag = false;
        foreach ($arrNum as $k=>$v){
            if(is_numeric($v)){
                $count++;
            }
            if($count == 2 && $flag == false){
                $flag = true;
                $offset = $k;
            }
            if($count == 3){
                $lenght = $k;
                break;
            }
        }

        if($count == 2){
            $twoLastDigit = $number;
            $result = [
                'firstDigit' => '',
                'betweenDigit' => '',
                'twoLastDigit' => $twoLastDigit
            ];
        }else{
            $twoLastDigit = array_splice($arrNum, 0, $offset+1);
            $twoLastDigit = implode('',array_reverse($twoLastDigit));
            $betweenDigit = '';
            $firstDigit = '';
            if(count($arrNum) > 0){
                $betweenDigit = array_splice($arrNum, 0, $lenght - $offset - 1);
                $betweenDigit = implode('',array_reverse($betweenDigit));
            }
            if(count($arrNum) > 0){
                $firstDigit = implode('',array_reverse($arrNum));
            }

            $result = [
                'firstDigit' => $firstDigit,
                'betweenDigit' => $betweenDigit,
                'twoLastDigit' => $twoLastDigit
            ];
        }
        $twoLastDigitFormat = str_replace('<i class="red">', '', str_replace('</i>', '', $result['twoLastDigit']));
        if($n1.$n2 == $twoLastDigitFormat){ // kiểm tra $n1, $n2 trùng 2 số cuối
            $result['twoLastDigit'] = str_replace('<i class="red">', '', $result['twoLastDigit']);
            $result['twoLastDigit'] = '<large class="red bold">'.$twoLastDigitFormat.'</large>';
        }

        return $result['firstDigit'].$result['betweenDigit'].$result['twoLastDigit'];
    }

    public function prepareDigitData($result){
        $data = [];
        if($result) {
            $data = implode(',', str_split($result['g0'])).','.implode(',', str_split($result['g1'])).','.implode(',', str_split($result['g2'])).','.implode(',', str_split($result['g3'])).','.implode(',', str_split($result['g4'])).','.implode(',', str_split($result['g5'])).','.implode(',', str_split($result['g6'])).','.implode(',', str_split($result['g7']));
            if($result['g8'] != ''){
                $data = $data.','.implode(',', str_split($result['g8']));
            }
            $data = str_replace('-,', '',$data);
            $data = explode(',',$data);
        }
        return $data;
    }
    
    /**
     * @return Loto_Model_Result
     */
    protected function _getResultModel(){
        return $this->getModelFromCache('Loto_Model_Result');
    }
}