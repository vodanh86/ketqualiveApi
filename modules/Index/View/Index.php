<?php
    $provinces = get_all_province();
    $route = '';
?>
<div class="clearfix xs-result-filter">
    <div class="pull-left">
        <select class="form-control form-control-sm" id="quick_view_result_province">
            <optgroup label="Miền Bắc">
                <?php
                    foreach($provinces['bac'] as $item){
                      echo '<option value="'. $item['code'] .'" data-slug="'. $item['slug'] .'"'. ($province==$item['code']?' selected':'') .'>'. htmlspecialchars($item['title']) .'</option>';
                    } 
                ?>
            </optgroup>
            <optgroup label="Miền Trung">
                <?php
                    foreach($provinces['trung'] as $item){
                         echo '<option value="'. $item['code'] .'" data-slug="'. $item['slug'] .'"'. ($province==$item['code']?' selected':'') .'>'. htmlspecialchars($item['title']) .'</option>';
                    } 
                ?>
            </optgroup>
            <optgroup label="Miền Nam">
                <?php
                    foreach($provinces['nam'] as $item){
                        echo '<option value="'. $item['code'] .'" data-slug="'. $item['slug'] .'"'. ($province==$item['code']?' selected':'') .'>'. htmlspecialchars($item['title']) .'</option>';
                    } 
                ?>
            </optgroup>

        </select>
    </div>
    <div class="pull-right">
        <label>Kết quả: </label>
        <div class="btn-group btn-group-sm">
            <?php
                $date = $day .'-'. $month .'-'. $year;
                if((int)date('H') > 18){
                    echo '<a href="'. Mava_Url::getPageLink('/') .'" class="btn btn-secondary'. ($date==date('d-m-Y')||$date=="--"?' active':'') .'">Hôm nay</a>';
                }
                $today = time();
            ?>
            <a href="javascript:void(0);" onclick="show_result_by_date('<?php echo date('d-m-Y', time()-86400); ?>');" class="btn btn-secondary<?php echo $date==date('d-m-Y', time()-86400)?' active':''; ?>">Hôm qua</a>
            <a href="javascript:void(0);" onclick="show_result_by_date('<?php echo date('d-m-Y', time()-(86400*2)); ?>');"  class="btn btn-secondary<?php echo $date==date('d-m-Y', time()-(86400*2))?' active':''; ?>">Hôm kia</a>
            <a href="javascript:void(0);" data-func="show_result_by_date" class="btn btn-secondary xs-date-picker-button">Khác <i class="fa fa-caret-down"></i></a>
        </div>
    </div>
</div>

<?php
    if($day > 0 && $month > 0 && $year > 0){
        $date = $day .'-'. $month .'-'. $year;
    }else{
        $day = (int)date('d');
        $month = (int)date('m');
        $year = (int)date('Y');
        $date = Mava_Url::getParam('date')!=""?Mava_Url::getParam('date'):((int)date('H') > 18?date('d-m-Y', time()):date('d-m-Y', $today-86400));
    }
    
    if($province == ""){
        $province = 'tt';    
    }
    echo '<input type="hidden" id="quick_view_result_day" value="'. $day .'" />';
    echo '<input type="hidden" id="quick_view_result_month" value="'. $month .'" />';
    echo '<input type="hidden" id="quick_view_result_year" value="'. $year .'" />';
    echo '<input type="hidden" id="quick_view_result_date" value="'. $date .'" />';
    echo '<div id="loto_tt_result">'.get_result_home_html($date, $province).'</div>';
?>
<div class="xs-home-center-ads"><img src="http://img.ketqua.net/images/2018/01/16/10279b5fc3a0fc1ae13b043bc9954bf3.gif" /></div>
<!-- facebook comment -->
<div class="xs-box">
    <div class="xs-box-head">Bình luận</div>
    <div class="xs-box-body">
        <div class="fb-comments" data-href="<?php echo Mava_Url::getPageLink('/', array('date' => date('d-m-Y'))); ?>" data-width="100%" data-numposts="5"></div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var day = new Date().getDate();
        var month = new Date().getMonth() + 1;
        var year = new Date().getFullYear();

        var cur_day = $('#quick_view_result_day').val();
        var cur_month = $('#quick_view_result_month').val();
        var cur_year = $('#quick_view_result_year').val();

        if(parseInt(cur_day) === day && parseInt(cur_month) === month && parseInt(cur_year) === year){
            setInterval(callAjax, 60000);
        }

        function callAjax() {
            var minute = new Date().getMinutes();
            var hour = new Date().getHours();
            if(hour === 18 && minute <= 30){
                setInterval(getLastestLotoResult, 60000);
            }
        }

        function getLastestLotoResult() {
            $.ajax({
                url: DOMAIN  + '/get-latest-loto-result?pv=' + $('#quick_view_result_province option:selected').val(),
                success: function(res){
                    $("#loto_tt_result").html(res.result);
                },
                error: function (e) {
                    window.location.href = DOMAIN
                }
            });
        }

    })
</script>