<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/16/2019
 * Time: 2:47 PM
 */
class API_Model_LotoResult extends Mava_Model
{
    public function getResultByProvince($province, $offset, $limit)
    {
        return $result = $this->_getDb()->fetchAll("SELECT * FROM #__loto_result WHERE `province`='". $province ."' ORDER BY result_date DESC LIMIT ". $offset .",". $limit ."");
    }

    public function getResultByDateProvince($date, $province, $offset, $limit)
    {
        return $result = $this->_getDb()->fetchAll("SELECT * FROM #__loto_result WHERE `result_date`<'". date_to_time($date,'/') ."' AND `province`='". $province ."' ORDER BY result_date DESC LIMIT ". $offset .",". $limit ."");
    }

    public function getList($start_date, $end_date, $province){
        return $this->_getDb()->fetchAll("SELECT * FROM #__loto_result WHERE `result_date` BETWEEN '". date_to_time($start_date,'/')  ."' AND '". date_to_time($end_date,'/') ."' AND `province`='". $province ."' ORDER BY result_date DESC ");
    }

    public function getSpecialResultByProvince($province)
    {
        return $result = $this->_getDb()->fetchAll("SELECT id, result_date, g0 FROM #__loto_result WHERE `province`='". $province ."' ORDER BY result_date DESC");
    }

    public function getLatestResult($province = 'tt'){
        return $this->_getDb()->fetchRow("SELECT * FROM #__loto_result WHERE `province`='". $province ."' ORDER BY `result_date` DESC");
    }

    public function hasLive($date, $province){
        if(date('d/m/Y') !== $date){
            return false;
        }else{
            $today_loto = Mava_Application::getConfig('loto_schedule/T'. (date('w')+1));
            $loto_live_offset = intval(Mava_Application::getConfig('loto_live_offset_minute'));
            if(!key_exists(trim($province), $today_loto)){
                return false;
            }else{
                $now_time = intval(date('Hi'));
                $real_start_time = intval(str_replace(':','', $today_loto[$province][0]));
                $real_end_time = intval(str_replace(':','', $today_loto[$province][1]));
                if($now_time+$loto_live_offset >= $real_start_time && $now_time <= $real_end_time){
                    return true;
                }else{
                    return false;
                }
            }
        }
    }

    public function getLatest($province, $include_province = false){
        $has_live = $this->hasLive(date('d/m/Y'), $province);
        if($has_live){
            $this->_crawlLatest($province);
        }
        $result = $this->_getDb()->fetchRow("SELECT * FROM #__loto_result WHERE `province`='". $province ."' ORDER BY `result_date` DESC");
        if($result || $has_live){
            $data = $result?$this->_formatLotoData($result):[];
            $response = [
                'has_live' => (int)$has_live,
                'loto' => $data
            ];
            if($include_province){
                $provinceModel = $this->_getProvinceModel();
                $provinces = $provinceModel->getSimpleList();
                $response['provinces'] = $provinces;
            }
            return [
                'error' => 0,
                'message' => '',
                'data' => $response
            ];
        }else{
            return [
                'error' => 1,
                'message' => 'Không có kết quả'
            ];
        }
    }

    public function getResultByDate($date, $province){
        $result = $this->_getDb()->fetchRow("SELECT * FROM #__loto_result WHERE `result_date`='". date_to_time($date,'/') ."' AND `province`='". $province ."'");
        $has_live = $this->hasLive($date, $province);
        if($result || $has_live){
            $data = $result?$this->_formatLotoData($result):[];
            return [
                'error' => 0,
                'message' => '',
                'data' => [
                    'has_live' => (int)$has_live,
                    'loto' => $data
                ]
            ];
        }else{
            return [
                'error' => 1,
                'message' => 'Không có kết quả'
            ];
        }
    }

    public function getNext($province, $loto_id){
        $result = $this->_getDb()->fetchRow("SELECT * FROM #__loto_result WHERE `id`>'". (int)$loto_id ."' AND `province`='". $province ."' ORDER BY `result_date` ASC");
        if($result){
            $has_live = $this->hasLive(date('d/m/Y', $result['result_date']), $province);
            $data = $this->_formatLotoData($result);
            return [
                'error' => 0,
                'message' => '',
                'data' => [
                    'has_live' => (int)$has_live,
                    'loto' => $data
                ]
            ];
        }else{
            return [
                'error' => 1,
                'message' => 'Không có kết quả'
            ];
        }
    }

    public function getPrev($province, $loto_id){
        $result = $this->_getDb()->fetchRow("SELECT * FROM #__loto_result WHERE `id`<'". (int)$loto_id ."' AND `province`='". $province ."' ORDER BY `result_date` DESC");
        if($result){
            $has_live = $this->hasLive(date('d/m/Y', $result['result_date']), $province);
            $data = $this->_formatLotoData($result);
            return [
                'error' => 0,
                'message' => '',
                'data' => [
                    'has_live' => (int)$has_live,
                    'loto' => $data
                ]
            ];
        }else{
            return [
                'error' => 1,
                'message' => 'Không có kết quả'
            ];
        }
    }

    protected function _crawlLatest($province){
        Mava_Url::setParam('pv', $province);
        Mava_Url::setParam('start', date('d-m-Y'));
        Mava_Url::setParam('end', time());
        ob_start();
        $controller = new Loto_Controller_Index();
        $controller->crawlAction();
        ob_end_clean();
    }

