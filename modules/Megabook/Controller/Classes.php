<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/26/16
 * @Time: 9:41 AM
 */
class Megabook_Controller_Classes extends Mava_Controller {
    public function indexAction(){
        Mava_Application::set('menu_selected', 'book_class');
        Mava_Application::set('seo/title', __('book_class'));
        $classModel = $this->_getBookClassModel();
        $productModel = $this->_getProductModel();
        $classes = $classModel->getAll();
        $data = array();
        if(is_array($classes) && count($classes) > 0){
            foreach($classes as $class){
                $books = $productModel->getProducts(0,20,array(
                    'class' => $class['id']
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
                $class['books'] = $products;
                $data[] = $class;
            }
        }

        $viewParams = array(
            'classes' => $data
        );
        return $this->responseView('Megabook_View_Classes_Index', $viewParams);
    }

    public function categoryAction(){
        Mava_Application::set('menu_selected', 'book_class');
        $class_alias = Mava_Url::getParam('slug');
        $classModel = $this->_getBookClassModel();
        $productModel = $this->_getProductModel();
        $classes = $classModel->getAll();
        $class = false;
        if(is_array($classes) && count($classes) > 0){
            foreach($classes as $item){
                if(Mava_String::unsignString($item['title'], '-') == $class_alias){
                    $class = $item;
                }
            }
        }
        if($class){
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

            Mava_Application::set('seo/title', __('book_class'));
            $books = $productModel->getProducts($skip,$limit,array(
                'class' => $class['id']
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
                'url' => Mava_Url::buildLink('mon-hoc/'. $class_alias),
                'text' => $class['title']
            );
            $viewParams = array(
                'page' => $page,
                'skip' => $skip,
                'limit' => $limit,
                'total' => $books['total'],
                'sort_by' => $sort_by,
                'sort_dir' => $sort_dir,
                'breadcrumbs' => $breadcrumbs,
                'class' => $class,
                'products' => $products,
                'classes' => $classes,
            );
            return $this->responseView('Megabook_View_Classes_Category', $viewParams);
        }else{
            return $this->responseError(__('book_class_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    /**
     * @return Megabook_Model_BookClass
     */
    protected function _getBookClassModel(){
        return $this->getModelFromCache('Megabook_Model_BookClass');
    }

    /**
     * @return Product_Model_Product
     */
    protected function _getProductModel(){
        return $this->getModelFromCache('Product_Model_Product');
    }
}