<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('admin_permission'); ?></div>
        <div class="fr">
            <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/permission/group_add'); ?>">+ <?php echo __('add_permission_group'); ?></a>
            <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/permission/add'); ?>">+ <?php echo __('add_permission'); ?></a>
        </div>
    </div>
    <div class="mava_list_filter permission">
        <div class="mava_list_filter_head clearfix">
            <div class="fl"><?php echo __('admin_permission'); ?></div>
        </div>
        <ol>
        <?php
            if(is_array($permissionGroups) && count($permissionGroups) > 0){
                foreach($permissionGroups as $group){
                    echo '<li><a href="javascript:void(0);" group-id="'. $group['group_id'] .'" group-title="'. htmlspecialchars($group['title']) .'" class="actionButton button_delete_group">'. __('delete') .'</a><a href="'. Mava_Url::buildLink('admin/permission/group_edit',array('groupID' => $group['group_id'])) .'" class="actionButton">'. __('edit') .'</a><h3>'. htmlspecialchars($group['title']) .'</h3>';
                    if(is_array($group['permissions']) && sizeof($group['permissions']) > 0){
                        echo '<ul class="list_items permission_list_main">';
                        $count = 0;
                        foreach($group['permissions'] as $item){
                            $count++;
                            echo '<li class="item">
                                <a href="javascript:void(0);" permission-id="'. $item['perm_id'] .'" permission-title="'. htmlspecialchars($item['title']) .'" class="actionButton button_delete_item button_delete_permission">'. __('delete') .'</a>
                                <h4><a class="button_edit_item button_edit_permission" href="'. Mava_Url::buildLink('admin/permission/edit',array('permissionID' => $item['perm_id'])) .'">'. htmlspecialchars($item['title']) .' <span class="item_description">'. $item['perm_key'] .'</span></a></h4>
                                </li>';
                        }
                        echo '</ul>';
                    }
                    echo '</li>';
                }
            }
        ?>
        </ol>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.button_delete_permission').on('click',function(){
            var permissionID = $(this).attr('permission-id');
            var permissionTitle = $(this).attr('permission-title');
            MV.dialog.st_confirm('<?php echo __('delete_permission_confirm'); ?><div class="space"></div><b>'+ permissionTitle +' (ID: '+ permissionID +')</b>',function(){
                MV.post(DOMAIN+'/admin/permission/delete',{
                    permissionID: permissionID
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

        $('.button_delete_group').on('click',function(){
            var groupID = $(this).attr('group-id');
            var groupTitle = $(this).attr('group-title');
            MV.dialog.st_confirm('<?php echo __('delete_permission_group_confirm'); ?><div class="space"></div><b>'+ groupTitle +' (ID: '+ groupID +')</b>',function(){
                MV.post(DOMAIN+'/admin/permission/group_delete',{
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