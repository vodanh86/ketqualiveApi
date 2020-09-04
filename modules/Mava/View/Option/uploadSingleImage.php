<div class="image_uploader">
    <input type="hidden" class="image_uploader_value" name="option[<?php echo $option['option_id']; ?>]" id="option<?php echo $option['option_id']; ?>" value="<?php echo htmlspecialchars($option['option_value']); ?>">
    <input type="file" class="image_uploader_input" name="image_uploader_input_<?php echo $option['option_id']; ?>" option-id="<?php echo $option['option_id']; ?>" accept="image/*"/>
    <div class="image_uploader_preview">
        <?php
            if($option['option_value']!=""){
                echo '<img src="'. get_option_file_url($option['option_value']) .'" class="image_uploader_preview_img" /><a href="javascript:void(0);" class="image_uploader_delete">'. __('delete') .'</a>';
            }
        ?>
    </div>
</div>