<div class="container">
    <div class="rm-search-no-keyword">
        <p><?php echo __('search_no_keyword_notice'); ?></p>
        <span class="text-muted"><?php echo __('search_keyword_tips'); ?></span>
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <form action="<?php echo Mava_Url::getPageLink('search'); ?>" method="get">
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" name="q" placeholder="<?php echo __('enter_to_search'); ?>"/>
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-warning"><?php echo __('search'); ?></button>
                        </span>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>