<div class="xs-box">
    <div class="xs-box-head">Thống kê lô về nhiều - về ít</div>
    <div class="xs-box-body">
        <form action="<?php echo Mava_Url::getPageLink('thong-ke-lo-ve-nhieu-ve-it'); ?>" method="GET" onsubmit="return showLoVeNhieuVeItResult();">
            <div class="row">
                <div class="col-md-3">
                    <label for="xs_thongkelovenhieuveit_province">Chọn tỉnh</label>
                    <select class="form-control form-control-sm" id="xs_thongkelovenhieuveit_province">
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
                    <label for="xs_thongkelovenhieuveit_type">Loại thống kê</label>
                    <select class="form-control form-control-sm" id="xs_thongkelovenhieuveit_type">
                        <option <?php echo ($typeInt == 1) ? 'selected' : '' ?> value="1" data-slug='ve-it'>Về ít</option>
                        <option <?php echo ($typeInt == 2) ? 'selected' : '' ?> value="2" data-slug='ve-nhieu'>Về nhiều</option>
                        <option <?php echo ($typeInt == 3) ? 'selected' : '' ?> value="3" data-slug='chua-ve'>Chưa về</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="xs_thongkelovenhieuveit_biendo">Biên độ</label>
                    <select class="form-control form-control-sm" id="xs_thongkelovenhieuveit_biendo">
                        <option <?php echo ($volumn == 10) ? 'selected' : '' ?> value="10">10</option>
                        <option <?php echo ($volumn == 30) ? 'selected' : '' ?> value="30">30</option>
                        <option <?php echo ($volumn == 60) ? 'selected' : '' ?> value="60">60</option>
                        <option <?php echo ($volumn == 100) ? 'selected' : '' ?> value="100">100</option>
                        <option <?php echo ($volumn == 365) ? 'selected' : '' ?> value="365">365</option>
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
                    echo '<div class="alert alert-warning"><b>Hướng dẫn:</b> Chọn tỉnh thành,loại thống kê,biên độ muốn xem thống kê sau đó bấm vào nút "Xem kết quả". Biên độ là 10 có nghĩa là thống kê trong 10 ngày gần nhất.</div>';
                }
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    function showLoVeNhieuVeItResult(){
        window.location.href = DOMAIN +'/thong-ke-lo-' + $('#xs_thongkelovenhieuveit_province option:selected').attr('data-slug') + '-loai-' + $('#xs_thongkelovenhieuveit_type option:selected').attr('data-slug') + '-voi-bien-do-' + $('#xs_thongkelovenhieuveit_biendo option:selected').val()
        return false;
    }
</script>