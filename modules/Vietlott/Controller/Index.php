<?php
class Vietlott_Controller_Index extends Mava_Controller {

    public function crawlMegaAction(){
        $db = Mava_Application::getDb();
        $start_run = time();
        $jump = 10;
        $maxId = $this->_getMaxIdMega();
        $start_id = max(1,(int)Mava_Url::getParam('start'));
        $end = (int)Mava_Url::getParam('end');
        if($end == ''){
            $end = $maxId;
        }
        $maxIdResult = $this->_getMegaModel()->getMaxId();
        if($maxIdResult >= $maxId) {
            $start_id = $maxId;
        } else {
            $start_id = $maxIdResult + 1;
        }
        $end_id = min($maxId,$start_id + $jump);
        $data = [];
        for($i=$start_id;$i<=$end_id;){
            $rs = $this->_getMegaResult($i);
            if($rs){
                $data[] = "('". date_to_time($rs['date'],'-') ."','". $rs['date'] ."','". $rs['code'] ."','". $rs['jackpot'] ."','". $rs['num_1'] ."','". $rs['num_2'] ."','". $rs['num_3'] ."','". $rs['num_4'] ."','". $rs['num_5'] ."','". $rs['num_6'] ."')";
            }
            $i++;
        }
        if(count($data) > 0){
            $db->query('INSERT INTO 
            #__vietlott_mega_result(
                `result_date`,
                `result_time`,
                `code`,
                `jackpot`,
                `num_1`,
                `num_2`,
                `num_3`,
                `num_4`,
                `num_5`,
                `num_6`
                ) VALUES'. implode(',', $data) .' 
                ON DUPLICATE KEY UPDATE 
                `result_time`=VALUES(`result_time`),
                `result_date`=VALUES(`result_date`),
                `code`=VALUES(`code`),
                `jackpot`=VALUES(`jackpot`),
                `num_1`=VALUES(`num_1`),
                `num_2`=VALUES(`num_2`),
                `num_3`=VALUES(`num_3`),
                `num_4`=VALUES(`num_4`),
                `num_5`=VALUES(`num_5`),
                `num_6`=VALUES(`num_6`)
                ');
        }
        if($end_id < $end){
            die('<h1 style="text-align: center;font-size: 60px;">Getting from '. Mava_Url::getParam('start') .'...</h1><script>window.location.href = "'. Mava_Url::getPageLink('vietlott-mega-crawl',['start' => $end_id,'end' => $end]) .'";</script>');
        }
        echo 'Done in '. (time() - $start_run) .' seconds !';
    }

    public function _getMegaResult($id){
        $id = sprintf('%05d', $id);
        $url = 'https://vietlott.vn/vi/trung-thuong/ket-qua-trung-thuong/645?id='.$id.'&nocatche=1';
        $html = file_get_html($url);
        if($html){
            $data = [];
            $check = $html->find('.chitietketqua_title', 0);
            if($check){
                //date
                $date = $html->find('.chitietketqua_title', 0)->find('h5', 0)->find('b',1)->innertext;
                $data['date'] = str_replace("/", "-", $date);
                //code
                $data['code'] = $html->find('.chitietketqua_title', 0)->find('h5', 0)->find('b',0)->innertext;
                //jackpot
                $data['jackpot'] = $html->find('.so_tien',0)->find('h3', 0)->innertext;
                //num 1-6
                $j = 1;
                foreach ($html->find('.bong_tron') as $element) {
                    $data['num_'.$j] = $element->innertext;
                    $j++;
                }
                return $data;
            }
        }
        return false;
    }

    public function _getMaxIdMega(){
        $url = 'https://vietlott.vn/vi/trung-thuong/ket-qua-trung-thuong/645';
        $html = file_get_html($url);
        if($html) {
            if ($html->find('.chitietketqua_title', 0)) {
                $maxCode = $html->find('.chitietketqua_title', 0)->find('h5', 0)->find('b', 0)->innertext;
                $maxId = (int)str_replace("#", "", $maxCode);
                return $maxId;
            }
        }
        return 0;
    }

