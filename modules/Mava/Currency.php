<?php
class Mava_Currency {
    public static function getByCode($code){
        return Mava_Application::getDb()->fetchRow("SELECT * FROM #__currency WHERE `currency_code`='". $code ."'");
    }
}