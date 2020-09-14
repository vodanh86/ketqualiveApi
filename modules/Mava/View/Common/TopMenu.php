<a name="home"></a>
<div id="xs_top" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-8 d-none d-md-block lr-welcome-message"><?php echo __('welcome_top_message'); ?></div>
            <div class="col-md-4">
                <?php
                echo '<ul class="nav pull-right lr-user-nav">';
                if(is_login()){
                    ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo Mava_Visitor::getInstance()->get('custom_title'); ?></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?php
                            if(Mava_Visitor::getInstance()->isSuperAdmin()){
                                echo '<a class="dropdown-item" href="'. Mava_Url::getPageLink('admin') .'"><strong>'. __('admin_page') .'</strong></a>';
                            }
                            ?>
                            <a class="dropdown-item" href="<?php echo Mava_Url::getPageLink('change-password'); ?>" class="lr-ajax-link"><?php echo __('change_password'); ?></a>
                            <a class="dropdown-item" href="<?php echo Mava_Url::getPageLink('logout'); ?>" class="lr-confirm-link" data-confirm="<?php echo __('logout_confirm_text'); ?>"><?php echo __('logout'); ?></a>
                        </div>
                    </li>
                <?php
                }else{
                ?>
                <li><a href="<?php echo Mava_Url::getPageLink('signup'); ?>" class="primary"><?php echo __('signup'); ?></a></li>
                <li><a href="<?php echo Mava_Url::getPageLink('login'); ?>"><?php echo __('login'); ?></a></li>
                <?php
                }
                echo '</ul>';
                ?>
            </div>
        </div>
    </div>
</div>
<div id="xs_header" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a class="xs-logo" id="logo" href="<?php echo Mava_Url::getPageLink('/'); ?>"><h2><?php echo Mava_Application::getOptions()->siteName; ?></h2></a>
                <div class="xs-header-ads"><img src="http://img.ketqua.net/images/2018/03/14/5c6a36aecaa2c25b2c029dbbd5ca17b0.gif" /></div>
            </div>
        </div>
    </div>
</div>
<nav id="top_menu">
    <div class="container">
    <div class="row">
    <div class="navbar navbar-expand-lg navbar-light">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#lr_main_menu" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="<?php echo __('show_menu'); ?>">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="lr_main_menu">
            <?php
            $provinces = get_all_province();
            echo '<ul class="navbar-nav mr-auto lr-common-nav">
            <li><a href="'. Mava_Url::getPageLink('/') .'"><i class="fa fa-home"></i></a></li>
            <li>
                    <a href="'. Mava_Url::getPageLink('ket-qua-xo-so-truyen-thong') .'">Kết quả xổ số truyền thống</a>
            </li>
            <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">Kết quả xổ số Miền Trung</a>
                    <div class="dropdown-menu">';
                    foreach($provinces['trung'] as $item){
                        echo '<a class="dropdown-item" href="'. Mava_Url::getPageLink($item['slug']) .'">'. htmlspecialchars($item['title']) .'</a>';
                    }    
            echo '  </div>
            </li>
            <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">Kết quả xổ số Miền Nam</a>
                    <div class="dropdown-menu">';
                    foreach($provinces['nam'] as $item){
                        echo '<a class="dropdown-item" href="'. Mava_Url::getPageLink($item['slug']) .'">'. htmlspecialchars($item['title']) .'</a>';
                    }    
            echo '  </div>
            </li>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="'. Mava_Url::getPageLink('ket-qua-vietlott') .'">Kết quả Vietlott</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="'. Mava_Url::getPageLink('ket-qua-vietlott-mega') .'">Kết quả Mega 6/45</a>
                    <a class="dropdown-item" href="'. Mava_Url::getPageLink('ket-qua-vietlott-max4d') .'">Kết quả Max 4D</a>
                    <a class="dropdown-item" href="'. Mava_Url::getPageLink('ket-qua-vietlott-power') .'">Kết quả Power 6/55</a>
                </div>
            </li>
            <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="'. Mava_Url::getPageLink('thong-ke') .'">Thống kê</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('soi-cau-theo-so') .'">Soi cầu theo số</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('soi-cau-theo-tinh') .'">Soi cầu theo tỉnh</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('soi-cau-bach-thu') .'">Soi cầu truyền thống - bạch thủ</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('soi-cau-ve-hai-nhay') .'">Soi cầu truyền thống - về hai nháy</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('soi-cau-dac-biet') .'">Soi cầu truyền thống - đặc biệt</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('thong-ke-lo-gan-theo-tinh') .'">Thống kê lô gan</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('thong-ke-tan-suat-lo') .'">Thống kê tần suất</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('thong-ke-theo-tong') .'">Thống kê tổng số</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('thong-ke-lo-ve-nhieu-ve-it') .'">Thống kê lô về nhiều - về ít</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('thong-ke-lo-roi') .'">Thống kê lô rơi</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('thong-ke-giai-dac-biet') .'">Thống kê giải đặc biệt</a>
                        <!--<a class="dropdown-item" href="'. Mava_Url::getPageLink('thong-ke-chu-ky') .'">Thống kê chu kỳ</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('thong-ke-chu-ky-dan-lo-to') .'">Thống kê chu kỳ dàn Lô lô</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('thong-ke-chu-ky-gan-theo-tinh') .'">Thống kê chu kỳ gan theo tỉnh</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('thong-ke-nhanh') .'">Thống kê nhanh</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('tong-hop-chu-ky-dac-biet') .'">Tổng hợp chu kỳ đặc biệt</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('thong-ke-chu-ky-dan-dac-biet') .'">Thống kê chu kỳ dàn đặc biệt</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('thong-ke-giai-dac-biet-gan') .'">Thống kê giải Đặc Biệt gan</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('thong-ke-cap-so-anh-em') .'">Thống kê cặp số anh em</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('thong-ke-theo-ngay') .'">Thống kê theo ngày</a>
                        <a class="dropdown-item" href="'. Mava_Url::getPageLink('ket-qua-giai-dac-biet-cho-ngay-mai') .'">Kết quả giải Đặc Biệt cho ngày mai</a>-->
                    </div>
            </li>
            <li>
                    <a href="'. Mava_Url::getPageLink('nap-tien') .'">Nạp tiền</a>
            </li>';
            $menu_selected = Mava_Url::getCurrentAddress();
            $menus = get_menus();
            if(is_array($menus) && count($menus) > 0){
                foreach($menus as $mnu){
                    if(substr($mnu['link'],0,7) == 'http://' || substr($mnu['link'],0,8) == 'https://' || substr($mnu['link'],0,4) == 'www.'){
                        $link = $mnu['link'];
                    }else{
                        $link = Mava_Url::getPageLink(trim($mnu['link'],'/'));
                    }
                    echo '<li'. ($menu_selected==$link?' class="active"':'') .'><a href="'. $link .'"'. ($mnu['open_in_new_tab']=='yes'?' target="_blank"':'') . ($mnu['text_color']!=""?" style='color: ". $mnu['text_color'] .";'":"") .' class="lr-ajax-link">'. __($mnu['title']) .'</a></li>';
                }
            }
            echo '</ul>';
            ?>
        </div>
    </div>
    </div>
    </div>
</nav>
