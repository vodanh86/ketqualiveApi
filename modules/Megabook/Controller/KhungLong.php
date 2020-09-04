<?php
class Megabook_Controller_KhungLong extends Mava_Controller {
    public function indexAction(){
        Mava_Application::set('body_id', 'tai_lieu_khung_long_toan');
        Mava_Application::set('seo/title', __('khunglong_document_seo_title'));
        return $this->responseView('Megabook_View_KhungLong_Toan');
    }

    public function downloadAction(){
        Mava_Application::set('body_id', 'tai_lieu_khung_long_toan');
        Mava_Application::set('seo/title', __('khunglong_document_seo_title'));
        $docId = Mava_Url::getParam('doc_index');
        if($docId > 0){
            $optionName = 'khunglongLop'. $docId;
            $linkDownload = Mava_Application::getOptions()->{$optionName};
            return $this->responseView('Megabook_View_KhungLong_ToanDownload', array(
                    'linkDownload' => $linkDownload,
                    'docId' => $docId
                ));
        }else{
            return $this->indexAction();
        }
    }
}