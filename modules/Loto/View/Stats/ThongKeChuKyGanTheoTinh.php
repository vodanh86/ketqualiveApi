<div class="xs-box">
    <div class="xs-box-head">Thống kê chu kỳ gan theo tỉnh</div>
    <div class="xs-box-body">
        <form action="<?php echo Mava_Url::getPageLink('thong-ke-chu-ky-gan-theo-tinh'); ?>" method="GET" onsubmit="return showLoGanTheoTinhResult();">
            <div class="row">
                <div class="col-md-5">
                    <label for="xs_thongkelogantheotinh_nums">Dãy số cần thống kê</label>
                    <input type="text" class="form-control form-control-sm" name="nums" id="xs_thongkelogantheotinh_nums" value="<?php echo isset($nums)?implode(',', $nums):''; ?>" />
                </div>
                <div class="col-md-4">
                    <label for="xs_thongkelogantheotinh_province">Chọn tỉnh</label>
                    <select class="form-control" id="xs_thongkelogantheotinh_province">
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
                    echo '<div class="alert alert-warning"><b>Hướng dẫn:</b> Nhập các bộ số cần kiểm tra, mỗi bộ số ngăn cách nhau bởi dấu phảy hoặc dấu cách.</div>';
                }
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    function showLoGanTheoTinhResult(){
        var nums = $('#xs_thongkelogantheotinh_nums');
        if(nums.val() == ""){
            nums.focus();     
        }else{
            window.location.href = DOMAIN +'/thong-ke-chu-ky-gan-'+ $('#xs_thongkelogantheotinh_province option:selected').attr('data-slug') +'-bo-so-'+ nums.val().replace(/(\-|,| )+/g,'-');
        }
        return false;
    }
</script>