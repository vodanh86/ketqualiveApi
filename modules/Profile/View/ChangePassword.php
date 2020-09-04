<div id="profile_page" class="container">
    <?php echo Mava_View::getView('Profile_View_Includes_Menu'); ?>
    <div id="profile_right" class="col-md-9">
        <div class="rm-box-border">
            <h4 class="rm-head-page-box"><?php echo ($has_password?__('change_password'):__('set_account_password')); ?></h4>
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="change_password">
                        <?php if($has_password){ ?>
                        <div class="form-group">
                            <label class="control-label" for="old_password"><?php echo __('old_password'); ?></label>
                            <input type="password" class="form-control" name="old_password" id="old_password" />
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <label class="control-label" for="new_password"><?php echo __('new_password'); ?></label>
                            <input type="password" class="form-control" name="new_password" id="new_password" />
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="confirm_new_password"><?php echo __('confirm_new_password'); ?></label>
                            <input type="password" class="form-control" name="confirm_new_password" id="confirm_new_password" />
                        </div>
                        <div class="form-group">
                            <a href="javascript:void(0);" class="btn btn-primary" id="save_edit_password"><?php echo __('save'); ?></a>
                            <a href="<?php echo Mava_Url::getPageLink('profile'); ?>" class="btn btn-default"><?php echo __('cancel'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#old_password').focus();
        $('#save_edit_password').click(function(){
            if($('#old_password').length && $('#old_password').val() == ''){
                $('#old_password').focus();
                return false;
            }else if($('#new_password').val() == ''){
                $('#new_password').focus();
                return false;
            }else if($('#confirm_new_password').val() != $('#new_password').val()){
                $('#confirm_new_password').focus();
                return false;
            }else{
                MV.post(DOMAIN +'/profile/changepassword', {
                    old_password: ($('#old_password').length?$('#old_password').val():''),
                    new_password: $('#new_password').val(),
                    confirm_new_password: $('#confirm_new_password').val(),
                }, function(res){
                    MV.show_notice(res.message);
                    if($('#old_password').length){
                        $('#old_password').val('');
                    }
                    $('#new_password').val('');
                    $('#confirm_new_password').val('');
                });
            }
        });
    });
</script>
