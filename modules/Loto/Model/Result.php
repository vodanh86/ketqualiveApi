<?php
class Loto_Model_Result extends Mava_Model {
    
    public function getById($id){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__loto_result WHERE `id`='". $id ."'");
        }else{
            return false;
        }
    }

    /**
     * @param string $date
     * @param string $province
     * @return array|bool
     */
    public function getByDate($date, $province = 'tt'){
        return $this->_getDb()->fetchRow("SELECT * FROM #__loto_result WHERE `result_date`='". date_to_time($date,'-') ."' AND `province`='". $province ."'");
    }

    public function getList($start_time, $end_time, $province = 'tt'){
        return $this->_getDb()->fetchAll("SELECT * FROM #__loto_result WHERE `result_date` BETWEEN '". date_to_time($start_time,'-')  ."' AND '". date_to_time($end_time,'-') ."' AND `province`='". $province ."'");
    }

    public function getLatestResult($province = 'tt'){
        return $this->_getDb()->fetchRow("SELECT * FROM #__loto_result WHERE `province`='". $province ."' ORDER BY `result_date` DESC");
    }

    public function getListByProvince($province = 'tt'){
        return $this->_getDb()->fetchAll("SELECT * FROM #__loto_result WHERE `province`='". $province ."' ORDER BY `result_date` DESC LIMIT 0,10");
    }
    
    protected function _getProvinceModel(){
        return $this->getModelFromCache('Loto_Model_Province');
    }

    public function updateResultLotoTip($date){
        $loto_result = $this->_getDb()->fetchAll("SELECT * FROM #__loto_result WHERE `result_date`='". date_to_time($date,'-') ."'");
        $loto_tips = $this->_getDb()->fetchAll("SELECT * FROM #__loto_tip WHERE `tip_date` = '". $date ."'");
        $loto_result_formated = [];
        $loto_tip_formated = [];
        if ($loto_result && count($loto_result) > 0){
            foreach ($loto_result as $loto){
                $loto_result_formated[$loto['province']] = $loto;
            }
        }
        if($loto_tips && count($loto_tips) > 0){
            foreach ($loto_tips as $loto_tip){
                if( isset($loto_tip['region_code']) && isset($loto_result_formated[$loto_tip['region_code']])){
                    $loto_tip['result'] = $loto_result_formated[$loto_tip['region_code']];
                    $loto_tip_formated[] = $loto_tip;
                }
            }
        }
        $tip_correct_ids = [];
        // kiểm tra để lấy tip_id đúng
        if( count($loto_tip_formated) > 0){
            foreach ($loto_tip_formated as $tip){
                $item = $tip['result'];
                $loto_numbers = explode('-', preg_replace('/(\-)+/', '-', $item['g0'] . '-' . $item['g1'] . '-' . $item['g2'] . '-' . $item['g3'] . '-' . $item['g4'] . '-' . $item['g5'] . '-' . $item['g6'] . '-' . $item['g7']. '-' . $item['g8']));
                array_walk($loto_numbers, function (&$num) {
                    $num = substr($num, -2, 2);
                });
                $special_number = array(
                    substr($item['g0'], 0, 2),
                    substr($item['g0'], -2, 2)
                );

                $num_1 = sprintf('%02d', $tip['num_1']);
                $num_2 = sprintf('%02d', $tip['num_2']);
                $num_3 = sprintf('%02d', $tip['num_3']);

                // kiểm tra cho gói 3 : Đầu đuôi giải Đặc Biệt
                if((int)$tip['pack'] == 3){
                    if(in_array($num_1, $special_number) || in_array($num_2, $special_number) || in_array($num_3, $special_number)){
                        $tip_correct_ids[] = $tip['id'];
                    }
                }else { // kiểm tra cho gói 1 : Song thủ, bạch thủ VIP + gói 2 : Song thủ, bạch thủ Siêu VIP
                    $num_11 = substr($num_1, 0, 1);
                    $num_12 = substr($num_1, 1, 1);
                    $num_21 = substr($num_2, 0, 1);
                    $num_22 = substr($num_2, 1, 1);
                    $num_31 = substr($num_3, 0, 1);
                    $num_32 = substr($num_3, 1, 1);
                    if(
                        in_array($num_11.$num_12, $loto_numbers) || in_array($num_12.$num_11, $loto_numbers) ||
                        in_array($num_21.$num_22, $loto_numbers) || in_array($num_22.$num_21, $loto_numbers) ||
                        in_array($num_31.$num_32, $loto_numbers) || in_array($num_32.$num_31, $loto_numbers)
                    ){
                        $tip_correct_ids[] = $tip['id'];
                    }
                }
            }
        }
        // run sql update tip_id đúng
        if(count($tip_correct_ids) > 0){
            $result = $this->_getDb()->query("UPDATE #__loto_tip SET `is_correct` = 1 WHERE `id` IN (". Mava_String::doImplode($tip_correct_ids) .")");
            if($result){
                return [
                    'error' => 0,
                    'result' => count($tip_correct_ids)
                ];
            }else{
                return [
                    'error' => 1,
                    'result' => $result
                ];
            }
        }else{
            return [
                'error' => 0,
                'result' => 0
            ];
        }
    }

    public function refundCoinForUser($date){
        $logs = $this->_getDb()->fetchAll("
            SELECT
            lt_log.id,
            lt_log.token,
            lt_log.price
            FROM #__loto_tip lt_tip
            INNER JOIN (
                SELECT id, tip_id, token, price, is_refunded
                FROM #__loto_suggest_logs
                WHERE is_refunded = 0
            ) lt_log ON lt_log.`tip_id` = lt_tip.`id`
            WHERE lt_tip.`tip_date` = '". $date ."' AND lt_tip.`is_correct` = 0");

        $log_ids = [];
        $data_user_update = [];
        if($logs && count($logs) > 0){
            foreach ($logs as $log){
                $log_ids[] = $log['id'];
                if(isset($data_user_update[$log['token']])){
                    $data_user_update[$log['token']] = $data_user_update[$log['token']] + $log['price'];
                }else{
                    $data_user_update[$log['token']] = $log['price'];
                }
            }
        }
        $data_user_update_formated = [];
        if(count($data_user_update) > 0){
            foreach ($data_user_update as $key=>$value){
                $data_user_update_formated[] = "('". $key ."','". (int)$value ."')";
            }
            // hoàn coin cho user
            $update_user_result = $this->_getDb()->query('
                INSERT INTO #__user(`token`,`coin`) VALUES'. implode(',', $data_user_update_formated) .' 
                ON DUPLICATE KEY UPDATE 
                `token`=VALUES(`token`),
                `coin`=`coin` + VALUES(`coin`)
            ');

            if($update_user_result){
                if(count($log_ids) > 0){ // update log thành đã xử lý hoàn coin
                    $update_log_result = $this->_getDb()->query("UPDATE #__loto_suggest_logs SET `is_refunded` = 1 WHERE `id` IN (". Mava_String::doImplode($log_ids) .")");
                    if($update_log_result){
                        return [
                            'error' => 0,
                            'result' => count($data_user_update)
                        ];
                    }else{
                        return [
                            'error' => 1,
                            'result' => $update_log_result
                        ];
                    }
                }
            }else{
                return [
                    'error' => 1,
                    'result' => $update_user_result
                ];
            }
        }else{
            return [
                'error' => 0,
                'result' => 0
            ];
        }
    }

    /**
     * @param string $date
     * @param string $province
     * @return string
     */
    public function getResultHtml($start_time, $end_time, $province, $silent = false){
        $provinceModel = $this->_getProvinceModel();
        $provinceObj = $provinceModel->getByCode($province);
        if(!$provinceObj){
            return $silent==false?'<div class="alert alert-danger">Không tìm thấy kết quả phù hợp</div>':'';
        }
        $html = '';
        $default_number = '--'; // cho giải không có dữ liệu
        if($start_time && $end_time) {
            $results = $this->getList($start_time, $end_time, $province);
        }else{
            $results = $this->getListByProvince($province);
        }
        if($results && count($results) > 0){
            foreach ($results as $result){
                $data = [];
                if($result){
                    $data = [
                        'g0' => $result['g0'],
                        'g1' => $result['g1'],
                        'g2' => $result['g2']!=""?explode('-', $result['g2']):[],
                        'g3' => $result['g3']!=""?explode('-', $result['g3']):[],
                        'g4' => $result['g4']!=""?explode('-', $result['g4']):[],
                        'g5' => $result['g5']!=""?explode('-', $result['g5']):[],
                        'g6' => $result['g6']!=""?explode('-', $result['g6']):[],
                        'g7' => $result['g7']!=""?explode('-', $result['g7']):[],
                        'g8' => $result['g8']!=""?explode('-', $result['g8']):[],
                    ];
                }else{
                    return $silent==false?'<div class="alert alert-danger">Không tìm thấy kết quả phù hợp</div>':'';
                }

                $first = [0 => [], 1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => [], 7 => [], 8 => [], 9 => []];
                $last = [0 => [], 1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => [], 7 => [], 8 => [], 9 => []];
                foreach($data as $item){
                    if(!is_array($item)){
                        $item = array($item);
                    }
                    foreach($item as $number){
                        // đầu
                        $lastTwoDigit = substr($number,-2,2);
                        $beforeLastDigit = substr($number,-2,1);
                        $lastDigit = substr($number,-1,1);
                        if(isset($first[$beforeLastDigit]) && isset($first[$beforeLastDigit][$lastTwoDigit])){
                            $first[$beforeLastDigit][$lastTwoDigit]++;
                        }else{
                            $first[$beforeLastDigit][$lastTwoDigit] = 1;
                        }
                        // đuôi
                        if(isset($last[$lastDigit]) && isset($last[$lastDigit][$lastTwoDigit])){
                            $last[$lastDigit][$lastTwoDigit]++;
                        }else{
                            $last[$lastDigit][$lastTwoDigit] =1;
                        }
                    }
                }
                switch($province){
                    /************************** TRUYEN THONG **************************/
                    case 'tt':
                        $html .= '
                            <div class="xs-result-table">
                                <div class="xs-result-head">
                                    <div class="xs-result-head-title">
                                        <h3>KẾT QUẢ XỔ SỐ '. $provinceObj['title'] .' NGÀY '. $result['result_time'] .'</h3>
                                    </div>
                                </div>
                                <table class="table table-bordered">
                                    <tr>
                                        <td class="xs-rl-spec" width="120">Đặc biệt</td>
                                        <td class="xs-rn-spec" colspan="12">'. (isset($data['g0'])?$data['g0']:$default_number) .'</td>
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
                            
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30" class="bg-orange white">Đầu</th>
                                            <th class="bg-orange white">Loto</th>
                                        </tr>';
                                $index = 0;
                                foreach($first as $key => $val){
                                    $html .= '<tr><td class="red bold text-center">'. $index .'</td><td class="bold">';
                                    if(count($first[$index]) > 0){
                                        $count = 0;
                                        foreach($first[$index] as $k => $v){
                                            $count++;
                                            if($count > 1){
                                                $html .= ', ';
                                            }
                                            $html .= $k;
                                            if($v > 1){
                                                $html .= '<small class="red bold">('. $v .')</small>';
                                            }
                                        }
                                    }
                                    $index++;
                                    $html .= '</td>
                                                </tr>';
                                }

                                $html .= '</table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30" class="bg-orange white">Đuôi</th>
                                            <th class="bg-orange white">Loto</th>
                                        </tr>';
                                $index = 0;
                                foreach($last as $key => $val){
                                    $html .= '<tr><td class="red bold text-center">'. $index .'</td><td class="bold">';
                                    if(count($last[$index]) > 0){
                                        $count = 0;
                                        foreach($last[$index] as $k => $v){
                                            $count++;
                                            if($count > 1){
                                                $html .= ', ';
                                            }
                                            $html .= $k;
                                            if($v > 1){
                                                $html .= '<small class="red bold">('. $v .')</small>';
                                            }
                                        }
                                    }
                                    $index++;
                                    $html .= '</td>
                                                </tr>';
                                }

                                $html .= '
                                    </table>
                                </div>
                            </div>';
                        break;
                    /************************** OTHER *********************************/
                    default:
                        $html .= '
                            <div class="xs-result-table">
                                <div class="xs-result-head">
                                    <div class="xs-result-head-title">
                                        <h3>KẾT QUẢ XỔ SỐ '. $provinceObj['title'] .' NGÀY '. $result['result_time'] .'</h3>
                                    </div>
                                </div>
                                <table class="table table-bordered">
                                    <tr>
                                        <td class="xs-rl-spec" width="120">Đặc biệt</td>
                                        <td class="xs-rn-spec" colspan="12">'. (isset($data['g0'])?$data['g0']:$default_number) .'</td>
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
                            
                            </div>
                
                            <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30" class="bg-orange white">Đầu</th>
                                        <th class="bg-orange white">Loto</th>
                                    </tr>';
                                $index = 0;
                                foreach($first as $key => $val){
                                    $html .= '<tr><td class="red bold text-center">'. $index .'</td><td class="bold">';
                                    if(count($first[$index]) > 0){
                                        $count = 0;
                                        foreach($first[$index] as $k => $v){
                                            $count++;
                                            if($count > 1){
                                                $html .= ', ';
                                            }
                                            $html .= $k;
                                            if($v > 1){
                                                $html .= '<small class="red bold">('. $v .')</small>';
                                            }
                                        }
                                    }
                                    $index++;
                                    $html .= '</td>
                                            </tr>';
                                }

                                $html .= '</table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30" class="bg-orange white">Đuôi</th>
                                        <th class="bg-orange white">Loto</th>
                                    </tr>';
                                $index = 0;
                                foreach($last as $key => $val){
                                    $html .= '<tr><td class="red bold text-center">'. $index .'</td><td class="bold">';
                                    if(count($last[$index]) > 0){
                                        $count = 0;
                                        foreach($last[$index] as $k => $v){
                                            $count++;
                                            if($count > 1){
                                                $html .= ', ';
                                            }
                                            $html .= $k;
                                            if($v > 1){
                                                $html .= '<small class="red bold">('. $v .')</small>';
                                            }
                                        }
                                    }
                                    $index++;
                                    $html .= '</td>
                                            </tr>';
                                }

                                $html .= '
                                </table>
                            </div>
                        </div>';
                        break;
                }
            }
        }

        return $html;
    }

    public function getResultHomeHtml($date, $province, $silent = false){
        $provinceModel = $this->_getProvinceModel();
        $provinceObj = $provinceModel->getByCode($province);
        if(!$provinceObj){
            return $silent==false?'<div class="alert alert-danger">Không tìm thấy kết quả phù hợp</div>':'';
        }
        $html = '';
        $default_number = '--'; // cho giải không có dữ liệu

        // get data from cache
        $result = Mava_Application::getCache($province. '_' . $date);
        if(!$result) { // nếu không có cached data thì query lấy trong DB
            $result = $this->getByDate($date, $province);
        }
        $data = [];
        if($result){
            $data = [
                'g0' => $result['g0'],
                'g1' => $result['g1'],
                'g2' => $result['g2']!=""?explode('-', $result['g2']):[],
                'g3' => $result['g3']!=""?explode('-', $result['g3']):[],
                'g4' => $result['g4']!=""?explode('-', $result['g4']):[],
                'g5' => $result['g5']!=""?explode('-', $result['g5']):[],
                'g6' => $result['g6']!=""?explode('-', $result['g6']):[],
                'g7' => $result['g7']!=""?explode('-', $result['g7']):[],
                'g8' => $result['g8']!=""?explode('-', $result['g8']):[],
            ];
        }else{
            return $silent==false?'<div class="alert alert-danger">Không tìm thấy kết quả phù hợp</div>':'';
        }

        $first = [0 => [], 1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => [], 7 => [], 8 => [], 9 => []];
        $last = [0 => [], 1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => [], 7 => [], 8 => [], 9 => []];
        foreach($data as $item){
            if(!is_array($item)){
                $item = array($item);
            }
            foreach($item as $number){
                // đầu
                $lastTwoDigit = substr($number,-2,2);
                $beforeLastDigit = substr($number,-2,1);
                $lastDigit = substr($number,-1,1);
                if(isset($first[$beforeLastDigit]) && isset($first[$beforeLastDigit][$lastTwoDigit])){
                    $first[$beforeLastDigit][$lastTwoDigit]++;
                }else{
                    $first[$beforeLastDigit][$lastTwoDigit] = 1;
                }
                // đuôi
                if(isset($last[$lastDigit]) && isset($last[$lastDigit][$lastTwoDigit])){
                    $last[$lastDigit][$lastTwoDigit]++;
                }else{
                    $last[$lastDigit][$lastTwoDigit] =1;
                }
            }
        }
        switch($province){
            /************************** TRUYEN THONG **************************/
            case 'tt':
                $html .= '
                    <div class="xs-result-table">
                        <div class="xs-result-head">
                            <div class="xs-result-head-title">
                                <h3>KẾT QUẢ XỔ SỐ '. $provinceObj['title'] .' NGÀY '. $result['result_time'] .'</h3>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <tr>
                                <td class="xs-rl-spec" width="120">Đặc biệt</td>
                                <td class="xs-rn-spec" colspan="12">'. (isset($data['g0'])?$data['g0']:$default_number) .'</td>
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
                    
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30" class="bg-orange white">Đầu</th>
                                    <th class="bg-orange white">Loto</th>
                                </tr>';
                $index = 0;
                foreach($first as $key => $val){
                    $html .= '<tr><td class="red bold text-center">'. $index .'</td><td class="bold">';
                    if(count($first[$index]) > 0){
                        $count = 0;
                        foreach($first[$index] as $k => $v){
                            $count++;
                            if($count > 1){
                                $html .= ', ';
                            }
                            $html .= $k;
                            if($v > 1){
                                $html .= '<small class="red bold">('. $v .')</small>';
                            }
                        }
                    }
                    $index++;
                    $html .= '</td>
                                        </tr>';
                }

                $html .= '</table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30" class="bg-orange white">Đuôi</th>
                                    <th class="bg-orange white">Loto</th>
                                </tr>';
                $index = 0;
                foreach($last as $key => $val){
                    $html .= '<tr><td class="red bold text-center">'. $index .'</td><td class="bold">';
                    if(count($last[$index]) > 0){
                        $count = 0;
                        foreach($last[$index] as $k => $v){
                            $count++;
                            if($count > 1){
                                $html .= ', ';
                            }
                            $html .= $k;
                            if($v > 1){
                                $html .= '<small class="red bold">('. $v .')</small>';
                            }
                        }
                    }
                    $index++;
                    $html .= '</td>
                                        </tr>';
                }

                $html .= '
                            </table>
                        </div>
                    </div>';
                break;
            /************************** OTHER *********************************/
            default:
                $html .= '
                    <div class="xs-result-table">
                        <div class="xs-result-head">
                            <div class="xs-result-head-title">
                                <h3>KẾT QUẢ XỔ SỐ '. $provinceObj['title'] .' NGÀY '. $result['result_time'] .'</h3>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <tr>
                                <td class="xs-rl-spec" width="120">Đặc biệt</td>
                                <td class="xs-rn-spec" colspan="12">'. (isset($data['g0'])?$data['g0']:$default_number) .'</td>
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
                    
                    </div>
        
                    <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30" class="bg-orange white">Đầu</th>
                                <th class="bg-orange white">Loto</th>
                            </tr>';
                $index = 0;
                foreach($first as $key => $val){
                    $html .= '<tr><td class="red bold text-center">'. $index .'</td><td class="bold">';
                    if(count($first[$index]) > 0){
                        $count = 0;
                        foreach($first[$index] as $k => $v){
                            $count++;
                            if($count > 1){
                                $html .= ', ';
                            }
                            $html .= $k;
                            if($v > 1){
                                $html .= '<small class="red bold">('. $v .')</small>';
                            }
                        }
                    }
                    $index++;
                    $html .= '</td>
                                    </tr>';
                }

                $html .= '</table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30" class="bg-orange white">Đuôi</th>
                                <th class="bg-orange white">Loto</th>
                            </tr>';
                $index = 0;
                foreach($last as $key => $val){
                    $html .= '<tr><td class="red bold text-center">'. $index .'</td><td class="bold">';
                    if(count($last[$index]) > 0){
                        $count = 0;
                        foreach($last[$index] as $k => $v){
                            $count++;
                            if($count > 1){
                                $html .= ', ';
                            }
                            $html .= $k;
                            if($v > 1){
                                $html .= '<small class="red bold">('. $v .')</small>';
                            }
                        }
                    }
                    $index++;
                    $html .= '</td>
                                    </tr>';
                }

                $html .= '
                        </table>
                    </div>
                </div>';
                break;
        }

        return $html;
    }

    public function getDoVeSoResultHtml($date, $province, $goal){
        $provinceModel = $this->_getProvinceModel();
        $provinceObj = $provinceModel->getByCode($province);
        if(!$provinceObj){
            return '<div class="alert alert-danger">Không tìm thấy kết quả phù hợp</div>';
        }
        $html = '';
        $default_number = '--'; // cho giải không có dữ liệu
        $result = $this->getByDate($date, $province);
        $data = [];
        $appear = [];
        if($result){
            $data = [
                'g0' => $result['g0'],
                'g1' => $result['g1'],
                'g2' => $result['g2']!=""?explode('-', $result['g2']):[],
                'g3' => $result['g3']!=""?explode('-', $result['g3']):[],
                'g4' => $result['g4']!=""?explode('-', $result['g4']):[],
                'g5' => $result['g5']!=""?explode('-', $result['g5']):[],
                'g6' => $result['g6']!=""?explode('-', $result['g6']):[],
                'g7' => $result['g7']!=""?explode('-', $result['g7']):[],
                'g8' => $result['g8']!=""?explode('-', $result['g8']):[],
            ];
            if($goal != '') {
                $length = strlen($goal);
                $arrNumbers = explode('-', preg_replace('/(\-)+/', '-', $result['g0'] . '-' . $result['g1'] . '-' . $result['g2'] . '-' . $result['g3'] . '-' . $result['g4'] . '-' . $result['g5'] . '-' . $result['g6'] . '-' . $result['g7']. '-' . $result['g8']));
                $nums = [];
                foreach ($arrNumbers as $n) {
                    $nums[] = substr($n, -$length, $length);
                }
                $position = array_keys($nums, $goal);
                $count = count($position);
                if($count > 0) {
                    foreach ($position as $p) {
                        $appear[] = $arrNumbers[$p];
                    }
                }
                $html .= '<div class="mb-2 bold">Số <span class="red bold">'. $goal .'</span> về <span class="red bold">'. $count .'</span> lần trong ngày <span class="red bold">'. $date .'</span></div>';
            }
        }else{
            return '<div class="alert alert-danger">Không tìm thấy kết quả phù hợp</div>';
        }

        $first = [0 => [], 1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => [], 7 => [], 8 => [], 9 => []];
        $last = [0 => [], 1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => [], 7 => [], 8 => [], 9 => []];
        foreach($data as $item){
            if(!is_array($item)){
                $item = array($item);
            }
            foreach($item as $number){
                // đầu
                $lastTwoDigit = substr($number,-2,2);
                $beforeLastDigit = substr($number,-2,1);
                $lastDigit = substr($number,-1,1);
                if(isset($first[$beforeLastDigit]) && isset($first[$beforeLastDigit][$lastTwoDigit])){
                    $first[$beforeLastDigit][$lastTwoDigit]++;
                }else{
                    $first[$beforeLastDigit][$lastTwoDigit] = 1;
                }
                // đuôi
                if(isset($last[$lastDigit]) && isset($last[$lastDigit][$lastTwoDigit])){
                    $last[$lastDigit][$lastTwoDigit]++;
                }else{
                    $last[$lastDigit][$lastTwoDigit] =1;
                }
            }
        }
        switch($province){
            /************************** TRUYEN THONG **************************/
            case 'tt':
                $html .= '
                    <div class="xs-result-table">
                        <div class="xs-result-head">
                            <div class="xs-result-head-title">
                                <h3>KẾT QUẢ XỔ SỐ '. $provinceObj['title'] .' NGÀY '. $date .'</h3>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <tr>
                                <td class="xs-rl-spec" width="120">Đặc biệt</td>
                                <td class="xs-rn-spec" colspan="12">'. (isset($data['g0'])?(in_array($data['g0'], $appear) ? '<span>'. str_replace($goal, '', $data['g0']) .'</span><large class="font-italic">'. $goal .'</large>' : $data['g0']):$default_number) .'</td>
                            </tr>
                            <tr>
                                <td class="xs-rl-normal">Giải nhất</td>
                                <td class="xs-rn-normal" colspan="12">'. (isset($data['g1'])?(in_array($data['g1'], $appear) ? '<span>'. str_replace($goal, '', $data['g1']) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g1']):$default_number) .'</td>
                            </tr>
                            <tr>
                                <td class="xs-rl-normal">Giải nhì</td>
                                <td class="xs-rn-normal" colspan="6">'. (isset($data['g2']) && isset($data['g2'][0])?(in_array($data['g2'][0], $appear) ? '<span>'. str_replace($goal, '', $data['g2'][0]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g2'][0]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="6">'. (isset($data['g2']) && isset($data['g2'][1])?(in_array($data['g2'][1], $appear) ? '<span>'. str_replace($goal, '', $data['g2'][1]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g2'][1]):$default_number) .'</td>
                            </tr>
                            <tr>
                                <td class="xs-rl-normal" rowspan="2">Giải ba</td>
                                <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][0])?(in_array($data['g3'][0], $appear) ? '<span>'. str_replace($goal, '', $data['g3'][0]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g3'][0]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][1])?(in_array($data['g3'][1], $appear) ? '<span>'. str_replace($goal, '', $data['g3'][1]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g3'][1]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][2])?(in_array($data['g3'][2], $appear) ? '<span>'. str_replace($goal, '', $data['g3'][2]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g3'][2]):$default_number) .'</td>
                            </tr>
                            <tr>
                                <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][3])?(in_array($data['g3'][3], $appear) ? '<span>'. str_replace($goal, '', $data['g3'][3]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g3'][3]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][4])?(in_array($data['g3'][4], $appear) ? '<span>'. str_replace($goal, '', $data['g3'][4]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g3'][4]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="4">'. (isset($data['g3']) && isset($data['g3'][5])?(in_array($data['g3'][5], $appear) ? '<span>'. str_replace($goal, '', $data['g3'][5]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g3'][5]):$default_number) .'</td>
                            </tr>
                            <tr>
                                <td class="xs-rl-normal">Giải tư</td>
                                <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][0])?(in_array($data['g4'][0], $appear) ? '<span>'. str_replace($goal, '', $data['g4'][0]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g4'][0]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][1])?(in_array($data['g4'][1], $appear) ? '<span>'. str_replace($goal, '', $data['g4'][1]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g4'][1]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][2])?(in_array($data['g4'][2], $appear) ? '<span>'. str_replace($goal, '', $data['g4'][2]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g4'][2]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][3])?(in_array($data['g4'][3], $appear) ? '<span>'. str_replace($goal, '', $data['g4'][3]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g4'][3]):$default_number) .'</td>
                            </tr>
                    
                            <tr>
                                <td class="xs-rl-normal" rowspan="2">Giải năm</td>
                                <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][0])?(in_array($data['g5'][0], $appear) ? '<span>'. str_replace($goal, '', $data['g5'][0]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g5'][0]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][1])?(in_array($data['g5'][1], $appear) ? '<span>'. str_replace($goal, '', $data['g5'][1]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g5'][1]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][2])?(in_array($data['g5'][2], $appear) ? '<span>'. str_replace($goal, '', $data['g5'][2]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g5'][2]):$default_number) .'</td>
                            </tr>
                            <tr>
                                <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][3])?(in_array($data['g5'][3], $appear) ? '<span>'. str_replace($goal, '', $data['g5'][3]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g5'][3]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][4])?(in_array($data['g5'][4], $appear) ? '<span>'. str_replace($goal, '', $data['g5'][4]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g5'][4]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g5'][5])?(in_array($data['g5'][5], $appear) ? '<span>'. str_replace($goal, '', $data['g5'][5]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g5'][5]):$default_number) .'</td>
                            </tr>
                            <tr>
                                <td class="xs-rl-normal">Giải sáu</td>
                                <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g6'][0])?(in_array($data['g6'][0], $appear) ? '<span>'. str_replace($goal, '', $data['g6'][0]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g6'][0]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g6'][1])?(in_array($data['g6'][1], $appear) ? '<span>'. str_replace($goal, '', $data['g6'][1]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g6'][1]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g6'][2])?(in_array($data['g6'][2], $appear) ? '<span>'. str_replace($goal, '', $data['g6'][2]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g6'][2]):$default_number) .'</td>
                            </tr>
                            <tr>
                                <td class="xs-rl-normal">Giải bảy</td>
                                <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][0])?(in_array($data['g7'][0], $appear) ? '<span>'. str_replace($goal, '', $data['g7'][0]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g7'][0]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][1])?(in_array($data['g7'][1], $appear) ? '<span>'. str_replace($goal, '', $data['g7'][1]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g7'][1]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][2])?(in_array($data['g7'][2], $appear) ? '<span>'. str_replace($goal, '', $data['g7'][2]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g7'][2]):$default_number) .'</td>
                                <td class="xs-rn-normal" colspan="3">'. (isset($data['g7']) && isset($data['g7'][3])?(in_array($data['g7'][3], $appear) ? '<span>'. str_replace($goal, '', $data['g7'][3]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g7'][3]):$default_number) .'</td>
                            </tr>
                        </table>
                    
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30" class="bg-orange white">Đầu</th>
                                    <th class="bg-orange white">Loto</th>
                                </tr>';
                $index = 0;
                foreach($first as $key => $val){
                    $html .= '<tr><td class="red bold text-center">'. $index .'</td><td class="bold">';
                    if(count($first[$index]) > 0){
                        $count = 0;
                        foreach($first[$index] as $k => $v){
                            $count++;
                            if($count > 1){
                                $html .= ', ';
                            }
                            $html .= $k;
                            if($v > 1){
                                $html .= '<small class="red bold">('. $v .')</small>';
                            }
                        }
                    }
                    $index++;
                    $html .= '</td>
                                        </tr>';
                }

                $html .= '</table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30" class="bg-orange white">Đuôi</th>
                                    <th class="bg-orange white">Loto</th>
                                </tr>';
                $index = 0;
                foreach($last as $key => $val){
                    $html .= '<tr><td class="red bold text-center">'. $index .'</td><td class="bold">';
                    if(count($last[$index]) > 0){
                        $count = 0;
                        foreach($last[$index] as $k => $v){
                            $count++;
                            if($count > 1){
                                $html .= ', ';
                            }
                            $html .= $k;
                            if($v > 1){
                                $html .= '<small class="red bold">('. $v .')</small>';
                            }
                        }
                    }
                    $index++;
                    $html .= '</td>
                                        </tr>';
                }

                $html .= '
                            </table>
                        </div>
                    </div>';
                break;
            /************************** OTHER *********************************/
            default:
                $html .= '
                <div class="xs-result-table">
                    <div class="xs-result-head">
                        <div class="xs-result-head-title">
                            <h3>KẾT QUẢ XỔ SỐ '. $provinceObj['title'] .' NGÀY '. $date .'</h3>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <td class="xs-rl-spec" width="120">Đặc biệt</td>
                            <td class="xs-rn-spec" colspan="12">'. (isset($data['g0'])?(in_array($data['g0'], $appear) ? '<span>'. str_replace($goal, '', $data['g0']) .'</span><large class="font-italic">'. $goal .'</large>' : $data['g0']):$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải nhất</td>
                            <td class="xs-rn-normal" colspan="12">'. (isset($data['g1'])?(in_array($data['g1'], $appear) ? '<span>'. str_replace($goal, '', $data['g1']) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g1']):$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải nhì</td>
                            <td class="xs-rn-normal" colspan="12">'. (isset($data['g2']) && isset($data['g2'][0])?(in_array($data['g2'][0], $appear) ? '<span>'. str_replace($goal, '', $data['g2'][0]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g2'][0]):$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải ba</td>
                            <td class="xs-rn-normal" colspan="6">'. (isset($data['g3']) && isset($data['g3'][0])?(in_array($data['g3'][0], $appear) ? '<span>'. str_replace($goal, '', $data['g3'][0]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g3'][0]):$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="6">'. (isset($data['g3']) && isset($data['g3'][1])?(in_array($data['g3'][1], $appear) ? '<span>'. str_replace($goal, '', $data['g3'][1]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g3'][1]):$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal" rowspan="2">Giải tư</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][0])?(in_array($data['g4'][0], $appear) ? '<span>'. str_replace($goal, '', $data['g4'][0]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g4'][0]):$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][1])?(in_array($data['g4'][1], $appear) ? '<span>'. str_replace($goal, '', $data['g4'][1]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g4'][1]):$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][2])?(in_array($data['g4'][2], $appear) ? '<span>'. str_replace($goal, '', $data['g4'][2]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g4'][2]):$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="3">'. (isset($data['g4']) && isset($data['g4'][3])?(in_array($data['g4'][3], $appear) ? '<span>'. str_replace($goal, '', $data['g4'][3]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g4'][3]):$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g4']) && isset($data['g4'][4])?(in_array($data['g4'][4], $appear) ? '<span>'. str_replace($goal, '', $data['g4'][4]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g4'][4]):$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g4']) && isset($data['g4'][5])?(in_array($data['g4'][5], $appear) ? '<span>'. str_replace($goal, '', $data['g4'][5]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g4'][5]):$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g4']) && isset($data['g4'][6])?(in_array($data['g4'][6], $appear) ? '<span>'. str_replace($goal, '', $data['g4'][6]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g4'][6]):$default_number) .'</td>
                        </tr>
                
                        <tr>
                            <td class="xs-rl-normal">Giải năm</td>
                            <td class="xs-rn-normal" colspan="12">'. (isset($data['g5']) && isset($data['g5'][0])?(in_array($data['g5'][0], $appear) ? '<span>'. str_replace($goal, '', $data['g5'][0]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g5'][0]):$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải sáu</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g6'][0])?(in_array($data['g6'][0], $appear) ? '<span>'. str_replace($goal, '', $data['g6'][0]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g6'][0]):$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g6'][1])?(in_array($data['g6'][1], $appear) ? '<span>'. str_replace($goal, '', $data['g6'][1]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g6'][1]):$default_number) .'</td>
                            <td class="xs-rn-normal" colspan="4">'. (isset($data['g5']) && isset($data['g6'][2])?(in_array($data['g6'][2], $appear) ? '<span>'. str_replace($goal, '', $data['g6'][2]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g6'][2]):$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải bảy</td>
                            <td class="xs-rn-normal" colspan="12">'. (isset($data['g7']) && isset($data['g7'][0])?(in_array($data['g7'][0], $appear) ? '<span>'. str_replace($goal, '', $data['g7'][0]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g7'][0]):$default_number) .'</td>
                        </tr>
                        <tr>
                            <td class="xs-rl-normal">Giải tám</td>
                            <td class="xs-rn-normal" colspan="12">'. (isset($data['g8']) && isset($data['g8'][0])?(in_array($data['g8'][0], $appear) ? '<span>'. str_replace($goal, '', $data['g8'][0]) .'</span><large class="red bold font-italic">'. $goal .'</large>' : $data['g8'][0]):$default_number) .'</td>
                        </tr>
                    </table>
                
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30" class="bg-orange white">Đầu</th>
                                <th class="bg-orange white">Loto</th>
                            </tr>';
                $index = 0;
                foreach($first as $key => $val){
                    $html .= '<tr><td class="red bold text-center">'. $index .'</td><td class="bold">';
                    if(count($first[$index]) > 0){
                        $count = 0;
                        foreach($first[$index] as $k => $v){
                            $count++;
                            if($count > 1){
                                $html .= ', ';
                            }
                            $html .= $k;
                            if($v > 1){
                                $html .= '<small class="red bold">('. $v .')</small>';
                            }
                        }
                    }
                    $index++;
                    $html .= '</td>
                                    </tr>';
                }

                $html .= '</table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30" class="bg-orange white">Đuôi</th>
                                <th class="bg-orange white">Loto</th>
                            </tr>';
                $index = 0;
                foreach($last as $key => $val){
                    $html .= '<tr><td class="red bold text-center">'. $index .'</td><td class="bold">';
                    if(count($last[$index]) > 0){
                        $count = 0;
                        foreach($last[$index] as $k => $v){
                            $count++;
                            if($count > 1){
                                $html .= ', ';
                            }
                            $html .= $k;
                            if($v > 1){
                                $html .= '<small class="red bold">('. $v .')</small>';
                            }
                        }
                    }
                    $index++;
                    $html .= '</td>
                                    </tr>';
                }

                $html .= '
                        </table>
                    </div>
                </div>';
                break;
        }

        return $html;
    }
}