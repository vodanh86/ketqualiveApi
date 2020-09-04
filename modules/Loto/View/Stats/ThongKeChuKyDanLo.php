<div class="xs-box">
    <div class="xs-box-head">Thống kê chu kỳ dàn lô</div>
    <div class="xs-box-body">
        <form action="<?php echo Mava_Url::getPageLink('thong-ke-chu-ky-dan-lo'); ?>" method="GET" onsubmit="return showChuKyDanLoResult();">
            <div class="row">
                <div class="col-md-12">
                    <label>Nhập số cần xem (mỗi số cách nhau bởi dấu phảy hoặc dấu cách)</label>
                    <input type="text" class="form-control form-control-sm" name="nums" id="xs_thongkechuky_nums" value="<?php echo isset($nums)?implode(', ', $nums):''; ?>" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label>Từ ngày</label>
                    <input type="text" class="form-control form-control-sm xs-date-picker" name="start_time" data-pair="xs_thongkechukydanloto_end" id="xs_thongkechukydanloto_start" value="<?php echo isset($start_time)?htmlspecialchars($start_time):date('d-m-Y', time() - 86400*30); ?>" />
                </div>
                <div class="col-md-4">
                    <label>Đến ngày</label>
                    <input type="text" class="form-control form-control-sm xs-date-picker" name="end_time" id="xs_thongkechukydanloto_end" value="<?php echo isset($end_time)?htmlspecialchars($end_time):date('d-m-Y'); ?>" />
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
        <div id="xs_stats_result">
            <?php
                if(isset($result_html) && $result_html != ""){
                    echo $result_html;                    
                }else{
                    echo '<div class="alert alert-warning"><b>Hướng dẫn:</b> Thống kê chu kỳ dàn lô là thống kê số ngày nhiều nhất mà các cặp số không về cùng nhau (max gan) trong một khoảng thời gian.</div>';
                }
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    function showChuKyDanLoResult(){
        var nums_input = $('#xs_thongkechuky_nums');
        if(nums_input.val() == ""){
            nums_input.focus();     
        }else{
            window.location.href = DOMAIN +'/thong-ke-chu-ky-dan-lo-to-'+ nums_input.val().replace(/(\-|,| )+/g,'-') +'-tu-ngay-'+ $('#xs_thongkechukydanloto_start').val().replace(/\//g,'-') +'-den-ngay-'+ $('#xs_thongkechukydanloto_end').val().replace(/\//g,'-');
        }
        return false;
    }
</script>