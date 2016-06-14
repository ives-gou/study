<?php 
/**
 * 数据库操作类
 * auth : gpj
 * date : 2016-04-29
 */
// $config 应放在配置文件中
$config = array(
        'host'          =>   'localhost',     //服务器地址
        'user'          =>   'root',          //用户名
        'pwd'           =>   'root',          //密码
        'db_name'       =>   'test',          //数据库名
        'db_char'       =>   'utf8'           //字符集
        );
class Mysql{
    private $conn;
    static private $_instance;
    
    //禁止直接实例化对象
    private function __construct($config){
        $this->config = $config;
        $this->conn = mysql_connect($this->config['host'], $this->config['user'], $this->config['pwd']);
        if(!$this->conn){
            throw new Exception("数据库连接失败");
        }
        mysql_select_db($config['db_name'], $this->conn);
        mysql_set_charset($config['db_char'], $this->conn);
        
    }
    //禁止克隆
    private function __clone(){}
    //单例模式获取对象实例
    static public function getInstance($config){
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self($config);
        }
        return self::$_instance;
    }
    //数据库执行语句，可执行查询添加修改删除等任何sql语句
    public function query($sql){
        if ($sql == '') {
            throw new Exception('SQL 语句错误');
        } else {
            $result = mysql_query($sql, $this->conn);
            if (!$result) {
                throw new Exception(mysql_errno().': '.mysql_error());
            }
            return $result;
        }
    }
    //获取一个数据 如count()
    public function getOne($sql){
        $result = $this->query($sql);
        $row = mysql_fetch_row($result);
        return $row[0];   
    }
    //获取一行数据 一维数组
    public function getRow($sql){  
        $result = $this->query($sql);
        return mysql_fetch_assoc($result);  
    }
    //获取所有数据 多维数组
    public function getAll($sql){
        $result = $this->query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $all[] = $row; 
        }
        return $all;
    }
    /**
     * [add 添加数据]
     * @param [string] $table [数据表]
     * @param [array] $data   [数据]
     */
    public function add($table, $data){
        if (!is_array($data)) throw new Exception('参数类型错误');
        $value_str = implode('","' , $data);
        $sql = 'INSERT INTO '.$table;
            
        //判断数组类型(索引 OR 关联)
        if (($field = array_keys($data)) == array_keys(array_keys($data))) {
            $sql .= ' VALUES ("'.$value_str.'")';
        } else {
            $field_str = implode(',' , $field);
            $sql .= ' ('.$field_str.') VALUES ("'.$value_str.'")';
        }
            
        $result =  $this->query($sql);
        return $result;   
    }
    /**
     * [edit 修改数据]
     * @param  [string]   $table [数据表]
     * @param  [array]    $data  [数据]
     * @param  [string]   $where [条件]
     * @return [bool]          
     */
    public function edit($table, $data, $where = null){
       
        if (!is_array($data)) throw new Exception('参数类型错误');
        $sql = 'UPDATE '.$table.' SET ';
        foreach ($data as $k => $v) {
            $sql .= $k.'="'.$v.'",';
        }
        $sql = rtrim($sql,',');
        if ($where) {
            $sql .= ' WHERE '.$where;
        }
        $result =  $this->query($sql);
        return $result;  
    }
    //获得最新添加数据的Id
    public function getInsertId(){
        return mysql_insert_id();
    }
    //获得受影响的行数
    public function getAffectedRows(){
        return mysql_affected_rows();
    }
}



