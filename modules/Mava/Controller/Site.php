<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 4/23/15
 * @Time: 10:56 PM
 */
class Mava_Controller_Site extends Mava_Controller {
    public function phraseAdminAction(){
        @header("Content-type: text/javascript; charset: UTF-8");
        $phrase = array(
            'delete_campaign_link'    =>  __('delete_campaign_link'),
            'edit_link'    =>  __('edit_link'),
            'note'    =>  __('note'),
            'link'    =>  __('link'),
            'add_link'    =>  __('add_link'),
            'male'    =>  __('male'),
            'female'    =>  __('female'),
            'mark_all_new_recall_to_read_confirm'    =>  __('mark_all_new_recall_to_read_confirm'),
            'mark_all_new_order_to_read_confirm'    =>  __('mark_all_new_order_to_read_confirm'),
            'save'    =>  __('save'),
            'view_product_order'    =>  __('view_product_order'),
            'finish'    =>  __('finish'),
            'order_product'    =>  __('order_product'),
            'ok'    =>  __('ok'),
            'alert'    =>  __('alert'),
            'confirm'    =>  __('confirm'),
            'close'    =>  __('close'),
            'loading'    =>  __('loading'),
            'done'    =>  __('done'),
            'cancel'    =>  __('cancel'),
        );
        echo '
            var MP = window.MP || {};
            MP.const = {PHRASE: '. json_encode($phrase) .'};';
        die;
    }

    public function phraseAction(){
        @header("Content-type: text/javascript; charset: UTF-8");
        $phrase = array(
            'see_video_demo' => __('see_video_demo'),
            'register_success'    =>  __('register_success'),
            'register_success_message'    =>  __('register_success_message'),
            'note'    =>  __('note'),
            'check'    =>  __('check'),
            'phone_number'    =>  __('phone_number'),
            'choose_gender'    =>  __('choose_gender'),
            'view_detail'    =>  __('view_detail'),
            'quick_view'    =>  __('quick_view'),
            'account_info_saved'    =>  __('account_info_saved'),
            'save'    =>  __('save'),
            'please_login_first'    =>  __('please_login_first'),
            'remove_from_cart'    =>  __('remove_from_cart'),
            'register_tos_notice'    =>  __('register_tos_notice'),
            'female'    =>  __('female'),
            'male'    =>  __('male'),
            'gender'    =>  __('gender'),
            'retype_password'    =>  __('retype_password'),
            'password'    =>  __('password'),
            'email'    =>  __('email'),
            'phone'    =>  __('phone'),
            'fullname'    =>  __('fullname'),
            'forgot_password_text_link'    =>  __('forgot_password_text_link'),
            'login_text_button'    =>  __('login_text_button'),
            'login_remember_label'    =>  __('login_remember_label'),
            'login_password_label'    =>  __('login_password_label'),
            'login_text_label'    =>  __('login_text_label'),
            'signup'    =>  __('signup'),
            'login'    =>  __('login'),
            'finish'    =>  __('finish'),
            'order_product'    =>  __('order_product'),
            'ok'    =>  __('ok'),
            'alert'    =>  __('alert'),
            'confirm'    =>  __('confirm'),
            'close'    =>  __('close'),
            'loading'    =>  __('loading'),
            'done'    =>  __('done'),
            'cancel'    =>  __('cancel'),
            'other'    =>  __('other'),
            'add'    =>  __('add'),
            'enter_your_phone_number'    =>  __('enter_your_phone_number'),
            'please_enter_fullname'    =>  __('please_enter_fullname'),
            'enter_your_email'    =>  __('enter_your_email'),
            'please_enter_password'    =>  __('please_enter_password'),
            'password_length_from_x_to_y'    =>  __('password_length_from_x_to_y'),
            'password_confirm_not_match'    =>  __('password_confirm_not_match'),
        );
        echo '
            var MP = window.MP || {};
            MP.const = {PHRASE: '. json_encode($phrase) .'};';
        die;
    }
    public function thumbAction(){
        $_GET['src'] = Mava_Url::getParam('src');
        $_GET['w'] = Mava_Url::getParam('width');
        $_GET['h'] = Mava_Url::getParam('height');
        $_GET['zc'] = Mava_Url::getParam('zc');
        include(BASEDIR .'/timthumb.php');
        timthumb::start();
    }
    public function upload_imageAction(){
        if(!is_login()){
            return $this->responseError(__('please_login_to_use'), Mava_Error::ACCESS_DENIED);
        }
        $type = Mava_Url::getParam('type'); // editor, avatar
        $image_url = upload_image('upload', 'hodela_image_input');
        if($image_url['error'] == 0){
            $image_full_url = get_static_domain() .'/'. $image_url['image'];
            if($type == 'avatar'){
                // crop avatar
                $avatar_dir = mkdir_by_id(BASEDIR .'/data/images/avatar', (int)Mava_Visitor::getUserId());
                thumbs(BASEDIR .'/'. $image_url['image'], BASEDIR .'/data/images/avatar/'. $avatar_dir .'_avatar_small.jpg', 50, 50);
                thumbs(BASEDIR .'/'. $image_url['image'], BASEDIR .'/data/images/avatar/'. $avatar_dir .'_avatar_middle.jpg', 100, 100);
                thumbs(BASEDIR .'/'. $image_url['image'], BASEDIR .'/data/images/avatar/'. $avatar_dir .'_avatar_big.jpg', 200, 200);
                thumbs(BASEDIR .'/'. $image_url['image'], BASEDIR .'/data/images/avatar/'. $avatar_dir .'_avatar_org.jpg', 500, 500);
                $image_full_url = get_avatar_url('big');
            }
            return $this->responseView('Mava_View_UploadImageSuccess', array(
                'image_url' => $image_full_url,
                'image' => $image_url['image'],
                'type' => $type
            ));
        }else{
            return $this->responseView('Mava_View_UploadImageFailed', array(
                'type' => $type
            ));
        }
    }

    public function upload_thumbnailAction(){
        if(!is_login()){
            return $this->responseView('Mava_View_UploadImageFailed', array(
                'message' => __('please_login_to_use')
            ));
        }
        $postData = Mava_Url::getParams();
        if($postData['uploader_id'] > 0){
            if(!isset($postData['folder'])){
                $postData['folder'] = "";
            }
            $uploaded = upload_multiple_image($postData['folder'], 'image');
            if(is_array($uploaded) && count($uploaded) > 0){
                return $this->responseView('Mava_View_UploadThumbnailImageSuccess', array(
                    'uploaded' => $uploaded,
                    'folder' => $postData['folder'],
                    'input_name' => $postData['input_name'],
                    'uploader_id' => $postData['uploader_id']
                ));
            }else{
                return $this->responseView('Mava_View_UploadImageFailed', array(
                    'message' => __('invalid_image_file')
                ));
            }
        }else{
            return $this->responseView('Mava_View_UploadImageFailed', array(
                'message' => __('invalid_request')
            ));
        }
    }
}