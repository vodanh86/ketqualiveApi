<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 3/14/14
 * Time: 10:26 PM
 * To change this template use File | Settings | File Templates.
 */
class Mava_View {
    protected $_layout = 'default';
    protected $_layoutDir = '';
    protected $_pageContent = '';
    protected $_layoutFolder = 'template/layout';
    public static $jsFolder = 'template/js';
    public static $cssFolder = 'template/css';

    public function __construct(){
        $this->_layoutDir = BASEDIR .'/'.  $this->_layoutFolder;
    }

    public static function getCssUrl($filename){
        if($filename!=""){
            return get_static_domain() .'/'.  self::$cssFolder .'/'. $filename .'.css';
        }else{
            return '';
        }
    }

    public static function getJsUrl($filename){
        if($filename!=""){
            return get_static_domain() .'/'.  self::$jsFolder ."/". $filename .'.js';
        }else{
            return '';
        }
    }

    public function deploy(){
        if(file_exists($this->_layoutDir .'/'. $this->_layout .'.php')){
            $seo_config = Mava_Application::get('seo');
            $options = Mava_Application::getOptions();
            if(!isset($seo_config['title']) || $seo_config['title'] == ""){
                $seo_config['title'] = $options->seo_title;
            }
            if(!isset($seo_config['description']) || $seo_config['description'] == ""){
                $seo_config['description'] = $options->seo_description;
            }
            if(!isset($seo_config['robots']) || $seo_config['robots'] == ""){
                $seo_config['robots'] = $options->seo_robots;
            }
            if(!isset($seo_config['keywords']) || $seo_config['keywords'] == ""){
                $seo_config['keywords'] = $options->seo_keywords;
            }
            if(!isset($seo_config['canonical']) || $seo_config['canonical'] == ""){
                $seo_config['canonical'] = Mava_Url::getCurrentAddress();
            }
            if(!isset($seo_config['image']) || $seo_config['image'] == ""){
                $seo_config['image'] = get_option_file_url($options->seo_image);
            }

            Mava_Application::set('seo', $seo_config);
            ob_start();
            $pageContent = $this->_pageContent;
            include($this->_layoutDir .'/'. $this->_layout .'.php');
            $bufferedContents = ob_get_contents();
            ob_end_clean();
            echo $bufferedContents;
        }else{
            throw new Mava_Exception('Layout not found ('. $this->_layoutDir .'/'. $this->_layout  .'.php)');
        }
    }

    public function setPageContent($content){
        $this->_pageContent = $content;
    }

    public function setLayout($layoutName){
        if(file_exists($this->_layoutDir .'/'. $layoutName .'.php')){
            $this->_layout = $layoutName;
        }else{
            throw new Mava_Exception('Layout not found ('. $this->_layoutDir .'/'. $layoutName .'.php)');
        }
    }

    public static function getView($viewName, $params = array(), $checkExist = true){
        Mava_Event::fire('before_get_view',array(&$viewName,&$params));
        $viewPath = Mava_Loader::getInstance()->autoloaderClassToFile($viewName);
        if(file_exists($viewPath)){
            ob_start();
            if(is_array($params)){
                foreach($params as $k => $v){
                    $$k = $v;
                }
            }
            include($viewPath);
            $bufferedContents = ob_get_contents();
            ob_end_clean();
            Mava_Event::fire('after_return_view',array($viewName,&$bufferedContents));
            return $bufferedContents;
        }else if($checkExist){
            throw new Mava_Exception('View not found ('. $viewPath .')');
        }else{
            return false;
        }
    }

    public static function buildBreadcrumbs($breadcrumbs = array(), $separator = '&gt;'){
        if(isset($breadcrumbs) && is_array($breadcrumbs) && sizeof($breadcrumbs) > 0){
            $results = array();
            foreach($breadcrumbs as $item){
                if($item['url']!=""){
                    $results[] = '<a href="'. $item['url'] .'" class="breadcrumb_item">'. htmlspecialchars($item['text']) .'</a>';
                }else{
                    $results[] = '<span class="breadcrumb_item">'. htmlspecialchars($item['text']) .'</span>';
                }
            }
            return implode('<span class="breadcrumb_separator">'. $separator .'</span>',$results);
        }else{
            return '';
        }
    }

    public static function buildFrontPageBreadcrumbs($breadcrumbs = array()){
        if(isset($breadcrumbs) && is_array($breadcrumbs) && sizeof($breadcrumbs) > 0){
            $results = '';
            $count = 0;
            $total = count($breadcrumbs);
            foreach($breadcrumbs as $item){
                $count++;
                if($item['url']!=""){
                    $results .= '<li'. ($count==$total?' class="active"':'') .'><a href="'. $item['url'] .'" class="breadcrumb_item">'. htmlspecialchars($item['text']) .'</a></li>';
                }else{
                    $results .= '<li'. ($count==$total?' class="active"':'') .'><span class="breadcrumb_item">'. htmlspecialchars($item['text']) .'</span></li>';
                }
            }
            return $results;
        }else{
            return '';
        }
    }

