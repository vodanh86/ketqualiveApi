<?php
    if(Mava_Visitor::getInstance()->isSuperAdmin()){
        echo '<div class="mv-debug-quick-view" style="
        position: fixed;
        bottom: 0;
        right:0;
        padding: 1px 5px;
        background: #FFF;
        border: 1px solid #ddd;
        border-radius: 0 3px 0 0;
        font-size: 11px;
        color: #999;
        ">';
        $debugInfo = Mava_Debug::getDebugTemplateParams();
        echo '<a href="'. $debugInfo['debug_url'] .'" class="show_debug_content" style="color: #999;">Timing: '. number_format($debugInfo['page_time'],6) .' seconds | Memory: '. number_format($debugInfo['memory_usage'] / 1024 / 1024, 4) .' MB | DB Queries: '. $debugInfo['db_queries'] .'</a>
         | <a href="'. $debugInfo['phrase_text_url'] .'"  style="color: #999;">Phrase & Text</a>
         | <a href="'. $debugInfo['phrase_url'] .'"  style="color: #999;">Phrase Only</a>
         | <a href="'. str_replace(array('&_phrase=1','&_phrase=2','?_phrase=1','?_phrase=2'),'',$debugInfo['phrase_url']) .'"  style="color: #999;">Text Only</a>';
        echo '</div>';
        echo '<div class="debug_content hidden">'. Mava_Debug::getDebugHtml() .'</div>';
    }
?>
<script type="text/javascript">
    $(document).ready(function(){
        $('.show_debug_content').click(function(){
            if($('.debug_content').hasClass('hidden')){
                $('.debug_content').removeClass('hidden');
            }else{
                $('.debug_content').addClass('hidden');
            }
            return false;
        });
    });
</script>