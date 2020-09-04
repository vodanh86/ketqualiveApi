<?php
class Megabook_Model_GiftCode extends Mava_Model {
    public function getById($id){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__gift_code WHERE `id`='". $id ."'");
        }else{
            return false;
        }
    }

    public function getByCode($code){
        if($code != ""){
            return $this->_getDb()->fetchRow("SELECT * FROM #__gift_code WHERE `code`='". $code ."'");
        }else{
            return false;
        }
    }

    public function getList($skip = 0, $limit = 10, $searchTerm = ''){
        $db = $this->_getDb();
        $where = 'WHERE 1=1';
        if($searchTerm != ""){
            $where .= " AND `code` LIKE '%". $db->quoteLike($searchTerm) ."%'";
        }
        $items = $db->fetchAll("SELECT * FROM #__gift_code ". $where ." LIMIT ". $skip .','. $limit);
        $count = $db->fetchRow("SELECT COUNT(*) AS 'total' FROM #__gift_code ". $where);
        return array(
            'items' => $items,
            'total' => $count['total']
        );
    }

    public function generate(
        $codeLength,
        $codeCount,
        $codeChar,
        $codeValueInt,
        $codeValueType,
        $codeCondProduct,
        $codeCondAmount,
        $codeStartTime,
        $codeEndTime,
        $codeNumOfUse
    ){
        $codes = array();
        for($i=0;$i<$codeCount;$i++){
            $code = Mava_String::generalHash($codeChar, $codeLength);
            $codes[$code] = "('". $code ."','". $codeValueInt ."','". $codeValueType ."','". $codeCondProduct ."','". $codeCondAmount ."','". $codeStartTime ."','". $codeEndTime ."','". $codeNumOfUse ."')";;
        }
        return $this->_getDb()->query("INSERT INTO #__gift_code(`code`,`value_int`,`value_type`,`cond_num_product`,`cond_total_amount`,`cond_start_time`,`cond_end_time`,`num_of_use`) VALUES". implode(',', array_values($codes)));
    }


    public function calculateValue($code, $product_count, $total_amount, $show_reason = false){
        $code = $this->getByCode($code);
        if(!$code){
            if($show_reason == true){
                return array(
                    'value' => 0,
                    'reason' => __('gift_code_do_not_exist')
                );
            }else{
                return 0;
            }
        }
        if($show_reason == true){
            if($code['cond_start_time'] > time()){
                return array(
                    'value' => 0,
                    'reason' => __('gift_code_available_at_x', array('time' => date('d/m/Y H:i', $code['cond_start_time'])))
                );
            }elseif($code['cond_end_time'] > 0 && $code['cond_end_time'] < time()){
                return array(
                    'value' => 0,
                    'reason' => __('gift_code_expired')
                );
            }elseif($code['num_of_use'] <= $code['used_count']){
                return array(
                    'value' => 0,
                    'reason' => __('gift_code_expired')
                );
            }elseif($product_count < $code['cond_num_product']){
                return array(
                    'value' => 0,
                    'reason' => __('gift_code_apply_for_x_product', array('num' => $code['cond_num_product']))
                );
            }elseif($total_amount < $code['cond_total_amount']){
                return array(
                    'value' => 0,
                    'reason' => __('gift_code_apply_for_x_amount', array('num' => Mava_String::price_format($code['cond_total_amount'])))
                );
            }elseif($code['value_int'] == 0){
                return array(
                    'value' => 0,
                    'reason' => __('gift_code_value_zero')
                );
            }else{
                if($code['value_type'] == 'fixed'){
                    $value = $code['value_int'];
                }else{
                    $value = ceil($code['value_int']*$total_amount/100);
                }
                return array(
                    'value' => $value,
                    'reason' => __('gift_code_valid')
                );
            }
        }else{
            if(
                $code['cond_start_time'] > time()
                || ($code['cond_end_time'] > 0 && $code['cond_end_time'] < time())
                || $code['num_of_use'] <= $code['used_count']
                || $product_count < $code['cond_num_product']
                || $total_amount < $code['cond_total_amount']
                || $code['value_int'] == 0
            ){
                return 0;
            }else{
                if($code['value_type'] == 'fixed'){
                    return $code['value_int'];
                }else{
                    return ceil($code['value_int']*$total_amount/100);
                }
            }
        }
    }

    public function incrementUse($code, $use_time = 1){
        return $this->_getDb()->query('UPDATE #__gift_code SET `used_count`=`used_count`+'. $use_time .' WHERE `code`="'. $code .'"');
    }

    public function decrementUse($code, $use_time = 1){
        return $this->_getDb()->query('UPDATE #__gift_code SET `used_count`=`used_count`-'. $use_time .' WHERE `code`="'. $code .'"');
    }
}