<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 4/5/14
 * Time: 11:13 AM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Helper_User {
    public static function renderUserGroupOption($optionItem){
        $userGroup = Mava_Model::create('Mava_Model_User')->getUserGroupList();
        if($optionItem['option_value']=='' && $optionItem['default_value']!=""){
            $cVal = $optionItem['default_value'];
        }else{
            $cVal = $optionItem['option_value'];
        }
        $optionData = array();
        if(sizeof($userGroup) > 0){
            foreach($userGroup as $item){
                $optionData[] = array(
                    'value' => $item['group_id'],
                    'text' => $item['group_title']
                );
            }
        }
        return Mava_Helper_Input::renderSelectOptionHtml($optionData, $cVal, 'option'. $optionItem['option_id'],'','option['. $optionItem['option_id'] .']');
    }
}