<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_edit_option" id="form_edit_option" action="<?php echo Mava_Url::buildLink('admin/option/do_edit_option'); ?>" method="post">
        <h2 class="mava_form_title"><?php echo __('edit_option'); ?></h2>
        <input type="hidden" name="currentOptionID" id="currentOptionID" value="<?php echo $option['option_id']; ?>" />
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('option_id'); ?><b>*</b></dt>
                <dd><input type="text" class="input_text" name="optionID" id="optionID" value="<?php echo htmlspecialchars($option['option_id']); ?>" /></dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('addon'); ?></dt>
                <dd><select name="addOnID" id="addOnID">
                        <option value=""></option>
                        <?php
                        if(isset($addon) && sizeof($addon)>0){
                            foreach($addon as $item){
                                echo '<option value="'. $item['addon_id'] .'" '. ($item['addon_id']==$option['addon_id']?'selected':'') .'>'. htmlspecialchars($item['title']) .'</option>';
                            }
                        }
                        ?>
                    </select></dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('option_title'); ?><b>*</b></dt>
                <dd><input type="text" class="input_text input_medium" name="optionTitle" id="optionTitle" value="<?php echo htmlspecialchars($optionTitle); ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('option_description'); ?></dt>
                <dd><textarea class="input_area" name="optionDescription" id="optionDescription"><?php echo htmlspecialchars($optionDescription); ?></textarea></dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('option_edit_format'); ?><b>*</b></dt>
                <dd>
                    <select name="optionEditFormat" id="optionEditFormat">
                        <option value="textbox" <?php echo ($option['edit_format']=='textbox'?'selected':''); ?>>Text Box</option>
                        <option value="spinbox" <?php echo ($option['edit_format']=='spinbox'?'selected':''); ?>>Spin Box</option>
                        <option value="onoff" <?php echo ($option['edit_format']=='onoff'?'selected':''); ?>>On/Off Check Box</option>
                        <option value="onofftextbox" <?php echo ($option['edit_format']=='onofftextbox'?'selected':''); ?>>On/Off Check Box with Text Box</option>
                        <option value="radio" <?php echo ($option['edit_format']=='radio'?'selected':''); ?>>Radio Buttons</option>
                        <option value="select" <?php echo ($option['edit_format']=='select'?'selected':''); ?>>Select Menu</option>
                        <option value="checkbox" <?php echo ($option['edit_format']=='checkbox'?'selected':''); ?>>Check Boxes</option>
                        <option value="template" <?php echo ($option['edit_format']=='template'?'selected':''); ?>>Named Template</option>
                        <option value="callback" <?php echo ($option['edit_format']=='callback'?'selected':''); ?>>PHP Callback</option>
                    </select>
                </dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('option_format_parameters'); ?></dt>
                <dd><textarea class="input_area" name="optionFormatParameters" id="optionFormatParameters"><?php echo htmlspecialchars($option['edit_format_params']); ?></textarea>
                    <ul class="format_parameter_list">
                        <li id="format_parameter_list_textbox"><?php echo __('format_parameter_list_textbox'); ?></li>
                        <li id="format_parameter_list_spinbox"><?php echo __('format_parameter_list_spinbox'); ?></li>
                        <li id="format_parameter_list_onoff"><?php echo __('format_parameter_list_onoff'); ?></li>
                        <li id="format_parameter_list_onofftextbox"><?php echo __('format_parameter_list_onofftextbox'); ?></li>
                        <li id="format_parameter_list_radio"><?php echo __('format_parameter_list_radio'); ?></li>
                        <li id="format_parameter_list_select"><?php echo __('format_parameter_list_select'); ?></li>
                        <li id="format_parameter_list_checkbox"><?php echo __('format_parameter_list_checkbox'); ?></li>
                        <li id="format_parameter_list_template"><?php echo __('format_parameter_list_template'); ?></li>
                        <li id="format_parameter_list_callback"><?php echo __('format_parameter_list_callback'); ?></li>
                    </ul>
                </dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('option_data_type'); ?><b>*</b></dt>
                <dd>
                    <select name="optionDataType" id="optionDataType">
                        <option value="boolean" <?php echo ($option['data_type']=='boolean'?'selected':''); ?>>Boolean</option>
                        <option value="string" <?php echo ($option['data_type']=='string'?'selected':''); ?>>String</option>
                        <option value="integer" <?php echo ($option['data_type']=='integer'?'selected':''); ?>>Integer</option>
                        <option value="array" <?php echo ($option['data_type']=='array'?'selected':''); ?>>Array</option>
                    </select>
                </dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('option_default_value'); ?></dt>
                <dd><textarea class="input_area" name="optionDefaultValue" id="optionDefaultValue"><?php echo htmlspecialchars($option['default_value']); ?></textarea></dd>
            </dl>

            <dl class="row option_array_sub_option">
                <dt><?php echo __('option_sub_option'); ?></dt>
                <dd><textarea class="input_area" name="optionSubOption" id="optionSubOption"><?php echo htmlspecialchars($option['sub_options']); ?></textarea>
                    <p class="gray tmall"><?php echo __('option_sub_option_description'); ?></p></dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('option_validation_callback'); ?></dt>
                <dd>
                    <input type="text" class="input_text input_normal" value="<?php echo htmlspecialchars($option['validation_class']); ?>" name="optionValidationCallbackClass" id="optionValidationCallbackClass" placeholder="Class" />
                    ::
                    <input type="text" class="input_text input_normal" value="<?php echo htmlspecialchars($option['validation_method']); ?>" name="optionValidationCallbackMethod" id="optionValidationCallbackMethod" placeholder="Method" />
                </dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('option_display_in_group'); ?><b>*</b></dt>
                <dd class="mava_check_boxes_list optionDisplayGroup">
                    <?php
                    if(sizeof($option_group) > 0){
                        foreach($option_group as $item){
                            if(in_array($item['group_id'],$sortKey)){
                                $selected = true;
                                $sortOrder = isset($sortData[$item['group_id']])?$sortData[$item['group_id']]:0;
                            }else{
                                $selected = false;
                                $sortOrder = 0;
                            }
                            echo '<div class="option_group_item"><input type="checkbox" class="optionDisplayInGroup" '. ($selected?'checked':'') .' name="optionDisplayInGroup[]" value="'. htmlspecialchars($item['group_id']) .'" id="optionGroup'. htmlspecialchars($item['group_id']) .'" /><label for="optionGroup'. htmlspecialchars($item['group_id']) .'">'. htmlspecialchars(__('_option_group_title_'. $item['group_id'])) .'</label>
                                <div class="display_order mava_spinbox '. ($selected?'':'hidden') .'">
                                    <input type="text" '. ($selected?'':'disabled') .' class="input_text input_short mava_spinbox_value" data-step="1" data-min="0" value="'. $sortOrder .'" name="optionGroupDisplayOrder[]" />
                                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_plus">+</a>
                                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_minus">-</a>
                                </div>
                                </div>';
                        }
                    }
                    ?>
                </dd>
            </dl>

            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_edit"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/option/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/option/delete',array('optionID' => $option['option_id'])); ?>" option-id="<?php echo $option['option_id']; ?>" option-title="<?php echo __('_option_title_'. $option['option_id']); ?>" class="mava_button mava_button_gray mava_button_medium button_delete_option"><?php echo __('delete_option'); ?></a>
                </dd>
            </dl>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){

        $('#format_parameter_list_'+$('#optionEditFormat').val()).show();

        $('#optionEditFormat').change(function(){
            $('.format_parameter_list li').hide();
            $('#format_parameter_list_'+$(this).val()).show();
            if($(this).val()=='template' || $(this).val()=='callback'){
                $('.option_array_sub_option').show();
            }else{
                $('.option_array_sub_option').hide();
            }
        });

        $('#button_submit_edit').click(function(){
            if($('#optionID').val()==''){
                $('#optionID').focus();
                return false;
            }else if($('#optionTitle').val()==''){
                $('#optionTitle').focus();
                return false;
            }else if($('#optionEditFormat').val()==''){
                $('#optionEditFormat').focus();
                $('#optionEditFormat').parents('dl').find('dt').fadeOut(200).fadeIn(100).fadeOut(200).fadeIn(100);
                return false;
            }else if($('#optionDataType').val()==''){
                $('#optionDataType').focus();
                $('#optionDataType').parents('dl').find('dt').fadeOut(200).fadeIn(100).fadeOut(200).fadeIn(100);
                return false;
            }else if($('.optionDisplayInGroup:checked').length==0){
                $('.optionDisplayGroup').parents('dl').find('dt').fadeOut(200).fadeIn(100).fadeOut(200).fadeIn(100);
                return false;
            }else{
                MV.post(DOMAIN +'/admin/option/do_edit_option',$('#form_edit_option').serialize(),function(res){
                    if(res.status==1){
                        MV.dialog.st_alert('<div class="text_success"><s class="icon_success_big"></s>'+res.message+'</div>',function(){
                            setTimeout(function(){
                                window.location.href = '<?php echo Mava_Url::buildLink('admin/option/index'); ?>';
                            },300);
                        });
                    }
                });
            }
            return false;
        });

        $('.optionDisplayGroup .option_group_item label').click(function(){
            if($(this).parents('.option_group_item').find('input.optionDisplayInGroup').is(":checked")){
                $(this).parents('.option_group_item').find('.display_order').slideUp(200);
                $(this).parents('.option_group_item').find('.display_order input').attr('disabled',true);
            }else{
                $(this).parents('.option_group_item').find('.display_order').hide().removeClass('hidden').slideDown(200);
                $(this).parents('.option_group_item').find('.display_order input').removeAttr('disabled');
            }
        });

        $('.button_delete_option').on('click',function(){
            var optionID = $(this).attr('option-id');
            MV.dialog.st_confirm('<?php echo __('delete_option_confirm'); ?><div class="space"></div><b>'+ $(this).attr('option-title') +'</b>',function(){
                MV.post(DOMAIN+'/admin/option/delete',{
                    optionID: optionID
                },function(res){
                    if(res.status==1){
                        MV.dialog.st_alert('<div class="text_success"><s class="icon_success_big"></s>'+ res.message +'</div>',function(){
                            window.location.href = '<?php echo Mava_Url::buildLink('admin/option/index'); ?>';
                        });
                    }else{
                        MV.dialog.st_alert('<div class="text_error"><s class="icon_error_big"></s>'+res.message+'</div>');
                    }
                });
            },function(){
                // cancel
            });
            return false;
        });

    });
</script>