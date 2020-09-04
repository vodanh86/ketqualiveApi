<div class="container forgotpassword_page">
    <div class="col-md-6 col-md-offset-3">
    <h2 class="text-center"><?php echo __('forgotpassword_page_title'); ?></h2>
    <form name="frmForgotPassword" id="frmForgotPassword" action="<?php echo Mava_Url::getPageLink('forgotpassword'); ?>" method="post">
        <div class="text-danger margin-top-bottom-20" id="forgotpassword_error"><?php echo (isset($error_msg) && $error_msg != "")?$error_msg:""; ?></div>
        <dl>
            <dt><?php echo __('forgotpassword_email_label'); ?></dt>
            <dd><input type="email" name="email" id="email" class="form-control" /></dd>
        </dl>
        <dl>
            <dd><input type="submit" class="btn btn-primary" value="<?php echo __('send_request'); ?>"/></dd>
        </dl>
    </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#email').focus();
        $('#frmForgotPassword').submit(function(){
            if($('#email').val() == ''){
                $('#forgotpassword_error').text('* <?php echo __('please_enter_email'); ?>');
                $('#email').focus();
                return false;
            }else if(!MV.string.isEmail($('#email').val())){
                $('#forgotpassword_error').text('* <?php echo __('email_invalid'); ?>');
                $('#email').focus();
                return false;
            }else{
                return true;
            }
            return false;
        });
    });
</script>