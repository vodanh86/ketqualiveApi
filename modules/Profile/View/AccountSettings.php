<div id="profile_page" class="container">
    <?php echo Mava_View::getView('Profile_View_Includes_Menu'); ?>
    <div id="profile_right" class="col-md-9">
        <div class="rm-box-border">
            <h4 class="rm-head-page-box"><?php echo __('account_information'); ?></h4>
        <div class="settings_list">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo __('uid'); ?></label>
                        <div class="form-control-static"><?php echo htmlspecialchars($user->get('user_id')); ?></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo __('email'); ?></label>
                        <div class="form-control-static"><?php echo htmlspecialchars($user->get('email')); ?></div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?php echo __('fullname'); ?></label>
                        <div class="form-control-static"><span class="user_fullname"><?php echo htmlspecialchars($user->get('custom_title')); ?></span> <a href="javascript:void(0);" class="edit_link" rel="edit_row_1">[ <?php echo __('edit'); ?> ]</a></div>
                    </div>
                    <div class="edit_row hidden edit_row_1">
                        <div class="form-group">
                            <input type="text" class="form-control" id="edit_custom_title" value="<?php echo htmlspecialchars($user->get('custom_title')); ?>" />
                        </div>
                        <div class="form-group">
                            <a href="javascript:void(0);" class="btn btn-primary btn-sm" id="save_custom_title"><?php echo __('save'); ?></a>
                            <a href="javascript:void(0);" class="btn btn-default btn-sm button_cancel_edit"><?php echo __('cancel'); ?></a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?php echo __('avatar'); ?></label>
                        <div class="form-control-static">
                            <p><img src="<?php echo get_avatar_url('middle'); ?>" width="100" class="avatar_preview" /></p>
                            <div class="upload_avatar_form margin-top-bottom-10">
                                <a href="javascript:void(0);" class="btn btn-success btn-xs" id="button_upload_avatar"><i class="fa fa-upload"></i> <?php echo __('upload_new_avatar'); ?></a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-6">

                    <div class="form-group">
                        <label class="control-label"><?php echo __('gender'); ?></label>
                        <div class="form-control-static"><span class="label_row_2"><?php echo $user->get('gender')!=""?__($user->get('gender')):__('not_provide'); ?></span> <a href="javascript:void(0);" class="edit_link" rel="edit_row_2">[ <?php echo __('edit'); ?> ]</a></div>
                    </div>
                    <div class="edit_row hidden edit_row_2">
                        <div class="form-group">
                            <select class="form-control" id="edit_gender">
                                <option value="">- <?php echo __('choose'); ?> -</option>
                                <option value="male" <?php echo ($user->get('gender')=='male')?' selected':''; ?>><?php echo __('male'); ?></option>
                                <option value="female" <?php echo ($user->get('gender')=='female')?' selected':''; ?>><?php echo __('female'); ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <a href="javascript:void(0);" class="btn btn-primary" id="save_gender"><?php echo __('save'); ?></a>
                            <a href="javascript:void(0);" class="btn btn-default button_cancel_edit"><?php echo __('cancel'); ?></a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?php echo __('city'); ?></label>
                        <div class="form-control-static"><span class="label_row_3"><?php echo get_city_title($user->get('city_id')); ?></span> <a href="javascript:void(0);" class="edit_link" rel="edit_row_3">[ <?php echo __('edit'); ?> ]</a></div>
                    </div>
                    <div class="edit_row hidden edit_row_3">
                        <div class="form-group">
                        <select class="form-control" id="edit_city">
                            <option value="">- <?php echo __('choose'); ?> -</option>
                            <?php
                            if(isset($cities) && count($cities) > 0){
                                foreach($cities as $item){
                                    echo '<option value="'. $item['city_id'] .'" '. ($item['city_id']==$user->get('city_id')?' selected':'') .'>'. htmlspecialchars($item['title']) .'</option>';
                                }
                            }
                            ?>
                        </select>
                        </div>
                        <div class="form-group">
                            <a href="javascript:void(0);" class="btn btn-primary" id="save_city"><?php echo __('save'); ?></a>
                            <a href="javascript:void(0);" class="btn btn-default button_cancel_edit"><?php echo __('cancel'); ?></a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?php echo __('phone_number'); ?></label>
                        <div class="form-control-static"><span class="label_row_4"><?php echo htmlspecialchars($user->get('phone')); ?></span> <a href="javascript:void(0);" class="edit_link" rel="edit_row_4">[ <?php echo __('edit'); ?> ]</a></div>
                    </div>
                    <div class="edit_row hidden edit_row_4">
                        <div class="form-group">
                            <input type="text" class="form-control" id="edit_phone" value="<?php echo htmlspecialchars($user->get('phone')); ?>" />
                        </div>
                        <div class="form-group">
                            <a href="javascript:void(0);" class="btn btn-primary" id="save_phone"><?php echo __('save'); ?></a>
                            <a href="javascript:void(0);" class="btn btn-default button_cancel_edit"><?php echo __('cancel'); ?></a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?php echo __('birthday'); ?></label>
                        <div class="form-control-static"><span class="label_row_5"><?php echo print_birthday($user->get('birthday')); ?></span> <a href="javascript:void(0);" class="edit_link" rel="edit_row_5">[ <?php echo __('edit'); ?> ]</a></div>
                    </div>
                    <?php
                    if($user->get('birthday') > 0){
                        $birthday = explode('/', date('d/m/Y', $user->get('birthday')));
                    }else{
                        $birthday = array();
                    }
                    ?>
                    <div class="edit_row hidden edit_row_5 edit_row_date">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <select class="form-control" id="edit_birthday_day">
                                        <option value="">- <?php echo __('day'); ?> -</option>
                                        <?php
                                        for($i=1;$i<=31;$i++){
                                            echo '<option value="'. $i .'"'. (count($birthday)>0&&(int)$birthday[0]==$i?' selected':'') .'>'. $i .'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" id="edit_birthday_month">
                                        <option value="">- <?php echo __('month'); ?> -</option>
                                        <?php
                                        for($i=1;$i<=12;$i++){
                                            echo '<option value="'. $i .'"'. (count($birthday)>0&&(int)$birthday[1]==$i?' selected':'') .'>'. $i .'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" id="edit_birthday_year">
                                        <option value="">- <?php echo __('year'); ?> -</option>
                                        <?php
                                        $start = date('Y')-13;
                                        $end = date('Y')-100;
                                        for($i=$start;$i>$end;$i--){
                                            echo '<option value="'. $i .'"'. (count($birthday)>0&&(int)$birthday[2]==$i?' selected':'') .'>'. $i .'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="javascript:void(0);" class="btn btn-primary" id="save_birthday"><?php echo __('save'); ?></a>
                            <a href="javascript:void(0);" class="btn btn-default button_cancel_edit"><?php echo __('cancel'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<iframe id="hodela_form_upload_image" name="hodela_form_upload_image" style="display:none"></iframe>
<form id="hodela_upload_image" action="<?php echo Mava_Url::getPageLink('upload_image', array('type' => 'avatar')); ?>" target="hodela_form_upload_image" method="post" enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
    <input name="hodela_image_input" accept="image/*" type="file" onchange="$('#hodela_upload_image').submit();$('.upload_image_loading').show();this.value='';">
</form>

<script type="text/javascript">
    $(document).ready(function(){
        $('#button_upload_avatar').click(function(){
            $('#hodela_upload_image input').click();
        });
        $('.edit_link').click(function(){
            var edit_row_class = $(this).attr('rel');
            if(edit_row_class != undefined && edit_row_class != ''){
                $('.'+ edit_row_class).removeClass('hidden');
                $('.'+ edit_row_class).find('input').first().focus();
            }
        });

        $('.button_cancel_edit').click(function(){
            $(this).parents('.edit_row').addClass('hidden');
        });

        $('#save_custom_title').click(function(){
            if($('#edit_custom_title').val() == ''){
                $('#edit_custom_title').focus();
            }else{
                MV.post(DOMAIN + '/profile/save',{
                    type: 'custom_title',
                    value: $('#edit_custom_title').val()
                },function(res){
                    MP.notice.show(__('account_info_saved'), 'success', 3);
                    $('.user_fullname').html(res.value);
                    $('.pg_name').html(res.value);
                    MV.show_notice(res.message, 3);
                    $('.edit_row_1').addClass('hidden');
                });
            }
        });

        $('#save_gender').click(function(){
            if($('#edit_gender').val() == ''){
                $('#edit_gender').focus();
            }else{
                MV.post(DOMAIN + '/profile/save',{
                    type: 'gender',
                    value: $('#edit_gender').val()
                },function(res){
                    MP.notice.show(__('account_info_saved'), 'success', 3);
                    $('.user_lead').html(res.user_lead);
                    $('.label_row_2').html(res.value);
                    MV.show_notice(res.message, 3);
                    $('.edit_row_2').addClass('hidden');
                });
            }
        });

        $('#save_city').click(function(){
            if($('#edit_city').val() == ''){
                $('#edit_city').focus();
            }else{
                MV.post(DOMAIN + '/profile/save',{
                    type: 'city',
                    value: $('#edit_city').val()
                },function(res){
                    MP.notice.show(__('account_info_saved'), 'success', 3);
                    $('.user_lead').html(res.user_lead);
                    $('.label_row_3').html(res.value);
                    MV.show_notice(res.message, 3);
                    $('.edit_row_3').addClass('hidden');
                });
            }
        });

        $('#save_phone').click(function(){
            if($('#edit_phone').val() == ''){
                $('#edit_phone').focus();
            }else{
                MV.post(DOMAIN + '/profile/save',{
                    type: 'phone',
                    value: $('#edit_phone').val()
                },function(res){
                    MP.notice.show(__('account_info_saved'), 'success', 3);
                    $('.label_row_4').html(res.value);
                    MV.show_notice(res.message, 3);
                    $('.edit_row_4').addClass('hidden');
                });
            }
        });

        $('#save_birthday').click(function(){
            if($('#edit_birthday_day').val() == ''){
                $('#edit_birthday_day').focus();
            }else if($('#edit_birthday_month').val() == ''){
                $('#edit_birthday_month').focus();
            }else if($('#edit_birthday_year').val() == ''){
                $('#edit_birthday_year').focus();
            }else{
                MV.post(DOMAIN + '/profile/save',{
                    type: 'birthday',
                    value: [$('#edit_birthday_day').val(), $('#edit_birthday_month').val(), $('#edit_birthday_year').val()]
                },function(res){
                    MP.notice.show(__('account_info_saved'), 'success', 3);
                    $('.user_lead').html(res.user_lead);
                    $('.label_row_5').html(res.value);
                    MV.show_notice(res.message, 3);
                    $('.edit_row_5').addClass('hidden');
                });
            }
        });
    });
</script>