<script type="text/javascript">
    <?php
    if(isset($uploaded) && is_array($uploaded) && count($uploaded) > 0){
        foreach($uploaded as $item){
        if($item['height'] > $item['width']){
            $h = 100;
            $w = (100/$item['height']) * $item['width'];
        }else{
            $w = 100;
            $h = (100/$item['width']) * $item['height'];
        }
        $margin_top = (100-$h)/2;
        $margin_left = (100-$w)/2;
        $key = md5($item['image']);
        $gender = '';
        $gender_input = '';
        if(isset($folder) && $folder == 'products'){
            $gender_input .= '<input type="hidden" name="gender_'. $input_name .'['. $key .']" class="tl_ui_ig_'. $key .'" value="0" />';
            $gender .= '<div class="tl_iu_gender"><label class="male"><input type="checkbox" value="male" rel="'. $key .'"/> '. __('male') .'</label><label class="female"><input type="checkbox" value="female"/> '. __('female') .'</label></div>';
        }
?>
    $('<?php echo $gender_input; ?><input type="hidden" name="<?php echo $input_name; ?>[]" class="tl_ui_ih_<?php echo $key; ?>" value="<?php echo htmlspecialchars(json_encode($item)); ?>" />').insertBefore($('#tl_uploader_<?php echo $uploader_id; ?>', parent.document));
    $('#tl_uploader_<?php echo $uploader_id; ?> .tl_iu_preview', parent.document).append('<div class="item" data-key="<?php echo $key; ?>"><?php echo $gender; ?><a href="javascript:void(0);" class="tl_ui_item_remove" title="<?php echo __('delete'); ?>"><i class="fa fa-times"></i></a><img src="<?php echo image_url($item['image']); ?>" style="margin-top:<?php echo $margin_top; ?>px;margin-left:<?php echo $margin_left; ?>px" /></div>');
    <?php
            }
        }else{
    ?>
    parent.MP.notice.show('<?php echo __('success_but_no_image_upload'); ?>','warning',3);

    <?php
        }
    ?>
</script>