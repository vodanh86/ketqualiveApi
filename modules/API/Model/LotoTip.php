<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_LotoTip extends Mava_Model
{
    /**
     * @param int $id
     * @return bool | array
     */
    public function getById($id)
    {
        return $this->_getDb()->fetchRow("SELECT * FROM #__loto_tip WHERE `id`=" . (int)$id);
    }

    public function getTipByDay($day, $region_code, $package){
        $tip = $this->_getDb()->fetchRow("SELECT * FROM #__loto_tip WHERE `tip_date`='". $day ."' AND `region_code`='". $region_code ."' AND `pack`='". $package ."'");
        if($tip){
            return $tip;
        }else{
            if(Mava_Application::getConfig('loto_auto_tip') === true && $day == date('d-m-Y')){
                $calcTip = $this->_calculateTip($day, $region_code, $package);
                $tipDW = $this->_getTipDataWriter();
                $tipDW->bulkSet([
                    'tip_date' => $day,
                    'region_code' => $region_code,
                    'pack' => $package,
                    'num_1' => $calcTip['num_1'],
                    'num_2' => $calcTip['num_2'],
                    'num_3' => $calcTip['num_3'],
                    'reg_count' => 0
                ]);
                if($tipDW->save()){
                    return $this->getById($tipDW->get('id'));
                }else{
                    Mava_Log::error($tipDW->getErrors());
                    return false;
                }
            }else{
                return false;
            }
        }
    }

    public function increment_reg_count($id){
        return $this->_getDb()->query("UPDATE #__loto_tip SET `reg_count`=`reg_count`+1 WHERE `id`='". (int)$id ."'");
    }

    protected function _calculateTip($day, $region_code, $package){
        $exclude_pair = [0, 11, 22, 33, 44, 55, 66, 77, 88, 99];
        $num_1 = "--";
        $num_2 = "--";
        $num_3 = "--";
        switch ($package){
            case 1:
                $num_1 = $this->_random(0, 99, $exclude_pair);
                $num_2 = implode("", array_reverse(str_split(sprintf('%02d', $num_1))));
                $num_3 = $this->_random(0,99, array_merge($exclude_pair, [$num_1, $num_2]));
                break;
            case 2:
                $num_1 = $this->_random(0, 99, $exclude_pair);
                $num_2 = implode("", array_reverse(str_split(sprintf('%02d', $num_1))));
                $num_3 = $this->_random(0,99, array_merge($exclude_pair, [$num_1, $num_2]));
                break;
            case 3:
                $num_1 = $this->_random(0, 9, []);
                $num_2 = $this->_random(0, 9, [$num_1]);
                $num_3 = "";
                break;
        }
        return [
            'num_1' => $num_1,
            'num_2' => $num_2,
            'num_3' => $num_3
        ];
    }

    protected function _random($from, $to, $excludes = []){
        do{
            $num = rand($from, $to);
        }while(in_array($num, $excludes));
        return $num;
    }


    /**
     * @return API_DataWriter_LotoTip
     */
    protected function _getTipDataWriter(){
        return Mava_DataWriter::create('API_DataWriter_LotoTip');
    }
}