<div id="document_index_page">
    <?php
    $banners = get_banners('top_document');
    if(is_array($banners) && count($banners) > 0){
        ?>
        <div class="container">
            <div id="rm_top_slideshow" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner" role="listbox">
                    <?php
                    $count = 0;
                    $indicators = '';
                    foreach($banners as $item){
                        $indicators .= '<li data-target="#rm_top_slideshow" data-slide-to="'. $count .'"'. ($count==0?' class="active"':'') .'></li>';
                        $count++;
                        echo '<div class="item'. ($count==1?' active':'') .'"'. ($item['background']!=""?' style="background:'. $item['background'] .';"':'') .'>
                        '. ($item['href']!=""?'<a href="'. $item['href'] .'"><img src="'. $item['image'] .'" /></a>':'<img src="'. $item['image'] .'" />') .'
<div class="carousel-caption">'. ($item['title'] != ""?'<h3>'. $item['title'] .'</h3>':'') . ($item['subtitle']!=""?'<p>'. $item['subtitle'] .'</p>':'') .'</div>
                    </div>';
                    }
                    ?>
                </div>
                <ol class="carousel-indicators">
                    <?php echo $indicators; ?>
                </ol>
                <a class="left carousel-control" href="#rm_top_slideshow" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only"><?php echo __('previous'); ?></span>
                </a>
                <a class="right carousel-control" href="#rm_top_slideshow" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only"><?php echo __('next'); ?></span>
                </a>
            </div>
        </div>
    <?php } ?>
    <div class="mb-document-list-container">
        <?php
        if(isset($category) && is_array($category) && count($category) > 0){
            ?>
            <div class="rm-box">
                <div class="container">
                    <h2 class="rm-box-title clearfix">
                        <span class="pull-left"><a href="<?php echo Mava_Url::getPageLink('tai-lieu/'. Mava_String::unsignString($category['title'], '-')); ?>"><?php echo htmlspecialchars($category['title']); ?></a></span>
                        <a class="pull-right rm-title-more" href="<?php echo Mava_Url::getPageLink('tai-lieu/'. Mava_String::unsignString($category['title'], '-')); ?>"><?php echo __('view_more'); ?> <i class="fa fa-caret-right"></i></a>
                    </h2>
                    <div class="rm-document-list-wrap">
                        <div class="rm-document-list clearfix">
                            <?php
                            if(isset($posts) && is_array($posts) && count($posts) > 0){
                                foreach($posts as $doc){
                                    if($doc['category_id'] > 0){
                                        $detail_url = Mava_Url::getPageLink('tai-lieu/'. Mava_String::unsignString($doc['category_title'],'-') .'/'. Mava_String::unsignString($doc['title'],'-') .'-p'. $doc['post_id']);
                                    }else{
                                        $detail_url = Mava_Url::getPageLink('tai-lieu/view/'. Mava_String::unsignString($doc['title'],'-') .'-p'. $doc['post_id']);
                                    }

                                    echo '<div class="rm-doc-item">
                                                        <a href="'. $detail_url .'" class="rm-thumb-link"><img src="'. thumb_url($doc['cover_image'], 285, 285) .'" /></a>
                                                        <a href="'. $detail_url .'" class="rm-name-link">'. htmlspecialchars($doc['title']) .'</a>
                                                        <div class="row rm-doc-action">
                                                        <div class="col-md-6 rm-download"><a href="'. Mava_Url::getPageLink('tai-lieu', array('cmd' => 'download', 'doc_id' => $doc['post_id'])) .'" target="_blank" class="btn btn-success btn-block"><i class="fa fa-download"></i> '. __('download') .'</a></div>
                                                        <div class="col-md-6 rm-read"><a href="'. $detail_url .'" class="btn btn-default btn-block">'. __('read_online') .'</a></div>
                                                        </div>
                                                    </div>';
                                }
                            }else{
                                echo '<div class="alert alert-warning">'. __('no_document_found') .'</div>';
                            }

                            echo Mava_View::buildPagination(
                                Mava_Url::removeParam(Mava_Url::getCurrentAddress(), array('page')),
                                ceil($total/$limit),
                                $page,
                                $offset,
                                'document_list_pagination'
                            );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }else{
            echo '<div class="alert alert-warning">'. __('no_document_found') .'</div>';
        }
        ?>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){

    });
</script>