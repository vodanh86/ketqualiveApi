<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 3/18/14
 * Time: 3:33 PM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Session {

    public function start(){
        if($this->get('user_id') > 0){
            Mava_Visitor::setup($this->get('user_id'));
        }else{
            Mava_Visitor::setup(0);
        }
    }

    public static function set($key,$value){
        $_SESSION[$key] = $value;
    }

    public function setMulti($data = array()){
        if(sizeof($data) > 0){
            foreach($data as $k => $v){
                $_SESSION[$k] = $v;
            }
        }
    }

    public static function get($key){
        if(isset($_SESSION[$key])){
            return $_SESSION[$key];
        }else{
            return false;
        }
    }

    public function remove($key){
        $this->delete($key);
    }

    public static function delete($key){
        $_SESSION[$key] = null;
        if(isset($_SESSION[$key])){
            unset($_SESSION[$key]);
        }
    }

    public static function getCSRF(){
        return md5(session_id());
    }
}