<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_view_user" id="form_view_user" action="<?php echo Mava_Url::buildLink('admin/users/detail'); ?>" method="post" enctype="multipart/form-data">
        <h2 class="mava_form_title"><?php echo htmlspecialchars($user['custom_title']); ?></h2>
        <div class="mava_form_rows">
            <dl class="row">
                <dt><?php echo __('user_group'); ?></dt>
                <dd><b><?php echo is_array($userGroup)?htmlspecialchars($userGroup['group_title']):""; ?></b></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('fullname'); ?></dt>
                <dd><?php echo htmlspecialchars($user['custom_title']); ?></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('email'); ?></dt>
                <dd><?php echo htmlspecialchars($user['email']); ?></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('phone'); ?></dt>
                <dd><?php echo htmlspecialchars($user['phone'])?></dd>
            </dl>

            <dl class="row">
                <dt><?php echo __('gender'); ?></dt>
                <dd><?php echo ($user['gender']!='')?__($user['gender']):__('unknown'); ?></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('city'); ?></dt>
                <dd><?php echo htmlspecialchars($cityTitle); ?></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('birthday'); ?></dt>
                <dd><?php echo $user['birthday']>0?date('d/m/Y', $user['birthday']):""; ?></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('active'); ?></dt>
                <dd><?php echo $user['is_active']==1?__('activated').' (<a href="javascript:void(0);" id="deactive_account" data-id="'. $user['user_id'] .'">'. __('deactive') .'</a>)':__('nonactivated') .' (<a href="javascript:void(0);" id="active_account" data-id="'. $user['user_id'] .'">'. __('active') .'</a>)'; ?></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('register_date'); ?></dt>
                <dd><?php echo date('d/m/Y H:i:s', $user['register_date']); ?></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('last_activity'); ?></dt>
                <dd><?php echo date('d/m/Y H:i:s', $user['last_activity']); ?></dd>
            </dl>
            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="<?php echo Mava_Url::buildLink('admin/users/edit', array('userID' => $user['user_id'])); ?>" class="btn_blue mava_button_medium"><?php echo __('edit'); ?></a>
                    <a href="javascript:history.go(-1);" class="mava_button mava_button_gray mava_button_medium"><?php echo __('back'); ?></a>
                </dd>
            </dl>
        </div>
    </form>

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
                                <select class="permission_status" perm-id="'. $item['perm_id'] .'" user-id="'. $user['user_id'] .'">
                                <option value="inherit"'. (!isset($user['permissions'][$item['perm_id']]) || (isset($user['permissions'][$item['perm_id']]) && $user['permissions'][$item['perm_id']]=='inherit')?' selected':'') .'>Theo nhóm ('. (isset($userGroup['permissions'][$item['perm_id']]) && $userGroup['permissions'][$item['perm_id']]=='allowed'?__('allowed'):__('denied')) .')</option>
                                <option value="allowed"'. (isset($user['permissions'][$item['perm_id']]) && $user['permissions'][$item['perm_id']]=='allowed'?' selected':'') .'>Cho phép</option>
                                <option value="denied"'. (isset($user['permissions'][$item['perm_id']]) && $user['permissions'][$item['perm_id']]=='denied'?' selected':'') .'>Không cho phép</option></select>
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
        <?php
            if(isset($error_message) && $error_message != ''){
        ?>
        MV.show_notice('<?php echo $error_message; ?>',3);
        <?php
            }
        ?>

        $('#active_account').click(function(){
            var userId = $(this).attr('data-id');
            MV.post(DOMAIN +'/admin/users/active', {
                userId: userId
            }, function(res){
                window.location.reload();
            });
        });

        $('#deactive_account').click(function(){
            var userId = $(this).attr('data-id');
            MV.post(DOMAIN +'/admin/users/deactive', {
                userId: userId
            }, function(res){
                window.location.reload();
            });
        });

        $('.permission_status').change(function(){
            var userID = $(this).attr('user-id');
            var permID = $(this).attr('perm-id');
            var permValue = $(this).val();
            if(userID > 0 && permID > 0 && permValue != ""){
                MV.post(DOMAIN+'/admin/users/change_user_permission',{
                    userID: userID,
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