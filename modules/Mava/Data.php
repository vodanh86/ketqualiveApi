<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 3/7/16
 * @Time: 9:49 AM
 */
class Mava_Data {
    const ENGINE_MONGODB = 'mongodb';

    const TABLE_USERS = 'users';
    const TABLE_STATS = 'stats';
    const TABLE_CLICK = 'click';
    const TABLE_ORDER = 'order';

    static $_engine = self::ENGINE_MONGODB;
    static $_database = 'landingpage';
    static $_connection = false;
    static $_db = false;

    public static function _getDB(){
        switch(self::$_engine){
            case self::ENGINE_MONGODB:
            default:
                if(self::$_connection === false){
                    self::$_connection = new MongoClient();
                }
                if(self::$_db === false){
                    self::$_db = self::$_connection->selectDB(self::$_database);
                }
                return self::$_db;
                break;
        }
    }

    /**
     * Thêm một bản ghi vào bảng được chỉ định, nếu engine là MongoDB thì có thể truyền column động
     * @param $tableName
     * @param array $data
     * @return bool|string
     */
    public static function add($tableName, $data = array()){
        switch(self::$_engine){
            case self::ENGINE_MONGODB:
            default:
                if($tableName != "" && count($data) > 0){
                    $tbl = self::_getDB()->selectCollection($tableName);
                    $tbl->insert($data);
                    if(isset($data['_id']) && $data['_id'] != ""){
                        return (string)$data['_id'];
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
                break;
        }
    }

    /**
     * Thêm nhiều bản ghi vào bảng được chỉ định, nếu engine là MongoDB thì có thể truyền column động
     * @param $tableName
     * @param array $data
     * @return bool|string
     */
    public static function adds($tableName, $data = array()){
        switch(self::$_engine){
            case self::ENGINE_MONGODB:
            default:
                if($tableName != "" && count($data) > 0){
                    $tbl = self::_getDB()->selectCollection($tableName);
                    $tbl->batchInsert($data);
                    if(is_array($data) && count($data) > 0){
                        return $data;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
                break;
        }
    }

    /**
     * Cập nhật bản ghi có _id được chỉ định (chuỗi 24 kí tự)
     * @param $tableName
     * @param string $id
     * @param array $data
     * @return bool
     */
    public static function update($tableName, $id, $data = array()){
        switch(self::$_engine) {
            case self::ENGINE_MONGODB:
            default:
                if ($tableName != "" && $id != "" && strlen($id)== 24 && count($data) > 0) {
                    $tbl = self::_getDB()->selectCollection($tableName);
                    $result = $tbl->update(array('_id' => new MongoId($id)), array('$set' => $data));
                    if($result['updatedExisting'] == true && $result['ok'] == 1){
                        return true;
                    }else{
                        return false;
                    }
                } else {
                    return false;
                }
                break;
        }
    }

    /**
     * Cập nhật nhiều bản ghi theo điều kiện được chỉ định (lưu ý điều kiện _id phải là một MongoId)
     * @param $tableName
     * @param array $conditions
     * @param array $data
     * @return bool
     */
    public static function updates($tableName, $conditions = array(), $data = array(), $auto_set = true, $upset = false){
        switch(self::$_engine) {
            case self::ENGINE_MONGODB:
            default:
                if ($tableName != "" && count($conditions) > 0 && count($data) > 0) {
                    $tbl = self::_getDB()->selectCollection($tableName);
                    if($auto_set){
                        $update_data = array('$set' => $data);
                    }else{
                        $update_data = $data;
                    }
                    $options = array("multiple" => true);
                    if($upset == true){
                        $options['upset'] = true;
                    }
                    $result = $tbl->update($conditions, $update_data,$options);
                    if($result['updatedExisting'] == true && $result['ok'] == 1){
                        return true;
                    }else{
                        return false;
                    }
                } else {
                    return false;
                }
                break;
        }
    }

    public static function pull($tableName, $conditions = array(), $data = array()){
        switch(self::$_engine) {
            case self::ENGINE_MONGODB:
            default:
                if ($tableName != "" && is_array($conditions) && count($data) > 0) {
                    $tbl = self::_getDB()->selectCollection($tableName);
                    $result = $tbl->update($conditions, array('$pull' => $data),array("multiple" => true));
                    if($result['updatedExisting'] == true && $result['ok'] == 1){
                        return true;
                    }else{
                        return false;
                    }
                } else {
                    return false;
                }
                break;
        }
    }

    /**
     * @param $tableName
     * @param array $conditions
     * @param array $data
     * @return bool
     */
    public static function increment($tableName, $conditions = array(), $data = array()){
        switch(self::$_engine) {
            case self::ENGINE_MONGODB:
            default:
                if ($tableName != "" && count($conditions) > 0 && count($data) > 0) {
                    $tbl = self::_getDB()->selectCollection($tableName);
                    $result = $tbl->update($conditions, array('$inc' => $data),array("multiple" => true));
                    if($result['updatedExisting'] == true && $result['ok'] == 1){
                        return true;
                    }else{
                        return false;
                    }
                } else {
                    return false;
                }
                break;
        }
    }

    /**
     * @param $tableName
     * @param array $conditions
     * @param array $data
     * @return bool
     */
    public static function addToSet($tableName, $conditions = array(), $data = array()){
        switch(self::$_engine) {
            case self::ENGINE_MONGODB:
            default:
                if ($tableName != "" && count($conditions) > 0 && count($data) > 0) {
                    $tbl = self::_getDB()->selectCollection($tableName);
                    $result = $tbl->update($conditions, array('$addToSet' => $data),array("multiple" => true));
                    if($result['updatedExisting'] == true && $result['ok'] == 1){
                        return true;
                    }else{
                        return false;
                    }
                } else {
                    return false;
                }
                break;
        }
    }

    /**
     * Xóa bản ghi có _id được chỉ định (chuỗi 24 kí tự)
     * @param $tableName
     * @param string $id
     * @return bool
     */
    public static function delete($tableName, $id){
        switch(self::$_engine) {
            case self::ENGINE_MONGODB:
            default:
                if ($tableName != "" && $id != "" && strlen($id)== 24) {
                    $tbl = self::_getDB()->selectCollection($tableName);
                    $result = $tbl->remove(array('_id' => new MongoId($id)));
                    if($result['ok'] == 1){
                        return true;
                    }else{
                        return false;
                    }
                } else {
                    return false;
                }
                break;
        }
    }

    /**
     * Xóa nhiều bản ghi theo điều kiện chỉ định (lưu ý điều kiện _id phải là một MongoId)
     * @param $tableName
     * @param $conditions
     * @return bool
     */
    public static function deletes($tableName, $conditions){
        switch(self::$_engine) {
            case self::ENGINE_MONGODB:
            default:
                if ($tableName != "" && is_array($conditions)) {
                    $tbl = self::_getDB()->selectCollection($tableName);
                    $result = $tbl->remove($conditions);
                    if($result['ok'] == 1){
                        return true;
                    }else{
                        return false;
                    }
                } else {
                    return false;
                }
                break;
        }
    }

    /**
     * Lấy ra một bản ghi theo _id được chỉ định (chuỗi 24 kí tự)
     * @param $tableName
     * @param string $id
     * @return array|bool|null
     */
    public static function get($tableName, $id){
        switch(self::$_engine){
            case self::ENGINE_MONGODB:
            default:
                if($tableName != "" && $id != "" && strlen($id) == 24){
                    $tbl = self::_getDB()->selectCollection($tableName);
                    $result = $tbl->findOne(array('_id' => new MongoId($id)));
                    return $result;
                }else{
                    return false;
                }
                break;
        }
    }

    /**
     * @param $tableName
     * @param array $conditions
     * @param int $skip
     * @param int $limit
     * @return array|bool|null
     */
    public static function gets($tableName, $conditions = array(), $skip = 0, $limit = 10,$sort=array()){
        switch(self::$_engine){
            case self::ENGINE_MONGODB:
            default:
                if($tableName != ""){
                    $tbl = self::_getDB()->selectCollection($tableName);
                    $result = $tbl->find($conditions);
                    if($limit > 0){
                        $result->skip($skip)->limit($limit);
                    }
                    if(count($sort) > 0){
                        $result->sort($sort);
                    }
                    $data = array();
                    foreach($result as $item){
                        $data[] = $item;
                    }
                    return $data;
                }else{
                    return array();
                }
                break;
        }
    }

    public static function getOne($tableName, $conditions = array()){
        switch(self::$_engine){
            case self::ENGINE_MONGODB:
            default:
                if($tableName != ""){
                    $tbl = self::_getDB()->selectCollection($tableName);
                    $result = $tbl->find($conditions)->skip(0)->limit(1);
                    if($result && count($result) > 0){
                        return $result->getNext();
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
                break;
        }
    }

    public static function drop($tableName){
        switch(self::$_engine){
            case self::ENGINE_MONGODB:
            default:
                if($tableName != ""){
                    $tbl = self::_getDB()->selectCollection($tableName);
                    $result = $tbl->drop();
                    if($result['ok'] == 1){
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
                break;
        }
    }

    public static function deleteField($tableName, $fields = array()){
        switch(self::$_engine){
            case self::ENGINE_MONGODB:
            default:
                if($tableName != ""){
                    $tbl = self::_getDB()->selectCollection($tableName);
                    /*$field_unset = array();
                    if(is_array($fields) && count($fields) > 0){
                        foreach($fields as $f){
                            $field_unset[] = array($f => true);
                        }
                    }*/
                    if(count($fields) > 0){
                        $result = $tbl->update(array(),array('$unset' => $fields),array('multiple' => true));
                        if($result['ok'] == 1){
                            return true;
                        }else{
                            return false;
                        }
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
                break;
        }
    }

    public static function emptyField($tableName, $fields = array()){
        switch(self::$_engine){
            case self::ENGINE_MONGODB:
            default:
                if($tableName != ""){
                    $tbl = self::_getDB()->selectCollection($tableName);
                    $field_unset = array();
                    if(is_array($fields) && count($fields) > 0){
                        foreach($fields as $f){
                            $field_unset[] = array($f => null);
                        }
                    }
                    if(count($field_unset) > 0){
                        $result = $tbl->update(array(),array('$set' => $field_unset),array('multiple' => true));
                        if($result['ok'] == 1){
                            return true;
                        }else{
                            return false;
                        }
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
                break;
        }
    }

    public static function count($tableName, $conditions = array()){
        switch(self::$_engine){
            case self::ENGINE_MONGODB:
            default:
                if($tableName != ""){
                    $tbl = self::_getDB()->selectCollection($tableName);
                    $result = $tbl->find($conditions)->count();
                    return $result;
                }else{
                    return 0;
                }
                break;
        }
    }

    public static function max($tableName, $field, $conditions = array()){
        switch(self::$_engine){
            case self::ENGINE_MONGODB:
            default:
                if($tableName != ""){
                    $tbl = self::_getDB()->selectCollection($tableName);
                    $result = $tbl->find($conditions)->sort(array($field => -1))->limit(1);
                    if($result->hasNext()){
                        $count = $result->getNext();
                        if(isset($count[$field])){
                            return $count[$field];
                        }
                    }
                    return 0;
                }else{
                    return 0;
                }
                break;
        }
    }

    public static function sum($tableName, $field, $conditions = array()){
        switch(self::$_engine){
            case self::ENGINE_MONGODB:
            default:
                if($tableName != ""){
                    $tbl = self::_getDB()->selectCollection($tableName);
                    if(count($conditions) > 0){
                        $match = array('$match' => $conditions);
                    }else{
                        $match = array();
                    }
                    $result = $tbl->aggregate(
                        $match,
                        array(
                            '$group' => array(
                                '_id' => null,
                                'total' => array(
                                    '$sum' => '$'. $field
                                )
                            )
                        )
                    );
                    if(
                        is_array($result)
                        && isset($result['ok'])
                        && $result['ok']==1
                        && isset($result['result'])
                        && is_array($result['result'])
                        && count($result['result']) > 0
                        && isset($result['result'][0]['total'])
                    ){
                        return (int)$result['result'][0]['total'];
                    }
                    return 0;
                }else{
                    return 0;
                }
                break;
        }
    }
}