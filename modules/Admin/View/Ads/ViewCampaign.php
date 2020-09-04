<?php
if($campaign['group_color'] == ''){
    $campaign['group_color'] = '#999999';
}

if($campaign['group_title'] == ''){
    $campaign['group_title'] = __('ungrouped');
}
?>
<div class="page_width">
    <input type="hidden" id="campaign_id" value="<?php echo $campaign['id']; ?>" />
    <div class="admin_breadcrumbs">
        <?php echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):''); ?>
    </div>
    <h3><?php echo '<b style="color: '. $campaign['group_color'] .';">[ '. htmlspecialchars($campaign['group_title']) .' ]</b> '. htmlspecialchars($campaign['title']); ?></h3>
    <?php
        if($campaign['deleted'] == 'yes'){
            echo '<div class="alert alert-danger">'. __('campaign_has_been_deleted') .'</div>';
        }
    ?>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#cp_statistic" data-toggle="tab"><?php echo __('statistics'); ?></a></li>
        <li><a href="#cp_information" data-toggle="tab"><?php echo __('information_and_link'); ?></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="cp_statistic">
            <div class="clearfix">
                <div class="pull-left">
                    <div class="form-inline mb-stats-filter-source">
                        <?php echo __('source'); ?>:
                        <select class="form-control" id="stats_filter_link">
                            <option value="0"><?php echo __('all_link'); ?></option>
                            <?php
                                if(isset($links) && is_array($links) && count($links) > 0){
                                    foreach($links as $item){
                                        echo '<option value="'. $item['id'] .'"'. ($item['id']==$link_id?' selected':'') .'>'. htmlspecialchars($item['url']) .'</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="pull-right">
                    <div class="mb-ads-stats-range">
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon" id="sizing-addon3"><i class="fa fa-calendar"></i></span>
                            <input type="text" class="form-control" id="date_ranger" />
                        </div>
                    </div>
                </div>
            </div>
            <div id="container"></div>
            <div class="mb-ads-order-list">
                <h4><?php echo __('orders') .' ('. $total_order .')'; ?></h4>
                <table class="table">
                    <tr>
                        <th><?php echo __('order_time'); ?></th>
                        <th><?php echo __('order_id'); ?></th>
                        <th><?php echo __('order_by'); ?></th>
                        <th><?php echo __('product_count'); ?></th>
                        <th><?php echo __('total_amount'); ?></th>
                        <th></th>
                    </tr>
                <?php
                    if(isset($order_lists) && is_array($order_lists) && count($order_lists) > 0){
                        foreach($order_lists as $item){
                            echo '<tr>
                                <td>'. date('d/m/Y H:i', $item['order_time']) .'</td>
                                <td>'. $item['order_id'] .'</td>
                                <td>'. ($item['megabook_user_id'] > 0?'<a href="'. Mava_Url::getPageLink('admin/users/detail', array('userID' => $item['megabook_user_id'])) .'" target="_blank">'. htmlspecialchars($item['megabook_user_name']) .'</a>':htmlspecialchars($item['megabook_user_name'])) .'</td>
                                <td>'. $item['item_count'] .'</td>
                                <td>'. Mava_String::price_format($item['total_amount']) .'</td>
                                <td><a href="'. Mava_Url::getPageLink('admin/products/orders', array('q' => $item['order_id'])) .'#'. $item['order_id'] .'" target="_blank">'. __('view_order_detail') .' <i class="fa fa-external-link"></i></a></td>
                                </tr>';
                        }
                    }else{
                        echo '<tr><td colspan="10"><div class="alert alert-warning">'. __('no_order_found') .'</div></td></tr>';
                    }
                ?>
                </table>
                <?php
                    $paginateParams = array(
                        'id' => $campaign['id'],
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                    );
                    echo Mava_View::buildPagination(
                        Mava_Url::buildLink('admin/ads/view-campaign',$paginateParams),
                        ceil($total_order/$limit),
                        $page,
                        3,
                        'post_list_pagination'
                    );
                ?>
            </div>
        </div>
        <div class="tab-pane" id="cp_information">
            <table class="table">
                <tr>
                    <td width="250"><?php echo __('code'); ?></td>
                    <td><?php echo $campaign['id']; ?></td>
                </tr>
                <tr>
                    <td><?php echo __('click'); ?></td>
                    <td><?php echo number_format($campaign['click_count'],0,',','.'); ?></td>
                </tr>
                <tr>
                    <td><?php echo __('order'); ?></td>
                    <td><?php echo number_format($campaign['order_count'],0,',','.'); ?></td>
                </tr>
                <tr>
                    <td><?php echo __('revenue'); ?></td>
                    <td><?php echo Mava_String::price_format($campaign['total_revenue']); ?></td>
                </tr>
                <tr>
                    <td><?php echo __('note'); ?></td>
                    <td><div class="well"><?php echo htmlspecialchars($campaign['note']); ?></div></td>
                </tr>
                <?php if($campaign['deleted'] == 'no'){ ?>
                    <tr>
                        <td></td>
                        <td><a href="<?php echo Mava_Url::getPageLink('admin/ads/edit-campaign', array('id' => $campaign['id'])); ?>" class="btn btn-primary"><i class="fa fa-edit"></i> <?php echo __('edit_campaign'); ?></a></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><?php echo __('links'); ?></td>
                    <td>
                        <table class="table mbc-link-list">
                            <tr>
                                <th><?php echo __('note'); ?></th>
                                <th><?php echo __('link'); ?></th>
                                <th><?php echo __('click'); ?></th>
                                <th><?php echo __('order'); ?></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <?php
                                if(isset($links) && is_array($links) && count($links) > 0){
                                    foreach($links as $item){
                                        echo '<tr class="link_'. $item['id'] .'" data-id="'. $item['id'] .'">
                                <td width="200">'. htmlspecialchars($item['note']) .'</td>
                                <td><input type="text" class="disabled form-control" readonly="true" value="'. Mava_Url::addParam($item['url'], array('_mcid' => $campaign['id'])) .'" onclick="$(this).select();"/></i></a></td>
                                <td width="50">'. $item['click_count'] .'</td>
                                <td width="80">'. $item['order_count'] .'</td>
                                <td width="20"><a href="javascript:void(0);" class="btn btn-default mb-edit"><i class="fa fa-edit"></td>
                                <td width="20"><a href="javascript:void(0);" class="btn btn-default mb-delete"><i class="fa fa-trash"></td>
                            </tr>';
                                    }
                                }
                            ?>

                        </table>
                        <a href="javascript:void(0);" class="btn btn-success mbc-add-campaign-link"><i class="fa fa-plus"></i> <?php echo __('add_link'); ?></a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var base_statistic_url = '<?php echo Mava_Url::getPageLink('admin/ads/view-campaign', array('id' => $campaign['id'])); ?>';

        // filter
        $('#stats_filter_link').change(function(){
            window.location.href = base_statistic_url +'&link_id='+ $(this).val();
        });
        // edit link
        $('.mbc-link-list').on('click','.mb-edit', function(){
            var link_id = parseInt($(this).parents('tr').attr('data-id'));
            if(link_id > 0 && link_id.toString() != 'NaN'){
                MP.notice.show(__('loading'),'warning');
                MV.post(DOMAIN +'/admin/ads/get-campaign-link-info', {
                    link_id: link_id
                }, function(res){
                    MP.notice.hide();
                    if(res.status == 1){
                        var edit_campaign_link_html = '<div id="edit_campaign_link_error"></div>' +
                            '<div class="form-group">' +
                            '<label class="control-label" for="edit_campaign_link_url">'+ __('link') +' <b>(*)</b></label>' +
                            '<input type="text" class="form-control disabled" id="edit_campaign_link_url" readonly="true" placeholder="http://" value="'+ res.link.url +'" />' +
                            '</div>' +
                            '<div class="form-group">' +
                            '<label class="control-label" for="edit_campaign_link_note">'+ __('note') +'</label>' +
                            '<input type="text" class="form-control" id="edit_campaign_link_note" value="'+ res.link.note +'" />' +
                            '</div>';
                        MP.modal.show({
                            id: 'edit_campaign_link_modal',
                            title: __('edit_link'),
                            size: 'md',
                            type: 'html',
                            html: edit_campaign_link_html,
                            init_callback: function(){
                            },
                            ok_callback: function(){
                                $('#edit_campaign_link_error').html('');
                                if($('#edit_campaign_link_url').val() == '' || !MV.string.isURL($('#edit_campaign_link_url').val())){
                                    $('#edit_campaign_link_url').focus();
                                }else{
                                    MV.post(DOMAIN +'/admin/ads/edit-campaign-link', {
                                        link_id: link_id,
                                        note: $('#edit_campaign_link_note').val()
                                    }, function(res){
                                        if(res.status == 1){
                                            var new_link = $('<tr class="link_'+ res.link.id +'" data-id="'+ res.link.id +'">' +
                                                '<td width="200">'+ res.link.note +'</td>' +
                                                '<td><input type="text" class="disabled form-control" readonly="true" value="'+ res.link.url +'" onclick="$(this).select();"/></i></a></td>' +
                                                '<td width="50">'+ res.link.click_count +'</td>' +
                                                '<td width="80">'+ res.link.order_count +'</td>' +
                                                '<td width="20"><a href="javascript:void(0);" class="btn btn-default mb-edit"><i class="fa fa-edit"></td>' +
                                                '<td width="20"><a href="javascript:void(0);" class="btn btn-default mb-delete"><i class="fa fa-trash"></td>' +
                                                '</tr>');
                                            $('.link_'+ link_id).html(new_link.html());
                                            MP.notice.show(res.message,'success',2);
                                            $('#edit_campaign_link_modal').modal('hide');
                                        }else{
                                            $('#edit_campaign_link_error').html('<div class="alert alert-danger alert-dismissable">'+ res.message +'<button type="button" class="close" data-dismiss="alert" aria-label="'+ __('close') +'"><span aria-hidden="true">&times;</span></button></div>');
                                        }
                                    });
                                }
                            },
                            ok_title: __('save'),
                            cancel_title: __('close')
                        });
                    }else{
                        MP.notice.show(res.message,'danger', 3);
                    }
                });
            }
        });
        // delete link


        $('.mbc-link-list').on('click','.mb-delete', function(){
            var link_id = parseInt($(this).parents('tr').attr('data-id'));
            MP.modal.confirm(__('delete_campaign_link'), function(){
                MV.post(DOMAIN +'/admin/ads/delete-campaign-link', {
                    link_id: link_id
                }, function(res){
                    $('#confirm_modal').modal('hide');
                    if(res.status == 1){
                        $('.link_'+ link_id).remove();
                        MP.notice.show(res.message, 'success', 2);
                    }else{
                        MP.notice.show(res.message, 'danger', 3);
                    }
                });
            });
        });

        // add link
        $('.mbc-add-campaign-link').click(function(){
            var add_campaign_link_html = '<div id="add_campaign_link_error"></div>' +
                '<div class="form-group">' +
                '<label class="control-label" for="add_campaign_link_url">'+ __('link') +' <b>(*)</b></label>' +
                '<input type="text" class="form-control" id="add_campaign_link_url" placeholder="http://" />' +
                '</div>' +
                '<div class="form-group">' +
                '<label class="control-label" for="add_campaign_link_note">'+ __('note') +'</label>' +
                '<input type="text" class="form-control" id="add_campaign_link_note" />' +
                '</div>';
            MP.modal.show({
                id: 'add_campaign_link_modal',
                title: __('add_link'),
                size: 'md',
                type: 'html',
                html: add_campaign_link_html,
                init_callback: function(){
                    $('#add_campaign_link_url').focus();
                },
                ok_callback: function(){
                    $('#add_campaign_link_error').html('');
                    if($('#add_campaign_link_url').val() == '' || !MV.string.isURL($('#add_campaign_link_url').val())){
                        $('#add_campaign_link_url').focus();
                    }else{
                        MV.post(DOMAIN +'/admin/ads/add-campaign-link', {
                            campaign_id: $('#campaign_id').val(),
                            url: $('#add_campaign_link_url').val(),
                            note: $('#add_campaign_link_note').val()
                        }, function(res){
                            if(res.status == 1){
                                var new_link = '<tr class="link_'+ res.link.id +'" data-id="'+ res.link.id +'">' +
                                    '<td width="200">'+ res.link.note +'</td>' +
                                    '<td><input type="text" class="disabled form-control" readonly="true" value="'+ res.link.url +'" onclick="$(this).select();"/></i></a></td>' +
                                    '<td width="50">'+ res.link.click_count +'</td>' +
                                    '<td width="80">'+ res.link.order_count +'</td>' +
                                    '<td width="20"><a href="javascript:void(0);" class="btn btn-default mb-edit"><i class="fa fa-edit"></td>' +
                                    '<td width="20"><a href="javascript:void(0);" class="btn btn-default mb-delete"><i class="fa fa-trash"></td>' +
                                    '</tr>';
                                $('.mbc-link-list').append(new_link);
                                $('#add_campaign_link_url').val('');
                                $('#add_campaign_link_note').val('');
                                MP.notice.show(res.message,'success',2);
                                $('#add_campaign_link_modal').modal('hide');
                            }else{
                                $('#add_campaign_link_error').html('<div class="alert alert-danger alert-dismissable">'+ res.message +'<button type="button" class="close" data-dismiss="alert" aria-label="'+ __('close') +'"><span aria-hidden="true">&times;</span></button></div>');
                            }
                        });
                    }
                },
                ok_title: __('save'),
                cancel_title: __('close')
            });
        });
        var start_date = '<?php echo $start_date; ?>';
        var end_date = '<?php echo $end_date; ?>';
        $('#date_ranger').daterangepicker(
            {
                locale: {
                    format: 'DD/MM/YYYY'
                },
                startDate: start_date,
                endDate: end_date,
                ranges: {
                    '<?php echo __('today'); ?>': [moment(), moment()],
                    '<?php echo __('yesterday'); ?>': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '<?php echo __('last_7_days'); ?>': [moment().subtract(6, 'days'), moment()],
                    '<?php echo __('last_30_days'); ?>': [moment().subtract(29, 'days'), moment()],
                    '<?php echo __('this_month'); ?>': [moment().startOf('month'), moment().endOf('month')]
                },
                "opens": "left"
            },
            function(start, end, label) {
                window.location.href = base_statistic_url +'&start_date='+ start.format('DD/MM/YYYY') +'&end_date='+ end.format('DD/MM/YYYY');
            }
        );
        $('.mb-ads-note-toggle').click(function(){
            $(this).parents('tr').find('.mb-ads-note').toggle();
        });
    });
    $(function () {
        Highcharts.chart('container', {
            title: {
                text: '',
                x: -20 //center
            },
            xAxis: {
                categories: <?php echo json_encode($xAxis); ?>
            },
            credits: {
                enabled: false
            },
            yAxis: {
                title: {
                    text: '<?php echo __('quantity'); ?>'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [{
                name: '<?php echo __('click_label_x', array('num' => $total_click)); ?>',
                data: <?php echo json_encode($clicks); ?>
            },{
                name: '<?php echo __('order_label_x', array('num' => $total_order)); ?>',
                data: <?php echo json_encode($orders); ?>
            }]
        });
    });
</script>