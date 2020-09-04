<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_edit_question" id="form_edit_question" action="<?php echo Mava_Url::buildLink('admin/qa/edit', array('id' => $question['id'])); ?>" method="post">
        <h2 class="mava_form_title"><?php echo __('edit_question'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('fullname'); ?></dt>
                <dd><input type="text" class="input_text input_medium questionFullname" name="questionFullname" id="questionFullname" value="<?php echo htmlspecialchars($question['name']); ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('email'); ?></dt>
                <dd><input type="text" class="input_text input_medium questionEmail" name="questionEmail" id="questionEmail" value="<?php echo htmlspecialchars($question['email']); ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('phone'); ?></dt>
                <dd><input type="text" class="input_text input_medium questionPhone" name="questionPhone" id="questionPhone" value="<?php echo htmlspecialchars($question['phone']); ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('question'); ?></dt>
                <dd><textarea class="input_area input_medium questionTitle" name="questionTitle" id="questionTitle"><?php echo htmlspecialchars($question['question']); ?></textarea></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('answer'); ?></dt>
                <dd><textarea class="input_area input_medium questionAnswer" name="questionAnswer" id="questionAnswer"><?php echo htmlspecialchars($question['answer']); ?></textarea></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('sort_order'); ?></dt>
                <dd class="mava_spinbox">
                    <input type="text" class="input_text input_short mava_spinbox_value" data-step="1" data-min="0" value="<?php echo (int)$question['sort_order']; ?>" name="questionSortOrder" id="questionSortOrder">
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_plus">+</a>
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_minus">-</a>
                </dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_edit"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/qa/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
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

        $('#button_submit_edit').click(function(){
            $('#form_edit_question').submit();
        });
        $('#form_edit_question').submit(function(){
            if($('.questionTitle').val()==''){
                $('.questionTitle').focus();
                return false;
            }else{
                return true;
            }
        });


    });
</script>