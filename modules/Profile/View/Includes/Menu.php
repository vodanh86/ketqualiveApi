<?php
    $menu_profile = Mava_Application::get('menu_profile');
?>
<div id="profile_left" class="col-md-3">
    <div class="rm-box-border">
        <div class="rm-user-info">
            <a href="<?php echo Mava_Url::getPageLink('profile'); ?>" class="rm-user-avatar-link"><img src="<?php echo get_avatar_url('big'); ?>" class="rm-user-avatar"/></a>
            <a href="<?php echo Mava_Url::getPageLink('profile'); ?>" class="rm-user-fullname-link"><h3 class="rm-user-fullname"><?php echo get_fullname(); ?></h3></a>
            <p class="rm-user-bio"><?php echo get_user_lead(); ?></p>
        </div>
        <div class="rm-profile-tab">
            <ul class="nav nav-pills nav-stacked">
                <li <?php echo ($menu_profile=='' || $menu_profile=='home')?' class="active"':''; ?>><a href="<?php echo Mava_Url::getPageLink('profile'); ?>"><?php echo __('profile'); ?></a></li>
                <li <?php echo ($menu_profile=='order')?' class="active"':''; ?>><a href="<?php echo Mava_Url::getPageLink('profile/orders'); ?>"><?php echo __('my_orders'); ?></a></li>
                <li <?php echo ($menu_profile=='address')?' class="active"':''; ?>><a href="<?php echo Mava_Url::getPageLink('profile/address'); ?>"><?php echo __('consignee_address'); ?></a></li>
                <li <?php echo ($menu_profile=='account')?' class="active"':''; ?>><a href="<?php echo Mava_Url::getPageLink('profile/account'); ?>"><?php echo __('account_information'); ?></a></li>
                <li <?php echo ($menu_profile=='password')?' class="active"':''; ?>><a href="<?php echo Mava_Url::getPageLink('profile/password'); ?>"><?php echo __('change_password'); ?></a></li>
                <li id="logout_button"><a href="<?php echo Mava_Url::getPageLink('logout'); ?>"><?php echo __('logout'); ?></a></li>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#logout_button').click(function(){
            MP.modal.confirm('<?php echo __('logout_confirm'); ?>', function(){
                window.location.href = '<?php echo Mava_Url::getPageLink('logout'); ?>';
            });
            return false;
        });
    });
</script>