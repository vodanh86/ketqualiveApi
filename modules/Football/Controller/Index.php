<?php

class Football_Controller_Index extends Mava_Controller {
    /**
     *  craw remain date => always
     *  craw date => 1h/times
     *  crawl match => 1m/times
     *  crawl live => always
     *  crawl league => 1h/times
     *  crawl round => 1h/times
     *  Order: remain date > date > league > round > match > live
     */
    public function crawlDateAction(){
        return $this->_crawl('date', 2, 15);
    }

    public function crawlTodayAction(){
        return $this->_crawl('today', 5, 15);
    }

    public function crawlMatchAction(){
        return $this->_crawl('match', 5, 3);
    }

    public function crawlNotFinishAction(){
        return $this->_crawl('not_finish', 8, 3);
    }

    public function crawlRemainMatchAction(){
        return $this->_crawl('remain_match', 8, 3);
    }

    public function crawlLiveAction(){
        return $this->_crawl('live', 2, 1);
    }

    public function crawlRoundAction(){
        return $this->_crawl('round', 5, 3);
    }

    public function crawlLeagueAction(){
        return $this->_crawl('league', 5, 5);
    }

    public function crawlRemainDateAction(){
        return $this->_crawl('remain', 5, 5);
    }


    protected function _crawl($key, $minute, $sleep = 1){
        $minute = min($minute, 8);
        set_time_limit(0);
        $cache_key = 'crawl-'. $key;
        $is_crawling = (int)Mava_Application::getCache($cache_key);
        if($is_crawling > time()){
            return $this->responseJson([
                'error' => 0,
                'message' => '['. $key .'] Crawling...'
            ]);
        }else{
            $result = [];
            $end_time = time()+($minute*60);
            $current_time = time();
            $crawlService = $this->_getCrawlModel();
            while($end_time > $current_time){
                Mava_Application::setCache($cache_key, time()+($minute*60), 60);
                $current_time = time();
                switch ($key){
                    case 'not_finish':
                        $res = $crawlService->not_finish();
                        if(isset($res['no_match']) && $res['no_match'] == 1){
                            $current_time = $end_time;
                        }
                        $result[] = $res;
                        break;
                    case 'remain_match':
                        $res = $crawlService->remain_match();
                        if(isset($res['no_match']) && $res['no_match'] == 1){
                            $current_time = $end_time;
                        }
                        $result[] = $res;
                        break;
                    case 'match':
                        $res = $crawlService->match();
                        if(isset($res['no_match']) && $res['no_match'] == 1){
                            $current_time = $end_time;
                        }
                        $result[] = $res;
                        break;
                    case 'date':
                        $res = $crawlService->date(Mava_Url::getParam('date'));
                        if(isset($res['no_match']) && $res['no_match'] == 1){
                            $current_time = $end_time;
                        }
                        $result[] = $res;
                        break;
                    case 'today':
                        $res = $crawlService->today();
                        if(isset($res['no_match']) && $res['no_match'] == 1){
                            $current_time = $end_time;
                        }
                        $result[] = $res;
                        break;
                    case 'league':
                        $res = $crawlService->league();
                        if(isset($res['no_match']) && $res['no_match'] == 1){
                            $current_time = $end_time;
                        }
                        $result[] = $res;
                        break;
                    case 'round':
                        $res = $crawlService->round();
                        if(isset($res['no_match']) && $res['no_match'] == 1){
                            $current_time = $end_time;
                        }
                        $result[] = $res;
                        break;
                    case 'live':
                        $result[] = $crawlService->live();
                        break;
                    case 'remain':
                        $res = $crawlService->remain_date();
                        if(isset($res['no_match']) && $res['no_match'] == 1){
                            $current_time = $end_time;
                        }
                        $result[] = $res;
                        break;
                    default:
                        $current_time = $end_time;
                        break;
                }
                sleep($sleep);
            }
            Mava_Application::setCache($cache_key, time()-1, 6000);
            return $this->responseJson($result);
        }
    }

    public function apiAction(){
        $urlAPI = Mava_Application::getConfig('X_RapidAPI_Endpoint') ."fixtures/live";
        $RapidAPIHost = Mava_Application::getConfig('X_RapidAPI_Host');
        $RapidAPIKey = Mava_Application::getConfig('X_RapidAPI_Key');
        $responseLiveAPI = Unirest\Request::get($urlAPI,
            array(
                "X-RapidAPI-Host" => $RapidAPIHost,
                "X-RapidAPI-Key" => $RapidAPIKey
            )
        );
        if(Mava_String::isJson($responseLiveAPI->raw_body)){
            $leagues = @json_decode($responseLiveAPI->raw_body, true);
            dd($leagues);
        }
    }

    public function updateResultFootballTipAction(){
        $date = Mava_Url::getParam('date');
        if(!$date) {
            $date = date('d-m-Y', time());
        }
        $result = $this->_getResultModel()->updateResultFootballTip($date);
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

    public function leagueIconAction(){
        $file_name = Mava_Url::getParam('file_name');
        if(file_exists(IMAGE_DIR . DS . 'league'. DS . $file_name)){
            return $this->responseRedirect(image_url('data/images/league/'. $file_name));
        }else{
            Mava_Log::info('Missing logo for '. $file_name);
            return $this->responseRedirect(image_url('data/images/league/football.png'));
        }
    }
    /**
     * @return Football_Model_Result
     * @throws Mava_Exception
     */
    protected function _getResultModel(){
        return $this->getModelFromCache('Football_Model_Result');
    }

    /**
     * @return Football_Model_Crawl
     */
    protected function _getCrawlModel(){
        return $this->getModelFromCache('Football_Model_Crawl');
    }
}