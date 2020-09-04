<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_item" id="form_add_item" action="<?php echo Mava_Url::buildLink('admin/menu/add'); ?>" method="post">
        <h2 class="mava_form_title"><?php echo __('add_menu'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('menu_title'); ?></dt>
                <dd><input type="text" class="input_text input_medium itemTitle" name="itemTitle" id="itemTitle" value="<?php echo isset($itemTitle)?htmlspecialchars($itemTitle):''; ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('menu_link'); ?></dt>
                <dd><input type="text" class="input_text input_medium itemLink" name="itemLink" id="itemLink" value="<?php echo isset($itemLink)?htmlspecialchars($itemLink):''; ?>" placeholder="http://" /></dd>
            </dl>
            <dl class="row">
                <dt>&nbsp;</dt>
                <dd>
                    <label><input type="checkbox" name="itemTarget" value="blank" <?php echo isset($itemTarget) && $itemTarget=='blank'?'checked':''; ?>/> <?php echo __('open_in_new_tab'); ?></label>
                </dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('menu_text_color'); ?></dt>
                <dd>
                    <input type="text" class="input_text input_medium itemTextColor" name="itemTextColor" id="itemTextColor" value="<?php echo isset($itemTextColor)?htmlspecialchars($itemTextColor):''; ?>" placeholder="#FFFFFF" />
                    <div class="gray tsmall"><?php echo __('menu_text_color_note'); ?></div>
                </dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('sort_order'); ?></dt>
                <dd class="mava_spinbox">
                    <input type="text" class="input_text input_short mava_spinbox_value" data-step="1" data-min="0" value="<?php echo isset($itemSortOrder)?(int)$itemSortOrder:0; ?>" name="itemSortOrder" id="itemSortOrder">
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_plus">+</a>
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_minus">-</a>
                </dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/menu/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
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
        $('#itemTitle').focus();

        $('#button_submit_add').click(function(){
            $('#form_add_item').submit();
        });
        $('#form_add_item').submit(function(){
            if($('#itemTitle').val()==''){
                $('#itemTitle').focus();
                return false;
            }else{
                return true;
            }
        });
    });
</script>