    protected function _formatLotoData($result){
        if(Mava_Url::getParam('_source') !== 'app'){
            return $result;
        }
        $data = [
            'g0'   => $result['g0'],
            'g1'   => $result['g1'],
            'g2'   => $result['g2']!=""?explode('-', $result['g2']):[],
            'g3'   => $result['g3']!=""?explode('-', $result['g3']):[],
            'g4'   => $result['g4']!=""?explode('-', $result['g4']):[],
            'g5'   => $result['g5']!=""?explode('-', $result['g5']):[],
            'g6'   => $result['g6']!=""?explode('-', $result['g6']):[],
            'g7'   => $result['g7']!=""?explode('-', $result['g7']):[],
            'g8'   => $result['g8']!=""?explode('-', $result['g8']):[],
        ];
        $first = [];
        $last = [];
        for($i=0;$i<10;$i++){
            $first["dau". $i] = [];
            $last["duoi". $i] = [];
        }

        $all_number = [];

        foreach($data as $item){
            if(!is_array($item)){
                $item = array($item);
            }
            foreach($item as $number){
                $all_number[] = $number;
                // 2 số cuối
                $lastTwoDigit = substr($number,-2,2);
                // số đầu của 2 số cuối
                $beforeLastDigit = substr($number,-2,1);
                // số cuối của 2 số cuối
                $lastDigit = substr($number,-1,1);
                // array đầu
                $first["dau". $beforeLastDigit][] = $lastTwoDigit;
                // array cuối
                $last["duoi". $lastDigit][] =  $lastTwoDigit;
            }
        }

        for($i=0;$i<10;$i++){
            $count = array_count_values($first["dau". $i]);
            ksort($count);
            $item = [];
            foreach($count as $k => $v){
                $item[] = [
                    'num' => $k,
                    'count' => $v
                ];
            }
            $first["dau". $i] = $item;

            $count = array_count_values($last["duoi". $i]);
            ksort($count);
            $item = [];
            foreach($count as $k => $v){
                $item[] = [
                    'num' => $k,
                    'count' => $v
                ];
            }
            $last["duoi". $i] = $item;
        }
        $data['all'] = $all_number;
        $data['first'] = $first;
        $data['last'] = $last;
        $data = array_merge($data, [
            'id'   => $result['id'],
            'region' => $result['region'],
            'date' => $result['result_date'],
            'date_formatted' => date('d/m/Y',$result['result_date']),
        ]);
        return $data;
    }

    public function prayOneNumberResult($data){
        $latest = $this->getLatestResult('tt');
        $date = date('d-m-Y', time());
        if($latest) {
            // ngày tiếp theo
            $date = date('d-m-Y', $latest['result_date'] + 86400);
        }

        $key_cached = md5('loto_pray_one_' . 'tt' . '_' . $date);

        if($result = Mava_Application::getCache($key_cached)){
            return $result;
        }

        $data['number'] = sprintf('%02d', $data['number']);
        //get 30 first records
        $loto = $this->getResultByProvince('tt', 0, 30);

        $num_1 = substr($data['number'], 0, 1);
        $num_2 = substr($data['number'], 1, 1);

        $position_num_1 = [];
        $position_num_2 = [];

        // bóc tách kết quả ngày gần nhất
        $arrData = $this->prepareDigitData($loto[0]);

        foreach ($arrData as $k=>$v){
            if($v == $num_1){
                $position_num_1[] = $k;
            }
            if($v == $num_2){
                $position_num_2[] = $k;
            }
        }
        $position = [];
        foreach ($position_num_1 as $p1){
            foreach ($position_num_2 as $p2){
                $position[] = [$p1,$p2];
            }
        }

        foreach ($position as $key=>$value) {
            $rs[$value[0].'_'.$value[1]] = [
                'number' => $data['number'],
                'count' => 0,
                'result' => []
            ];

            for($i=1;$i<30;$i++){
                if(isset($loto[$i])){
                   // bóc tách kết quả của ngày liền trước
                    $digit = $this->prepareDigitData($loto[$i]);

                    // lấy ra số của ngày liền trước ở vị trí này
                    $number = $digit[$value[0]].$digit[$value[1]];
                    $invertedNumber = $digit[$value[1]].$digit[$value[0]];
                    if(isset($loto[$i-1])){
                        // kiểm tra number xem có về ở ngày liền sau ko
                        $arrNumber = $this->returnArrayNumber($loto[$i-1]);
                        if(in_array($number, $arrNumber) || in_array($invertedNumber, $arrNumber)){
                            $rs[$value[0].'_'.$value[1]]['count']++;
                            $rs[$value[0].'_'.$value[1]]['result'][] = isset($data['highlight']) && (int)$data['highlight']===1?$this->_splitNumberForPray($this->_formatLotoData($loto[$i-1]), $value[0], $value[1]):$this->_formatLotoData($loto[$i-1]);
                        } else {
                            // thêm kết quả cuối cùng trước khi break
                            $rs[$value[0].'_'.$value[1]]['result'][] = isset($data['highlight']) && (int)$data['highlight']===1?$this->_splitNumberForPray($this->_formatLotoData($loto[$i-1]), $value[0], $value[1]):$this->_formatLotoData($loto[$i-1]);
                            break;
                        }
                    }
                }
            }
        }

        array_walk($rs, function(&$item, $key){
            $item['position'] = $key;
        });

        $result = [];
        foreach ($rs as $k=>$v) {
            if($v['count'] >= 3){
                // nhóm lại theo số count
                $result[$v['count']]['count'] = $v['count'];
                $result[$v['count']]['result'][] = $v;
            }
        }

        usort($result, function($a, $b){
            return $a["count"] < $b["count"];
        });

        // set cached data
        Mava_Application::setCache($key_cached, $result, 24*60*60);

        return $result;
    }

