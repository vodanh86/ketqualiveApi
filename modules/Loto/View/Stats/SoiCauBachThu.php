<div class="xs-box">
    <div class="xs-box-head">Soi cầu truyền thống - bạch thủ</div>
    <div class="xs-box-body">
        <form action="<?php echo Mava_Url::getPageLink('soi-cau-bach-thu'); ?>" method="GET" onsubmit="return showSoiCaubachThuResult();">
            <div class="row">
                <div class="col-md-6">
                    <label for="xs_find_date">Chọn ngày</label>
                    <input type="text" data-func="show_result_province_by_date" class="form-control form-control-sm xs-date-picker" id="xs_find_date" placeholder="Chọn ngày" value="<?php echo ($date != '' ? $date : $latest_date) ?>" />
                </div>
                <div class="col-md-6">
                    <label>&nbsp;</label>
                    <div><button type="submit" class="btn btn-info btn-sm btn-block">Xem kết quả</button></div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php if($messageError != ''){
    echo '<div class="xs-box">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            '.$messageError.'
        </div>
    </div>';
} ?>
<div class="xs-box xs-box-red">
    <div class="xs-box-head">Kết quả</div>
    <div class="xs-box-body">
        <div id="xs_stats_result">
            <?php
                if(isset($result_html) && $result_html != ""){
                    echo $result_html;                    
                }else{
                    echo '<div class="alert alert-warning"><b>Hướng dẫn:</b> Chọn ngày muốn soi sau đó bấm vào nút "Xem kết quả".</div>';
                }
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    function showSoiCaubachThuResult(){
        window.location.href = DOMAIN +'/soi-cau-bach-thu-ngay-' + $('#xs_find_date').val()
        return false;
    }
</script>