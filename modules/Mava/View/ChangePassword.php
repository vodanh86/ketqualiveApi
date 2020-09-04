<div class="gw clearfix changepassword_page">
    <h2><?php echo __('change_password'); ?></h2>
    <form name="frmChangePassword" id="frmChangePassword" action="<?php echo Mava_Url::getPageLink('forgotpassword_confirm'); ?>" method="post">
        <input type="hidden" name="uid" value="<?php echo $user['user_id']; ?>" />
        <input type="hidden" name="forgot_code" value="<?php echo $forgot_code; ?>" />
        <?php
        if(isset($errorMessage) && $errorMessage != ""){
            echo '<div class="text-red padding-top-bottom-5">'. $errorMessage .'</div>';
        }
        ?>
        <dl>
            <dt><?php echo __('new_password'); ?></dt>
            <dd><input type="password" name="newPassword" id="newPassword" class="input-text" /></dd>
        </dl>
        <dl>
            <dt><?php echo __('confirm_new_password'); ?></dt>
            <dd><input type="password" name="confirmNewPassword" id="confirmNewPassword" class="input-text" /></dd>
        </dl>
        <dl>
            <dd><input type="submit" class="button button-info" value="<?php echo __('save'); ?>"/></dd>
        </dl>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#frmChangePassword').submit(function(){
            if($('#newPassword').val() == ''){
                $('#newPassword').focus();
                return false;
            }else if($('#newPassword').val() != $('#confirmNewPassword').val()){
                $('#confirmNewPassword').focus();
                return false;
            }else{
                return true;
            }
            return false;
        });
    });
</script>