<?php
    $agency = Mava_Application::get('agency');
?>
<div class="container-fluid">
    <?php
        if($agency['status'] == Megabook_DataWriter_Agency::STATUS_PENDING){
            echo '<div class="alert alert-info mbd-top-alert">'. __('agency_wait_approve_notice') .'</div>';
        }elseif($agency['status'] == Megabook_DataWriter_Agency::STATUS_SUSPENDED){
            echo '<div class="alert alert-danger mbd-top-alert">'. __('agency_suspended_notice') .'</div>';
        }elseif($agency['status'] == Megabook_DataWriter_Agency::STATUS_DELETED){
            echo '<div class="alert alert-danger mbd-top-alert">'. __('agency_deleted_notice') .'</div>';
        }
    ?>
    <div class="row">
        <div class="col-md-4">
            <div class="mbd-mini-box">
                <a href="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/links'); ?>">
                    <span class="mbd-icon-circle mbd-icon-orange"><i class="fa fa-eye"></i></span>
                    <b class="mbd-text-large"><?php echo number_format($visitor_count, 0, ',', '.'); ?></b>
                    <span class="mbd-text-small"><?php echo __('page_view'); ?></span>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mbd-mini-box">
                <a href="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/orders'); ?>">
                    <span class="mbd-icon-circle mbd-icon-blue"><i class="fa fa-cart-plus"></i></span>
                    <b class="mbd-text-large"><?php echo number_format($order_count, 0, ',', '.') .'/'. number_format($total_order_count, 0, ',', '.'); ?></b>
                    <span class="mbd-text-small"><?php echo __('finished_orders'); ?></span>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mbd-mini-box">
                <a href="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/transactions'); ?>">
                    <span class="mbd-icon-circle mbd-icon-green"><i class="fa fa-usd"></i></span>
                    <b class="mbd-text-large"><?php echo Mava_String::price_format($revenue_total); ?></b>
                    <span class="mbd-text-small"><?php echo __('commission'); ?></span>
                </a>
            </div>
        </div>
    </div>

    <div class="mbd-box">
        <div class="mbd-body">
            <div class="row">
                <div class="col-md-9">
                    <div class="form-group">
                        <label class="control-label"><?php echo __('reference_url'); ?> <span data-toggle="tooltip" title="<?php echo __('reference_url_tooltip'); ?>"><i class="fa fa-question-circle"></i></span></label>
                        <input type="text" class="form-control" value="<?php echo Mava_Url::getPageLink('', array('arc' => $agency['agency_code'])); ?>" readonly="true" onclick="$(this).select();" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">&nbsp;</label>
                        <a href="javascript:void(0);" class="btn btn-info btn-block" id="mbd_create_reference_url"><?php echo __('create_reference_url'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="mbd-box">
                <div class="mbd-head clearfix">
                    <h3 class="mbd-head-title pull-left"><i class="fa fa-cart-plus"></i> <?php echo __('recent_orders'); ?></h3>
                    <?php
                        if(isset($orders) && is_array($orders) && count($orders) > 0){
                            echo '<a class="pull-right" href="'. Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/orders') .'">'. __('view_all') .' <i class="fa fa-caret-right"></i></a>';
                        }
                    ?>
                </div>
                <div class="mbd-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th><?php echo __('order_date'); ?></th>
                            <th><?php echo __('order_id'); ?></th>
                            <th><?php echo __('buyer'); ?></th>
                            <th><?php echo __('product_count'); ?></th>
                            <th><?php echo __('total_amount'); ?></th>
                            <th><?php echo __('commission'); ?></th>
                            <th><?php echo __('status'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            if(isset($orders) && is_array($orders) && count($orders) > 0){
                                $commissionPercent = Mava_Application::getOptions()->affiliateCommission;
                                foreach($orders as $item){
                                    $commission = ceil((($item['total_amount']-$item['gift_code_value'])*$commissionPercent)/100);
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
                                    echo '<tr>
                            <td>'. date('d/m/Y H:i:s', $item['created_time']) .'</td>
                            <td>'. $item['id'] .'</td>
                            <td>'. $item['fullname'] .'</td>
                            <td>'. $item['quantity'] .'</td>
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
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="mbd_modal_create_reference_url">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo __('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo __('create_reference_url'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><?php echo __('enter_url_here'); ?></label>
                    <input type="text" class="form-control" id="mbd_base_reference_url" placeholder="http://" />
                </div>
                <div class="form-group">
                    <label><?php echo __('result'); ?></label>
                    <input type="text" class="form-control" readonly="true" onclick="$(this).select();" id="mbd_result_reference_url" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="mbd_submit_create_url"><?php echo __('create_url'); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo __('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var agency_code = '<?php echo htmlspecialchars($agency['agency_code'])?>';
    $(document).ready(function(){
        $('#mbd_create_reference_url').click(function(){
            $('#mbd_modal_create_reference_url').modal('show');
        });

        $('#mbd_submit_create_url').click(function(){
            var base_url = $('#mbd_base_reference_url');
            if(base_url.val() == ''){
                base_url.focus();
            }else{
                var result_url = '';
                if(base_url.val().split('?').length > 1){
                    result_url = base_url.val() +'&arc='+ agency_code;
                }else{
                    result_url = base_url.val() +'?arc='+ agency_code;
                }
                $('#mbd_result_reference_url').val(result_url);
            }
        });
    });
</script>