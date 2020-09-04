<div class="container login_wrap">
    <div class="col-md-6 col-md-offset-3">
        <?php echo (isset($login_message) && $login_message!="")?'<div class="alert alert-warning">'. $login_message .'</div>':""; ?>
         <h1 class="text-center"><?php echo __('login'); ?></h1>
        <?php
        if(isset($error) && isset($errorMessage) && $error==1){
            echo '<div class="text-red padding-top-bottom-5">'. $errorMessage .'</div>';
        }
        ?>
        <div class="login_use_email">
            <form name="frmLogin" id="frmLogin" action="<?php echo Mava_Url::getPageLink('login'); ?>" method="post" autocomplete="off">
                <dl>
                    <dt><?php echo __('login_text_label'); ?>:</dt>
                    <dd><input type="text" tabindex="1" class="form-control" id="login_text" name="login_text" value="<?php echo (isset($login_text) && $login_text!="")?htmlspecialchars($login_text):""; ?>"/></dd>
                </dl>
                <dl>
                    <dt class="clearfix"><div class="pull-left"><?php echo __('login_password_label'); ?>:</div><div class="pull-right"><a href="<?php echo Mava_Url::getPageLink('forgotpassword'); ?>" class="forgot_password_link"><?php echo __('forgot_password_text_link'); ?></a></div></dt>
                    <dd><input type="password" tabindex="2" class="form-control" id="login_password" name="login_password" /></dd>
                </dl>
                <dl>
                    <dd><input type="checkbox" value="1" id="remember_login" checked="checked" /><label for="remember_login"><?php echo __('login_remember_label'); ?></label></dd>
                </dl>
                <dl>
                    <dt>&nbsp;</dt>
                    <dd class="clearfix">
                        <input type="submit" tabindex="3" value="<?php echo __('login_text_button'); ?>" class="btn btn-primary" id="login_submit" />
                    </dd>
                </dl>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        if($('#login_text').val() == ''){
            $('#login_text').focus();
        }else{
            $('#login_password').focus();
        }
        $('#frmLogin').submit(function(){
            if($('#login_text').val().trim()==''){
                $('#login_text').val('').focus();
                return false;
            }else if($('#login_password').val().trim()==''){
                $('#login_password').focus();
                return false;
            }
            return true;
        });
    });
</script>