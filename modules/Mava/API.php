<?php
class Mava_API {
    public static function call($endpoint, $params = []){
        $controllerAction = self::getControllerAction($endpoint);
        self::setParams($params);
        $controller = new $controllerAction['controller']();
        $response = $controller->{$controllerAction['action']}();
        return $response->data;
    }

    public static function setParams($params = []){
        $_POST['source'] = 'web';
        if(is_array($params) && count($params) > 0){
            foreach($params as $k => $v){
                Mava_Url::setParam($k, $v);
            }
        }
    }

    public static function getControllerAction($endpoint){
        $controllerAction = explode('/', trim($endpoint, '/'));
        $controller = 'API_Controller_'. ucfirst($controllerAction[0]);
        $actions = explode('-', $controllerAction[1]);
        $action = '';
        for($i=0;$i<count($actions);$i++){
            $action .= ($i==0?strtolower($actions[$i]):ucfirst($actions[$i]));
        }
        $action .= 'Action';
        return [
            'controller' => $controller,
            'action' => $action
        ];
    }
}