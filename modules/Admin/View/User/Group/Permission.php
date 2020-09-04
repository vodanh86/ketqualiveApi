<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('user_permission') .": ". htmlspecialchars($user_group['group_title']); ?></div>
    </div>
    <div class="mava_list_filter permission user_group_permission">
        <div class="mava_list_filter_head clearfix">
            <div class="fl"><?php echo __('user_permission'); ?></div>
        </div>
        <ol>
            <?php
            if(is_array($permissionGroups) && count($permissionGroups) > 0){
                foreach($permissionGroups as $group){
                    echo '<li><h3>'. htmlspecialchars($group['title']) .'</h3>';
                    if(is_array($group['permissions']) && sizeof($group['permissions']) > 0){
                        echo '<ul class="list_items permission_list_main">';
                        $count = 0;
                        foreach($group['permissions'] as $item){
                            $count++;
                            echo '<li class="item">
                                <select class="permission_status" perm-id="'. $item['perm_id'] .'" group-id="'. $user_group['group_id'] .'"><option value="allowed"'. ((isset($groupPermission[$item['perm_id']]) && $groupPermission[$item['perm_id']]=='allowed')?' selected':'') .'>Cho phép</option><option value="denied"'. (!isset($groupPermission[$item['perm_id']]) || (isset($groupPermission[$item['perm_id']]) && $groupPermission[$item['perm_id']]=='denied')?' selected':'') .'>Không cho phép</option></select>
                                <h4>'. htmlspecialchars($item['title']) .' <span class="item_description">'. $item['perm_key'] .'</span></h4>
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
        $('.permission_status').change(function(){
            var userGroupID = $(this).attr('group-id');
            var permID = $(this).attr('perm-id');
            var permValue = $(this).val();
            if(userGroupID > 0 && permID > 0 && permValue != ""){
                MV.post(DOMAIN+'/admin/users/change_group_permission',{
                    userGroupID: userGroupID,
                    permID: permID,
                    permValue: permValue
                },function(res){
                    if(res.status==1){
                        MV.show_notice(res.message, 2);
                    }else{
                        MV.dialog.st_alert('<div class="text_error"><s class="icon_error_big"></s>'+res.message+'</div>');
                    }
                });
            }
        });
    });
</script>