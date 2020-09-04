<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_RequestCoinLogs extends Mava_Model
{
    /**
     * @param int $id
     * @return bool | array
     */
    public function getById($id)
    {
        return $this->_getDb()->fetchRow("SELECT * FROM #__request_coin_logs WHERE `id`=" . (int)$id);
    }

    public function saveRequestCoinLogs($token, $clientRq, $apiRq, $apiRp, $e){
        $rqCoinLogDW = $this->_getRequestCoinLogsDataWriter();
        $rqCoinLogDW->bulkSet(array(
            'token' => $token,
            'client_request' => json_encode($clientRq),
            'api_request' => json_encode($apiRq),
            'api_response' => json_encode($apiRp),
            'error' => $e
        ));
        $rqCoinLogDW->save();
    }

    public function getLogByToken($token){
        $result = $this->_getDb()->fetchAll("SELECT * FROM #__request_coin_logs WHERE `token`='". $token ."'");
        $logs = [];
        if($result) {
            foreach ($result as $item) {
                $logs[] = [
                    'id' => $item['id'],
                    'token' => $item['token'],
                    'client_request' => json_decode($item['client_request']),
                    'api_request' => json_decode($item['api_request']),
                    'api_response' => json_decode($item['api_response']),
                    'error' => $item['error'],
                    'created_at' => $item['created_at'],
                ];
            }
        }
        return $logs;
    }

    /**
     * @return API_DataWriter_RequestCoinLogs
     */
    protected function _getRequestCoinLogsDataWriter()
    {
        return Mava_DataWriter::create('API_DataWriter_RequestCoinLogs');
    }
}