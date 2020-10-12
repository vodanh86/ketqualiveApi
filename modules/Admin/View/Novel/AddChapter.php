<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_novel" id="form_add_chapter" action="<?php echo Mava_Url::buildLink('admin/novel/addChapter', array("novelId" => $novelId)); ?>" method="post" enctype="multipart/form-data">
        <h2 class="mava_form_title"><?php echo __('add_novel'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('name'); ?> <b>(*)</b></dt>
                <dd><input type="text" class="input_text input_medium" name="name" id="name" value="<?php echo isset($novel['name'])?htmlspecialchars($novel['name']):""; ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('link'); ?></dt>
                <dd><input type="text" class="input_text input_medium" name="link" id="link" value="<?php echo isset($novel['link'])?htmlspecialchars($novel['link']):""; ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('order'); ?></dt>
                <dd><input type="text" class="input_text input_medium" name="order" id="order" value="<?php echo isset($novel['order'])?htmlspecialchars($novel['order']):""; ?>" /></dd>
            </dl>
               <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/novel/view', array("novelId" => $novelId)); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
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
            $('#form_add_chapter').submit();
        });

        $('#form_add_chapter').submit(function(){
            if($('#name').val()==''){
                $('#name').focus();
                return false;
            }else if($('#link').val() == ''){
                $('#link').focus();
                return false;
            }else if($('#order').val() == ''){
                $('#order').focus();
                return false;
            }else{
                return true;
            }
        });


    });
</script>