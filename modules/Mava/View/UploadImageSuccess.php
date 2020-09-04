<script type="text/javascript">
    <?php
        if($type == 'editor'){
    ?>
    $('.mce-btn.mce-open', parent.document).parent().find('.mce-textbox').val('<?php echo $image_url; ?>').closest('.mce-window').find('.mce-primary').click();
    <?php }else if($type == 'avatar'){
    ?>
        $('.avatar_preview', parent.document).attr('src', '<?php echo $image_url .'?rd='. time(); ?>');
        $('.upload_image_loading', parent.document).hide();
    <?php
    }else if($type == 'attachment'){
        $target = str_replace(' ','.', trim(Mava_Url::getParam('target')));
    ?>
        $('.<?php echo $target; ?> .image_attach_value', parent.document).val('<?php echo $image; ?>');
        $('.<?php echo $target; ?> .image_attach_preview', parent.document).attr('src','<?php echo $image_url; ?>').show();
    <?php
    }
     ?>
</script>