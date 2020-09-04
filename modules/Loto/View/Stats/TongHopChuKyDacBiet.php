<div class="xs-box">
    <div class="xs-box-head">Tổng hợp chu kỳ đặc biệt</div>
    <div class="xs-box-body">
        <form action="<?php echo Mava_Url::getPageLink('tong-hop-chu-dac-biet'); ?>" method="GET" onsubmit="return showLoGanResult();">
            <div class="row">
                   <div class="col-md-8">
                    <label for="xs_tonghopchukydacbiet_start">Biên độ ngày</label>
                    <input type="text" class="form-control form-control-sm xs-date-picker" name="start_time" id="xs_tonghopchukydacbiet_start" value="<?php echo isset($start_time)?htmlspecialchars($start_time):date('d-m-Y', time() - 86400*30); ?>" />
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <div><button type="submit" class="btn btn-info btn-sm btn-block">Xem kết quả</button></div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="xs-box xs-box-red">
    <div class="xs-box-head">Kết quả thống kê</div>
    <div class="xs-box-body">
        <?php 
        if(isset($start_time) && $start_time != ""){
            echo '<div class="alert alert-danger">Chưa có dữ liệu thống kê</div>';
        }else{ ?>
        <div id="xs_stats_result">
            <?php
                if(isset($result_html) && $result_html != ""){
                    echo $result_html;                    
                }else{
                    echo '<div class="alert alert-warning"><b>Hướng dẫn:</b> Biên độ ngày là ngày bắt đầu thực hiện tổng hợp chu kỳ</div>';
                }
            ?>
        </div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
    function showLoGanResult(){
        var start_input = $('#xs_tonghopchukydacbiet_start');
        if(start_input.val() == ""){
            start_input.focus();     
        }else{
            window.location.href = DOMAIN +'/tong-hop-chu-ky-dac-biet-tu-ngay-'+ start_input.val().replace(/(\/)+/g,'-');
        }
        return false;
    }
</script>