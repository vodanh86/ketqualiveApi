<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
            echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <form class="mava_form form_add_phrase" action="<?php echo Mava_Url::buildLink('admin/page/add_content',array('page_id'=>$page_id)); ?>" method="post">
        <div class="mava_form_rows">
    <h3 class="mava_form_title"><?php echo __('add_page_content'); ?></h3>
        <div class="mava_row_tab">
    <?php
    if(isset($list_language) && is_array($list_language) && count($list_language) > 1){
        echo '<dl class="row row_tab'. (count($list_language)==1?' hidden':'') .'"><dt></dt><dd><ul class="clearfix">';
        $count = 0;
        foreach($list_language as $lang) {
            $count++;
            echo '<li><a href="javascript:void(0);" class="item'. ($count==1?' active':'') .'" rel="language_tab_'. $lang['language_id'] .'">'. $lang['title'] .'</a></li>';
        }
        echo '</ul></dd></dl>';
    }
    $d=0;
    if(is_array($list_language) && count($list_language) > 0){ ?>
        <?php foreach($list_language as $lang) {
            $d++;
            if($d==1){
                $class="row_group";
            }else{
                $class="row_group hidden";
            }
            $lang_id = $lang['language_id'];
            ?>

            <div id="language_tab_<?php echo $lang['language_id'] ; ?>" class="<?php echo $class; ?>">

                        <?php
                        if(isset($error_message) && $error_message != ''){
                            echo '
                        <dl class="row">
                            <dt></dt>
                            <dd style="color: #ff0000;">'.$error_message.'</dd>
                        </dl>
                    ';
                        }
                        ?>

                        <dl class="row">
                            <dt><?php echo __('short_title'); ?><b style="color: red;">(*)</b></dt>
                            <dd>
                                <input name="short_title_<?php echo $lang['language_id'] ; ?>" type="text" class="input_text" value="<?php echo (isset($data[$lang_id]['short_title'])?$data[$lang_id]['short_title']:'') ; ?>" />
                            </dd>
                        </dl>
                        <dl class="row">
                            <dt><?php echo __('long_title'); ?></dt>
                            <dd>
                                <input name="long_title_<?php echo $lang['language_id'] ; ?>" type="text" class="input_text" value="<?php echo (isset($data[$lang_id]['long_title'])?$data[$lang_id]['long_title']:'') ; ?>" />
                            </dd>
                        </dl>
                        <dl class="row">
                            <dt><?php echo __('content_css'); ?></dt>
                            <dd>
                                <textarea class="input_area" name="content_css_<?php echo $lang_id ; ?>"><?php echo (isset($data[$lang_id]['content_css'])?$data[$lang_id]['content_css']:'') ; ?></textarea>
                            </dd>
                        </dl>
                        <dl class="row">
                            <dt><?php echo __('content_js'); ?></dt>
                            <dd>
                                <textarea class="input_area" name="content_js_<?php echo $lang_id ; ?>"><?php echo (isset($data[$lang_id]['content_js'])?$data[$lang_id]['content_js']:'') ; ?></textarea>
                            </dd>
                        </dl>
                        <dl class="row">
                            <dt><?php echo __('format') ; ?></dt>
                            <dd>
                                <select name="content_type_<?php echo $lang['language_id'] ; ?>" class="text_type" lang_id="<?php echo $lang['language_id'] ; ?>">
                                    <option value="html">html</option>
                                    <option value="text">text</option>
                                </select>
                            </dd>
                        </dl>

                        <dl class="row content_html_inner" id="content_html_inner_<?php echo $lang['language_id'] ; ?>">
                            <h3 style="margin: 10px;"><?php echo __('content'); ?></h3>
                            <textarea name="content_html_<?php echo $lang['language_id'] ; ?>"  class="input_richtext"><?php echo (isset($data[$lang_id]['content_html'])?$data[$lang_id]['content_html']:'') ; ?></textarea>
                        </dl>
                        <dl class="row content_text_inner" id="content_text_inner_<?php echo $lang['language_id'] ; ?>">
                            <h3 style="margin: 10px;"><?php echo __('content'); ?></h3>
                            <textarea name="content_text_<?php echo $lang['language_id'] ; ?>" style="width:100%; height: 300px;"><?php echo (isset($data[$lang_id]['content_html'])?$data[$lang_id]['content_html']:'') ; ?></textarea>
                        </dl>
                    </div>

        <?php
        }
    }
        ?>

            <div class="row mava_form_action">
                <input style="height: 30px;" type="submit" href="javascript:void(0);" class="mava_button btn_blue mava_button_medium" id="button_submit_add" value="<?php echo __('save'); ?>"/>
                <a href="<?php echo Mava_Url::buildLink('admin/page/index'); ?>" class="mava_button mava_button_gray mava_button_medium"><?php echo __('cancel'); ?></a>
            </div>
        </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.content_text_inner').hide();
        $('.text_type').change(function(){
            var lang_id = $(this).attr('lang_id');
            var val = $(this).val();
            console.log(lang_id+' '+val);
            if(val=='html'){
                $('#content_html_inner_'+lang_id+'').show();
                $('#content_text_inner_'+lang_id+'').hide();
            }else{
                $('#content_html_inner_'+lang_id+'').hide();
                $('#content_text_inner_'+lang_id+'').show();
            }
        });
    });
</script>
