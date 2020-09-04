<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_edit_option_group" id="form_edit_option_group" action="<?php echo Mava_Url::buildLink('admin/option/do_edit_group'); ?>" method="post">
        <input type="hidden" name="currentGroupID" value="<?php echo htmlspecialchars($optionGroup['group_id']); ?>" />
        <h2 class="mava_form_title"><?php echo __('edit_option_group'); ?>: <?php echo __('_option_group_title_'. $optionGroup['group_id']); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('option_group_id'); ?></dt>
                <dd><input type="text" class="input_text" name="groupID" id="groupID" value="<?php echo htmlspecialchars($optionGroup['group_id']); ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('option_group_title'); ?></dt>
                <dd><input type="text" class="input_text input_medium" name="groupTitle" id="groupTitle" value="<?php echo htmlspecialchars($groupTitle); ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('option_group_description'); ?></dt>
                <dd><textarea class="input_area" name="groupDescription" id="groupDescription"><?php echo htmlspecialchars($groupDescription); ?></textarea></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('option_group_display_order'); ?></dt>
                <dd class="mava_spinbox">
                    <input type="text" class="input_text input_short mava_spinbox_value" data-step="1" data-min="0" value="<?php echo (int)$optionGroup['display_order']; ?>" name="groupDisplayOrder" id="groupDisplayOrder" />
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_plus">+</a>
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_minus">-</a>
                </dd>
            </dl>
            <dl class="row">
                <dt>&nbsp;</dt>
                <dd>
                    <input type="checkbox" name="groupDebugOnly" id="groupDebugOnly" value="1" <?php echo ($optionGroup['debug_only']==1)?"checked":""; ?> />
                    <label for="groupDebugOnly"><?php echo __('display_group_in_debug_mode_only'); ?></label>
                </dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('addon'); ?></dt>
                <dd><select name="addOnID" id="addOnID">
                        <option value=""></option>
                        <?php
                        if(isset($addon) && sizeof($addon)>0){
                            foreach($addon as $item){
                                echo '<option value="'. $item['addon_id'] .'"'. ($item['addon_id']==$optionGroup['addon_id']?" selected":"") .'>'. htmlspecialchars($item['title']) .'</option>';
                            }
                        }
                        ?>
                    </select></dd>
            </dl>

            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_edit"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/option/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
                </dd>
            </dl>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#button_submit_edit').click(function(){
            if($('#groupID').val()==''){
                $('#groupID').focus();
                return false;
            }else if($('#groupTitle').val()==''){
                $('#groupTitle').focus();
                return false;
            }else{
                MV.post(DOMAIN +'/admin/option/do_edit_group',$('#form_edit_option_group').serialize(),function(res){
                    if(res.status==1){
                        MV.dialog.st_alert('<div class="text_success"><s class="icon_success_big"></s>'+res.message+'</div>',function(){
                            setTimeout(function(){
                                window.location.href = '<?php echo Mava_Url::buildLink('admin/option/index'); ?>';
                            },300);
                        });
                    }
                });
            }
            return false;
        });


    });
</script>