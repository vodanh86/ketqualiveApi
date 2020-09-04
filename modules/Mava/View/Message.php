<div class="gw clearfix page-alert page-alert-error">
    <h3><?php __('alert'); ?></h3>
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