<div class="container-fluid">
    <div class="mbd-box">
        <div class="mbd-head">
            <div class="mbd-head-title">
                <?php echo __('link_stats') .' ('. number_format($total, 0, '.', ',') .')'. ($page > 1?' - '. __('page_x', array('num' => $page)):''); ?>
                <span data-toggle="tooltip" data-placement="right" title="<?php echo __('link_stats_tooltip'); ?>"><i class="fa fa-question-circle"></i></span>
            </div>
        </div>
        <div class="mbd-body">
            <table class="table">
                <thead>
                <tr>
                    <th width="250"><?php echo __('link'); ?></th>
                    <th width="150" class="text-right"><?php echo __('click_count'); ?></th>
                    <th width="150" class="text-right"><?php echo __('orders'); ?></th>
                    <th width="150" class="text-right"><?php echo __('total_amount'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(isset($links) && is_array($links) && count($links) > 0){
                    foreach($links as $item){
                        echo '<tr>
                            <td><a href="'. $item['link'] .'" target="_blank">'. htmlspecialchars($item['link']) .'</a></td>
                            <td align="right">'. number_format($item['visitor'], 0, ',', '.') .'</td>
                            <td align="right">'. number_format($item['order_count'], 0, ',', '.') .'</td>
                            <td align="right">'. Mava_String::price_format($item['total_revenue']) .'</td>
                         </tr>';
                    }
                }else{
                    echo '<tr><td colspan="10"><div class="text-center alert alert-warning">'. __('no_link_found') .'</div></td></tr>';
                }
                ?>
                </tbody>
            </table>
            <div class="text-right">
                <?php
                echo Mava_View::buildPagination(
                    Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/links'),
                    ceil($total/$limit),
                    $page
                );
                ?>
            </div>
        </div>
    </div>
</div>