<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_edit_campaign" id="form_edit_campaign" action="<?php echo Mava_Url::buildLink('admin/ads/edit-campaign', array('id' => $campaign['id'])); ?>" method="post">
        <h2 class="mava_form_title"><?php echo __('edit_campaign'); ?></h2>
        <?php
        if($campaign['deleted'] == 'yes'){
            echo '<div class="alert alert-danger">'. __('campaign_has_been_deleted') .'</div>';
        }
        ?>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('campaign_group'); ?></dt>
                <dd>
                    <select class="input_text input_medium campaignGroupID" name="campaignGroupID" id="campaignGroupID">
                        <option value="0"><?php echo __('ungrouped'); ?></option>
                        <?php
                        if(isset($campaign_groups) && is_array($campaign_groups) && count($campaign_groups) > 0){
                            foreach($campaign_groups as $item){
                                echo '<option value="'. $item['id'] .'"'. ($item['id']==$campaign['group_id']?' selected':'') .'>'. htmlspecialchars($item['title']) .'</option>';
                            }
                        }
                        ?>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('name'); ?> <b>*</b></dt>
                <dd><input type="text" <?php echo $campaign['deleted'] == 'yes'?'readonly="true"':'' ?> class="input_text input_medium campaignTitle<?php echo $campaign['deleted'] == 'yes'?' disabled':'' ?>" name="campaignTitle" id="campaignTitle" value="<?php echo htmlspecialchars($campaign['title']); ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('note'); ?></dt>
                <dd><textarea class="input_area campaignNote<?php echo $campaign['deleted'] == 'yes'?' disabled':'' ?>" <?php echo $campaign['deleted'] == 'yes'?'readonly="true"':'' ?> name="campaignNote" id="campaignNote"><?php echo htmlspecialchars($campaign['note']); ?></textarea></dd>
            </dl>
            <?php if($campaign['deleted'] == 'no'){ ?>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_edit"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/ads/campaigns'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
                </dd>
            </dl>
            <?php } ?>
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

        $('#button_submit_edit').click(function(){
            $('#form_edit_campaign').submit();
        });
        $('#form_edit_campaign').submit(function(){
            if($('#campaignTitle').val()==''){
                MV.show_notice('<?php echo __('campaign_name_empty'); ?>',3);
                $('#campaignTitle').focus();
                return false;
            }else{
                return true;
            }
        });
    });
</script>