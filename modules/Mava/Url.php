<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 3/18/14
 * Time: 10:26 AM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Url {
    public static function addParam($base_url, $params){
        if($base_url == ""){
            $url = self::getCurrentAddress();
        }else{
            $url = $base_url;
        }
        $segments = explode('?', $url);
        if(count($segments) > 1){
            parse_str($segments[1], $current_params);
            foreach($params as $k => $v){
                $current_params[$k] = $v;
            }
            $query = $current_params;
        }else{
            $query = $params;
        }

        $final_params = array();
        if(count($query) > 0){
            foreach($query as $k => $v){
                if(is_array($v)){
                    foreach($v as $i){
                        $final_params[] = $k ."[]=". $i;
                    }
                }else{
                    $final_params[] = $k ."=". $v;
                }
            }
        }

        return $segments[0] . (count($final_params) > 0?"?". implode('&', $final_params):"");
    }

    public static function removeParam($url, $params){
        if($url == ''){
            $url = self::getCurrentAddress();
        }
        $segments = explode('?', $url);
        $query = array();
        if(count($segments) > 1){
            parse_str($segments[1], $current_params);
            foreach($params as $k){
                if(isset($current_params[$k])){
                    unset($current_params[$k]);
                }
            }
            $query = $current_params;
        }

        $final_params = array();
        if(count($query) > 0){
            foreach($query as $k => $v){
                if(is_array($v)){
                    foreach($v as $i){
                        $final_params[] = $k ."[]=". $i;
                    }
                }else{
                    $final_params[] = $k ."=". $v;
                }
            }
        }

        return $segments[0] . (count($final_params) > 0?"?". implode('&', $final_params):"");
    }
    public static function responseGifNull(){
        header('Content-Type: image/gif');
        die(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='));
    }
    public static function redirect($url){
        if($url != ""){
            @header("Location: ". $url);
        }else{
            @header("Location: ". self::getDomainUrl());
        }
        die;
    }

    public static function redirectLogin($message){
        if(self::isAjax()){
            echo json_encode(array(
                'status' => -1,
                'message' => __('login_expired')
            ));
            die("");
        }else{
            Mava_Application::getSession()->set('login_return_url', Mava_Url::getCurrentAddress());
            if($message != ""){
                Mava_Application::getSession()->set('login_message', $message);
            }
            @header("Location: ". self::getPageLink('login'));
        }
    }

    public static function getDomainUrl(){
        if(Mava_Application::getConfig('domain') != ""){
            return Mava_Application::getConfig('domain');
        }else{
            return Mava_Application::get("options")->domainUrl;
        }
    }

    public static function getPageLink($alias = '', $params = array()){
        $queryString = '';
        $segment = array();
        if(count($params) > 0){
            foreach($params as $k => $v){
                $segment[] = urlencode($k) .'='. urlencode($v);
            }
            $queryString = '?'. implode('&', $segment);
        }
        return self::getDomainUrl() .'/'. trim($alias, '/') . $queryString;
    }

    public static function buildLink($moduleControllerAction,$params = array()){
        $queryString = '';
        $segment = array();
        if(sizeof($params) > 0){
            foreach($params as $k => $v){
                $segment[] = urlencode($k) .'='. urlencode($v);
            }
            $queryString = '?'. implode('&', $segment);
        }
        return self::getDomainUrl() .'/'. $moduleControllerAction . $queryString;
    }

    public static function isPost(){
        $json = json_decode(file_get_contents('php://input'), true);
        if(sizeof($_POST) > 0 || (is_array($json) && count($json) > 0)){
            return true;
        }else{
            return false;
        }
    }

    public static function isAjax(){
        if(self::getParam('_requestType') == 'ajax' || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')){
            return true;
        }else{
            return false;
        }
    }

    public static function isJson(){
        if(self::getParam('_responseType') == 'json'){
            return true;
        }else{
            return false;
        }
    }

    public static function getCSRF(){
        return self::getParam('_CSRF');
    }

    public static function isValidCSRF(){
        if(self::getCSRF() != Mava_Session::getCSRF()){
            return false;
        }else{
            return true;
        }
    }

    public static function getRequestPath(){
        $currentAddress = self::getCurrentAddress();
        $path = substr($currentAddress, strlen(self::getDomainUrl()));
        $result = '';
        if (preg_match('#^/([^?]+)(\?|$)#U', $path, $match))
        {
            $result = urldecode($match[1]);
        }
        else if (preg_match('#\?([^=&]+)(&|$)#U', $path, $match))
        {
            $result = urldecode($match[1]);
        }
        if ($result !== null)
        {
            return ltrim($result, '/');
        }

        return '';
    }

    public static function getParams(){
        $params = self::getQueryString();
        if(is_array($params)){
            $params = array_merge($params,$_POST);
        }else{
            $params = $_POST;
        }

        $json = json_decode(file_get_contents('php://input'), true);
        if(is_array($json) && count($json) > 0){
            $params = array_merge($params,$json);
        }
        $currents = Mava_Application::get('request/params');
        if(is_array($currents) && count($currents) > 0){
            $params = array_merge($params, $currents);
        }
        return $params;
    }

    public static  function initParams(){
        $params = self::getParams();
        if(is_array($params)){
            Mava_Application::set('request/params',$params);
            Mava_Application::set('requestPaths',self::getRequestPath());
        }
    }

    public static function getQueryString(){
        $queryString = Mava_Application::get('request/querystring');
        if(is_array($queryString) && count($queryString) > 0){
            return $queryString;
        }else{
            $currentAddress = self::getCurrentAddress();
            $check = explode('?',$currentAddress);
            if(sizeof($check) > 1){
                $queryString = explode('#',$check[1]);
                $queryString = $queryString[0];
                parse_str($queryString, $output);
                if(sizeof($output) > 0){
                    Mava_Application::set('request/querystring',$output);
                    return $output;
                }else{
                    return array();
                }
            }else{
                return array();
            }
        }
    }

    public static function getParam($key){
        if($key!=""){
            return Mava_Application::get('request/params/'. $key);
        }else{
            return false;
        }
    }

    public static function setParam($key,$value){
        if($key!=""){
            Mava_Application::set('request/params/'. $key,$value);
            return true;
        }else{
            return false;
        }
    }

    public static function delParam($key){
        if($key!=""){
            Mava_Application::remove('request/params/'. $key);
            return true;
        }else{
            return false;
        }
    }

    public static function getCurrentAddress(){
        $address = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['REQUEST_SCHEME'] =='https' || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] =='https') ? 'https://' : 'http://';
        $address .= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : getenv('HTTP_HOST');
        $address .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : getenv('REQUEST_URI');
        return $address;
    }
}