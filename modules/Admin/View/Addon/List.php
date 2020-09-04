<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
</div>
<div class="table_action_button clearfix">
    <div class="fl mava_table_title">
        <?php echo __('installed_addons'); ?>
    </div>
    <div class="fr">
        <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/add-ons/add'); ?>">+ <?php echo __('add_addon'); ?></a>
        <a class="ml5 mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/add-ons/install'); ?>"><?php echo __('install_addon'); ?></a>
    </div>
</div>
<div class="mava_list_filter option_group_list">
    <div class="mava_list_filter_head clearfix">
        <div class="fl"><?php echo __('addons'); ?></div>
        <div class="fr">
            <input type="search" id="filter_title" class="input_text input_small" results="10" placeholder="<?php echo __('filter_addons'); ?>" />
            <a href="javascript:void(0);" id="clear_filter" class="mava_button mava_button_gray mava_button_small"><?php echo __('clear_filter'); ?></a>
        </div>
    </div>

    <ul class="list_items list_no_scroll option_list_main">
        <?php
        if(isset($addons) && sizeof($addons) > 0){
            $count = 0;
            foreach($addons as $item){
                $count++;
                echo '<li class="item">
                                <a href="javascript:void(0);" addon-id="'. $item['addon_id'] .'" addon-title="'. htmlspecialchars($item['title']) .'" class="actionButton button_delete_item button_delete_addon">'. __('delete') .'</a>
                                <h4>
                                <div class="actionButton st_ui_dropdown mousehover">
                                    <a href="javascript:void(0);" class="label">Thao tác <s class="icon_arrow_down"></s></a>
                                    <div class="list">
                                        <a href="#" class="item">Upgrade</a>
                                        <a href="#" class="item">Disable</a>
                                        <a href="#" class="item">Sửa</a>
                                        <a href="#" class="item">Uninstall</a>
                                        <a href="#" class="item">Export</a>
                                    </div>
                                </div>
                                <label class="actionButton toggleAction"><input class="quickTooltip" title="Vô hiệu hóa" type="checkbox" /></label>
                                <a href="'. Mava_Url::buildLink('admin/add-ons/edit',array('addOnID' => $item['addon_id'])) .'" class="button_edit_item"><b>'. htmlspecialchars($item['title']) .'</b><span class="item_description">'. htmlspecialchars($item['version_string']) .'</span></a></h4>
                                </li>';
            }
        }
        ?>
    </ul>
    <div class="filter_option_no_results hidden">
        <div class="no_filter_item_found"><?php echo __('no_addon_found'); ?></div>
    </div>
    <div class="list_footer clearfix">
        <div class="list_row_stats">
            <?php
            echo __('show_x_y_in_z_record',array(
                'start' => $skip+1,
                'end' => $skip+sizeof($addons),
                'total' => number_format($total,0,',','.')
            ));
            ?>
        </div>
    </div>
    <?php
    echo Mava_View::buildPagination(
        Mava_Url::buildLink('admin/add-ons/index'),
        $total_page,
        $page,
        $page_offset,
        'addon_list_pagination'
    );
    ?>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        if(MV.cookie('option_group_filter_term')!=undefined && MV.cookie('option_group_filter_term')!=""){
            $('#filter_title').val(MV.cookie('option_group_filter_term'));
            do_filter_option();
        }
        function do_filter_option(){
            MV.cookie('option_group_filter_term',$('#filter_title').val());
            var filterTitle = $('#filter_title').val().trim();
            if(filterTitle!=''){
                var matchCount = 0;
                $('.option_list_main .item').each(function(){
                    var regexObj = new RegExp("("+filterTitle+")",'ig');
                    obj = $(this);
                    if(obj.text().match(regexObj)){
                        var groupTitle = obj.find('.button_edit_item b').eq(0);
                        groupTitle.html(groupTitle.text().replace(regexObj,"<u>$1</u>"));
                        var groupDescription = obj.find('.button_edit_item span').eq(0);
                        groupDescription.html(groupDescription.text().replace(regexObj,"<u>$1</u>"));
                        obj.show();
                        matchCount++;
                    }else{
                        obj.hide();
                    }
                });
                if(matchCount==0){
                    $('.filter_option_no_results').removeClass('hidden');
                }else{
                    $('.filter_option_no_results').addClass('hidden');
                }
            }else{
                $('.option_list_main .item').each(function(){
                    var groupTitle = $(this).find('.button_edit_item b').eq(0);
                    groupTitle.html(groupTitle.text());
                    var groupDescription = $(this).find('.button_edit_item span').eq(0);
                    groupDescription.html(groupDescription.text());
                    $(this).show();
                });
                $('.filter_option_no_results').addClass('hidden');
                $('.option_list_pagination').show();
                $('.option_list_main').show();
            }
        }

        $('#filter_title').change(function(){
            do_filter_option();
        });
        $('#filter_title').click(function(){
            do_filter_option();
        });
        $('#filter_title').keyup(function(){
            do_filter_option();
        });

        $('#clear_filter').click(function(){
            $('#filter_title').val('');
            do_filter_option();
        });

        $('.button_delete_option_group').on('click',function(){
            var groupID = $(this).attr('group-id');
            MV.dialog.st_confirm('<?php echo __('delete_option_group_confirm'); ?><div class="space"></div><b>'+ $(this).attr('group-title') +'</b>',function(){
                MV.post(DOMAIN+'/admin/option/delete_group',{
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