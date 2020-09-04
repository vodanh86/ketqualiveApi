<?php
    $agency = Mava_Application::get('agency');
    $notify_count = count_not_seen_notify($agency['id']);
    $my_agency = get_my_agency();
?>
<div class="mbd-top clearfix">
    <a href="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id']); ?>" class="mbd-top-logo pull-left">
        <span class="mbd-logo"></span>
        <span class="mbd-logo-text">+ <?php echo __('dashboard_of_x_agency', array('name' => $agency['title'])); ?></span>
    </a>
    <div class="pull-right mbd-right-top">
        <ul class="nav nav-pills">
            <li class="dropdown mbd-notify-menu">
                <a href="javascript:void(0);" data-toggle="dropdown" class="mbd-mark-all-as-seen"><i class="fa fa-bell-o"></i> <?php echo __('notify') . ($notify_count>0?'<span class="badge">'. min($notify_count,99) .'</span>':'<span class="badge hidden">0</span>'); ?></a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <?php echo get_notify_preview($agency['id'], 5); ?>
                    <li class="mbd-view-all-notify"><a href="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/notifications'); ?>"><?php echo __('view_all_notify'); ?></a></li>
                </ul>
            </li>
            <li class="dropdown mbd-user-menu">
                <a href="javascript:void(0);" data-toggle="dropdown"><img src="<?php echo get_avatar_url(); ?>" /> <?php echo get_fullname(); ?> <i class="fa fa-caret-down"></i></a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <?php
                    echo '<li><a href="'. Mava_Url::getPageLink('') .'" target="_blank"><i class="fa fa-home"></i> '. __('site_name') .'</a></li>';
                    if(is_array($my_agency) && count($my_agency) > 1){
                        echo '<li><a href="'. Mava_Url::getPageLink('dashboard') .'"><i class="fa fa-refresh"></i> '. __('switch_agency') .'</a></li>';
                    }
                    echo '<li><a href="'. Mava_Url::getPageLink('profile') .'"><i class="fa fa-user-secret"></i> '. __('profile') .'</a></li>';
                    echo '<li><a href="'. Mava_Url::getPageLink('logout') .'"><i class="fa fa-power-off"></i> '. __('logout') .'</a></li>';
                    ?>
                </ul>
            </li>
        </ul>
    </div>
</div>
<div class="mbd-aside">
    <div class="mbd-aside-inner">
        <div class="mbd-menu">
            <?php
                $selected_menu = Mava_Application::get('selected_menu');
            ?>
            <ul>
                <li><a href="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id']); ?>"<?php echo $selected_menu=='overview'?' class="active"':''; ?>><i class="fa fa-dashboard"></i> <?php echo __('overview'); ?></a></li>
                <li><a href="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/orders'); ?>"<?php echo $selected_menu=='orders'?' class="active"':''; ?>><i class="fa fa-cart-arrow-down"></i> <?php echo __('orders'); ?></a></li>
                <li><a href="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/withdraw'); ?>"<?php echo $selected_menu=='withdraw'?' class="active"':''; ?>><i class="fa fa-money"></i> <?php echo __('withdraw_request'); ?></a></li>
                <li><a href="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/transactions'); ?>"<?php echo $selected_menu=='transactions'?' class="active"':''; ?>><i class="fa fa-clock-o"></i> <?php echo __('transaction_history'); ?></a></li>
                <li><a href="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/links'); ?>"<?php echo $selected_menu=='links'?' class="active"':''; ?>><i class="fa fa-link"></i> <?php echo __('link_stats'); ?></a></li>
                <li><a href="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/settings'); ?>"<?php echo $selected_menu=='settings'?' class="active"':''; ?>><i class="fa fa-gear"></i> <?php echo __('settings'); ?></a></li>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('.mbd-mark-all-as-seen').click(function(){
            MV.post(DOMAIN +'/dashboard/<?php echo $agency['id']; ?>/mark-notify-as-seen', {}, function(){
                $('.mbd-notify-menu .badge').html('').hide();
            });
        });

        setInterval(function(){
            MV.post(DOMAIN +'/dashboard/<?php echo $agency['id']; ?>/update-quick-notify', {}, function(res){
                if(res.count > 0){
                    $('.mbd-notify-menu .badge').html(""+ res.count).removeClass('hidden').show();
                    $('.mbd-notify-menu .mbd-read, .mbd-notify-menu .mbd-unread').remove();
                    $(res.html).insertBefore($('.mbd-notify-menu .mbd-view-all-notify'));
                }else{
                    $('.mbd-notify-menu .badge').html('').hide();
                }
            });
        }, 10000);
    });
</script>