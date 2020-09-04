<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 4/10/14
 * Time: 3:49 PM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Language {
    public static function getAllPhraseInLanguage($languageID){
        $db = Mava_Application::getDb();
        $phrase = $db->query("SELECT * FROM #__phrase_compiled WHERE `language_id`='". $languageID ."'");
        $phraseArray = array();
        if($phrase->num_rows > 0){
            foreach($phrase->rows as $item){
                $phraseArray[$item['title']] = $item['phrase_text'];
            }
            return $phraseArray;
        }else{
            return array();
        }
    }

    public static function getByCode($code){
        return Mava_Application::getDb()->fetchRow("SELECT * FROM #__language WHERE `language_code`='". $code ."'");
    }
}