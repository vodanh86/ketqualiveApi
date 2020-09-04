<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_option_setting" id="form_option_setting" enctype="multipart/form-data" action="<?php echo Mava_Url::buildLink('admin/option/setting'); ?>" method="post">
        <input type="hidden" name="groupID" id="groupID" value="<?php echo $optionGroup['group_id']; ?>" />
        <div class="table_action_button clearfix">
            <div class="fl mava_table_title"><?php echo htmlspecialchars(__('_option_group_title_'. $optionGroup['group_id'])); ?></div>
            <?php if(is_debug()){ ?>
            <div class="fr">
                <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/option/add_option',array('groupID' => $optionGroup['group_id'])); ?>">+ <?php echo __('add_option'); ?></a>
                <a class="ml5 mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/option/edit_option_sort',array('groupID' => $optionGroup['group_id'])); ?>"><?php echo __('edit_option_order'); ?></a>
                <a class="ml5 mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/option/edit_group',array('groupID' => $optionGroup['group_id'])); ?>"><?php echo __('edit_option_group'); ?></a>
            </div>
            <?php } ?>
        </div>
        <div class="mava_form_rows">
            <?php
                if(sizeof($options) > 0){
                    foreach($options as $item){
                        echo '<dl class="row option_setting_row">';
                        if(is_debug()){
                            echo '<a href="'. Mava_Url::buildLink('admin/option/edit_option',array('optionID' => $item['option_id'])) .'" title="'. htmlspecialchars($item['option_id']) .'" class="edit_option_link quickTooltip">Sửa</a>';
                        }
                        echo '<dt>';
                        if(!in_array($item['edit_format'],array('onoff','onofftextbox'))){
                            echo htmlspecialchars(__('_option_title_'. $item['option_id']));
                        }
                        echo '</dt>';
                        $cVal = $item['option_value'];
                        $editFormatParams = array();
                        if($item['edit_format_params']!="" && !in_array($item['edit_format'],array('template','callback'))){
                            $formatParams = preg_split('/\r\n|\r|\n/', $item['edit_format_params']);
                            if(sizeof($formatParams) > 0){
                                foreach($formatParams as $fp){
                                    if(trim($fp)!=""){
                                        $formatItem = explode('=',$fp);
                                        if(sizeof($formatItem) > 1){
                                            $editFormatParams[$formatItem[0]] = $formatItem[1];
                                        }
                                    }
                                }
                            }
                        }
                        switch($item['edit_format']){
                            case 'textbox':
                                if(isset($editFormatParams['rows']) && (int)$editFormatParams['rows'] > 1){
                                    echo '<dd><textarea '. (isset($editFormatParams['placeholder'])?'placeholder="'. htmlspecialchars($editFormatParams['placeholder']) .'"':'') .' class="input_area" name="option['. htmlspecialchars($item['option_id']) .']" id="option'. htmlspecialchars($item['option_id']) .'" rows="'. (int)$editFormatParams['rows'] .'">'. htmlspecialchars($cVal) .'</textarea>';
                                }else{
                                    echo '<dd><input type="text" '. (isset($editFormatParams['placeholder'])?'placeholder="'. htmlspecialchars($editFormatParams['placeholder']) .'"':'') .' class="input_text" name="option['. htmlspecialchars($item['option_id']) .']" id="option'. htmlspecialchars($item['option_id']) .'" value="'. htmlspecialchars($cVal) .'" />';
                                }
                            break;

                            case 'spinbox':
                                echo '<dd class="mava_spinbox">
                                <input type="text" class="input_text input_short mava_spinbox_value" data-step="'. (isset($editFormatParams['step'])?(int)$editFormatParams['step']:1) .'" data-min="'. (isset($editFormatParams['min'])?(int)$editFormatParams['min']:0) .'" data-max="'. (isset($editFormatParams['max'])?(int)$editFormatParams['max']:0) .'" value="'. (int)$cVal .'" name="option['. htmlspecialchars($item['option_id']) .']" id="option'. htmlspecialchars($item['option_id']) .'" />
                                <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_plus">+</a>
                                <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_minus">-</a>
                            ';
                            break;

                            case 'onoff':
                                echo '<dd><input type="checkbox" value="1"  name="option['. htmlspecialchars($item['option_id']) .']" id="option'. htmlspecialchars($item['option_id']) .'" '. ((int)$cVal==1?'checked':'') .'><label for="option'. htmlspecialchars($item['option_id']) .'">'. __('_option_title_'. $item['option_id']) .'</label>';
                            break;

                            case 'callback':
                                $callbackObj = explode('::',$item['edit_format_params']);
                                if(is_callable($item['edit_format_params'])){
                                        $callbackHtml = call_user_func($item['edit_format_params'],$item);
                                        echo '<dd>'. $callbackHtml .'';
                                }else{
                                    echo '<dd>'. __('invalid_edit_format_params') .'';
                                }
                            break;

                            case 'onofftextbox':
                                if($cVal!="" && Mava_String::isJson($cVal)){
                                    $cVal = json_decode($cVal,true);
                                    if(!isset($cVal['value'])){
                                        $cVal['value'] = 0;
                                    }
                                    if(!isset($cVal['text'])){
                                        $cVal['text'] = '';
                                    }
                                }else{
                                    $cVal = array(
                                        'value' => 0,
                                        'text' => ''
                                    );
                                }
                                echo '<dd class="mava_onoff_textbox">
                                    <input type="checkbox" value="1"  name="option['. htmlspecialchars($item['option_id']) .'][value]" id="option'. htmlspecialchars($item['option_id']) .'_value" '. ((int)$cVal['value']==1?'checked':'') .'><label for="option'. htmlspecialchars($item['option_id']) .'_value">'. __('_option_title_'. $item['option_id']) .'</label>
                                    <div class="textbox"><input type="text" '. (isset($editFormatParams['placeholder'])?'placeholder="'. htmlspecialchars($editFormatParams['placeholder']) .'"':'') .' class="input_text '. ($cVal['value']==0?'disabled':'') .'" '. ($cVal['value']==0?'disabled':'') .' name="option['. htmlspecialchars($item['option_id']) .'][text]" id="option'. htmlspecialchars($item['option_id']) .'_text" value="'. htmlspecialchars($cVal['text']) .'" /></div>
                               ';
                            break;

                            case 'radio':
                                echo '<dd>';
                                if(sizeof($editFormatParams) > 0){
                                    foreach($editFormatParams as $k => $v){
                                        echo '<div class="radio_row"><input type="radio" value="'. $k .'"  name="option['. htmlspecialchars($item['option_id']) .']" id="option'. htmlspecialchars($item['option_id']) .'_'. md5($k) .'" '. ($cVal==$k?'checked':'') .'><label for="option'. htmlspecialchars($item['option_id']) .'_'. md5($k) .'">'. htmlspecialchars($v) .'</label></div>';
                                    }
                                }
                            break;

                            case 'select':
                                $optionData = array();
                                if(sizeof($editFormatParams) > 0){
                                    foreach($editFormatParams as $k => $v){
                                        $optionData[] = array(
                                            'value' => $k,
                                            'text' => $v
                                        );
                                    }
                                }
                                echo '<dd>'. Mava_Helper_Input::renderSelectOptionHtml($optionData, $cVal, 'option'. $item['option_id'],'','option['. $item['option_id'] .']');
                            break;

                            case 'checkbox':
                                echo '<dd>';
                                if(sizeof($editFormatParams) > 0){
                                    if($cVal!="" && Mava_String::isJson($cVal)){
                                        $cVal = json_decode($cVal,true);
                                    }else{
                                        $cVal = array();
                                    }
                                    foreach($editFormatParams as $k => $v){
                                        echo '<div class="checkbox_row"><input type="checkbox" value="'. $k .'"  name="option['. htmlspecialchars($item['option_id']) .'][]" id="option'. htmlspecialchars($item['option_id']) .'_'. md5($k) .'" '. (in_array($k,$cVal)?'checked':'') .'><label for="option'. htmlspecialchars($item['option_id']) .'_'. md5($k) .'">'. htmlspecialchars($v) .'</label></div>';
                                    }
                                }
                            break;

                            case 'template':
                                if($item['edit_format_params']!=""){
                                    echo '<dd>';
                                    $responseView = Mava_View::getView($item['edit_format_params'],array('option' => $item),false);
                                    if($responseView===false){
                                        echo __('template_file_not_found');
                                    }else{
                                        echo $responseView;
                                    }
                                }
                            break;
                        }
                        echo '<div class="gray tsmall option_description">'. htmlspecialchars(__('_option_description_'. $item['option_id'],array(),false)) .'</div></dd>';
                        echo '</dl>';
                    }
                }
            ?>

            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_edit"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/option/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
                </dd>
            </dl>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('.mava_onoff_textbox .checkbox').click(function(){
            if($(this).is(':checked')){
                $(this).parents('.mava_onoff_textbox').find('.textbox input').removeAttr('disabled').removeClass('disabled').focus();
            }else{
                $(this).parents('.mava_onoff_textbox').find('.textbox input').attr('disabled',true).addClass('disabled');
            }
        });

        $('#button_submit_edit').click(function(){
            MV.post(DOMAIN +'/admin/option/save_setting',$('#form_option_setting').serialize(),function(res){
                if(res.status==1){
                    MV.dialog.st_alert('<div class="text_success"><s class="icon_success_big"></s>'+res.message+'</div>',function(){
                        setTimeout(function(){
                            window.location.href = '<?php echo Mava_Url::buildLink('admin/option/index'); ?>';
                        },300);
                    });
                }
            });
            return false;
        });

        $('.image_uploader_input').change(function(){
            if($(this).val()!=""){
                if(!$('#image_uploader_frame').length){
                    $('body').append('<iframe style="border: 0;width:0;height:0;display: none" id="image_uploader_frame" name="image_uploader_frame" frameborder="0" width="0" height="0"></iframe>');
                }
                var defaultAction = $('#form_option_setting').attr('action');
                var defaultTarget = $('#form_option_setting').attr('target');
                var uploadOptionImageDomain = DOMAIN + '/admin/option/upload_image?optionID='+ $(this).attr('option-id');
                $('#form_option_setting').attr('action',uploadOptionImageDomain);
                $('#form_option_setting').attr('target','image_uploader_frame');
                $('#form_option_setting').submit();
                $('#form_option_setting').attr('action',defaultAction);
                $('#form_option_setting').attr('target',defaultTarget);
            }
        });

        $('.image_uploader_delete').on('click',function(){
            $(this).parents('.image_uploader').find('.image_uploader_value').val('');
            $(this).parents('.image_uploader').find('.image_uploader_preview').html('');
        });
    });
</script>