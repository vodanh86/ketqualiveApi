<div class="xs-box">
    <div class="xs-box-head">Thống kê giải đặc biệt</div>
    <div class="xs-box-body">
        <form action="<?php echo Mava_Url::getPageLink('thong-ke-giai-dac-biet'); ?>" method="GET" onsubmit="return showGiaiDacBietResult();">
            <div class="row">
                <div class="col-md-6">
                    <label for="xs_thongkegiaidacbiet_province">Chọn tỉnh</label>
                    <select class="form-control form-control-sm" id="xs_thongkegiaidacbiet_province">
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
            <?php if($result_html['result_sum_html'] == '' && $result_html['result_equal_html'] == '' && $result_html['result_all_html'] == '' && $result_html['result_recently_html'] == ''){?>
                <div class="alert alert-warning"><b>Hướng dẫn:</b> Chọn tỉnh thành muốn xem thống kê, sau đó bấm vào nút "Xem kết quả".</div>
            <?php } else { ?>
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#sum">Tổng</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#equal">Chạm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#all">ĐB lâu ra</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#recently">Bảng ĐB</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="sum" class="tab-pane active">
                    <?php  echo $result_html['result_sum_html']; ?>
                </div>
                <div id="equal" class="tab-pane fade">
                    <?php  echo $result_html['result_equal_html']; ?>
                </div>
                <div id="all" class="tab-pane fade">
                    <?php  echo $result_html['result_all_html']; ?>
                </div>
                <div id="recently" class="tab-pane fade">
                    <?php  echo $result_html['result_recently_html']; ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(".nav-tabs a").click(function(){
            $(this).tab('show');
        });
    });
    function showGiaiDacBietResult(){
        window.location.href = DOMAIN +'/thong-ke-giai-dac-biet-'+ $('#xs_thongkegiaidacbiet_province option:selected').attr('data-slug')
        return false;
    }
</script>