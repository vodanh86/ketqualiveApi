<div id="footer">
	<div class="container">
        <div class="text-center">
            <span class="lr-copyright-text">© <?php echo date('Y') .' '. __('site_name'); ?></span>
        </div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('body').on('mouseenter', '[data-toggle="tooltip"]', function(){
			var el = $(this);
			if (el.data('tooltip') === undefined) {
				el.tooltip({
					placement: el.data("placement") || "top",
					container: 'body'
				});
			}
			if($(this).hasClass('sc-live-time-tooltip')){
				$(this).attr('data-original-title', MP.time.since($(this).data('time')));
			}
			el.tooltip('show');
		});

		$('body').on('mouseleave', '[data-toggle="tooltip"]', function(){
			$(this).tooltip('hide');
		});

		// Begin subscribe
		$('.rm-subscribe-button').click(function(){
			var sub_input = $(this).parents('.rm-subscribe').find('.rm-subscribe-input');
			if(sub_input.val() == ''){
				sub_input.focus();
			}else{
				MV.post(DOMAIN +'/add-subscribe',{
					title: document.title,
					url: window.location.href,
					email: sub_input.val()
				},function(res){
					if(res.status == 1){
						if(res.email_id > 0 && res.email_token != ""){
							$('body').append('<img src="'+ DOMAIN +'/cron?type=send_mail&email_id='+ res.email_id +'&token='+ res.email_token +'" height="0" width="0" />');
						}
						sub_input.val('');
						MP.notice.show(res.message, 'success', 3);
					}else{
						MP.notice.show(res.message, 'danger', 3);
					}

				});
			}
		});
		// End subscribe

        $('.xs-date-picker').each(function(){
            var _that = $(this);
            var options = {
                format: 'd-m-Y',
                onSelect: function(views, elements){
                if($(this).attr('data-func') != ""){
                        date_picker_callback($(this).attr('data-func'), $(this).val());
                    }
                },
                days: ['CN','T2','T3','T4','T5','T6','T7'],
                months_abbr: [
                    'Tháng 1',
                    'Tháng 2',
                    'Tháng 3',
                    'Tháng 4',
                    'Tháng 5',
                    'Tháng 6',
                    'Tháng 7',
                    'Tháng 8',
                    'Tháng 9',
                    'Tháng 10',
                    'Tháng 11',
                    'Tháng 12'
                ],
                months: [
                    'Tháng 1',
                    'Tháng 2',
                    'Tháng 3',
                    'Tháng 4',
                    'Tháng 5',
                    'Tháng 6',
                    'Tháng 7',
                    'Tháng 8',
                    'Tháng 9',
                    'Tháng 10',
                    'Tháng 11',
                    'Tháng 12'
                ],
                lang_clear_date: 'Xóa ngày',
                show_select_today: 'Hôm nay',
                open_on_focus: true,
                readonly_element: false
            };
            if($(this).attr('data-pair') != undefined){
                options.direction = false; 
                options.pair = $('#'+ $(this).attr('data-pair'));
            }
            $(this).Zebra_DatePicker(options);
        });

        $('.xs-date-picker-button').Zebra_DatePicker({
            format: 'd-m-Y',
            inside: false,
            show_icon: false,
            onSelect: function(views, elements){
                if($(this).attr('data-func') != ""){
                    date_picker_callback($(this).attr('data-func'), $(this).val());
                }
            },
            days: ['CN','T2','T3','T4','T5','T6','T7'],
            months_abbr: [
                'Tháng 1',
                'Tháng 2',
                'Tháng 3',
                'Tháng 4',
                'Tháng 5',
                'Tháng 6',
                'Tháng 7',
                'Tháng 8',
                'Tháng 9',
                'Tháng 10',
                'Tháng 11',
                'Tháng 12'
            ],
            months: [
                'Tháng 1',
                'Tháng 2',
                'Tháng 3',
                'Tháng 4',
                'Tháng 5',
                'Tháng 6',
                'Tháng 7',
                'Tháng 8',
                'Tháng 9',
                'Tháng 10',
                'Tháng 11',
                'Tháng 12'
            ],
            lang_clear_date: 'Xóa ngày',
            show_select_today: 'Hôm nay',
            open_on_focus: true
        });
        
        // index province select
        $('#quick_view_result_province').change(function(){
            show_result_by_date($('#quick_view_result_date').val());    
        });
	});

    // date picker callback
    function date_picker_callback(func){
        if(arguments.length > 0){
            switch(func){
                case 'show_result_by_date':
                    show_result_by_date(arguments[1]);
                    break;
                case 'show_result_province_by_date':
                    show_result_province_by_date(arguments[1]);
                    break;
            }
        }
    }

    function show_result_by_date(date){
        var province_option = $('#quick_view_result_province');
        window.location.href = getLinkResult(date, province_option.val(), province_option.find('option:selected').attr('data-slug'));
    }
    
    function getLinkResult(date, province, slug) {
        return DOMAIN +'/'+ slug +'-ngay-'+ date;
    }

    function show_result_province_by_date(date) {
        date = date.split("-");
        var arrDay = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
        var newDate = new Date(date[2], date[1]-1, date[0]);
        var day = arrDay[newDate.getDay()];
        var prv = <?php echo json_encode(Mava_Application::getConfig('loto_schedule'))?>;
        $('#xs_result_province').children().remove();
        $.each( prv[day], function( key, value ) {
            if(key === 'tt'){
                $('#xs_result_province').prepend('<option selected value="'+ key +'" data-slug="'+ value[3] +'">'+ value[2]+'</option>')
            }else {
                $('#xs_result_province').append('<option value="'+ key +'" data-slug="'+ value[3] +'">'+ value[2]+'</option>')
            }
        });
    }

</script>
<?php
if(Mava_Session::get('otm') != ""){
	?>
	<!-- one time message -->
	<script type="text/javascript">
		MP.notice.show('<?php echo Mava_Session::get('otm'); ?>', 'success', 3);
	</script>
<?php
}
Mava_Session::set('otm', "");

if(Mava_Session::get('otem') != ""){
	?>
	<!-- one time error message -->
	<script type="text/javascript">
		MP.notice.show('<?php echo Mava_Session::get('otem'); ?>', 'danger', 5);
	</script>
<?php
}
Mava_Session::set('otem', "");

ads_tracking();
?>

<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.10";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>