    protected function _splitNumberForPray($loto, $pos_1, $pos_2){
        if(Mava_Url::getParam('_source') !== 'app'){
            return $loto;
        }
        $pos_1++;
        $pos_2++;
        $current_len = 0;
        $result = $loto;
        $result['g0'] = explode("-", $result['g0']);
        $result['g1'] = explode("-", $result['g1']);
        $set = ['g0','g1','g2','g3','g4','g5','g6','g7','g8'];
        foreach($set as $s){
            for($i=0;$i<count($result[$s]);$i++){
                $num_length = strlen($result[$s][$i]);
                $found = false;
                if($current_len < $pos_1 && $current_len + strlen($result[$s][$i]) >= $pos_1){
                    // found pos 1
                    $result[$s][$i] = [
                        ['type' => 'n', 'text' => substr($result[$s][$i], 0, $pos_1 - $current_len - 1)],
                        ['type' => 'h', 'text' => substr($result[$s][$i], $pos_1 - $current_len - 1, 1)],
                        ['type' => 'n', 'text' => substr($result[$s][$i],  $pos_1 - $current_len)],
                    ];
                    $found = true;
                }
                if($found){
                    // found pos 2 after pos 1
                    $item_length = 0;
                    for($k=0;$k<count($result[$s][$i]);$k++){
                        $this_length = strlen($result[$s][$i][$k]['text']);
                        if($current_len + $item_length < $pos_2 && $current_len + strlen($result[$s][$i][$k]['text']) + $item_length >= $pos_2){
                            // found pos 2 in sub
                            $result[$s][$i][$k] = [
                                ['type' => 'n', 'text' => substr($result[$s][$i][$k]['text'], 0, $pos_2 - $current_len - $item_length - 1)],
                                ['type' => 'h', 'text' => substr($result[$s][$i][$k]['text'], $pos_2 - $current_len - $item_length - 1, 1)],
                                ['type' => 'n', 'text' => substr($result[$s][$i][$k]['text'],  $pos_2 - $current_len - $item_length)],
                            ];
                        }
                        $item_length += $this_length;
                    }
                }else{
                    if($current_len < $pos_2 && $current_len + strlen($result[$s][$i]) >= $pos_2){
                        // found pos 2
                        $result[$s][$i] = [
                            ['type' => 'n', 'text' => substr($result[$s][$i], 0, $pos_2 - $current_len - 1)],
                            ['type' => 'h', 'text' => substr($result[$s][$i], $pos_2 - $current_len - 1, 1)],
                            ['type' => 'n', 'text' => substr($result[$s][$i],  $pos_2 - $current_len)],
                        ];
                    }
                }
                $current_len += $num_length;
            }
        }
        return $result;
    }

    public function prayAllNumberResult($data){
        $key_cached = md5('loto_pray_all_' . $data['region_code'] . '_' . $data['date']);

        if($result = Mava_Application::getCache($key_cached)){
            return $result;
        }

        ini_set('memory_limit', '128M');
        //get 30 first records
        $loto = $this->getResultByDateProvince($data['date'], $data['region_code'], 0, 30);
        $rsAll = [];
        $position_num_1 = [];
        $position_num_2 = [];
        $position = [];
        $rs = [];

        // bóc tách kết quả ngày gần nhất
        $arrData = $this->prepareDigitData($loto[0]);

        for($n=0;$n<=99;$n++) {
            $num = sprintf('%02d', $n);
            $num_1 = substr($num, 0, 1);
            $num_2 = substr($num, 1, 1);
            $position_num_1 = [];
            $position_num_2 = [];
            $position = [];

            foreach ($arrData as $k=>$v){
                if($v == $num_1){
                    $position_num_1[] = $k;
                }
                if($v == $num_2){
                    $position_num_2[] = $k;
                }
            }

            foreach ($position_num_1 as $p1){
                foreach ($position_num_2 as $p2){
                    $position[] = [$p1,$p2];
                }
            }

            foreach ($position as $key=>$value) {
                $rs[$value[0].'_'.$value[1]] = [
                    'number' => $num,
                    'count' => 0,
                    'result' => []
                ];

                for($i=1;$i<30;$i++){
                    if(isset($loto[$i])){
                        // bóc tách kết quả của ngày liền trước
                        $digit = $this->prepareDigitData($loto[$i]);

                        // lấy ra số của ngày liền trước ở vị trí này
                        $number = $digit[$value[0]].$digit[$value[1]];
                        $invertedNumber = $digit[$value[1]].$digit[$value[0]];

                        if(isset($loto[$i-1])){
                            // kiểm tra number xem có về ở ngày liền sau ko
                            $arrNumber = $this->returnArrayNumber($loto[$i-1]);
                            if(in_array($number, $arrNumber) || in_array($invertedNumber, $arrNumber)){
                                $rs[$value[0].'_'.$value[1]]['count']++;
                                $rs[$value[0].'_'.$value[1]]['result'][] = isset($data['highlight']) && (int)$data['highlight']===1?$this->_splitNumberForPray($this->_formatLotoData($loto[$i-1]), $value[0], $value[1]):$this->_formatLotoData($loto[$i-1]);
                            } else {
                                // thêm kết quả cuối cùng trước khi break
                                $rs[$value[0].'_'.$value[1]]['result'][] = isset($data['highlight']) && (int)$data['highlight']===1?$this->_splitNumberForPray($this->_formatLotoData($loto[$i-1]), $value[0], $value[1]):$this->_formatLotoData($loto[$i-1]);
                                break;
                            }
                        }
                    }
                }
            }

            if(is_array($rs)){
                array_walk($rs, function(&$item, $key){
                    $item['position'] = $key;
                });

                foreach ($rs as $k=>$v) {
                    if($v['count'] >= 3){
                        $rsAll['count'.'_'.$v['count']]['count'] = $v['count'];
                        $rsAll['count'.'_'.$v['count']]['result'][] = $v;
                    }
                }
            }

            // reset $rs
            $rs = [];
        }

        usort($rsAll, function($a, $b){
            return $a["count"] < $b["count"];
        });

        // loại bỏ kết quả của những số là số ngược của nhau
        $result = $this->removeResultDuplicate($rsAll);

        // set cached data
        Mava_Application::setCache($key_cached, $result, 24*60*60);

        return $result;
    }

