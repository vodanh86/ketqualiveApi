<?php
    class Index_Controller_Index extends Mava_Controller {
        public function indexAction(){
            Mava_Application::set('open_menu', 1);
            Mava_Application::set('seo', array(
                'title' => __('seo_home_title'),
                'canonical' => Mava_Url::getDomainUrl()
            ));
            Mava_Application::set('menu_selected', 'home');
            $viewParams = array(
                'day' => Mava_Url::getParam('day'),
                'month' => Mava_Url::getParam('month'),
                'year' => Mava_Url::getParam('year'),
                'province' => Mava_Url::getParam('pv')!=""?Mava_Url::getParam('pv'):'tt',
            );
            $provinceModel = $this->_getProvinceModel();
            $province = $provinceModel->getByCode($viewParams['province']);
            if($province){
                if($viewParams['day'] != ""){
                    Mava_Application::set('seo/title', 'Kết quả xổ số '. $province['title'] .' ngày '. $viewParams['day'] .'-'. $viewParams['month'] .'-'. $viewParams['year']);
                }else{
                    Mava_Application::set('seo/title', 'Kết quả xổ số hôm nay');
                }
            }
            return $this->responseView('Index_View_Index', $viewParams);
        }

        public function doVeSoAction(){
            Mava_Application::set('seo/title', 'Dò vé số');
            $province_slug = 'ket-qua-xo-so-'. Mava_Url::getParam('province_slug');
            $provinceModel = $this->_getProvinceModel();
            $province = $provinceModel->getBySlug($province_slug);
            if(!$province){
                $province = $provinceModel->getById(1);
            }
            $number = Mava_Url::getParam('number');
            $date = Mava_Url::getParam('date') ? Mava_Url::getParam('date') : date('d-m-Y', time());
            $day = date('w',date_to_time($date, '-')) + 1;
            $current_loto = Mava_Application::getConfig('loto_schedule/T'. $day);
            // sort truyền thống lên đầu tiên
            $tt = $current_loto['tt'];
            unset($current_loto['tt']);
            array_unshift($current_loto, $tt);
            $result_html = get_doveso_result_html($date, $province['code'], $number);
            return $this->responseView('Index_View_DoVeSo',array(
                'result_html' => $result_html,
                'province_slug' => Mava_Url::getParam('province_slug'),
                'date' => $date,
                'current_loto' => $current_loto,
                'number' => $number 
            ), 'doveso');
        }

        public function getLatestResultAction(){
            $date = date('d-m-Y', time());
            $province = Mava_Url::getParam('pv')!=""?Mava_Url::getParam('pv'):'tt';
            $result = $this->_getResultModel()->getResultHomeHtml($date, $province);
            return $this->responseJson(array(
                'error' => 0,
                'result' => $result
            ));
        }

        /**
         * @return Loto_Model_Province
         * @throws Mava_Exception
         */
        protected function _getProvinceModel(){
            return $this->getModelFromCache('Loto_Model_Province');
        }

        protected function _getResultModel(){
            return $this->getModelFromCache('Loto_Model_Result');
        }
    }
?>