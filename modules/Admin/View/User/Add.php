<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_user" id="form_add_user" action="<?php echo Mava_Url::buildLink('admin/users/add'); ?>" method="post" enctype="multipart/form-data">
        <h2 class="mava_form_title"><?php echo __('add_user'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('user_group'); ?> <b>(*)</b></dt>
                <dd>
                    <select name="userGroup" id="userGroup">
                        <option value="0">- <?php echo __('choose'); ?> -</option>
                        <?php
                        if(isset($userGroup) && count($userGroup) > 0){
                            foreach($userGroup as $item){
                                echo '<option value="'. $item['group_id'] .'"'. (isset($user['user_group_id']) && $user['user_group_id']==$item['group_id']?" selected":"") .'>'. htmlspecialchars($item['group_title']) .'</option>';
                            }
                        }
                        ?>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('fullname'); ?> <b>(*)</b></dt>
                <dd><input type="text" class="input_text input_medium" name="userTitle" id="userTitle" value="<?php echo isset($user['custom_title'])?htmlspecialchars($user['custom_title']):""; ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('email'); ?> <b>(*)</b></dt>
                <dd><input type="text" class="input_text input_medium" name="userEmail" id="userEmail" value="<?php echo isset($user['email'])?htmlspecialchars($user['email']):""; ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('password'); ?> <b>(*)</b></dt>
                <dd><input type="password" class="input_text input_medium" name="userPassword" id="userPassword" placeholder="<?php echo __('do_not_enter_if_you_dont_want_change'); ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('retype_password'); ?> <b>(*)</b></dt>
                <dd><input type="password" class="input_text input_medium" name="userRePassword" id="userRePassword" placeholder="<?php echo __('do_not_enter_if_you_dont_want_change'); ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('phone'); ?></dt>
                <dd><input type="text" class="input_text input_medium" name="userPhone" id="userPhone" value="<?php echo isset($user['phone'])?htmlspecialchars($user['phone']):""; ?>" /></dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('gender'); ?></dt>
                <dd>
                    <select name="userGender" id="userGender">
                        <option value="">- <?php echo __('choose'); ?> -</option>
                        <option value="male" <?php echo (isset($user['gender']) && $user['gender']=='male')?" selected":""; ?>><?php echo __('male'); ?></option>
                        <option value="female" <?php echo (isset($user['gender']) && $user['gender']=='female')?" selected":""; ?>><?php echo __('female'); ?></option>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('city'); ?></dt>
                <dd>
                    <select name="userCity" id="userCity">
                        <option value="0">- <?php echo __('choose'); ?> -</option>
                        <?php
                        if(isset($cities) && count($cities) > 0){
                            foreach($cities as $item){
                                echo '<option value="'. $item['city_id'] .'"'. ((isset($user['city_id']) && $user['city_id']==$item['city_id'])?" selected":"") .'>'. htmlspecialchars($item['title']) .'</option>';
                            }
                        }
                        ?>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('birthday'); ?><div class="tmedium gray">Ví dụ: 25/06/1992</div></dt>
                <dd><input type="text" class="input_text input_short" name="userBirthday" id="userBirthday" value="<?php echo (isset($user['birthday']) && $user['birthday']>0)?htmlspecialchars($user['birthday']):""; ?>" placeholder="d/m/Y" /></dd>
            </dl>
            <dl class="row">
                <dt>&nbsp;</dt>
                <dd>
                    <input type="checkbox" value="1" name="userActivated" id="userActivated" <?php echo (isset($user['is_active']) && (int)$user['is_active']==1)?'checked':''; ?>/>
                    <label for="userActivated"><?php echo __('active'); ?></label>
                </dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/users/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
                </dd>
            </dl>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        <?php
            if(isset($error_message) && $error_message != ''){
        ?>
        MV.show_notice('<?php echo $error_message; ?>',3);
        <?php
            }
        ?>

        $('#button_submit_add').click(function(){
            $('#form_add_user').submit();
        });

        $('#form_add_user').submit(function(){
            if(parseInt($('#userGroup').val()) <= 0){
                $('#userGroup').focus();
                return false;
            }else if($('#userTitle').val()==''){
                $('#userTitle').focus();
                return false;
            }else if($('#userEmail').val() == ''){
                $('#userEmail').focus();
                return false;
            }else if($('#userPassword').val() == ''){
                $('#userPassword').focus();
                return false;
            }else if($('#userPassword').val() != $('#userRePassword').val()){
                $('#userRePassword').focus();
                return false;
            }else{
                return true;
            }
        });


    });
</script>