<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('ads_campaign') .' ('. number_format($total,0) .')'; ?></div>
        <div class="fr">
            &nbsp; <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/ads/add-campaign'); ?>">+ <?php echo __('add_campaign'); ?></a>
        </div>
        <div class="fr mbc-search-campaign">
            <form name="form_search" id="form_search" action="<?php echo Mava_Url::getPageLink('admin/ads/campaigns'); ?>" method="get">
                <?php
                    if($group_id > 0){
                        echo '<input type="hidden" name="group_id" value="'. $group_id .'" />';
                    }
                ?>
                <input type="search" id="input_search" style="width: 200px;" class="input_text" name="q" placeholder="<?php echo __('enter_to_search'); ?>" value="<?php echo isset($search_term)?htmlspecialchars($search_term):''; ?>">
                <?php
                if(is_array($groups) && count($groups) > 0){
                    echo '<select class="input_text mbc-campaign-filter">
                        <option value="0">'. __('all_group') .'</option>
                    ';
                    foreach($groups as $item){
                        echo '<option value="'. $item['id'] .'"'. ($item['id'] == $group_id?' selected':'') .'>'. htmlspecialchars($item['title']) .'</option>';
                    }
                    echo '</select>';
                }
                ?>
                <a href="javascript:void(0);" id="button_search" onclick="$('#form_search').submit();" class="mava_button mava_button_gray"><?php echo __('search'); ?></a>
            </form>
        </div>

    </div>
    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php
                if($sort_by == 'id' && $sort_dir == 'desc'){
                    echo '<a href="'. Mava_Url::getPageLink('admin/ads/campaigns', array('sort_by' => 'id','sort_dir' => 'asc')) .'">'. __('code') .' <i class="fa fa-caret-down"></i></a>';
                }else{
                    echo '<a href="'. Mava_Url::getPageLink('admin/ads/campaigns', array('sort_by' => 'id','sort_dir' => 'desc')) .'">'. __('code') .' '. ($sort_by == 'id'?'<i class="fa fa-caret-up"></i>':'') .'</a>';
                }
                ?></th>
            <th><?php echo __('name'); ?></th>
            <th><?php
                if($sort_by == 'click_count' && $sort_dir == 'desc'){
                    echo '<a href="'. Mava_Url::getPageLink('admin/ads/campaigns', array('sort_by' => 'click_count','sort_dir' => 'asc')) .'">'. __('click') .' <i class="fa fa-caret-down"></i></a>';
                }else{
                    echo '<a href="'. Mava_Url::getPageLink('admin/ads/campaigns', array('sort_by' => 'click_count','sort_dir' => 'desc')) .'">'. __('click') .' '. ($sort_by == 'click_count'?'<i class="fa fa-caret-up"></i>':'') .'</a>';
                }
                ?></th>
            <th><?php
                if($sort_by == 'order_count' && $sort_dir == 'desc'){
                    echo '<a href="'. Mava_Url::getPageLink('admin/ads/campaigns', array('sort_by' => 'order_count','sort_dir' => 'asc')) .'">'. __('order') .' <i class="fa fa-caret-down"></i></a>';
                }else{
                    echo '<a href="'. Mava_Url::getPageLink('admin/ads/campaigns', array('sort_by' => 'order_count','sort_dir' => 'desc')) .'">'. __('order') .' '. ($sort_by == 'order_count'?'<i class="fa fa-caret-up"></i>':'') .'</a>';
                }
                ?></th>
            <th><?php
                if($sort_by == 'total_revenue' && $sort_dir == 'desc'){
                    echo '<a href="'. Mava_Url::getPageLink('admin/ads/campaigns', array('sort_by' => 'total_revenue','sort_dir' => 'asc')) .'">'. __('revenue') .' <i class="fa fa-caret-down"></i></a>';
                }else{
                    echo '<a href="'. Mava_Url::getPageLink('admin/ads/campaigns', array('sort_by' => 'total_revenue','sort_dir' => 'desc')) .'">'. __('revenue') .' '. ($sort_by == 'total_revenue'?'<i class="fa fa-caret-up"></i>':'') .'</a>';
                }
                ?></th>
            <th colspan="4">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($campaigns) && is_array($campaigns) && count($campaigns) > 0){
            $count = ($page-1)*$limit;
            foreach($campaigns as $item){
                if($item['group_color'] == ''){
                    $item['group_color'] = '#999999';
                }

                if($item['group_title'] == ''){
                    $item['group_title'] = __('ungrouped');
                }
                $count++;
                echo '<tr class="'. ($count%2==0?'odd':'') .'">
            <td>'. $count .'</td>
            <td'. ($sort_by=='id'?' style="background: #FFFDB6;"':'') .'>'. $item['id'] .'</td>
            <td>
                <b style="color: '. $item['group_color'] .';">[ '. htmlspecialchars($item['group_title']) .' ]</b> <a href="'. Mava_Url::getPageLink('admin/ads/view-campaign', array('id' => $item['id'])) .'">'. $item['title'] .'</a> '. ($item['note']!=""?'<a href="javascript:void(0);" class="mb-ads-note-toggle"><i class="fa fa-file-text"></i></a>':'') .'
                <div class="mb-ads-note well">'. nl2br(htmlspecialchars($item['note'])) .'</div>
                </td>
            <td'. ($sort_by=='click_count'?' style="background: #FFFDB6;"':'') .'>'. $item['click_count'] .'</td>
            <td'. ($sort_by=='order_count'?' style="background: #FFFDB6;"':'') .'>'. $item['order_count'] .'</td>
            <td'. ($sort_by=='total_revenue'?' style="background: #FFFDB6;"':'') .'>'. Mava_String::price_format($item['total_revenue']) .'</td>
            <td width="100" align="center"><i class="fa fa-line-chart"></i> <a href="'. Mava_Url::buildLink('admin/ads/view-campaign',array('id' => $item['id'])) .'">'. __('statistics') .'</a></td>
            <td width="50" align="center"><a href="'. Mava_Url::buildLink('admin/ads/edit-campaign',array('id' => $item['id'])) .'">'. __('edit') .'</a></td>
            <td width="50" align="center"><a href="javascript:void(0);" item-id="'. $item['id'] .'" item-title="'. htmlspecialchars($item['title']) .'" class="button_delete_banner">'. __('delete') .'</a></td>
        </tr>';
            }
        }else{
            echo '<tr><td colspan="10"><div class="text-center">'. __('no_campaign_found') .'</div></td></tr>';
        }
        ?>
        </tbody>
    </table>
    <?php echo $pagination; ?>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.mbc-campaign-filter').change(function(){
            if($(this).val() == 0){
                window.location.href = '<?php echo Mava_Url::getPageLink('admin/ads/campaigns'); ?>';
            }else{
                window.location.href = '<?php echo Mava_Url::getPageLink('admin/ads/campaigns'); ?>?group_id='+ $(this).val();
            }
        });
        $('.mb-ads-note-toggle').click(function(){
            $(this).parents('tr').find('.mb-ads-note').toggle();
        });
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
            MV.dialog.st_confirm('<?php echo __('delete_campaign_confirm'); ?><div class="space"></div><b>'+ itemTitle +' (ID: '+ itemID +')</b>',function(){
                MV.post(DOMAIN+'/admin/ads/delete-campaign',{
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