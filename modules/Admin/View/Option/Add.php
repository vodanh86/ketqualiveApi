<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_option" id="form_add_option" action="<?php echo Mava_Url::buildLink('admin/option/do_add_option'); ?>" method="post">
        <h2 class="mava_form_title"><?php echo __('create_new_option'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('option_id'); ?><b>*</b></dt>
                <dd><input type="text" class="input_text" name="optionID" id="optionID" /></dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('addon'); ?></dt>
                <dd><select name="addOnID" id="addOnID">
                        <option value=""></option>
                        <?php
                        if(isset($addon) && sizeof($addon)>0){
                            foreach($addon as $item){
                                echo '<option value="'. $item['addon_id'] .'">'. htmlspecialchars($item['title']) .'</option>';
                            }
                        }
                        ?>
                    </select></dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('option_title'); ?><b>*</b></dt>
                <dd><input type="text" class="input_text input_medium" name="optionTitle" id="optionTitle" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('option_description'); ?></dt>
                <dd><textarea class="input_area" name="optionDescription" id="optionDescription"></textarea></dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('option_edit_format'); ?><b>*</b></dt>
                <dd>
                    <select name="optionEditFormat" id="optionEditFormat">
                        <option value="textbox">Text Box</option>
                        <option value="spinbox">Spin Box</option>
                        <option value="onoff">On/Off Check Box</option>
                        <option value="onofftextbox">On/Off Check Box with Text Box</option>
                        <option value="radio">Radio Buttons</option>
                        <option value="select">Select Menu</option>
                        <option value="checkbox">Check Boxes</option>
                        <option value="template">Named Template</option>
                        <option value="callback">PHP Callback</option>
                    </select>
                </dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('option_format_parameters'); ?></dt>
                <dd><textarea class="input_area" name="optionFormatParameters" id="optionFormatParameters"></textarea>
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
                        <option value="boolean">Boolean</option>
                        <option value="string" selected="selected">String</option>
                        <option value="integer">Integer</option>
                        <option value="array">Array</option>
                    </select>
                </dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('option_default_value'); ?></dt>
                <dd><textarea class="input_area" name="optionDefaultValue" id="optionDefaultValue"></textarea></dd>
            </dl>

            <dl class="row option_array_sub_option">
                <dt><?php echo __('option_sub_option'); ?></dt>
                <dd><textarea class="input_area" name="optionSubOption" id="optionSubOption"></textarea>
                <p class="gray tmall"><?php echo __('option_sub_option_description'); ?></p></dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('option_validation_callback'); ?></dt>
                <dd>
                    <input type="text" class="input_text input_normal" name="optionValidationCallbackClass" id="optionValidationCallbackClass" placeholder="Class" />
                    ::
                    <input type="text" class="input_text input_normal" name="optionValidationCallbackMethod" id="optionValidationCallbackMethod" placeholder="Method" />
                </dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('option_display_in_group'); ?><b>*</b></dt>
                <dd class="mava_check_boxes_list optionDisplayGroup">
                    <?php
                        if(sizeof($option_group) > 0){
                            foreach($option_group as $item){
                                echo '<div class="option_group_item"><input type="checkbox" class="optionDisplayInGroup" '. ($optionGroup['group_id']==$item['group_id']?'checked':'') .' name="optionDisplayInGroup[]" value="'. htmlspecialchars($item['group_id']) .'" id="optionGroup'. htmlspecialchars($item['group_id']) .'" /><label for="optionGroup'. htmlspecialchars($item['group_id']) .'">'. htmlspecialchars(__('_option_group_title_'. $item['group_id'])) .'</label>
                                <div class="display_order mava_spinbox '. ($optionGroup['group_id']==$item['group_id']?'':'hidden') .'">
                                    <input type="text" '. ($optionGroup['group_id']==$item['group_id']?'':'disabled') .' class="input_text input_short mava_spinbox_value" data-step="1" data-min="0" value="0" name="optionGroupDisplayOrder[]" />
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
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/option/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
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

        $('#optionID').focus();

        $('#button_submit_add').click(function(){
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
                MV.post(DOMAIN +'/admin/option/do_add_option',$('#form_add_option').serialize(),function(res){
                    if(res.status==1){
                        MV.dialog.st_alert('<div class="text_success"><s class="icon_success_big"></s>'+res.message+'</div>',function(){
                            setTimeout(function(){
                                window.location.href = '<?php echo Mava_Url::buildLink('admin/option/setting',array('groupID' => $optionGroup['group_id'])); ?>';
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


    });
</script>