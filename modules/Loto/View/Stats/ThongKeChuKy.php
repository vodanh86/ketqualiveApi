<div class="xs-box">
    <div class="xs-box-head">Thống kê chu kỳ</div>
    <div class="xs-box-body">
        <form action="<?php echo Mava_Url::getPageLink('thong-ke-chu-ky'); ?>" method="GET" onsubmit="return showLoGanResult();">
            <div class="row">
                <div class="col-md-9">
                    <label>Nhập số cần xem (mỗi số cách nhau bởi dấu phảy hoặc dấu cách)</label>
                    <input type="text" class="form-control form-control-sm" name="nums" data-pair="xs_thongkelogan_end" id="xs_thongkechuky_nums" value="<?php echo isset($nums)?implode(', ', $nums):''; ?>" />
                </div>
                <div class="col-md-3">
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
                    echo '<div class="alert alert-warning"><b>Hướng dẫn:</b> Nhập các số muốn thống kê chu kỳ gan, mỗi cặp số ngăn cách nhau bởi dấu cách hoặc dấu chấm phảy. Ví dụ: 23,67,21</div>';
                }
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    function showLoGanResult(){
        var nums_input = $('#xs_thongkechuky_nums');
        if(nums_input.val() == ""){
            nums_input.focus();     
        }else{
            window.location.href = DOMAIN +'/thong-ke-chu-ky-bo-so-'+ nums_input.val().replace(/(\-|,| )+/g,'-');
        }
        return false;
    }
</script>