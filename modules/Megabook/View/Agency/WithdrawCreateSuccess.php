<div class="container-fluid">
    <div class="mbd-box">
        <div class="mbd-body">
            <div class="text-center">
                <h2><?php echo __('send_withdraw_request_success'); ?></h2>
                <p><?php echo __('send_withdraw_request_success_note'); ?></p>
                <p><a class="btn btn-info" href="<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/withdraw'); ?>"><?php echo __('back'); ?></a></p>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        setTimeout(function(){
            window.location.href = '<?php echo Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/withdraw'); ?>';
        }, 8000);
    });
</script>