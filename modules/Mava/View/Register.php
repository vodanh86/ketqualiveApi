<div class="lr-signup-box">
<div class="container">
    <div class="row">
    <div class="col-md-6 col-md-offset-3">
        <h1 class="text-center lr-head"><?php echo __('become_partner'); ?></h1>
        <?php
        if(isset($error) && isset($errorMessage) && $error==1){
            echo '<div class="alert alert-danger">'. $errorMessage .'</div>';
        }
        ?>
        <div class="signup_use_email">
            <form name="frmSignup" id="frmSignup" action="<?php echo Mava_Url::getPageLink('signup'); ?>" method="post" autocomplete="off">
                <div class="form-group">
                    <label class="control-label" for="signup_fullname"><?php echo __('fullname'); ?></label>
                    <input type="text" value="<?php echo htmlspecialchars($fullname); ?>" tabindex="1" class="form-control" id="signup_fullname" name="signup_fullname" />
                </div>
                <div class="form-group">
                    <label class="control-label" for="signup_phone"><?php echo __('phone_number'); ?></label>
                    <input type="text" value="<?php echo htmlspecialchars($phone); ?>" tabindex="2" class="form-control" id="signup_phone" name="signup_phone" />
                </div>
                <div class="form-group">
                    <label class="control-label" for="signup_email"><?php echo __('email'); ?></label>
                    <input type="text" value="<?php echo htmlspecialchars($email); ?>" tabindex="3" class="form-control" id="signup_email" name="signup_email" />
                </div>
                <div class="form-group">
                    <label class="control-label" for="signup_password"><?php echo __('password'); ?></label>
                    <input type="password" tabindex="4" class="form-control" id="signup_password" name="signup_password" />
                </div>
                <div class="form-group">
                    <label class="control-label" for="signup_retype_password"><?php echo __('retype_password'); ?></label>
                    <input type="password" tabindex="5" class="form-control" id="signup_retype_password" name="signup_retype_password" />
                </div>
                <div class="form-group">
                    <p class="text-muted"><?php echo __('register_tos_notice',array('sitename' => Mava_Application::getOptions()->siteName,'tos_link' => Mava_Url::getPageLink('tos'))); ?></p>
                </div>
                <div class="form-group">
                    <input type="submit" tabindex="6" value="<?php echo __('signup_text_button'); ?>" class="btn btn-primary" id="signup_submit" />
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
</div>

<script type="text/javascript">
    var passWordMinLength = <?php echo (int)Mava_Application::get('config/passwordMinLength'); ?>,
        passWordMaxLength = <?php echo (int)Mava_Application::get('config/passwordMaxLength'); ?>;
    $(document).ready(function(){
        if($('#signup_fullname').val() == ''){
            $('#signup_fullname').focus();
        }

        $('#frmSignup input').keydown(function(){
            $('.input-error-message').remove();
        });

        $('#frmSignup').submit(function(){
            $('.input-error-message').remove();
            if($('#signup_fullname').val()==''){
                $('<div class="input-error-message text-danger">* '+ __('please_enter_fullname') +'</div>').insertAfter($('#signup_fullname'));
                $('#signup_fullname').val('').focus();
                return false;
            }else if($('#signup_phone').val()=='' || $('#signup_phone').val().length < 9){
                $('<div class="input-error-message text-danger">* '+ __('enter_your_phone_number') +'</div>').insertAfter($('#signup_phone'));
                $('#signup_phone').focus();
                return false;
            }else if($('#signup_email').val()=='' || !MV.string.isEmail($('#signup_email').val().trim())){
                $('<div class="input-error-message text-danger">* '+ __('enter_your_email') +'</div>').insertAfter($('#signup_email'));
                $('#signup_email').focus();
                return false;
            }else if($('#signup_password').val()==''){
                $('<div class="input-error-message text-danger">* '+ __('please_enter_password') +'</div>').insertAfter($('#signup_password'));
                $('#signup_password').val('').focus();
                return false;
            }else if($('#signup_password').val().length < passWordMinLength || $('#signup_password').val().length > passWordMaxLength){
                $('<div class="input-error-message text-danger">* '+ __('password_length_from_x_to_y', {min_length: passWordMinLength, max_length: passWordMaxLength}) +'</div>').insertAfter($('#signup_password'));
                $('#signup_password').val('').focus();
                return false;
            }else if($('#signup_password').val() != $('#signup_retype_password').val()){
                $('<div class="input-error-message text-danger">* '+ __('password_confirm_not_match') +'</div>').insertAfter($('#signup_retype_password'));
                $('#signup_retype_password').val('').focus();
                return false;
            }else{
                return true;
            }
        });
    });
</script>