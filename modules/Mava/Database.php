<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 3/17/14
 * Time: 4:52 PM
 * To change this template use File | Settings | File Templates.
 */

final class Mava_Database {
    private $connection;
    protected $config = null;
    protected $_initialized = false;
    public $query_num = 0;
    public $query_time = 0;
    public $query_time_total = 0;
    public $querys = array();
    const INT_TYPE    = 0;
    const BIGINT_TYPE = 1;
    const FLOAT_TYPE  = 2;
    protected $_numericDataTypes = array(
        Mava_Database::INT_TYPE    => Mava_Database::INT_TYPE,
        Mava_Database::BIGINT_TYPE => Mava_Database::BIGINT_TYPE,
        Mava_Database::FLOAT_TYPE  => Mava_Database::FLOAT_TYPE
    );
    public function __construct() {
        if($this->_initialized){
            return $this;
        }
        $this->config = new stdClass();

        $this->config->db_host  = Mava_Application::get('config/database/host');
        $this->config->db_user  = Mava_Application::get('config/database/user');
        $this->config->db_pass  = Mava_Application::get('config/database/pass');
        $this->config->db_name  = Mava_Application::get('config/database/name');
        $this->config->prefix   = Mava_Application::get('config/database/prefix');

        if (!$this->connection = mysqli_connect($this->config->db_host, $this->config->db_user, $this->config->db_pass)) {
            throw new Mava_Exception(__FILE__ .' | Error: Could not make a database connection using ' . $this->config->db_user . '@' . $this->config->db_host);
        }

        if (!mysqli_select_db($this->connection, $this->config->db_name)) {
            throw new Mava_Exception(__FILE__ .' | Error: Could not connect to database ' . $this->config->db_name);
        }

        mysqli_query($this->connection, "SET NAMES 'UTF8'");
        $this->_initialized = true;
    }

    public function query($sql){
        $time_start = microtime(true);
        $sql = str_replace("#__",$this->config->prefix,$sql);
        $query_stats = array(
            'str' => $sql,
            'start_time' => $time_start
        );

        $resource = mysqli_query($this->connection, $sql);
        if ($resource) {
            if (is_object($resource)) {
                $i = 0;

                $data = array();

                while ($result = mysqli_fetch_assoc($resource)) {
                    $data[$i] = $result;

                    $i++;
                }

                mysqli_free_result($resource);

                $query = new stdClass();
                $query->row = isset($data[0]) ? $data[0] : array();
                $query->rows = $data;
                $query->num_rows = $i;

                unset($data);
                $this->query_num++;

                $time_end = microtime(true);
                $realTime = ($time_end - $time_start);
                $query_stats['end_time'] = $time_end;
                $query_stats['total_time'] = $realTime;
                $this->querys[] = $query_stats;
                $this->query_time = (float)number_format($realTime,6,'.','');
                $this->query_time_total += $this->query_time;
                return $query;
            } else {
                $this->query_num++;

                $time_end = microtime(true);
                $realTime = ($time_end - $time_start);
                $query_stats['end_time'] = $time_end;
                $query_stats['total_time'] = $realTime;
                $this->querys[] = $query_stats;
                $this->query_time = (float)number_format($realTime,4,'.','');
                $this->query_time_total += $this->query_time;
                return TRUE;
            }
        } else {
            throw new Mava_Exception(__FILE__ .' | Error: ' . mysqli_error($this->connection) . '<br />Error No: ' . mysqli_errno($this->connection) . '<br />' . $sql);
        }
    }

    public function delete($tableName, $where = ''){
        if($where!=""){
            $where = ' WHERE '. $where;
        }
        $this->query("DELETE FROM ". $tableName . $where);
        return $this->countAffected();
    }

    public function update($tableName, $data = array(), $condition = ''){
        if(sizeof($data) > 0 && $tableName!=""){
            $updateFields = array();
            foreach($data as $k => $v){
                $fields[] = $k;
                if($v!=""){
                    $values[] = addslashes($v);
                }else{
                    $values[] = $v;
                }
                $updateFields[] = '`'. $k .'`="'. addslashes($v) .'"';
            }

            $this->query("UPDATE ". $tableName ." SET ". implode(',',$updateFields) ." WHERE ". $condition);
            return $this->countAffected();
        }else{
            return false;
        }
    }

    public function insert($tableName, $data = array(), $hasPrimary = true){
        return $this->add($tableName, $data, $hasPrimary);
    }

    public function lastInsertId(){
        return $this->getLastId();
    }

