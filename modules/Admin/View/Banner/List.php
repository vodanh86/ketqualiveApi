<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo (is_array($position)?$position['title']:__('all_banner')) .' ('. number_format($total,0) .')'; ?></div>
        <div class="fr">
            <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/banner/add'); ?>">+ <?php echo __('add_banner'); ?></a>
            <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/banner/position'); ?>"><?php echo __('banner_position'); ?></a>
        </div>
    </div>
    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('image'); ?></th>
            <th><?php echo __('title'); ?></th>
            <th><?php echo __('link'); ?></th>
            <th><?php echo __('banner_position'); ?></th>
            <th colspan="4">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($banners) && is_array($banners) && count($banners) > 0){
            $count = ($page-1)*$limit;
            foreach($banners as $item){
                $count++;
                $data = isset($item['_data'])?array_values($item['_data']):array();
                if(is_array($data) && count($data) > 0){
                    $title = htmlspecialchars($data[0]['title']);
                    $subtitle = htmlspecialchars($data[0]['subtitle']);
                    $href = htmlspecialchars($data[0]['href']);
                    $image = json_decode($data[0]['image'], true);
                    if(is_array($image) && count($image) > 0){
                        $image = json_decode($image[0], true);
                    }else{
                        $image = false;
                    }
                }else{
                    $title = '';
                    $subtitle = '';
                    $image = false;
                    $href = '';
                }
                echo '<tr class="'. ($count%2==0?'odd':'') .'">
            <td>'. $count .'</td>
            <td><img src="'. (is_array($image)?image_url($image['image']):'') .'" style="max-width: 200px;" /></td>
            <td><p><b>'. $title .'</b></p>'. $subtitle .'</td>
            <td>'. $href .'</td>
            <td><a href="'. Mava_Url::getPageLink('admin/banner/index', array('positionID' => $item['position_id'])) .'">'. $item['position_title'] .'</a></td>
            <td width="50" align="center"><a href="'. Mava_Url::buildLink('admin/banner/edit',array('id' => $item['id'])) .'">'. __('edit') .'</a></td>
            <td width="50" align="center"><a href="javascript:void(0);" item-id="'. $item['id'] .'" item-title="'. $title .'" class="button_delete_banner">'. __('delete') .'</a></td>
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
            MV.dialog.st_confirm('<?php echo __('delete_banner_confirm'); ?><div class="space"></div><b>'+ itemTitle +' (ID: '+ itemID +')</b>',function(){
                MV.post(DOMAIN+'/admin/banner/delete',{
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