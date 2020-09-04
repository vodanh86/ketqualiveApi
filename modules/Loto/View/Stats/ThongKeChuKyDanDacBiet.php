<div class="xs-box">
    <div class="xs-box-head">Thống kê chu kỳ dàn đặc biệt</div>
    <div class="xs-box-body">
        <form action="<?php echo Mava_Url::getPageLink('thong-ke-chu-ky-dan-dac-biet'); ?>" method="GET" onsubmit="return showResult();">
            <div class="row">
                <div class="col-md-12">
                    <label for="xs_thongkechukydandacbiet_nums">Nhập số cần xem (mỗi số cách nhau bởi dấu phảy hoặc dấu cách)</label>
                    <input type="text" class="form-control form-control-sm" name="nums" id="xs_thongkechukydandacbiet_nums" value="<?php echo isset($nums)?implode(', ', $nums):''; ?>" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label>Từ ngày</label>
                    <input type="text" class="form-control form-control-sm xs-date-picker" name="start_time" data-pair="xs_thongkechukydandacbiet_end" id="xs_thongkechukydandacbiet_start" value="<?php echo isset($start_time)?htmlspecialchars($start_time):date('d-m-Y', time() - 86400*30); ?>" />
                </div>
                <div class="col-md-4">
                    <label>Đến ngày</label>
                    <input type="text" class="form-control form-control-sm xs-date-picker" name="end_time" id="xs_thongkechukydandacbiet_end" value="<?php echo isset($end_time)?htmlspecialchars($end_time):date('d-m-Y'); ?>" />
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
        if(isset($nums) && $nums != ""){
            echo '<div class="alert alert-danger">Chưa có dữ liệu thống kê</div>';
        }else{ ?>
        <div id="xs_stats_result">
            <?php
                if(isset($result_html) && $result_html != ""){
                    echo $result_html;                    
                }else{
                    echo '<div class="alert alert-warning"><b>Hướng dẫn:</b> Nhập các số muốn thống kê chu kỳ gan, mỗi cặp số ngăn cách nhau bởi dấu cách hoặc dấu chấm phảy. Ví dụ: 23,67,21</div>';
                }
            ?>
        </div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
    function showResult(){
        var nums_input = $('#xs_thongkechukydandacbiet_nums');
        if(nums_input.val() == ""){
            nums_input.focus();     
        }else{
            window.location.href = DOMAIN +'/thong-ke-chu-ky-dan-dac-biet-bo-so-'+ nums_input.val().replace(/(\-|,| )+/g,'-') +'-tu-ngay-'+ $('#xs_thongkechukydandacbiet_start').val().replace(/\//g,'-') +'-den-ngay-'+ $('#xs_thongkechukydandacbiet_end').val().replace(/\//g,'-');
        }
        return false;
    }
</script>