<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('menu'); ?></div>
        <div class="fr">
            <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/menu/add'); ?>">+ <?php echo __('add_menu'); ?></a>
        </div>
    </div>
    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('title'); ?></th>
            <th><?php echo __('link'); ?></th>
            <th><?php echo __('sort_order'); ?></th>
            <th colspan="4">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($menus) && is_array($menus) && count($menus) > 0){
            $count = 0;
            foreach($menus as $item){
                $count++;
                echo '<tr class="'. ($count%2==0?'odd ':'') .'">
            <td>'. $count .'</td>
            <td>'. htmlspecialchars($item['title']) .'</td>
            <td>'. htmlspecialchars($item['link']) .'</td>
            <td>'. (int)$item['sort_order'] .'</td>
            <td width="50" align="center"><a href="'. Mava_Url::buildLink('admin/menu/edit',array('id' => $item['id'])) .'">'. __('edit') .'</a></td>
            <td width="50" align="center"><a href="javascript:void(0);" item-id="'. $item['id'] .'" item-title="'. htmlspecialchars($item['title']) .'" class="button_delete_item">'. __('delete') .'</a></td>
        </tr>';
            }
        }else{
            echo '<tr><td colspan="10"><div class="text-center">'. __('no_menu_found') .'</div></td></tr>';
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
        $('.button_delete_item').on('click',function(){
            var itemID = $(this).attr('item-id');
            var itemTitle = $(this).attr('item-title');
            MV.dialog.st_confirm('<?php echo __('delete_menus_confirm'); ?><div class="space"></div><b>'+ itemTitle +' (ID: '+ itemID +')</b>',function(){
                MV.post(DOMAIN+'/admin/menu/delete',{
                    id: itemID
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