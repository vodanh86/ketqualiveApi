<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_blog_category" id="form_add_blog_category" action="<?php echo Mava_Url::buildLink('admin/blog/add_category'); ?>" method="post">
        <h2 class="mava_form_title"><?php echo __('add_category'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('category_title'); ?> <b>(*)</b></dt>
                <dd><input type="text" class="input_text input_medium" name="categoryTitle" id="categoryTitle" value="<?php echo (isset($categoryTitle)?htmlspecialchars($categoryTitle):""); ?>"/></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('category_description'); ?></dt>
                <dd><textarea class="input_area" name="categoryDescription" id="categoryDescription"><?php echo isset($categoryDescription)?$categoryDescription:''; ?></textarea></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('sort_order'); ?></dt>
                <dd class="mava_spinbox">
                    <input type="text" class="input_text input_short mava_spinbox_value" data-step="1" data-min="0" value="<?php echo isset($categorySortOrder)?(int)$categorySortOrder:0; ?>" name="categorySortOrder" id="categorySortOrder">
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_plus">+</a>
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_minus">-</a>
                </dd>
            </dl>

            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/blog/category'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
                </dd>
            </dl>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#categoryTitle').focus();

        $('#button_submit_add').click(function(){
            if($('#categoryTitle').val()==''){
                $('#categoryTitle').focus();
                return false;
            }else{
                MV.post(DOMAIN +'/admin/blog/add_category',$('#form_add_blog_category').serialize(),function(res){
                    if(res.status==1){
                        MV.dialog.st_alert('<div class="text_success"><s class="icon_success_big"></s>'+res.message+'</div>',function(){
                            setTimeout(function(){
                                window.location.href = '<?php echo Mava_Url::buildLink('admin/blog/category'); ?>';
                            },300);
                        });
                    }
                });
            }
            return false;
        });


    });
</script>