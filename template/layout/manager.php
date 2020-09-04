<!doctype html>
<html>

<head>
    <meta name="robots" content="nofollow, noindex" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <title>
        <?php echo Mava_Application::get('seo/title'); ?>
    </title>
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('dialog.default'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('dropdown.default'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('select.default'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('manager/manager') .'?v='. time(); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('bootstrap/css/bootstrap.min'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('bootstrap/css/dataTables.bootstrap'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('bootstrap/css/bootstrap-toggle.min'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('font-awesome/css/font-awesome.min'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('dateranger/daterangepicker'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('zebra_datepicker.min'); ?>" />
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('global'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('popper.min'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('bootstrap/js/bootstrap.min'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('bootstrap/js/bootstrap-toggle.min'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('app'); ?>?v=21"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('modal'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('highcharts/highcharts'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('dateranger/moment.min'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('dateranger/daterangepicker'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('zebra_datepicker.min'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('autonumeric-4.1.0'); ?>"></script>
    <script type="text/javascript">
        var DOMAIN = '<?php echo Mava_Url::getDomainUrl(); ?>';
        var CSRF = '<?php echo Mava_Session::getCSRF(); ?>';
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="logo-title">
        <a class="navbar-brand xs-logo" href="<?php echo Mava_Url::buildLink('manager/dashboard'); ?>">
            <img src="../template/css/images/logo.png" />
        </a>
<!--        <a class="navbar-brand supplier-name">--><?//= __('Supplier') ?><!--</a>-->
    </div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#top_menubar" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fa fa-align-justify" style="color:black"></i>
    </button>
    <div class="collapse navbar-collapse" id="top_menubar">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item <?=(Mava_Application::get('menu_selected')=='dashboard')?'active':''?>"><a class="nav-link" href="<?php echo Mava_Url::buildLink('manager/dashboard'); ?>"><i class="fa fa-list-alt icon-menu-before"></i><?= __(' Bảng điều khiển ') ?><i class="fa fa-angle-right icon-menu-after"></i></a></li>
            <li class="nav-item <?=(Mava_Application::get('menu_selected')=='user')?'active':''?>"><a class="nav-link" href="<?php echo Mava_Url::buildLink('manager/user'); ?>"><?= __(' Thành viên ') ?></a></li>
            <li class="nav-item <?=(Mava_Application::get('menu_selected')=='coin_charge')?'active':''?>"><a class="nav-link" href="<?php echo Mava_Url::buildLink('manager/charge-coin'); ?>"><?= __(' Coin nạp ') ?></a></li>
            <li class="nav-item <?=(Mava_Application::get('menu_selected')=='coin_consume')?'active':''?>"><a class="nav-link" href="<?php echo Mava_Url::buildLink('manager/consume-coin'); ?>"><?= __(' Coin dùng ') ?></a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo Mava_Url::buildLink('manager/user?sort_by=coin&sort_dir=desc'); ?>"><?= __(' Xếp hạng coin ') ?></a></li>
            <li class="nav-item <?=(Mava_Application::get('menu_selected')=='loto_tip')?'active':''?>"><a class="nav-link" href="<?php echo Mava_Url::buildLink('manager/list-loto-tip'); ?>"><?= __(' Tip lô tô ') ?></a></li>
            <li class="nav-item <?=(Mava_Application::get('menu_selected')=='football_tip')?'active':''?>"><a class="nav-link" href="<?php echo Mava_Url::buildLink('manager/list-football-tip'); ?>"><?= __(' Tip bóng đá ') ?></a></li>
            <li class="nav-item <?=(Mava_Application::get('menu_selected')=='activity')?'active':''?>"><a class="nav-link" href="<?php echo Mava_Url::buildLink('manager/activity'); ?>"><?= __(' Hoạt động ') ?></a></li>
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user-circle icon-menu-before"></i><?=__(' Tài khoản ')?></a>
                <div class="dropdown-menu dropdown-menu-right account-dropdown">
                    <a class="dropdown-item" href="<?php echo Mava_Url::getPageLink('manager/logout'); ?>" onclick="return confirm('<?php echo __('logout_confirm'); ?>')"><?php  echo __('logout'); ?></a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<div class="container-fluid">
    <div class="page-content">
        <?php echo $pageContent; ?>
    </div>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('tinymce/tinymce.min'); ?>"></script>
    <script type="text/javascript">
        tinymce.init({
            selector: ".input_richtext",
            statusbar: true,
            height: 300,
            relative_urls: false,
            remove_script_host: false,
            entity_encoding: 'raw',
            plugins: [
                ["advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker"],
                ["searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking"],
                ["save table contextmenu directionality emoticons template paste"]
            ],
            toolbar: "bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image media | blockquote | code preview fullscreen",
            file_browser_callback: function(field_name, url, type, win) {
                if (type == 'image') $('#hodela_upload_image input').click();
            },
            file_browser_callback_types: 'image'
        });
        $(document).ready(function() {
            $('.quickTooltip').on('mouseover', function() {
                var tooltip_content = $(this).attr('title');
                console.log(tooltip_content);
                if (tooltip_content == "" || tooltip_content == undefined) {
                    tooltip_content = $(this).attr('data-title');
                }
                if ($(this).attr('title') != "" && ($(this).attr('data-title') == "" || $(this).attr('data-title') == undefined)) {
                    $(this).attr('data-title', $(this).attr('title'));
                    $(this).attr('title', '');
                }
                if (tooltip_content != "" && tooltip_content != undefined) {
                    MV.showtip($(this), 'top', tooltip_content, 'quickTooltipContent');
                }
            });
            $('.quickTooltip').on('mouseout', function() {
                MV.hidetip("*");
            });

            MV.init_dropdown('st_ui_dropdown');
            MV.init_select('st_ui_selector');
            $('.mava_spinbox_plus').on('click', function() {
                var spinboxItem = $(this).parents('.mava_spinbox').find('.mava_spinbox_value');
                var spinMin = parseInt(spinboxItem.attr('data-min'));
                if (spinMin == undefined || isNaN(spinMin)) {
                    spinMin = 0;
                }
                var spinMax = spinboxItem.attr('data-max');
                if (spinMax == undefined || isNaN(spinMax)) {
                    spinMax = 0;
                }
                var spinStep = parseInt(spinboxItem.attr('data-step'));
                if (spinStep == undefined || isNaN(spinStep)) {
                    spinStep = 1;
                }
                var spinCurrentVal = parseInt(spinboxItem.val());
                if (spinCurrentVal == undefined || isNaN(spinCurrentVal)) {
                    spinCurrentVal = 0;
                }

                if (spinCurrentVal < spinMax || spinMax == 0) {
                    if (spinMax == 0) {
                        spinboxItem.val(spinCurrentVal + spinStep);
                    } else {
                        spinboxItem.val(Math.min(spinCurrentVal + spinStep, spinMax));
                    }
                }
            });

            $('.mava_spinbox_minus').on('click', function() {
                var spinboxItem = $(this).parents('.mava_spinbox').find('.mava_spinbox_value');
                var spinMin = parseInt(spinboxItem.attr('data-min'));
                if (spinMin == undefined || isNaN(spinMin)) {
                    spinMin = 0;
                }
                var spinMax = spinboxItem.attr('data-max');
                if (spinMax == undefined || isNaN(spinMax)) {
                    spinMax = 0;
                }
                var spinStep = parseInt(spinboxItem.attr('data-step'));
                if (spinStep == undefined || isNaN(spinStep)) {
                    spinStep = 1;
                }
                var spinCurrentVal = parseInt(spinboxItem.val());
                if (spinCurrentVal == undefined || isNaN(spinCurrentVal)) {
                    spinCurrentVal = 0;
                }

                if (spinCurrentVal > spinMin) {
                    spinboxItem.val(Math.max(spinCurrentVal - spinStep, spinMin));
                }
            });

            $('.mava_spinbox_value').keypress(function(e) {
                if (e.keyCode >= 48 && e.keyCode <= 57) {
                    return true;
                }
                e.preventDefault();
            });

            $('.mava_row_tab .row_tab .item').click(function() {
                $(this).parents('.mava_row_tab').find('.item').removeClass('active');
                $(this).addClass('active');
                $(this).parents('.mava_row_tab').find('.row_group').addClass('hidden');
                $('#' + $(this).attr('rel')).removeClass('hidden');
            });


            if($('.product-price').not('.has-init').length > 0){
                new AutoNumeric('.product-price', {aPad: false});
                $('.product-price').addClass('has-init');
            }

        });
    </script>
</div>
<?php echo Mava_View::getView('Mava_View_Common_Footer'); ?>
<?php if(Mava_Session::get('otm') != ""){?>
    <!-- one time message -->
    <script type="text/javascript">
        MP.notice.show('<?php echo Mava_Session::get('otm'); ?>', 'success', 5);
    </script>
<?php }
Mava_Session::set('otm', "");

if(Mava_Session::get('otem') != ""){ ?>
    <!-- one time error message -->
    <script type="text/javascript">
        MP.notice.show('<?php echo Mava_Session::get('otem'); ?>', 'danger', 5);
    </script>
<?php }
Mava_Session::set('otem', "");
?>
</body>

</html>