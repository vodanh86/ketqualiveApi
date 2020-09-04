<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 9/29/14
 * @Time: 11:06 AM
 */
class Mava_Helper_CURL {
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';
    const METHOD_PUT = 'put';
    const METHOD_DELETE = 'delete';

    public static function call($method = self::METHOD_GET, $input_url = '', $data = null, $timeout = 10){
        if(is_array($data) && sizeof($data) && $method == self::METHOD_GET){
            $queryString = array();
            foreach($data as $k => $v){
                $queryString[] = $k .'='. $v;
            }

            $queryString = implode('&', $queryString);
            $input_url .= (strpos($input_url,'?')!==false?'&':'?') . $queryString;
        }
        $url = $input_url;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
        if($method==self::METHOD_POST){
            curl_setopt($curl, CURLOPT_POST, 1);
        }else if($method==self::METHOD_PUT){
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        }else if($method==self::METHOD_DELETE){
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        if($data && $method != self::METHOD_GET){
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        $result = curl_exec($curl);
        $curl_info = curl_getinfo($curl);
        curl_close($curl);
        return $result;
    }
}