    public function prayOneWayNumberResult($data){
        $key_cached = md5('loto_pray_one_way_' . 'tt' . '_' . $data['date']);

        if($result = Mava_Application::getCache($key_cached)){
            return $result;
        }

        //get 30 first records
        $loto = $this->getResultByDateProvince($data['date'], 'tt', 0, 30);

        $rsAll = [];
        $position_num_1 = [];
        $position_num_2 = [];
        $position = [];

        // bóc tách kết quả ngày gần nhất
        $arrData = $this->prepareDigitData($loto[0]);

        for($n=0;$n<=99;$n++) {
            $num = sprintf('%02d', $n);
            $num_1 = substr($num, 0, 1);
            $num_2 = substr($num, 1, 1);
            $position_num_1 = [];
            $position_num_2 = [];
            $position = [];

            foreach ($arrData as $k=>$v){
                if($v == $num_1){
                    $position_num_1[] = $k;
                }
                if($v == $num_2){
                    $position_num_2[] = $k;
                }
            }

            foreach ($position_num_1 as $p1){
                foreach ($position_num_2 as $p2){
                    $position[] = [$p1,$p2];
                }
            }

            foreach ($position as $value) {
                $rs[$value[0].'_'.$value[1]] = [
                    'number' => $num,
                    'count' => 0,
                    'result' => []
                ];

                for($i=1;$i<30;$i++){
                    if(isset($loto[$i])){
                        // bóc tách kết quả của ngày liền trước
                        $digit = $this->prepareDigitData($loto[$i]);

                        // lấy ra số của ngày liền trước ở vị trí này
                        $number = $digit[$value[0]].$digit[$value[1]];
                        if(isset($loto[$i-1])){
                          // kiểm tra number xem có về ở ngày liền sau ko
                            $arrNumber = $this->returnArrayNumber($loto[$i-1]);
                            if(in_array($number, $arrNumber)){
                                $rs[$value[0].'_'.$value[1]]['count']++;
                                $rs[$value[0].'_'.$value[1]]['result'][] = isset($data['highlight']) && (int)$data['highlight']===1?$this->_splitNumberForPray($this->_formatLotoData($loto[$i-1]), $value[0], $value[1]):$this->_formatLotoData($loto[$i-1]);
                            } else {
                                // thêm kết quả cuối cùng trước khi break
                                $rs[$value[0].'_'.$value[1]]['result'][] = isset($data['highlight']) && (int)$data['highlight']===1?$this->_splitNumberForPray($this->_formatLotoData($loto[$i-1]), $value[0], $value[1]):$this->_formatLotoData($loto[$i-1]);
                                break;
                            }  
                        }
                    }
                }
            }

            array_walk($rs, function(&$item, $key){
                $item['position'] = $key;
            });

            foreach ($rs as $k=>$v) {
                if($v['count'] >= 3){
                    $rsAll['count'.'_'.$v['count']]['count'] = $v['count'];
                    $rsAll['count'.'_'.$v['count']]['result'][] = $v;
                }
            }

            // reset $rs
            $rs = [];
        }

        usort($rsAll, function($a, $b){
            return $a["count"] < $b["count"];
        });

        // set cached data
        Mava_Application::setCache($key_cached, $rsAll, 24*60*60);

        return $rsAll;
    }

