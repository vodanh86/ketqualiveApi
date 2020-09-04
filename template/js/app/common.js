$(document).ready(function(){
    // play video
    $('.lr-play-video-modal').click(function(){
        var videoId = $(this).attr('video-id');
        var play_video_form = '<div class="lr-video-wrap">' +
            '<iframe src="https://www.youtube.com/embed/'+ videoId +'?rel=0&amp;controls=1&amp;showinfo=0&amp;autoplay=1" frameborder="0" allowfullscreen></iframe>' +
            '</div>';
        if(videoId != undefined){
            MP.modal.show({
                id: 'play_video_modal',
                title: __('see_video_demo'),
                size: 'lg',
                type: 'html',
                html: play_video_form,
                init_callback: function(){
                    $('#play_video_modal .modal-footer').hide();
                    $('#play_video_modal .lr-video-wrap').css({
                        height: ($('#play_video_modal .lr-video-wrap').outerWidth()/16)*9
                    });
                },
                ok_callback: function(){

                }
            });
        }
        return false;
    });
    // resize video
    $(window).resize(function(){
        $('.lr-video-wrap').each(function(){
            $(this).css({
                height: ($(this).outerWidth()/16)*9
            });
        });

        if($('body').height() < $(window).height()){
            $('#footer').addClass('fixed-bottom');
        }else{
            $('#footer').removeClass('fixed-bottom');
        }
    });

    // fixed footer
    if($('body').height() < $(window).height()){
        $('#footer').addClass('fixed-bottom');
    }else{
        $('#footer').removeClass('fixed-bottom');
    }
});