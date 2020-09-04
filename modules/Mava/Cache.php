<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 4/10/14
 * Time: 2:46 PM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Cache {
     protected $enable;
     protected $obj;
     protected $_config;
     protected $_engine;

     public function __construct(){
         $this->_config = Mava_Application::get('config/memcache');
         $this->_init();
     }

     private function _init() {
         $config = $this->_config;
         if(!empty($config['server'])) {
             if(class_exists("Memcache")){
                 $this->_engine = 'memcache';
                 $this->obj = new Memcache();
                 if($config['pconnect']) {
                     $connect = @$this->obj->pconnect($config['server'], $config['port']);
                 }
                 if(!isset($connect) || !$connect){
                     $connect = @$this->obj->connect($config['server'], $config['port']);
                 }
             }elseif(class_exists("Memcached")){
                 $this->_engine = 'memcached';
                 $this->obj = new Memcached();
                 $connect = @$this->obj->addServer($config['server'], $config['port']);
             }else{
                 $connect = false;
             }
             $this->enable = $connect ? true : false;
         }
     }

     public function get($key) {
         if($this->enable){
             return $this->obj->get($this->_config['prefix'] .'_'. $key);
         }else{
             return false;
         }
     }

     public function set($key, $value, $life_second = 0) {
         if($this->enable){
             if($this->_engine === "memcache"){
                 return $this->obj->set($this->_config['prefix'] .'_'. $key, $value, MEMCACHE_COMPRESSED, $life_second);
             }else{
                 return $this->obj->set($this->_config['prefix'] .'_'. $key, $value, $life_second);
             }
         }else{
             return false;
         }
     }

     public function rm($key) {
         if($this->enable){
             return $this->obj->delete($this->_config['prefix'] .'_'. $key);
         }else{
             return false;
         }
     }

     public function remove($key) {
         return $this->rm($key);
     }
 }
