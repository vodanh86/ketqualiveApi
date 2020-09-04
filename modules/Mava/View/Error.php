<br />
<div class="container">
    <div class="alert alert-danger">
    <h3><?php
        switch($error_code){
            case Mava_Error::NOT_FOUND:
                echo __('error_not_found_title');
                break;
            case Mava_Error::ACCESS_DENIED:
                echo __('error_access_denied_title');
                break;
            case Mava_Error::INVALID_REQUEST:
                echo __('error_invalid_request_title');
                break;
            case Mava_Error::SERVER_ERROR:
                echo __('error_server_error_title');
                break;
        }
        ?></h3>
    <div class="page-alert-content">
<?php
if(isset($message) && $message!=""){
    echo trim($message);
}else{
    echo __('please_contact_admin');
}
?>
</div>
    </div>
</div>