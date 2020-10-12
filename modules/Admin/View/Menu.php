<div id="admin_menu">
    <ul class="clearfix page_width">
        <li><a href="<?php echo Mava_Url::buildLink('admin'); ?>"><b><?php echo Mava_Application::getOptions()->siteName; ?></b></a></li>
        <li><a href="<?php echo Mava_Url::buildLink('admin/banner/index'); ?>"><?php echo __('banner'); ?></a></li>
        <li class="st_ui_dropdown inline mousehover">
            <a href="javascript:void(0);" class="label">
                <?php echo __('admin_ads'); ?><s class="icon_white_down"></s>
            </a>
            <div class="list">
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/ads/index'); ?>"><?php echo __('overview'); ?></a>
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/ads/campaigns'); ?>"><?php echo __('ads_campaign'); ?></a>
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/ads/campaign-groups'); ?>"><?php echo __('ads_campaign_group'); ?></a>
            </div>
        </li>
        <li class="st_ui_dropdown inline mousehover">
            <a href="javascript:void(0);" class="label">
                <?php echo __('news'); ?><s class="icon_white_down"></s>
            </a>
            <div class="list">
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/news/index'); ?>"><?php echo __('all_news'); ?></a>
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/news/category'); ?>"><?php echo __('news_category'); ?></a>
            </div>
        </li>
        <li class="st_ui_dropdown inline mousehover">
            <a href="javascript:void(0);" class="label">
                <?php echo __('admin_users'); ?><s class="icon_white_down"></s>
            </a>
            <div class="list">
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/users/index'); ?>"><?php echo __('all_user'); ?></a>
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/users/group'); ?>"><?php echo __('user_group'); ?></a>
            </div>
        </li>
        <li class="st_ui_dropdown inline mousehover">
            <a href="javascript:void(0);" class="label">
                <?php echo __('admin_videos'); ?><s class="icon_white_down"></s>
            </a>
            <div class="list">
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/videos/index'); ?>"><?php echo __('all_videos'); ?></a>
            </div>
        </li>
        <li class="st_ui_dropdown inline mousehover">
            <a href="javascript:void(0);" class="label">
                <?php echo __('admin_novel'); ?><s class="icon_white_down"></s>
            </a>
            <div class="list">
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/novel/index'); ?>"><?php echo __('all_novels'); ?></a>
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/novel/upload'); ?>"><?php echo __('file_upload'); ?></a>
            </div>
        </li>
        <?php if(is_debug() === 2){ ?>
            <li class="st_ui_dropdown inline mousehover">
                <a href="javascript:void(0);" class="label">
                    <?php echo __('admin_development'); ?><s class="icon_white_down"></s>
                </a>
                <div class="list">
                    <a class="item" href="<?php echo Mava_Url::buildLink('admin/logs/index'); ?>"><?php echo __('admin_logs'); ?></a>
                    <a class="item" href="<?php echo Mava_Url::buildLink('admin/add-ons/index'); ?>"><?php echo __('admin_addons'); ?></a>
                    <a class="item" href="<?php echo Mava_Url::buildLink('admin/code-events/index'); ?>"><?php echo __('admin_code_events'); ?></a>
                    <a class="item" href="<?php echo Mava_Url::buildLink('admin/code-event-listener/index'); ?>"><?php echo __('admin_code_event_listeners'); ?></a>
                </div>
            </li>
        <?php } ?>
        <li class="st_ui_dropdown inline mousehover">
            <a href="javascript:void(0);" class="label">
                <?php echo __('other'); ?><s class="icon_white_down"></s>
            </a>
            <div class="list">
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/subscribes/index'); ?>"><?php echo __('admin_subscribes'); ?></a>
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/menu/index'); ?>"><?php echo __('admin_menu'); ?></a>
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/page/index'); ?>"><?php echo __('page'); ?></a>
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/page/group'); ?>"><?php echo __('page_group'); ?></a>
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/page/slug'); ?>"><?php echo __('slug'); ?></a>
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/language/index'); ?>"><?php echo __('admin_language'); ?></a>
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/option/index'); ?>"><?php echo __('admin_option'); ?></a>
                <a class="item" href="<?php echo Mava_Url::buildLink('admin/permission/index'); ?>"><?php echo __('admin_permission'); ?></a>
            </div>
        </li>
        <li class="st_ui_dropdown right inline mousehover fr">
            <a href="javascript:void(0);" class="label">
                <b><?php echo htmlspecialchars(Mava_Visitor::getInstance()->get('custom_title')); ?></b><s class="icon_white_down"></s>
            </a>
            <div class="list">
                <a class="item" href="<?php echo Mava_Url::getPageLink(); ?>" target="_blank"><?php echo __('view_front_page'); ?></a>
                <a class="item" href="<?php echo Mava_Url::getPageLink('logout'); ?>" onclick="return confirm('<?php echo __('logout_confirm'); ?>');"><?php echo __('logout'); ?></a>
            </div>
        </li>
    </ul>
</div>