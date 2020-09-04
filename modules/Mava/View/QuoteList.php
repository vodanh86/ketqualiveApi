<h2 class="ms-box-title"><a href="<?php echo Mava_Url::getPageLink('raq'); ?>">Danh sách yêu cầu báo giá<?php echo " - ". $page; ?></a></h2>
<div class="ms-quote-list-wrap">
<table class="ms-quote-list">
    <tr>
        <th>STT</th>
        <th class="nowrap">Họ tên/Điện thoại</th>
        <th>Mô tả</th>
    </tr>
    <?php
        if(is_array($quotes) && count($quotes) > 0){
            $count = ($page-1)*$limit;
            foreach($quotes as $item){
                $count++;
                echo '<tr>
        <td>'. $count .'</td>
        <td><div class="nowrap"><b>'. date('d/m/y H:i', $item['created_date']) .'</b></div>'. htmlspecialchars($item['fullname']) .'<p>'. htmlspecialchars($item['phone']) . '</p></td>
        <td><div style="width: 500px;background: #fff7c3;padding: 20px;">' . nl2br(htmlspecialchars($item['description'])) .'</div></td>
    </tr>';
            }
        }else{
            echo '<tr><td colspan="10">Chưa có yêu cầu nào!</td></tr>';
        }
    ?>
</table>
</div>
<div class="margin-top-bottom-10">
<?php
    if($page > 1){
        echo '<a href="'. Mava_Url::getPageLink('raq', array('page'=>$page-1)) .'" class="btn_blue">« Trang '. ($page-1) .'</a> ';
    }
    if(count($quotes) == $limit){
        echo ' <a href="'. Mava_Url::getPageLink('raq', array('page'=>$page+1)) .'" class="btn_blue">Trang '. ($page+1) .' »</a>';
    }
?>
</div>