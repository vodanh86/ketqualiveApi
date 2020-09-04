<div class="container">
    <div class="page-alert text-center">
        <h1><?php echo __('account_is_banned'); ?></h1>
        <div class="page-alert-content">
            <?php
            echo __('your_account_is_banned_to_x_by_reason_y', array('time' => date('Y/m/d H:i:s', $banned_time), 'reason' => $banned_reason));
            ?>
            <div class="lr-action">
                <a href="<?php echo Mava_Url::getDomainUrl(); ?>" class="btn btn-default"><?php echo __('back_to_home'); ?></a>
            </div>
        </div>
    </div>
</div>