<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 4/25/14
 * Time: 2:10 PM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Model_Option extends Mava_Model {
    /**
     * @param $optionID
     * @param $data
     * @return bool
     */
    public function editOption($optionID, $data, $optionDisplayInGroup = array(), $optionGroupDisplayOrder = array()){
        $db = $this->_getDb();
        if(sizeof($data) > 0){
            $setString = array();
            foreach($data as $k => $v){
                $setString[] = "`". $k ."`='". addslashes($v) ."'";
            }
            $db->query("UPDATE #__option SET ". implode(',', $setString) ." WHERE `option_id`='". addslashes($optionID) ."'");

            if(sizeof($optionDisplayInGroup) > 0){
                $db->delete('#__option_group_relation','`option_id`="'. addslashes($optionID) .'"');
                $count = 0;
                foreach($optionDisplayInGroup as $item){
                    $sort = 0;
                    if(isset($optionGroupDisplayOrder[$count])){
                        $sort = (int)$optionGroupDisplayOrder[$count];
                    }
                    $db->add("#__option_group_relation",array(
                        'option_id' => (isset($data['option_id'])?$data['option_id']:$optionID),
                        'group_id' => $item,
                        'display_order' => $sort
                    ));
                    $count++;
                }
            }
            return true;
        }else{
            return false;
        }
    }

    public function deleteOption($optionID){
        $this->_getDb()->delete('#__option_group_relation','`option_id`="'. addslashes($optionID) .'"');
        $this->_getDb()->delete('#__option','`option_id`="'. addslashes($optionID) .'"');
        return true;
    }

    /**
     * @return array
     */
    public function getAllOptionGroup(){
        $optionGroup = $this->_getDb()->query("SELECT * FROM #__option_group ". (Mava_Application::debugMode()?"":"WHERE `debug_only`='0'") ." ORDER BY `display_order` ASC");
        return $optionGroup->rows;
    }

    public function isOptionGroupExist($groupID, $excludedID = array()){
        $whereExcluded = '';
        if(sizeof($excludedID) > 0){
            $whereExcluded = " AND `group_id` NOT IN (". Mava_String::doImplode($excludedID) .")";
        }
        $group = $this->_getDb()->query("SELECT COUNT(*) AS 'total' FROM #__option_group WHERE `group_id`='". addslashes($groupID) ."' ". $whereExcluded);
        if($group->row['total'] > 0){
            return true;
        }else{
            return false;
        }
    }

    public function isOptionExist($optionID, $excludedID = array()){
        $whereExcluded = '';
        if(sizeof($excludedID) > 0){
            $whereExcluded = " AND `option_id` NOT IN (". Mava_String::doImplode($excludedID) .")";
        }
        $option = $this->_getDb()->query("SELECT COUNT(*) AS 'total' FROM #__option WHERE `option_id`='". addslashes($optionID) ."' ". $whereExcluded);
        if($option->row['total'] > 0){
            return true;
        }else{
            return false;
        }
    }

    public function addOptionGroup($groupID, $displayOrder = 0, $debugOnly = 0, $addonID = ''){
        $db = $this->_getDb();
        $db->add('#__option_group',array(
            'group_id' => $groupID,
            'display_order' => (int)$displayOrder,
            'debug_only' => (int)$debugOnly,
            'addon_id' => addslashes($addonID)
        ));
        return true;
    }

    public function editOptionGroup($groupId, $data = array()){
        if($groupId!="" && sizeof($data) > 0){
            $db = $this->_getDb();
            $setString = array();
            foreach($data as $k => $v){
                $setString[] = "`". $k ."`='". addslashes($v) ."'";
            }
            $db->query("UPDATE #__option_group SET ". implode(',', $setString) ." WHERE `group_id`='". addslashes($groupId) ."'");
            if(isset($data['group_id']) && $data['group_id']!=$groupId){
                $db->query("UPDATE #__option_group_relation SET `group_id`='". addslashes($data['group_id']) ."' WHERE `group_id`='". addslashes($groupId) ."'");
            }
            return true;
        }else{
            return false;
        }


    }

    /**
     * @param $groupID
     * @return bool | array
     */
    public function getOptionGroupById($groupID = ''){
        if(trim($groupID)==''){
            return false;
        }
        $group = $this->_getDb()->query("SELECT * FROM #__option_group WHERE `group_id`='". addslashes($groupID) ."'");
        if($group->num_rows > 0){
            return $group->row;
        }else{
            return false;
        }
    }

    /**
     * @param $groupID
     * @return array
     */
    public function getOptionByGroupId($groupID){
        $options = $this->_getDb()->query("SELECT o.*,r.group_id,r.display_order FROM #__option o, #__option_group_relation r WHERE o.`option_id`=r.`option_id` AND r.`group_id`='". addslashes($groupID) ."' ORDER BY r.`display_order` ASC");
        return $options->rows;
    }

    /**
     * @param $groupID
     * @return bool
     */
    public function deleteOptionGroup($groupID){
        $db = $this->_getDb();
        $db->delete('#__option_group_relation',"`group_id`='". addslashes($groupID) ."'");
        $db->delete('#__option',"`option_id` NOT IN (SELECT option_id FROM `#__option_group_relation`)");
        $db->delete('#__option_group',"`group_id`='". addslashes($groupID) ."'");
        $db->delete('#__phrase',"`title`='_option_group_description_". addslashes($groupID) ."'");
        $db->delete('#__phrase',"`title`='_option_group_title_". addslashes($groupID) ."'");
        $db->delete('#__phrase_map',"`title`='_option_group_description_". addslashes($groupID) ."'");
        $db->delete('#__phrase_map',"`title`='_option_group_title_". addslashes($groupID) ."'");
        $db->delete('#__phrase_compiled',"`title`='_option_group_description_". addslashes($groupID) ."'");
        $db->delete('#__phrase_compiled',"`title`='_option_group_title_". addslashes($groupID) ."'");
        return true;
    }

    /**
     * @param $sortData
     * @return bool
     */
    public function editOptionGroupSort($sortData){
        if(is_array($sortData) && sizeof($sortData) > 0){
            $db = $this->_getDb();
            foreach($sortData as $groupID => $displayOrder){
                $db->query("UPDATE #__option_group SET `display_order`='". (int)$displayOrder ."' WHERE `group_id`='". addslashes($groupID) ."'");
            }
        }
        return true;
    }

    /**
     * @param string $optionID
     * @param string $optionEditFormat
     * @param string $optionDataType
     * @param string $addOnID
     * @param string $optionFormatParameters
     * @param string $optionDefaultValue
     * @param string $optionSubOption
     * @param string $optionValidationCallbackClass
     * @param string $optionValidationCallbackMethod
     * @param array $optionDisplayInGroup
     * @param array $optionGroupDisplayOrder
     * @return bool
     */
    public function addOption(
        $optionID,
        $optionEditFormat,
        $optionDataType,
        $addOnID = '',
        $optionFormatParameters = '',
        $optionDefaultValue = '',
        $optionSubOption = '',
        $optionValidationCallbackClass = '',
        $optionValidationCallbackMethod = '',
        $optionDisplayInGroup = array(),
        $optionGroupDisplayOrder = array()
    ){
        $db = $this->_getDb();
        $db->add('#__option',array(
            'option_id' => $optionID,
            'option_value' => $optionDefaultValue,
            'default_value' => $optionDefaultValue,
            'edit_format' => $optionEditFormat,
            'edit_format_params' => $optionFormatParameters,
            'data_type' => $optionDataType,
            'sub_options' => $optionSubOption,
            'validation_class' => $optionValidationCallbackClass,
            'validation_method' => $optionValidationCallbackMethod,
            'addon_id' => $addOnID
        ));

        if(sizeof($optionDisplayInGroup) > 0){
            $count = 0;
            foreach($optionDisplayInGroup as $item){
                $sort = 0;
                if(isset($optionGroupDisplayOrder[$count])){
                    $sort = (int)$optionGroupDisplayOrder[$count];
                }
                $db->add("#__option_group_relation",array(
                    'option_id' => $optionID,
                    'group_id' => $item,
                    'display_order' => $sort
                ));
                $count++;
            }
        }
        return true;
    }

    /**
     * @return array
     */
    public function getAllOption(){
        $db = $this->_getDb();
        $options = $db->query("SELECT * FROM #__option");
        return $options->rows;
    }

    /**
     * @param $optionID
     * @return bool | array
     */
    public function getOptionById($optionID){
        $db = $this->_getDb();
        $option = $db->query("SELECT * FROM #__option WHERE `option_id`='". addslashes($optionID) ."'");
        if($option->num_rows > 0){
            return $option->row;
        }else{
            return false;
        }
    }

    /**
     * @param $sortData
     * @return bool
     */
    public function editOptionSort($sortData, $groupID){
        if(is_array($sortData) && sizeof($sortData) > 0){
            $db = $this->_getDb();
            foreach($sortData as $optionID => $displayOrder){
                $db->query("UPDATE #__option_group_relation SET `display_order`='". (int)$displayOrder ."' WHERE `group_id`='". addslashes($groupID) ."' AND `option_id`='". addslashes($optionID) ."'");
            }
        }
        return true;
    }

    public function getOptionDisplayData($optionID){
        $db = $this->_getDb();
        $sortData = $db->query("SELECT * FROM #__option_group_relation WHERE `option_id`='". addslashes($optionID) ."'");
        return $sortData->rows;
    }
}