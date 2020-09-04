<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('admin_qa'); ?></div>
    </div>
    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('question'); ?></th>
            <th><?php echo __('name'); ?></th>
            <th><?php echo __('email'); ?></th>
            <th><?php echo __('phone'); ?></th>
            <th><?php echo __('status'); ?></th>
            <th><?php echo __('sort_order'); ?></th>
            <th colspan="4">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($questions) && is_array($questions) && count($questions) > 0){
            $count = 0;
            foreach($questions as $item){
                if($item['status'] == 'answered'){
                    $label_class = 'success';
                }else if($item['status'] == 'deleted'){
                    $label_class = 'danger';
                }else {
                    $label_class = 'primary';
                }
                $count++;
                echo '<tr class="'. ($count%2==0?'odd ':'') .'">
            <td>'. $count .'</td>
            <td><p><b>'. htmlspecialchars($item['question']) .'</b></p>'. ($item['answer']!=""?htmlspecialchars($item['answer']):'') .'</td>
            <td>'. htmlspecialchars($item['name']) .'</td>
            <td>'. htmlspecialchars($item['email']) .'</td>
            <td>'. htmlspecialchars($item['phone']) .'</td>
            <td><span class="label label-'. $label_class .'">'. __('question_status_'. $item['status']) .'</span></td>
            <td>'. $item['sort_order'] .'</td>
            '. ($item['status'] != 'deleted'?'<td width="50" align="center"><a href="'. Mava_Url::buildLink('admin/qa/edit',array('id' => $item['id'])) .'">'. __('edit') .'</a></td>
            <td width="50" align="center"><a href="javascript:void(0);" item-id="'. $item['id'] .'" item-title="'. htmlspecialchars($item['question']) .'" class="button_delete_question">'. __('delete') .'</a></td>':'<td></td><td></td>') .'
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
        $('.button_delete_question').on('click',function(){
            var itemID = $(this).attr('item-id');
            var itemTitle = $(this).attr('item-title');
            MV.dialog.st_confirm('<?php echo __('delete_question_confirm'); ?><div class="space"></div><b>'+ itemTitle +' (ID: '+ itemID +')</b>',function(){
                MV.post(DOMAIN+'/admin/qa/delete',{
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