<div class="mb-checkout-page">
    <div class="mb-checkout-step-outer">
        <div class="container clearfix">
            <div class="mb-checkout-step clearfix">
                <div class="mb-step step-1 active"><b>1</b><p><?php echo __('choose_book_want_buy'); ?></p></div>
                <div class="mb-step step-2"><b>2</b><p><?php echo __('fill_information'); ?></p></div>
                <div class="mb-step step-3"><b>3</b><p><?php echo __('finish'); ?></p></div>
                <s class="mb-line"></s>
            </div>
        </div>
    </div>
    <form id="CheckoutForm" action="<?php echo Mava_Url::getPageLink('dang-ky-mua-sach'); ?>" method="post">
        <div class="container clearfix">
            <div class="mb-checkout-form">
                <div class="mb-checkout-pane pane-1 clearfix active">
                    <div id="mb_checkout_cart_items"></div>
                </div>
                <div class="mb-checkout-pane pane-2 clearfix">
                    <div class="mb-col pull-left">
                        <dl>
                            <dt><label for="who_is"><?php echo __('you_are'); ?>:</label></dt>
                            <dd>
                                <select name="who_is" id="who_is" class="mb-input-text">
                                    <option value="1"><?php echo __('student'); ?></option>
                                    <option value="2"><?php echo __('parents'); ?></option>
                                    <option value="3"><?php echo __('teacher'); ?></option>
                                    <option value="4"><?php echo __('other'); ?></option>
                                </select>
                                <p class="mb-help-block"><?php echo __('you_are_notice'); ?></p>
                            </dd>
                        </dl>
                        <dl>
                            <dt><label for="fullname"><?php echo __('fullname'); ?> <b>(*)</b>:</label></dt>
                            <dd>
                                <input type="text" name="fullname" id="fullname" class="mb-input-text">
                            </dd>
                        </dl>
                        <dl>
                            <dt><label for="phone_number"><?php echo __('phone'); ?> <b>(*)</b>:</label></dt>
                            <dd>
                                <input type="text" name="phone_number" id="phone_number" class="mb-input-text">
                            </dd>
                        </dl>
                        <dl>
                            <dt><label for="free_time"><?php echo __('free_time'); ?>:</label></dt>
                            <dd>
                                <input type="text" name="free_time" id="free_time" class="mb-input-text">
                            </dd>
                        </dl>
                        <p style="color: #F00;"><?php echo __('required_field_notice'); ?></p>
                    </div>
                    <div class="mb-col pull-right">
                        <dl>
                            <dt><label for="email"><?php echo __('email'); ?>:</label></dt>
                            <dd>
                                <input type="text" name="email" id="email" class="mb-input-text">
                            </dd>
                        </dl>
                        <dl>
                            <dt><label for="gift_code"><?php echo __('gift_code_label'); ?>:</label></dt>
                            <dd>
                                <input type="text" name="gift_code" id="gift_code" class="mb-input-text">
                            </dd>
                        </dl>
                        <dl>
                            <dt><label for="city_id"><?php echo __('city'); ?> <b>(*)</b>:</label></dt>
                            <dd>
                                <select name="city_id" id="city_id" class="mb-input-text">
                                    <?php
                                        $cities = get_all_city();
                                        if(is_array($cities) && count($cities) > 0){
                                            foreach($cities as $city){
                                                echo '<option value="'. $city['city_id'] .'">'. htmlspecialchars($city['title']).'</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </dd>
                        </dl>
                        <dl>
                            <dt><label for="receiver_info"><?php echo __('book_consignee_address'); ?> <b>(*)</b>:</label></dt>
                            <dd>
                                <textarea name="receiver_info" id="receiver_info" class="mb-input-text" style="height: 70px;line-height: 18px;"></textarea>
                                <?php echo __('book_consignee_address_notice'); ?>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="mb-checkout-pane pane-3 clearfix">
                    <p class="mb_checkout_done_notice"><?php echo __('finish_direct_order_notice'); ?></p>
                    <div class="mb-col pull-left">
                        <div class="mb_checkout_info_preview">
                            <h3><?php echo __('checkout_information'); ?></h3>
                            <table></table>
                            <a href="javascript:void(0);" onclick="checkout_go(2)" class="btn-edit-info"><?php echo __('edit'); ?></a>
                        </div>
                    </div>
                    <div class="mb-col pull-right">
                        <div class="mb_checkout_items_preview">
                            <h3><?php echo __('book_to_buy'); ?></h3>
                            <table></table>
                            <a href="javascript:void(0);" onclick="checkout_go(1)" class="btn-edit-info"><?php echo __('edit'); ?></a>
                        </div>
                        <div style="padding: 10px 0; color: #666;"><b><?php echo __('payment_method'); ?>:</b> <?php echo __('payment_method_notice'); ?></div>
                    </div>
                </div>
            </div>

            <div class="mb-checkout-action">
                <a href="javascript:void(0);" class="mb-btn-back disabled" id="mb_checkout_back"><i class="fa fa-caret-left"></i> <?php echo __('previous_step'); ?></a> &nbsp;
                <a href="javascript:void(0);" class="mb-btn-next" id="mb_checkout_next"><?php echo __('next_step'); ?> <i class="fa fa-caret-right"></i></a>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    function update_cart_quantity(input){
        var product_id = parseInt($(input).data('id'));
        var new_quantity = parseInt($(input).val());
        if(new_quantity < 1){
            new_quantity = 1;
            $(input).val(1);
        }
        var cart_items = MV.cookie('dh_cart_items');
        if(cart_items != null && cart_items != ""){
            cart_items = JSON.parse(cart_items);
        }else{
            cart_items = [];
        }
        var new_items = [];
        for(var i=0;i<cart_items.length;i++){
            if(parseInt(cart_items[i].id) == product_id){
                cart_items[i].quantity = new_quantity;
                cart_items[i].amount = new_quantity * cart_items[i].price;
                cart_items[i].amount_formatted = MP.helper.number_format(new_quantity * cart_items[i].price,0,',','.') + ' đ';
            }
            new_items.push(cart_items[i]);
        }

        MV.cookie('dh_cart_items', JSON.stringify(new_items), {expires: 30, domain: '.'+ DOMAIN.replace('http://','')});
        get_cart_info();
    }

    function get_cart_info(){
        var cart_items = MV.cookie('dh_cart_items');
        if(cart_items != null && cart_items != ""){
            cart_items = JSON.parse(cart_items);
        }else{
            cart_items = [];
        }
        if(cart_items.length > 0) {
            $('#mb_checkout_cart_items').html('<ul class="mb-cart-item-list"><li class="clearfix mb-cart-head"><span class="mbc-name">'+ __('name') +'</span><span class="mbc-price">'+ __('price') +'</span><span class="mbc-quantity">'+ __('book_quantity') +'</span><span class="mbc-total-amount">'+ __('amount') +'</span></li></ul>').show();
            var total_amount = 0;
            for (var i = 0; i < cart_items.length; i++) {
                $('#mb_checkout_cart_items ul').append('<li class="clearfix" data-price="'+ cart_items[i].amount +'" data-old-price="'+ cart_items[i].old_price +'"><a href="'+ DOMAIN +'/p/'+ cart_items[i].id +'" class="pull-left mb-cart-item-name" target="_blank"><div class="text-muted">'+ __('product_id') +': <b>'+ cart_items[i].id +'</b></div><b>'+ cart_items[i].name.replace(/\+/g,' ') +'</b></a><span class="mb-cart-price-per-item">'+ cart_items[i].price_formatted.replace(/\+/g,' ') +' '+ (parseInt(cart_items[i].old_price)>0?'<div class="text-muted"><s>'+ cart_items[i].old_price_formatted.replace(/\+/g,' ') +'</s></div>':'') +'</span><span class="mb-cart-quantity"> X <input type="number" class="mb-cart-quantity-input" onchange="update_cart_quantity(this);" data-id="' + cart_items[i].id + '" value="' + cart_items[i].quantity + '" /></span><a href="javascript:void(0);" class="pull-right mb-remove-cart-item" data-id="' + cart_items[i].id + '" title="'+ __('remove_from_cart') +'"><i class="fa fa-times"></i></a><span class="pull-right">'+ cart_items[i].amount_formatted.replace(/\+/g,' ') +'</span></li>');
                total_amount += parseInt(cart_items[i].amount);
            }
            $('#mb_checkout_cart_items').append('<div class="mb-cart-total-price clearfix"><span class="pull-left" style="font-size: 13px;font-style: italic;">'+ __('cart_amount_notice') +'</span><span>'+ __('total_amount') +': </span><b>'+ MP.helper.number_format(total_amount,0,',','.') +' <u>đ</u></b></div><div style="font-weight: 400;color:#F30;border: 1px #F30 solid;text-align: center;padding: 5px 10px;width: 68%;margin: 0 auto;border-radius: 3px;">'+ __('shipping_cost_notice') +'</div>');

            if ($('#mb_checkout_next').length && $('.mb-checkout-step .step-1').hasClass('active')) {
                if (checkout_validate_step_1()) {
                    $('#mb_checkout_next').removeClass('disabled');
                } else {
                    $('#mb_checkout_next').addClass('disabled');
                }
            }

            $('.mb-remove-cart-item').click(function(){
                var product_id = parseInt($(this).attr('data-id'));
                var cart_items = MV.cookie('dh_cart_items');
                if(cart_items != null && cart_items != ""){
                    cart_items = JSON.parse(cart_items);
                }else{
                    cart_items = [];
                }
                var new_items = [];
                for(var i=0;i<cart_items.length;i++){
                    if(parseInt(cart_items[i].id) != product_id){
                        new_items.push(cart_items[i]);
                    }
                }

                MV.cookie('dh_cart_items', JSON.stringify(new_items), {expires: 30, domain: '.'+ DOMAIN.replace('http://',''), path: '/'});
                get_cart_info();
            });
        }else if ($('#mb_checkout_cart_items').length) {
            $('#mb_checkout_cart_items').html(__('please_choose_book_want_buy'));
        }

        DH.cart.show_count();
    }

    function general_preview_cart(){
        switch (parseInt($('#who_is').val())){
            case 1:
                who_is = __('student');
                break;
            case 2:
                who_is = __('parents');
                break;
            case 3:
                who_is = __('teacher');
                break;
            default:
                who_is = __('other');
                break;
        }
        var info = '<tr><td width="30%">'+ __('you_are') +'</td><td>'+ who_is +'</td></tr>' +
            '<tr><td>'+ __('fullname') +'</td><td>'+ $('#fullname').val() +'</td></tr>' +
            '<tr><td>'+ __('phone') +'</td><td>'+ $('#phone_number').val() +'</td></tr>' +
            '<tr><td>'+ __('email') +'</td><td>'+ $('#email').val() +'</td></tr>' +
            '<tr><td>'+ __('book_consignee_address') +'</td><td>'+ $('#receiver_info').val() +'</td></tr>' +
            '<tr><td>'+ __('city') +'</td><td>'+ $('#city_id option:selected').html() +'</td></tr>' +
            '<tr><td>'+ __('gift_code') +'</td><td>'+ ($('#gift_code').val()!=""?$('#gift_code').val():__('not_have')) +'<span class="mbp_gift_code_notice"></span></td></tr>';
        $('.mb_checkout_info_preview table').html(info);
        var items = '<tr><th>'+ __('book') +'</th><th>'+ __('book_quantity') +'</th></tr>';
        var total_amount = 0;
        var total_product = 0;
        if($('#mb_checkout_cart_items .mb-cart-item-list li').length){
            $('#mb_checkout_cart_items .mb-cart-item-list li').each(function(){
                if($(this).find('.mb-cart-item-name').length) {
                    total_amount += parseInt($(this).attr('data-price'));
                    total_product += parseInt($(this).find('.mb-cart-quantity-input').val());
                    items += '<tr><td class="mbp_name">' + $(this).find('.mb-cart-item-name').html() + '<div class="mbp_price">' + $(this).find('.mb-cart-price-per-item').html() + '</div></td>';
                    items += '<td class="mbp_quantity">' + $(this).find('.mb-cart-quantity-input').val() + '</td></tr>';
                }
            });
        }
        var ship_cost = 30000;
        if(total_amount > 250000){
            ship_cost = 0;
        }else if($('#city_id').val() == 18){
            ship_cost = 20000;
        }else{
            ship_cost = 30000;
        }
        items += '<tr><td>'+ __('total_amount') +'</td><td class="nowrap">'+ MP.helper.number_format(total_amount) +' <u>đ</u></td></tr>';
        items += '<tr><td>'+ __('gift_code') +'</td><td class="nowrap mbp_gift_code_value">0 <u>đ</u></td></tr>';
        items += '<tr><td>'+ __('ship_cost') +'</td><td class="nowrap">'+ MP.helper.number_format(ship_cost) +' <u>đ</u></td></tr>';
        items += '<tr><td>'+ __('total_amount_payment') +'</td><td class="nowrap"><b class="mbp_total_amount_payment">'+ MP.helper.number_format(total_amount + ship_cost) +' <u>đ</u></b></td></tr>';
        $('.mb_checkout_items_preview table').html(items);
        if($('#gift_code').val() != ""){
            MV.post(DOMAIN + '/check-gift-code', {
                code: $('#gift_code').val(),
                product_count: total_product,
                total_amount: total_amount
            }, function(res){
                if(res.status == 1){
                    if(res.code_value > 0){
                        $('.mbp_gift_code_value').html(res.code_value_html);
                        $('.mbp_gift_code_notice').html(' ('+res.code_value_html +')');
                        $('.mbp_total_amount_payment').html(MP.helper.number_format((total_amount + ship_cost - res.code_value),'.','.') + ' đ');
                    }else{
                        $('.mbp_gift_code_notice').html('<span class="text-danger">'+ res.message +'</span>');
                    }
                }else{
                    $('.mbp_gift_code_notice').html('<span class="text-danger">'+ res.message +'</span>');
                }
            });
        }
    }

    function checkout_validate_step_1(){
        var cart_items = MV.cookie('dh_cart_items');
        if(cart_items != null && cart_items != ""){
            cart_items = JSON.parse(cart_items);
        }else{
            cart_items = [];
        }
        return cart_items.length > 0;
    }

    function checkout_validate_step_2(){
        if($('#fullname').val().trim() == ''){
            return false;
        }else if($('#phone_number').val().trim() == ''){
            return false;
        }else if($('#receiver_info').val().trim() == ''){
            return false;
        }
        return true;
    }

    function checkout_go(step){
        $('.mb-checkout-step .mb-step').removeClass('active');
        $('.mb-checkout-form .mb-checkout-pane').removeClass('active');
        $('.mb-checkout-step .mb-step.step-' + step).addClass('active');
        $('.mb-checkout-form .mb-checkout-pane.pane-' + step).addClass('active');
        switch(step){
            case 1:
                get_cart_info();
                if(checkout_validate_step_1()){
                    $('#mb_checkout_next').removeClass('disabled');
                }else{
                    $('#mb_checkout_next').addClass('disabled');
                }
                break;
            case 2:
                if(checkout_validate_step_2()){
                    $('#mb_checkout_next').removeClass('disabled');
                }else{
                    $('#mb_checkout_next').addClass('disabled');
                }
                break;
            case 3:
                general_preview_cart();
                break;
        }
        if(step != 1){
            $('#mb_checkout_back').removeClass('disabled');
        }else{
            $('#mb_checkout_back').addClass('disabled');
        }

        if(step == 3){
            $('#mb_checkout_next').html(__('finish'));
        }else{
            $('#mb_checkout_next').html(__('next_step') +' <i class="fa fa-caret-right"></i>');
        }

        $('body').animate({
            scrollTop: $('#CheckoutForm').offset().top - 30
        },1);
    }

    var timeout_hide_notice = undefined;
    $(document).ready(function(){
        if(USER_NAME != ''){
            $('#fullname').val(USER_NAME);
        }
        if(USER_PHONE != ''){
            $('#phone_number').val(USER_PHONE);
        }
        $('#mb_select_book_on_cart').remove();
        $.post(DOMAIN +"/get_book_set_section_list", {} , function(res){
            if(res.status == 1){
                var select_book_html = '<div class="clearfix" id="mb_select_book_on_cart">' +
                    '<div class="pull-left mba_choose_set"><h3>'+ __('choose_book_step_1') +'</h3>' +
                    '<ul>';
                for(var i=0;i<res.sections.length;i++) {
                    select_book_html += '<li><a href="#mb_section_'+ res.sections[i].id +'" data-toggle="collapse" data-parent="#mb_select_book_on_cart"><i class="fa fa-caret-right"></i> '+ res.sections[i].name +'</a>';
                    if(res.sections[i].sets.length > 0){
                        select_book_html += '<ul class="collapse mb-set-link" id="mb_section_'+ res.sections[i].id +'">';
                        for(var j=0;j<res.sections[i].sets.length;j++){
                            select_book_html += '<li><a href="javascript:void(0);" data-id="'+ res.sections[i].sets[j].id +'"><i class="fa fa-angle-right"></i> '+ res.sections[i].sets[j].name +'</a></li>'
                        }
                        select_book_html += '</ul>';
                    }
                    select_book_html += '</li>';
                }

                select_book_html += '</ul></div>' +
                '<div class="pull-right mba_choose_book"><h3>'+ __('choose_book_step_2') +'</h3>' +
                '<ul>' +
                '</ul>'+
                '</div>' +
                '</div>';

                $(select_book_html).insertBefore($('#mb_checkout_cart_items'));
                $('#mb_select_book_on_cart .mba_choose_set .mb-set-link li a').click(function(){
                    $(this).parents('ul.collapse').addClass('in');
                    window.location.hash = "set-"+ $(this).attr('data-id');
                    $('#mb_select_book_on_cart .mba_choose_set li a').removeClass('active');
                    $(this).addClass('active');
                    $.post(DOMAIN +"/get_book_list", {set_id: $(this).data('id')} , function(res){
                        if(res.status == 1){
                            if(res.books.length == 0){
                                $('#mb_select_book_on_cart .mba_choose_book ul').html("<li class='no_book'>"+ __('no_book_in_set') +"</li>");
                            }else{
                                $('#mb_select_book_on_cart .mba_choose_book ul').html("");
                                for(var i = 0; i < res.books.length; i++){
                                    $('#mb_select_book_on_cart .mba_choose_book ul').append('<li class="clearfix"><img src="'+ res.books[i].image +'" class="mba_thumb" width="40" style="float: left;margin: 10px;" /><a href="'+ DOMAIN +'/p/'+ res.books[i].id +'" target="_blank">'+ res.books[i].name +'<div class="mba_price"><b>'+ res.books[i].price_final +'</b></div>'+ (res.books[i].has_promotion==1?'<div class="mba_price_old"><s>'+ res.books[i].price +'</s></div>':'') +'</a><div class="mba_quantity_add"><input type="number" class="mba_quantity" value="1" /> '+ __('book_quantity_short') +' <a href="javascript:void(0);" class="mba_quantity_add_btn" data-id="'+ res.books[i].id +'">'+ __('add') +'</a></div></li>');
                                }
                            }
                            $('#mb_select_book_on_cart .mba_choose_book .mba_quantity_add_btn').click(function(){
                                MP.notice.show(__('loading'), 'warning');
                                var product_id = $(this).attr('data-id');
                                var quantity_input = $(this).parents('li').find('.mba_quantity');
                                var product_quantity = 1;
                                if(quantity_input.length){
                                    product_quantity = quantity_input.val();
                                }
                                MV.post(DOMAIN +'/add-to-cart',{
                                    product_id: product_id,
                                    product_quantity: product_quantity
                                },function(res){
                                    //GA track event
                                    ga('send', {
                                        hitType: 'event',
                                        eventCategory: 'Orders',
                                        eventAction: 'add_to_cart',
                                        eventLabel: 'Thêm sản phẩm vào giỏ hàng'
                                    });
                                    if(res.status == 1){
                                        MP.notice.show(res.message, 'success', 3);
                                        get_cart_info();
                                    }else{
                                        MP.notice.show(res.message, 'danger', 3);
                                    }
                                });
                                return false;
                            });
                        }else{
                            MP.modal.alert(res.message);
                        }
                    });
                });


                if(window.location.hash != "" && window.location.hash.split('-').length == 2){
                    var active_set = window.location.hash.split('-');
                    if(parseInt(active_set[1]).toString() != 'NaN' && parseInt(active_set[1]) > 0){
                        $('#mb_select_book_on_cart .mba_choose_set li a[data-id="'+ parseInt(active_set[1]) +'"]').trigger('click');
                    }else{
                        $('#mb_select_book_on_cart .mba_choose_set > ul > li > ul > li').first().find('a').click();
                    }
                }else {
                    $('#mb_select_book_on_cart .mba_choose_set > ul > li > ul > li').first().find('a').click();
                }
            }else{
                MP.modal.alert(res.message);
            }
        },'json');
        // enable next on step 1
        $('#fullname,#phone_number,#email,#receiver_info').keyup(function(){
            if(checkout_validate_step_2()){
                $('#mb_checkout_next').removeClass('disabled');
            }else{
                $('#mb_checkout_next').addClass('disabled');
            }
        });

        $('#mb_checkout_next').click(function(){
            var selected_step = $('.mb-checkout-step .mb-step').index($('.mb-checkout-step .mb-step.active'));
            if(selected_step < 0){
                selected_step = 0;
            }

            var next_step = selected_step+1;
            if(selected_step == $('.mb-checkout-step .mb-step').length-1){
                next_step = 0;
            }

            switch(next_step){
                case 0:
                    $.post(DOMAIN +"/direct_checkout", $('#CheckoutForm').serialize() , function(res){
                        if(res.status == 1){
                            //GA track event
                            ga('send', {
                                hitType: 'event',
                                eventCategory: 'Orders',
                                eventAction: 'checkout_finish',
                                eventLabel: 'Đặt hàng thành công'
                            });
                            if(res.email_id > 0 && res.email_token != ""){
                                $('body').append('<img src="'+ DOMAIN +'/cron?type=send_mail&email_id='+ res.email_id +'&token='+ res.email_token +'" height="0" width="0" />');
                            }
                            MP.notice.show(__('book_order_success'),'success',3);
                            setTimeout(function(){
                                window.location.href = '/dang-ky-mua-sach-thanh-cong';
                            }, 1000);
                        }else{
                            checkout_go(res.step);
                            //GA track event
                            ga('send', {
                                hitType: 'event',
                                eventCategory: 'Orders',
                                eventAction: 'checkout_failed',
                                eventLabel: 'Đặt hàng thất bại'
                            });
                            MP.modal.alert(res.message);
                        }
                    },'json');
                    break;
                case 1:
                    if(checkout_validate_step_1()){
                        checkout_go(2);
                    }else{
                        MP.modal.alert(__('please_choose_book_want_buy'));
                    }
                    break;
                case 2:
                    if(checkout_validate_step_2()){
                        checkout_go(3);
                    }else{
                        MP.modal.alert(__('please_full_fill_information_to_form'));
                    }
                    break;
                default:
                    MP.modal.alert(__('have_an_error_contact_admin'));
                    break;
            }
        });

        $('#mb_checkout_back').click(function(){
            var selected_step = $('.mb-checkout-step .mb-step').index($('.mb-checkout-step .mb-step.active'));
            if(selected_step <= 0){
                $(this).addClass('disabled');
            }else{
                $(this).removeClass('disabled');
                checkout_go(selected_step);
            }
        });

        $('body').css('padding-bottom',100);
        $('#internal_links').hide();
        $('#footer').hide();
        get_cart_info();
    });
</script>
