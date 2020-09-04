<div id="blog_index_page">
    <?php
    $banners = get_banners('top_news');
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
    <div class="container">
        <div class="row">
            <div class="col-md-9 blog_left blog_post_list">
                <a href="javascript:void(0);" class="show_blog_category hidden"><?php echo __('blog_category'); ?><s class="arrow-down-black"></s></a>
                <?php
                    echo (is_array($category) && count($category) > 0)?'<h1 class="blog_list_title">'. htmlspecialchars($category['title']) .'</h1>':'<h1 class="blog_list_title">'. __('all_blog') .'</h1>';
                    if(is_array($posts) && count($posts) > 0){
                        foreach($posts as $item){
                            if($item['category_id'] > 0){
                                $detail_url = Mava_Url::getPageLink('documents/'. Mava_String::unsignString($item['category_title'],'-') .'/'. Mava_String::unsignString($item['title'],'-') .'-p'. $item['post_id']);
                            }else{
                                $detail_url = Mava_Url::getPageLink('documents/view/'. Mava_String::unsignString($item['title'],'-') .'-p'. $item['post_id']);
                            }

                            echo '<div class="item">
    '. ($item['cover_image']!=""?'<a href="'. $detail_url .'"><img src="'. thumb_url($item['cover_image'], 160, 110) .'" class="post_pic"/></a>':'') .'
    <h2 class="post_title"><a href="'. $detail_url .'">'. htmlspecialchars($item['title']) .'</a></h2>
    <p class="post_lead">'. htmlspecialchars($item['lead']) .'</p>
    <div class="clearfix">
    <div class="pull-left post_meta">'. htmlspecialchars($item['category_title']) .' - '. print_time($item['created_date']) .'</div>
    <div class="pull-right post_view_more"><a href="'. $detail_url .'" class="btn btn-default btn-sm">'. __('view_detail') .'</a></div>
    </div>
    </div>';
                        }
                    }else{
                        echo '<div class="no_post_found">'. __('no_post_found') .'</div>';
                    }

                echo Mava_View::buildPagination(
                    Mava_Url::removeParam(Mava_Url::getCurrentAddress(), array('page')),
                    ceil($total/$limit),
                    $page,
                    $offset,
                    'blog_list_pagination'
                );
                ?>
            </div>
            <div class="col-md-3 blog_right">
                <?php
                echo '<div class="blog_category_list"><h3 class="category_filter_title">'. __('blog_category') .'</h3>
                        <ul class="category_filter clearfix"><li'. ($categoryId==0?" class='active'":"") .'><a href="'. Mava_Url::getPageLink('documents') .'">'. __('all_blog') .'</a></li>';
                if(is_array($categories) && count($categories) > 0){
                    foreach($categories as $item){
                        echo '<li'. ((isset($categoryId) && $categoryId==$item['category_id'])?" class='active'":"") .'><a href="'. Mava_Url::getPageLink('documents/'. Mava_String::unsignString($item['title'], '-')) .'">'. htmlspecialchars($item['title']) .'</a></li>';
                    }
                }

                echo '</ul></div>';
                ?>
                <div class="rm-blog-right-banner">
                    <?php
                    $banners = get_banners('right_news');
                    if(isset($categories) && is_array($banners) && count($banners) > 0){
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
<script type="text/javascript">
    $(document).ready(function(){
        $('.show_blog_category').click(function(){
            if($('#blog_page .blog_right').hasClass('open')){
                $('#blog_page .blog_right').removeClass('open').addClass('close');
            }else{
                $('#blog_page .blog_right').removeClass('close').addClass('open');
            }
        });
    });
</script>