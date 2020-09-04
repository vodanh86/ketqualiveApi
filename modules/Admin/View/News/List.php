<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('all_news') . ((count($category) > 0)?": ". htmlspecialchars($category['_data'][Mava_Visitor::getLanguageCode()]['title']):"") . ($searchTerm!=""?": ". htmlspecialchars($searchTerm):""); ?></div>
        <div class="fr">
            <form name="form_search_post" id="form_search_post" action="<?php echo Mava_Url::buildLink('admin/news/index'); ?>" method="get">
                <input type="search" id="input_search_post" style="width: 200px;" class="input_text" name="q" placeholder="<?php echo __('enter_keyword_to_search_post'); ?>" value="<?php echo isset($searchTerm)?htmlspecialchars($searchTerm):""; ?>" />
                <a href="javascript:void(0);" id="button_search_post" class="mava_button mava_button_gray"><?php echo __('search'); ?></a>
                <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/news/add', array('categoryId' => (isset($categoryId)?$categoryId:0))); ?>">+ <?php echo __('add_post'); ?></a>
            </form>
        </div>
    </div>
    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('title'); ?></th>
            <th><?php echo __('post_date'); ?></th>
            <th><?php echo __('view_count'); ?></th>
            <th><?php echo __('category_title'); ?></th>
            <th colspan="3">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($posts) && is_array($posts) && count($posts) > 0){
            $count = 0;
            foreach($posts as $item){
                $count++;
                echo '<tr class="'. ($count%2==0?'odd ':'') .'">
            <td>'. $count .'</td>
            <td>'. htmlspecialchars($item['title']) .'</td>
            <td><span class="quickTooltip" title="'. date('d/m/Y H:i:s', $item['created_date']) .'">'. print_time($item['created_date']) .'</span></td>
            <td>'. number_format($item['views'],0) .'</td>
            <td><a href="'. Mava_Url::buildLink('admin/news/index', array('categoryId' => $item['category_id'])) .'">'. htmlspecialchars($item['category_title']) .'</a></td>
            <td width="50" align="center"><a href="'. Mava_Url::buildLink('admin/news/edit',array('newsId' => $item['news_id'])) .'">'. __('edit') .'</a></td>
            <td width="50" align="center"><a href="javascript:void(0);" post-id="'. $item['news_id'] .'" post-title="'. htmlspecialchars($item['title']) .'" class="button_delete_post">'. __('delete') .'</a></td>
        </tr>';
            }
        }else{
            echo '<tr><td colspan="10"><div class="text-center">'. __('no_campaign_found') .'</div></td></tr>';
        }
        ?>
        </tbody>
    </table>

    <?php
    $paginateParams = array();
    if($categoryId > 0){
        $paginateParams['categoryId'] = $categoryId;
    }

    if($searchTerm != ""){
        $paginateParams['q'] = $searchTerm;
    }
    echo Mava_View::buildPagination(
        Mava_Url::buildLink('admin/news/index',$paginateParams),
        ceil($total/$limit),
        $page,
        $offset,
        'post_list_pagination'
    );
    ?>

</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#button_search_post').click(function(){
            $('#form_search_post').submit();
        });
        <?php
            if(isset($error_message) && $error_message != ''){
        ?>
        MV.show_notice('<?php echo $error_message; ?>',3);
        <?php
            }
            if(isset($added) && $added > 0){
        ?>
        MV.show_notice('<?php echo __('post_added'); ?>',3);
        <?php
            }
            if(isset($updated) && $updated > 0){
        ?>
        MV.show_notice('<?php echo __('post_updated'); ?>',3);
        <?php
            }
            if(isset($deleted) && $deleted > 0){
        ?>
        MV.show_notice('<?php echo __('post_deleted'); ?>',3);
        <?php
            }
        ?>
        $('.button_delete_post').on('click',function(){
            var postId = $(this).attr('post-id');
            var postTitle = $(this).attr('post-title');
            MV.dialog.st_confirm('<?php echo __('delete_post_confirm'); ?><div class="space"></div><b>'+ postTitle +' (ID: '+ postId +')</b>',function(){
                MV.post(DOMAIN+'/admin/news/delete',{
                    postId: postId
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
</script>