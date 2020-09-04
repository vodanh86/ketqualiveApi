<div class="pg-title user-vip-title">Danh sách trận đấu</div>
<div class="pg-content user-vip-content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-12">
            <?php
            $matchHtml = '';
            if(isset($matchs) && count($matchs) > 0) {
                foreach ($matchs as $match) {
                    if(isset($match['fixtures']) && count($match['fixtures']) > 0){
                        if(isset($match['league']) && count($match['league']) > 0){
                            $matchHtml .= '
                            <div class="match-group">
                                <div class="league-detail">
                                    <p class="league-name">'.$match['league']['name'].'</p>
                                    <p class="match-date">'.$date.'</p>
                                </div>
                            ';
                        }else{
                            $matchHtml .= '
                            <div class="match-group">
                                <div class="league-detail">
                                    <p class="league-name"></p>
                                    <p class="match-date">'.$date.'</p>
                                </div>
                            ';
                        }
                        foreach ($match['fixtures'] as $fixture) {
                            if($fixture['statusShort'] == 'NS'){ // chỉ nhập kèo những trận chưa diễn ra
                                $matchHtml .= '
                                    <div class="match-detail">
                                        <span>'.date('H:i', $fixture['event_timestamp']).'</span><br>
                                        <span class="home-team">'.$fixture['homeTeam']['team_name'].'</span><br>
                                        <i class="home-vs-away">vs</i><br>
                                        <span class="away-team">'.$fixture['awayTeam']['team_name'].'</span><br>
                                        <a href="football-tip?fixture='.$fixture["fixture_id"].'"><span class="btn-add-tip"><i class="fa fa-plus-square" aria-hidden="true"></i></span></a>
                                    </div>
                                    ';
                            }
                        }
                    }
                    $matchHtml .= '</div>';
                }
            }
            echo $matchHtml;
            ?>
        </div>
    </div>
</div>