    public function crawlMax4DAction(){
        $db = Mava_Application::getDb();
        $start_run = time();
        $jump = 10;
        $maxId = $this->_getMaxIdMax4D();
        $start_id = max(1,(int)Mava_Url::getParam('start'));
        $end = (int)Mava_Url::getParam('end');
        if($end == ''){
            $end = $maxId;
        }
        $maxIdResult = $this->_getMax4dModel()->getMaxId();
        if($maxIdResult >= $maxId) {
            echo 'Already up to date';
            die;
        } else {
            $start_id = $maxIdResult + 1;
        }
        $end_id = min($maxId,$start_id + $jump);
        $data = [];
        for($i=$start_id;$i<=$end_id;){
            $rs = $this->_getMax4DResult($i);
            if($rs){
                $data[] = "('". date_to_time($rs['date'],'-') ."','". $rs['date'] ."','". $rs['code'] ."','". $rs['g1'] ."','". $rs['g2'] ."','". $rs['g3'] ."','". $rs['kk1'] ."','". $rs['kk2'] ."')";
            }
            $i++;
        }
        if(count($data) > 0){
            $db->query('INSERT INTO 
            #__vietlott_max4d_result(
                `result_date`,
                `result_time`,
                `code`,
                `g1`,
                `g2`,
                `g3`,
                `kk1`,
                `kk2`
                ) VALUES'. implode(',', $data) .' 
                ON DUPLICATE KEY UPDATE 
                `result_time`=VALUES(`result_time`),
                `result_date`=VALUES(`result_date`),
                `code`=VALUES(`code`),
                `g1`=VALUES(`g1`),
                `g2`=VALUES(`g2`),
                `g3`=VALUES(`g3`),
                `kk1`=VALUES(`kk1`),
                `kk2`=VALUES(`kk2`)
                ');
        }
        if($end_id < $end){
            die('<h1 style="text-align: center;font-size: 60px;">Getting from '. Mava_Url::getParam('start') .'...</h1><script>window.location.href = "'. Mava_Url::getPageLink('vietlott-max4d-crawl',['start' => $end_id,'end' => $end]) .'";</script>');
        }
        echo 'Done in '. (time() - $start_run) .' seconds !';
    }

    public function _getMax4DResult($id){
        $id = sprintf('%05d', $id);
        $url = 'https://vietlott.vn/vi/trung-thuong/ket-qua-trung-thuong/max-4d?id='.$id.'&nocatche=1';
        $html = file_get_html($url);
        $data = [];
        $check = $html->find('.chitietketqua_title', 0);
        if($check){
            //date
            $date = $html->find('.chitietketqua_title', 0)->find('h5', 0)->find('b',1)->innertext;
            $data['date'] = str_replace("/", "-", $date);
            //code
            $data['code'] = $html->find('.chitietketqua_title', 0)->find('h5', 0)->find('b',0)->innertext;
            if((int)$id <= 258) {
                //g1
                $data['g1'] = $html->find('.max4d_table',0)->find('tr',1)->find('td',1)->innertext;
                //g2
                $g2 = $html->find('.max4d_table',0)->find('tr',2)->find('td',1)->innertext;
                $data['g2'] = str_replace(" &nbsp;&nbsp;", "-", $g2);
                //g3
                $g3 = $html->find('.max4d_table',0)->find('tr',3)->find('td',1)->innertext;
                $data['g3'] = str_replace(" &nbsp;&nbsp;", "-", $g3);
                //kk1
                $data['kk1'] = $html->find('.max4d_table',0)->find('tr',4)->find('td',1)->innertext;
                //kk2
                $data['kk2'] = $html->find('.max4d_table',0)->find('tr',5)->find('td',1)->innertext;
            } else {
                //g1
                $data['g1'] = $html->find('.max4d_table',0)->find('tr',3)->find('td',1)->innertext;
                //g2
                $g2 = $html->find('.max4d_table',0)->find('tr',4)->find('td',1)->innertext;
                $data['g2'] = str_replace(" &nbsp;&nbsp;", "-", $g2);
                //g2
                $g3_1 = $html->find('.max4d_table',0)->find('tr',5)->find('td',1)->find('span',0)->innertext;
                $g3_2 = $html->find('.max4d_table',0)->find('tr',5)->find('td',1)->find('span',1)->innertext;
                $g3_3 = $html->find('.max4d_table',0)->find('tr',5)->find('td',1)->find('span',2)->innertext;
                $data['g3'] = $g3_1.'-'.$g3_2.'-'.$g3_3;
                //kk1
                $data['kk1'] = $html->find('.max4d_table',0)->find('tr',6)->find('td',1)->innertext;
                //kk2
                $data['kk2'] = $html->find('.max4d_table',0)->find('tr',7)->find('td',1)->innertext;
            }
            return $data;
        }
        return false;
    }

    public function _getMaxIdMax4D(){
        $url = 'https://vietlott.vn/vi/trung-thuong/ket-qua-trung-thuong/max-4d';
        $html = file_get_html($url);
        if($html){
            if($html->find('.chitietketqua_title', 0)){
                $maxCode = $html->find('.chitietketqua_title', 0)->find('h5', 0)->find('b',0)->innertext;
                $maxId = (int)str_replace("#", "", $maxCode);
                return $maxId;
            }
        }
        return 0;
    }

