<div id="login_required_page" class="container">
    <div class="content">
    <h3><?php echo __('function_only_logged_in'); ?></h3>
    <p><?php
        echo isset($message)?$message:__('login_required');
        ?></p>
    <div>
        <a href="<?php echo Mava_Url::getPageLink('login'); ?>" class="button button-info"><?php echo __('login'); ?></a>
        <a href="<?php echo Mava_Url::getPageLink('signup'); ?>" class="button button-default"><?php echo __('signup'); ?></a>
    </div>
    </div>
</div>