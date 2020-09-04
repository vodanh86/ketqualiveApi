<div class="mb-view-video">
    <form action="<?php echo Mava_Url::getPageLink('video-kem-sach'); ?>" method="post">
    <h3 class="text-center mb-title"><?php echo __('view_video_attach_title'); ?></h3>
    <p class="text-center text-muted"><?php echo __('view_video_attach_sapo'); ?></p>

    <div class="mb-view-video-form">
        <?php
            if(isset($error) && $error != ""){
                echo '<div class="alert alert-danger">'. $error .'</div>';
            }
        ?>
        <div class="input-group">
            <input type="text" class="form-control" name="codeAttach" id="codeAttach" />
            <div class="input-group-btn">
                <button type="submit" class="btn btn-info"><?php echo __('view_video'); ?></button>
            </div>
        </div>
    </div>
    </form>
</div>

<?php
    if(isset($products) && is_array($products) && count($products) > 0){
        echo '<div class="container"><div class="mb-video-recent-activated">
            <h3 class="mb-title">'. __('product_video_recent_activated') .'</h3>
            <div class="mb-list clearfix">';
        foreach($products as $item){
            echo '<div class="mb-item"><a href="'. Mava_Url::getPageLink('video-kem-sach/'. $item['id']) .'"><img src="'. $item['thumbnails'] .'"/></a></div>';
        }
        echo '</div></div></div>';
    }
?>

<script type="text/javascript">
    $(document).ready(function(){
        $('#codeAttach').focus();
    });
</script>