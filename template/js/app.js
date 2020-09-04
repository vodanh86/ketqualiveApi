/**
 * Created by nguyen dinh the on 10/16/15.
 */
var MP = window.MP || {};

function escapeRegExp(str) {
    return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
}

function __(key, params){
    if(MP.const != undefined && MP.const.PHRASE[key] != undefined){
        var phrases = MP.const.PHRASE[key];
        if(typeof params == 'object'){
            for(var v in params){
                if(params.hasOwnProperty(v)){
                    var reg = new RegExp(escapeRegExp('{'+v+'}'),'ig');
                    phrases = phrases.replace(reg,params[v]);
                }
            }
        }
        return phrases;
    }else{
        return "["+ key +"]";
    }
}

MP.helper = {
    number_format: function(number, decimals, decPoint, thousandsSep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number;
        var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
        var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep;
        var dec = (typeof decPoint === 'undefined') ? '.' : decPoint;
        var s = '';

        var toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k).toFixed(prec);
        };
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }

        return s.join(dec);
    }
};

var tl_uploader_id = 0;
MP.uploader = {
    image: {
        setup: function(){
            // ví dụ: <input type="hidden" class="tl_image_upload" data-folder="product" data-input-name="product_photo" />
            $('.tl_image_upload').each(function(){
                tl_uploader_id++;
                if(!$('#tl_ui_form_frame_'+ tl_uploader_id).length){
                    $('body').append('<div id="tl_ui_form_frame_'+ tl_uploader_id +'" class="hidden">' +
                    '<iframe src="about:blank" width="0" height="0" name="tl_ui_upload_frame_'+ tl_uploader_id +'"></iframe>' +
                    '<form id="tl_ui_upload_form_'+ tl_uploader_id +'" action="'+ DOMAIN +'/upload_thumbnail_image" method="post" enctype="multipart/form-data" target="tl_ui_upload_frame_'+ tl_uploader_id +'">' +
                    '<input type="hidden" id="tl_ui_folder_'+ tl_uploader_id +'" name="folder" value="" />' +
                    '<input type="hidden" id="tl_ui_id_'+ tl_uploader_id +'" name="uploader_id" value="" />' +
                    '<input type="hidden" id="tl_ui_input_name_'+ tl_uploader_id +'" name="input_name" value="" />' +
                    '<input type="file" accept="image/*" multiple="true" id="tl_ui_input_'+ tl_uploader_id +'" name="image[]" />' +
                    '</form>' +
                    '</div>');
                    $('#tl_ui_input_'+ tl_uploader_id).change(function(){
                        if($(this).val() != ""){
                            $(this).parents('form').submit();
                            $(this).val("");
                        }
                    });
                }
                var folder = $(this).data('folder');
                var exist_photo = '';
                $('input[name="'+ $(this).data('input-name') +'[]"]').each(function(){
                    var photo = $.parseJSON($(this).val());
                    if(photo != undefined){
                        if(photo.width > photo.height){
                            w = 100;
                            h = (100/photo.width) * photo.height;
                        }else{
                            h = 100;
                            w = (100/photo.height) * photo.width;
                        }
                        var photo_top = (100-h)/2;
                        var photo_left = (100-w)/2;
                        var photo_url = DOMAIN +'/'+ photo.image;
                        if(photo.image.substr(0,7).toLowerCase() == 'http://' || photo.image.substr(0,8).toLowerCase() == 'https://'){
                            photo_url = photo.image;
                        }
                        var gender = '';
                        if(folder == 'products'){
                            gender = '<div class="tl_iu_gender"><label class="male"><input type="checkbox" value="male" '+ (photo.gender==''||photo.gender=='male'?'checked':'') +'/> '+ __('male') +'</label><label class="female"><input type="checkbox" value="female" '+ (photo.gender==''||photo.gender=='female'?'checked':'') +'/> '+ __('female') +'</label></div>';
                        }
                        exist_photo += '<div class="item" data-key="'+ $(this).data('class') +'">'+ gender +'<a href="javascript:void(0);" class="tl_ui_item_remove" title="Xóa"><i class="fa fa-times"></i></a><img src="'+ photo_url +'" style="margin-top:'+ photo_top +'px;margin-left:'+ photo_left +'px"></div>';
                    }
                });

                // Chèn uploader
                var uploader = $('<div class="tl_image_uploader" id="tl_uploader_'+ tl_uploader_id +'">' +
                '<div class="tl_iu_preview">'+ exist_photo +'</div>' +
                '<div class="tl_iu_choose_file">' +
                '<a href="javascript:void(0);" class="btn_green" ' +
                ' data-folder="'+ $(this).data('folder') +'"' +
                ' data-id="'+ tl_uploader_id +'"' +
                ' data-input-name="'+ $(this).data('input-name') +'"' +
                ' >' +
                '<i class="fa fa-picture-o"></i> Chọn ảnh từ máy tính...' +
                '</a>' +
                '</div>' +
                '</div>');
                $(uploader).insertAfter($(this));

                uploader.find('.tl_iu_choose_file a').click(function(){
                    $('#tl_ui_folder_'+ $(this).data('id')).val($(this).data('folder'));
                    $('#tl_ui_id_'+ $(this).data('id')).val($(this).data('id'));
                    $('#tl_ui_input_name_'+ $(this).data('id')).val($(this).data('input-name'));
                    $('#tl_ui_input_'+ $(this).data('id')).click();
                });

                uploader.on('click','.tl_iu_preview .item .tl_ui_item_remove',function(){
                    var key = $(this).parents('.item').data('key');
                    if(key != undefined && key != ""){
                        $('.tl_ui_ih_'+ key).remove();
                        $('.tl_ui_ig_'+ key).remove();
                        $(this).parents('.item').remove();
                    }
                });

                uploader.on('click','.tl_iu_gender input[type="checkbox"]',function(){
                    var key = $(this).parents('.item').data('key');
                    if(key != undefined && key != ""){
                        if($(this).parents('.tl_iu_gender').find('.male input').is(':checked') && $(this).parents('.tl_iu_gender').find('.female input').is(':checked')){
                            $('.tl_ui_ig_'+ key).val(0);
                        }else if($(this).parents('.tl_iu_gender').find('.male input').is(':checked')){
                            $('.tl_ui_ig_'+ key).val(1);
                        }else if($(this).parents('.tl_iu_gender').find('.female input').is(':checked')){
                            $('.tl_ui_ig_'+ key).val(2);
                        }else{
                            $('.tl_ui_ig_'+ key).val(0);
                        }
                    }
                });
            });
        }
    }
};