    public function prayDoubleNumberResult($data){
        $key_cached = md5('loto_pray_double_' . 'tt' . '_' . $data['date']);

        if($result = Mava_Application::getCache($key_cached)){
            return $result;
        }

        //get 30 first records
        $loto = $this->getResultByDateProvince($data['date'], 'tt', 0, 30);

        $rsAll = [];
        $position_num_1 = [];
        $position_num_2 = [];
        $position = [];

        // bóc tách kết quả ngày gần nhất
        $arrData = $this->prepareDigitData($loto[0]);

        for($n=0;$n<=99;$n++) {
            $num = sprintf('%02d', $n);
            $num_1 = substr($num, 0, 1);
            $num_2 = substr($num, 1, 1);
            $position_num_1 = [];
            $position_num_2 = [];
            $position = [];

            foreach ($arrData as $k=>$v){
                if($v == $num_1){
                    $position_num_1[] = $k;
                }
                if($v == $num_2){
                    $position_num_2[] = $k;
                }
            }

            foreach ($position_num_1 as $p1){
                foreach ($position_num_2 as $p2){
                    $position[] = [$p1,$p2];
                }
            }

            foreach ($position as $value) {
                $rs[$value[0].'_'.$value[1]] = [
                    'number' => $num,
                    'count' => 0,
                    'result' => []
                ];

                for($i=1;$i<30;$i++){
                    if(isset($loto[$i])){
                        // bóc tách kết quả của ngày liền trước
                        $digit = $this->prepareDigitData($loto[$i]);

                        // lấy ra số của ngày liền trước ở vị trí này
                        $number = $digit[$value[0]].$digit[$value[1]];
                        $invertedNumber = $digit[$value[1]].$digit[$value[0]];

                        if(isset($loto[$i-1])){
                            // kiểm tra number xem có về 2 nháy ở ngày liền sau ko
                            $arrNumber = $this->returnArrayNumber($loto[$i-1]);
                            $arrCountNumber = array_count_values($arrNumber);

                            if($number != $invertedNumber) {
                                if((in_array($number, $arrNumber) && in_array($invertedNumber, $arrNumber)) || (array_key_exists($number, $arrCountNumber) && $arrCountNumber[$number] >= 2) || (array_key_exists($invertedNumber, $arrCountNumber) && $arrCountNumber[$invertedNumber] >= 2)){
                                    $rs[$value[0].'_'.$value[1]]['count']++;
                                    $rs[$value[0].'_'.$value[1]]['result'][] = isset($data['highlight']) && (int)$data['highlight']===1?$this->_splitNumberForPray($this->_formatLotoData($loto[$i-1]), $value[0], $value[1]):$this->_formatLotoData($loto[$i-1]);
                                } else {
                                    // thêm kết quả cuối cùng trước khi break
                                    $rs[$value[0].'_'.$value[1]]['result'][] = isset($data['highlight']) && (int)$data['highlight']===1?$this->_splitNumberForPray($this->_formatLotoData($loto[$i-1]), $value[0], $value[1]):$this->_formatLotoData($loto[$i-1]);
                                    break;
                                }
                            } else {
                                if(array_key_exists($number, $arrCountNumber) && $arrCountNumber[$number] >= 2){
                                    $rs[$value[0].'_'.$value[1]]['count']++;
                                    $rs[$value[0].'_'.$value[1]]['result'][] = isset($data['highlight']) && (int)$data['highlight']===1?$this->_splitNumberForPray($this->_formatLotoData($loto[$i-1]), $value[0], $value[1]):$this->_formatLotoData($loto[$i-1]);
                                } else {
                                    // thêm kết quả cuối cùng trước khi break
                                    $rs[$value[0].'_'.$value[1]]['result'][] = isset($data['highlight']) && (int)$data['highlight']===1?$this->_splitNumberForPray($this->_formatLotoData($loto[$i-1]), $value[0], $value[1]):$this->_formatLotoData($loto[$i-1]);
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            array_walk($rs, function(&$item, $key){
                $item['position'] = $key;
            });

            foreach ($rs as $k=>$v) {
                if($v['count'] >= 2){
                    $rsAll['count'.'_'.$v['count']]['count'] = $v['count'];
                    $rsAll['count'.'_'.$v['count']]['result'][] = $v;
                }
            }

            // reset $rs
            $rs = [];
        }

        usort($rsAll, function($a, $b){
            return $a["count"] < $b["count"];
        });

        // loại bỏ kết quả của những số là số ngược của nhau
        $result = $this->removeResultDuplicate($rsAll);

        // set cached data
        Mava_Application::setCache($key_cached, $result, 24*60*60);

        return $result;
    }

    public function praySpecialNumberResult($data){
        $key_cached = md5('loto_pray_special_' . 'tt' . '_' . $data['date']);

        if($result = Mava_Application::getCache($key_cached)){
            return $result;
        }

        //get 30 first records
        $loto = $this->getResultByDateProvince($data['date'], 'tt', 0, 30);

        $rsAll = [];
        $position_num_1 = [];
        $position_num_2 = [];
        $position = [];

        // bóc tách kết quả ngày gần nhất
        $arrData = $this->prepareDigitData($loto[0]);

        for($n=0;$n<=99;$n++) {
            $num = sprintf('%02d', $n);
            $num_1 = substr($num, 0, 1);
            $num_2 = substr($num, 1, 1);
            $position_num_1 = [];
            $position_num_2 = [];
            $position = [];

            foreach ($arrData as $k=>$v){
                if($v == $num_1){
                    $position_num_1[] = $k;
                }
                if($v == $num_2){
                    $position_num_2[] = $k;
                }
            }

            foreach ($position_num_1 as $p1){
                foreach ($position_num_2 as $p2){
                    $position[] = [$p1,$p2];
                }
            }

            foreach ($position as $key=>$value) {
                $rs[$value[0].'_'.$value[1]] = [
                    'number' => $num,
                    'count' => 0,
                    'result' => []
                ];

                for($i=1;$i<30;$i++){
                    if(isset($loto[$i])){
                        // bóc tách kết quả của ngày liền trước
                        $digit = $this->prepareDigitData($loto[$i]);

                        // lấy ra số của ngày liền trước ở vị trí này
                        $number = $digit[$value[0]].$digit[$value[1]];
                        $invertedNumber = $digit[$value[1]].$digit[$value[0]];

                        if(isset($loto[$i-1]) && isset($loto[$i-1]['g0'])){
                            // kiểm tra number xem có về ở giải đặc biệt ngày liền sau ko
                            $arrNumber = (array)substr($loto[$i-1]['g0'], -2, 2);
                            if(in_array($number, $arrNumber) || in_array($invertedNumber, $arrNumber)){
                                $rs[$value[0].'_'.$value[1]]['count']++;
                                $rs[$value[0].'_'.$value[1]]['result'][] = isset($data['highlight']) && (int)$data['highlight']===1?$this->_splitNumberForPray($this->_formatLotoData($loto[$i-1]), $value[0], $value[1]):$this->_formatLotoData($loto[$i-1]);
                            } else {
                                // thêm kết quả cuối cùng trước khi break
                                $rs[$value[0].'_'.$value[1]]['result'][] = isset($data['highlight']) && (int)$data['highlight']===1?$this->_splitNumberForPray($this->_formatLotoData($loto[$i-1]), $value[0], $value[1]):$this->_formatLotoData($loto[$i-1]);
                                break;
                            }
                        }
                    }
                }
            }

            array_walk($rs, function(&$item, $key){
                $item['position'] = $key;
            });

            foreach ($rs as $k=>$v) {
                if($v['count'] >= 1){
                    $rsAll['count'.'_'.$v['count']]['count'] = $v['count'];
                    $rsAll['count'.'_'.$v['count']]['result'][] = $v;
                }
            }

            // reset $rs
            $rs = [];
        }

        usort($rsAll, function($a, $b){
            return $a["count"] < $b["count"];
        });

        // loại bỏ kết quả của những số là số ngược của nhau
        $result = $this->removeResultDuplicate($rsAll);

        // set cached data
        Mava_Application::setCache($key_cached, $result, 24*60*60);

        return $result;
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

    public function returnArrayNumber($item){
        $numbers = explode('-', preg_replace('/(\-)+/', '-', $item['g0'] . '-' . $item['g1'] . '-' . $item['g2'] . '-' . $item['g3'] . '-' . $item['g4'] . '-' . $item['g5'] . '-' . $item['g6'] . '-' . $item['g7']. '-' . $item['g8']));
        array_walk($numbers, function (&$num) {
            $num = substr($num, -2, 2);
        });
        return $numbers;
    }

    public function removeResultDuplicate($rsAll){
        $existPosition = [];
        $existNumber = [];
        foreach ($rsAll as $key => $value) {
            if(count($value['result']) > 0){
                foreach ($value['result'] as $k => $v) {
                    $position = explode('_', $v['position']);
                    $num1 = substr($v['number'], 0, 1);
                    $num2 = substr($v['number'], 1, 1);
                    if((in_array($position[0].'_'.$position[1], $existPosition) || in_array($position[1].'_'.$position[0], $existPosition)) && (in_array($num1.$num2, $existNumber) || in_array($num2.$num1, $existNumber))){
                        unset($value['result'][$k]);
                    }
                    $existPosition[] = $v['position'];
                    $existNumber[] = $v['number'];
                    $result = array_values($value['result']);
                }
            }
            $rsAll[$key] = [
                'count' => $value['count'],
                'result' => $result
            ];
        }
        return $rsAll;
    }

    public function hardyNumberResult($province){
        //get 30 first records
        $loto = $this->getResultByProvince($province, 0, 30);
        Mava_Log::info("========");
        Mava_Log::info($loto);
        $nums = [];
        for($i=0;$i<=99;$i++){
            $i = sprintf('%02d', $i);
            $nums[] = $i;
            $hardy[$i] = [
                'found' => false,
                'count' => 0,
                'end_time' => 0
            ];
        }

        foreach ($nums as $n){
            if(is_array($loto) && count($loto) && $hardy[$n]['found'] === false) {
                foreach ($loto as $item) {
                    $numbers = explode('-', preg_replace('/(\-)+/', '-', $item['g0'] . '-' . $item['g1'] . '-' . $item['g2'] . '-' . $item['g3'] . '-' . $item['g4'] . '-' . $item['g5'] . '-' . $item['g6'] . '-' . $item['g7']. '-' . $item['g8']));
                    array_walk($numbers, function (&$num) {
                        $num = substr($num, -2, 2);
                    });
                    $numbers = array_unique($numbers);

                    if (in_array($n, $numbers)) {
                        $hardy[$n]['found'] = true;
                        $hardy[$n]['end_time'] = $item['result_time'];
                        break;
                    } else {
                        $hardy[$n] = [
                            'count' => $hardy[$n]['count']+1,
                        ];
                    }
                }
            }
        }

        //check count >= 30 => get 30 next records => calc again count
        $hardy_30 = [];
        foreach ($hardy as $k=>$v) {
            if($v['count'] >= 30) {
                $hardy_30[$k] = [
                    'count' => $v['count'],
                ];
            }
        }
        if(count($hardy_30) > 0) {
            $loto_30 = $this->getResultByProvince($province, 30, 30);
            foreach ($hardy_30 as $n=>$v){
                $n = (string)$n;
                $count[$n] = $hardy_30[$n]['count'];
                if(is_array($loto_30) && count($loto_30)) {
                    foreach ($loto_30 as $item) {
                        $numbers = explode('-', preg_replace('/(\-)+/', '-', $item['g0'] . '-' . $item['g1'] . '-' . $item['g2'] . '-' . $item['g3'] . '-' . $item['g4'] . '-' . $item['g5'] . '-' . $item['g6'] . '-' . $item['g7']. '-' . $item['g8']));
                        array_walk($numbers, function (&$num) {
                            $num = substr($num, -2, 2);
                        });
                        $numbers = array_unique($numbers);

                        if (in_array($n, $numbers)) {
                            $hardy_30[$n] = [
                                'count' => $hardy_30[$n]['count'],
                                'end_time' => $item['result_time']
                            ];
                            break;
                        } else {
                            $count[$n]++;
                            $hardy_30[$n] = [
                                'count' => $count[$n]
                            ];
                        }
                    }
                }
            }
            foreach ($hardy_30 as $key => $value) {
                $hardy[$key] = $value;
            }
        }

        array_walk($hardy, function(&$item, $key){
            $item['number'] = $key;
        });

        usort($hardy, function($a, $b){
            return $a["count"] < $b["count"];
        });

        $rank = 1;
        for ($i = 0; $i < count($hardy); $i++) {
            $j = $i - 1;
            if ($j < 0) {
                $rank = 1;
            } else {
                if ($hardy[$i]['count'] != $hardy[$j]['count']) {
                    $rank++;
                }
            }
            $hardy[$i]['rank'] = $rank;
        }

        foreach ($hardy as $k=>$v){
            if($v['rank'] > 10) {
                unset($hardy[$k]);
            }
        }

        return $hardy;
    }

    public function frequencyNumberResult($data){
        $loto = $this->getResultByProvince($data['region_code'], 0, $data['limit']);
        $nums = [];
        $length = 9;
        if($data['type'] == 1) {
            $length = 99;
        }
        for($i=0;$i<=$length;$i++){
            if($data['type'] == 1) {
                $i = sprintf('%02d', $i);
            }
            $nums[] = $i;
            $freq[$i] = [
                'number_days' => 0,
                'number_times' => 0
            ];
        }
        foreach ($nums as $n){
            if(is_array($loto) && count($loto)) {
                foreach ($loto as $item) {
                    $numbers = explode('-', preg_replace('/(\-)+/', '-', $item['g0'] . '-' . $item['g1'] . '-' . $item['g2'] . '-' . $item['g3'] . '-' . $item['g4'] . '-' . $item['g5'] . '-' . $item['g6'] . '-' . $item['g7']. '-' . $item['g8']));
                    if($data['type'] == 1) {
                       array_walk($numbers, function (&$num) {
                            $num = substr($num, -2, 2);
                        });
                    }elseif($data['type'] == 2){
                        array_walk($numbers, function (&$num) {
                            $num = substr($num, -2, 1);
                        });
                    }else {
                        array_walk($numbers, function (&$num) {
                            $num = substr($num, -1, 1);
                        });
                    }
                    if (in_array($n, $numbers)) {
                        $count = 0;
                        if(array_key_exists($n, array_count_values($numbers))){
                            $count = array_count_values($numbers)[$n];
                        }
                        $freq[$n] = [
                            'number_days' => $freq[$n]['number_days'] + 1,
                            'number_times' => $freq[$n]['number_times'] + $count
                        ];
                    }
                }
            }
        }
        array_walk($freq, function(&$item, $key){
            $item['number'] = $key;
        });
        if($data['type'] == 1){
            usort($freq, function($a, $b){
                return $a["number_days"] < $b["number_days"];
            }); 
        }

        return $freq;
    }

    public function totalNumberResult($data){
        
        $loto = $this->getList($data['start_date'], $data['end_date'], $data['region_code']);
        switch ($data['sum']) {
            case 0:
                $nums = array('00','19','28','37','46','55','64','73','82','91');
                break;
            case 1:
                $nums = array('01','10','29','38','47','56','65','74','83','92');
                break;
            case 2:
                $nums = array('02','11','20','39','48','57','66','75','84','93');
                break;
            case 3:
                $nums = array('03','12','21','30','49','58','67','76','85','94');
                break;
            case 4:
                $nums = array('04','13','22','31','40','59','68','77','86','95');
                break;
            case 5:
                $nums = array('05','14','23','32','41','50','69','78','87','96');
                break;
            case 6:
                $nums = array('06','15','24','33','42','51','60','79','88','97');
                break;
            case 7:
                $nums = array('07','16','25','34','43','52','61','70','89','98');
                break;
            case 8:
                $nums = array('08','17','26','35','44','53','62','71','80','99');
                break;
            case 9:
                $nums = array('09','18','27','36','45','54','63','72','81','90');
                break;
            default:
                break;
        }

        foreach ($nums as $n) {
            $total[$n] = [
                'number_times' => 0,
                'count' => 0,
                'end_time' => 0
            ];
            $count[$n] = 0;
        }
        foreach ($nums as $n){
            if(is_array($loto) && count($loto)) {
                $i = 0;
                $flag = false;
                foreach ($loto as $item) {
                    $numbers = explode('-', preg_replace('/(\-)+/', '-', $item['g0'] . '-' . $item['g1'] . '-' . $item['g2'] . '-' . $item['g3'] . '-' . $item['g4'] . '-' . $item['g5'] . '-' . $item['g6'] . '-' . $item['g7']. '-' . $item['g8']));
                    array_walk($numbers, function (&$num) {
                        $num = substr($num, -2, 2);
                    });

                    if (in_array($n, $numbers)) {
                        $time = 0;
                        if(array_key_exists($n, array_count_values($numbers))){
                            $time = array_count_values($numbers)[$n];
                        }
                        $total[$n]['number_times'] = $total[$n]['number_times'] + $time;
                        if($i == 0){
                            $total[$n]['end_time'] = $item['result_time'];
                            $flag = true;
                        }
                        $i++;
                    } else {
                        $count[$n]++;
                        $total[$n]['number_times'] = $total[$n]['number_times'];
                        if(!$flag){
                            $total[$n]['count'] = $count[$n];
                            $total[$n]['end_time'] = $item['result_time'];
                        }
                    }
                }
            }
        }

        array_walk($total, function(&$item, $key){
            $item['number'] = $key;
        });
        usort($total, function($a, $b){
            return $a["number"] > $b["number"];
        });
        foreach ($total as $key => $value) {
            if($value['number_times'] == 0){
                unset($total[$key]);
            }
        }
        return $total;
    }

    public function timesNumberResult($data){
        $specialTimes = $this->getNumberTimes($data, 'special');
        $lotoTimes = $this->getNumberTimes($data, 'loto');
        return array(
            'special' => $specialTimes,
            'loto' => $lotoTimes
        );
    }

    public function getNumberTimes($data, $type){
        $loto = $this->getResultByProvince($data['region_code'], 0, $data['limit']);
        $nums = [];
        $length = 99;

        for($i=0;$i<=$length;$i++){
            $i = sprintf('%02d', $i);
            $nums[] = $i;
            $times[$i] = [
                'number_times' => 0
            ];
        }
        foreach ($nums as $n){
            if(is_array($loto) && count($loto)) {
                foreach ($loto as $item) {
                    if($type == 'special'){
                        $numbers = (array)$item['g0'];
                    } else {
                        $numbers = explode('-', preg_replace('/(\-)+/', '-', $item['g0'] . '-' . $item['g1'] . '-' . $item['g2'] . '-' . $item['g3'] . '-' . $item['g4'] . '-' . $item['g5'] . '-' . $item['g6'] . '-' . $item['g7']. '-' . $item['g8']));
                    }
                    array_walk($numbers, function (&$num) {
                        $num = substr($num, -2, 2);
                    });
                    if (in_array($n, $numbers)) {
                        $count = 0;
                        if(array_key_exists($n, array_count_values($numbers))){
                            $count = array_count_values($numbers)[$n];
                        }
                        $times[$n] = [
                            'number_times' => $times[$n]['number_times'] + $count
                        ];
                    }
                }
            }
        }
        array_walk($times, function(&$item, $key){
            $item['number'] = $key;
        });
        $notAppear = [];
        foreach ($times as $k=>$v){
            if($v['number_times'] == 0){
                $notAppear[$k] = $v;
                unset($times[$k]);
            }
        }

        if($data['type'] == 1){
            usort($times, function($a, $b){
                return $a["number_times"] > $b["number_times"];
            });
        }elseif ($data['type'] == 2){
            usort($times, function($a, $b){
                return $a["number_times"] < $b["number_times"];
            });
        }else{
            // not appear
            usort($notAppear, function($a, $b){
                return $a["number"] > $b["number"];
            });
            return $notAppear;
        }
        // get 10 items
        $times = array_slice($times, 0, 10);
        return $times;
    }

    public function fallenNumberResult($data){
        // get 30 records
        $loto = $this->getResultByProvince($data['region_code'], 0, 30);
        // array number of first records
        $item = $loto[0];
        unset($loto[0]);
        $curNumbers = explode('-', preg_replace('/(\-)+/', '-', $item['g0'] . '-' . $item['g1'] . '-' . $item['g2'] . '-' . $item['g3'] . '-' . $item['g4'] . '-' . $item['g5'] . '-' . $item['g6'] . '-' . $item['g7']. '-' . $item['g8']));
        array_walk($curNumbers, function (&$num) {
            $num = substr($num, -2, 2);
        });
        $curNumbers = array_unique($curNumbers);
        foreach ($curNumbers as $n) {
            if($n != ''){
                $fallen[$n] = [
                    'number_days' => 0
                ];
                if(is_array($loto) && count($loto)) {
                    foreach ($loto as $item) {
                        $numbers = explode('-', preg_replace('/(\-)+/', '-', $item['g0'] . '-' . $item['g1'] . '-' . $item['g2'] . '-' . $item['g3'] . '-' . $item['g4'] . '-' . $item['g5'] . '-' . $item['g6'] . '-' . $item['g7']. '-' . $item['g8']));
                        
                        array_walk($numbers, function (&$num) {
                            $num = substr($num, -2, 2);
                        });
                        if(in_array($n, $numbers)) {
                            $fallen[$n]['number_days'] = $fallen[$n]['number_days'] + 1;
                        } else {
                            break;
                        }
                    }
                }
                if($fallen[$n]['number_days'] == 0) {
                    unset($fallen[$n]);
                }
            }
            
        }
        array_walk($fallen, function(&$item, $key){
            $item['number'] = $key;
        });
        usort($fallen, function($a, $b){
                return $a["number_days"] < $b["number_days"];
            });
        return $fallen;
    }

    public function specialNumberResult($data){
        $nums = [];
        for($i=0;$i<=99;$i++){
            $i = sprintf('%02d', $i);
            $nums[] = $i;
            $all[$i] = [
                'count' => 0,
            ];
            $countAll[$i] = 0;
        }

        // all special
        $allSpecial = $this->getSpecialResultByProvince($data['region_code']);
        foreach ($nums as $n){
            if(is_array($allSpecial) && count($allSpecial)) {
                foreach ($allSpecial as $item) {
                    $numbers = (array)$item['g0'];
                    array_walk($numbers, function (&$num) {
                        $num = substr($num, -2, 2);
                    });
                    $numbers = array_unique($numbers);

                    if (in_array($n, $numbers)) {
                        $all[$n] = [
                            'count' => $all[$n]['count'],
                        ];
                        break;
                    } else {
                        $countAll[$n]++;
                        $all[$n] = [
                            'count' => $countAll[$n],
                        ];
                    }
                }
            }
        }
        // sum (0,1,2,3,4,5,6,7,8,9)
        $sumArr = [
            0 => array('00','19','28','37','46','55','64','73','82','91'),
            1 => array('01','10','29','38','47','56','65','74','83','92'),
            2 => array('02','11','20','39','48','57','66','75','84','93'),
            3 => array('03','12','21','30','49','58','67','76','85','94'),
            4 => array('04','13','22','31','40','59','68','77','86','95'),
            5 => array('05','14','23','32','41','50','69','78','87','96'),
            6 => array('06','15','24','33','42','51','60','79','88','97'),
            7 => array('07','16','25','34','43','52','61','70','89','98'),
            8 => array('08','17','26','35','44','53','62','71','80','99'),
            9 => array('09','18','27','36','45','54','63','72','81','90')
        ];
        $sum = [];
        foreach ($sumArr as $key => $value) {
            $min = $all[$value[0]]['count'];
            foreach ($value as $v) {
                $count = min($min,$all[$v]['count']);
                $min = $count;
            }
            $sum[] = [
                    'number' => $key,
                    'count' => $count
                ];
        }
        usort($sum, function($a, $b){
                return $a["count"] < $b["count"];
        });

        //equal (0,1,2,3,4,5,6,7,8,9)
        $equalArr = [
            0 => array('00','01','02','03','04','05','06','07','08','09','10','20','30','40','50','60','70','80','90'),
            1 => array('10','11','12','13','14','15','16','17','18','19','01','21','31','41','51','61','71','81','91'),
            2 => array('20','21','22','23','24','25','26','27','28','29','02','12','32','42','52','62','72','82','92'),
            3 => array('30','31','32','33','34','35','36','37','38','39','03','13','23','43','53','63','73','83','93'),
            4 => array('40','41','42','43','44','45','46','47','48','49','04','14','24','34','54','64','74','84','94'),
            5 => array('50','51','52','53','54','55','56','57','58','59','05','15','25','35','45','65','75','85','95'),
            6 => array('60','61','62','63','64','65','66','67','68','69','06','16','26','36','46','56','76','86','96'),
            7 => array('70','71','72','73','74','75','76','77','78','79','07','17','27','37','47','57','67','87','97'),
            8 => array('80','84','82','83','84','85','86','87','88','89','08','18','28','38','48','58','68','78','98'),
            9 => array('90','91','92','93','94','95','96','97','98','99','09','19','29','39','49','59','69','79','89')
        ];
        $equal = [];
        foreach ($equalArr as $key => $value) {
            $min = $all[$value[0]]['count'];
            foreach ($value as $v) {
                $count = min($min,$all[$v]['count']);
                $min = $count;
            }
            $equal[] = [
                    'number' => $key,
                    'count' => $count
                ];
        }
        usort($equal, function($a, $b){
                return $a["count"] < $b["count"];
        });

        // all
        array_walk($all, function(&$item, $key){
            $item['number'] = $key;
        });
        usort($all, function($a, $b){
                return $a["count"] < $b["count"];
        });
        $rank = 1;
        for ($i = 0; $i < count($all); $i++) {
            $j = $i - 1;
            if ($j < 0) {
                $rank = 1;
            } else {
                if ($all[$i]['count'] != $all[$j]['count']) {
                    $rank++;
                }
            }
            $all[$i]['rank'] = $rank;
        }

        foreach ($all as $k=>$v){
            if($v['rank'] > 30) {
                unset($all[$k]);
            }
        }

        // 30 special latest
        $loto = $this->getResultByProvince($data['region_code'], 0, 30);
        $recently = [];
        if(is_array($loto) && count($loto)) {
            foreach ($loto as $item) {
                $recently[] = [
                    'g0' => $item['g0'],
                    'day' => $item['result_time']
                ];
            }
        }

        return array(
            'sum' =>$sum,
            'equal' => $equal,
            'all'=> $all,
            'recently'=> $recently,
        );
    }

    /**
     * @return Loto_Model_Province
     * @throws Mava_Exception
     */
    protected function _getProvinceModel(){
        return $this->getModelFromCache('Loto_Model_Province');
    }

}