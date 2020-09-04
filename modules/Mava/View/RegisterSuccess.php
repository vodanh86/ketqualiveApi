<div class="container">
        <div class="page-alert text-center">
            <h1><?php echo __('register_success'); ?></h1>
            <div class="page-alert-content">
<?php
    switch($active){
        case 'email':
            echo __('signup_active_email_tips', array(
                    'email' => htmlspecialchars($email),
                    'resend_email_active_link' => Mava_Url::getPageLink('resend_email_active', array('uid' => $uid, 'hash_email' => $hash_email))
                )) .'<img src="'. Mava_Url::getPageLink('cron', array('type' => 'send_mail','email_id' => $email_queue_id,'_CSRF' => Mava_Session::getCSRF())) .'" />';
            break;
        case 'phone':
            echo __('signup_active_phone_tip_head', array('phone' => htmlspecialchars($phone))) .':<br />
            <form name="frmActivePhone" id="frmActivePhone" action="'. Mava_Url::getPageLink('phone_active') .'" method="post">
                <input type="hidden" name="user_id" value="'. $uid .'" />
                <input type="hidden" name="hash_phone" value="'. $hash_phone .'" />
                <input type="text" class="input-text" name="active_code" maxlength="6" placeholder="'. __('active_phone_code_placeholder') .'" />
                <input type="submit" class="button button-info button-medium" value="'. __('active') .'" />
            </form>
            <p>'. __('signup_active_phone_tip_foot', array('resend_phone_active_message' => Mava_Url::getPageLink('resend_phone_active',array('uid' => $uid, 'hash_phone' => $hash_phone)))) .'</p>';
            break;
        case 'admin':
            echo __('signup_admin_active_head', array('email' => htmlspecialchars($email)));
            break;
        default:
            echo '<p>'. __('register_success_message') .'</p>';
            break;
    }
?>
                <div class="form-group lr-action"><a href="<?php echo Mava_Url::getDomainUrl(); ?>" class="btn btn-success"><?php echo __('back_to_home'); ?></a></div>
            </div>
        </div>
</div>