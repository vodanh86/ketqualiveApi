<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('admin_language'); ?></div>
        <?php if(is_debug()){ ?>
        <a class="fr mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/language/add'); ?>">+ <?php echo __('add_language'); ?></a>
        <?php } ?>
    </div>
    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('language_title'); ?></th>
            <th><?php echo __('language_code'); ?></th>
            <th colspan="3">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        echo '<tr>
            <td>1</td>
            <td>'. __('master_language') .'</td>
            <td>&nbsp;</td>
            <td width="100"><a href="'. Mava_Url::buildLink('admin/phrase/index',array('languageID' => 0)) .'" class="hover">'. __('phrase') .'</a></td>
        </tr>';
        if(isset($languages) && sizeof($languages) > 0){
            $count = 1;
            foreach($languages as $item){
                $count++;
                echo '<tr'. ($count%2==0?' class="odd"':'') .'>
            <td>'. $count .'</td>
            <td>'. htmlspecialchars($item['title']) .'</td>
            <td>'. htmlspecialchars($item['language_code']) .'</td>
            <td width="50"><a href="'. Mava_Url::buildLink('admin/phrase/index',array('languageID' => $item['language_id'])) .'">'. __('phrase') .'</a></td>';
            if(is_debug()){
                echo '<td width="50"><a href="'. Mava_Url::buildLink('admin/language/edit',array('languageID' => $item['language_id'])) .'">'. __('edit') .'</a></td>
                    <td width="50"><a href="javascript:void(0);" language-id="'. $item['language_id'] .'" language-title="'. htmlspecialchars($item['title']) .'" language-code="'. htmlspecialchars($item['language_code']) .'" class="button_delete_language">'. __('delete') .'</a></td>';
            }
        echo '</tr>';
            }
        }
        ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.button_delete_language').on('click',function(){
            var languageID = $(this).attr('language-id');
            var languageTitle = $(this).attr('language-title');
            var languageCode = $(this).attr('language-code');
            MV.dialog.st_confirm('<?php echo __('delete_language_confirm'); ?><div class="space"></div><b>'+ languageTitle +' ('+ languageCode +')</b>',function(){
                MV.post(DOMAIN+'/admin/language/delete',{
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