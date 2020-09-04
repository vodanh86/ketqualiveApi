<div id="profile_page" class="container">
    <?php echo Mava_View::getView('Profile_View_Includes_Menu'); ?>
    <div id="profile_right" class="col-md-9">
        <div class="rm-box-border">
            <h4 class="rm-head-page-box"><?php echo __('order_detail') .' ('. __('order_id') .': <span class="text-red">'. $order['id'] .'</span>)'; ?></h4>
            <div class="row">
                <div class="col-md-6">
                    <b><?php echo __('consignee_address'); ?></b>
                    <ul>
                        <li><?php echo __('fullname') .': '. $order['fullname']; ?></li>
                        <li><?php echo __('address') .': '. $order['address']; ?></li>
                        <li><?php echo __('phone') .': '. $order['phone']; ?></li>
                    </ul>
                </div>
                <?php
                    if($order['status'] == 'cancelled'){
                        $label = 'danger';
                    }else if($order['status'] == 'read'){
                        $label = 'primary';
                    }else if($order['status'] == 'processing'){
                        $label = 'info';
                    }else if($order['status'] == 'done'){
                        $label = 'success';
                    }else {
                        $label = 'default';
                    }
                ?>
                <div class="col-md-6">
                    <b><?php echo __('order_info'); ?></b>
                    <ul>
                        <li><?php echo __('order_id') .': '. $order['id']; ?></li>
                        <li><?php echo __('order_time') .': '. date('d/m/Y H:i', $order['created_time']); ?></li>
                        <li><?php echo __('order_item_count') .': '. $order['quantity']; ?></li>
                        <li><?php echo __('order_total_amount') .': '. Mava_String::price_format($order['total_amount']); ?></li>
                        <li><?php echo __('status') .': <span class="label label-'. $label .'">'. __('order_status_member_'. $order['status']) .'</span>'; ?></li>
                    </ul>
                </div>
            </div>
            <h4><?php echo __('product'); ?></h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo __('stt'); ?></th>
                        <th><?php echo __('product'); ?></th>
                        <th><?php echo __('price'); ?></th>
                        <th><?php echo __('quantity'); ?></th>
                        <th><?php echo __('amount'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        if(isset($order['items']) && is_array($order['items']) && count($order['items']) > 0){
                            foreach($order['items'] as $item){
                                echo '<tr></tr>';
                            }
                        }
                        if(isset($order['items']) && is_array($order['items']) && count($order['items']) > 0){
                            $count = 0;
                            $total_amount = 0;
                            foreach($order['items'] as $item){
                                $discount_label = '';
                                if($item['order_discount_price'] > 0 && $item['order_discount_price'] < $item['order_price']){
                                    $price = $item['order_discount_price'];
                                    $discount_label = ' <span class="rm-discount-percent label label-warning">-'. ceil(($item['order_price']-$item['order_discount_price'])*100/$item['order_price']) .'%</span>';
                                }else{
                                    $price = $item['order_price'];
                                }
                                $count++;
                                echo '<tr>
                                        <td>'. $count .'</td>
                                        <td><div class="rm-cart-item-photo"><img src="'. $item['order_photo'] .'" width="50" height="50" /> '. htmlspecialchars(str_replace('+',' ', $item['name'])) .'</div></td>
                                        <td>'. ($item['order_discount_price'] > 0? Mava_String::price_format($item['order_discount_price']) .'<div class="text-muted"><s>'. Mava_String::price_format($item['order_price']) .'</s>'. $discount_label .'</div>':Mava_String::price_format($item['order_price'])) .'</td>
                                        <td>'. $item['order_quantity'] .'</td>
                                        <td>'. Mava_String::price_format($item['order_quantity'] * $price) .'</td>
                                        </tr>';
                                $total_amount += $item['order_quantity'] * $price;
                            }
                            echo '<tr><td colspan="10" align="right"><b>'. __('total_amount') .': </b><b class="rm-cart-total-amount">'. Mava_String::price_format($total_amount) .'</b></td></tr>';
                        }else{
                            echo '<tr><td colspan="10"><div class="text-center rm-no-cart-item">'. __('no_product_in_cart') .'</div></td></tr>';
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>