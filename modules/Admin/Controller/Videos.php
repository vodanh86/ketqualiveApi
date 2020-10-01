<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 8/1/16
 * @Time: 4:42 PM
 */
class Admin_Controller_Videos extends Mava_AdminController {
    public function exportSubscribeAction(){
        //get subscribe
        $subModel = $this->_getSubscribeModel();
        $items = $subModel->getList(0,5000,'subscribe');
        require_once(BASEDIR .'/modules/PHPExcel/PHPExcel.php');
        // Create new PHPExcel object

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator(__('site_name'))
            ->setLastModifiedBy(__('site_name'))
            ->setTitle( __('subscribe') .' - '. date('d-m-Y H:i'))
            ->setSubject(__('subscribe'))
            ->setCategory(__('subscribe'));

        $activeSheet = $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        $redColor = new PHPExcel_Style_Color('FFFF3300');
        $grayColor = new PHPExcel_Style_Color('FF999999');
        //1
        $created_date = new PHPExcel_RichText();
        $created_date->createTextRun(__('created_date'))->getFont()->setBold(true);
        //2
        $email = new PHPExcel_RichText();
        $email->createTextRun(__('email'))->getFont()->setBold(true);

        $activeSheet->getColumnDimension('A')->setWidth(10);
        $activeSheet->getColumnDimension('B')->setWidth(20);
        $activeSheet->getColumnDimension('C')->setWidth(20);
        $activeSheet->getColumnDimension('D')->setWidth(15);
        $activeSheet->setCellValue("A1", $created_date)->getStyle("A1")->getFont()->setBold(true);
        $activeSheet->setCellValue("B1", $email)->getStyle("B1")->getFont()->setBold(true);

        $count = 1;
        if(isset($items['items']) && is_array($items['items']) && count($items['items']) > 0){
            foreach($items['items'] as $item){
                $count++;
                $activeSheet->setCellValue("A". $count, date('d/m/Y H:i', $item['created_time']));
                $activeSheet->setCellValue("B". $count, $item['email']);
            }
        }

        $writer = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $file_name = Mava_String::unsignString(__('site_name') .'-'. __('subscribe'),'-') .'_'. date('d-m-Y H:i');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'. $file_name .'.xlsx"');
        $writer->save('php://output');
        die;
    }

    public function indexAction(){
        Mava_Application::set('seo', array(
            'title' => __('admin_videos')
        ));
        $page = max((int)Mava_Url::getParam('page'),1);
        $limit = 50;
        $skip = ($page-1)*$limit;
        $subVideo = $this->_getVideoModel();
        $videos = $subVideo->getPageList($skip, $limit);
        $baseUrl = Mava_Url::removeParam(Mava_Url::getCurrentAddress(),array('page'));
        $pagination = Mava_View::buildPagination($baseUrl,ceil($videos['total']/$limit),$page);
        $viewParams = array(
            'videos' => $videos['items'],
            'total' => $videos['total'],
            'page' => $page,
            'skip' => $skip,
            'limit' => $limit,
            'pagination' => $pagination
        );
        return $this->responseView('Admin_View_Video_List', $viewParams);
    }

    public function deleteAction(){
        $userID = (int)Mava_Url::getParam('videoID');
        if($userID > 0){
            $userModel = $this->_getVideoModel();
            $user = $userModel->getById($userID, false);
            if($user){
                $check = $userModel->deleteById($userID);
                if($check){
                    return $this->responseJson(array(
                        'status' => 1,
                        'message' => __('video_deleted')
                    ));
                }else{
                    return $this->responseJson(array(
                        'status' => -1,
                        'message' => __('can_not_delete_video')
                    ));
                }
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('video_not_found')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('video_not_found')
            ));
        }
    }

    public function addAction(){
        Mava_Application::set('seo/title',__('add_video'));
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/videos/index'),
            'text' => __('admin_videos')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_videos')
        );

        $error_message = '';

        $userModel = $this->_getVideoModel();
        $userData = array();
        if(Mava_Url::isPost()){
            $videoData = array(
                'title' => Mava_Url::getParam('title'),
                'second' => 300,
                'token' => "12",
                'youtube_id' => (int)Mava_Url::getParam('youtubeId'),
                'view' => 0,
                'created_by' => 1
            );

            $userID = $userModel->insert($videoData);

            if($userID > 0){
                Mava_Url::redirect(Mava_Url::getPageLink('admin/videos/index', array('added' => 1)));
            }else{
                $error_message = __('can_not_add_video');
            }
        }
        return $this->responseView('Admin_View_Video_Add', array(
            'breadcrumbs' => $breadcrumbs,
            'user' => $userData,
            'error_message' => $error_message,
        ));
    }

    /**
     * @return API_Model_Video
     */
    protected function _getVideoModel()
    {
        return $this->getModelFromCache('API_Model_Video');
    }

    /**
     * @return Index_DataWriter_Subscribe
     * @throws Mava_Exception
     */
    protected function _getSubscribeDataWriter(){
        return Mava_DataWriter::create('Index_DataWriter_Subscribe');
    }
}