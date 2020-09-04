<div class="gw clearfix forgotpassword_page">
    <div class="page-alert page-alert-success">
        <h3><?php echo __('request_sent'); ?></h3>
        <div class="page-alert-content">
            <?php
            echo __('forgotpassword_sent', array('email' => $email));
            echo '<img src="'. Mava_Url::getPageLink('cron', array('type' => 'send_mail','email_id' => $email_queue_id, '_CSRF' => Mava_Session::getCSRF())) .'" />';
            ?>
            <div class="margin-top-bottom-10"><a href="<?php echo Mava_Url::getDomainUrl(); ?>" class="button button-info"><?php echo __('back_to_home'); ?></a></div>
        </div>
    </div>
</div>