MP.modal = {
    tpl: '<div class="modal" tabindex="-1" role="dialog" id="{$id}">\
                <div class="modal-dialog modal-{$size}">\
                    <div class="modal-content">\
                        <div class="modal-header">\
                            <button type="button" class="close" data-dismiss="modal" aria-label="'+ __('close') +'"><span aria-hidden="true">&times;</span></button>\
                            <h4 class="modal-title">{$title}</h4>\
                        </div>\
                        <div class="modal-body">\
                            <p>'+ __('loading') +'</p>\
                        </div>\
                        <div class="modal-footer hidden">\
                            <button type="button" class="btn btn-primary btn-ok">{$ok_title}</button>\
                            <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">{$cancel_title}</button>\
                        </div>\
                    </div>\
                </div>\
            </div>',
    show: function(options){
        $('.modal').modal('hide');
        $('.modal').remove();

        var modal_html = MP.modal.tpl;
        modal_html = modal_html.replace('{$id}', options.id);
        modal_html = modal_html.replace('{$title}', options.title);
        if(options.ok_title != undefined){
            modal_html = modal_html.replace('{$ok_title}', options.ok_title);
        }else{
            modal_html = modal_html.replace('{$ok_title}', __('done'));
        }
        if(options.cancel_title != undefined) {
            modal_html = modal_html.replace('{$cancel_title}', options.cancel_title);
        }else{
            modal_html = modal_html.replace('{$cancel_title}', __('cancel'));
        }
        modal_html = modal_html.replace('{$size}', options.size);
        $('body').append(modal_html);


        // Fix lỗi không focus được vào input ở tinymce
        $('#'+ options.id).on('shown.bs.modal', function() {
            $(document).off('focusin.modal');
        });

        $('#'+ options.id).on('hidden.bs.modal', function () {
            if(MV.is_function(options.close_callback)) {
                options.close_callback();
            }
            if($('#'+ options.id).data('has_change') == 1){
                window.location.reload();
            }
            $('#'+ options.id).remove();
        });

        // Hiển thị modal
        $('#'+ options.id).modal({
            show: true,
            backdrop: options.backdrop=='static'?'static':true,
            keyboard: options.keyboard===false?false:true
        });

        if(options.type == 'ajax'){
            MV.post(options.url,options.params,function(res){

                if(MV.is_function(options.cancel_callback)) {
                    $('#' + options.id + ' .btn-cancel').click(function () {
                        options.cancel_callback(res);
                    });
                }
                if(MV.is_function(options.ok_callback)){
                    $('#'+ options.id +' .btn-ok').unbind('click').click(function(){
                        options.ok_callback(res);
                    });
                }
                if(res.status == 1){
                    $('#'+ options.id +' .modal-footer').removeClass('hidden');
                    $('#'+ options.id +' .modal-body').html(res.html);
                    if(MV.is_function(options.init_callback)){
                        options.init_callback(res);
                    }
                }else{
                    $('#'+ options.id +' .modal-body').html('<div class="alert alert-danger">'+ res.message +'</div>');
                }
            });
        }else if(options.type == 'html'){
            $('#'+ options.id +' .modal-body').html(options.html);
            if(MV.is_function(options.init_callback)){
                options.init_callback();
            }
            $('#'+ options.id +' .modal-footer').removeClass('hidden');
            if(MV.is_function(options.cancel_callback)) {
                $('#' + options.id + ' .btn-cancel').click(function () {
                    options.cancel_callback();
                });
            }
            if(MV.is_function(options.ok_callback)){
                $('#'+ options.id +' .btn-ok').unbind('click').click(function(){
                    options.ok_callback();
                });
            }
        }
    },
    confirm: function(message, ok_callback){
        if(message == 'hide'){
            $('#confirm_modal').modal('hide');
        }else{
            MP.modal.show({
                id: 'confirm_modal',
                title: __('confirm'),
                size: 'md',
                type: 'html',
                html: message,
                init_callback: function(){
                },
                ok_callback: function(){
                    if(MV.is_function(ok_callback)){
                        ok_callback();
                    }
                },
                ok_title: __('ok'),
                cancel_title: __('cancel')
            });
        }
    },
    alert: function(message, ok_callback){
        if(message == 'hide'){
            $('#alert_modal').modal('hide');
        }else {
            MP.modal.show({
                id: 'alert_modal',
                title: __('alert'),
                size: 'md',
                type: 'html',
                html: message,
                init_callback: function () {
                    $('#alert_modal .modal-footer').css('text-align', 'center');
                    $('#alert_modal .btn-cancel').hide();
                },
                ok_callback: function () {
                    if (MV.is_function(ok_callback)) {
                        ok_callback();
                    }else{
                        $('#alert_modal').modal('hide');
                    }
                },
                ok_title: __('ok'),
                cancel_title: __('cancel')
            });
        }
    }
};

