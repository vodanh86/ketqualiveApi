<div class="xs-box">
    <div class="xs-box-head">Thống kê tần suất</div>
    <div class="xs-box-body">
        <form action="<?php echo Mava_Url::getPageLink('thong-ke-tan-suat-lo'); ?>" method="GET" onsubmit="return showTanSuatLoResult();">
            <div class="row">
                <div class="col-md-3">
                    <label for="xs_thongketansuatlo_province">Chọn tỉnh</label>
                    <select class="form-control form-control-sm" id="xs_thongketansuatlo_province">
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
                <div class="col-md-3">
                    <label for="xs_thongketansuatlo_boso">Chọn bộ số</label>
                    <select class="form-control form-control-sm" id="xs_thongketansuatlo_boso">
                        <option <?php echo ($typeInt == 1) ? 'selected' : '' ?> value="1" data-slug='tat-ca'>Tất cả</option>
                        <option <?php echo ($typeInt == 2) ? 'selected' : '' ?> value="2" data-slug='dau'>Đầu</option>
                        <option <?php echo ($typeInt == 3) ? 'selected' : '' ?> value="3" data-slug='duoi'>Đuôi</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="xs_thongketansuatlo_biendo">Biên độ</label>
                    <select class="form-control form-control-sm" id="xs_thongketansuatlo_biendo">
                        <option <?php echo ($volumn == 10) ? 'selected' : '' ?> value="10">10</option>
                        <option <?php echo ($volumn == 30) ? 'selected' : '' ?> value="30">30</option>
                        <option <?php echo ($volumn == 50) ? 'selected' : '' ?> value="50">50</option>
                        <option <?php echo ($volumn == 100) ? 'selected' : '' ?> value="100">100</option>
                        <option <?php echo ($volumn == 300) ? 'selected' : '' ?> value="300">300</option>
                        <option <?php echo ($volumn == 500) ? 'selected' : '' ?> value="500">500</option>
                    </select>
                </div>
                <div class="col-md-3">
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
                    echo '<div class="alert alert-warning"><b>Hướng dẫn:</b> Chọn tỉnh thành,bộ số,biên độ muốn xem thống kê sau đó bấm vào nút "Xem kết quả". Biên độ là 10 có nghĩa là thống kê tần suất trong 10 ngày gần nhất.</div>';
                }
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    function showTanSuatLoResult(){
        window.location.href = DOMAIN +'/thong-ke-tan-suat-lo-'+ $('#xs_thongketansuatlo_province option:selected').attr('data-slug') + '-bo-so-' + $('#xs_thongketansuatlo_boso option:selected').attr('data-slug') + '-voi-bien-do-' + $('#xs_thongketansuatlo_biendo option:selected').val()
        return false;
    }
</script>