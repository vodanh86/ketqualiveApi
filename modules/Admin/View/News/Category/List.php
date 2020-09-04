<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('news_category'); ?></div>
        <div class="fr">
            <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/news/add_category'); ?>">+ <?php echo __('add_category'); ?></a>
        </div>
    </div>
    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('title'); ?></th>
            <th><?php echo __('sort_order'); ?></th>
            <th colspan="4">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(is_array($categories) && count($categories) > 0){
            $count = 0;
            foreach($categories as $item){
                $count++;
                echo '<tr class="'. ($count%2==0?'odd ':'') .'">
            <td>'. $count .'</td>
            <td>'. text_loop("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $item['level']) . (isset($item['_data']) && isset(array_values($item['_data'])[0]['title'])?htmlspecialchars(array_values($item['_data'])[0]['title']):__('unnamed')) .'</td>
            <td>'. text_loop("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $item['level']) . (int)$item['sort_order'] .'</td>
            <td width="50" align="center"><a href="'. Mava_Url::buildLink('admin/news/index',array('categoryId' => $item['category_id'])) .'">'. __('post') .'</a></td>
            <td width="50" align="center"><a href="'. Mava_Url::buildLink('admin/news/edit_category',array('categoryID' => $item['category_id'])) .'">'. __('edit') .'</a></td>
            <td width="50" align="center"><a href="javascript:void(0);" category-id="'. $item['category_id'] .'" category-title="'. (isset($item['_data']) && isset(array_values($item['_data'])[0]['title'])?htmlspecialchars(array_values($item['_data'])[0]['title']):__('unnamed')) .'" class="button_delete_category">'. __('delete') .'</a></td>
        </tr>';
            }
        }
        ?>
        </tbody>
    </table>
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
        $('.button_delete_category').on('click',function(){
            var categoryId = $(this).attr('category-id');
            var categoryTitle = $(this).attr('category-title');
            MV.dialog.st_confirm('<?php echo __('delete_news_category_confirm'); ?><div class="space"></div><b>'+ categoryTitle +' (ID: '+ categoryId +')</b>',function(){
                MV.post(DOMAIN+'/admin/news/delete_category',{
                    categoryId: categoryId
                },function(res){
                    if(res.status==1){
                        MV.dialog.st_alert('<div class="text_success"><s class="icon_success_big"></s>'+ res.message +'</div>',function(){
                            window.location.reload();
                        });
                    }else{
                        MV.dialog.st_alert('<div class="text_error"><s class="icon_error_big"></s>'+res.message+'</div>');
                    }
                });
            },function(){
                // cancel
            });
        });
    });
</script>s