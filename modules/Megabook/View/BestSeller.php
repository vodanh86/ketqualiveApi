    <div class="container">
        <div class="rm-single-list-content">
            <h3 class="rm-box-title"><?php echo __('book_best_seller'); ?></h3>
            <div class="rm-single-list-list">
                <?php if(isset($products) && is_array($products) && count($products) > 0){ ?>
                    <div class="rm-product-list-wrap">
                        <div class="rm-product-list rm-product-list-tab">
                            <div class="rm-product-list-inner clearfix active">
                                <?php
                                $count = 0;
                                foreach($products as $item){
                                    $count++;
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
                    </div>
                <?php }else{
                    echo '<div class="alert alert-warning rm-no-product">'. __('no_product_found') .'</div>';
                }

                echo '<div class="text-center">'. Mava_View::buildPagination(Mava_Url::getPageLink('sach-ban-chay'),ceil($total/$limit),$page,5) .'</div>';

                ?>
            </div>
        </div>
    </div>
