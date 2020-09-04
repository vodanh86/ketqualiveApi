<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_edit_option_group_sort" id="form_edit_option_group_sort" action="<?php echo Mava_Url::buildLink('admin/option/do_edit_group_sort'); ?>" method="post">
        <h2 class="mava_form_title"><?php echo __('edit_option_group_sort'); ?></h2>
        <div class="mava_form_rows">
            <?php
                if(sizeof($option_group) > 0){
                    foreach($option_group as $item){
            ?>

                        <dl class="row">
                            <dt><?php echo __('_option_group_title_'. $item['group_id']); ?>
                            <p class="gray tmall"><?php echo htmlspecialchars($item['group_id']); ?></p></dt>
                            <dd class="mava_spinbox">
                                <input type="text" class="input_text input_short mava_spinbox_value" data-step="1" data-min="0" value="<?php echo intval($item['display_order']); ?>" name="groupDisplayOrder[<?php echo htmlspecialchars($item['group_id']); ?>]" id="groupDisplayOrder[<?php echo htmlspecialchars($item['group_id']); ?>]" />
                                <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_plus">+</a>
                                <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_minus">-</a>
                            </dd>
                        </dl>
            <?php
                    }
                }
            ?>

            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_edit"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/option/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
                </dd>
            </dl>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#button_submit_edit').click(function(){
            MV.post(DOMAIN +'/admin/option/do_edit_group_sort',$('#form_edit_option_group_sort').serialize(),function(res){
                if(res.status==1){
                    MV.dialog.st_alert('<div class="text_success"><s class="icon_success_big"></s>'+res.message+'</div>',function(){
                        setTimeout(function(){
                            window.location.reload();
                        },300);
                    });
                }
            });
            return false;
        });
    });
</script>