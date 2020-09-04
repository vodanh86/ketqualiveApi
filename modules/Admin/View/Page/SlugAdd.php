<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_phrase" id="form_add_slug" action="<?php echo Mava_Url::buildLink('admin/page/slug_add'); ?>" method="post">
        <h2 class="mava_form_title"><?php echo __('create_new_slug'); ?></h2>
        <div class="mava_form_rows">
            <?php
                if(isset($error_message) && $error_message != ''){
                    echo '
                        <dl class="row">
                            <dt></dt>
                            <dd style="color: #ff0000;">'.$error_message.'</dd>
                        </dl>
                    ';
                }
            ?>

            <dl class="row">
                <dt><?php echo __('slug'); ?><b style="color: red;">(*)</b></dt>
                <dd><input type="text" class="input_text" value="<?php echo (isset($postData['slug'])?$postData['slug']:'') ; ?>" name="slug" id="slug" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('app'); ?></dt>
                <dd><input type="text" class="input_text" value="<?php echo (isset($postData['app'])?$postData['app']:'') ; ?>" name="app" id="app" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('controller'); ?></dt>
                <dd><input type="text" class="input_text" value="<?php echo (isset($postData['controller'])?$postData['controller']:'') ; ?>" name="controller" id="controller" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('action'); ?></dt>
                <dd><input type="text" class="input_text" value="<?php echo (isset($postData['action'])?$postData['action']:'') ; ?>" name="action" id="action" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('params'); ?></dt>
                <dd><textarea class="input_area" name="params" id="params"><?php echo (isset($postData['params'])?$postData['params']:'') ; ?></textarea></dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/page/slug'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
                </dd>
            </dl>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        <?php
            if(isset($error_message) && $error_message != ''){
        ?>
        MV.show_notice('<?php echo $error_message; ?>',3);
        <?php
            }
        ?>
        $('#button_submit_add').click(function(){
            $('#form_add_slug').submit();
        });
        $('#form_add_slug').submit(function(){
            if($('#slug').val()==''){
                $('#slug').focus();
                return false;
            }else{
                return true;
            }
        });
    });
</script>