<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_novel" id="form_add_novel" action="<?php echo Mava_Url::buildLink('admin/novel/add'); ?>" method="post" enctype="multipart/form-data">
        <h2 class="mava_form_title"><?php echo __('add_novel'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('name'); ?> <b>(*)</b></dt>
                <dd><input type="text" class="input_text input_medium" name="name" id="name" value="<?php echo isset($novel['name'])?htmlspecialchars($novel['name']):""; ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('description'); ?></dt>
                <dd><input type="text" class="input_text input_medium" name="description" id="description" value="<?php echo isset($novel['description'])?htmlspecialchars($novel['description']):""; ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('image'); ?></dt>
                <dd><input type="text" class="input_text input_medium" name="image" id="image" value="<?php echo isset($novel['image'])?htmlspecialchars($novel['image']):""; ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('author'); ?></dt>
                <dd><input type="text" class="input_text input_medium" name="author" id="author" value="<?php echo isset($novel['author'])?htmlspecialchars($novel['author']):""; ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('category'); ?></dt>
                <dd><input type="text" class="input_text input_medium" name="category_id" id="category_id" value="<?php echo isset($novel['category_id'])?htmlspecialchars($novel['category_id']):""; ?>" /></dd>
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
            $('#form_add_novel').submit();
        });

        $('#form_add_novel').submit(function(){
            if($('#name').val()==''){
                $('#name').focus();
                return false;
            }else if($('#description').val() == ''){
                $('#description').focus();
                return false;
            }else if($('#image').val() == ''){
                $('#image').focus();
                return false;
            }else{
                return true;
            }
        });


    });
</script>