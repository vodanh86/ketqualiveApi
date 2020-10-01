<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_Video extends Mava_Model
{
	public function getList($data) {
		$user = $this->_getUserModel()->_getByToken($data['token']);
        if(!$user) {
            return false;
        }
        $result = $this->_getDb()->query("SELECT v.*,u.custom_title as 'author_name',u.avatar as 'author_avatar' FROM #__video v, #__user u WHERE v.created_by=u.user_id AND v.`id`<". (int)$data['max_id'] ." ORDER BY v.id DESC LIMIT 0,". (int)$data['limit']);
        if($result->num_rows > 0){
            $result_formatted = [];
            foreach($result->rows as $item){
                $item['time'] = floor($item['second']/60) .":". sprintf('%02d', $item['second']%60);
                if($item['author_avatar'] != ""){
                    $item['author_avatar'] = image_url($item['author_avatar']);
                }else{
                    $item['author_avatar'] = "";
                }
                $youtube_id = Mava_String::getYoutubeId($item['youtube_id']);
                $item['screenshot'] = 'https://img.youtube.com/vi/'. $youtube_id .'/0.jpg';
                $result_formatted[] = $item;
            }
            return $result_formatted;
        }else{
            return [];
        }
    }

    public function getPageList($skip = 0, $limit = 10){
        $where = '';
        $subscribes = $this->_getDb()->fetchAll("SELECT * FROM #__video ". $where ." ORDER BY `id` DESC LIMIT ". $skip .','. $limit);
        $count = $this->_getDb()->fetchRow("SELECT COUNT(*) AS 'total' FROM #__video ". $where);
        return array(
            'items' => $subscribes,
            'total' => $count['total']
        );
    }


    public function getById($id){
        return $this->_getDb()->fetchRow("SELECT * FROM #__video WHERE `id`=". (int)$id);
    }

    public function deleteById($id){
        return $this->_getDb()->delete('#__video','id='. "'".$id."'");
    }

    public function insert($data){
        return $this->_getDb()->query('
        INSERT INTO #__video(`title`,`second`,`token`,`youtube_id`,`view`, `created_by`) VALUES("'. implode('","', $data) .'") 
    ');
    }

    /**
     * @return API_Model_User
     */
    protected function _getUserModel()
    {
        return $this->getModelFromCache('API_Model_User');
    }
}