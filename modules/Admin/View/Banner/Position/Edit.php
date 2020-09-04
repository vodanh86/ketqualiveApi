<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_edit_banner_position" id="form_edit_banner_position" action="<?php echo Mava_Url::buildLink('admin/banner/edit_position', array('positionID' => $position['id'])); ?>" method="post">
        <h2 class="mava_form_title"><?php echo __('edit_banner_position'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('title'); ?></dt>
                <dd><input type="text" class="input_text input_medium positionTitle" name="positionTitle" id="positionTitle" value="<?php echo htmlspecialchars($position['title']); ?>"/></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('position_code'); ?></dt>
                <dd><input type="text" class="input_text input_medium positionCode" name="positionCode" id="positionCode" value="<?php echo htmlspecialchars($position['position']); ?>" /></dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_edit"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/banner/position'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
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

        $('#button_submit_edit').click(function(){
            $('#form_edit_banner_position').submit();
        });
        $('#form_edit_banner_position').submit(function(){
            if($('#positionTitle').val()==''){
                $('#positionTitle').focus();
                return false;
            }else if($('#positionCode').val()==''){
                $('#positionCode').focus();
                return false;
            }else {
                return true;
            }
        });
    });
</script>