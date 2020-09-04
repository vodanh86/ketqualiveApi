<div class="container">
    <div class="page-alert text-center">
        <h1><?php echo __('account_activated'); ?></h1>
        <div class="page-alert-content">
            <?php
            echo __('account_activated_message');
            ?>
            <div class="lr-action">
                <a href="<?php echo Mava_Url::getPageLink('login'); ?>" class="btn btn-primary"><?php echo __('login'); ?></a>
                <a href="<?php echo Mava_Url::getDomainUrl(); ?>" class="btn btn-default"><?php echo __('back_to_home'); ?></a>
            </div>
        </div>
    </div>
</div>