<div class="container-fluid">
    <div class="mbd-box">
        <div class="mbd-head clearfix">
            <div class="mbd-head-title pull-left"><?php echo __('notifications') .' ('. number_format($total, 0, '.', ',') .')'. ($page > 1?' - '. __('page_x', array('num' => $page)):''); ?></div>
            <!--<a href="javascript:void(0);" class="pull-right md-mark-all-as-read"><i class="fa fa-check"></i> <?php /*echo __('mark_all_as_read'); */?></a>-->
        </div>
        <div class="mbd-body">
            <ul class="mbd-notify-list">
                <?php
                if(isset($notifications) && is_array($notifications) && count($notifications) > 0){
                    foreach($notifications as $item){
                        echo '<li class="'. ($item['has_read']==1?'mbd-read':'mbd-unread') .'"><a href="'. $item['link'] .'">'. $item['text'] .'<span class="mbd-time">'. date('d/m/Y H:i:s', $item['created_date']) .'</span></a></li>';
                    }
                }else{
                    echo '<li class="mbd-no-notify"><p>'. __('no_notify_found') .'</li>';
                }
                ?>
            </ul>
            <div class="text-right">
                <?php
                echo Mava_View::buildPagination(
                    Mava_Url::getPageLink('dashboard/'. $agency['id'] .'/notifications'),
                    ceil($total/$limit),
                    $page
                );
                ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('.md-mark-all-as-read').click(function(){
            MV.post(DOMAIN +'/dashboard/<?php echo $agency['id']; ?>/mark-notify-as-read', {}, function(res){
                $('.mbd-notify-menu .badge').html('').hide();
                $('.mbd-notify-list .mbd-unread').removeClass('mbd-unread').addClass('mbd-read');
                MP.notice.show(res.message, 'warning', 2, 'top');
            });
        });
    });
</script>