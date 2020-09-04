<div class="container">
    <div class="page-alert text-center">
        <h1><?php echo __('account_not_active'); ?></h1>
        <div class="page-alert-content">
            <?php
                if($active_type == 'email'){
                    echo __('email_active_instruction', array('email' => $email));
                }else if($active_type == 'phone'){
                    echo __('phone_active_instruction', array(
                        'phone' => $phone,
                        'active_link' => Mava_Url::getPageLink('phone_active'),
                        'user_id' => $uid,
                        'hash_phone' => $hash_phone
                    ));
                }else{
                    echo __('please_wait_admin_active');
                }
            ?>
            <div class="lr-action">
                <?php
                    if($active_type == 'email'){
                        echo '<a href="'. Mava_Url::getPageLink('resend_email_active', array('uid' => $uid, 'hash_email' => $hash_email)) .'" class="btn btn-primary">'. __('resend_mail_active') .'</a>';
                    }else if($active_type == 'phone'){
                        echo '<a href="'. Mava_Url::getPageLink('resend_phone_active', array('uid' => $uid, 'hash_phone' => $hash_phone)) .'" class="btn btn-primary">'. __('resend_phone_active') .'</a>';
                    }
                ?>
                <a href="<?php echo Mava_Url::getDomainUrl(); ?>" class="btn btn-default"><?php echo __('back_to_home'); ?></a>
            </div>
        </div>
    </div>
</div>