<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_campaign" id="form_add_campaign" action="<?php echo Mava_Url::buildLink('admin/ads/add-campaign'); ?>" method="post">
        <h2 class="mava_form_title"><?php echo __('add_campaign'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('campaign_group'); ?></dt>
                <dd>
                    <select class="input_text input_medium campaignGroupID" name="campaignGroupID" id="campaignGroupID">
                        <option value="0"><?php echo __('ungrouped'); ?></option>
                        <?php
                            if(isset($campaign_groups) && is_array($campaign_groups) && count($campaign_groups) > 0){
                                foreach($campaign_groups as $item){
                                    echo '<option value="'. $item['id'] .'">'. htmlspecialchars($item['title']) .'</option>';
                                }
                            }
                        ?>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('name'); ?> <b>*</b></dt>
                <dd><input type="text" class="input_text input_medium campaignTitle" name="campaignTitle" id="campaignTitle" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('note'); ?></dt>
                <dd><textarea class="input_area campaignNote" name="campaignNote" id="campaignNote"></textarea></dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/ads/campaigns'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
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
        $('#campaignTitle').focus();

        $('#button_submit_add').click(function(){
            $('#form_add_campaign').submit();
        });
        $('#form_add_campaign').submit(function(){
            if($('#campaignTitle').val()==''){
                MV.show_notice('<?php echo __('campaign_name_empty'); ?>',3);
                $('#campaignTitle').focus();
                return false;
            }else if($('#campaignLink').val()==''){
                MV.show_notice('<?php echo __('campaign_link_empty'); ?>',3);
                $('#campaignLink').focus();
                return false;
            }else{
                return true;
            }
        });
    });
</script>