<?php
class Football_Model_Crawl extends Mava_Model {
    const CRAWL_ENPOINT = 'http://lichthidau.com.vn';
    public function _match($id){
        $url = self::CRAWL_ENPOINT .'/tran-a-b-'. $id .'.html';
        $html = @file_get_html($url);
        $start_time = 0;
        if($html){
            $page_title = $html->find('title', 0);
            if($page_title){
                $start_time = explode(' tiếp lúc ', $page_title->text());
                if(count($start_time) == 2){
                    $start_time = explode(' ', trim($start_time[1]));
                    list($day, $month, $year) = explode('/', $start_time[1]);
                    list($hour, $minute) = explode('h', $start_time[0]);
                    $start_time = mktime($hour, $minute, 0, $month, $day, $year);
                }
            }
            // summary
            $match_sum = $html->find('.TRANDAU_TYSO_bg',0);
            if($match_sum){
                $goal_time = $html->find('.TRANDAU_TYSO_3',0);
                if($goal_time){
                    $time = $goal_time->find('p',0)->innertext;
                    $goal = $goal_time->find('.TRANDAU_TYSO_boxts',0)?$goal_time->find('.TRANDAU_TYSO_boxts',0)->text():"";
                    if($goal != ""){
                        $goal = explode('-', $goal);
                        $home_goal = trim($goal[0]);
                        $away_goal = trim($goal[1]);
                    }else{
                        $home_goal = '';
                        $away_goal = '';
                    }
                    $h1 = $goal_time->find('.h1_Trandau',0)?$goal_time->find('.h1_Trandau',0)->text():"";
                }else{
                    $home_goal = 0;
                    $away_goal = 0;
                    $h1 = "";
                }
                $home_logo = $match_sum->find('.TRANDAU_TYSO_1 img',0)?$match_sum->find('.TRANDAU_TYSO_1 img',0)->src:"";
                $away_logo = $match_sum->find('.TRANDAU_TYSO_1 img',1)?$match_sum->find('.TRANDAU_TYSO_1 img',1)->src:"";

                $home_name = $match_sum->find('.TRANDAU_TYSO_2',0)?$match_sum->find('.TRANDAU_TYSO_2',0)->innertext:"";
                $home_name = explode('<p', $home_name);
                $home_name = trim($home_name[0]);

                $away_name = $match_sum->find('.TRANDAU_TYSO_4',0)?$match_sum->find('.TRANDAU_TYSO_4',0)->innertext:"";
                $away_name = explode('<p', $away_name);
                $away_name = trim($away_name[0]);

                $home_redcard = 0;
                $home_redcard2 = 0;
                $home_yellowcard = 0;
                $away_redcard = 0;
                $away_redcard2 = 0;
                $away_yellowcard = 0;

                // events
                $event_html = $html->find('#matchEventLive',0);
                $events = [];
                if($event_html){
                    foreach($event_html->find('li') as $event){
                        $item = [];
                        if(trim(str_replace('&nbsp;',' ', $event->find('.TRANDAU_TRUCTIEP_dienbien_1',0)->text())) == ""){
                            // away event
                            $item['side'] = 'away';
                            $event_detail = str_replace('&nbsp;', ' ',$event->find('.TRANDAU_TRUCTIEP_dienbien_3',0)->innertext);
                        }else{
                            // home event
                            $item['side'] = 'home';
                            $event_detail = str_replace('&nbsp;', ' ',$event->find('.TRANDAU_TRUCTIEP_dienbien_1',0)->innertext);
                        }
                        $item['minute'] = trim(preg_replace('/[^0-9\+]/','',$event->find('.TRANDAU_TRUCTIEP_dienbien_2',0)->text()));
                        if(strpos($event_detail,'/ghiban.png') > 0){
                            $item['type'] = 'GOAL';
                            $item['player'] = trim(strip_tags($event_detail));
                        }elseif(strpos($event_detail,'/ownergoal.png') > 0){
                            $item['type'] = 'OWNERGOAL';
                            $item['player'] = trim(strip_tags($event_detail));
                        }elseif(strpos($event_detail,'/goalpenalty.png') > 0){
                            $item['type'] = 'PENGOAL';
                            $item['player'] = trim(strip_tags($event_detail));
                        }elseif(strpos($event_detail, '4.gif') > 0){
                            $item['type'] = 'ROTATION';
                            $in_out = explode("<img title='In' src='https://lh6.googleusercontent.com/-GmAiXDMtxCM/UtOPzJ58KEI/AAAAAAAAATM/9U7NYrqmnsQ/w12-h11-no/4.gif'/>", $event_detail);
                            $item['player'] = [
                                'in' => trim($in_out[0]),
                                'out' => trim(strip_tags($in_out[1]))
                            ];
                        }elseif(strpos($event_detail,'/thevang.png') > 0){
                            $item['type'] = 'YELLOWCARD';
                            $item['player'] = trim(strip_tags($event_detail));
                            $item['side']=='home'?$home_yellowcard++:$away_yellowcard++;
                        }elseif(strpos($event_detail,'/thedo.png') > 0){
                            $item['type'] = 'REDCARD1';
                            $item['player'] = trim(strip_tags($event_detail));
                            $item['side']=='home'?$home_redcard++:$away_redcard++;
                        }elseif(strpos($event_detail,'/2thevang.png') > 0){
                            $item['type'] = 'REDCARD2';
                            $item['player'] = trim(strip_tags($event_detail));
                            $item['side']=='home'?$home_redcard2++:$away_redcard2++;
                            $item['side']=='home'?$home_yellowcard++:$away_yellowcard++;
                        }
                        $events[] = $item;
                    }
                }

                // statics
                $statics = [
                    'home' => [
                        'kick' => 0,
                        'ontarget' => 0,
                        'corner' => 0,
                        'foul' => 0,
                        'redcard' => 0,
                        'yellowcard' => 0,
                        'offside' => 0,
                        'possession' => 0
                    ],
                    'away' => [
                        'kick' => 0,
                        'ontarget' => 0,
                        'corner' => 0,
                        'foul' => 0,
                        'redcard' => 0,
                        'yellowcard' => 0,
                        'offside' => 0,
                        'possession' => 0
                    ]
                ];

                $statics_html = $html->find('.TRANDAU_TRUCTIEP_TK_chitiet',0);
                if($statics_html){
                    foreach($statics_html->find('tr') as $row){
                        $type = $row->find('.TRANDAU_TRUCTIEP_TK_chitiet_2',0);
                        $home_num = $row->find('.TRANDAU_TRUCTIEP_TK_chitiet_1',0);
                        $away_num = $row->find('.TRANDAU_TRUCTIEP_TK_chitiet_3',0);
                        if($type){
                            if($type->text() == 'Sút bóng'){
                                $home_kick = explode('(',$home_num->text());
                                $statics['home']['kick'] = trim($home_kick[0]);
                                $statics['home']['ontarget'] = trim(preg_replace('/[^0-9]/','', $home_kick[1]));
                                $away_kick = explode('(',$away_num->text());
                                $statics['away']['kick'] = trim($away_kick[0]);
                                $statics['away']['ontarget'] = trim(preg_replace('/[^0-9]/','', $away_kick[1]));
                            }elseif($type->text() == 'Phạt góc'){
                                $statics['home']['corner'] = trim(preg_replace('/[^0-9]/','', $home_num->text()));
                                $statics['away']['corner'] = trim(preg_replace('/[^0-9]/','', $away_num->text()));
                            }elseif($type->text() == 'Phạm lỗi'){
                                $statics['home']['foul'] = trim(preg_replace('/[^0-9]/','', $home_num->text()));
                                $statics['away']['foul'] = trim(preg_replace('/[^0-9]/','', $away_num->text()));
                            }elseif($type->text() == 'Thẻ đỏ'){
                                $statics['home']['redcard'] = trim(preg_replace('/[^0-9]/','', $home_num->text()));
                                $statics['away']['redcard'] = trim(preg_replace('/[^0-9]/','', $away_num->text()));
                            }elseif($type->text() == 'Thẻ vàng'){
                                $statics['home']['yellowcard'] = trim(preg_replace('/[^0-9]/','', $home_num->text()));
                                $statics['away']['yellowcard'] = trim(preg_replace('/[^0-9]/','', $away_num->text()));
                            }elseif($type->text() == 'Việt vị'){
                                $statics['home']['offside'] = trim(preg_replace('/[^0-9]/','', $home_num->text()));
                                $statics['away']['offside'] = trim(preg_replace('/[^0-9]/','', $away_num->text()));
                            }elseif($type->text() == 'Cầm bóng'){
                                $statics['home']['possession'] = trim(preg_replace('/[^0-9]/','', $home_num->text()));
                                $statics['away']['possession'] = trim(preg_replace('/[^0-9]/','', $away_num->text()));
                            }
                        }
                    }
                }

                // tips
                $tips = [
                    'asia' => [
                        'total' => 0,
                        'home' => 0,
                        'away' => 0
                    ],
                    'taixiu' => [
                        'total' => 0,
                        'home' => 0,
                        'away' => 0
                    ]
                ];

                $has_tips = 0;
                $tips_html = $html->find('.YKIENCHUYENGIA',0);
                if($tips_html){
                    foreach($tips_html->find('.do') as $p){
                        if(strpos("Line ". $p->text(), 'Châu Á') > 0){
                            $has_tips = 1;
                            $tip = explode('*', $p->text());
                            $tips['asia']['total'] = trim(preg_replace('/[^0-9\.\s\/:]/','', $tip[1]));
                            $tips['asia']['home'] = trim(preg_replace('/[^0-9\.]/','', $tip[0]));
                            $tips['asia']['away'] = trim(preg_replace('/[^0-9\.]/','', $tip[2]));
                        }elseif(strpos("Line ". $p->text(), 'Tài xỉu') > 0){
                            $has_tips = 1;
                            $tip = explode('*', $p->text());
                            $tips['taixiu']['total'] = trim(preg_replace('/[^0-9\.\s\/:]/','', $tip[1]));
                            $tips['taixiu']['home'] = trim(preg_replace('/[^0-9\.]/','', $tip[0]));
                            $tips['taixiu']['away'] = trim(preg_replace('/[^0-9\.]/','', $tip[2]));
                        }
                        if((int)$tips['asia']['total'] == 0 && (int)$tips['taixiu']['total'] == 0){
                            $has_tips = 0;
                        }
                    }
                }
                $fixture = [
                    'id' => $id,
                    'time' => trim(strip_tags(str_replace("><","> <", $time))),
                    'start_time' => $start_time,
                    'h1' => str_replace(['(',')'],'',$h1),
                    'home' => [
                        'name' => $home_name,
                        'goal' => (int)$home_goal,
                        'logo' => $home_logo,
                        'redcard' => $home_redcard,
                        'redcard2' => $home_redcard2,
                        'yellowcard' => $home_yellowcard
                    ],
                    'away' => [
                        'name' => $away_name,
                        'goal' => (int)$away_goal,
                        'logo' => $away_logo,
                        'redcard' => $away_redcard,
                        'redcard2' => $away_redcard2,
                        'yellowcard' => $away_yellowcard
                    ],
                    'events' => array_reverse($events),
                    'statics' => $statics,
                    'tips' => $tips,
                    'has_tips' => $has_tips
                ];
                Mava_Log::info('Crawl match #'. $id .' ('. $home_name .' vs '. $away_name .')');
                return [
                    'error' => 0,
                    'data' => $fixture
                ];
            }else{
                // fail
                Mava_Log::error('#1 Could not crawl match detail with ID = '. $id);
                return [
                    'error' => 1,
                    'message' => '#1 Could not crawl match detail with ID = '. $id
                ];
            }
        }else{
            // fail
            Mava_Log::error('#2 Could not crawl match detail with ID = '. $id);
            return [
                'error' => 1,
                'message' => '#2 Could not crawl match detail with ID = '. $id
            ];
        }
    }

