<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_addon" id="form_add_addon" action="<?php echo Mava_Url::buildLink('admin/add-ons/add'); ?>" method="post" enctype="multipart/form-data">
        <h2 class="mava_form_title"><?php echo __('add_addon'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('addon_id'); ?></dt>
                <dd><input type="text" class="input_text" name="addOnId" id="addOnId" value="<?php echo isset($addOnId)?htmlspecialchars($addOnId):""; ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('addon_title'); ?></dt>
                <dd><input type="text" class="input_text" name="addOnTitle" id="addOnTitle" value="<?php echo isset($addOnTitle)?htmlspecialchars($addOnTitle):""; ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('addon_version_string'); ?></dt>
                <dd><input type="text" class="input_text" name="addOnVersionString" id="addOnVersionString" value="<?php echo isset($addOnVersionString)?htmlspecialchars($addOnVersionString):"1.0"; ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('addon_version_id'); ?></dt>
                <dd class="mava_spinbox">
                    <input type="text" class="input_text input_short mava_spinbox_value" data-step="1" data-min="0" value="<?php echo isset($addOnVersionId)?(int)$addOnVersionId:1; ?>" name="addOnVersionId" id="addOnVersionId">
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_plus">+</a>
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_minus">-</a>
                </dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('addon_url'); ?></dt>
                <dd><input type="text" class="input_text" name="addOnUrl" id="addOnUrl" value="<?php echo isset($addOnUrl)?htmlspecialchars($addOnUrl):""; ?>" /></dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('addon_install_code'); ?></dt>
                <dd>
                    <input type="text" class="input_text input_normal" name="addOnInstallClass" id="addOnInstallClass" value="<?php echo isset($addOnInstallClass)?htmlspecialchars($addOnInstallClass):""; ?>" placeholder="Class" /> ::
                    <input type="text" class="input_text input_normal" name="addOnInstallMethod" id="addOnInstallMethod" value="<?php echo isset($addOnInstallMethod)?htmlspecialchars($addOnInstallMethod):""; ?>" placeholder="Method"  />
                </dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('addon_uninstall_code'); ?></dt>
                <dd>
                    <input type="text" class="input_text input_normal" name="addOnUninstallClass" id="addOnUninstallClass" value="<?php echo isset($addOnUninstallClass)?htmlspecialchars($addOnUninstallClass):""; ?>" placeholder="Class" /> ::
                    <input type="text" class="input_text input_normal" name="addOnUninstallMethod" id="addOnUninstallMethod" value="<?php echo isset($addOnUninstallMethod)?htmlspecialchars($addOnUninstallMethod):""; ?>" placeholder="Method"  />
                </dd>
            </dl>

            <dl class="row">
                <dt>&nbsp;</dt>
                <dd>
                    <input type="checkbox" value="1" name="addOnActivated" id="addOnActivated" <?php echo (isset($addOnActivated) && (int)$addOnActivated==1)?'checked':''; ?>/>
                    <label for="courseActivated"><?php echo __('active'); ?></label>
                </dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/add-ons/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
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
        $('#addOnId').focus();

        $('#button_submit_add').click(function(){
            $('#form_add_addon').submit();
        });

        $('#form_add_addon').submit(function(){
            if($('#addOnId').val()==''){
                $('#addOnId').focus();
                return false;
            }else if($('#addOnTitle').val() == ''){
                $('#addOnTitle').focus();
                return false;
            }else{
                return true;
            }
            return false;
        });


    });
</script>