    public function crawlPowerAction(){
        $db = Mava_Application::getDb();
        $start_run = time();
        $jump = 10;
        $maxId = $this->_getMaxIdPower();
        $start_id = max(1,(int)Mava_Url::getParam('start'));
        $end = (int)Mava_Url::getParam('end');
        if($end == ''){
            $end = $maxId;
        }
        $maxIdResult = $this->_getPowerModel()->getMaxId();
        if($maxIdResult >= $maxId) {
            $start_id = $maxId;
        } else {
            $start_id = $maxIdResult + 1;
        }
        $end_id = min($maxId,$start_id + $jump);
        $data = [];
        for($i=$start_id;$i<=$end_id;){
            $rs = $this->_getPowerResult($i);
            if($rs){
                $data[] = "('". date_to_time($rs['date'],'-') ."','". $rs['date'] ."','". $rs['code'] ."','". $rs['jackpot_1'] ."','". $rs['jackpot_2'] ."','". $rs['num_1'] ."','". $rs['num_2'] ."','". $rs['num_3'] ."','". $rs['num_4'] ."','". $rs['num_5'] ."','". $rs['num_6'] ."','". $rs['num_7'] ."')";
            }
            $i++;
        }
        if(count($data) > 0){
            $db->query('INSERT INTO 
            #__vietlott_power_result(
                `result_date`,
                `result_time`,
                `code`,
                `jackpot_1`,
                `jackpot_2`,
                `num_1`,
                `num_2`,
                `num_3`,
                `num_4`,
                `num_5`,
                `num_6`,
                `num_7`
                ) VALUES'. implode(',', $data) .' 
                ON DUPLICATE KEY UPDATE 
                `result_time`=VALUES(`result_time`),
                `result_date`=VALUES(`result_date`),
                `code`=VALUES(`code`),
                `jackpot_1`=VALUES(`jackpot_1`),
                `jackpot_2`=VALUES(`jackpot_2`),
                `num_1`=VALUES(`num_1`),
                `num_2`=VALUES(`num_2`),
                `num_3`=VALUES(`num_3`),
                `num_4`=VALUES(`num_4`),
                `num_5`=VALUES(`num_5`),
                `num_6`=VALUES(`num_6`),
                `num_7`=VALUES(`num_7`)
                ');
        }
        if($end_id < $end){
            die('<h1 style="text-align: center;font-size: 60px;">Getting from '. Mava_Url::getParam('start') .'...</h1><script>window.location.href = "'. Mava_Url::getPageLink('vietlott-power-crawl',['start' => $end_id,'end' => $end]) .'";</script>');
        }
        echo 'Done in '. (time() - $start_run) .' seconds !';
    }

    public function _getPowerResult($id){
        $id = sprintf('%05d', $id);
        $url = 'https://vietlott.vn/vi/trung-thuong/ket-qua-trung-thuong/655?id='.$id.'&nocatche=1';
        $html = file_get_html($url);
        if($html){
            $data = [];
            $check = $html->find('.chitietketqua_title', 0);
            if($check){
                //date
                $date = $html->find('.chitietketqua_title', 0)->find('h5', 0)->find('b',1)->innertext;
                $data['date'] = str_replace("/", "-", $date);
                //code
                $data['code'] = $html->find('.chitietketqua_title', 0)->find('h5', 0)->find('b',0)->innertext;
                //jackpot_1
                $data['jackpot_1'] = $html->find('.so_tien',0)->find('h3', 0)->innertext;
                //jackpot_2
                $data['jackpot_2'] = $html->find('.so_tien',1)->find('h3', 0)->innertext;
                //num 1-7
                $j = 1;
                foreach ($html->find('.bong_tron') as $element) {
                    $data['num_'.$j] = $element->innertext;
                    $j++;
                }
                return $data;
            }
        }
        return false;
    }

    public function _getMaxIdPower(){
        $url = 'https://vietlott.vn/vi/trung-thuong/ket-qua-trung-thuong/655';
        $html = file_get_html($url);
        if($html) {
            if ($html->find('.chitietketqua_title', 0)) {
                $maxCode = $html->find('.chitietketqua_title', 0)->find('h5', 0)->find('b', 0)->innertext;
                $maxId = (int)str_replace("#", "", $maxCode);
                return $maxId;
            }
        }
        return 0;
    }

    /**
     * @return Vietlott_Model_Max4d
     */
    protected function _getMax4dModel()
    {
        return $this->getModelFromCache('Vietlott_Model_Max4d');
    }

    /**
     * @return Vietlott_Model_Power
     */
    protected function _getPowerModel()
    {
        return $this->getModelFromCache('Vietlott_Model_Power');
    }

    /**
     * @return Vietlott_Model_Mega
     */
    protected function _getMegaModel()
    {
        return $this->getModelFromCache('Vietlott_Model_Mega');
    }
}