<?php
    if(isset($message) && $message!=""){
        if(is_debug()){
        echo '<h2>'. __('error_occurred') .':</h2><br />'. htmlspecialchars($message) .' (errorCode: '. ((isset($error_code)&&$error_code!="")?$error_code:'-') .')';
        }else{
            echo __('error_occurred');
        }
    }
?>