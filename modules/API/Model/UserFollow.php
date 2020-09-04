<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_UserFollow extends Mava_Model
{
	public function checkFollow($data) {
        $result = $this->_getDb()->fetchRow("SELECT * FROM #__user_follow WHERE `token`='". $data['token'] ."' AND user_id='". (int)$data['user_id'] ."'");
        return $result;
    }

    public function addFollow($data){
        $follow = $this->checkFollow($data);
        if($follow){
            return [
                'error' => 0,
                'message' => 'Đã theo dõi người này'
            ];
        }
        $userFlDW = $this->_getUserFollowDataWriter();
        $userFlDW->bulkSet([
            'token' => $data['token'],
            'user_id' => $data['user_id'],
            'created_at' => time()
        ]);
        if($userFlDW->save()){
            return [
                'error' => 0,
                'message' => 'Đã theo dõi người này'
            ];
        }else {
            Mava_Log::error($userFlDW->getErrors());
            return [
                'error' => 1,
                'message' => 'Không thể lưu thông tin'
            ];
        }
    }

    public function unFollow($data){
        $follow = $this->checkFollow($data);
        if(!$follow){
            return [
                'error' => 1,
                'message' => 'Chưa theo dõi người này'
            ];
        }
        $del = $this->_getDb()->delete('#__user_follow',"token='". $data['token'] ."' AND user_id='". (int)$data['user_id'] ."'");
        if($del > 0){
            return [
                'error' => 0,
                'message' => 'Đã hủy theo dõi người này'
            ];
        }else{
            return [
                'error' => 0,
                'message' => 'Không hủy được theo dõi'
            ];
        }
    }

    public function getFollowing($data) {
        $result = $this->_getDb()->query("
            SELECT u_fl.id as id, u.user_id as user_id, u.custom_title as custom_title, FROM_UNIXTIME(u.birthday, '%d-%m-%Y') as birthday, u.email as email, u.avatar as avatar, u.cover as cover
            FROM #__user_follow u_fl
            JOIN #__user u ON u_fl.`user_id` = u.`user_id`
            WHERE u_fl.token='". $data['token'] ."'
            AND u_fl.id>". $data['min_id'] ."
            ORDER BY u_fl.id ASC
            LIMIT 0,". $data['limit'] ."");
         return $result->rows;
    }

    public function getFollower($data) {
        $result = $this->_getDb()->query("
            SELECT u_fl.id as id, u.user_id as user_id, u.custom_title as custom_title, FROM_UNIXTIME(u.birthday, '%d-%m-%Y') as birthday, u.email as email, u.avatar as avatar, u.cover as cover
            FROM #__user_follow u_fl
            JOIN #__user u ON u_fl.`token` = u.`token`
            WHERE u_fl.user_id='". $data['user_id'] ."'
            AND u_fl.id > ". $data['min_id'] ."
            ORDER BY u_fl.id ASC
            LIMIT 0,". $data['limit'] ."");
         return $result->rows;
    }

    /**
     * @return API_Model_User
     */
    protected function _getUserModel()
    {
        return $this->getModelFromCache('API_Model_User');
    }

    /**
     * @return API_DataWriter_UserFollow
     */
    protected function _getUserFollowDataWriter()
    {
        return Mava_DataWriter::create('API_DataWriter_UserFollow');
    }
}