<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
            echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_phrase" id="form_add_phrase" action="<?php echo Mava_Url::buildLink('admin/phrase/do_add'); ?>" method="post">
        <input type="hidden" name="languageID" id="languageID" value="<?php echo $language['language_id']; ?>" />
        <h2 class="mava_form_title"><?php echo __('create_new_phrase'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row row_info">
                <dt><?php echo __('language'); ?></dt>
                <dd><?php echo htmlspecialchars($language['title']); ?></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('phrase_key'); ?></dt>
                <dd><input type="text" class="input_text" name="phraseTitle" id="phraseTitle" /></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('phrase_text'); ?></dt>
                <dd><textarea class="input_area" name="phraseText" id="phraseText"></textarea></dd>
            </dl>
            <?php
                if($language['language_id'] == 0){
            ?>
            <dl class="row">
                <dt><?php echo __('addon'); ?></dt>
                <dd><select name="addOnID" id="addOnID">
                        <option value=""></option>
                        <?php
                            if(isset($addon) && sizeof($addon)>0){
                                foreach($addon as $item){
                                    echo '<option value="'. $item['addon_id'] .'">'. htmlspecialchars($item['title']) .'</option>';
                                }
                            }
                        ?>
                </select></dd>
            </dl>
            <?php
                }
            ?>

            <dl class="row mava_form_action">
                <dt>&nbsp;</dt>
                <dd>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add_and_exit"><?php echo __('save'); ?></a>
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_add"><?php echo __('save_and_add'); ?></a>
                    <a href="<?php echo Mava_Url::buildLink('admin/phrase/index',array('languageID' => $language['language_id'])); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
                </dd>
            </dl>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#phraseTitle').focus();

        $('#button_submit_add').click(function(){
            if($('#phraseTitle').val()==''){
                $('#phraseTitle').focus();
                return false;
            }else{
                MV.post(DOMAIN +'/admin/phrase/do_add',$('#form_add_phrase').serialize(),function(res){
                    if(res.status==1){
                        MV.dialog.st_alert('<div class="text_success"><s class="icon_success_big"></s>'+res.message+'</div>',function(){
                            $('#form_add_phrase').reset();
                            $('#phraseTitle').focus();
                        });
                    }
                });
            }
            return false;
        });

        $('#button_submit_add_and_exit').click(function(){
            if($('#phraseTitle').val()==''){
                $('#phraseTitle').focus();
                return false;
            }else{
                MV.post(DOMAIN +'/admin/phrase/do_add',$('#form_add_phrase').serialize(),function(res){
                    if(res.status==1){
                        MV.dialog.st_alert('<div class="text_success"><s class="icon_success_big"></s>'+res.message+'</div>',function(){
                            setTimeout(function(){
                                window.location.href = '<?php echo Mava_Url::buildLink('admin/phrase/index',array('languageID' => $language['language_id'])); ?>';
                            },300);
                        });
                    }
                });
            }
            return false;
        });


    });
</script>