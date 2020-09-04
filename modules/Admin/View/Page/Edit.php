<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_phrase" id="form_add_page" action="<?php echo Mava_Url::buildLink('admin/page/edit', array('page_id'=>$postData['id'])); ?>" method="post">
        <h2 class="mava_form_title"><?php echo __('edit_static_page'); ?></h2>
        <div class="mava_form_rows">
            <?php
                if(isset($error_message) && $error_message != ''){
                    echo '
                        <dl class="row">
                            <dt></dt>
                            <dd style="color: #ff0000;">'.$error_message.'</dd>
                        </dl>
                    ';
                }
            ?>

            <dl class="row">
                <dt><?php echo __('slug'); ?><b>(*)</b></dt>
                <dd><input type="text" class="input_text" value="<?php echo (isset($postData['slug'])?$postData['slug']:'') ; ?>" name="slug" id="slug" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('page_group'); ?></dt>
                <dd>
                    <select name="group_id">
                        <option value="0"><?php echo __('not_in_groups');  ?></option>
                        <?php
                        if(is_array($list_page_group) && count($list_page_group) > 0){
                            foreach($list_page_group as $pg){
                                if(isset($postData['group_id']) && $pg['id'] == $postData['group_id']){
                                    $selected = 'selected';
                                }else{
                                    $selected = '';
                                }
                                echo '<option '.$selected.' value="'.$pg['id'].'">'.htmlspecialchars($pg['title']).'</option>';
                            }
                        }
                        ?>

                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('layout'); ?></dt>
                <dd>
                    <select name="layout">
                        <option value="one_col" <?php echo ((isset($postData['layout']) && $postData['layout']=='one_col')?'selected':'') ; ?> >one_col</option>
                        <option value="two_col" <?php echo ((isset($postData['layout']) && $postData['layout']=='two_col')?'selected':'') ; ?> >two_col</option>
                        <option value="fluid" <?php echo ((isset($postData['layout']) && $postData['layout']=='fluid')?'selected':'') ; ?> >fluid</option>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt>
                    <?php echo __('publish_time'); ?>
                    <div class="tmedium gray">Ex: 2016-03-22 20:40:29</div>
                </dt>
                <dd>
                    <input type="text" class="input_text" name="publish_time" id="publish_time" value="<?php echo (isset($postData['publish_time'])?$postData['publish_time']:'') ; ?>" />
                </dd>
            </dl>
            <dl class="row">
                <dt>
                    <?php echo __('unpublish_time'); ?>
                <div class="tmedium gray">Ex: 2016-08-22 20:40:29</div>
                </dt>
                <dd>
                    <input class="input_text" type="text" name="unpublish_time" id="unpublish_time"  value="<?php echo (isset($postData['unpublish_time'])?$postData['unpublish_time']:'') ; ?>"  />
                </dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('sort_order'); ?></dt>
                <dd>
                    <input type="text" class="input_text" name="sort_order" id="sort_order" value="<?php echo (isset($postData['sort_order'])?$postData['sort_order']:'') ; ?>" />
                </dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('show_title'); ?></dt>
                <dd>
                    <select name="show_title">
                        <option value="yes" <?php echo ((isset($postData['show_title']) && $postData['show_title']=='yes')?'selected':'') ; ?>>yes</option>
                        <option value="no" <?php echo ((isset($postData['show_title']) && $postData['show_title']=='no')?'selected':'') ; ?>>no</option>
                    </select>
                </dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/page/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
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
            $('#form_add_page').submit();
        });
        $('#form_add_page').submit(function(){
            if($('#slug').val()==''){
                $('#slug').focus();
                return false;
            }else{
                return true;
            }
        });
    });
</script>