    public function add($tableName, $data = array(), $hasPrimary = true){
        if(sizeof($data) > 0 && $tableName!=""){
            $fields = array();
            $values = array();
            $fieldString = '';
            $valueString = '';
            foreach($data as $k => $v){
                $fields[] = $k;
                if($v!=""){
                    $values[] = addslashes($v);
                }else{
                    $values[] = $v;
                }
            }
            if(sizeof($data) > 1){
                $fieldString = '`'. implode('`,`',$fields) .'`';
                $valueString = "'". implode("','",$values) ."'";
            }else{
                $fieldString = '`'. $fields[0] .'`';
                $valueString = "'". $values[0] ."'";
            }

            $this->query("INSERT IGNORE INTO ". $tableName ."(". $fieldString .") VALUE(". $valueString .")");
            if($hasPrimary){
                return $this->getLastId();
            }else{
                return true;
            }
        }else{
            return false;
        }
    }

    /**
     * @return bool | array
     */
    public function fetchRow(){
        $args = func_get_args();
        if($args[0]!=""){
            $queryString = $args[0];
            if(sizeof($args) > 1){
                $count = 0;
                foreach($args as $item){
                    $count++;
                    if($item!="" && $count > 1){
                        if(is_string($item)){
                            $item = '"'. addslashes($item) .'"';
                        }
                        $queryString = preg_replace('/\?/',$item,$queryString,1);
                    }
                }
            }
            $queryString .= ' LIMIT 0,1';
            $data = $this->query($queryString);
            if($data && $data->num_rows > 0){
                return $data->row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * @return bool | array
     */
    public function fetchAll(){
        $args = func_get_args();
        if($args[0]!=""){
            $queryString = $args[0];
            if(sizeof($args) > 1){
                $count = 0;
                foreach($args as $item){
                    $count++;
                    if($count > 1){
                        if(is_string($item)){
                            $item = '"'. addslashes($item) .'"';
                        }
                        $queryString = preg_replace('/\?/',$item,$queryString,1);
                    }
                }
            }
            $data = $this->query($queryString);
            if($data && $data->num_rows > 0){
                return $data->rows;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }



    public function getSingleTableRow($table,$field = '*', $condition = array(), $skip = 0, $limit = 1, $order_by = '', $order_dir = 'asc'){
        $where = array();
        if(sizeof($condition) > 0){
            foreach($condition as $con){
                if(sizeof($con) == 3){
                    if(strpos($con[2],')') === FALSE){
                        $con[2] = "'". addslashes($con[2]) ."'";
                    }
                    $where[] = '`'. addslashes($con[0]) .'` '. addslashes($con[1]) . $con[2];
                }
            }
        }
        if(sizeof($where) > 0){
            $where = implode(' AND ',$where);
            $where = " WHERE ". $where;
        }else{
            $where = "";
        }

        $order = '';
        if($order_by!=""){
            if(!in_array(strtolower($order_dir),array('asc','desc'))){
                $order_dir = 'asc';
            }
            $order = " ORDER BY `". addslashes($order_by) ."` ". $order_dir;
        }

        $query = $this->query("SELECT ". $field ." FROM ". $table . $where . $order ." LIMIT ". $skip .",". $limit);
        return $query;
    }

    public function quote($value, $type = null){
        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = $this->quote($val, $type);
            }
            return implode(', ', $value);
        }
        if ($type !== null && array_key_exists($type = strtoupper($type), $this->_numericDataTypes)) {
            $quotedValue = '0';
            switch ($this->_numericDataTypes[$type]) {
                case Mava_Database::INT_TYPE: // 32-bit integer
                    $quotedValue = (string) intval($value);
                    break;
                case Mava_Database::BIGINT_TYPE: // 64-bit integer
                    // ANSI SQL-style hex literals (e.g. x'[\dA-F]+')
                    // are not supported here, because these are string
                    // literals, not numeric literals.
                    if (preg_match('/^(
                          [+-]?                  # optional sign
                          (?:
                            0[Xx][\da-fA-F]+     # ODBC-style hexadecimal
                            |\d+                 # decimal or octal, or MySQL ZEROFILL decimal
                            (?:[eE][+-]?\d+)?    # optional exponent on decimals or octals
                          )
                        )/x',
                        (string) $value, $matches)) {
                        $quotedValue = $matches[1];
                    }
                    break;
                case Mava_Database::FLOAT_TYPE: // float or decimal
                    $quotedValue = sprintf('%F', $value);
            }
            return $quotedValue;
        }

        return $this->_quote($value);
    }

    /**
    * Quote a raw string.
    *
    * @param string $value     Raw string
    * @return string           Quoted string
    */
    protected function _quote($value)
    {
        if (is_int($value)) {
            return $value;
        } elseif (is_float($value)) {
            return sprintf('%F', $value);
        }
        return "'" . addcslashes($value, "\000\n\r\\'\"\032") . "'";
    }

    public function quoteLike($str){
        return str_replace(array('?','%','"',"'"),'',$str);
    }

    public function escape($value) {
        return mysqli_real_escape_string($value, $this->connection);
    }

    public function countAffected() {
        return mysqli_affected_rows($this->connection);
    }

    public function getLastId() {
        return mysqli_insert_id($this->connection);
    }

    public function __destruct() {
        if(is_resource($this->connection)){
            mysqli_close($this->connection);
        }
    }
}
?>