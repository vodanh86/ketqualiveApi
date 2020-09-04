<?php
    $now= date('d-m-Y', time());
    $day = date('w',date_to_time($now, '-')) + 1;
    $today_loto = Mava_Application::getConfig('loto_schedule/T'. $day);
    $tt = $today_loto['tt'];
    unset($today_loto['tt']);
    array_unshift($today_loto, $tt);
?>
<div class="xs-box">
    <div class="xs-box-head">Dò vé số</div>
    <div class="xs-box-body">
        <form action="" method="GET" onsubmit="return showDoVeSoResult();">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" data-func="show_result_province_by_date" class="form-control form-control-sm xs-date-picker" id="xs_find_date" placeholder="Chọn ngày" value="<?php echo $now ?>" />
                </div>
                <div class="col-md-6">
                    <select class="form-control form-control-sm" id="xs_result_province">
                        <?php foreach($today_loto as $k=>$v) {?>
                            <option data-slug="<?php echo $v[3] ?>"><?php echo $v[2] ?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <input type="text" class="form-control form-control-sm" id="xs_find_number" placeholder="Nhập số" />
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-info btn-sm btn-block" id="xs_find_submit">Xem kết quả</button>
                </div>
            </div>
        </form>
    </div>
</div>

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