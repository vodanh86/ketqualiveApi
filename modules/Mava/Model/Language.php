<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 4/8/14
 * Time: 9:21 PM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Model_Language extends Mava_Model {

    public function editPhrase($currentPhrase, $languageID, $phraseTitle, $phraseText, $addOn){
        $db = $this->_getDb();
        if($languageID==0){
            $db->query("UPDATE #__phrase SET `title`='". addslashes($phraseTitle) ."', `phrase_text`='". addslashes($phraseText) ."', `addon_id`='". addslashes($addOn) ."' WHERE `phrase_id`='". $currentPhrase['phrase_id'] ."'");
            if($currentPhrase['title']!=$phraseTitle || $currentPhrase['phrase_text']!=$phraseText){  // has change
                $phraseMap = $db->query("SELECT * FROM #__phrase_map WHERE `phrase_id`='". $currentPhrase['phrase_id'] ."'");
                if($phraseMap->num_rows > 0){
                    $db->query("UPDATE #__phrase_map SET `title`='". addslashes($phraseTitle) ."' WHERE `phrase_id`='". $currentPhrase['phrase_id'] ."'");
                    foreach($phraseMap->rows as $item){
                        $db->query("UPDATE #__phrase_compiled SET `phrase_text`='". addslashes($phraseText) ."' WHERE `title`='". addslashes($currentPhrase['title']) ."' AND `language_id`='". $item['language_id'] ."'");
                        $this->reCacheLanguage($item['language_id']);
                    }
                }
            }
        }else{
            if($currentPhrase['phrase_state']=='default'){
                if($currentPhrase['title']!=$phraseTitle || $currentPhrase['phrase_text']!=$phraseText){  // has change
                    // insert new phrase
                    $newPhraseID = $db->add('#__phrase', array(
                        'language_id' => $languageID,
                        'title' => $phraseTitle,
                        'phrase_text' => $phraseText,
                        'addon_id' => $addOn
                    ));
                    if($newPhraseID > 0){
                        if($currentPhrase['title']!=$phraseTitle){
                            // new phrase
                            $db->add('#__phrase_map',array(
                                'language_id' => $languageID,
                                'title' => $phraseTitle,
                                'phrase_id' => $newPhraseID
                            ));
                            $db->add('#__phrase_compiled',array(
                                'language_id' => $languageID,
                                'title' => $phraseTitle,
                                'phrase_text' => $phraseText
                            ));
                        }else{
                            $db->query("UPDATE #__phrase_map SET `phrase_id`='". $newPhraseID ."' WHERE `title`='". addslashes($currentPhrase['title']) ."' AND `language_id`='". $languageID ."'");
                            $db->query("UPDATE #__phrase_compiled SET `phrase_text`='". addslashes($phraseText) ."' WHERE `title`='". addslashes($currentPhrase['title']) ."' AND `language_id`='". $languageID ."'");
                        }
                    }else{
                        return false;
                    }
                }
            }else{
                // update custom phrase (phrase, map, compiled)
                if($currentPhrase['title']!=$phraseTitle){
                    $newPhraseID = $db->add('#__phrase', array(
                        'language_id' => $languageID,
                        'title' => $phraseTitle,
                        'phrase_text' => $phraseText,
                        'addon_id' => $addOn
                    ));
                    if($newPhraseID > 0){
                        $db->add('#__phrase_map',array(
                            'language_id' => $languageID,
                            'title' => $phraseTitle,
                            'phrase_id' => $newPhraseID
                        ));
                        $db->add('#__phrase_compiled',array(
                            'language_id' => $languageID,
                            'title' => $phraseTitle,
                            'phrase_text' => $phraseText
                        ));
                    }else{
                        return false;
                    }

                }else{
                    $db->query("UPDATE #__phrase SET `phrase_text`='". addslashes($phraseText) ."',`addon_id`='". addslashes($addOn) ."' WHERE `title`='". addslashes($currentPhrase['title']) ."' AND `language_id`='". $languageID ."'");
                    $db->query("UPDATE #__phrase_compiled SET `phrase_text`='". addslashes($phraseText) ."' WHERE `title`='". addslashes($currentPhrase['title']) ."' AND `language_id`='". $languageID ."'");
                }
            }
            $this->reCacheLanguage($languageID);
        }
        return true;
    }

    public function isPhraseExist($title, $languageID, $excludedTitle = array()){
        $whereExcluded = '';
        if(sizeof($excludedTitle) > 0){
            $whereExcluded = " AND `title` NOT IN (". Mava_String::doImplode($excludedTitle) .")";
        }
        $phrase = $this->_getDb()->query("SELECT COUNT(*) AS 'total' FROM #__phrase_map WHERE `language_id`='". (int)$languageID ."' ". $whereExcluded ." AND `title`='". addslashes($title) ."'");
        if($phrase->row['total'] > 0){
            return true;
        }else{
            return false;
        }
    }

    public function addPhrase($languageID, $phraseTitle, $phraseText, $addOn = ''){
        $db = $this->_getDb();
        $phraseID = $db->add('#__phrase', array(
            'language_id' => $languageID,
            'title' => $phraseTitle,
            'phrase_text' => $phraseText,
            'addon_id' => $addOn
        ));
        if($phraseID > 0){
            $db->add('#__phrase_map',array(
                'language_id' => $languageID,
                'title' => $phraseTitle,
                'phrase_id' => $phraseID
            ));
            $db->add('#__phrase_compiled',array(
                'language_id' => $languageID,
                'title' => $phraseTitle,
                'phrase_text' => $phraseText
            ));
            $this->reCacheLanguage($languageID);
            if($languageID==0){
                // master language
                $languages = $this->getListLanguage();
                if(sizeof($languages) > 0){
                    foreach($languages as $lang){
                        if(!$this->isPhraseExist($phraseTitle, $lang['language_id'])){
                            $db->add('#__phrase_map',array(
                                'language_id' => $lang['language_id'],
                                'title' => $phraseTitle,
                                'phrase_id' => $phraseID
                            ));
                            $db->add('#__phrase_compiled',array(
                                'language_id' => $lang['language_id'],
                                'title' => $phraseTitle,
                                'phrase_text' => $phraseText
                            ));
                            $this->reCacheLanguage($lang['language_id']);
                        }
                    }
                }
            }
            return $phraseID;
        }else{
            return false;
        }
    }

    public function deletePhrase($phraseID, $languageID){
        $db = $this->_getDb();
        $db->delete("#__phrase","`phrase_id`='". (int)$phraseID ."' AND `language_id`='". (int)$languageID ."'");
        if($languageID==0){
            $phraseMap = $db->query("SELECT * FROM #__phrase_map WHERE `phrase_id`='". (int)$phraseID ."'");
            if($phraseMap->num_rows > 0){
                $db->delete("#__phrase_map","`phrase_id`='". (int)$phraseID ."'");
                foreach($phraseMap->rows as $item){
                    $db->delete("#__phrase_compiled","`title`='". addslashes($item['title']) ."' AND `language_id`='". $item['language_id'] ."'");
                    $this->reCacheLanguage($item['language_id']);
                }
            }
        }else{
            $phraseMap = $db->query("SELECT * FROM #__phrase_map WHERE `phrase_id`='". (int)$phraseID ."' AND `language_id`='". (int)$languageID ."'");
            if($phraseMap->num_rows > 0){
                $db->delete("#__phrase_map","`phrase_id`='". (int)$phraseID ."' AND `language_id`='". (int)$languageID ."'");
                foreach($phraseMap->rows as $item){
                    $db->delete("#__phrase_compiled","`title`='". addslashes($item['title']) ."' AND `language_id`='". $item['language_id'] ."'");
                    $masterPhrase = $this->getPhraseByTitle($item['title'],0);
                    if($masterPhrase){
                        $db->add('#__phrase_map',array(
                            'language_id' => $languageID,
                            'title' => $item['title'],
                            'phrase_id' => $masterPhrase['phrase_id']
                        ));
                        $db->add('#__phrase_compiled',array(
                            'language_id' => $languageID,
                            'title' => $item['title'],
                            'phrase_text' => $masterPhrase['phrase_text']
                        ));
                    }
                    $this->reCacheLanguage($item['language_id']);
                }
            }
        }
        return true;
    }

    public function reCacheLanguage($languageID){
        Mava_Application::delCache('language_phrase_'. $languageID);
        $phrases = Mava_Language::getAllPhraseInLanguage($languageID);
        if(!is_array($phrases)){
            $phrases = array();
        }
        Mava_Application::setCache('language_phrase_'. $languageID,$phrases,86400*30);
        return true;
    }

    public function getListLanguage(){
        $db = $this->_getDb();
        $language = $db->query("SELECT * FROM #__language ORDER BY `language_id` ASC");
        if($language->num_rows > 0){
            return $language->rows;
        }else{
            return array();
        }
    }

    public function getLanguageById($languageID){
        $db = $this->_getDb();
        $language = $db->query("SELECT * FROM #__language WHERE `language_id`='". (int)$languageID ."'");
        if($language->num_rows > 0){
            return $language->row;
        }else{
            return false;
        }
    }

    public function getLanguageByIds($languageIDs){
        if(is_array($languageIDs) && count($languageIDs) > 0){
            return $this->_getDb()->fetchAll("SELECT * FROM #__language WHERE `language_id` IN ('". implode("','", $languageIDs) ."') ORDER BY FIELD(language_id,'". implode("','", $languageIDs) ."')");
        }else{
            return false;
        }
    }

    public function getLanguageByCodes($languageCodes){
        if(is_array($languageCodes) && count($languageCodes) > 0){
            return $this->_getDb()->fetchAll("SELECT * FROM #__language WHERE `language_code` IN ('". implode("','", $languageCodes) ."') ORDER BY FIELD(language_code,'". implode("','", $languageCodes) ."')");
        }else{
            return false;
        }
    }

    public function getPhraseListByLanguageID($languageID = 0, $skip = 0, $limit = 100, $count = true){
        $db = $this->_getDb();
        $result = array(
            'phrase' => array(),
            'total' => 0
        );
        $phrase = $db->query("
            SELECT phrase_map.phrase_map_id,
                phrase_map.language_id AS map_language_id,
                phrase.phrase_id,
                phrase_map.title,
                IF(phrase.language_id = 0, 'default', IF(phrase.language_id = phrase_map.language_id, 'custom', 'inherited')) AS phrase_state,
                IF(phrase.language_id = phrase_map.language_id, 1, 0) AS canDelete,
                addon.addon_id, addon.title AS addonTitle
            FROM #__phrase_map AS phrase_map
            INNER JOIN #__phrase AS phrase ON
                (phrase_map.phrase_id = phrase.phrase_id)
            LEFT JOIN #__addon AS addon ON
                (addon.addon_id = phrase.addon_id)
            WHERE phrase_map.language_id = '". (int)$languageID ."'
            ORDER BY phrase.phrase_id DESC
             LIMIT ". (int)$skip .",". $limit);
        if($count){
            $countPhrase = $db->query("
            SELECT count(phrase.phrase_id) as 'total'
            FROM #__phrase_map AS phrase_map
            INNER JOIN #__phrase AS phrase ON
                (phrase_map.phrase_id = phrase.phrase_id)
            WHERE phrase_map.language_id = '". (int)$languageID ."'");
            $result['total'] = $countPhrase->row['total'];
        }
        $result['phrase'] = $phrase->rows;
        return $result;
    }

    public function getPhraseListInMaster($skip = 0, $limit = 100){
        return $this->getPhraseListByLanguageID(0, $skip, $limit);
    }

    public function searchPhraseList($filterTitle, $languageID, $prefixMatch = 0, $skip = 0, $limit = 100){
        $db = $this->_getDb();
        $searchCond = " AND CONVERT(phrase_map.title USING utf8) LIKE '". ($prefixMatch==1?'':'%'). addslashes(str_replace(array('_','%','?'),array('\\_','\\%','\\?'),$filterTitle)) ."%'";
        $phrase = $db->query("
            SELECT phrase_map.phrase_map_id,
                phrase_map.language_id AS map_language_id,
                phrase.phrase_id,
                phrase_map.title,
                IF(phrase.language_id = 0, 'default', IF(phrase.language_id = phrase_map.language_id, 'custom', 'inherited')) AS phrase_state,
                IF(phrase.language_id = phrase_map.language_id, 1, 0) AS canDelete,
                addon.addon_id, addon.title AS addonTitle
            FROM #__phrase_map AS phrase_map
            INNER JOIN #__phrase AS phrase ON
                (phrase_map.phrase_id = phrase.phrase_id)
            LEFT JOIN #__addon AS addon ON
                (addon.addon_id = phrase.addon_id)
            WHERE phrase_map.language_id = '". (int)$languageID ."' ". $searchCond ."
             LIMIT ". (int)$skip .",". $limit);
        return $phrase->rows;
    }

    public function getPhraseEdit($phraseID, $languageID){
        $db = $this->_getDb();
        $phrase = $db->query("
            SELECT phrase_map.phrase_map_id,
                phrase_map.language_id AS map_language_id,
                phrase.phrase_id,
                phrase_map.title,
                phrase.phrase_text,
                IF(phrase.language_id = 0, 'default', IF(phrase.language_id = phrase_map.language_id, 'custom', 'inherited')) AS phrase_state,
                IF(phrase.language_id = phrase_map.language_id, 1, 0) AS canDelete,
                addon.addon_id, addon.title AS addonTitle
            FROM #__phrase_map AS phrase_map
            INNER JOIN #__phrase AS phrase ON
                (phrase_map.phrase_id = phrase.phrase_id)
            LEFT JOIN #__addon AS addon ON
                (addon.addon_id = phrase.addon_id)
            WHERE phrase_map.language_id = '". (int)$languageID ."' AND phrase_map.phrase_id='". (int)$phraseID ."'");
        if($phrase->num_rows > 0){
            return $phrase->row;
        }else{
            return false;
        }
    }

    /**
     * @param $phraseTitle
     * @param $languageID
     * @return array | bool
     */
    public function getPhraseByTitle($phraseTitle, $languageID){
        $db = $this->_getDb();
        $phrase = $db->query("
            SELECT phrase_map.phrase_map_id,
                phrase_map.language_id AS map_language_id,
                phrase.phrase_id,
                phrase_map.title,
                phrase.phrase_text,
                IF(phrase.language_id = 0, 'default', IF(phrase.language_id = phrase_map.language_id, 'custom', 'inherited')) AS phrase_state,
                IF(phrase.language_id = phrase_map.language_id, 1, 0) AS canDelete,
                addon.addon_id, addon.title AS addonTitle
            FROM #__phrase_map AS phrase_map
            INNER JOIN #__phrase AS phrase ON
                (phrase_map.phrase_id = phrase.phrase_id)
            LEFT JOIN #__addon AS addon ON
                (addon.addon_id = phrase.addon_id)
            WHERE phrase_map.language_id = '". (int)$languageID ."' AND CONVERT(phrase_map.title USING utf8)='". addslashes($phraseTitle) ."'");
        if($phrase->num_rows > 0){
            return $phrase->row;
        }else{
            return false;
        }
    }

    public function getPhraseDefineList($languageID, $skip = 0, $limit = 100){
        $phrase = $this->_getDb()->query("SELECT * FROM #__phrase WHERE `language_id`='' ORDER BY `phrase_id` ASC LIMIT ". $skip .",". $limit);
        return $phrase->rows;
    }

    public function languageCodeExist($code, $excluded_id = 0){
        if($code!=""){
            $lang = $this->_getDb()->query("SELECT COUNT(*) AS 'total' FROM #__language WHERE `language_code`='". addslashes($code) ."' AND `language_id`<>'". (int)$excluded_id ."'");
            if($lang->row['total'] > 0){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function addLanguage($languageTitle, $languageCode, $dateFormat, $timeFormat, $textDirection, $decimalPoint, $thousandsSeparator){
        $db = $this->_getDb();
        $languageID = $db->add('#__language',array(
            'title' => $languageTitle,
            'language_code' => $languageCode,
            'date_format' => $dateFormat,
            'time_format' => $timeFormat,
            'decimal_point' => $decimalPoint,
            'thousands_separator' => $thousandsSeparator,
            'text_direction' => $textDirection
        ));
        if($languageID){
            $hasPhrase = true;
            $step = 0;
            $limit = 1000;
            while($hasPhrase){
                $step++;
                $skip = ($step-1)*$limit;
                $phrase = $this->getPhraseDefineList(0,$skip, $limit);
                if(sizeof($phrase) > 0){
                    $mapValues = array();
                    $compiledValues = array();
                    foreach($phrase as $item){
                        $mapValues[] = "('". $languageID ."','". addslashes($item['title']) ."','".$item['phrase_id']  ."')";
                        $compiledValues[] = "('". $languageID ."','". addslashes($item['title']) ."','". addslashes($item['phrase_text'])  ."')";
                    }
                    $db->query("INSERT IGNORE INTO #__phrase_map(`language_id`,`title`,`phrase_id`) VALUES". implode(',',$mapValues));
                    $db->query("INSERT IGNORE INTO #__phrase_compiled(`language_id`,`title`,`phrase_text`) VALUES". implode(',',$compiledValues));
                }else{
                    $hasPhrase = false;
                }
            }
            $this->reCacheLanguage($languageID);
            return $languageID;
        }else{
            return false;
        }
    }

    public function editLanguage($languageID, $languageTitle, $languageCode, $dateFormat, $timeFormat, $textDirection, $decimalPoint, $thousandsSeparator){
        $db = $this->_getDb();
        if($languageID>0){
            $db->query("UPDATE #__language
            SET
            `title`='". addslashes($languageTitle) ."',
            `language_code`='". addslashes($languageCode) ."',
            `date_format`='". addslashes($dateFormat) ."',
            `time_format`='". addslashes($timeFormat) ."',
            `decimal_point`='". addslashes($decimalPoint) ."',
            `thousands_separator`='". addslashes($thousandsSeparator) ."',
            `text_direction`='". addslashes($textDirection) ."'
             WHERE
             `language_id`='". $languageID ."'");
            return true;
        }else{
            return false;
        }
    }

    public function deleteLanguage($languageID){
        if($languageID > 0){
            $db = $this->_getDb();
            $db->delete('#__phrase',"`language_id`='". $languageID ."'");
            $db->delete('#__phrase_map',"`language_id`='". $languageID ."'");
            $db->delete('#__phrase_compiled',"`language_id`='". $languageID ."'");
            $db->delete('#__language',"`language_id`='". $languageID ."'");
            return true;
        }else{
            return false;
        }
    }

}