<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 5/7/14
 * Time: 10:34 AM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Helper_Input {
    public static function renderSelectOptionHtml($data, $selectedItem = null, $id = '', $class = '', $name = ''){
        $selectHtml = '<select'. ($name!=''?' name="'. htmlspecialchars($name) .'"':'') . ($id!=''?' id="'. htmlspecialchars($id) .'"':'') . ($class!=''?' class="'. htmlspecialchars($class) .'"':'') .'>';
        if(sizeof($data) > 0){
            foreach($data as $item){
                if($item['value']!="" && $item['text']!=""){
                    $selectHtml .= '<option value="'. $item['value'] .'"'. ($item['value']==$selectedItem?' selected':'') .'>'. $item['text'] .'</option>';
                }
            }
        }
        $selectHtml .= '</select>';
        return $selectHtml;
    }
}