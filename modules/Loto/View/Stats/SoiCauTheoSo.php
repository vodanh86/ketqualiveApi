<div class="xs-box">
    <div class="xs-box-head">Soi cầu theo số</div>
    <div class="xs-box-body">
        <form action="<?php echo Mava_Url::getPageLink('soi-cau-theo-so'); ?>" method="GET" onsubmit="return showSoiCauTheoSoResult();">
            <div class="row">
                <div class="col-md-4">
                    <label for="xs_soicautheoso_province">Tỉnh thành</label>
                    <select class="form-control form-control-sm">
                        <option>Truyền thống</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="xs_soicautheoso_number">Nhập số</label>
                    <input type="text" class="form-control form-control-sm" id="xs_soicautheoso_number" placeholder="Nhập số" value="<?php echo $number ?>"/>
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
                    echo '<div class="alert alert-warning"><b>Hướng dẫn:</b> Chọn số muốn soi sau đó bấm vào nút "Xem kết quả".</div>';
                }
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    function showSoiCauTheoSoResult(){
        if($('#xs_soicautheoso_number').val() == ''){
            $('#xs_soicautheoso_number').focus();
            return false;
        }
        window.location.href = DOMAIN +'/soi-cau-theo-so-'+ $('#xs_soicautheoso_number').val()
        return false;
    }
</script>