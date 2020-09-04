<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_edit_banner" id="form_edit_banner" action="<?php echo Mava_Url::buildLink('admin/banner/edit', array('id' => $banner['id'])); ?>" method="post">
        <h2 class="mava_form_title"><?php echo __('edit_banner'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><label for="bannerPosition"><?php echo __('banner_position'); ?></label></dt>
                <dd>
                    <select name="bannerPosition" id="bannerPosition">
                        <option value="0">- <?php echo __('choose'); ?> -</option>
                        <?php
                        if(isset($position) && is_array($position) && count($position) > 0){
                            foreach($position as $item){
                                echo '<option value="'. (int)$item['id'] .'"'. ($item['id']==$banner['position_id']?' selected':'') .'>'. $item['title'] .' ('. $item['position'] .')</option>';
                            }
                        }
                        ?>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt><label for="bannerSortOrder"><?php echo __('sort_order'); ?></label></dt>
                <dd class="mava_spinbox">
                    <input type="text" class="input_text input_short mava_spinbox_value" data-step="1" data-min="0" value="<?php echo (int)$banner['sort_order']; ?>" name="bannerSortOrder" id="bannerSortOrder" />
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_plus">+</a>
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_minus">-</a>
                </dd>
            </dl>
            <?php
            if(isset($languages) && is_array($languages) && count($languages) > 0){
                $count = 0;
                echo '<div class="mava_row_tab"><dl class="row row_tab'. (count($languages)==1?' hidden':'') .'"><dt></dt><dd><ul class="clearfix">';
                foreach($languages as $item){
                    $count++;
                    echo '<li><a href="javascript:void(0);" class="item'. ($count == 1?" active":"") .'" rel="language_tab_'. $item['language_id'] .'">'. htmlspecialchars($item['title']) .'</a></li>';
                }
                echo '</ul></dd></dl>';
                $count = 0;
                foreach($languages as $item){
                    $count++;
                    ?>
                    <div class="row_group<?php echo $count>1?" hidden":""; ?>" id="language_tab_<?php echo $item['language_id']; ?>">
                        <dl class="row">
                            <dt><?php echo __('image'); ?></dt>
                            <dd>
                                <input type="hidden" class="tl_image_upload" data-folder="banner" data-input-name="bannerImage[<?php echo $item['language_code']; ?>]" />
                                <?php
                                    $has_data = (isset($banner['_data']) && isset($banner['_data'][$item['language_code']]))?true:false;
                                    if($has_data && $banner['_data'][$item['language_code']]['image'] != ""){
                                        $images = json_decode($banner['_data'][$item['language_code']]['image'],true);
                                        if(is_array($images) && count($images) > 0){
                                            $count = 0;
                                            foreach($images as $img){
                                                $count++;
                                                echo '<input type="hidden" name="bannerImage['. $item['language_code'] .'][]" class="tl_ui_ih_bannerImage-'. $count .'" data-class="bannerImage-'. $count .'" value="'. htmlspecialchars(trim($img)) .'" />';
                                            }
                                        }
                                    }
                                ?>
                            </dd>
                        </dl>
                        <dl class="row">
                            <dt><?php echo __('title'); ?></dt>
                            <dd><input value="<?php echo $has_data && isset($banner['_data'][$item['language_code']]['title'])?htmlspecialchars($banner['_data'][$item['language_code']]['title']):''; ?>" type="text" class="input_text input_medium bannerTitle" name="bannerTitle[<?php echo $item['language_code']; ?>]" id="bannerTitle_<?php echo $item['language_id']; ?>" /></dd>
                        </dl>
                        <dl class="row">
                            <dt><?php echo __('subtitle'); ?></dt>
                            <dd><input value="<?php echo $has_data && isset($banner['_data'][$item['language_code']]['subtitle'])?htmlspecialchars($banner['_data'][$item['language_code']]['subtitle']):''; ?>" type="text" class="input_text input_medium bannerSubtitle" name="bannerSubtitle[<?php echo $item['language_code']; ?>]" id="bannerSubtitle_<?php echo $item['language_id']; ?>" /></dd>
                        </dl>
                        <dl class="row">
                            <dt><?php echo __('link'); ?></dt>
                            <dd><input value="<?php echo $has_data && isset($banner['_data'][$item['language_code']]['href'])?htmlspecialchars($banner['_data'][$item['language_code']]['href']):''; ?>" type="text" class="input_text input_medium bannerHref" name="bannerHref[<?php echo $item['language_code']; ?>]" id="bannerHref_<?php echo $item['language_id']; ?>" /></dd>
                        </dl>
                        <dl class="row">
                            <dt><?php echo __('css_background'); ?></dt>
                            <dd><input value="<?php echo $has_data && isset($banner['_data'][$item['language_code']]['background'])?htmlspecialchars($banner['_data'][$item['language_code']]['background']):''; ?>" type="text" class="input_text input_medium bannerBackground" name="bannerBackground[<?php echo $item['language_code']; ?>]" id="bannerBackground_<?php echo $item['language_id']; ?>" /></dd>
                        </dl>
                    </div>
                <?php
                }
                echo '</div>';
            }
            ?>

            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_edit"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/banner/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
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

        $('#button_submit_edit').click(function(){
            $('#form_edit_banner').submit();
        });
        $('#form_edit_banner').submit(function(){
            if($('#bannerPosition').val()==0){
                MV.show_notice('<?php echo __('please_choose_banner_position'); ?>',3);
                $('#bannerPosition').focus();
                return false;
            }else if($('.tl_iu_preview > .item').length==0){
                MV.show_notice('<?php echo __('please_upload_banner_image'); ?>',3);
                return false;
            }else{
                return true;
            }
        });
    });
</script>