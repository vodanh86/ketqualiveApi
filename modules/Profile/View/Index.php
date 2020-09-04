<div id="profile_page" class="container">
    <div class="row">
        <?php echo Mava_View::getView('Profile_View_Includes_Menu'); ?>
        <div id="profile_right" class="col-md-9">
            <div class="rm-box-border">
                <h5><?php echo __('recent_order') . ' ('. $total_order .')'; ?></h5>
                <?php
                    if(isset($orders) && is_array($orders) && count($orders) > 0){
                ?>      <div class="table-responsive">
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
                        <?php if($total_order > count($orders)){ ?>
                        <div class="text-right">
                            <a href="<?php echo Mava_Url::getPageLink('profile/orders'); ?>" class="btn btn-primary"><?php echo __('see_all_x_order', array('num' => $total_order)); ?></a>
                        </div>
                        <?php } ?>
                <?php
                    }else{
                        echo '<div class="well text-center">'. __('no_order_found') .'</div>';
                    }
                ?>
            </div>
            <div class="rm-box-border">
                <h5><?php echo __('account_information') . ' (<a href="'. Mava_Url::getPageLink('profile/account') .'">'.  __('see_all').'</a>)'; ?></h5>
                <table class="table table-bordered">
                    <tr>
                        <th><?php echo __('user_id'); ?></th>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <th><?php echo __('email'); ?></th>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo __('fullname'); ?></th>
                        <td><?php echo htmlspecialchars($user['custom_title']); ?></td>
                        <th><?php echo __('gender'); ?></th>
                        <td><?php echo __($user['gender']); ?></td>
                    </tr>
                </table>
            </div>
            <div class="rm-box-border">
                <h5><?php echo __('consignee_address'); ?></h5>
                <div class="rm-address-list">
                    <?php
                        if(isset($address) && is_array($address) && count($address) > 0){
                            foreach($address as $item){
                                echo '<div class="rm-address-item"><div class="rm-address-item-inner">
                                        <p><b>'. htmlspecialchars($item['fullname']) .'</b></p>
                                        <p>'. __('phone') .': '.htmlspecialchars($item['phone']) .'</p>
                                        <p>'. __('address') .': '. htmlspecialchars($item['address']) .'</p>
                                        '. ($item['is_default']=='yes'?'<span class="label label-success rm-address-default-label">'. __('default') .'</span>':'') .'
                                        <a href="javascript:void(0);" class="btn btn-default btn-sm" onclick="DH.address.edit(this,'. $item['id'] .');" data-fullname="'. htmlspecialchars($item['fullname']) .'" data-address="'. htmlspecialchars($item['address']) .'" data-phone="'. htmlspecialchars($item['phone']) .'"data-default="'. htmlspecialchars($item['is_default']) .'"><i class="fa fa-edit"></i> '. __('edit') .'</a>
                                        <a href="javascript:void(0);" class="btn btn-default btn-sm" onclick="DH.address.remove(this,'. $item['id'] .');"><i class="fa fa-trash"></i> '. __('delete') .'</a>
                                        '. ($item['is_default'] == 'no'?'<a href="javascript:void(0);" class="btn btn-default btn-sm"  onclick="DH.address.set_default(this,'. $item['id'] .');" data-toggle="tooltip" title="'. __('set_default_address_tooltip') .'">'. __('set_default') .'</a>':'') .'
                                    </div></div>';
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>