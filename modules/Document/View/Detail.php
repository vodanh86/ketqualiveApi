<div id="blog_detail_page">
        <?php
            $banners = get_banners('top_mini_document');
            if(is_array($banners) && count($banners) > 0){
                echo '<div class="mb-doc-top-banner">
        <div class="container">';
                foreach($banners as $item){
                    echo ($item['href']!=""?'<a href="'. $item['href'] .'"><img src="'. $item['image'] .'" class="rm-banner-item" /></a>':'<img src="'. $item['image'] .'" />');
                }
                echo '</div>
    </div>';
            }
        ?>
    <div class="container">
        <div class="row">
            <div class="col-md-9 blog_left blog_post_list">
                <?php
                    if($post['category_id'] > 0){
                        $detail_url = Mava_Url::getPageLink('documents/'. Mava_String::unsignString($post['category_title'],'-') .'/'. Mava_String::unsignString($post['title'],'-') .'-p'. $post['post_id']);
                    }else{
                        $detail_url = Mava_Url::getPageLink('documents/view/'. Mava_String::unsignString($post['title'],'-') .'-p'. $post['post_id']);
                    }
                    $download_link = Mava_Url::getPageLink('tai-lieu', array('cmd' => 'download', 'doc_id' => $post['post_id']));
                    if(isset($post) && $post['deleted'] == 0){
                        echo '
                                <div class="mb-document-head clearfix">
                                    <div class="pull-left">
                                        <h1 class="post_title">'. htmlspecialchars($post['title']) .'</h1>
                                        <div class="mb-document-item-stats">
                                            <span><i class="fa fa-eye"></i> '. $post['view_count'] .'</span>
                                            <span><i class="fa fa-download"></i> '. $post['download_count'] .'</span>
                                        </div>
                                    </div>
                                    <div class="pull-right mb-doc-download">
                                        <a href="'. $download_link .'" class="btn btn-warning btn-lg"><i class="fa fa-download"></i> '. __('download_now') .' <span class="badge">'. $post['download_count'] .'</span></a>
                                    </div>
                                </div>
                         <div class="post_content">';
                        preg_match('%https://drive\.google\.com/file/d/([^\/]+)/(.*?)%', $post['content'], $driver_id);
                        echo '<div class="content_view"><iframe class="mb-document-viewer" frameborder="0" src="https://drive.google.com/file/d/'. $driver_id[1] .'/preview" width="847" height="550"></iframe></div>
                        </div>
                        <div class="mb-comment-box"><div class="fb-comments" data-href="'. $detail_url .'" data-numposts="10" data-width="847"></div></div>
                        ';
                    }
                ?>
            </div>
            <div class="col-md-3 blog_right">
                <?php
                echo '<div class="blog_category_list"><h3 class="category_filter_title">'. __('document_category') .'</h3>
                        <ul class="category_filter clearfix">';
                if(isset($categories) && is_array($categories) && count($categories) > 0){
                    foreach($categories as $item){
                        echo '<li'. ((isset($post['category_id']) && $post['category_id']==$item['category_id'])?" class='active'":"") .'><a href="'. Mava_Url::getPageLink('tai-lieu/'. Mava_String::unsignString($item['title'], '-')) .'">'. htmlspecialchars($item['title']) .'</a></li>';
                    }
                }

                echo '</ul></div>';
                ?>
                <div class="rm-blog-right-banner">
                    <?php
                    $banners = get_banners('right_document');
                    if(is_array($banners) && count($banners) > 0){
                        foreach($banners as $item){
                            echo ($item['href']!=""?'<a href="'. $item['href'] .'"><img src="'. $item['image'] .'" class="rm-banner-item" /></a>':'<img src="'. $item['image'] .'" />');
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>