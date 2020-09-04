<?php
class Loto_Controller_Index extends Mava_Controller {
    public function crawlAction(){
        if(Mava_Url::getParam('auto') == 'yes'){
            Mava_Url::setParam('pv','all');
            Mava_Url::setParam('start',date('d-m-Y'));
        }
        set_time_limit(0);
        $start_run = time();
        $day = 10;
        $province_map = [];
        $all_province = get_all_province();
        $province_list = [];
        $all_code = [];
        foreach($all_province['bac'] as $item){
            if($item['map_id'] > 0){
                $province_map[$item['code']] = $item['map_id'];
                $all_code[] = $item['code'];
            }
            $province_list[$item['code']] = $item;
        }
        foreach($all_province['trung'] as $item){
            if($item['map_id'] > 0){
                $province_map[$item['code']] = $item['map_id'];
                $all_code[] = $item['code'];
            }
            $province_list[$item['code']] = $item;
        }
        foreach($all_province['nam'] as $item){
            if($item['map_id'] > 0){
                $province_map[$item['code']] = $item['map_id'];
                $all_code[] = $item['code'];
            }
            $province_list[$item['code']] = $item;
        }
        $province = Mava_Url::getParam('pv');
        if($province != ''){
            if($province == 'all'){
                $province = $all_code;
            }else{
                $province = explode(',', $province);
            }
            $start_time = max(946573200, date_to_time(Mava_Url::getParam('start'),'-'));
            $end = Mava_Url::getParam('end');
            if($end == ''){
                $end = time();
            }
            
            $end_time = min(time(),$start_time + ($day*86400));
            $db = Mava_Application::getDb();
            foreach($province as $item){
                $data = [];
                if(isset($province_map[trim($item)])){
                    for($i=$start_time;$i<$end_time;){
                        $rs = $this->_getResult($i, $province_map[trim($item)]);
                        if($rs){
                            $data[] = "('". date('d-m-Y', $i) ."','". $i ."','". $rs['g0'] ."','". $rs['g1'] ."','". $rs['g2'] ."','". $rs['g3'] ."','". $rs['g4'] ."','". $rs['g5'] ."','". $rs['g6'] ."','". $rs['g7'] ."','". $rs['g8'] ."','". $province_list[trim($item)]['code'] ."','". $province_list[trim($item)]['region'] ."')";

                            $key_cached = $province_list[trim($item)]['code']. '_' . date('d-m-Y', $i);
                            // delete cached data
                            Mava_Application::delCache($key_cached);
                            // set cached data
                            $data_cached = [
                                'result_time' => date('d-m-Y', $i),
                                'result_date' => $i,
                                'g0' => $rs['g0'],
                                'g1' => $rs['g1'],
                                'g2' => $rs['g2'],
                                'g3' => $rs['g3'],
                                'g4' => $rs['g4'],
                                'g5' => $rs['g5'],
                                'g6' => $rs['g6'],
                                'g7' => $rs['g7'],
                                'g8' => $rs['g8'],
                                'province' => $province_list[trim($item)]['code'],
                                'region' => $province_list[trim($item)]['region']
                            ];
                            $life_time = 60; //giây
                            Mava_Application::setCache($key_cached, $data_cached, $life_time);

                            // delete pray one cached data
                            Mava_Application::delCache(md5('loto_pray_one_' . 'tt' . '_' . date('d-m-Y', $i)));

                            // delete pray all cached data
                            Mava_Application::delCache(md5('loto_pray_all_' . $province_list[trim($item)]['code'] . '_' . date('d-m-Y', $i)));

                            // delete pray one way cached data
                            Mava_Application::delCache(md5('loto_pray_one_way_' . 'tt' . '_' . date('d-m-Y', $i)));

                            // delete pray double cached data
                            Mava_Application::delCache(md5('loto_pray_double_' . 'tt' . '_' . date('d-m-Y', $i)));

                            // delete pray special cached data
                            Mava_Application::delCache(md5('loto_pray_special_' . 'tt' . '_' . date('d-m-Y', $i)));
                        }
                        $i += 86400;
                    }
                    if(count($data) > 0){
                        $db->query('INSERT INTO 
                        #__loto_result(
                            `result_time`,
                            `result_date`,
                            `g0`,
                            `g1`,
                            `g2`,
                            `g3`,
                            `g4`,
                            `g5`,
                            `g6`,
                            `g7`,
                            `g8`,
                            `province`,
                            `region`
                            ) VALUES'. implode(',', $data) .' 
                            ON DUPLICATE KEY UPDATE 
                            `result_time`=VALUES(`result_time`),
                            `result_date`=VALUES(`result_date`),
                            `g0`=VALUES(`g0`),
                            `g1`=VALUES(`g1`),
                            `g2`=VALUES(`g2`),
                            `g3`=VALUES(`g3`),
                            `g4`=VALUES(`g4`),
                            `g5`=VALUES(`g5`),
                            `g6`=VALUES(`g6`),
                            `g7`=VALUES(`g7`),
                            `g8`=VALUES(`g8`),
                            `province`=VALUES(`province`),
                            `region`=VALUES(`region`)
                            ');
                    }
                }
            }
            if($end_time < $end){
                die('<h1 style="text-align: center;font-size: 60px;">Getting from '. Mava_Url::getParam('start') .'...</h1><script>window.location.href = "'. Mava_Url::getPageLink('crawl',['start' => date('d-m-Y', $end_time),'end' => $end,'pv'=> Mava_Url::getParam('pv')]) .'";</script>');
            }
    
            echo 'Done in '. (time() - $start_run) .' seconds !';
            
        }else{
            echo 'Missing [pv] params ! ex: tt,bd,bp... (ex: /crawl?start=10-01-2019&end='. time() .'&pv=all)';
        }

    }

