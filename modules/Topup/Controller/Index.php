<?php
class Topup_Controller_Index extends Mava_Controller {
    
    public function indexAction(){
        return $this->responseView('Topup_View_Result', [ ]);    
    }
}
 