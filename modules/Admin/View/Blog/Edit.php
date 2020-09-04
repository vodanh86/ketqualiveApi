<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_edit_post" id="form_edit_post" action="<?php echo Mava_Url::buildLink('admin/blog/edit', array('postId' => $postId)); ?>" method="post" enctype="multipart/form-data">
        <h2 class="mava_form_title"><?php echo __('edit_post'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('title'); ?> <b>(*)</b></dt>
                <dd><input type="text" class="input_text input_medium" name="postTitle" id="postTitle" value="<?php echo isset($postTitle)?htmlspecialchars($postTitle):''; ?>" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('category'); ?> <b>(*)</b></dt>
                <dd><select name="categoryId" id="categoryId">
                        <option value="0">- <?php echo __('choose'); ?> -</option>
                        <?php
                        if(isset($categories) && count($categories) > 0) {
                            foreach ($categories as $item) {
                                echo '<option value="' . $item['category_id'] . '"' . ((isset($categoryId) && $item['category_id'] == $categoryId) ? " selected" : "") . '>' . htmlspecialchars($item['title']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('lead'); ?> <b>(*)</b></dt>
                <dd><textarea class="input_area" name="postLead" id="postLead"><?php echo (isset($postLead) && $postLead!="")?$postLead:""; ?></textarea></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('content'); ?> <b>(*)</b></dt>
                <dd><textarea class="input_area input_richtext" name="postContent" id="postContent"><?php echo (isset($postContent) && $postContent!="")?$postContent:""; ?></textarea></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('image'); ?>
                <div class="gray tsmall"><?php echo __('post_cover_rule'); ?></div></dt>
                <dd>
                    <input type="file" class="input_text" name="postCover" id="postCover" accept="image/*" />
                    <?php
                        echo (isset($postCover) && $postCover!="")?"<div class='image_preview_wrap'><img class='image_preview' src='". image_url($postCover) ."' width='160' height='110'/></div>":"";
                    ?>
                </dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_edit"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/blog/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
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
            $('#form_edit_post').submit();
        });

        $('#form_edit_post').submit(function(){
            if($('#postTitle').val()==''){
                $('#postTitle').focus();
                return false;
            }else if($('#categoryId').val()==0){
                $('#categoryId').focus();
                return false;
            }else if($('#postLead').val()==''){
                $('#postLead').focus();
                return false;
            }else if(tinyMCE.get('postContent').getContent() == ''){
                tinyMCE.get('postContent').getBody().focus();
                return false;
            }else{
                return true;
            }
            return false;
        });


    });
</script>