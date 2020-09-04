<div class="xs-box">
    <div class="xs-box-head">Soi cầu theo tỉnh</div>
    <div class="xs-box-body">
        <form action="<?php echo Mava_Url::getPageLink('soi-cau-theo-tinh'); ?>" method="GET" onsubmit="return showSoiCauTheoTinhResult();">
            <div class="row">
                <div class="col-md-4">
                    <label for="xs_find_date">Chọn ngày</label>
                    <input type="text" data-func="show_result_province_by_date" class="form-control form-control-sm xs-date-picker" id="xs_find_date" placeholder="Chọn ngày" value="<?php echo $date ?>" />
                </div>
                <div class="col-md-4">
                    <label for="xs_result_province">Chọn tỉnh</label>
                    <select class="form-control form-control-sm" id="xs_result_province">
                        <?php foreach($current_loto as $v) {?>
                            <option <?php echo ($v[3]==$province_slug)? 'selected' : '' ?> data-slug="<?php echo $v[3] ?>"><?php echo $v[2] ?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-md-4">
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
                    echo '<div class="alert alert-warning"><b>Hướng dẫn:</b> Chọn ngày và tỉnh thành muốn soi sau đó bấm vào nút "Xem kết quả".</div>';
                }
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    function showSoiCauTheoTinhResult(){
        window.location.href = DOMAIN +'/soi-cau-theo-tinh-' + $('#xs_result_province option:selected').attr('data-slug') + '-ngay-' + $('#xs_find_date').val()
        return false;
    }
</script>