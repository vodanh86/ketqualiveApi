<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('ads_campaign_group') .' ('. number_format($total,0) .')'; ?></div>
        <div class="fr">
            <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/ads/add-campaign-group'); ?>">+ <?php echo __('add_campaign_group'); ?></a>
        </div>
    </div>
    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('name'); ?></th>
            <th><?php echo __('sort_order'); ?></th>
            <th colspan="4">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($campaign_groups) && is_array($campaign_groups) && count($campaign_groups) > 0){
            $count = ($page-1)*$limit;
            foreach($campaign_groups as $item){
                $count++;
                echo '<tr class="'. ($count%2==0?'odd':'') .'">
            <td>'. $count .'</td>
            <td><b style="color:'. $item['color'] .';">'. htmlspecialchars($item['title']) .'</b></td>
            <td>'. $item['sort_order'] .'</td>
            <td width="150" align="center"><a href="'. Mava_Url::buildLink('admin/ads/campaigns',array('group_id' => $item['id'])) .'">'. __('campaigns') .'</a></td>
            <td width="50" align="center"><a href="'. Mava_Url::buildLink('admin/ads/edit-campaign-group',array('id' => $item['id'])) .'">'. __('edit') .'</a></td>
            <td width="50" align="center"><a href="javascript:void(0);" item-id="'. $item['id'] .'" item-title="'. htmlspecialchars($item['title']) .'" class="button_delete_banner">'. __('delete') .'</a></td>
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
        $('.button_delete_banner').on('click',function(){
            var itemID = $(this).attr('item-id');
            var itemTitle = $(this).attr('item-title');
            MV.dialog.st_confirm('<?php echo __('delete_campaign_group_confirm'); ?><div class="space"></div><b>'+ itemTitle +' (ID: '+ itemID +')</b>',function(){
                MV.post(DOMAIN+'/admin/ads/delete-campaign-group',{
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