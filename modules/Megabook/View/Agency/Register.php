<div class="container">
    <h3 class="text-center"><?php echo __('register_agency'); ?></h3>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="mbd-error">
                <?php if($error != ""){
                    echo '<div class="alert alert-danger alert-dismissable"><span>'. $error .'</span><button type="button" class="close" data-dismiss="alert" aria-label="'. __('close') .'"><span aria-hidden="true">&times;</span></button></div>';
                } ?>
            </div>
            <form id="form_agency_register" action="<?php echo Mava_Url::getPageLink('dang-ky-dai-ly'); ?>" method="post" class="mb-agency-register-form">
                <div class="form-group">
                    <label for="agencyTitle"><?php echo __('agency_title'); ?></label>
                    <input type="text" class="form-control" name="agencyTitle" id="agencyTitle" />
                </div>
                <div class="form-group">
                    <label for="agencyEmail"><?php echo __('email'); ?></label>
                    <input type="text" class="form-control" name="agencyEmail" id="agencyEmail" />
                </div>
                <div class="form-group">
                    <label for="agencyPhone"><?php echo __('phone_number'); ?></label>
                    <input type="text" class="form-control" name="agencyPhone" id="agencyPhone" />
                </div>
                <div class="form-group">
                    <label for="agencyAddress"><?php echo __('address'); ?></label>
                    <input type="text" class="form-control" name="agencyAddress" id="agencyAddress" />
                </div>

                <div class="form-group mbd-tos">
                    <label><input type="checkbox" name="agencyTOS" id="agencyTOS" value="1" /> <?php echo __('i_agree_with_agency_tos', array('link' => Mava_Url::getPageLink('agency-tos'))); ?></label>
                </div>
                <div class="form-group">
                   <button type="submit" class="btn btn-primary btn-lg btn-block"><?php echo __('finish'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#form_agency_register').submit(function(){
            if($('#agencyTitle').val() == ''){
                $('#agencyTitle').focus();
                $('.mbd-error').html('<div class="alert alert-danger alert-dismissable"><span><?php echo __('agency_title_empty'); ?></span><button type="button" class="close" data-dismiss="alert" aria-label="<?php echo __('close'); ?>"><span aria-hidden="true">&times;</span></button></div>')
                return false;
            }else if(!$('#agencyTOS').is(':checked')){
                $('#agencyTOS').focus();
                $('.mbd-tos').animate({opacity: 0.5}, 200).animate({opacity: 1}, 200).animate({opacity: 0.5}, 200).animate({opacity: 1}, 200);
                $('.mbd-error').html('<div class="alert alert-danger alert-dismissable"><span><?php echo __('you_must_agree_with_agency_tos'); ?></span><button type="button" class="close" data-dismiss="alert" aria-label="<?php echo __('close'); ?>"><span aria-hidden="true">&times;</span></button></div>')
                return false;
            }else{
                return true;
            }
        });
    });
</script>