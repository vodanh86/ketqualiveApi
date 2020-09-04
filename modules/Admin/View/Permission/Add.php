<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_permission" id="form_add_permission" action="<?php echo Mava_Url::buildLink('admin/permission/add'); ?>" method="post" enctype="multipart/form-data">
        <h2 class="mava_form_title"><?php echo __('add_permission'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('permission_group'); ?></dt>
                <dd>
                    <select name="permissionGroup" id="permissionGroup">
                        <option value="0">- <?php echo __('choose'); ?> -</option>
                        <?php
                            if(is_array($permissionGroup) && count($permissionGroup) > 0){
                                foreach($permissionGroup as $item){
                                    echo '<option value="'. (int)$item['group_id'] .'">'. htmlspecialchars($item['title']) .'</option>';
                                }
                            }
                        ?>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('title'); ?></dt>
                <dd><input type="text" class="input_text input_medium" name="permissionTitle" id="permissionTitle" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('permission_key'); ?></dt>
                <dd><input type="text" class="input_text input_medium" name="permissionKey" id="permissionKey" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('sort_order'); ?></dt>
                <dd class="mava_spinbox">
                    <input type="text" class="input_text input_short mava_spinbox_value" data-step="1" data-min="0" value="0" name="permissionSortOrder" id="permissionSortOrder">
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_plus">+</a>
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_minus">-</a>
                </dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/permission/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
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
            $('#form_add_permission').submit();
        });
        $('#form_add_permission').submit(function(){
            if($('#permissionGroup').val()==0){
                $('#permissionGroup').focus();
                return false;
            }else if($('#permissionTitle').val()==''){
                $('#permissionTitle').focus();
                return false;
            }else if($('#permissionKey').val()==''){
                $('#permissionKey').focus();
                return false;
            }else{
                return true;
            }
        });
    });
</script>