MP.notice = {
    hideTimeoutObj: undefined,
    show: function(message, type, second){
        if(MP.notice.hideTimeoutObj != undefined){
            clearTimeout(MP.notice.hideTimeoutObj);
        }
        MP.notice.hide();
        var content = $('<div class="alert alert-'+ type +' alert-dismissable tl_page_notice">'+ message +'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        content.css({
            'position': 'fixed',
            'top': 100,
            'left': 0,
            'z-index': 9999
        });
        $('body').append(content);
        content.css({
            'left': - (content.outerWidth() + 100)
        });
        content.animate({left: 0},500);
        if(second > 0){
            MP.notice.hideTimeoutObj = setTimeout(function(){
                MP.notice.hide();
            }, second*1000 + 500);
        }
    },
    hide: function(){
        $('.tl_page_notice').remove();
    }
};

MP.mask = {
    show: function(elem) {
        elem.find('.tl_mask').remove();
        elem.css('position','relative');
        elem.append('<div class="tl_mask"></div>');
        $('.tl_mask').css({
            'display': 'block',
            'width': '100%',
            'height': '100%',
            'position': 'absolute',
            'top': 0,
            'left': 0,
            'right': 0,
            'bottom': 0,
            'z-index': 9,
            'background': 'rgba(255,255,255,0.5)'
        });
    },
    hide: function(elem){
        elem.find('.tl_mask').remove();
    }
};

