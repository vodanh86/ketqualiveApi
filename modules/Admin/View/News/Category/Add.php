<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_news_category" id="form_add_news_category" action="<?php echo Mava_Url::buildLink('admin/news/add_category'); ?>" method="post">
        <h2 class="mava_form_title"><?php echo __('add_category'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('parent_category'); ?></dt>
                <dd>
                    <select name="categoryParent" id="categoryParent">
                        <option value="0">- <?php echo __('this_is_parent'); ?> -</option>
                        <?php
                            if(is_array($categories) && count($categories) > 0){
                                foreach($categories as $item){
                                    echo '<option value="'. (int)$item['category_id'] .'">'. text_loop("----", $item['level']) . (isset($item['_data']) && isset(array_values($item['_data'])[0]['title'])?htmlspecialchars(array_values($item['_data'])[0]['title']):__('unnamed')) .'</option>';
                                }
                            }
                        ?>
                    </select>
                </dd>
            </dl>
            <?php
                if(is_array($languages) && count($languages) > 0){
                    $count = 0;
                    echo '<div class="mava_row_tab"><dl class="row row_tab"><dt></dt><dd><ul class="clearfix">';
                    foreach($languages as $item){
                        $count++;
                        echo '<li><a href="javascript:void(0);" class="item'. ($count == 1?" active":"") .'" rel="language_tab_'. $item['language_id'] .'">'. htmlspecialchars($item['title']) .'</a></li>';
                    }
                    echo '</ul></dd></dl>';
                    $count = 0;
                    foreach($languages as $item){
                        $count++;
            ?>
                        <div class="row_group<?php echo $count>1?" hidden":""; ?>" id="language_tab_<?php echo $item['language_id']; ?>">
                            <dl class="row">
                                <dt><?php echo __('category_title'); ?></dt>
                                <dd><input type="text" class="input_text input_medium categoryTitle" name="categoryTitle[<?php echo $item['language_code']; ?>]" id="categoryTitle_<?php echo $item['language_id']; ?>" /></dd>
                            </dl>
                            <dl class="row">
                                <dt><?php echo __('category_description'); ?></dt>
                                <dd><textarea class="input_area categoryDescription" name="categoryDescription[<?php echo $item['language_code']; ?>]" id="categoryDescription_<?php echo $item['language_id']; ?>"></textarea></dd>
                            </dl>
                        </div>
            <?php
                    }
                    echo '</div>';
                }
            ?>
            <dl class="row">
                <dt><?php echo __('sort_order'); ?></dt>
                <dd class="mava_spinbox">
                    <input type="text" class="input_text input_short mava_spinbox_value" data-step="1" data-min="0" value="0" name="categorySortOrder" id="categorySortOrder">
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_plus">+</a>
                    <a href="javascript:void(0);" class="mava_button mava_button_gray mava_button_medium mava_spinbox_minus">-</a>
                </dd>
            </dl>

            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/news/category'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
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
        $('.categoryTitle').first().focus();

        $('#button_submit_add').click(function(){
            $('#form_add_news_category').submit();
        });
        $('#form_add_news_category').submit(function(){
            if($('.categoryTitle').first().val()==''){
                $('.categoryTitle').first().focus();
                return false;
            }else{
                return true;
            }
        });


    });
</script>