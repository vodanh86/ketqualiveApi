<div id="profile_page" class="container">
    <div class="row">
        <?php echo Mava_View::getView('Profile_View_Includes_Menu'); ?>
        <div id="profile_right" class="col-md-9">
            <div class="rm-box-border">
                <h4><?php echo __('my_orders') . ' ('. $total_order .')'; ?></h4>
                <?php
                if(isset($orders) && is_array($orders) && count($orders) > 0){
                    ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th><?php echo __('order_id'); ?></th>
                            <th width="300"><?php echo __('product'); ?></th>
                            <th><?php echo __('total_amount'); ?></th>
                            <th><?php echo __('order_time'); ?></th>
                            <th><?php echo __('status'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach($orders as $item){
                            $product = '<ul>';
                            if(isset($item['products']) && is_array($item['products']) && count($item['products']) > 0){
                                foreach($item['products'] as $prod){
                                    $product .= '<li><b>'. $prod['quantity'] .' x </b>'. htmlspecialchars($prod['title']) .'</li>';
                                }
                            }
                            $product .= '</ul>';
                            echo '<tr>
                                        <td>'. $item['id'] .'</td>
                                        <td>'. $product .'</td>
                                        <td>'. Mava_String::price_format($item['total_amount']) .'</td>
                                        <td>'. date('d/m/Y H:i', $item['created_time']) .'</td>
                                        <td>'. __('order_status_member_'. $item['status']) .'</td>
                                        <td><a href="'. Mava_Url::getPageLink('profile/order-detail', array('id' => $item['id'])) .'">'. __('view_detail') .'</a></td>
                                        </tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php
                    echo '<div class="text-right">'. Mava_View::buildPagination(Mava_Url::getPageLink('profile/orders'), ceil($total_order/$limit), $page) .'</div>';
                }else{
                    echo '<div class="well text-center">'. __('no_order_found') .'</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>