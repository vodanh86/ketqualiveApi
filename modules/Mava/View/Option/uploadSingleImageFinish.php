<script type="text/javascript" src="<?php echo Mava_View::getJsUrl('global'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function(){
    <?php
        if($status==1){
    ?>
        var uploadPanel = $('#option<?php echo $optionID; ?>',parent.document).parents('.image_uploader');
        uploadPanel.find('.image_uploader_preview').html('<img src="<?php echo $fileUrl; ?>" class="image_uploader_preview_img" /><a href="javascript:void(0);" class="image_uploader_delete"><?php echo __('delete'); ?></a>');
        uploadPanel.find('.image_uploader_value').val('<?php echo $fileName; ?>');
        uploadPanel.find('.image_uploader_input').val('');
    <?php
        }else{
    ?>
    window.parent.MV.dialog.st_alert('<div class="text_error"><s class="icon_error_big"></s><?php echo htmlspecialchars($message); ?></div>');
    <?php
        }
    ?>
    });
</script>