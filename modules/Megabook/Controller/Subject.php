<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/26/16
 * @Time: 9:41 AM
 */
class Megabook_Controller_Subject extends Mava_Controller {
    public function indexAction(){
        Mava_Application::set('menu_selected', 'book_subject');
        Mava_Application::set('seo/title', __('book_subject'));
        $subjectModel = $this->_getBookSubjectModel();
        $productModel = $this->_getProductModel();
        $subjects = $subjectModel->getAll();
        $data = array();
        if(is_array($subjects) && count($subjects) > 0){
            foreach($subjects as $subject){
                $books = $productModel->getProducts(0,20,array(
                    'subject' => $subject['id']
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
                $subject['books'] = $products;
                $data[] = $subject;
            }
        }

        $viewParams = array(
            'subjects' => $data
        );
        return $this->responseView('Megabook_View_Subject_Index', $viewParams);
    }

    public function categoryAction(){
        Mava_Application::set('menu_selected', 'book_subject');
        $subject_alias = Mava_Url::getParam('slug');
        $subjectModel = $this->_getBookSubjectModel();
        $productModel = $this->_getProductModel();
        $subjects = $subjectModel->getAll();
        $subject = false;
        if(is_array($subjects) && count($subjects) > 0){
            foreach($subjects as $item){
                if(Mava_String::unsignString($item['title'], '-') == $subject_alias){
                    $subject = $item;
                }
            }
        }
        if($subject){
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

            Mava_Application::set('seo/title', __('book_subject'));
            $books = $productModel->getProducts($skip,$limit,array(
                'subject' => $subject['id']
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
                'url' => Mava_Url::buildLink('mon-hoc/'. $subject_alias),
                'text' => $subject['title']
            );
            $viewParams = array(
                'page' => $page,
                'skip' => $skip,
                'limit' => $limit,
                'total' => $books['total'],
                'sort_by' => $sort_by,
                'sort_dir' => $sort_dir,
                'breadcrumbs' => $breadcrumbs,
                'subject' => $subject,
                'products' => $products,
                'subjects' => $subjects,
            );
            return $this->responseView('Megabook_View_Subject_Category', $viewParams);
        }else{
            return $this->responseError(__('book_subject_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    /**
     * @return Megabook_Model_BookSubject
     */
    protected function _getBookSubjectModel(){
        return $this->getModelFromCache('Megabook_Model_BookSubject');
    }

    /**
     * @return Product_Model_Product
     */
    protected function _getProductModel(){
        return $this->getModelFromCache('Product_Model_Product');
    }
}