<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/26/2019
 * Time: 2:48 PM
 */
class API_Model_FootballCache extends Mava_Model
{
    public function getById($id){
        return $this->_getDb()->fetchRow("SELECT * FROM #__football_cache WHERE `id`=" . (int)$id);
    }

    public function getByRequestKey($key){
        $key = md5($key);
        return $this->_getDb()->fetchRow("SELECT * FROM #__football_cache WHERE `request_key`='". $key ."'");
    }

    public function getMatchDay($data){
        if(strpos($data['date'],'/') > 0){
            $data['date'] = str_replace('/', '-',$data['date']);
        }
        $schedule = $this->_getDb()->fetchRow("SELECT * FROM #__football_schedules WHERE `date`='". $data['date'] ."'");
        if($schedule && (Mava_String::isJson(trim($schedule['leagues'])) || Mava_String::isJson(stripslashes($schedule['leagues'])))){
            if(Mava_String::isJson(trim($schedule['leagues']))){
                $schedule = json_decode(trim($schedule['leagues']), true);
            }else{
                $schedule = json_decode(stripslashes($schedule['leagues']), true);
            }
            if(is_array($schedule) && count($schedule) > 0){
                $fixture_ids = [];
                foreach($schedule as $item){
                    if(is_array($item['fixtures']) && count($item['fixtures']) > 0){
                        $fixture_ids = array_merge($fixture_ids, $item['fixtures']);
                    }
                }
                $fixtures = $this->_getDb()->fetchAll("SELECT * FROM #__football_matches WHERE `has_info`=1 AND `match_id` IN(". Mava_String::doImplode($fixture_ids) .")");
                if(is_array($fixtures) && count($fixtures) > 0){
                    $fixture_by_keys = [];
                    foreach($fixtures as $item){
                        $fixture = [
                            'id' => $item['match_id'],
                            'start_time' => $item['start_time'],
                            'time' => $item['time'],
                            'h1' => $item['h1'],
                            'home' => json_decode(trim($item['home']), true),
                            'away' => json_decode(trim($item['away']), true),
                            'tips' => json_decode(trim($item['tips']), true),
                            'finish' => $item['finish'],
                            'has_tips' => $item['has_tips'],
                            'has_goal' => ($item['finish']==1||$item['start_time']<time())?1:0
                        ];
                        if(
                            $fixture['has_tips'] == 0 &&
                            isset($fixture['tips']['asia']) &&
                            isset($fixture['tips']['asia']['total']) &&
                            isset($fixture['tips']['asia']['home']) &&
                            isset($fixture['tips']['asia']['away']) &&
                            (
                                $fixture['tips']['asia']['total'] != 0 ||
                                $fixture['tips']['asia']['home'] != 0 ||
                                $fixture['tips']['asia']['away'] != 0
                            )
                        ){
                            $fixture['has_tips'] = 1;
                        }
                        $fixture_by_keys[$item['match_id']] = $fixture;
                    }
                    $schedule_formatted = [];
                    foreach($schedule as $item){
                        $item['icon'] = url('league-icon/'. $item['icon']);
                        $item_formatted = [
                            'league' => $item,
                            'fixtures' => []
                        ];
                        $fixtures = [];
                        if(is_array($item['fixtures']) && count($item['fixtures']) > 0){
                            foreach($item['fixtures'] as $f){
                                if(isset($fixture_by_keys[$f])){
                                    $fixtures[] = $fixture_by_keys[$f];
                                }
                            }
                            $item_formatted['fixtures'] = $fixtures;
                        }
                        $schedule_formatted[] = $item_formatted;
                    }
                    return $schedule_formatted;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            $this->_getDb()->query("INSERT IGNORE INTO #__football_schedule_queue(`date`) VALUE('". $data['date'] ."')");
            return false;
        }
    }

    public function getMatchDayLive($date) {
        $date = date('Y-m-d', date_to_time($date, '/'));
        $urlAPI = Mava_Application::getConfig('X_RapidAPI_Endpoint') ."fixtures/date/".$date .'?timezone=Asia/Bangkok';
        $RapidAPIHost = Mava_Application::getConfig('X_RapidAPI_Host');
        $RapidAPIKey = Mava_Application::getConfig('X_RapidAPI_Key');
        $responseLiveAPI = Unirest\Request::get($urlAPI,
            array(
                "X-RapidAPI-Host" => $RapidAPIHost,
                "X-RapidAPI-Key" => $RapidAPIKey
            )
        );
        return $responseLiveAPI->raw_body;
    }

    public function saveCacheData($data){
        $fcDW = $this->_getFootballCacheDataWriter();
        $fcDW->bulkSet($data);
        if($fcDW->save()){
            return $this->getById($fcDW->get('id'));
        }
        return false;
    }

    public function updateCacheData($id, $data){
        $fcDW = $this->_getFootballCacheDataWriter();
        $fcDW->setExistingData($id);
        $fcDW->bulkSet($data);
        if($fcDW->save()){
            return $this->getById($id);
        }
        return false;
    }

    public function formatData($leagueIds, $data){
        $leagueModel = $this->_getLeagueModel();
        $data = is_string($data)?json_decode($data, true):(is_object($data)?json_decode(json_encode($data), true):$data);
        $leagueIds = [];
        if(isset($data['api']) && isset($data['api']['results']) && $data['api']['results'] > 0) {
            $matchs = [];
            $fixtures = $data['api']['fixtures'];
            // group by league_id
            foreach ($fixtures as $key=>$value){
                $leagueIds[$value['league_id']] = 1;
            }
            $leagues_filter = $leagueModel->getByLeagueCountryConfig();
            $league_ids_filter = [];
            if($leagues_filter && count($leagues_filter) > 0){
                foreach ($leagues_filter as $value) {
                    $league_ids_filter[] = $value['league_id'];
                }
            }
            $country = Mava_Application::getConfig('league_country');
            foreach($country as $c){
                $matchs[$c] = [];
            }
            $leagueIds = array_keys($leagueIds);
            $leagues = $leagueModel->getByLeagueIds($leagueIds);
            if($leagues){
                $leagues_formatted = [];
                foreach($leagues as $l){
                    $leagues_formatted[$l['league_id']] = $l;
                }
                foreach($fixtures as $k => $v){
                    if(isset($leagues_formatted[$v['league_id']]) && $leagues_formatted[$v['league_id']]['league_id'] == $v['league_id'] && in_array($v['league_id'], $league_ids_filter)) {
                        $v['league'] = array_filter_key($leagues_formatted[$v['league_id']],[
                            'id','league_id','name','country','logo'
                        ]);
                        if (!isset($matchs[$v['league']['country']][$v['league_id']])) {
                            $matchs[$v['league']['country']][$v['league_id']] = [
                                'league' => $v['league'],
                                'fixtures' => []
                            ];
                        }
                        $matchs[$v['league']['country']][$v['league_id']]['fixtures'][] = array_filter_key($v, [
                            'fixture_id','event_timestamp','statusShort','homeTeam','awayTeam','goalsHomeTeam','goalsAwayTeam','score'
                        ]);
                    }
                }
                $results = [];
                foreach(array_values($matchs) as $c){
                    $results = array_merge($results, array_values($c));
                }
                return $results;
            }else{
                return [];
            }
        } else {
            return [];
        }
    }

    public function getMatchDetail($data){
        $match = $this->_getDb()->fetchRow("SELECT * FROM #__football_matches WHERE `match_id`='". $data['match_id'] ."' AND `has_info`=1");
        if($match){
            $fixture = [
                'id' => $match['match_id'],
                'time' => $match['time'],
                'start_time' => $match['start_time'],
                'h1' => $match['h1'],
                'home' => json_decode(trim($match['home']), true),
                'away' => json_decode(trim($match['away']), true),
                'events' => json_decode(trim($match['events']), true),
                'statics' => json_decode(trim($match['statics']), true),
                'tips' => json_decode(trim($match['tips']), true),
                'finish' => $match['finish'],
                'has_tips' => $match['has_tips'],
                'has_goal' => ($match['finish']==1||$match['start_time'] < time())?1:0
            ];
            if(
                $fixture['has_tips'] == 0 &&
                isset($fixture['tips']['asia']) &&
                isset($fixture['tips']['asia']['total']) &&
                isset($fixture['tips']['asia']['home']) &&
                isset($fixture['tips']['asia']['away']) &&
                (
                    $fixture['tips']['asia']['total'] != 0 ||
                    $fixture['tips']['asia']['home'] != 0 ||
                    $fixture['tips']['asia']['away'] != 0
                )
            ){
                $fixture['has_tips'] = 1;
            }
            return $fixture;
        }else{
            return false;
        }
    }

    public function getLeagueDetail($data){
        $league = $this->_getDb()->fetchRow("SELECT * FROM #__football_leagues WHERE `league_id`='". $data['league_id'] ."'");
        if($league){
            $rounds = $this->_getDb()->fetchAll("SELECT * FROM #__football_rounds WHERE `league_id`='". $data['league_id'] ."' ORDER BY `sort_order` ASC");
            $round_formatted = [];
            $fixture_formatted = [];
            $league['current_round'] = false;
            if($rounds){
                foreach($rounds as $round){
                    $round_formatted[] = [
                        'id' => $round['id'],
                        'title' => $round['title'],
                        'is_current' => $round['is_current'],
                        'has_info' => $round['has_info']
                    ];
                    if($round['is_current'] == 1 && $league['current_round'] === false){
                        $league['current_round'] = $round;
                    }
                    if(isset($data['round_id']) && $data['round_id'] == $round['id']){
                        $league['current_round'] = $round;
                    }
                }
                if($league['current_round'] === false){
                    $league['current_round'] = $rounds[0];
                }
                if(is_array($league['current_round']) && Mava_String::isJson($league['current_round']['fixtures'])){
                    $fixture_ids = json_decode($league['current_round']['fixtures'], true);
                    $fixtures = $this->_getDb()->fetchAll("SELECT * FROM #__football_matches WHERE `has_info`=1 AND `match_id` IN(". Mava_String::doImplode($fixture_ids) .")");
                    if($fixtures){
                        foreach($fixtures as $item){
                            $fixture = [
                                'id' => $item['match_id'],
                                'start_time' => $item['start_time'],
                                'time' => $item['time'],
                                'h1' => $item['h1'],
                                'home' => json_decode(trim($item['home']), true),
                                'away' => json_decode(trim($item['away']), true),
                                'tips' => json_decode(trim($item['tips']), true),
                                'finish' => $item['finish'],
                                'has_tips' => $item['has_tips'],
                                'has_goal' => ($item['finish']==1||$item['start_time']<time())?1:0
                            ];
                            if(
                                $fixture['has_tips'] == 0 &&
                                isset($fixture['tips']['asia']) &&
                                isset($fixture['tips']['asia']['total']) &&
                                isset($fixture['tips']['asia']['home']) &&
                                isset($fixture['tips']['asia']['away']) &&
                                (
                                    $fixture['tips']['asia']['total'] != 0 ||
                                    $fixture['tips']['asia']['home'] != 0 ||
                                    $fixture['tips']['asia']['away'] != 0
                                )
                            ){
                                $fixture['has_tips'] = 1;
                            }
                            $fixture_formatted[$item['match_id']] = $fixture;
                        }
                    }
                }
            }
            if(Mava_String::isJson($league['standings'])){
                $league['standings'] = json_decode($league['standings'], true);
                $standings = [];
                foreach($league['standings'] as $k => $v){
                    $standings[] = [
                        'name' => $k,
                        'teams' => $v
                    ];
                }
                $league['standings'] = $standings;
            }else{
                $league['standings'] = [];
            }
            return [
                'league' => $league,
                'rounds' => $round_formatted,
                'fixtures' => array_values($fixture_formatted)
            ];
        }else{
            return false;
        }
    }

    protected function _groupByGroup($standings){
        $result = [];
        foreach($standings as $group){
            foreach($group as $item){
                if(!isset($result[$item['group']])){
                    $result[$item['group']] = [
                        'name' => $item['group'],
                        'teams'=> []
                    ];
                }
                $result[$item['group']]['teams'][] = $item;
            }
        }
        return array_values($result);
    }

    protected function _groupByRound($fixtures){
        $result = [];
        $offset = 0; // use for scroll list to current round
        foreach($fixtures as $item){
            if(!isset($result[$item['round']])){
                $result[$item['round']] = [
                    'round' => $item['round'],
                    'fixtures' => []
                ];
                if($item['event_timestamp'] < time()){
                    $offset++;
                }
            }
            $result[$item['round']]['fixtures'][] = $item;
        }
        return [
            'rounds' => array_values($result),
            'offset' => $offset
        ];
    }

    public function getLeagueDetailLive($league_id) {
        $RapidAPIHost = Mava_Application::get('config/X_RapidAPI_Host');
        $RapidAPIKey = Mava_Application::get('config/X_RapidAPI_Key');

        // get schedule league
        $urlScheduleAPI = Mava_Application::getConfig('X_RapidAPI_Endpoint') ."fixtures/league/".$league_id;
        $responseScheduleAPI = Unirest\Request::get($urlScheduleAPI,
            array(
                "X-RapidAPI-Host" => $RapidAPIHost,
                "X-RapidAPI-Key" => $RapidAPIKey
            )
        );

        // get standings league
        $urlStandingsAPI = Mava_Application::getConfig('X_RapidAPI_Endpoint') ."leagueTable/".$league_id;
        $responseStandingsAPI = Unirest\Request::get($urlStandingsAPI,
            array(
                "X-RapidAPI-Host" => $RapidAPIHost,
                "X-RapidAPI-Key" => $RapidAPIKey
            )
        );

        $data = array(
            'schedule' => $responseScheduleAPI->raw_body,
            'standings' => $responseStandingsAPI->raw_body
        );

        return json_encode($data);
    }

    /**
     * @return API_DataWriter_FootballCache
     */
    protected function _getFootballCacheDataWriter()
    {
        return Mava_DataWriter::create('API_DataWriter_FootballCache');
    }

    /**
     * @return Football_Model_Leagues
     * @throws Mava_Exception
     */
    protected function _getLeagueModel(){
        return $this->getModelFromCache('Football_Model_Leagues');
    }
}