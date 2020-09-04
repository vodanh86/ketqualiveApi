<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 3/19/14
 * Time: 11:45 AM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Phrase {
    public static function getPhrase($key, $params = array(), $returnKeyIfNotExist = true){
        $text = Mava_Application::get('phrases/'. $key);
        $options = Mava_Application::getOptions();
        if($text != ""){
            if(sizeof($params) > 0){
                foreach($params as $k => $v){
                    $text = str_replace('{'. $k .'}',$v,$text);
                }
            }
            $text = str_replace('{_email_support}', $options->email_support, $text);
            $text = str_replace('{_phone_support}', $options->phone_support, $text);
            $text = str_replace('{domain}', Mava_Url::getDomainUrl(), $text);
            $text = str_replace('{site_name}', $options->siteName, $text);
            $text = str_replace('{email_signature}', nl2br($options->emailSignature), $text);
            unset($options);
            return $text;
        }else{
            if($returnKeyIfNotExist){
                return $key;
            }else{
                return '';
            }
        }
    }
}