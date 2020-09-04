<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('admin_subscribes'); ?></div>
        <div class="fr">
            <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/subscribes/export-subscribe'); ?>" target="_blank"><i class="fa fa-file-excel-o"></i> <?php echo __('export_all_subscribe_to_excel'); ?></a>
        </div>
    </div>
    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('created_date'); ?></th>
            <th><?php echo __('email'); ?></th>
            <th><?php echo __('status'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($subscribes) && is_array($subscribes) && count($subscribes) > 0){
            $count = 0;
            foreach($subscribes as $item){
                if($item['status'] == 'unsubscribe'){
                    $label_class = 'danger';
                }else {
                    $label_class = 'success';
                }
                $count++;
                echo '<tr class="'. ($count%2==0?'odd ':'') .'">
            <td>'. $count .'</td>
            <td title="'. __('ip_address') .': '. $item['created_ip'] .'">'. print_time($item['created_time']) .'</td>
            <td>'. htmlspecialchars($item['email']) .'</td>
            <td width="120"><div class="btn-group">
                  <button data-label="'. __('subscribe_status_'. $item['status']) .'" data-class="btn-'. $label_class .'" class="btn btn-'. $label_class .' btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                    '. __('subscribe_status_'. $item['status']) .' <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="javascript:void(0);" class="change_status_btn" data-class="btn-success" data-id="'. $item['id'] .'" data-status="subscribe">'. __('subscribe_status_subscribe') .'</a></li>
                    <li><a href="javascript:void(0);" class="change_status_btn" data-class="btn-danger" data-id="'. $item['id'] .'" data-status="unsubscribe">'. __('subscribe_status_unsubscribe') .'</a></li>
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
            MV.post(DOMAIN +'/admin/subscribes/change-status',{
                id: id,
                status: status
            },function(res){
                if(res.status == 1){
                    change_button.removeClass('btn-default btn-primary btn-warning btn-danger btn-success btn-info').addClass(res.subscribe_class);
                    change_button.html(res.subscribe_label +' <span class="caret"></span>');
                    change_button.data('label',res.subscribe_label);
                    change_button.data('class',res.subscribe_class);
                }else{
                    change_button.removeClass('btn-default btn-primary btn-warning btn-danger btn-success btn-info').addClass(old_class);
                    change_button.html(old_label +' <span class="caret"></span>');
                }
            });
        });
    });
</script>