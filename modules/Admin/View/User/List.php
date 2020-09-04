<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('admin_users') . (($groupTitle!="")?": ". htmlspecialchars($groupTitle):"") . (($searchTerm!="")?": ". htmlspecialchars($searchTerm):""); ?></div>
        <div class="fr">
            <form name="form_search_users" id="form_search_users" action="<?php echo Mava_Url::buildLink('admin/users/index'); ?>" method="get">
                <?php
                    if(isset($groupID) && $groupID > 0){
                        echo '<input type="hidden" name="groupID" value="'. (int)$groupID .'" />';
                    }
                ?>
                <input type="search" id="input_search_users" style="width: 200px;" class="input_text" name="q" placeholder="<?php echo __('enter_keyword_to_search_user'); ?>" value="<?php echo isset($searchTerm)?htmlspecialchars($searchTerm):""; ?>" />
                <a href="javascript:void(0);" id="button_search_users" class="mava_button mava_button_gray"><?php echo __('search'); ?></a>
                <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/users/add'); ?>">+ <?php echo __('add_user'); ?></a>
            </form>
        </div>
    </div>
    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('id'); ?></th>
            <th><?php echo __('fullname'); ?></th>
            <th><?php echo __('email'); ?></th>
            <th><?php echo __('register_date'); ?></th>
            <th><?php echo __('active'); ?></th>
            <th><?php echo __('user_group'); ?></th>
            <th colspan="3">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($users) && count($users) > 0){
            $count = 0;
            foreach($users as $item){
                $count++;
                echo '<tr class="'. ($count%2==0?'odd ':'') .'">
            <td>'. $count .'</td>
            <td>'. $item['user_id'] .'</td>
            <td>'. htmlspecialchars($item['custom_title']) .'</td>
            <td>'. htmlspecialchars($item['email']) .'</td>
            <td><span title="'. date('d/m/Y H:i:s', $item['register_date']) .'">'. print_time($item['register_date']) .'</td>
            <td>'. ($item['is_active']==1?'<span class="green">'. __('activated') .'</span>': '<span class="orange">'.__('nonactivated') .'</span>') .'</td>
            <td><a href="'. Mava_Url::getPageLink('admin/users/index', array('groupID' => $item['user_group_id'],'q' => $searchTerm)) .'">'. $item['group_title'] .'</a></td>
            <td width="50" align="center"><a href="'. Mava_Url::buildLink('admin/users/detail',array('userID' => $item['user_id'])) .'">'. __('detail') .'</a></td>
            <td width="50" align="center"><a href="'. Mava_Url::buildLink('admin/users/edit',array('userID' => $item['user_id'])) .'">'. __('edit') .'</a></td>
            <td width="50" align="center"><a href="javascript:void(0);" user-id="'. $item['user_id'] .'" user-email="'. htmlspecialchars($item['email']) .'" user-title="'. htmlspecialchars($item['custom_title']) .'" class="button_delete_user">'. __('delete') .'</a></td>
        </tr>';
            }
        }
        ?>
        </tbody>
    </table>

    <?php
    $paginateParams = array();

    if($searchTerm != ""){
        $paginateParams['q'] = $searchTerm;
    }
    echo Mava_View::buildPagination(
        Mava_Url::buildLink('admin/users/index',$paginateParams),
        ceil($total/$limit),
        $page,
        $offset,
        'users_list_pagination'
    );
    ?>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#button_search_users').click(function(){
            $('#form_search_users').submit();
        });
        <?php
            if(isset($error_message) && $error_message != ''){
        ?>
        MV.show_notice('<?php echo $error_message; ?>',3);
        <?php
            }
            if(isset($added) && $added > 0){
        ?>
        MV.show_notice('<?php echo __('user_added'); ?>',3);
        <?php
            }
            if(isset($updated) && $updated > 0){
        ?>
        MV.show_notice('<?php echo __('user_updated'); ?>',3);
        <?php
            }
            if(isset($deleted) && $deleted > 0){
        ?>
        MV.show_notice('<?php echo __('user_deleted'); ?>',3);
        <?php
            }
        ?>
        $('.button_delete_user').on('click',function(){
            var userID = $(this).attr('user-id');
            var userEmail = $(this).attr('user-email');
            var userTitle = $(this).attr('user-title');
            MV.dialog.st_confirm('<?php echo __('delete_user_confirm'); ?><div class="space"></div><b>'+ userTitle +' - '+ userEmail +' (ID: '+ userID +')</b>',function(){
                MV.post(DOMAIN+'/admin/users/delete',{
                    userID: userID
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