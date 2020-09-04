<div class="container-fluid">
    <div class="mbd-box">
        <div class="mbd-head">
            <span class="mbd-head-title"><?php echo __('transaction_history') .' ('. number_format($total, 0, '.', ',') .')'. ($page > 1?' - '. __('page_x', array('num' => $page)):''); ?></span>
        </div>
        <div class="mbd-body">
            <table class="table">
                <thead>
                <tr>
                    <th width="150"><?php echo __('created_date'); ?></th>
                    <th width="200" class="text-right"><?php echo __('balance_before_transaction'); ?></th>
                    <th width="150" class="text-right"><?php echo __('transaction_amount'); ?></th>
                    <th width="200" class="text-right"><?php echo __('balance_after_transaction'); ?></th>
                    <th><?php echo __('reason'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(isset($transactions) && is_array($transactions) && count($transactions) > 0){
                    foreach($transactions as $item){
                        if($item['transaction_type'] == 'add'){
                            $item['transaction_amount'] = '+'. Mava_String::price_format($item['transaction_amount']);
                            $btn_class = 'success';
                        }else {
                            $btn_class = 'danger';
                            $item['transaction_amount'] = '-'. Mava_String::price_format($item['transaction_amount']);
                        }
                        echo '<tr>
                                <td>'. date('d/m/Y H:i:s',$item['created_date']) .'</td>
                                <td align="right">'. Mava_String::price_format($item['before_change']) .'</td>
                                <td align="right"><b class="text-'. $btn_class .'">'. $item['transaction_amount'] .'</b></td>
                                <td align="right">'. Mava_String::price_format($item['after_change']) .'</td>
                                <td><div class="alert alert-info">'. $item['reason'] .'</div></td>
                                </tr>';
                    }
                }else{
                    echo '<tr><td colspan="10"><div class="text-center alert alert-warning">'. __('no_transaction_found') .'</div></td></tr>';
                }
                ?>
                </tbody>
            </table>
            <div class="text-right">
                <?php
                echo Mava_View::buildPagination(
                    Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/transactions'),
                    ceil($total/$limit),
                    $page
                );
                ?>
            </div>
        </div>
    </div>
</div>