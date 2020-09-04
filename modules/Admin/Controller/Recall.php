<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 8/1/16
 * @Time: 12:39 PM
 */
class Admin_Controller_Recall extends Mava_AdminController {
    public function exportNewRecallAction(){
        //get recall
        $recallModel = $this->_getRecallModel();
        $items = $recallModel->getList(0,5000,'recall','new');
        require_once(BASEDIR .'/modules/PHPExcel/PHPExcel.php');
        // Create new PHPExcel object

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator(__('site_name'))
            ->setLastModifiedBy(__('site_name'))
            ->setTitle( __('recall_request') .' - '. date('d-m-Y H:i'))
            ->setSubject(__('recall_request'))
            ->setCategory(__('recall_request'));

        $activeSheet = $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        $redColor = new PHPExcel_Style_Color('FFFF3300');
        $grayColor = new PHPExcel_Style_Color('FF999999');
        //1
        $created_date = new PHPExcel_RichText();
        $created_date->createTextRun(__('created_date'))->getFont()->setBold(true);
        //2
        $phone = new PHPExcel_RichText();
        $phone->createTextRun(__('phone'))->getFont()->setBold(true);
        //3
        $website_title = new PHPExcel_RichText();
        $website_title->createTextRun(__('website_title'))->getFont()->setBold(true);
        //4
        $website_url = new PHPExcel_RichText();
        $website_url->createTextRun(__('website_url'))->getFont()->setBold(true);

        $activeSheet->getColumnDimension('A')->setWidth(10);
        $activeSheet->getColumnDimension('B')->setWidth(20);
        $activeSheet->getColumnDimension('C')->setWidth(20);
        $activeSheet->getColumnDimension('D')->setWidth(15);
        $activeSheet->setCellValue("A1", $created_date)->getStyle("A1")->getFont()->setBold(true);
        $activeSheet->setCellValue("B1", $phone)->getStyle("B1")->getFont()->setBold(true);
        $activeSheet->setCellValue("C1", $website_title)->getStyle("C1")->getFont()->setBold(true);
        $activeSheet->setCellValue("D1", $website_url)->getStyle("D1")->getFont()->setBold(true);

        $count = 1;
        if(isset($items['items']) && is_array($items['items']) && count($items['items']) > 0){
            foreach($items['items'] as $item){
                $count++;
                $activeSheet->setCellValue("A". $count, date('d/m/Y H:i', $item['created_time']));
                $activeSheet->setCellValue("B". $count, $item['phone']);
                $activeSheet->setCellValue("C". $count, $item['title']);
                $activeSheet->setCellValue("D". $count, $item['url']);
            }
        }

        $writer = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $file_name = Mava_String::unsignString(__('site_name') .'-'. __('recall_request'),'-') .'_'. date('d-m-Y H:i');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'. $file_name .'.xlsx"');
        $writer->save('php://output');
        die;
    }

    public function indexAction(){
        Mava_Application::set('seo', array(
            'title' => __('admin_callback_request')
        ));
        $type = Mava_Url::getParam('type');
        $status = Mava_Url::getParam('status');
        $page = max((int)Mava_Url::getParam('page'),1);
        $limit = 50;
        $skip = ($page-1)*$limit;
        $recallModel = $this->_getRecallModel();
        $recall = $recallModel->getList($skip, $limit, $type, $status);
        $baseUrl = Mava_Url::removeParam(Mava_Url::getCurrentAddress(),array('page'));
        $pagination = Mava_View::buildPagination($baseUrl,ceil($recall['total']/$limit),$page);
        $viewParams = array(
            'recall' => $recall['items'],
            'total' => $recall['total'],
            'page' => $page,
            'skip' => $skip,
            'limit' => $limit,
            'pagination' => $pagination
        );
        return $this->responseView('Admin_View_Recall_List', $viewParams);
    }

    public function changeStatusAction(){
        $id = Mava_Url::getParam('id');
        $status = Mava_Url::getParam('status');
        $recallModel = $this->_getRecallModel();
        if($id > 0 && in_array($status, array('new','read','done','deleted')) && $recall = $recallModel->getById($id)){
            $recallDW = $this->_getRecallDataWriter();
            $recallDW->setExistingData($id);
            $recallDW->set('status', $status);
            if($recallDW->save()){
                if($status == 'deleted'){
                    $status_class = 'btn-danger';
                }else if($status == 'read'){
                    $status_class = 'btn-info';
                }else if($status == 'done'){
                    $status_class = 'btn-success';
                }else {
                    $status_class = 'btn-primary';
                }
                return $this->responseJson(array(
                    'status' => 1,
                    'recall_label' => __('recall_status_'. $status),
                    'recall_class' => $status_class,
                    'message' => __('recall_status_changed')
                ));
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('can_not_change_recall_status')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('recall_request_not_found')
            ));
        }
    }

    public function markReadAction(){
        $recallModel = $this->_getRecallModel();
        $recallModel->markReadRecall();
        return $this->responseJson(array(
            'status' => 1,
            'message' => __('success')
        ));
    }

    /**
     * @return Index_Model_Recall
     */
    protected function _getRecallModel(){
        return $this->getModelFromCache('Index_Model_Recall');
    }

    /**
     * @return Index_DataWriter_Recall
     * @throws Mava_Exception
     */
    protected function _getRecallDataWriter(){
        return Mava_DataWriter::create('Index_DataWriter_Recall');
    }
}