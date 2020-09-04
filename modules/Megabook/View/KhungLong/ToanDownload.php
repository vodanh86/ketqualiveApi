<div class="klt-download-wrap">
    <div class="container">
    <div class="klt-download-box">
        <img src="<?php echo css_image_url('khunglongtoan/sub_top.png'); ?>" />
        <form>
            <div class="klt-title"><?php echo __('khunglong_document_class_x', array('num' => $docId)); ?></div>
            <div class="form-group form-group-lg">
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-user"></i></div>
                    <input type="text" class="form-control" id="sub_fullname" name="sub_fullname" placeholder="<?php echo __('fullname'); ?>">
                </div>
            </div>
            <div class="form-group form-group-lg">
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                    <input type="text" class="form-control" id="sub_email" name="sub_email" placeholder="<?php echo __('email'); ?>">
                </div>
            </div>

            <div class="text-center">
                <p><a href="javascript:void(0);" id="sub_download_button"><img src="<?php echo css_image_url('khunglongtoan/sub_download.png'); ?>" style="width: 100%" /></a></p>
                <p><?php echo __('khunglong_document_download_tips'); ?></p>
                <a href="<?php echo Mava_Url::getPageLink('tai-lieu-toan-khung-long'); ?>">[ <?php echo __('back'); ?> ]</a>
            </div>
        </form>
    </div>
    </div>
</div>

<script type="text/javascript">
    if(MV.cookie('user_name') != ""){
        $('#sub_fullname').val(MV.cookie('user_name'));
    }
    if(MV.cookie('user_email') != ""){
        $('#sub_email').val(MV.cookie('user_email'));
    }
    var doc_download_url = '<?php echo $linkDownload; ?>';
    $('#sub_download_button').click(function(){
        var sub_email = $('#sub_email');
        var sub_name = $('#sub_fullname');
        if(sub_name.val() == ''){
            sub_name.focus();
        }else if(sub_email.val() == ''){
            sub_email.focus();
        }else{
            MV.cookie('user_name', sub_name.val(), {expires: 30, domain: '.'+ DOMAIN.replace('http://',''), path: '/'});
            MV.cookie('user_email', sub_email.val(), {expires: 30, domain: '.'+ DOMAIN.replace('http://',''), path: '/'});
            MV.post(DOMAIN +'/add-subscribe',{
                source_title: __('document_class_x',{num: '<?php echo $docId; ?>'}),
                source_key: 'Lop<?php echo $docId; ?>',
                fullname: sub_name.val(),
                email: sub_email.val(),
                ignore_duplicate: 1
            },function(res){
                if(res.status == 1){
                    if(res.email_id > 0 && res.email_token != ""){
                        $('body').append('<img src="'+ DOMAIN +'/cron?type=send_mail&email_id='+ res.email_id +'&token='+ res.email_token +'" height="0" width="0" />');
                    }
                    window.location.href = doc_download_url;
                }else{
                    MP.notice.show(res.message, 'danger', 3);
                }
            });
        }
    });
</script>