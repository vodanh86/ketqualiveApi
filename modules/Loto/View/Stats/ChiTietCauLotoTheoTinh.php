<div class="xs-box">
    <div class="xs-box-head">Chi tiết cầu Lô tô</div>
    <div class="xs-box-body">
        <p>Chi tiết cầu lô tô <?php echo $province['title'] ?> ngày <?php echo $date ?> tại vị trí <span class="red bold"><?php echo $position_1.'-'.$position_2 ?></span></p>
        <p>Theo cầu này dự đoán ngày <?php echo $date ?> sẽ về
            <span class="red bold"><?php echo $number ?></span>
            <?php if(substr($number, 1, 1) != substr($number, 0, 1)){
                echo 'hoặc <span class="red bold">'. substr($number, 1, 1).substr($number, 0, 1).'</span>';
            } ?>
        </p>
        <?php echo $result_html; ?>
    </div>
</div>