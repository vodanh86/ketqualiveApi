<!doctype html>
<html>
<head>
    <meta name="robots" content="nofollow, noindex" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title><?php echo Mava_Application::get('seo/title'); ?></title>
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('dialog.default'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('dropdown.default'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('select.default'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('admin/global'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('admin/admin'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('bootstrap/css/bootstrap.min'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('font-awesome/css/font-awesome.min'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('dateranger/daterangepicker'); ?>" />
    <script type="text/javascript" src="<?php echo Mava_Url::getPageLink('admin-phrase.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('global'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('popper.min'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('bootstrap/js/bootstrap.min'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('app'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('highcharts/highcharts'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('dateranger/moment.min'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('dateranger/daterangepicker'); ?>"></script>
    <script type="text/javascript">
        var DOMAIN = '<?php echo Mava_Url::getDomainUrl(); ?>';
        var CSRF = '<?php echo Mava_Session::getCSRF(); ?>';
    </script>
</head>
<body>
<?php echo Mava_View::getView('Admin_View_Menu'); ?>
<div class="admin_page_wrap">
<?php echo $pageContent; ?>
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
                if(type=='image') $('#hodela_upload_image input').click();
            },
            file_browser_callback_types: 'image'
        });
        $(document).ready(function(){
            $('.quickTooltip').on('mouseover',function(){
                var tooltip_content = $(this).attr('title');
                console.log(tooltip_content);
                if(tooltip_content == "" || tooltip_content == undefined){
                    tooltip_content = $(this).attr('data-title');
                }
                if($(this).attr('title') != "" && ($(this).attr('data-title') == "" || $(this).attr('data-title')==undefined)){
                    $(this).attr('data-title',$(this).attr('title'));
                    $(this).attr('title', '');
                }
                if(tooltip_content != "" && tooltip_content != undefined){
                    MV.showtip($(this),'top',tooltip_content,'quickTooltipContent');
                }
            });
            $('.quickTooltip').on('mouseout',function(){
                MV.hidetip("*");
            });

            MV.init_dropdown('st_ui_dropdown');
            MV.init_select('st_ui_selector');
            $('.mava_spinbox_plus').on('click',function(){
                var spinboxItem = $(this).parents('.mava_spinbox').find('.mava_spinbox_value');
                var spinMin = parseInt(spinboxItem.attr('data-min'));
                if(spinMin==undefined || isNaN(spinMin)){
                    spinMin = 0;
                }
                var spinMax = spinboxItem.attr('data-max');
                if(spinMax==undefined || isNaN(spinMax)){
                    spinMax = 0;
                }
                var spinStep = parseInt(spinboxItem.attr('data-step'));
                if(spinStep==undefined || isNaN(spinStep)){
                    spinStep = 1;
                }
                var spinCurrentVal = parseInt(spinboxItem.val());
                if(spinCurrentVal==undefined || isNaN(spinCurrentVal)){
                    spinCurrentVal = 0;
                }

                if(spinCurrentVal < spinMax || spinMax==0){
                    if(spinMax==0){
                        spinboxItem.val(spinCurrentVal+spinStep);
                    }else{
                        spinboxItem.val(Math.min(spinCurrentVal+spinStep,spinMax));
                    }
                }
            });

            $('.mava_spinbox_minus').on('click',function(){
                var spinboxItem = $(this).parents('.mava_spinbox').find('.mava_spinbox_value');
                var spinMin = parseInt(spinboxItem.attr('data-min'));
                if(spinMin==undefined || isNaN(spinMin)){
                    spinMin = 0;
                }
                var spinMax = spinboxItem.attr('data-max');
                if(spinMax==undefined || isNaN(spinMax)){
                    spinMax = 0;
                }
                var spinStep = parseInt(spinboxItem.attr('data-step'));
                if(spinStep==undefined || isNaN(spinStep)){
                    spinStep = 1;
                }
                var spinCurrentVal = parseInt(spinboxItem.val());
                if(spinCurrentVal==undefined || isNaN(spinCurrentVal)){
                    spinCurrentVal = 0;
                }

                if(spinCurrentVal > spinMin){
                    spinboxItem.val(Math.max(spinCurrentVal-spinStep,spinMin));
                }
            });

            $('.mava_spinbox_value').keypress(function(e){
                if(e.keyCode>=48 && e.keyCode<=57){
                    return true;
                }
                e.preventDefault();
            });

            $('.mava_row_tab .row_tab .item').click(function(){
                $(this).parents('.mava_row_tab').find('.item').removeClass('active');
                $(this).addClass('active');
                $(this).parents('.mava_row_tab').find('.row_group').addClass('hidden');
                $('#'+ $(this).attr('rel')).removeClass('hidden');
            });
        });
    </script>
    </div>
<iframe id="hodela_form_upload_image" name="hodela_form_upload_image" style="display:none"></iframe>
<form id="hodela_upload_image" action="<?php echo Mava_Url::getPageLink('upload_image', array('type' => 'editor')); ?>" target="hodela_form_upload_image" method="post" enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
    <input name="hodela_image_input" accept="image/*" type="file" onchange="$('#hodela_upload_image').submit();this.value='';">
</form>
    <?php
        echo Mava_View::getView('Mava_View_Common_DebugBar');
    ?>
<?php
if(Mava_Session::get('otm') != ""){
    ?>
    <!-- one time message -->
    <script type="text/javascript">
        MP.notice.show('<?php echo Mava_Session::get('otm'); ?>', 'success', 3);
    </script>
<?php
}
Mava_Session::set('otm', "");

if(Mava_Session::get('otem') != ""){
    ?>
    <!-- one time error message -->
    <script type="text/javascript">
        MP.notice.show('<?php echo Mava_Session::get('otem'); ?>', 'danger', 5);
    </script>
<?php
}
Mava_Session::set('otem', "");
?>
</body>
</html>
