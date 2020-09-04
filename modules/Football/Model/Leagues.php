<?php
class Football_Model_Leagues extends Mava_Model {
    public function getById($id){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__football_leagues WHERE `id`='". (int)$id ."'");
        }else{
            return false;
        }
    }

    public function getByLeagueId($id){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__football_leagues WHERE `league_id`='". (int)$id ."'");
        }else{
            return false;
        }
    }

    public function recache($leagues = []){
        $sql = [];
        foreach($leagues as $item){
            $sql[] = "('". $item['league_id'] ."','". addslashes($item['name']) ."','". addslashes($item['country']) ."','". $item['country_code'] ."','". $item['season'] ."','". strtotime($item['season_start']) ."','". strtotime($item['season_end']) ."','". $item['logo'] ."','". $item['flag'] ."','". $item['standings'] ."','". $item['is_current'] ."')";
        }
        $db = $this->_getDb();
        $db->query("DELETE FROM #__football_leagues");
        $db->query("INSERT INTO #__football_leagues(`league_id`,`name`,`country`,`country_code`,`season`,`season_start`,`season_end`,`logo`,`flag`,`standings`,`is_current`) VALUES". implode(",", $sql));
    }

    public function getByLeagueIds($ids){
        if(is_array($ids) && count($ids) > 0){
            return $this->_getDb()->fetchAll("SELECT * FROM #__football_leagues WHERE `league_id` IN(". Mava_String::doImplode($ids) .")");
        }else{
            return false;
        }
    }

    public function getByLeagueCountryConfig(){
         return $this->_getDb()->fetchAll("SELECT * FROM #__football_leagues WHERE country IN (". Mava_String::doImplode(Mava_Application::getConfig('league_country')) .") AND is_current=1");
    }
}