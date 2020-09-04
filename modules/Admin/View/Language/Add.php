<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_language" id="form_add_language" action="<?php echo Mava_Url::buildLink('admin/language/do_add'); ?>" method="post">
        <h2 class="mava_form_title"><?php echo __('add_language'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('language_title'); ?></dt>
                <dd><input type="text" class="input_text input_medium" name="languageTitle" id="languageTitle" /></dd>
            </dl>
            <dl class="row">
                <dt>
                    <?php echo __('language_code'); ?>
                    <p class="gray tsmall"><?php echo __('language_code_must_unique'); ?></p>
                </dt>
                <dd><input type="text" class="input_text input_short" name="languageCode" id="languageCode" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('date_format'); ?>
                <p class="gray tsmall"><?php echo __('date_format_note'); ?></p>
                </dt>
                <dd><input type="text" class="input_text input_short" name="dateFormat" id="dateFormat" value="d/m/Y" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('time_format'); ?>
                <p class="gray tsmall"><?php echo __('time_format_note'); ?></p>
                </dt>
                <dd><input type="text" class="input_text input_short" name="timeFormat" id="timeFormat" value="H:i:s" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('text_direction'); ?></dt>
                <dd><select name="textDirection" id="textDirection" >
                        <option value="LTR"><?php echo __('left_to_right'); ?></option>
                        <option value="RTL"><?php echo __('right_to_left'); ?></option>
                </select></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('decimal_point'); ?></dt>
                <dd><input type="text" maxlength="1" class="input_text input_short" name="decimalPoint" id="decimalPoint" value=","/></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('thousands_separator'); ?></dt>
                <dd><input type="text" maxlength="1" class="input_text input_short" name="thousandsSeparator" id="thousandsSeparator" value="."/></dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/language/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
                </dd>
            </dl>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#languageTitle').focus();

        $('#button_submit_add').click(function(){
            if($('#languageTitle').val()==''){
                $('#languageTitle').focus();
                return false;
            }else if($('#languageCode').val()==''){
                $('#languageCode').focus();
                return false;
            }else{
                MV.post(DOMAIN +'/admin/language/do_add',$('#form_add_language').serialize(),function(res){
                    if(res.status==1){
                        MV.dialog.st_alert('<div class="text_success"><s class="icon_success_big"></s>'+res.message+'</div>',function(){
                            setTimeout(function(){
                                window.location.href = '<?php echo Mava_Url::buildLink('admin/language/index'); ?>';
                            },300);
                        });
                    }
                });
            }
            return false;
        });


    });
</script>