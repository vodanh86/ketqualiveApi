<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
            echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title">
            <?php echo htmlspecialchars($current_language['title']) .': '. __('phrase'); ?>
            <div class="phrase_quick_change st_ui_dropdown">
                <a href="javascript:void(0);" class="label"><?php echo __('see_phrase_in_other_language'); ?></a>
                <div class="list">
                    <a href="<?php echo Mava_Url::buildLink('admin/phrase/index',array('languageID' => 0)); ?>" class="item"><?php echo __('master_language') .': '. __('phrase'); ?></a>
                    <?php
                    foreach($languages as $lang){
                        echo '<a href="'. Mava_Url::buildLink('admin/phrase/index',array('languageID' => $lang['language_id'])) .'" class="item">'. htmlspecialchars($lang['title']) .': '. __('phrase') .'</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <a class="fr mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/phrase/add',array('languageID' => $language_id)); ?>">+ <?php echo __('add_phrase'); ?></a>
    </div>
    <div class="mava_list_filter phrase_list">
        <div class="mava_list_filter_head clearfix">
            <div class="fl"><?php echo __('phrase'); ?></div>
            <div class="fr">
                <input type="hidden" id="filter_language_id" value="<?php echo (int)$language_id; ?>" />
                <input type="search" id="filter_title" placeholder="<?php echo __('filter_phrase'); ?>" class="input_text input_small" results="10" />
                <input type="checkbox" id="prefix_match" value="1" />
                <label for="prefix_match"><?php echo __('prefix_match'); ?></label>
                <a href="javascript:void(0);" id="clear_filter" class="mava_button mava_button_gray mava_button_small"><?php echo __('clear_filter'); ?></a>
            </div>
        </div>

        <ul class="list_items phrase_list_main">
            <?php
            if(isset($phrases) && sizeof($phrases) > 0){
                $count = 0;
                foreach($phrases as $item){
                    $count++;
                    echo '<li class="item item_'. $item['phrase_state'] .'">
                                '. ($item['canDelete']==1?'<a href="javascript:void(0);" phrase-id="'. $item['phrase_id'] .'" phrase-title="'. htmlspecialchars($item['title']) .'" class="actionButton button_delete_item button_delete_phrase"'. ($item['map_language_id']>0?' title="'. __('revert_phrase') .'">'. __('revert_phrase'):' title="'. __('delete_phrase') .'">'. __('delete_phrase')) .'</a>':'') .'
                                <h4><a class="button_edit_phrase" href="'. Mava_Url::buildLink('admin/phrase/edit',array('phraseID' => $item['phrase_id'],'languageID' => $language_id)) .'">'. htmlspecialchars($item['title']) .'</a></h4>
                                </li>';
                }
            }
            ?>
        </ul>
        <div class="filter_phrase_results hidden">

        </div>
        <div class="list_footer clearfix">
            <div class="list_row_stats">
                <?php
                    echo __('show_x_y_in_z_record',array(
                        'start' => $skip+1,
                        'end' => $skip+sizeof($phrases),
                        'total' => number_format($total,0,',','.')
                    ));
                ?>
            </div>
        </div>
        <?php
            echo Mava_View::buildPagination(
                Mava_Url::buildLink('admin/phrase/index',array('languageID' => $language_id)),
                $total_page,
                $page,
                $page_offset,
                'phrase_list_pagination'
            );
        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        if(MV.cookie('phrase_filter_prefix')!=undefined && parseInt(MV.cookie('phrase_filter_prefix'))==1){
            $('#prefix_match').attr('checked','checked');
        }else{
            $('#prefix_match').removeAttr('checked');
        }
        if(MV.cookie('phrase_filter_term')!=undefined && MV.cookie('phrase_filter_term')!=""){
            $('#filter_title').val(MV.cookie('phrase_filter_term'));
            do_filter_phrase();
        }
        function do_filter_phrase(){
            MV.cookie('phrase_filter_term',$('#filter_title').val());
            MV.cookie('phrase_filter_prefix',$('#prefix_match').is(':checked')?1:0);
            if($('#filter_title').val().trim()!=''){
                MV.post(DOMAIN +'/admin/phrase/filter_phrase',{
                    filterTitle: $('#filter_title').val(),
                    prefixMatch: $('#prefix_match').is(':checked')?1:0,
                    languageID: $('#filter_language_id').val()
                },function(res){
                    if(res.status==1){
                        $('.filter_phrase_results').html(res.phraseHTML);
                        $('.filter_phrase_results').removeClass('hidden');
                        $('.phrase_list_pagination').hide();
                        $('.phrase_list_main').hide();
                    }
                });
            }else{
                $('.filter_phrase_results').html('');
                $('.filter_phrase_results').addClass('hidden');
                $('.phrase_list_pagination').show();
                $('.phrase_list_main').show();
            }
        }

        $('#filter_title').change(function(){
            do_filter_phrase();
        });
        $('#filter_title').click(function(){
            do_filter_phrase();
        });
        $('#filter_title').keyup(function(){
            do_filter_phrase();
        });
        $('#prefix_match').click(function(){
            do_filter_phrase();
        });

        $('#clear_filter').click(function(){
            $('#filter_title').val('');
            do_filter_phrase();
        });

        $('.button_delete_phrase').on('click',function(){
            var phraseID = $(this).attr('phrase-id');
            var languageID = $('#filter_language_id').val();
            MV.dialog.st_confirm('<?php echo __('delete_phrase_confirm'); ?><div class="space"></div><b>'+ $(this).attr('phrase-title') +'</b>',function(){
                MV.post(DOMAIN+'/admin/phrase/delete',{
                    phraseID: phraseID,
                    languageID: languageID
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