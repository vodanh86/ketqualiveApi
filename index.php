<?php
    if(!session_id()){
        session_start();
    }
    $startTime = microtime(true);
    $fileDir = dirname(__FILE__);
    define('DS',DIRECTORY_SEPARATOR);
    define('BASEDIR',$fileDir);
    define('MODULE_DIR',$fileDir ."/modules");
    define('IMAGE_DIR',$fileDir ."/data/images");
    require(MODULE_DIR ."/Mava/Loader.php");
    require(BASEDIR .'/helpers.php');
    require(BASEDIR .'/simple_html_dom.php');
    require(BASEDIR .'/unirest/src/Unirest.php');
    Mava_Loader::getInstance()->setupAutoloader(MODULE_DIR);

    $app = new Mava_Application();
    Mava_Application::set('page_start_time', $startTime);
    $app->initApplication($fileDir .'/config.php', $fileDir .'/env.php');
    /*if(rand(0,1)==2){
        Mava_Event::addListener('before_call_action','Index_Listener::beforeCallAction');
        Mava_Event::addListener('before_get_view','Index_Listener::beforeGetView');
        Mava_Event::addListener('after_return_view','Index_Listener::afterReturnView');
    }
    Mava_Event::addListener('load_class_model','Index_Listener::loadClassModel');
    Mava_Event::addListener('load_class_controller','Index_Listener::loadClassController');*/
    $app->run();

/*
    before_load_controller(&$controller)
    before_call_action($controller,&$action)
    before_get_view(&$viewName,&$params)
*/
