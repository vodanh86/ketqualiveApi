<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 3/18/14
 * Time: 1:54 PM
 * To change this template use File | Settings | File Templates.
 */
class Admin_Controller_Index extends Mava_AdminController {
    public function indexAction(){
        // seo
        Mava_Application::set('seo/title',__('admin_page'));
        return $this->responseView('Admin_View_Index');
    }
}