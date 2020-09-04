<div class="mb-video-view-list container">
    <div class="row">
        <div class="col-md-8"><h3 class="mb-video-view-title"><?php echo htmlspecialchars($product['_data'][Mava_Visitor::getLanguageCode()]['name']); ?></h3></div>
        <div class="col-md-4 clearfix">
            <form class="pull-right" action="<?php echo Mava_Url::getPageLink('video-kem-sach/'. $product['id']); ?>" method="get">
                <div class="input-group input-group-sm mb-video-search">
                    <input type="text" class="form-control" placeholder="<?php echo __('search_video_placeholder'); ?>" name="q" id="q" value="<?php echo htmlspecialchars($search_term); ?>" />
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-success"><?php echo __('search'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <table class="table mb-video-list">
        <thead>
        <tr>
            <th width="100"><?php echo __('page_no'); ?></th>
            <th><?php echo __('title'); ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
            if(isset($videos) && is_array($videos) && count($videos) > 0){
                foreach($videos as $item){
                    $youtube_id = Mava_String::getYoutubeId($item['video_url']);
                    echo '<tr>
            <td>'. $item['page_no'] .'</td>
            <td>'. htmlspecialchars($item['title']) .'
            <div class="mb-video-player"><iframe width="560" height="315" src="about:blank" frameborder="0" allowfullscreen></iframe></div>
            </td>
            <td align="right">
            <a href="javascript:void(0);" data-youtube-id="'. $youtube_id .'" class="btn btn-sm btn-warning btn-play-video">'. __('play_video') .'</a>
            <a href="javascript:void(0);" data-youtube-id="'. $youtube_id .'" class="btn btn-sm btn-danger btn-close-video">'. __('close_video') .'</a>
            </td>
        </tr>';
                }
            }
        ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-play-video').click(function(){
            $('.btn-play-video').show();
            $('.btn-close-video').hide();
            $('.mb-video-player').hide().find('iframe').attr('src', 'about:blank');
            $(this).next('.btn-close-video').show();
            $(this).hide();
            $(this).parents('tr').find('.mb-video-player').show().find('iframe').attr('src', 'https://www.youtube.com/embed/'+ $(this).attr('data-youtube-id') +'?autoplay=1');
        });

        $('.btn-close-video').click(function(){
            $(this).prev('.btn-play-video').show();
            $(this).hide();
            $(this).parents('tr').find('.mb-video-player').hide().find('iframe').attr('src', 'about:blank');
        });
    });
</script>