<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('user_group'); ?></div>
        <div class="fr">
            <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/users/group_add'); ?>">+ <?php echo __('add_group'); ?></a>
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
        if(isset($groups) && count($groups) > 0){
            $count = 0;
            foreach($groups as $item){
                $count++;
                echo '<tr class="'. ($count%2==0?'odd ':'') .'">
                <td>'. $count .'</td>
                <td>'. htmlspecialchars($item['group_title']) .'</td>
                <td>'. (int)$item['sort_order'] .'</td>
                <td width="80" align="center"><a href="'. Mava_Url::buildLink('admin/users/index',array('groupID' => $item['group_id'])) .'">'. __('member') .'</a></td>
                <td width="80" align="center"><a href="'. Mava_Url::buildLink('admin/users/group_permission',array('groupID' => $item['group_id'])) .'">'. __('user_permission') .'</a></td>
                <td width="50" align="center"><a href="'. Mava_Url::buildLink('admin/users/group_edit',array('groupID' => $item['group_id'])) .'">'. __('edit') .'</a></td>
                <td width="50" align="center"><a href="javascript:void(0);" group-id="'. $item['group_id'] .'" group-title="'. htmlspecialchars($item['group_title']) .'" class="button_delete_group">'. __('delete') .'</a></td>
            </tr>';
            }
        }
        ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.button_delete_group').on('click',function(){
            var groupID = $(this).attr('group-id');
            var groupTitle = $(this).attr('group-title');
            MV.dialog.st_confirm('<?php echo __('delete_user_group_confirm'); ?><div class="space"></div><b>'+ groupTitle +' (ID: '+ groupID +')</b>',function(){
                MV.post(DOMAIN+'/admin/users/group_delete',{
                    groupID: groupID
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