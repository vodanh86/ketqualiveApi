<?php
$content_html = preg_replace('/\{domain\}/i',Mava_Url::getDomainUrl(),$content_html);
if($layout == 'fluid'){
    echo '<div class="container-fluid tl-static-page">
                <div class="row">
                    <div class="col-md-12 tl-static-page-content">';
    if($show_title=='yes'){
        echo '<h1 class="tl_title">'.$long_title.'</h1>';
    }
    echo ''. $content_html .'
                    </div>
                </div>
            </div>';
}else if($layout == 'one_col'){
    echo '<div class="container tl-static-page">
                <div class="row">
                    <div class="col-md-12 tl-static-page-content">';
    if($show_title=='yes'){
        echo '<h1 class="tl_title">'.$long_title.'</h1>';
    }
    echo ''. $content_html .'
                    </div>
                </div>
            </div>';
}else{
    echo '<div class="container tl-static-page">
                <div class="row">
                    <div class="col-md-9 tl-static-page-content pull-right"> ';
    if($show_title=='yes'){
        echo '<h1 class="tl_title">'.$long_title.'</h1>';
    }
    echo ''. $content_html .'
                    </div>
                    <div class="col-md-3 pull-left tl-static-page-list">';
    echo            '<div class="left-nav-menu">';
    if(isset($page_same_group) && is_array($page_same_group) && count($page_same_group) > 0){
        foreach($page_same_group as $item){
            $a_active = ($page_id==$item['page_id']?'active':'');
            echo '<div class="item"><a class="'.$a_active.'" href="'. Mava_Url::buildLink($item['slug']) .'"><i class="fa fa-angle-right"></i>'. $item['short_title'] .'</a></div>';
        }
    }
    echo            '</div>';
    echo '</div>
                </div>
            </div>';
}
if(is_login()){
    echo '<p class="text-center"><a href="'. Mava_Url::getPageLink('admin/page/add_content', array('page_id' => $page_id)) .'" class="btn btn-default" target="_blank"><i class="fa fa-edit"></i> '. __('edit_post') .'</a></p>';
}
?>
<style type="text/css">
    <?php echo $content_css; ?>
</style>
<script type="text/javascript">
    <?php echo $content_js; ?>
</script>