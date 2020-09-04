<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 9/29/16
 * @Time: 9:44 PM
 */
class Index_Controller_Search extends Mava_Controller {
    public function indexAction(){
        Mava_Application::set('body_id', 'search_page');
        Mava_Application::set('seo/title', __('search'));
        $search_term = Mava_Url::getParam('q');
        if($search_term != ""){
            $page = max((int)Mava_Url::getParam('page'),1);
            $category_id = Mava_Url::getParam('cid');
            $limit = 30;
            $skip = ($page-1)*$limit;
            $productModel = $this->_getProductModel();
            if($category_id <= 0 || !$category = $productModel->getProductCategoryById($category_id, true)){
                $category_ids = null;
            }else{
                $category_ids = $productModel->getCategoryTree($category['id']);
            }

            $products = $productModel->getProducts($skip,$limit,$category_ids,false,'buy','desc',true, null, '', $search_term);
            $results = array();
            if(is_array($products['rows'])){
                foreach($products['rows'] as $item){
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
                    $results[] = $item;
                }
            }

            return $this->responseView('Index_View_Search_Result', array(
                'category_id' => $category_id,
                'search_term' => $search_term,
                'products' => $results,
                'skip' => $skip,
                'limit' => $limit,
                'page' => $page,
                'total' => $products['total']
            ));
        }else{
            return $this->responseView('Index_View_Search_NoKeyword');
        }
    }

    /**
     * @return Product_Model_Product
     */
    protected function _getProductModel(){
        return $this->getModelFromCache('Product_Model_Product');
    }
}