    public static function buildPagination($baseUrl, $totalPage, $currentPage, $offsetPage = 5, $addClass = ''){
        if(strpos($baseUrl,'?') > 1){
            $indicate = '&';
        }else{
            $indicate = '?';
        }
        if($totalPage > 1){
            $output = "<div class='mava_pagination'><div class='mava_pagination_inner ". $addClass ."'>";
            $page = max($currentPage,1);
            $start = $page - $offsetPage;
            if($start < 1){
                $start = 1;
            }

            $end = $page + $offsetPage;
            if($end > $totalPage){
                $end = $totalPage;
            }

            if($page > 1){
                $output .= "<a href='". $baseUrl . $indicate .'page='. ($page-1) ."' class='prev' title='". __('prev_page') ."'><span class='prev_page_icon'></span>". __('prev_page') ."</a> ";
            }else{
                $output .= "<a href='javascript:void(0);' class='prev disabled' title='". __('prev_page') ."'><span class='prev_page_icon'></span>". __('prev_page') ."</a> ";
            }


            if($page-$offsetPage > 1){
                $output .= " <a href='". $baseUrl ."'>1</a> ";
            }

            if($page > ($offsetPage+2)){
                 $output .= "<a href='javascript:void(0);' class='disabled' style='border: 0;background: none;'>...</a>";
             }

            for($i=$start;$i<= $end; $i++){
                if($i == $page){
                    $output .= " <a href='javascript:void(0);' class='selected'>". $i ."</a> ";
                }else{
                    $output .= " <a href='". $baseUrl . $indicate .'page='. $i ."'>". $i ."</a> ";
                }

            }

            if($page < ($totalPage-$offsetPage-1)){
                $output .= "<a href='javascript:void(0);' class='disabled' style='border: 0;background: none;'>...</a>";
            }


            if($page+$offsetPage < $totalPage){
                $output .= " <a href='". $baseUrl . $indicate .'page='. $totalPage ."'>". $totalPage ."</a> ";
            }

            if($page < $totalPage){
                $output .= "<a href='". $baseUrl . $indicate .'page='. ($page+1) ."' class='next' title='". __('next_page') ."'>". __('next_page') ."<span class='next_page_icon'></span></a> ";
            }else{
                $output .= "<a href='javascript:void(0);' class='next disabled' title='". __('next_page') ."'>". __('next_page') ."<span class='next_page_icon'></span></a> ";
            }

            $output .= '</div></div>';
            return $output;
        }else{
            return '';
        }
    }

    public static function buildPaginationBootrap($baseUrl, $totalPage, $currentPage, $offsetPage = 5, $addClass = ''){
        if(strpos($baseUrl,'?') > 1){
            $indicate = '&';
        }else{
            $indicate = '?';
        }
        if($totalPage > 1){
            $output = "<div class='dataTables_paginate paging_simple_numbers' id='datatable_paginate'><ul class='pagination'>";
            $page = max($currentPage,1);
            $start = $page - $offsetPage;
            if($start < 1){
                $start = 1;
            }

            $end = $page + $offsetPage;
            if($end > $totalPage){
                $end = $totalPage;
            }

            if($page > 1){
                $output .= "<li class='page-item'><a href='". $baseUrl . $indicate .'page='. ($page-1) ."' class='page-link' title='". __('prev_page') ."'>". __('prev_page') ."</a></li> ";
            }else{
                $output .= "<li class='page-item disabled'><a href='javascript:void(0);' class='page-link' title='". __('prev_page') ."'><span class='prev_page_icon'></span>". __('prev_page') ."</a></li> ";
            }


            if($page-$offsetPage > 1){
                $output .= " <li class='page-item'><a class='page-link' href='". $baseUrl ."'>1</a></li> ";
            }

            if($page > ($offsetPage+2)){
                 $output .= "<li class='page-item disabled'><a href='javascript:void(0);' class='page-link' style='border: 0;background: none;'>...</a></li>";
             }

            for($i=$start;$i<= $end; $i++){
                if($i == $page){
                    $output .= " <li class='page-item active'><span class='page-link'>". $i ."</span></li> ";
                }else{
                    $output .= " <li class='page-item'><a class='page-link' href='". $baseUrl . $indicate .'page='. $i ."'>". $i ."</a></li> ";
                }

            }

            if($page < ($totalPage-$offsetPage-1)){
                $output .= "<li class='page-item disabled'><a href='javascript:void(0);' class='page-link' style='border: 0;background: none;'>...</a></li>";
            }


            if($page+$offsetPage < $totalPage){
                $output .= " <li class='page-item'><a class='page-link' href='". $baseUrl . $indicate .'page='. $totalPage ."'>". $totalPage ."</a></li> ";
            }

            if($page < $totalPage){
                $output .= "<li class='page-item'><a class='page-link' href='". $baseUrl . $indicate .'page='. ($page+1) ."' title='". __('next_page') ."'>". __('next_page') ."<span class='next_page_icon'></span></a></li> ";
            }else{
                $output .= "<li class='page-item disabled'><a href='javascript:void(0);' class='page-link' title='". __('next_page') ."'>". __('next_page') ."<span class='next_page_icon'></span></a></li> ";
            }

            $output .= '</ul></div>';
            return $output;
        }else{
            return '';
        }
    }
}