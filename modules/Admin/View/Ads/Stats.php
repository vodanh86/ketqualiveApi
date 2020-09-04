<div class="container-fluid">
    <div class="clearfix">
        <h3 class="pull-left"><?php echo __('statistics'); ?></h3>
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
    <h3><a href="<?php echo Mava_Url::getPageLink('admin/ads/campaigns'); ?>"><?php echo __('ads_campaign'); ?></a></h3>
    <table class="table">
        <thead>
        <tr>
            <th><?php echo __('code'); ?></th>
            <th><?php echo __('name'); ?></th>
            <th><?php echo __('click'); ?></th>
            <th><?php echo __('order'); ?></th>
            <th><?php echo __('revenue'); ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $count = 0;
        if(isset($campaigns) && is_array($campaigns) && count($campaigns) > 0){
            foreach($campaigns as $item){
                if($item['group_color'] == ''){
                    $item['group_color'] = '#999999';
                }

                if($item['group_title'] == ''){
                    $item['group_title'] = __('ungrouped');
                }
                $count++;
                echo '<tr class="'. ($count%2==0?'odd':'') .'">
            <td>'. $item['id'] .'</td>
            <td>
                <b style="color: '. $item['group_color'] .';">[ '. htmlspecialchars($item['group_title']) .' ]</b> <a href="'. Mava_Url::getPageLink('admin/ads/view-campaign', array('id' => $item['id'])) .'">'. $item['title'] .'</a> '. ($item['note']!=""?'<a href="javascript:void(0);" class="mb-ads-note-toggle"><i class="fa fa-file-text"></i></a>':'') .'
                <div class="mb-ads-note well">'. nl2br(htmlspecialchars($item['note'])) .'</div>
                </td>
            <td>'. $item['click_count'] .'</td>
            <td>'. $item['order_count'] .'</td>
            <td>'. Mava_String::price_format($item['total_revenue']) .'</td>
            <td width="100" align="center"><i class="fa fa-line-chart"></i> <a href="'. Mava_Url::buildLink('admin/ads/view-campaign',array('id' => $item['id'])) .'">'. __('statistics') .'</a></td>
        </tr>';
            }
        }else{
            echo '<tr><td>'. __('no_campaign_found') .'</td></tr>';
        }
        ?>
        </tbody>
    </table>
    <div class="text-right">
        <a href="<?php echo Mava_Url::getPageLink('admin/ads/campaigns'); ?>" class="btn btn-info"><i class="fa fa-caret-right"></i> <?php echo __('view_all_campaign'); ?></a>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var base_statistic_url = '<?php echo Mava_Url::getPageLink('admin/ads/index'); ?>';
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
                window.location.href = base_statistic_url +'?start_date='+ start.format('DD/MM/YYYY') +'&end_date='+ end.format('DD/MM/YYYY');
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