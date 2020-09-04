<div class="xs-box">
    <div class="xs-box-head">Thống kê lô rơi</div>
    <div class="xs-box-body">
        <form action="<?php echo Mava_Url::getPageLink('thong-ke-lo-roi'); ?>" method="GET" onsubmit="return showLoRoiResult();">
            <div class="row">
                <div class="col-md-6">
                    <label for="xs_thongkeloroi_province">Chọn tỉnh</label>
                    <select class="form-control form-control-sm" id="xs_thongkeloroi_province">
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
    <div class="xs-box-head">Kết quả thống kê</div>
    <div class="xs-box-body">
        <div id="xs_stats_result">
            <?php
                if(isset($result_html) && $result_html != ""){
                    echo $result_html;                    
                }else{
                    echo '<div class="alert alert-warning"><b>Hướng dẫn:</b> Chọn tỉnh thành muốn xem thống kê, sau đó bấm vào nút "Xem kết quả". Lô rơi là lô ra ngày liên tiếp.</div>';
                }
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    function showLoRoiResult(){
        window.location.href = DOMAIN +'/thong-ke-lo-roi-'+ $('#xs_thongkeloroi_province option:selected').attr('data-slug')
        return false;
    }
</script>