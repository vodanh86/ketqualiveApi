<?php
    $banners = get_banners('top_book_set');
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
<?php
    }

    if(isset($sets) && is_array($sets) && count($sets)){
        foreach($sets as $set){
?>
<div class="rm-box">
    <div class="container">
        <h2 class="rm-box-title">
            <span><a href="<?php echo Mava_Url::getPageLink('bo-sach/'. Mava_String::unsignString($set['title'], '-')); ?>"><?php echo $set['title']; ?></a></span>
            <?php
            $count = 0;
            $item_per_row = 4;
            $count_product = count($set['books']);
            $tab_count = ceil($count_product/$item_per_row);
            if($tab_count > 1){
                echo '<div class="rm-product-tab-nav">
                <a href="javascript:void(0);" class="previous disabled"><i class="fa fa-angle-left"></i></a>';
                for($i=0;$i<$tab_count;$i++){
                    echo '<a href="javascript:void(0);" class="point'. ($i==0?' active':'') .'" data-tab="'. $i .'"><i class="fa fa-circle'. ($i==0?'':'-o') .'"></i></a>';
                }
                echo '<a href="javascript:void(0);" class="next"><i class="fa fa-angle-right"></i></a>
                </div>';
            }
            ?>
        </h2>
        <div class="rm-product-list-wrap">
            <div class="rm-product-list rm-product-list-tab">
                <div class="rm-product-list-inner clearfix active">
                    <?php
                    if(isset($set['books']) && is_array($set['books']) && count($set['books']) > 0){
                        foreach($set['books'] as $item){
                            if($count > 0 && $count < $count_product && ($count % $item_per_row) == 0){
                                echo '</div><div class="rm-product-list-inner clearfix">';
                            }
                            $count++;
                            $price = Mava_String::price_format($item['price'],$item['currency_unit']);
                            if($item['price_discount'] > 0 && $item['price_discount'] != $item['price'] && ($item['discount_time']==0||$item['discount_time']>time())){
                                $price = '<span class="text-muted"><b title="'. ($item['discount_time']>0?__('discount_to_date').': '.date('d/m/Y H:i', $item['discount_time']):'') .'">'. Mava_String::price_format($item['price_discount'],$item['currency_unit']) .'</b> <s>'. $price .'</s> <span class="rm-discount-percent label label-warning">-'. ceil(($item['price']-$item['price_discount'])*100/$item['price']) .'%</span></span>';
                            }else{
                                $price = '<b>'. $price .'</b>';
                            }
                            if(isset($item['thumbnails'][0]['image'])){
                                $path = $item['thumbnails'][0]['image'];
                            }else{
                                $path = '';
                            }
                            echo '<div class="rm-product-item" data-images="'. htmlspecialchars(json_encode($item['thumbnails'])) .'"><div class="rm-product-item-inner">
                                <a href="'. Mava_Url::getPageLink('san-pham/'. $item['slug'] .'-p'. $item['id']) .'"><img src="'. thumb_url($path, 640,640,2) .'" class="rm-thumb" /></a>
                                <p class="rm-product-item-name-link"><a href="'. Mava_Url::getPageLink('san-pham/'. $item['slug'] .'-p'. $item['id']) .'" title="'. htmlspecialchars($item['name']) .'">'. htmlspecialchars($item['name']) .'</a></p>
                                '. $price .'
                                <div class="rm-product-action clearfix">
                                    '. ($item['out_of_stock']=='yes'?'<a href="javascript:void(0);" class="btn btn-block btn-default disabled btn-out-of-stock">'. __('product_out_of_stock') .'</a>':'<div class="pull-left rm-buy-now">
                                <a href="javascript:void(0);" class="btn btn-block btn-danger" onclick="DH.cart.add(this,'. (int)$item['id'] .');"><i class="fa fa-shopping-cart"></i> '. __('buy_now') .'</a>
                            </div>
                            <div class="pull-right rm-quick-view">
                                <a href="javascript:void(0);" class="btn btn-block btn-default" onclick="DH.product.quick_view(this,'. (int)$item['id'] .');">'. __('quick_view') .'</a>
                            </div>') .'
                                </div>
                                <div class="rm-product-stats clearfix">
                                    <div class="pull-left">
                                        <i class="fa fa-shopping-cart"></i> '. number_format($item['order_item_count'],0) .' '. __('buy_count') .'
                                    </div>
                                    <div class="pull-right rm-quick-view">
                                        <i class="fa fa-eye"></i> '. number_format($item['view_count'],0) .' '. __('view_count') .'
                                    </div>
                                </div>
                            </div></div>';
                        }
                    }else{
                        echo '<div class="alert alert-warning mb-no-product">'. __('no_book_found') .'</div>';
                    }
                     ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
        }
    }
?>