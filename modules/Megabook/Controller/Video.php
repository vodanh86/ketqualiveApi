<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 4/26/17
 * @Time: 3:16 AM
 */
class Megabook_Controller_Video extends Mava_Controller {
    public function indexAction(){
        if(!is_login()){
            Mava_Session::set('login_message',__('login_first'));
            Mava_Session::set('login_return_url',Mava_Url::getPageLink('video-kem-sach'));
            return $this->responseRedirect('login');
        }
        $error = '';
        $bookCodeSetModel = $this->_getBookCodeSetModel();
        Mava_Application::set('seo/title',__('view_video_attach_on_product'));
        if(Mava_Url::isPost()){
            $codeAttach = strtoupper(Mava_Url::getParam('codeAttach'));
            $code = $bookCodeSetModel->getCodeByCode($codeAttach);
            if(!$code){
                $error = __('this_code_is_not_existed');
            }else if($code['used_by'] > 0 && $code['used_by'] != Mava_Visitor::getUserId()){
                $error = __('this_code_is_used');
            }else if($code['status'] == 'deleted'){
                $error = __('this_code_is_deleted');
            }else{
                $bookCodeSetModel->useCode($code['id']);
                return $this->responseRedirect(Mava_Url::getPageLink('video-kem-sach/'. $code['product_id']));
            }
        }

        $products = array();
        $activated_product = $bookCodeSetModel->getBookActiveCodeList(Mava_Visitor::getUserId());
        if(is_array($activated_product) && count($activated_product) > 0){
            foreach($activated_product as $item){
                $thumbs = json_decode($item['thumbnails'], true);
                $thumbs = json_decode($thumbs[0], true);
                if(is_array($thumbs) && count($thumbs) > 0){
                    $item['thumbnails'] = thumb_url($thumbs['image'], 640,640,2);
                }else{
                    $item['thumbnails'] = '';
                }
                $products[] = $item;
            }
        }
        return $this->responseView('Megabook_View_CodeEnter', array(
            'error' => $error,
            'products' => $products
        ));
    }

    public function viewAction(){
        $search_term = Mava_Url::getParam('q');
        $product_id = Mava_Url::getParam('product_id');
        $productModel = $this->_getProductModel();
        if($product_id > 0 && $product = $productModel->getProductById($product_id, true)){
            Mava_Application::set('seo/title', $product['_data'][Mava_Visitor::getLanguageCode()]['name']);
            $videoModel = $this->_getProductVideoModel();
            if($videoModel->canViewVideo($product_id)){
                $videos = $videoModel->getByProduct($product_id, $search_term);
                return $this->responseView('Megabook_View_ViewVideo', array(
                    'search_term' => $search_term,
                    'videos' => $videos,
                    'product' => $product
                ));
            }else{
                return $this->responseRedirect(Mava_Url::getPageLink('video-kem-sach'));
            }
        }else{
            return $this->responseError(__('product_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    /**
     * @return Megabook_Model_BookCodeSet
     */
    protected function _getBookCodeSetModel(){
        return $this->getModelFromCache('Megabook_Model_BookCodeSet');
    }

    /**
     * @return Product_Model_Video
     */
    protected function _getProductVideoModel(){
        return $this->getModelFromCache('Product_Model_Video');
    }

    /**
     * @return Product_Model_Product
     */
    protected function _getProductModel(){
        return $this->getModelFromCache('Product_Model_Product');
    }
}