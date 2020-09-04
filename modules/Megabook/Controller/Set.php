<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/26/16
 * @Time: 9:41 AM
 */
class Megabook_Controller_Set extends Mava_Controller {
    public function indexAction(){
        Mava_Application::set('menu_selected', 'book_set');
        Mava_Application::set('seo/title', __('book_set'));
        $setModel = $this->_getBookSetModel();
        $productModel = $this->_getProductModel();
        $sets = $setModel->getAll();
        $data = array();
        if(is_array($sets) && count($sets) > 0){
            foreach($sets as $set){
                $books = $productModel->getProducts(0,20,array(
                    'set' => $set['id']
                ),'no','id','desc',true,true);
                $products = array();
                if(is_array($books['rows'])){
                    foreach($books['rows'] as $item){
                        $images = array();
                        if($item['thumbnails'] != "" && Mava_String::isJson($item['thumbnails'])){
                            $thumbs = json_decode($item['thumbnails'], true);
                            if(is_array($thumbs) && count($thumbs) > 0){
                                foreach($thumbs as $thumb){
                                    $images[] = json_decode($thumb, true);
                                }
                            }
                        }
                        $item['thumbnails'] = $images;
                        $item['slug'] = Mava_String::unsignString($item['name'],'-');
                        $products[] = $item;
                    }
                }
                $set['books'] = $products;
                $data[] = $set;
            }
        }

        $viewParams = array(
            'sets' => $data
        );
        return $this->responseView('Megabook_View_Set_Index', $viewParams);
    }

    public function categoryAction(){
        Mava_Application::set('menu_selected', 'book_set');
        $set_alias = Mava_Url::getParam('slug');
        $setModel = $this->_getBookSetModel();
        $productModel = $this->_getProductModel();
        $sets = $setModel->getAll();
        $set = false;
        if(is_array($sets) && count($sets) > 0){
            foreach($sets as $item){
                if(Mava_String::unsignString($item['title'], '-') == $set_alias){
                    $set = $item;
                }
            }
        }
        if($set){
            if($set['landing_page'] != ""){
                return $this->responseRedirect($set['landing_page']);
            }
            $page = max((int)Mava_Url::getParam('page'),1);
            $limit = 20;
            $skip = ($page-1)*$limit;

            $sort_by = Mava_Url::getParam('sort_by');
            $sort_dir = Mava_Url::getParam('sort_dir');

            if(!in_array($sort_by, array('buy','time','price'))){
                $sort_by = 'id';
            }

            if($sort_dir != 'asc'){
                $sort_dir = 'desc';
            }

            Mava_Application::set('seo/title', __('book_set'));
            $books = $productModel->getProducts($skip,$limit,array(
                'set' => $set['id']
            ),'no',$sort_by,$sort_dir,true);
            $products = array();
            if(is_array($books['rows'])){
                foreach($books['rows'] as $item){
                    $images = array();
                    if($item['thumbnails'] != "" && Mava_String::isJson($item['thumbnails'])){
                        $thumbs = json_decode($item['thumbnails'], true);
                        if(is_array($thumbs) && count($thumbs) > 0){
                            foreach($thumbs as $thumb){
                                $images[] = json_decode($thumb, true);
                            }
                        }
                    }
                    $item['thumbnails'] = $images;
                    $item['slug'] = Mava_String::unsignString($item['name'],'-');
                    $products[] = $item;
                }
            }
            $breadcrumbs = array();
            $breadcrumbs[] = array(
                'url' => Mava_Url::getDomainUrl(),
                'text' => __('site_name')
            );
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('bo-sach/'. $set_alias),
                'text' => $set['title']
            );
            $viewParams = array(
                'page' => $page,
                'skip' => $skip,
                'limit' => $limit,
                'total' => $books['total'],
                'sort_by' => $sort_by,
                'sort_dir' => $sort_dir,
                'breadcrumbs' => $breadcrumbs,
                'set' => $set,
                'products' => $products,
                'sets' => $sets,
            );
            return $this->responseView('Megabook_View_Set_Category', $viewParams);
        }else{
            return $this->responseError(__('book_set_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    /**
     * @return Megabook_Model_BookSet
     */
    protected function _getBookSetModel(){
        return $this->getModelFromCache('Megabook_Model_BookSet');
    }

    /**
     * @return Product_Model_Product
     */
    protected function _getProductModel(){
        return $this->getModelFromCache('Product_Model_Product');
    }
}