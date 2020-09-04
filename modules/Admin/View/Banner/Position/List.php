<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('banner_position'); ?></div>
        <div class="fr">
            <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/banner/add_position'); ?>">+ <?php echo __('add_banner_position'); ?></a>
        </div>
    </div>
    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('title'); ?></th>
            <th><?php echo __('position_code'); ?></th>
            <th colspan="3">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($positions) && is_array($positions) && count($positions) > 0){
            $count = 0;
            foreach($positions as $item){
                $count++;
                echo '<tr class="'. ($count%2==0?'odd ':'') .'">
            <td>'. $count .'</td>
            <td>'. $item['title'] .'</td>
            <td>'. $item['position'] .'</td>
            <td width="100" align="center"><a href="'. Mava_Url::buildLink('admin/banner/index',array('positionID' => $item['id'])) .'">'. __('banner') .'</a></td>
            <td width="50" align="center"><a href="'. Mava_Url::buildLink('admin/banner/edit_position',array('positionID' => $item['id'])) .'">'. __('edit') .'</a></td>
            <td width="50" align="center"><a href="javascript:void(0);" position-id="'. $item['id'] .'" position-title="'. htmlspecialchars($item['title']) .'" class="button_delete_position">'. __('delete') .'</a></td>
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
        $('.button_delete_position').on('click',function(){
            var positionId = $(this).attr('position-id');
            var positionTitle = $(this).attr('position-title');
            MV.dialog.st_confirm('<?php echo __('delete_banner_position_confirm'); ?><div class="space"></div><b>'+ positionTitle +' (ID: '+ positionId +')</b>',function(){
                MV.post(DOMAIN+'/admin/banner/delete_position',{
                    positionID: positionId
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