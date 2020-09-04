<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/25/16
 * @Time: 1:02 AM
 */
class Megabook_Model_Transaction extends Mava_Model {
    const TYPE_ADD = 'add';
    const TYPE_SUB = 'sub';

    public function getById($id = 0){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__agency_transaction_log WHERE `id`='". (int)$id ."'");
        }else{
            return false;
        }
    }

    public function getList($skip = 0, $limit = 10, $agencyId = 0){
        $db = $this->_getDb();
        $where = '';
        if($agencyId > 0){
            $where .= 'WHERE `agency_id`="'. (int)$agencyId .'"';
        }

        $items = $db->fetchAll("SELECT * FROM #__agency_transaction_log ". $where ." ORDER BY `created_date` DESC LIMIT ". $skip .",". $limit);
        $count = $db->fetchRow("SELECT COUNT(*) AS 'total' FROM #__agency_transaction_log ". $where);
        return array(
            'items' => $items,
            'total' => $count['total']
        );
    }

    public function add($agencyId, $amount, $reason = ''){
        $agencyModel = $this->_getAgencyModel();
        $agency = $agencyModel->getById($agencyId);
        if($agency){
            // log transaction
            $transactionDW = $this->_getTransactionDataWriter();
            $transactionDW->bulkSet(array(
                    'agency_id' => $agencyId,
                    'transaction_amount' => $amount,
                    'transaction_type' => self::TYPE_ADD,
                    'reason' => $reason,
                    'before_change' => $agency['balance'],
                    'after_change' => ($agency['balance'] + $amount),
                    'created_date' => time(),
                    'created_ip' => ip()
                ));
            if($transactionDW->save()){
                // add to agency balance
                $agencyDW = $this->_getAgencyDataWriter();
                $agencyDW->setExistingData($agencyId);
                $agencyDW->bulkSet(array(
                        'balance' => ($agency['balance'] + $amount)
                    ));
                if($agencyDW->save()){
                    return array(
                        'status' => 1,
                        'message' => __('add_agency_balance_success')
                    );
                }else{
                    return array(
                        'status' => -1,
                        'message' => __('could_not_add_agency_balance')
                    );
                }
            }else{
                return array(
                    'status' => -1,
                    'message' => __('could_not_add_agency_balance')
                );
            }

        }else{
            return array(
                'status' => -1,
                'message' => __('agency_not_found')
            );
        }
    }

    public function sub($agencyId, $amount, $reason = ''){
        $agencyModel = $this->_getAgencyModel();
        $agency = $agencyModel->getById($agencyId);
        if($agency){
            if($agency['balance'] < $amount){
                return array(
                    'status' => -1,
                    'message' => __('agency_balance_not_enough')
                );
            }else{
                // log transaction
                $transactionDW = $this->_getTransactionDataWriter();
                $transactionDW->bulkSet(array(
                        'agency_id' => $agencyId,
                        'transaction_amount' => abs($amount),
                        'transaction_type' => self::TYPE_SUB,
                        'reason' => $reason,
                        'before_change' => $agency['balance'],
                        'after_change' => ($agency['balance'] - abs($amount)),
                        'created_date' => time(),
                        'created_ip' => ip()
                    ));
                if($transactionDW->save()){
                    // add to agency balance
                    $agencyDW = $this->_getAgencyDataWriter();
                    $agencyDW->setExistingData($agencyId);
                    $agencyDW->bulkSet(array(
                        'balance' => ($agency['balance'] - abs($amount))
                    ));
                    if($agencyDW->save()){
                        return array(
                            'status' => 1,
                            'message' => __('sub_agency_balance_success')
                        );
                    }else{
                        return array(
                            'status' => -1,
                            'message' => __('could_not_sub_agency_balance')
                        );
                    }
                }else{
                    return array(
                        'status' => -1,
                        'message' => __('could_not_sub_agency_balance')
                    );
                }
            }
        }else{
            return array(
                'status' => -1,
                'message' => __('agency_not_found')
            );
        }
    }

    /**
     * @return Megabook_DataWriter_Transaction
     */
    protected function _getTransactionDataWriter(){
        return Mava_DataWriter::create('Megabook_DataWriter_Transaction');
    }

    /**
     * @return Megabook_DataWriter_Agency
     */
    protected function _getAgencyDataWriter(){
        return Mava_DataWriter::create('Megabook_DataWriter_Agency');
    }

    /**
     * @return Megabook_Model_Agency
     */
    protected function _getAgencyModel(){
        return $this->getModelFromCache('Megabook_Model_Agency');
    }
}