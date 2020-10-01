<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_user" id="form_add_user" action="<?php echo Mava_Url::buildLink('admin/videos/add'); ?>" method="post" enctype="multipart/form-data">
        <h2 class="mava_form_title"><?php echo __('add_video'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('title'); ?> <b>(*)</b></dt>
                <dd><input type="text" class="input_text input_medium" name="title" id="userTitle" value="<?php echo isset($user['custom_title'])?htmlspecialchars($user['custom_title']):""; ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('youtube_id'); ?></dt>
                <dd><input type="text" class="input_text input_medium" name="youtubeId" id="userPhone" value="<?php echo isset($user['phone'])?htmlspecialchars($user['phone']):""; ?>" /></dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/videos/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
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
            if($('#userTitle').val()==''){
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