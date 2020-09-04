<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 10/4/16
 * @Time: 4:05 PM
 */
class Admin_Controller_Reviews extends Mava_AdminController {
    public function indexAction(){
        Mava_Application::set('seo', array(
            'title' => __('product_review')
        ));
        $page = max((int)Mava_Url::getParam('page'),1);
        $limit = 50;
        $skip = ($page-1)*$limit;
        $reviewModel = $this->_getReviewModel();
        $reviews = $reviewModel->getList($skip, $limit);
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/reviews/index'),
            'text' => __('product_review')
        );
        $pagination = Mava_View::buildPagination(Mava_Url::getPageLink('admin/reviews/index'),ceil($reviews['total']/$limit),$page);
        $viewParams = array(
            'breadcrumbs' => $breadcrumbs,
            'reviews' => $reviews['rows'],
            'total' => $reviews['total'],
            'page' => $page,
            'skip' => $skip,
            'limit' => $limit,
            'pagination' => $pagination
        );
        return $this->responseView('Admin_View_Product_Reviews_List', $viewParams);
    }

    public function changeStatusAction(){
        $review_id = Mava_Url::getParam('review_id');
        $status = Mava_url::getParam('status');
        $reviewModel = $this->_getReviewModel();
        if($review_id > 0 && $review = $reviewModel->getById($review_id)){
           if(in_array($status, array('new','approved','rejected','deleted'))){
               $reviewDW = $this->_getReviewDataWriter();
               $reviewDW->setExistingData($review_id);
               $reviewDW->bulkSet(array(
                   'status' => $status
               ));
               if($reviewDW->save()){
                   Mava_Session::set('otm', __('review_updated'));
                   return $this->responseJson(array(
                       'status' => 1,
                       'message' => __('review_updated')
                   ));
               }else{
                   return $this->responseJson(array(
                       'status' => -1,
                       'message' => __('can_not_change_review_status')
                   ));
               }
           }else{
               return $this->responseJson(array(
                   'status' => -1,
                   'message' => __('invalid_request')
               ));
           }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('review_not_found')
            ));
        }
    }

    /**
     * @return Product_Model_Reviews
     */
    protected function _getReviewModel(){
        return $this->getModelFromCache('Product_Model_Reviews');
    }

    /**
     * @return Product_DataWriter_Reviews
     * @throws Mava_Exception
     */
    protected function _getReviewDataWriter()
    {
        return Mava_DataWriter::create('Product_DataWriter_Reviews');
    }
}