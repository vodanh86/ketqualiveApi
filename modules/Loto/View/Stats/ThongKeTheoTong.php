<div class="xs-box">
    <div class="xs-box-head">Thống kê tổng số</div>
    <div class="xs-box-body">
        <form action="<?php echo Mava_Url::getPageLink('thong-ke-thoe-tong'); ?>" method="GET" onsubmit="return showThongKeTheoTongResult();">
            <div class="row">
                <div class="col-md-4">
                    <label>Từ ngày</label>
                    <input type="text" class="form-control form-control-sm xs-date-picker" name="start_time" id="xs_thongketheotong_start" value="<?php echo isset($start_time)?htmlspecialchars($start_time):date('d-m-Y', time() - 86400*10); ?>" />
                </div>
                <div class="col-md-4">
                    <label>Đến ngày</label>
                    <input type="text" class="form-control form-control-sm xs-date-picker" name="end_time" id="xs_thongketheotong_end" value="<?php echo isset($end_time)?htmlspecialchars($end_time):date('d-m-Y'); ?>" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="xs_thongketheotong_province">Chọn tỉnh</label>
                    <select class="form-control form-control-sm" id="xs_thongketheotong_province">
                        <?php
                        $all_province = get_all_province();
                        if(is_array($all_province['bac']) && count($all_province['bac']) > 0){
                            foreach($all_province['bac'] as $item){
                                echo '<option value="'. (int)$item['id'] .'" '. (isset($province_id) && $province_id == $item['id']?' selected':'') .' data-slug="'. str_replace('ket-qua-xo-so-','',$item['slug']) .'">'. htmlspecialchars($item['title']) .'</option>';
                            }
                        }
                        if(is_array($all_province['trung']) && count($all_province['trung']) > 0){
                            foreach($all_province['trung'] as $item){
                                echo '<option value="'. (int)$item['id'] .'" '. (isset($province_id) && $province_id == $item['id']?' selected':'') .' data-slug="'. str_replace('ket-qua-xo-so-','',$item['slug']) .'">'. htmlspecialchars($item['title']) .'</option>';
                            }
                        }
                        if(is_array($all_province['nam']) && count($all_province['nam']) > 0){
                            foreach($all_province['nam'] as $item){
                                echo '<option value="'. (int)$item['id'] .'" '. (isset($province_id) && $province_id == $item['id']?' selected':'') .' data-slug="'. str_replace('ket-qua-xo-so-','',$item['slug']) .'">'. htmlspecialchars($item['title']) .'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="xs_thongketheotong_sum">Tổng</label>
                    <select class="form-control form-control-sm" id="xs_thongketheotong_sum">
                        <option <?php echo ($sum == 0) ? 'selected' : '' ?> value="0">0</option>
                        <option <?php echo ($sum == 1) ? 'selected' : '' ?> value="1">1</option>
                        <option <?php echo ($sum == 2) ? 'selected' : '' ?> value="2">2</option>
                        <option <?php echo ($sum == 3) ? 'selected' : '' ?> value="3">3</option>
                        <option <?php echo ($sum == 4) ? 'selected' : '' ?> value="4">4</option>
                        <option <?php echo ($sum == 5) ? 'selected' : '' ?> value="5">5</option>
                        <option <?php echo ($sum == 6) ? 'selected' : '' ?> value="6">6</option>
                        <option <?php echo ($sum == 7) ? 'selected' : '' ?> value="7">7</option>
                        <option <?php echo ($sum == 8) ? 'selected' : '' ?> value="8">8</option>
                        <option <?php echo ($sum == 9) ? 'selected' : '' ?> value="9">9</option>
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
    <div class="xs-box-head">Kết quả thống kê</div>
    <div class="xs-box-body">
        <div id="xs_stats_result">
            <?php
                if(isset($result_html) && $result_html != ""){
                    echo $result_html;                    
                }else{
                    echo '<div class="alert alert-warning"><b>Hướng dẫn:</b> Chọn khoảng thời gian, tỉnh thành, tổng muốn xem thống kê, sau đó bấm vào nút "Xem kết quả". Tổng = 0 có nghĩa là sẽ thống kê các số mà có tổng 2 chữ số tận cùng = 0</div>';
                }
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    function showThongKeTheoTongResult(){
        window.location.href = DOMAIN +'/thong-ke-theo-tong-' + $('#xs_thongketheotong_province option:selected').attr('data-slug') + '-tu-ngay-' + $('#xs_thongketheotong_start').val().replace(/\//g,'-') + '-den-ngay-' + $('#xs_thongketheotong_end').val().replace(/\//g,'-') + '-voi-tong-' + $('#xs_thongketheotong_sum option:selected').val();
        return false;
    }
</script>