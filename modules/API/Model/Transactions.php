<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_Transactions extends Mava_Model
{
    /**
     * @param int $id
     * @return bool | array
     */
    public function getById($id){
        return $this->_getDb()->fetchRow("SELECT * FROM #__transactions WHERE `id`=" . (int)$id);
    }

    public function getTransactonByCode($code){
        return $this->_getDb()->fetchRow("
            SELECT *
            FROM #__transactions
            WHERE `code`='".$code."'
        ");
    }

    public function saveTransaction($data){
        $transactionDW = $this->_getTransactionsDataWriter();
        $transactionDW->bulkSet([
            'code' => md5(time()),
            'token' => $data['token'],
            'card_number' => $data['card_number'],
            'card_serial' => $data['card_serial'],
            'card_value' => $data['card_value']
        ]);
        if($transactionDW->save()){
            return $this->getById($transactionDW->get('id'));
        }else{
            return false;
        }

    }

    public function updateTransaction($id, $data){
        $transactionDW = $this->_getTransactionsDataWriter();
        $transactionDW->setExistingData($id);
        $transactionDW->bulkSet($data);
        $transactionDW->save();
        return true;
    }

    public function getByUserTransactonByCode($data){
        return $this->_getDb()->fetchRow("
            SELECT *
            FROM #__transactions
            WHERE `token`='".$data['token']."'
            AND `code`='".$data['code']."'
        ");
    }

    public function checkTransactionStatus($data) {
        $user = $this->_getUserModel()->_getByToken($data['token']);
        $transaction = $this->getByUserTransactonByCode($data);
        if($transaction) {
            if($transaction['is_received'] == 0) { // thẻ chưa xử lý nạp
                if($transaction['status'] == 1) { // thành công -> xử lý + coin cho user
                    $coinRate = Mava_Application::get('config/coin_rate');
                    $supervipRate = Mava_Application::get('config/supervip_rate');
                    switch ($transaction['card_value']) {
                        case 20000:
                            $rate = $coinRate['20k'];
                            break;
                        case 50000:
                            $rate = $coinRate['50k'];
                            break;
                        case 100000:
                            $rate = $coinRate['100k'];
                            break;
                        case 200000:
                            $rate = $coinRate['200k'];
                            break;
                        case 300000:
                            $rate = $coinRate['300k'];
                            break;
                        case 500000:
                            $rate = $coinRate['500k'];
                            break;
                        case 1000000:
                            $rate = $coinRate['1000k'];
                            break;
                        default:
                            $rate = $coinRate['50k'];
                            break;
                    }
                    $coin =  $rate;
                    // check user is supervip
                    if($user['is_supervip'] == 1){
                        $coin = $coin + $supervipRate*$coin;
                    } else {
                        $totalCoinCharge = $this->_getCoinLogsModel()->getTotalCoinCharge($data['token']);
                        if($totalCoinCharge >= Mava_Application::get('config/supervip_coin')) {
                            $coin = $coin + $supervipRate*$coin;
                            $this->_getUserModel()->setSuperVip($data['token']);
                        }
                    }
                    // increase coin
                    if($this->_getUserModel()->changeCoin($data['token'], $coin)){
                        //save coin log
                        $coinLog = [
                            'token' => $data['token'],
                            'coin_before'=> $user['coin'],
                            'coin_change'=> $coin,
                            'coin_after'=> $user['coin'] + $coin,
                            'type'=> 'napthe',
                        ];
                        $this->_getCoinLogsModel()->saveLog($coinLog);
                    }

                    //update transaction
                    $this->updateTransaction($transaction['id'], array('is_received'=>1));
                    return [
                        'error' => 0,
                        'message' => 'Nạp thẻ thành công',
                        'data' => $this->_getUserModel()->getUserById($user['user_id'], false)
                    ];
                } elseif($transaction['status'] == 0) {
                    return [
                        'error' => 0,
                        'message' => 'Thẻ đang chờ xử lý',
                        'data' => []
                    ];
                } else {
                    return [
                        'error' => 1,
                        'message' => 'Nạp coin thất bại, vui lòng kiểm tra lại thông tin thẻ và thử lại'
                    ];
                }
            } else {
                return [
                    'error' => 1,
                    'message' => 'Thẻ đã được sử dụng để nạp coin'
                ];
            }
        } else {
            return [
                'error' => 1,
                'message' => 'Nạp coin thất bại, vui lòng kiểm tra lại thông tin thẻ và thử lại'
            ];
        }
    }

    public function updateTransactionStatus($code, $status){
        $transaction = $this->getTransactonByCode($code);
        if($transaction && $transaction['status'] != 1) { // tiến hành update trạng thái transaction
            $this->updateTransaction($transaction['id'], array('status'=>$status));
        }
        return true;
    }

    /**
     * @return API_Model_User
     */
    protected function _getUserModel()
    {
        return $this->getModelFromCache('API_Model_User');
    }

    /**
     * @return API_Model_CoinLogs
     */
    protected function _getCoinLogsModel()
    {
        return $this->getModelFromCache('API_Model_CoinLogs');
    }

    /**
     * @return API_DataWriter_Transactions
     */
    protected function _getTransactionsDataWriter()
    {
        return Mava_DataWriter::create('API_DataWriter_Transactions');
    }
}