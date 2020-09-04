<div class="container logout_success_page">
    <div class="page-alert page-alert-success">
        <h3 class="text-center"><?php echo __('login_success'); ?></h3>
        <div class="text-center">
            <p><?php echo __('login_success_content'); ?></p>
            <div class="margin-top-bottom-10">
                <?php
                    if(Mava_Visitor::getInstance()->isSuperAdmin()){
                ?>
                <a href="<?php echo Mava_Url::getPageLink('admin'); ?>" class="btn btn-primary"><?php echo __('admin_page'); ?></a>
                <?php } ?>
                <a href="<?php echo Mava_Url::getDomainUrl(); ?>" class="btn btn-default"><?php echo __('back_to_home'); ?></a>
            </div>
        </div>
    </div>
</div>