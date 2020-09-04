<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('admin_callback_request'); ?></div>
        <div class="fr">
            <a class="mava_button mava_button_gray rma-mark-recall-new-to-read" href="javascript:void(0);"><i class="fa fa-check"></i> <?php echo __('mark_new_recall_to_read'); ?></a>
            <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/recall/export-new-recall'); ?>" target="_blank"><i class="fa fa-file-excel-o"></i> <?php echo __('export_new_recall_to_excel'); ?></a>
        </div>
    </div>
    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('created_date'); ?></th>
            <th><?php echo __('phone'); ?></th>
            <th><?php echo __('website_title'); ?></th>
            <th><?php echo __('status'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($recall) && is_array($recall) && count($recall) > 0){
            $count = 0;
            foreach($recall as $item){
                if($item['status'] == 'read'){
                    $label_class = 'info';
                }else if($item['status'] == 'done'){
                    $label_class = 'success';
                }else if($item['status'] == 'deleted'){
                    $label_class = 'danger';
                }else {
                    $label_class = 'primary';
                }
                $count++;
                echo '<tr class="'. ($count%2==0?'odd ':'') .'">
            <td>'. $count .'</td>
            <td title="'. __('ip_address') .': '. $item['created_ip'] .'">'. print_time($item['created_time']) .'</td>
            <td>'. htmlspecialchars($item['phone']) .'</td>
            <td><a href="'. htmlspecialchars($item['url']) .'" target="_blank">'. htmlspecialchars($item['title']) .'</a></td>
            <td width="120"><div class="btn-group">
                  <button data-label="'. __('recall_status_'. $item['status']) .'" data-class="btn-'. $label_class .'" class="btn btn-'. $label_class .' btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                    '. __('recall_status_'. $item['status']) .' <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="javascript:void(0);" class="change_status_btn" data-class="btn-primary" data-id="'. $item['id'] .'" data-status="new">'. __('recall_status_new') .'</a></li>
                    <li><a href="javascript:void(0);" class="change_status_btn" data-class="btn-info" data-id="'. $item['id'] .'" data-status="read">'. __('recall_status_read') .'</a></li>
                    <li><a href="javascript:void(0);" class="change_status_btn" data-class="btn-success" data-id="'. $item['id'] .'" data-status="done">'. __('recall_status_done') .'</a></li>
                    <li><a href="javascript:void(0);" class="change_status_btn" data-class="btn-danger" data-id="'. $item['id'] .'" data-status="deleted">'. __('recall_status_deleted') .'</a></li>
                  </ul>
                </div>
            </td>
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

        $('.change_status_btn').click(function(){
            var change_button = $(this).parents('.btn-group').find('button');
            var old_label = change_button.data('label');
            var old_class = change_button.data('class');

            change_button.removeClass('btn-default btn-primary btn-warning btn-danger btn-success btn-info').addClass($(this).data('class'));
            change_button.html($(this).text() +' <span class="caret"></span>');

            var id = $(this).data('id');
            var status = $(this).data('status');
            MV.post(DOMAIN +'/admin/recall/change-status',{
                id: id,
                status: status
            },function(res){
                if(res.status == 1){
                    change_button.removeClass('btn-default btn-primary btn-warning btn-danger btn-success btn-info').addClass(res.recall_class);
                    change_button.html(res.recall_label +' <span class="caret"></span>');
                    change_button.data('label',res.recall_label);
                    change_button.data('class',res.recall_class);
                }else{
                    change_button.removeClass('btn-default btn-primary btn-warning btn-danger btn-success btn-info').addClass(old_class);
                    change_button.html(old_label +' <span class="caret"></span>');
                }
            });
        });

        $('.rma-mark-recall-new-to-read').click(function(){
            MP.modal.confirm(__('mark_all_new_recall_to_read_confirm'),function(){
                MV.post(
                    DOMAIN + '/admin/recall/mark-read',
                    {},
                    function (res) {
                        MP.modal.confirm('hide');
                        if (res.status == 1) {
                            window.location.reload();
                        } else {
                            MV.show_notice(res.message,3);
                        }
                    }
                );
            });
        });
    });
</script>