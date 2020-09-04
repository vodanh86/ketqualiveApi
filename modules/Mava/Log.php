<?php
class Mava_Log {
    public static function info($message, $file = 'logs/info.log'){
        try{
        if(!is_string($message)){
            $message = json_encode($message);
        }
        $path = explode('/', $file);
        $fileName = end($path);
        array_pop($path);
        $dir = BASEDIR;
        foreach($path as $p){
            $dir .= "/". $p;
            !is_dir($dir) && mkdir($dir, 0777);
        }
        $fullPath = $dir .'/'. date('Y-m-d') .'-'. $fileName;
        $fp = fopen($fullPath, "a");
        fwrite($fp, "[ ". date('Y-m-d H:i:s') .' ] [ INFO ] '. $message ."\r\n");
        fclose($fp);
        }catch(Exception $e){
            //TODO
	}
    }

    public static function error($message, $file = 'logs/error.log'){
        try{
        if(!is_string($message)){
            $message = json_encode($message);
        }
        $path = explode('/', $file);
        $fileName = end($path);
        array_pop($path);
        $dir = BASEDIR;
        foreach($path as $p){
            $dir .= "/". $p;
            !is_dir($dir) && mkdir($dir, 0777);
        }
        $fullPath = $dir .'/'. date('Y-m-d') .'-'. $fileName;
        $fp = fopen($fullPath, "a");
        fwrite($fp, "[ ". date('Y-m-d H:i:s') .' ] [ ERROR ] '. $message ."\r\n");
        fclose($fp);
        }catch(Exception $e){
            //TODO
        }
    }
}
