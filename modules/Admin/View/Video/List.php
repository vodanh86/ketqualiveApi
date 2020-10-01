<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('admin_videos'); ?></div>
        <div class="fr">
            <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/videos/add'); ?>">+ <?php echo __('add_video'); ?></a>
        </div>
    </div>
    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('created_date'); ?></th>
            <th><?php echo __('title'); ?></th>
            <th><?php echo __('id'); ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($videos) && is_array($videos) && count($videos) > 0){
            $count = 0;
            foreach($videos as $item){
                $count++;
                echo '<tr class="'. ($count%2==0?'odd ':'') .'">
            <td>'. $count .'</td>
            <td title="'. __('title') .': '. $item['youtube_id'] .'">'. $item['created_at'] .'</td>
            <td>'. htmlspecialchars($item['title']) .'</td>
            <td>'. htmlspecialchars($item['youtube_id']) .'</td>
            <td width="50" align="center"><a href="javascript:void(0);" user-id="'. $item['id'] .'" user-email="'. htmlspecialchars($item['id']) .'" user-title="'. htmlspecialchars($item['title']) .'" class="button_delete_user">'. __('delete') .'</a></td>
            </tr>';
            }
        }
        ?>
        </tbody>
    </table>
    <?php echo $pagination; ?>
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

        $('.button_delete_user').on('click',function(){
            var userID = $(this).attr('user-id');
            var userEmail = $(this).attr('user-email');
            var userTitle = $(this).attr('user-title');
            MV.dialog.st_confirm('<?php echo __('delete_video_confirm'); ?><div class="space"></div><b>'+ userTitle +' - '+ userEmail +' (ID: '+ userID +')</b>',function(){
                MV.post(DOMAIN+'/admin/videos/delete',{
                    videoID: userID
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