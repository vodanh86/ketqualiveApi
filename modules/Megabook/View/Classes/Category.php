<div class="container rm-product-category-page">
    <div class="row">
        <div class="col-md-3">
            <div class="rm-product-category-menu">
                <ul>
                    <?php
                        if(isset($classes) && is_array($classes) && count($classes) > 0){
                            foreach($classes as $item){
                                echo '<li'. ($item['id']==$class['id']?' class="active"':'') .'><a href="'. Mava_Url::getPageLink('lop-hoc/'. Mava_String::unsignString($item['title'], '-')) .'">'. htmlspecialchars($item['title']) .' <i class="fa fa-angle-right"></i></a></li>';
                            }
                        }
                    ?>
                </ul>
            </div>
            <div class="rm-product-category-left-banner">
                <?php
                    $banners = get_banners('left_book_by_classes');
                    if(is_array($banners) && count($banners) > 0){
                       foreach($banners as $item){
                           echo ($item['href']!=""?'<a href="'. $item['href'] .'"><img src="'. $item['image'] .'" class="rm-banner-item" /></a>':'<img src="'. $item['image'] .'" />');
                       }
                    }
                ?>
            </div>
        </div>
        <div class="col-md-9">
            <?php
            if(is_array($products) && count($products) > 0){
                ?>
                <div class="rm-box rm-product-list three">
                    <div class="clearfix rm-product-category-head">
                        <div class="rm-title pull-left">
                            <?php echo htmlspecialchars($class['title']); ?>
                        </div>
                        <div class="pull-right form-inline">
                            <span class="text-muted"><?php echo __('sort_by'); ?></span>
                            <select id="product_sort_by" class="form-control">
                                <option value="1"<?php echo ($sort_by=='time' && $sort_dir=='desc')?' selected':''; ?>><?php echo __('newest'); ?></option>
                                <option value="2"<?php echo ($sort_by=='buy' && $sort_dir=='desc')?' selected':''; ?>><?php echo __('best_buy'); ?></option>
                                <option value="3"<?php echo ($sort_by=='price' && $sort_dir=='asc')?' selected':''; ?>><?php echo __('price_low_to_high'); ?></option>
                                <option value="4"<?php echo ($sort_by=='price' && $sort_dir=='desc')?' selected':''; ?>><?php echo __('price_high_to_low'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="rm-product-list-inner clearfix">
                        <?php foreach($products as $item){
                            $price = Mava_String::price_format($item['price'],$item['currency_unit']);
                            if($item['price_discount'] > 0 && $item['price_discount'] != $item['price'] && ($item['discount_time']==0||$item['discount_time']>time())){
                                $price = '<span class="text-muted"><b title="'. ($item['discount_time']>0?__('discount_to_date').': '.date('d/m/Y H:i', $item['discount_time']):'') .'">'. Mava_String::price_format($item['price_discount'],$item['currency_unit']) .'</b> <s>'. $price .'</s> <span class="rm-discount-percent label label-warning">-'. ceil(($item['price']-$item['price_discount'])*100/$item['price']) .'%</span></span>';
                            }else{
                                $price = '<b>'. $price .'</b>';
                            }
                            $path = $item['thumbnails'][0]['image'];
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
                        } ?>
                    </div>
                </div>
                <div class="text-center">
                    <?php
                        echo Mava_View::buildPagination(Mava_Url::removeParam(Mava_Url::getCurrentAddress(),array('page')),ceil($total/$limit),$page,5);
                    ?>
                </div>
            <?php }else{
                echo '<div id="product_page_container"><div class="alert alert-warning">'. __('no_product_found') .'</div></div>';
            }
            ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var baseUrl = '<?php
         $url = Mava_Url::getCurrentAddress();
         $url = explode('?', $url);
         echo $url[0];
         ?>';
        $('#product_sort_by').change(function(){
            if($(this).val() == 1){       // newest
                baseUrl += '?sort_by=time&sort_dir=desc';
            }else if($(this).val() == 2){ // best_buy
                baseUrl += '?sort_by=buy&sort_dir=desc';
            }else if($(this).val() == 3){ // price_low_to_high
                baseUrl += '?sort_by=price&sort_dir=asc';
            }else if($(this).val() == 4){ // price_high_to_low
                baseUrl += '?sort_by=price&sort_dir=desc';
            }
            window.location.href = baseUrl;
        });
    });
</script>