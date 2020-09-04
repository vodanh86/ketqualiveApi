<div class="container" id="ask_page">
    <div class="row">
        <div class="col-md-8">
            <h2><?php echo __('recent_question'); ?></h2>
            <div class="rm-ask-list">
                <?php
                    if(isset($questions) && is_array($questions) && count($questions) > 0){
                        foreach($questions as $item){
                            echo '<div class="rm-ask-item">
                                    <div class="rm-ask-item-question"><span>'. htmlspecialchars($item['question']) .'</span></div>
                                    <div class="rm-ask-item-answer"><b>'. __('site_name') .'</b>: '. $item['answer'] .'</div>
                                </div>';
                        }
                    }
                ?>
            </div>
            <div class="text-center">
            <?php echo Mava_View::buildPagination(Mava_Url::removeParam(Mava_Url::getCurrentAddress(),array('page')),ceil($total/$limit),$page,5); ?>
            </div>
        </div>
        <div class="col-md-4">
            <h3><?php echo __('ask_royalmen'); ?></h3>
            <div class="rm-ask-form">
                <div class="alert alert-success hidden"></div>
                <div class="form-group">
                    <input type="text" class="form-control" id="askFullname" name="askFullname" placeholder="<?php echo __('fullname'); ?>" />
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="askPhone" name="askPhone" placeholder="<?php echo __('phone'); ?>" />
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="askEmail" name="askEmail" placeholder="<?php echo __('email'); ?>" />
                </div>
                <div class="form-group">
                    <textarea class="form-control" id="askQuestion" name="askQuestion" placeholder="<?php echo __('question_content'); ?> *"></textarea>
                </div>
                <div class="form-group">
                    <input type="button" class="btn btn-primary" id="askSubmit" value="<?php echo __('send_question'); ?>" />
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#askSubmit').click(function(){
            if($('#askQuestion').val() == ''){
                $('#askQuestion').focus();
            }else{
                MV.post(DOMAIN +'/add-question',{
                    fullname: $('#askFullname').val(),
                    phone: $('#askPhone').val(),
                    email: $('#askEmail').val(),
                    question: $('#askQuestion').val()
                },function(res){
                    if(res.status == 1){
                        $('#askFullname,#askPhone,#askEmail,#askQuestion').val('');
                        if(res.email_id > 0 && res.email_token != ""){
                            $('body').append('<img src="'+ DOMAIN +'/cron?type=send_mail&email_id='+ res.email_id +'&token='+ res.email_token +'" height="0" width="0" />');
                        }
                        $('.rm-ask-form > .alert').removeClass('alert-danger hidden').addClass('alert-success').html(res.message);
                    }else{
                        $('.rm-ask-form > .alert').removeClass('alert-success hidden').addClass('alert-danger').html(res.message);
                    }
                });
            }
        });
    });
</script>