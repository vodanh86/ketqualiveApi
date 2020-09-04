<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_edit_phrase" id="form_edit_phrase" action="<?php echo Mava_Url::buildLink('admin/phrase/do_edit'); ?>" method="post">
        <input type="hidden" name="languageID" id="languageID" value="<?php echo $language['language_id']; ?>" />
        <input type="hidden" name="phraseID" id="phraseID" value="<?php echo (int)$phrase['phrase_id']; ?>" />
        <h2 class="mava_form_title"><?php echo __('edit_phrase'); ?></h2>
        <div class="mava_form_rows">
            <dl class="row row_info">
                <dt><?php echo __('language'); ?></dt>
                <dd><?php echo htmlspecialchars($language['title']); ?></dd>
            </dl>
            <dl class="row">
                <dt><?php echo __('phrase_key'); ?></dt>
                <dd><input type="text" class="input_text" name="phraseTitle" id="phraseTitle" value="<?php echo htmlspecialchars($phrase['title']); ?>" /></dd>
            </dl>
            <?php
                if(isset($master_phrase) && is_array($master_phrase) && isset($master_phrase['phrase_text']) && $master_phrase['phrase_text']!=""){
                    ?>
                    <dl class="row">
                        <dt><?php echo __('master_phrase_text'); ?></dt>
                        <dd><?php echo htmlspecialchars($master_phrase['phrase_text']); ?></dd>
                    </dl>
                <?php
                }
            ?>
            <dl class="row">
                <dt><?php echo __('phrase_text'); ?></dt>
                <dd><textarea class="input_area" name="phraseText" id="phraseText"><?php echo htmlspecialchars($phrase['phrase_text']); ?></textarea></dd>
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
                                $selected = '';
                                if($item['addon_id']==$phrase['addon_id']){
                                    $selected = ' selected';
                                }
                                echo '<option value="'. $item['addon_id'] .'"'. $selected .'>'. htmlspecialchars($item['title']) .'</option>';
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
                    <a href="javascript:void(0);" class="btn_blue mava_button_medium" id="button_submit_edit"><?php echo __('save'); ?></a>
                    &nbsp;
                    <a href="<?php echo Mava_Url::buildLink('admin/phrase/index',array('languageID' => $language['language_id'])); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
                    <?php
                        if($phrase['canDelete']==1){
                    ?>&nbsp;
                    <a href="javascript:void(0);" phrase-id="<?php echo $phrase['phrase_id']; ?>" phrase-title="<?php echo htmlspecialchars($phrase['title']); ?>" class="mava_button mava_button_gray mava_button_medium button_delete_phrase"><?php echo $language['language_id']==0?__('delete_phrase'):__('revert_phrase'); ?></a>
                    <?php
                        }
                    ?>
                </dd>
            </dl>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#button_submit_edit').click(function(){
            if($('#phraseTitle').val()==''){
                $('#phraseTitle').focus();
                return false;
            }else{
                MV.post(DOMAIN +'/admin/phrase/do_edit',$('#form_edit_phrase').serialize(),function(res){
                    if(res.status==1){
                        MV.dialog.st_alert('<div class="text_success"><s class="icon_success_big"></s>'+res.message+'</div>',function(){
                            setTimeout(function(){
                                window.location.href = '<?php echo Mava_Url::buildLink('admin/phrase/index',array('languageID' => $language['language_id'])); ?>';
                            },300);
                        });
                    }else{
                        MV.dialog.st_alert('<div class="text_error"><s class="icon_error_big"></s>'+res.message+'</div>');
                    }
                });
            }
            return false;
        });

        $('.button_delete_phrase').on('click',function(){
            var phraseID = $(this).attr('phrase-id');
            var languageID = $('#languageID').val();
            MV.dialog.st_confirm('<?php echo __('delete_phrase_confirm'); ?><div class="space"></div><b>'+ $(this).attr('phrase-title') +'</b>',function(){
                MV.post(DOMAIN+'/admin/phrase/delete',{
                    phraseID: phraseID,
                    languageID: languageID
                },function(res){
                    if(res.status==1){
                        MV.dialog.st_alert('<div class="text_success"><s class="icon_success_big"></s>'+ res.message +'</div>',function(){
                            window.location.href = '<?php echo Mava_Url::buildLink('admin/phrase/index',array('languageID' => $language['language_id'])); ?>';
                        });
                    }else{
                        MV.dialog.st_alert('<div class="text_error"><s class="icon_error_big"></s>'+res.message+'</div>');
                    }
                });
            },function(){
                // cancel
            });
            return false;
        });
    });
</script>