var DH = window.DH || {};


DH.user = {
    login: function(btn){
        var login_form = ' <div class="rm-login-form-wrap"><div class="rm-modal-alert"></div>' +
            '<form name="rm-login-form" id="login_form_modal" action="'+ DOMAIN +'/login" method="post" autocomplete="off">' +
            '<div class="form-group">' +
            '<label class="control-label" for="login_modal_email">'+ __('login_text_label') +'</label>' +
            '<input type="text" tabindex="1" class="form-control" id="login_modal_email" name="login_text"/>' +
            '</div>' +
            '<div class="form-group">' +
            '<label class="control-label" for="login_modal_password">'+ __('login_password_label') +' <small>(<a href="'+ DOMAIN +'/forgotpassword">'+ __('forgot_password_text_link') +'</a>)</small></label>' +
            '<input type="password" tabindex="2" class="form-control" id="login_modal_password" name="login_password" />' +
            '</div>' +
            '<div class="form-group checkbox checkbox-primary">' +
            '<input type="checkbox" value="1" id="login_modal_remember" checked="checked" />' +
            '<label for="login_modal_remember">'+ __('login_remember_label') +'</label>' +
            '</div>' +
            '</form>' +
            '</div>';
        MP.modal.show({
            id: 'login_modal',
            title: __('login'),
            size: 'md',
            type: 'html',
            html: login_form,
            init_callback: function(){
                $('#login_modal_email').focus();
                $('#login_modal_email, #login_modal_password').keypress(function(e){
                    if(e.keyCode == 13){
                        $('#login_modal .btn-ok').click();
                    }
                });
            },
            ok_callback: function(){
                if($('#login_modal_email').val() == ''){
                    $('#login_modal_email').focus();
                }else if($('#login_modal_password').val() == ''){
                    $('#login_modal_password').focus();
                }else{
                    MP.mask.show($('#login_modal .modal-content'));
                    MP.notice.show(__('loading'), 'warning');
                    MV.post(DOMAIN +'/ajax_login', {
                        email: $('#login_modal_email').val(),
                        password: $('#login_modal_password').val(),
                        remember: $('#login_modal_remember').is(':checked')?true:false
                    }, function(res){
                        MP.notice.hide();
                        MP.mask.hide($('#login_modal .modal-content'));
                        if(res.status == 1){
                            window.location.href = DOMAIN;
                        }else{
                            res.message = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-label="'+ __('close') +'"><span aria-hidden="true">&times;</span></button><div class="alert-content">'+ res.message +'</div></div>';
                            $('#login_modal .rm-modal-alert').html(res.message);
                        }
                    });
                }
            },
            ok_title: __('login_text_button'),
            cancel_title: __('cancel')
        });
        return false;
    },
    signup: function(btn){
        var signup_form = ' <div class="rm-signup-form-wrap"><div class="rm-modal-alert"></div>' +
            '<form name="rm-signup-form" id="signup_form_modal" action="'+ DOMAIN +'/signup" method="post" autocomplete="off">' +
            '<div class="row">' +
            '<div class="form-group col-md-6">' +
            '<label class="control-label" for="signup_modal_fullname">'+ __('fullname') +'</label>' +
            '<input type="text" tabindex="1" class="form-control" id="signup_modal_fullname" name="signup_fullname"/>' +
            '</div>' +
            '<div class="form-group col-md-6">' +
            '<label class="control-label" for="signup_modal_phone">'+ __('phone') +'</label>' +
            '<input type="text" tabindex="1" class="form-control" id="signup_modal_phone" name="signup_phone"/>' +
            '</div>' +
            '</div>' +
            '<div class="form-group">' +
            '<label class="control-label" for="signup_modal_email">'+ __('email') +'</label>' +
            '<input type="text" tabindex="1" class="form-control" id="signup_modal_email" name="signup_email"/>' +
            '</div>' +
            '<div class="form-group">' +
            '<label class="control-label" for="signup_modal_password">'+ __('password') +'</label>' +
            '<input type="password" tabindex="2" class="form-control" id="signup_modal_password" name="signup_password" />' +
            '</div>' +
            '<div class="form-group">' +
            '<label class="control-label" for="signup_modal_retype_password">'+ __('retype_password') +'</label>' +
            '<input type="password" tabindex="2" class="form-control" id="signup_modal_retype_password" name="signup_retype_password" />' +
            '</div>' +
            '<div class="form-group">' +
            '<label class="control-label" for="signup_modal_gender">'+ __('gender') +'</label>' +
            '<select class="form-control" id="signup_modal_gender" name="signup_gender">' +
            '<option value="male">'+ __('male') +'</option>' +
            '<option value="female">'+ __('female') +'</option>' +
            '</select>' +
            '</div>' +
            '<div class="form-group">'+ __('register_tos_notice') +'</div>' +
            '</form>' +
            '</div>';
        MP.modal.show({
            id: 'signup_modal',
            title: __('signup'),
            size: 'md',
            type: 'html',
            html: signup_form,
            init_callback: function(){
                $('#signup_modal_fullname').focus();
                $('#signup_modal_fullname, #signup_modal_phone, #signup_modal_email, #signup_modal_password, #signup_modal_retype_password,#signup_modal_gender').keypress(function(e){
                    if(e.keyCode == 13){
                        $('#signup_modal .btn-ok').click();
                    }
                });
            },
            ok_callback: function(){
                if($('#signup_modal_fullname').val() == ''){
                    $('#signup_modal_fullname').focus();
                }else if($('#signup_modal_phone').val() == ''){
                    $('#signup_modal_phone').focus();
                }else if($('#signup_modal_email').val() == ''){
                    $('#signup_modal_email').focus();
                }else if($('#signup_modal_password').val() == ''){
                    $('#signup_modal_password').focus();
                }else if($('#signup_modal_retype_password').val() != $('#signup_modal_password').val()){
                    $('#signup_modal_retype_password').focus();
                }else{
                    MP.mask.show($('#signup_modal .modal-content'));
                    MP.notice.show(__('loading'), 'warning');
                    MV.post(DOMAIN +'/ajax_signup', {
                        fullname: $('#signup_modal_fullname').val(),
                        phone: $('#signup_modal_phone').val(),
                        gender: $('#signup_modal_gender').val(),
                        email: $('#signup_modal_email').val(),
                        password: $('#signup_modal_password').val(),
                        confirm_password: $('#signup_modal_retype_password').val()
                    }, function(res){
                        MP.notice.hide();
                        MP.mask.hide($('#signup_modal .modal-content'));
                        if(res.status == 1){
                            $('#signup_modal').data('has_change',1);
                            $('#signup_modal .modal-body').html('<div style="margin-bottom: 50px;" class="text-center"><h3>'+ __('register_success') +'</h3><p>'+ __('register_success_message') +'</p><p><a href="'+ DOMAIN +'" class="btn btn-success">'+ __('start_shopping') +'</a></p></div>');
                            $('#signup_modal .modal-footer').hide();
                        }else{
                            res.message = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-label="'+ __('close') +'"><span aria-hidden="true">&times;</span></button><div class="alert-content">'+ res.message +'</div></div>';
                            $('#signup_modal .rm-modal-alert').html(res.message);
                        }
                    });
                }
            },
            ok_title: __('signup'),
            cancel_title: __('cancel')
        });
        return false;
    }
};

var XS = window.XS || {};

XS.validate = {
    date: function(value, format){
        if(format.split('/').length > 1 && value.split('/').length == format.split('/').length){
            return true;
        }else if(format.split('-').length > 1 && value.split('-').length == format.split('-').length){
            return true;
        }
        return false;
    }
};

