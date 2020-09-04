<div class="xs-box">
    <div class="xs-box-head">DÒ VÉ SỐ - TRA CỨU KẾT QUẢ XỔ SỐ</div>
    <div class="xs-box-body">
        <form action="" method="GET" onsubmit="return showDoVeSoResult();">
            <div class="row">
                <div class="col-md-3">
                    <label for="xs_find_date">Chọn ngày</label>
                    <input type="text" data-func="show_result_province_by_date" class="form-control form-control-sm xs-date-picker" id="xs_find_date" placeholder="Chọn ngày" value="<?php echo $date ?>" />
                </div>
                <div class="col-md-3">
                    <label for="xs_result_province">Chọn tỉnh</label>
                    <select class="form-control form-control-sm" id="xs_result_province">
                        <?php foreach($current_loto as $v) {?>
                            <option <?php echo ($v[3]==$province_slug)? 'selected' : '' ?> data-slug="<?php echo $v[3] ?>"><?php echo $v[2] ?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="xs_find_number">Nhập số</label>
                    <input type="text" class="form-control form-control-sm" id="xs_find_number" placeholder="Nhập số" value="<?php echo $number ?>"/>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <div><button type="submit" class="btn btn-info btn-sm btn-block">Xem kết quả</button></div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php echo $result_html; ?>

<script type="text/javascript">
    function showDoVeSoResult(){
        if(!$('#xs_find_number').val()){
            $('#xs_find_number').focus();
            return false;
        }
        window.location.href = DOMAIN +'/do-ve-so-' + $('#xs_result_province option:selected').attr('data-slug') + '-ngay-' + $('#xs_find_date').val() + '-so-' + $('#xs_find_number').val();
        return false;
    }
</script>