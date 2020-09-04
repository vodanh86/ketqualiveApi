<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title">
            <?php echo __('admin_option'); ?>
        </div>
        <?php if(is_debug()){ ?>
        <div class="fr">
            <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/option/add_group'); ?>">+ <?php echo __('add_option_group'); ?></a>
            <a class="ml5 mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/option/edit_group_sort'); ?>"><?php echo __('display_order'); ?></a>
        </div>
        <?php } ?>
    </div>
    <div class="mava_list_filter option_group_list">
        <div class="mava_list_filter_head clearfix">
            <div class="fl"><?php echo __('options'); ?></div>
            <div class="fr">
                <input type="search" id="filter_title" class="input_text input_small" results="10" placeholder="<?php echo __('filter_option_group'); ?>" />
                <a href="javascript:void(0);" id="clear_filter" class="mava_button mava_button_gray mava_button_small"><?php echo __('clear_filter'); ?></a>
            </div>
        </div>

        <ul class="list_items option_list_main">
            <?php
            if(isset($option_group) && sizeof($option_group) > 0){
                $count = 0;
                foreach($option_group as $item){
                    if($item['debug_only'] == 0 || is_debug()){
                    $count++;
                    echo '<li class="item">';
                    if(is_debug()){
                        echo '<a href="javascript:void(0);" group-id="'. $item['group_id'] .'" group-title="'. htmlspecialchars(__('_option_group_title_'. $item['group_id'])) .'" class="actionButton button_delete_item button_delete_option_group">'. __('delete_option_group') .'</a>';
                    }
                    echo '<h4><a href="'. Mava_Url::buildLink('admin/option/setting',array('groupID' => $item['group_id'])) .'" class="button_edit_item"><b>'. htmlspecialchars(__('_option_group_title_'. $item['group_id'])) .'</b><span class="item_description">'. htmlspecialchars(__('_option_group_description_'. $item['group_id'],array(),false)) .'</span></a></h4>
                        </li>';
                    }
                }
            }
            ?>
        </ul>
        <div class="filter_option_no_results hidden">
            <div class="no_filter_item_found"><?php echo __('no_option_group_found'); ?></div>
        </div>
        <div class="list_footer clearfix">
            <div class="list_row_stats">
                <?php
                echo __('show_x_y_in_z_record',array(
                    'start' => $skip+1,
                    'end' => $skip+sizeof($option_group),
                    'total' => number_format($total,0,',','.')
                ));
                ?>
            </div>
        </div>
        <?php
        echo Mava_View::buildPagination(
            Mava_Url::buildLink('admin/option/index'),
            $total_page,
            $page,
            $page_offset,
            'option_list_pagination'
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
        <?php if(is_debug()){ ?>
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
        <?php } ?>
    });
</script>