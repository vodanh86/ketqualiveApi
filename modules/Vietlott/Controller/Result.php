<?php

class Vietlott_Controller_Result extends Mava_Controller {

	public function getResultMegaAction(){
		Mava_Application::set('seo/title', 'Kết quả Vietlot Mega 6/45');
		$visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
        
        $messageError = '';
        $result = [];
		// call API to get result
        $data = [
            'token' => $token,
            'max_id' => 0,
            'limit' => 10
        ];
        $result = Mava_API::call('vietlott/mega', $data);
        if($result['error'] == 0) {
            $result = $result['data'];
        }else {
            $messageError = $result['message'];
        }
		return $this->responseView('Vietlott_View_Result_Mega',array(
			'result' => $result,
			'messageError' => $messageError
		));
	}

    public function getResultMax4dAction(){
        Mava_Application::set('seo/title', 'Kết quả Vietlot Max 4D');
        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
        $minId = $this->_getMax4dModel()->getMinId();
        $maxId = $this->_getMax4dModel()->getMaxId();
        $messageError = '';
        $result = [];
        // call API to get result
        $data = [
            'token' => $token,
            'max_id' => 0,
            'limit' => 10
        ];
        $result = Mava_API::call('vietlott/max4d-latest', $data);
        if($result['error'] == 0) {
            $result = $result['data'];
        }else {
            $messageError = $result['message'];
        }
        return $this->responseView('Vietlott_View_Result_Max4d',array(
            'result' => $result,
            'messageError' => $messageError,
            'minId' => $minId,
            'maxId' => $maxId
        ));
    }

    public function getResultPrevMax4dAction(){
        Mava_Application::set('seo/title', 'Kết quả Vietlot Max 4D');
        $max4dId = (int)Mava_Url::getParam('id');
        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
        $minId = $this->_getMax4dModel()->getMinId();
        $maxId = $this->_getMax4dModel()->getMaxId();
        $messageError = '';
        $result = [];
        // call API to get result
        $data = [
            'token' => $token,
            'max4d_id' => $max4dId
        ];
        $result = Mava_API::call('vietlott/max4d-prev', $data);
        if($result['error'] == 0) {
            $result = $result['data'];
        }else {
            $messageError = $result['message'];
        }
        return $this->responseView('Vietlott_View_Result_Max4d',array(
            'result' => $result,
            'messageError' => $messageError,
            'minId' => $minId,
            'maxId' => $maxId
        ));
    }

    public function getResultNextMax4dAction(){
        Mava_Application::set('seo/title', 'Kết quả Vietlot Max 4D');
        $max4dId = (int)Mava_Url::getParam('id');
        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
        $minId = $this->_getMax4dModel()->getMinId();
        $maxId = $this->_getMax4dModel()->getMaxId();
        $messageError = '';
        $result = [];
        // call API to get result
        $data = [
            'token' => $token,
            'max4d_id' => $max4dId
        ];
        $result = Mava_API::call('vietlott/max4d-next', $data);
        if($result['error'] == 0) {
            $result = $result['data'];
        }else {
            $messageError = $result['message'];
        }
        return $this->responseView('Vietlott_View_Result_Max4d',array(
            'result' => $result,
            'messageError' => $messageError,
            'minId' => $minId,
            'maxId' => $maxId
        ));
    }

    public function getResultPowerAction(){
        Mava_Application::set('seo/title', 'Kết quả Vietlot Power 6/55');
        $visitor = Mava_Visitor::getInstance();
        $token = $visitor->get('token') != '' ? $visitor->get('token') : Mava_Application::get('config/guest_token');
        $messageError = '';
        $result = [];
        // call API to get result
        $data = [
            'token' => $token,
            'max_id' => 0,
            'limit' => 10
        ];
        $result = Mava_API::call('vietlott/power', $data);
        if($result['error'] == 0) {
            $result = $result['data'];
        }else {
            $messageError = $result['message'];
        }
        return $this->responseView('Vietlott_View_Result_Power',array(
            'result' => $result,
            'messageError' => $messageError
        ));
    }

    /**
     * @return Vietlott_Model_Max4d
     */
    protected function _getMax4dModel()
    {
        return $this->getModelFromCache('Vietlott_Model_Max4d');
    }

}