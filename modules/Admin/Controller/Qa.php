<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 8/1/16
 * @Time: 10:58 AM
 */
class Admin_Controller_Qa extends Mava_AdminController {

    public function editAction(){
        Mava_Application::set('seo',array(
            'title' => __('edit_question')
        ));
        $id = Mava_Url::getParam('id');
        $askModel = $this->_getAskModel();
        if($id > 0 && $question = $askModel->getById($id)){
            if($question['status'] == 'deleted'){
                return $this->responseError(__('can_not_edit_question_deleted'), Mava_Error::ACCESS_DENIED);
            }
            $viewParams = array(
                'question' => $question
            );
            $postData = array();
            $error = '';
            if(Mava_Url::isPost()){
                $postData = Mava_Url::getParams();
                if(!isset($postData['questionTitle']) || $postData['questionTitle'] == ''){
                    $error = __('question_title_empty');
                }else{
                    $askDW = $this->_getAskDataWriter();
                    $askDW->setExistingData($id);
                    $askDW->bulkSet(array(
                        'name' => (isset($postData['questionFullname'])?$postData['questionFullname']:''),
                        'email' => (isset($postData['questionEmail'])?$postData['questionEmail']:''),
                        'phone' => (isset($postData['questionPhone'])?$postData['questionPhone']:''),
                        'question' => (isset($postData['questionTitle'])?$postData['questionTitle']:''),
                        'answer' => (isset($postData['questionAnswer'])?$postData['questionAnswer']:''),
                        'answer_by' => (isset($postData['questionAnswer']) && $postData['questionAnswer'] != ""?Mava_Visitor::getUserId():0),
                        'sort_order' => (isset($postData['questionSortOrder'])?(int)$postData['questionSortOrder']:0),
                        'status' => (isset($postData['questionAnswer']) && $postData['questionAnswer'] != ""?'answered':'new'),
                    ));
                    if($askDW->save()){
                        Mava_Url::redirect(Mava_Url::getPageLink('admin/qa/index', array('updated' => 1)));
                    }else{
                        print_r($askDW->getErrors());
                        $error = __('can_not_edit_question');
                    }
                }
            }
            $viewParams['error_message'] = $error;
            return $this->responseView('Admin_View_Qa_Edit', array_merge($viewParams, $postData));
        }else{
            return $this->responseError(__('question_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    public function deleteAction(){
        $id = Mava_Url::getParam('id');
        $askModel = $this->_getAskModel();
        if($id > 0 && $question = $askModel->getById($id)){
            if($question['status'] != 'deleted'){
                $askDW = $this->_getAskDataWriter();
                $askDW->setExistingData($id);
                $askDW->set('status','deleted');
                if(!$askDW->save()){
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('can_not_delete_question')
                    ));
                }
            }
            return $this->responseJson(array(
                'status' => 1,
                'message' => __('question_deleted')
            ));
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('question_not_found')
            ));
        }
    }

    public function indexAction(){
        Mava_Application::set('seo', array(
            'title' => __('admin_qa')
        ));
        $page = max((int)Mava_Url::getParam('page'),1);
        $limit = 50;
        $skip = ($page-1)*$limit;
        $askModel = $this->_getAskModel();
        $questions = $askModel->getList($skip, $limit);
        $pagination = Mava_View::buildPagination(Mava_Url::getPageLink('admin/qa/index'),ceil($questions['total']/$limit),$page);
        $viewParams = array(
            'questions' => $questions['items'],
            'total' => $questions['total'],
            'page' => $page,
            'skip' => $skip,
            'limit' => $limit,
            'pagination' => $pagination
        );
        return $this->responseView('Admin_View_Qa_List', $viewParams);
    }

    /**
     * @return Index_Model_Ask
     */
    protected function _getAskModel(){
        return $this->getModelFromCache('Index_Model_Ask');
    }

    /**
     * @return Index_DataWriter_Ask
     * @throws Mava_Exception
     */
    protected function _getAskDataWriter(){
        return Mava_DataWriter::create('Index_DataWriter_Ask');
    }
}