<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_user_group" id="form_add_user_group" action="<?php echo Mava_Url::buildLink('admin/users/group_add'); ?>" method="post" enctype="multipart/form-data">
        <h2 class="mava_form_title"><?php echo __('add_group'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('title'); ?></dt>
                <dd><input type="text" class="input_text input_medium" name="groupTitle" id="groupTitle" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('sort_order'); ?></dt>
                <dd class="mava_spinbox">
                    <input type="text" class="input_text input_short mava_spinbox_value" data-step="1" data-min="0" value="0" name="groupSortOrder" id="groupSortOrder">
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_plus">+</a>
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_minus">-</a>
                </dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/users/group'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
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
            $('#form_add_user_group').submit();
        });
        $('#form_add_user_group').submit(function(){
            if($('#groupTitle').val()==''){
                $('#groupTitle').focus();
                return false;
            }else{
                return true;
            }
        });
    });
</script>