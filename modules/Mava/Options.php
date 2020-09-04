<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 4/25/14
 * Time: 10:39 AM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Options
{
    protected $_options = array();
    protected $_initialized = false;

    public function __construct()
    {
        if($this->_initialized){
            return;
        }
        $optionModel = Mava_Model::create('Mava_Model_Option');
        $options = $optionModel->getAllOption();
        $initOption = array();
        if(sizeof($options) > 0){
            foreach($options as $item){
                if($item['option_value']==''){
                    $item['option_value'] = $item['default_value'];
                }
                $oVal = '';
                if($item['data_type']=='integer'){
                    $oVal = (int)$item['option_value'];
                }else if($item['data_type']=='boolean'){
                    if((int)$item['option_value']==1){
                        $oVal = 1;
                    }else{
                        $oVal = 0;
                    }
                }else if($item['data_type']=='string'){
                    $oVal = $item['option_value'];
                }else if($item['data_type']=='array'){
                    if(Mava_String::isJson($item['option_value'])){
                        $oVal = json_decode($item['option_value'],true);
                    }else{
                        $oVal = array();
                    }
                }
                $initOption[$item['option_id']] = $oVal;
            }
        }
        $this->setOptions($initOption);
        $this->_initialized = true;
    }

    public function get($optionName, $subOption = null)
    {
        if (!isset($this->_options[$optionName]))
        {
            return null;
        }

        $option = $this->_options[$optionName];

        if (is_array($option))
        {
            if ($subOption === null)
            {
                return (isset($option[$optionName]) ? $option[$optionName] : $option);
            }
            else if ($subOption === false)
            {
                return $option;
            }
            else
            {
                return (isset($option[$subOption]) ? $option[$subOption] : null);
            }
        }
        else
        {
            return ($subOption === null ? $option : null);
        }
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function setOptions(array $options)
    {
        $this->_options = $options;
    }

    public function __get($option)
    {
        return $this->get($option);
    }

    public function __isset($option)
    {
        return ($this->get($option) !== null);
    }

    public function set($option, $subOption, $value = null)
    {
        if ($value === null)
        {
            $value = $subOption;
            $subOption = false;
        }

        if ($subOption === false)
        {
            $this->_options[$option] = $value;
        }
        else if (isset($this->_options[$option]) && is_array($this->_options[$option]))
        {
            $this->_options[$option][$subOption] = $value;
        }
        else
        {
            throw new Mava_Exception('Tried to write sub-option to invalid/non-array option.');
        }
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }
}