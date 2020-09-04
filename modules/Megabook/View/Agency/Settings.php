<div class="container-fluid">
    <div class="mbd-box">
        <div class="mbd-head">
            <span class="mbd-head-title"><?php echo __('settings'); ?></span>
        </div>
        <div class="mbd-body">
            <?php
                if(isset($error) && $error != ""){
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="'. __('close') .'"><span aria-hidden="true">&times;</span></button>
                        '. $error .'
                        </div>';
                }
                if(isset($success) && $success != ""){
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="'. __('close') .'"><span aria-hidden="true">&times;</span></button>
                        '. $success .'
                        </div>';
                }
            ?>
            <form action="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/settings'); ?>" method="post">
                <div class="row">
                    <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="agencyTitle"><?php echo __('agency_title'); ?></label>
                                <input type="text" class="form-control" name="agencyTitle" id="agencyTitle" value="<?php echo htmlspecialchars($agency['title']); ?>" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="agencyCode"><?php echo __('agency_code'); ?></label>
                                <input type="text" placeholder="<?php echo __('agency_code_placeholder'); ?>" class="form-control" name="agencyCode" id="agencyCode" value="<?php echo htmlspecialchars($agency['agency_code']); ?>" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="agencyEmail"><?php echo __('email'); ?></label>
                                <input type="email" class="form-control" name="agencyEmail" id="agencyEmail" value="<?php echo htmlspecialchars($agency['email']); ?>" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="agencyPhone"><?php echo __('phone'); ?></label>
                                <input type="text" class="form-control" name="agencyPhone" id="agencyPhone" value="<?php echo htmlspecialchars($agency['phone']); ?>" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="agencyAddress"><?php echo __('address'); ?></label>
                                <input type="text" class="form-control" name="agencyAddress" id="agencyAddress" value="<?php echo htmlspecialchars($agency['address']); ?>" />
                            </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="agencyBankName"><?php echo __('bank_name'); ?> <span data-toggle="tooltip" title="<?php echo __('for_withdraw'); ?>"><i class="fa fa-question-circle"></i></span></label>
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
                        <div class="alert alert-info"><?php echo __('withdraw_info_note'); ?></div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary"><?php echo __('save'); ?></button>
                    <a href="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id']); ?>" class="btn btn-default"><?php echo __('cancel'); ?></a>
                </div>
            </form>
        </div>
    </div>
</div>