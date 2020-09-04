<div class="container-fluid">
    <?php
    if($agency['status'] == Megabook_DataWriter_Agency::STATUS_PENDING){
        echo '<div class="alert alert-danger mbd-top-alert">'. __('agency_wait_approve_notice') .'</div>';
    }
    ?>
    <div class="mbd-box">
        <div class="mbd-body">
            <div class="row mbd-income-stats">
                <div class="col-md-4">
                    <p><?php echo __('total_income'); ?></p>
                    <b><?php echo Mava_String::price_format($total_income); ?></b>
                </div>
                <div class="col-md-4">
                    <p><?php echo __('withdraw_total_amount'); ?></p>
                    <b><?php echo Mava_String::price_format($withdraw_total_amount); ?></b>
                </div>
                <div class="col-md-4">
                    <p><?php echo __('current_balance'); ?></p>
                    <p><b><?php echo Mava_String::price_format($agency['balance']); ?></b></p>
                    <?php
                        $minWithdraw = Mava_Application::getOptions()->affiliateWithdrawMinimum;
                        if($agency['balance'] >= $minWithdraw){
                            echo '<a href="'. Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/new-withdraw-request') .'" class="btn btn-success btn-sm">'. __('send_withdraw_request') .'</a>';
                        }else{
                            echo '<a href="javascript:void(0);" class="btn btn-success btn-sm" data-toggle="tooltip" title="'. __('withdraw_minimum_x', array('num' => Mava_String::price_format($minWithdraw))) .'">'. __('send_withdraw_request') .'</a>';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="mbd-box">
        <div class="mbd-head">
            <span class="mbd-head-title"><?php echo __('withdraw_request') .' ('. number_format($total, 0, '.', ',') .')'. ($page > 1?' - '. __('page_x', array('num' => $page)):''); ?></span>
        </div>
        <div class="mbd-body">
            <table class="table">
                <thead>
                <tr>
                    <th><?php echo __('withdraw_code'); ?></th>
                    <th><?php echo __('withdraw_amount'); ?></th>
                    <th><?php echo __('created_date'); ?></th>
                    <th><?php echo __('created_by'); ?></th>
                    <th><?php echo __('status'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(isset($requests) && is_array($requests) && count($requests) > 0){
                    foreach($requests as $item){
                        if($item['status'] == 'reviewed'){
                            $label_class = 'warning';
                        }else if($item['status'] == 'reject'){
                            $label_class = 'danger';
                        }elseif($item['status'] == 'paid'){
                            $label_class = 'success';
                        }else {
                            $label_class = 'info';
                        }
                        echo '<tr>
                        <td>'. $item['id'] .'</td>
                        <td class="text-success">'. Mava_String::price_format($item['amount']) .'</td>
                        <td>'. date('d/m/Y H:i:s', $item['created_date']) .'</td>
                        <td>'. htmlspecialchars($item['created_by']) .' (UID: '. $item['user_id'] .')</td>
                        <td><label class="label label-'. $label_class .'">'. __('withdraw_request_status_'. $item['status']) .'</label>'. ($item['status']==Megabook_DataWriter_WithdrawRequest::STATUS_REJECT?"<br/><br/><div class='alert alert-danger'>". htmlspecialchars($item['reject_reason']) ."</div>":"") .'</td>
                        </tr>';
                    }
                }else{
                    echo '<tr><td colspan="10"><div class="text-center alert alert-warning">'. __('no_withdraw_request_found') .'</div></td></tr>';
                }
                ?>
                </tbody>
            </table>
            <div class="text-right">
                <?php
                echo Mava_View::buildPagination(
                    Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/withdraw'),
                    ceil($total/$limit),
                    $page
                );
                ?>
            </div>
        </div>
    </div>
</div>