<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_campaign" id="form_add_campaign" action="<?php echo Mava_Url::buildLink('admin/ads/add-campaign-group'); ?>" method="post">
        <h2 class="mava_form_title"><?php echo __('add_campaign_group'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('name'); ?> <b>*</b></dt>
                <dd><input type="text" class="input_text input_medium campaignGroupTitle" name="campaignGroupTitle" id="campaignGroupTitle" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('color'); ?></dt>
                <dd><input type="color" class="input_text input_medium campaignGroupColor" name="campaignGroupColor" id="campaignGroupColor" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('sort_order'); ?></dt>
                <dd class="mava_spinbox">
                    <input type="text" class="input_text input_short mava_spinbox_value" data-step="1" data-min="0" value="1" name="campaignGroupSortOrder" id="campaignGroupSortOrder">
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_plus">+</a>
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_minus">-</a>
                </dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/ads/campaign-groups'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
                </dd>
            </dl>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        MP.uploader.image.setup();
        <?php
            if(isset($error_message) && $error_message != ''){
        ?>
        MV.show_notice('<?php echo $error_message; ?>',3);
        <?php
            }
        ?>
        $('#campaignGroupTitle').focus();

        $('#button_submit_add').click(function(){
            $('#form_add_campaign').submit();
        });
        $('#form_add_campaign').submit(function(){
            if($('#campaignGroupTitle').val()==''){
                MV.show_notice('<?php echo __('campaign_group_title_empty'); ?>',3);
                $('#campaignGroupTitle').focus();
                return false;
            }else{
                return true;
            }
        });
    });
</script>