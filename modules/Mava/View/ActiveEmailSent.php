<div class="container">
    <div class="page-alert text-center">
        <h1><?php echo __('active_email_sent'); ?></h1>
        <div class="page-alert-content">
            <?php
            echo __('resend_email_active_tips', array(
                        'email' => htmlspecialchars($email)
                    )) .'<img src="'. Mava_Url::getPageLink('cron', array('type' => 'send_mail','email_id' => $email_queue_id,'_CSRF' => Mava_Session::getCSRF())) .'" />';
            ?>
            <div class="lr-action">
                <a href="<?php echo Mava_Url::getPageLink('resend_email_active', array('uid' => $uid, 'hash_email' => $hash_email)); ?>" class="btn btn-primary"><?php echo __('resend'); ?></a>
                <a href="<?php echo Mava_Url::getDomainUrl(); ?>" class="btn btn-default"><?php echo __('back_to_home'); ?></a>
            </div>
        </div>
    </div>
</div>