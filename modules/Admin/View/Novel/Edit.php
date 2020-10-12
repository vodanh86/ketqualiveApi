<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_edit_language" id="form_edit_language" action="<?php echo Mava_Url::buildLink('admin/novel/do_edit'); ?>" method="post">
        <input type="hidden" name="novelId" value="<?php echo $language['id']; ?>" />
        <h2 class="mava_form_title"><?php echo __('edit_novel'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('novel_name'); ?></dt>
                <dd><input type="text" class="input_text input_medium" name="novelName" value="<?php echo htmlspecialchars($language['name']); ?>" id="novelName" /></dd>
            </dl>
            <dl class="row">
                <dt>
                    <?php echo __('novel_description'); ?>
                </dt>
                <dd><input type="text" class="input_text input_medium" name="novelDescription" value="<?php echo htmlspecialchars($language['description']); ?>" id="novelDescription" /></dd>
            </dl>
            <dl class="row">
                <dt>
                    <?php echo __('novel_image'); ?>
                </dt>
                <dd><input type="text" class="input_text input_medium" name="novelImage" value="<?php echo htmlspecialchars($language['image']); ?>" id="novelImage" /></dd>
            </dl>
            <dl class="row">
                <dt>
                    <?php echo __('novel_author'); ?>
                </dt>
                <dd><input type="text" class="input_text input_medium" name="novelAuthor" value="<?php echo htmlspecialchars($language['author']); ?>" id="novelAuthor" /></dd>
            </dl>
            <dl class="row">
                <dt>
                    <?php echo __('novel_category'); ?>
                </dt>
                <dd><input type="text" class="input_text input_medium" name="novelCategory" value="<?php echo htmlspecialchars($language['category_id']); ?>" id="novelCategory" /></dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_edit"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/novel/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
                </dd>
            </dl>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#button_submit_edit').click(function(){
            if($('#novelName').val()==''){
                $('#novelName').focus();
                return false;
            }else if($('#novelAuthor').val()==''){
                $('#novelAuthor').focus();
                return false;
            }else{
                MV.post(DOMAIN +'/admin/novel/do_edit',$('#form_edit_language').serialize(),function(res){
                    if(res.status==1){
                        MV.dialog.st_alert('<div class="text_success"><s class="icon_success_big"></s>'+res.message+'</div>',function(){
                            setTimeout(function(){
                                window.location.href = '<?php echo Mava_Url::buildLink('admin/novel/index'); ?>';
                            },300);
                        });
                    }
                });
            }
            return false;
        });


    });
</script>