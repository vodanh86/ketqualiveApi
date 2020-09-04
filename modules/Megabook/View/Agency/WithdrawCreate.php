<div class="container-fluid">
    <div class="mbd-box">
        <div class="mbd-head">
            <span class="mbd-head-title"><?php echo __('send_withdraw_request'); ?></span>
        </div>
        <div class="mbd-body">
            <?php
                $minWithdraw = Mava_Application::getOptions()->affiliateWithdrawMinimum;
                if(isset($error) && $error != ""){
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="'. __('close') .'"><span aria-hidden="true">&times;</span></button>
                            '. $error .'
                            </div>';
                }
            ?>
            <form action="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/new-withdraw-request'); ?>" method="post">
                <div class="form-group">
                    <label class="control-label"><?php echo __('current_balance'); ?></label>
                    <div class="form-control-static"><?php echo Mava_String::price_format($agency['balance']); ?></div>
                </div>
                <div class="form-group">
                    <label class="control-label"><?php echo __('withdraw_amount') .' ('. __('withdraw_minimum_x', array('num' => Mava_String::price_format($minWithdraw))) .')'; ?></label>
                    <input type="text" class="form-control" name="withdrawAmount" id="withdrawAmount" placeholder="<?php echo __('withdraw_amount_placeholder'); ?>" />
                </div>
                <div class="form-group">
                    <label class="control-label" for="agencyBankName"><?php echo __('bank_name'); ?></label>
                    <input type="text" placeholder="<?php echo __('bank_name_placeholder'); ?>" class="form-control" name="agencyBankName" id="agencyBankName" value="<?php echo htmlspecialchars($agency['bank_name']); ?>" />
                </div>
                <div class="form-group">
                    <label class="control-label" for="agencyBankBranch"><?php echo __('bank_branch'); ?></label>
                    <input type="text" placeholder="<?php echo __('bank_branch_placeholder'); ?>" class="form-control" name="agencyBankBranch" id="agencyBankBranch" value="<?php echo htmlspecialchars($agency['bank_branch']); ?>" />
                </div>
                <div class="form-group">
                    <label class="control-label" for="agencyBankFullname"><?php echo __('bank_fullname'); ?></label>
                    <input type="text" placeholder="<?php echo __('bank_fullname_placeholder'); ?>" class="form-control" name="agencyBankFullname" id="agencyBankFullname" value="<?php echo htmlspecialchars($agency['bank_fullname']); ?>" />
                </div>
                <div class="form-group">
                    <label class="control-label" for="agencyBankIdString"><?php echo __('bank_id_string'); ?></label>
                    <input type="text" placeholder="<?php echo __('bank_id_string_placeholder'); ?>" class="form-control" name="agencyBankIdString" id="agencyBankIdString" value="<?php echo htmlspecialchars($agency['bank_id_string']); ?>" />
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary"><?php echo __('send_request'); ?></button>
                    <a href="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/withdraw'); ?>" class="btn btn-default"><?php echo __('cancel'); ?></a>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        if($('#withdrawAmount').val() == ''){
            $('#withdrawAmount').focus();
        }
    });
</script>