    public function _matches($ids){
        if(is_array($ids) && count($ids) > 0){
            $existed = $this->_getDb()->fetchAll("SELECT `match_id`,`finish` FROM #__football_matches WHERE `error`<10 AND `match_id` IN(". Mava_String::doImplode($ids) .")");
            $finished_id = [];
            $existed_id = [];
            if($existed){
                foreach($existed as $item){
                    if($item['finish'] == 1){
                        $finished_id[] = $item['match_id'];
                    }
                    $existed_id[] = $item['match_id'];
                }
            }
            $total = count($ids);
            $updated = 0;
            $failed = 0;
            foreach($ids as $id){
                if(!in_array($id, $finished_id)){
                    $result = $this->_match($id);
                    if($result['error'] == 0){
                        if(in_array($result['data']['time'], ['FT','ft','Hoãn','Huỷ'])){
                            $finish = 1;
                        }else{
                            $finish = 0;
                        }
                        if(in_array($id, $existed_id)){
                            $this->_getDb()->query("UPDATE #__football_matches SET 
                            `time` = '". addslashes($result['data']['time']) ."',
                            `start_time` = '". intval($result['data']['start_time']) ."',
                            `has_info` = 1,
                            `h1` = '". addslashes($result['data']['h1']) ."',
                            `home` = '". addslashes(json_encode($result['data']['home'])) ."',
                            `away` = '". addslashes(json_encode($result['data']['away'])) ."',
                            `events` = '". addslashes(json_encode($result['data']['events'])) ."',
                            `statics` = '". addslashes(json_encode($result['data']['statics'])) ."',
                            `tips` = '". addslashes(json_encode($result['data']['tips'])) ."',
                            `has_tips` = '". ($result['data']['has_tips']==1?1:0) ."',
                            `finish` = '". $finish ."'
                            WHERE `match_id`='". $id ."'");
                        }else{
                            $this->_getDb()->insert('#__football_matches',[
                                'match_id' => trim($result['data']['id']),
                                'time' => trim($result['data']['time']),
                                'start_time' => intval($result['data']['start_time']),
                                'has_info' => 1,
                                'h1' => trim($result['data']['h1']),
                                'home' => trim(json_encode($result['data']['home'])),
                                'away' => trim(json_encode($result['data']['away'])),
                                'events' => trim(json_encode($result['data']['events'])),
                                'statics' => trim(json_encode($result['data']['statics'])),
                                'tips' => trim(json_encode($result['data']['tips'])),
                                'has_tips' => ($result['data']['has_tips']==1?1:0),
                                'finish' => $finish,
                                'created_at' => time()
                            ]);
                        }
                        $updated++;
                    }else{
                        Mava_Log::error("Could not crawl match #". $id ." (". $result['message'] .")");
                        $this->_getDb()->query("UPDATE #__football_matches SET `error` = `error`+1 WHERE `match_id`='". $id ."'");
                        $failed++;
                    }
                }
            }
            return [
                'error' => 0,
                'data' => [
                    'total' => $total,
                    'updated' => $updated,
                    'failed' => $failed
                ]
            ];
        }else{
            return [
                'error' => 0,
                'message' => 'No match'
            ];
        }
    }

    public function _league($id){
        $link = self::CRAWL_ENPOINT .'/giai-'. $id .'.html';
        $html = @file_get_html($link);
        if($html) {
            if($html->find('.Table_LTD_title_form .selectpicker option')){
                // rounds
                $rounds = [];
                $count = 0;
                foreach($html->find('.Table_LTD_title_form .selectpicker option') as $round){
                    $count++;
                    $rounds[] = [
                        'url' => trim($round->value,'/'),
                        'title' => trim($round->text()),
                        'sort_order' => $count,
                        'is_last' => 0,
                        'is_current' => (strpos($round->outertext,'selected')>0)?1:0
                    ];
                }
                if(count($rounds) > 0){
                    $rounds[count($rounds)-1]['is_last'] = 1;
                }

                $standings = [];
                $current_group = 'all';
                // standing
                if($html->find('.BXH_table_content tr')){
                   foreach ( $html->find('.BXH_table_content tr') as $item){
                        if(strpos("-". $item->text(), 'Bảng') > 0){
                            $current_group = trim(str_replace('&nbsp;','',$item->text()));
                        }elseif($item->class == 'BXH_table_content_row_1'){
                            if(!isset($standings[$current_group])){
                                $standings[$current_group] = [];
                            }
                            $data = [
                                'rank' => preg_replace('/[^0-9]+/','', $item->find('.BXH_table_content_col_1',0)->text()),
                                'name' => trim($item->find('.BXH_table_content_col_2',0)->text()),
                                'match' => trim($item->find('.BXH_table_content_col_3',0)->text()),
                                'win' => trim($item->find('.BXH_table_content_col_3',1)->text()),
                                'draw' => trim($item->find('.BXH_table_content_col_3',2)->text()),
                                'lost' => trim($item->find('.BXH_table_content_col_3',3)->text()),
                                'offset' => trim($item->find('.BXH_table_content_col_4',0)->text()),
                                'point' => trim($item->find('.BXH_table_content_col_4',1)->text()),
                            ];
                            $standings[$current_group][] = $data;
                        }else{
                            // unknow row
                        }
                   }
                }
                return [
                    'error' => 0,
                    'data' => [
                        'rounds' => $rounds,
                        'standings' => $standings
                    ]
                ];
            }else{
                return [
                    'error' => 1,
                    'message' => '#1 Could not crawl league round'
                ];
            }
        }else{
            return [
                'error' => 1,
                'message' => '#1 Could not crawl league round'
            ];
        }
    }

    public function _round($url){
        $html = @file_get_html(self::CRAWL_ENPOINT .'/'. trim($url, '/'));
        if($html){
            $leagues = [];
            $fixture_ids = [];
            $insert_leagues = [];
            foreach($html->find('table') as $table){
                $title = $table->find('.LTD_table_row_title',0);
                if($title){
                    $league_link = $title->find('a',0);
                    if($league_link){
                        $league_link = parse_url($league_link->href);
                        $league_path = str_replace(['/','.html'], '', $league_link['path']);
                        $league_title = $title->find('h2 > a',0);
                        if($league_title){
                            $league_title = trim($league_title->innertext);
                            $league_id = explode('-', $league_path);
                            $league_id = $league_id[count($league_id)-1];
                            $league = [
                                'id' => $league_id,
                                'title' => $league_title,
                                'icon' => 'icon-'. $league_id .'.png',
                                'fixtures' => []
                            ];
                            $insert_leagues[] = '("'. $league_id .'","'. addslashes($league_title) .'","0","0")';
                            if($table->find('.LTD_table_row_1')){
                                foreach($table->find('.LTD_table_row_1') as $match){
                                    $link = parse_url($match->find('.LTD_tabale_col_2 a',0)->href);
                                    $link = $link['path'];
                                    preg_match('/\-([0-9]+)\.html/', $link, $match_id);
                                    list($home, $away) = explode(' vs ', $match->find('.LTD_tabale_col_2',0)->text());
                                    $status = $match->find('.LTD_tabale_col_1',0)->text();
                                    $status = explode(' ', preg_replace('/(\s)+/',' ', str_replace('&nbsp;',' ', $status)));
                                    if(count(explode('-', $status[1])) == 2){
                                        list($home_goal, $away_goal) = explode('-', $status[1]);
                                    }else{
                                        $home_goal = '';
                                        $away_goal = '';
                                    }
                                    if(in_array($status[0], ['FT','H1','H2','PEN'])){
                                        $home_goal = $status[1];
                                        $away_goal = $status[3];
                                    }
                                    $start_time = 0;
                                    if(count(explode('h', $status[1])) == 2){
                                        $time = $status[0] .' '. $status[1];
                                    }else{
                                        $time = $status[0];
                                    }

                                    if($status[1] == 'Hoãn'){
                                        $time = 'Hoãn';
                                    }

                                    $tips = [
                                        'asia' => [
                                            'total' => trim(str_replace('&nbsp;',' ', $match->find('.LTD_tabale_col_3', 0)->text())),
                                            'home' => trim(str_replace('&nbsp;',' ', $match->find('.LTD_tabale_col_4', 0)->text())),
                                            'away' => trim(str_replace('&nbsp;',' ', $match->find('.LTD_tabale_col_4', 1)->text()))
                                        ],
                                        'taixiu' => [
                                            'total' => trim(str_replace('&nbsp;',' ', $match->find('.LTD_tabale_col_3', 2)->text())),
                                            'home' => trim(str_replace('&nbsp;',' ', $match->find('.LTD_tabale_col_4', 4)->text())),
                                            'away' => trim(str_replace('&nbsp;',' ', $match->find('.LTD_tabale_col_4', 5)->text()))
                                        ]
                                    ];

                                    $league['fixtures'][$match_id[1]] = $match_id[1];
                                    $fixture_ids[$match_id[1]] = [
                                        'id' => $match_id[1],
                                        'time' => $time,
                                        'start_time' => $start_time,
                                        'home_goal' => $home_goal,
                                        'away_goal' => $away_goal,
                                        'home' => preg_replace('/^([0-9]+)([^\s]+)/','$2',preg_replace('/([^\s]+)([0-9]+)$/','$1',trim(str_replace('&nbsp;','', $home)))),
                                        'away' => preg_replace('/^([0-9]+)([^\s]+)/','$2',preg_replace('/([^\s]+)([0-9]+)$/','$1',trim(str_replace('&nbsp;','', $away)))),
                                        'tips' => $tips
                                    ];
                                }
                            }
                            if(isset($league['fixtures']) && is_array($league['fixtures']) && count($league['fixtures']) > 0){
                                $league['fixtures'] = array_keys($league['fixtures']);
                            }else{
                                $league['fixtures'] = [];
                            }
                            $leagues[$league_id] = $league;
                        }else{
                            return [
                                'error' => 1,
                                'message' => '#1 Could not crawl round at '. $url
                            ];
                        }
                    }else{
                        return [
                            'error' => 1,
                            'message' => '#2 Could not crawl round at '. $url
                        ];
                    }
                }
            }
            return [
                'error' => 0,
                'data' => $fixture_ids
            ];
        }else{
            return [
                'error' => 1,
                'message' => '#3 Could not crawl round at '. $url
            ];
        }
    }

    public function _date($date = '', $high_priority = false){
        if($date == ''){
            $date = date('d-m-Y', time()+86400);
        }
        // $date = dd-mm-yyyy
        $html = @file_get_html(self::CRAWL_ENPOINT .'/ngay-'. $date .'.html');
        if($html){
            $leagues = [];
            $fixture_ids = [];
            $insert_leagues = [];
            foreach($html->find('table') as $table){
                $title = $table->find('.LTD_table_row_title',0);
                if($title){
                    $league_link = $title->find('a',0);
                    if($league_link){
                        $league_link = parse_url($league_link->href);
                        $league_path = str_replace(['/','.html'], '', $league_link['path']);
                        $league_title = $title->find('h2 > a',0);
                        if($league_title){
                            $league_title = trim($league_title->innertext);
                            $league_id = explode('-', $league_path);
                            $league_id = $league_id[count($league_id)-1];
                            $league = [
                                'id' => $league_id,
                                'title' => $league_title,
                                'icon' => 'icon-'. $league_id .'.png',
                                'fixtures' => []
                            ];
                            $insert_leagues[] = '("'. $league_id .'","'. addslashes($league_title) .'","0","0")';
                            if($table->find('.LTD_table_row_1')){
                                foreach($table->find('.LTD_table_row_1') as $match){
                                    $link = parse_url($match->find('.LTD_tabale_col_2 a',0)->href);
                                    $link = $link['path'];
                                    preg_match('/\-([0-9]+)\.html/', $link, $match_id);
                                    list($home, $away) = explode(' vs ', $match->find('.LTD_tabale_col_2',0)->text());
                                    $status = $match->find('.LTD_tabale_col_1',0)->text();
                                    $status = explode(' ', preg_replace('/(\s)+/',' ', str_replace('&nbsp;',' ', $status)));
                                    if(count(explode('-', $status[1])) == 2){
                                        list($home_goal, $away_goal) = explode('-', $status[1]);
                                    }else{
                                        $home_goal = '';
                                        $away_goal = '';
                                    }
                                    if(in_array($status[0], ['FT','H1','H2','PEN'])){
                                        $home_goal = $status[1];
                                        $away_goal = $status[3];
                                    }
                                    $start_time = 0;
                                    if(count(explode('h', $status[1])) == 2){
                                        $time = $status[0] .' '. $status[1];
                                        $year = explode('-', $date);
                                        $year = $year[2];
                                        list($day, $month) = explode('/', $status[0]);
                                        list($hour, $minute) = explode('h', $status[1]);
                                        $start_time = mktime($hour, $minute, 0, $month, $day, $year);
                                    }else{
                                        $time = $status[0];
                                    }

                                    if($status[1] == 'Hoãn'){
                                        $time = 'Hoãn';
                                    }

                                    $tips = [
                                        'asia' => [
                                            'total' => trim(str_replace('&nbsp;',' ', $match->find('.LTD_tabale_col_3', 0)->text())),
                                            'home' => trim(str_replace('&nbsp;',' ', $match->find('.LTD_tabale_col_4', 0)->text())),
                                            'away' => trim(str_replace('&nbsp;',' ', $match->find('.LTD_tabale_col_4', 1)->text()))
                                        ],
                                        'taixiu' => [
                                            'total' => trim(str_replace('&nbsp;',' ', $match->find('.LTD_tabale_col_3', 2)->text())),
                                            'home' => trim(str_replace('&nbsp;',' ', $match->find('.LTD_tabale_col_4', 4)->text())),
                                            'away' => trim(str_replace('&nbsp;',' ', $match->find('.LTD_tabale_col_4', 5)->text()))
                                        ]
                                    ];

                                    $league['fixtures'][$match_id[1]] = $match_id[1];
                                    /*$league['fixtures'][$match_id[1]] = [
                                        'id' => $match_id[1],
                                        'time' => $time,
                                        'home_goal' => $home_goal,
                                        'away_goal' => $away_goal,
                                        'home' => trim(str_replace('&nbsp;','', $home)),
                                        'away' => trim(str_replace('&nbsp;','', $away)),
                                        'tips' => $tips
                                    ];*/
                                    $fixture_ids[$match_id[1]] = [
                                        'id' => $match_id[1],
                                        'time' => $time,
                                        'start_time' => $start_time,
                                        'home_goal' => $home_goal,
                                        'away_goal' => $away_goal,
                                        'home' => trim(str_replace('&nbsp;','', $home)),
                                        'away' => trim(str_replace('&nbsp;','', $away)),
                                        'tips' => $tips
                                    ];
                                }
                            }
                            if(isset($league['fixtures']) && is_array($league['fixtures']) && count($league['fixtures']) > 0){
                                $league['fixtures'] = array_keys($league['fixtures']);
                            }else{
                                $league['fixtures'] = [];
                            }
                            $leagues[$league_id] = $league;
                        }else{
                            return [
                                'error' => 1,
                                'message' => '#1 Could not crawl schedule at '. $date
                            ];
                        }
                    }else{
                        return [
                            'error' => 1,
                            'message' => '#2 Could not crawl schedule at '. $date
                        ];
                    }
                }
            }
            $this->_getDb()->query('INSERT INTO #__football_schedules(`date`,`leagues`) VALUES("'. $date .'","'. addslashes(json_encode($leagues)) .'") ON DUPLICATE KEY UPDATE `leagues`=VALUES(`leagues`)');
            // insert matches
            if(count($fixture_ids) > 0){
                $matches = [];
                foreach($fixture_ids as $fixture){
                    $matches[] = '("'. $fixture['id'] .'","'. $fixture['time'] .'","'. $fixture['start_time'] .'","","","","","","","0","'. time() .'","'. ($high_priority?1:0) .'")';
                }
                $this->_getDb()->query("INSERT INTO #__football_matches (`match_id`,`time`,`start_time`,`h1`,`home`,`away`,`events`,`statics`,`tips`,`finish`,`created_at`,`priority`) VALUES". implode(',', $matches) ." ON DUPLICATE KEY UPDATE `priority`=VALUES(`priority`)");
            }
            if(count($insert_leagues) > 0){
                $this->_getDb()->query("INSERT INTO #__football_leagues (`league_id`,`title`,`has_round`,`finish`) VALUES". implode(',', $insert_leagues) ." ON DUPLICATE KEY UPDATE `title`=VALUES(`title`)");
            }
            return [
                'error' => 0,
                'data' => $leagues
            ];
        }else{
            return [
                'error' => 1,
                'message' => '#3 Could not crawl schedule at '. $date
            ];
        }
    }

    public function date($date = ''){
        if($date == ''){
            $date = date('d-m-Y', time()+86400*7);
        }
        $exist = $this->_getDb()->fetchRow("SELECT * FROM #__football_schedules WHERE `date`='". $date ."'");
        if($exist){
            return [
                'error' => 0,
                'no_match' => 1,
                'data' => json_decode(str_replace(["\'",'\\u'],["'",'\u'],$exist['leagues']), true)
            ];
        }else{
            return $this->_date($date);
        }
    }

    public function today(){
        if(date('H') > 6){
            $date = date('d-m-Y');
        }else{
            $date = date('d-m-Y', time() - 86400);
        }
        $result = $this->_date($date, true);
        if($result['error'] == 0){
            if(is_array($result['data']) && count($result['data']) > 0){
                $fixture_ids = [];
                foreach($result['data'] as $league){
                    $fixture_ids = array_merge($fixture_ids, $league['fixtures']);
                }
                return $this->_matches(array_unique($fixture_ids));
            }
        }else{
            return [
                'error' => 1,
                'message' => 'Could not crawl date '. $date
            ];
        }
    }

    public function match(){
        $not_finish = $this->_getDb()->fetchAll("SELECT `match_id` FROM #__football_matches WHERE `finish`=0 AND (`error`<3 OR `priority`=1) AND (`has_info`=0 OR (`has_tips`=0 AND `start_time`>'". time() ."' AND `start_time`<'". (time()+172800) ."')) ORDER BY `priority` DESC, `id` ASC LIMIT 0,30");
        if($not_finish){
            $fixture_ids = [];
            foreach ($not_finish as $item){
                $fixture_ids[] = $item['match_id'];
            }
            $result = $this->_matches($fixture_ids);
            return $result;
        }else{
            return [
                'error' => 0,
                'no_match' => 1,
                'message' => 'No match'
            ];
        }
    }

    public function remain_match(){
        $not_finish = $this->_getDb()->fetchAll("SELECT `match_id` FROM #__football_matches WHERE `has_info`=0 AND (`error`<10 OR `priority`=1) ORDER BY `priority` ASC, `start_time` DESC, `id` DESC LIMIT 0,30");
        if($not_finish){
            $fixture_ids = [];
            foreach ($not_finish as $item){
                $fixture_ids[] = $item['match_id'];
            }
            $result = $this->_matches($fixture_ids);
            return $result;
        }else{
            return [
                'error' => 0,
                'no_match' => 1,
                'message' => 'No match'
            ];
        }
    }

    public function not_finish(){
        $not_finish = $this->_getDb()->fetchAll("SELECT `match_id` FROM #__football_matches WHERE `finish`=0 AND `has_info`=1 AND (`error`<10 OR `priority`=1) ORDER BY `priority` ASC, `start_time` ASC, `id` DESC LIMIT 0,30");
        if($not_finish){
            $fixture_ids = [];
            foreach ($not_finish as $item){
                $fixture_ids[] = $item['match_id'];
            }
            $result = $this->_matches($fixture_ids);
            return $result;
        }else{
            return [
                'error' => 0,
                'no_match' => 1,
                'message' => 'No match'
            ];
        }
    }

    public function live(){
        $max_match_time = 150*60;
        $live = $this->_getDb()->fetchAll("SELECT `match_id` FROM #__football_matches WHERE `finish`=0 AND `start_time` < '". time() ."' AND `start_time` > '". (time()-$max_match_time) ."'");
        if($live){
            $fixture_ids = [];
            foreach ($live as $item){
                $fixture_ids[] = $item['match_id'];
            }
            $result = $this->_matches($fixture_ids);
            return $result;
        }else{
            return [
                'error' => 0,
                'no_match' => 1,
                'message' => 'No match'
            ];
        }
    }

    public function league(){
        $leagues = $this->_getDb()->fetchAll("SELECT `league_id` FROM #__football_leagues ORDER BY `updated_at` ASC, `id` ASC LIMIT 0,30");
        if(is_array($leagues) && count($leagues) > 0){
            $league_ids = [];
            foreach($leagues as $item){
                $league_ids[] = $item['league_id'];
                $rounds = $this->_league($item['league_id']);
                if($rounds['error'] == 0){
                    if(isset($rounds['data']['rounds']) && is_array($rounds['data']['rounds']) && count($rounds['data']['rounds']) > 0){
                        $round_insert = [];
                        foreach($rounds['data']['rounds'] as $r){
                            $round_insert[] = '("'. $item['league_id'] .'","'. addslashes($this->_titleBeautify($r['title'])) .'","'. $r['url'] .'","","'. $r['sort_order'] .'","'. $r['is_last'] .'","'. $r['is_current'] .'","0","0")';
                        }
                        $this->_getDb()->query("INSERT INTO #__football_rounds(`league_id`,`title`,`url`,`fixtures`,`sort_order`,`is_last`,`is_current`,`has_info`,`finish`) VALUES". implode(',', $round_insert) ." 
                        ON DUPLICATE KEY UPDATE 
                        `title`=VALUES(`title`),
                        `url`=VALUES(`url`),
                        `sort_order`=VALUES(`sort_order`),
                        `is_last`=VALUES(`is_last`),
                        `is_current`=VALUES(`is_current`)");
                    }
                    if(isset($rounds['data']['standings']) && is_array($rounds['data']['standings']) && count($rounds['data']['standings']) > 0){
                        $this->_getDb()->update('#__football_leagues',['standings' => json_encode($rounds['data']['standings'])],"`league_id` ='". $item['league_id'] ."'");
                    }
                }
            }
            $this->_getDb()->update('#__football_leagues',['has_round' => 1,'updated_at' => time()],'`league_id` IN ('. Mava_String::doImplode($league_ids) .')');
            return [
                'error' => 0,
                'message' => 'Done'
            ];
        }else{
            return [
                'error' => 0,
                'no_match' => 1,
                'message' => 'No league'
            ];
        }
    }

    public function round(){
        $rounds = $this->_getDb()->fetchAll("SELECT `id`,`url` FROM #__football_rounds WHERE `has_info`=0 ORDER BY `last_run` ASC, `id` ASC LIMIT 0,30");
        if(is_array($rounds) && count($rounds) > 0){
            $round_ids = [];
            foreach($rounds as $item){
                $round_ids[] = $item['id'];
                $fixtures = $this->_round($item['url']);
                if($fixtures['error'] == 0 && isset($fixtures['data']) && is_array($fixtures['data']) && count($fixtures['data']) > 0){
                    // insert matches
                    $fixture_ids = $fixtures['data'];
                    if(count($fixture_ids) > 0){
                        $matches = [];
                        foreach($fixture_ids as $fixture){
                            $matches[] = '("'. $fixture['id'] .'","'. $fixture['time'] .'","'. $fixture['start_time'] .'","","","","","","","0","'. time() .'")';
                        }
                        $this->_getDb()->query("INSERT IGNORE INTO #__football_matches (`match_id`,`time`,`start_time`,`h1`,`home`,`away`,`events`,`statics`,`tips`,`finish`,`created_at`) VALUES". implode(',', $matches));
                        $this->_getDb()->update('#__football_rounds',['has_info' => 1,'last_run' => time(), 'fixtures' => trim(json_encode(array_keys($fixture_ids)))],"`id`='". $item['id'] ."'");
                    }
                }else{
                    $this->_getDb()->update('#__football_rounds',['last_run' => time()],"`id`='". $item['id'] ."'");
                }
            }
            return [
                'error' => 0,
                'message' => 'Done'
            ];
        }else{
            return [
                'error' => 0,
                'no_match' => 1,
                'message' => 'No round'
            ];
        }
    }

    public function remain_date(){
        $date = $this->_getDb()->fetchAll("SELECT * FROM #__football_schedule_queue ORDER BY `id` DESC LIMIT 0,30");
        if(is_array($date) && count($date) > 0){
            $results = [];
            $ids = [];
            $dates = [];
            foreach($date as $item){
                $ids[] = $item['id'];
                $dates[] = $item['date'];
                $results[] = $this->_date($item['date'], true);
            }
            $this->_getDb()->query("DELETE FROM #__football_schedule_queue WHERE `id` IN(". Mava_String::doImplode($ids) .")");
            return [
                'error' => 0,
                'data' => $dates
            ];
        }else{
            return [
                'error' => 0,
                'no_match' => 1,
                'message' => 'No date queue'
            ];
        }
    }

    public function _titleBeautify($title){
        $replace = [
            'Vòng Tu Ket' => 'Vòng Tứ Kết',
            'Vòng Ban Ket' => 'Vòng Bán Kết',
            'Vòng Chung Ket' => 'Vòng Chung Kết',
            'Vòng Vong Loai' => 'Vòng Loại'
        ];
        if(isset($replace[$title])){
            return $replace[$title];
        }else{
            return $title;
        }
    }
}