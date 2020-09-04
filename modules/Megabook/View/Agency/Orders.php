<div class="container-fluid">
    <div class="mbd-box">
        <div class="mbd-head">
            <span class="mbd-head-title"><?php echo __('orders') .' ('. number_format($total, 0, '.', ',') .')'. ($page > 1?' - '. __('page_x', array('num' => $page)):''); ?></span>
        </div>
        <div class="mbd-body">
            <table class="table">
                <thead>
                <tr>
                    <th><?php echo __('order_date'); ?></th>
                    <th><?php echo __('order_id'); ?></th>
                    <th><?php echo __('buyer'); ?></th>
                    <th><?php echo __('quantity'); ?></th>
                    <th><?php echo __('total_amount'); ?></th>
                    <th><?php echo __('commission'); ?></th>
                    <th><?php echo __('status'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(isset($orders) && is_array($orders) && count($orders) > 0){
                    $count = ($page-1)*$limit;
                    $commissionPercent = Mava_Application::getOptions()->affiliateCommission;
                    foreach($orders as $item){
                        if($item['status'] == 'cancelled'){
                            $btn_class = 'danger';
                        }else if($item['status'] == 'read'){
                            $btn_class = 'info';
                        }else if($item['status'] == 'processing'){
                            $btn_class = 'warning';
                        }else if($item['status'] == 'done'){
                            $btn_class = 'success';
                        }else {
                            $btn_class = 'primary';
                        }
                        $commission = ceil((($item['total_amount']-$item['gift_code_value']) * $commissionPercent)/100);
                        $count++;
                        echo '<tr>
                                <td>'. date('d/m/Y H:i:s',$item['created_time']) .'</td>
                                <td>'. $item['id'] .'</td>
                                <td>'. htmlspecialchars($item['fullname']) .'</td>
                                <td>'. number_format($item['quantity'],0) .'</td>
                                <td>'. Mava_String::price_format($item['total_amount']-$item['gift_code_value']) .'</td>
                                <td>'. Mava_String::price_format($commission) .'</td>
                                <td><span class="label label-'. $btn_class .'">'. __('order_status_'. $item['status']) .'</span></td>
                                </tr>';
                    }
                }else{
                    echo '<tr><td colspan="10"><div class="text-center alert alert-warning">'. __('agency_no_order_found') .'</div></td></tr>';
                }
                ?>
                </tbody>
            </table>
            <div class="text-right">
                <?php
                echo Mava_View::buildPagination(
                    Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/orders'),
                    ceil($total/$limit),
                    $page
                );
                ?>
            </div>
        </div>
    </div>
</div>