    protected function _getResult($date, $province){
        $ch = curl_init('https://xoso360.com/do-ve-so');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'search-date='. date('d-m-Y', $date) .'&search-province='. $province);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Requested-With: XMLHttpRequest'));
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        Mava_Log::info($info,'logs/crawl/info.log');
        curl_close($ch);
        if(0 === strpos(bin2hex($result), 'efbbbf')){
            $result = substr($result, 3);
        }
        if(Mava_String::isJson($result)){
            $result = json_decode($result);
            if(isset($result->kqxs) && isset($result->kqxs->g0)){
                $data = array(
                    'date' => date('d-m-Y', $date),
                    'g0' => strip_tags($result->kqxs->g0),
                    'g1' => strip_tags($result->kqxs->g1),
                    'g2' => strip_tags($result->kqxs->g2),
                    'g3' => strip_tags($result->kqxs->g3),
                    'g4' => strip_tags($result->kqxs->g4),
                    'g5' => strip_tags($result->kqxs->g5),
                    'g6' => strip_tags($result->kqxs->g6),
                    'g7' => strip_tags($result->kqxs->g7),
                    'g8' => strip_tags($result->kqxs->g8)
                );
                return $data;
            }
        }
        return false;
    }
    
    public function resultAction(){
        $pv = Mava_Url::getParam('pv');
        $start_time = Mava_Url::getParam('from');
        if($start_time != ""){
            $start_time = date_to_time($start_time,'-');
        }
        $end_time = Mava_Url::getParam('to');
        if($end_time != ""){
            $end_time = date_to_time($end_time,'-');
        }
        $provinceModel = $this->_getProvinceModel();
        $province = $provinceModel->getByCode($pv);
        if($province){
            Mava_Application::set('seo/title','Kết quả xổ số '. $province['title'] .' từ ngày '. date('d-m-Y', $start_time) .' đến ngày '. date('d-m-Y', $end_time));
            return $this->responseView('Loto_View_Result', [
                'province' => $pv,
                'start_time' => $start_time,
                'end_time' => $end_time
            ]);    
        }else{
            return $this->responseError('Không tìm thấy dữ liệu Lô tô', Mava_Error::NOT_FOUND);
        }
    }

    public function updateResultLotoTipAction(){
        $date = Mava_Url::getParam('date');
        if(!$date) {
            $date = date('d-m-Y', time());
        }
        $result = $this->_getResultModel()->updateResultLotoTip($date);
        if($result['error'] == 0){
            dd('Success: ' . $result['result']. ' record(s) updated');
        }else {
            dd($result['result']);
        }
    }

    public function refundCoinAction(){
        $date = Mava_Url::getParam('date');
        if(!$date) {
            $date = date('d-m-Y', time());
        }
        $result = $this->_getResultModel()->refundCoinForUser($date);
        if($result['error'] == 0){
            dd('Success: ' . $result['result']. ' user(s) updated');
        }else {
            dd($result['result']);
        }
    }

    protected function _getResultModel(){
        return $this->getModelFromCache('Loto_Model_Result');
    }
    
    protected function _getProvinceModel(){
        return $this->getModelFromCache('Loto_Model_Province');
    }
}
 