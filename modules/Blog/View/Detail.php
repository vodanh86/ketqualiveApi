<div id="blog_detail_page">
    <div class="container">
        <div class="row">
            <div class="col-md-9 blog_left blog_post_list">
                <?php
                    if($post['category_id'] > 0){
                        $detail_url = Mava_Url::getPageLink('documents/'. Mava_String::unsignString($post['category_title'],'-') .'/'. Mava_String::unsignString($post['title'],'-') .'-p'. $post['post_id']);
                    }else{
                        $detail_url = Mava_Url::getPageLink('documents/view/'. Mava_String::unsignString($post['title'],'-') .'-p'. $post['post_id']);
                    }
                    if(isset($post) && $post['deleted'] == 0){
                        echo '<h1 class="post_title">'. htmlspecialchars($post['title']) .'</h1>
                        <div class="post_content">';
                        ?>
                        <div class="mb-share-post">
                            <div class="fb-like" data-href="<?php echo $detail_url; ?>" data-layout="button_count" data-action="like" data-size="small" data-show-faces="false" data-share="true"></div>
                        </div>
                        <?php

                        echo '<div class="content_view">'. $post['content'] .'</div>
                        </div>
                        <div class="mb-comment-box"><div class="fb-comments" data-href="'. $detail_url .'" data-numposts="10" data-width="847"></div></div>
                        ';
                    }
                ?>
            </div>
            <div class="col-md-3 blog_right">
                <?php
                echo '<div class="blog_category_list"><h3 class="category_filter_title">'. __('blog_category') .'</h3>
                        <ul class="category_filter clearfix"><li'. ($post['category_id']==0?" class='active'":"") .'><a href="'. Mava_Url::getPageLink('documents') .'">'. __('all_blog') .'</a></li>';
                if(isset($categories) && is_array($categories) && count($categories) > 0){
                    foreach($categories as $item){
                        echo '<li'. ((isset($post['category_id']) && $post['category_id']==$item['category_id'])?" class='active'":"") .'><a href="'. Mava_Url::getPageLink('documents/'. Mava_String::unsignString($item['title'], '-')) .'">'. htmlspecialchars($item['title']) .'</a></li>';
                    }
                }

                echo '</ul></div>';
                ?>
                <div class="rm-blog-right-banner">
                    <?php
                    $banners = get_banners('right_news');
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