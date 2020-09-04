<div class="container mb-agency-switcher">
    <h3 class="text-center"><?php echo __('choose_agency'); ?></h3>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <?php
                if(is_array($agency) && count($agency) > 0){
                    foreach($agency as $item){
                        echo '<div class="mb-agency-item">
                        <a href="'. Mava_Url::getPageLink('dashboard/'. $item['id']) .'">
                        <h4>'. htmlspecialchars($item['title']) .'</h4>
                        <span class="mb-id">'. __('id') .': '. $item['id'] .'</span>
                        </a>
                        </div>';
                    }
                }
            ?>
        </div>
    </div>
</div>