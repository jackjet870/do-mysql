<?php
/**
 * func 数据库操作类
 * @author lizifa
 * @time 2016-08-03
 */

class DB {

    private $link_id;    
    private $handle;   
    
    // 数据库默认配置
    private $config = array(
    	'db_host' => '127.0.0.1', //主机名
	    'db_user' => 'root',	//数据库用户
	    'db_psw' => '',	//数据库密码
	    'db_name' => '',  //数据库名称
	    'port' => '3306',  //数据库端口号
	    'charset' => 'utf8',	//数据库字符集
	    'pconnect' => 0           //默认是数据库正常连接
    );


    // 实例化时 自动连接数据库 参数就是一个配置的数组
    public function __construct($config=array()) {
        header("Content-type: text/html; charset=utf-8"); 
        $this -> config = array_merge($this -> config,$config);
        $this -> connect($this -> config["db_host"].':'.$this -> config["port"], $this -> config["db_user"], $this -> config["db_psw"], $this -> config["db_name"], $this -> config["pconnect"]);
    }
    

    // 数据库连接数据库
    public function connect($db_host, $db_user, $db_psw, $db_name, $pconnect = 0, $charset ='utf8') {
        if($this -> config['pconnect'] == 0){
            $this -> link_id = mysql_connect($db_host, $db_user, $db_psw, true);
            if(!$this -> link_id){
                $this -> halt("数据库连接失败");    
            }
        }else{
            $this -> link_id = mysql_pconnect($db_host, $db_user, $db_psw);
            if(!$this -> link_id){
                $this -> halt("数据库长连接失败");
            }
        }
        if(!mysql_select_db($db_name, $this -> link_id)){
            $this -> halt("数据库选择失败");
        }
        mysql_query("set names " . $charset);
    }
    
    /**
     * 查询
     * @param $sql
     */
    public function query($sql){

        $query = mysql_query($sql, $this -> link_id);
        if(!$query){
            $this -> halt("查询失败 " . $sql);
        }
        return $query;
    }
    
    /**
     * 获取一条记录（MYSQL_ASSOC，MYSQL_NUM，MYSQL_BOTH）
     * @param $sql
     */
    public function getOne($sql, $result_type = MYSQL_ASSOC){

        $query = $this -> query($sql);
        $rt = mysql_fetch_array($query, $result_type);
        return $rt;
    }
    
    /**
     * 获取全部记录
     * @param  $sql
     * @param  $result_type 
     */
    public function getAll($sql, $result_type = MYSQL_ASSOC){

            $query = $this -> query($sql);
            $i = 0;
            $rt = array();
            while ($row = mysql_fetch_array($query, $result_type)) {
                $rt[$i++] = $row;
            }
        
        return $rt;
    }
    
    /**
     * 插入
     * @param $table
     * @param $dataArray
     */
    public function insert($table,$dataArray) {
        $field = "";
        $value = "";
        if( !is_array($dataArray) || count($dataArray) <= 0) {
            $this -> halt('没有要插入的数据');
            return false;
        }
        foreach ($dataArray as $key => $val){
            $field .="$key,";
            $value .="'$val',";
        }
        
        $field = substr( $field,0,-1);
        $value = substr( $value,0,-1);
        $sql = "insert into $table($field) values($value)";
        if(!$this -> query($sql)) return false;
        return true;
    }

    /**
     * 更新
     * @param $table
     * @param $dataArray
     * @param $condition
     */
    public function update( $table,$dataArray,$condition="") {
        if( !is_array($dataArray) || count($dataArray)<=0) {
            $this -> halt('没有要更新的数据');
            return false;
        }
        $value = "";
        while( list($key,$val) = each($dataArray))
        $value .= "$key = '$val',";
        $value .= substr( $value,0,-1);
        $sql = "update $table set $value where 1=1 and $condition";
        if(!$this -> query($sql)) return false;
        return true;
    }

    /**
     * 删除
     * @param $table
     * @param $condition
     */
    public function delete( $table,$condition="") {
        if( empty($condition) ) {
            $this -> halt('没有设置删除的条件');
            return false;
        }
        $sql = "delete from $table where 1=1 and $condition";
        if(!$this -> query($sql)) return false;
        return true;
    }

    /**
     * 返回结果集
     * @param $query
     * @param $result_type
     */
    public function fetch_array($query, $result_type = MYSQL_ASSOC){
        return mysql_fetch_array($query, $result_type);
    }

    /**
     * 获取记录条数
     * @param $results
     */
    public function num_rows($results) {
        if(!is_bool($results)) {
            $num = mysql_num_rows($results);
            return $num;
        } else {
            return 0;
        }
    }
    
    /**
     * 获取最后插入的id
     */
    public function insert_id() {
        $id = mysql_insert_id($this -> link_id);
        return $id;
    }

    /**
     * 关闭数据库连接
     */
    public function close() {
        return @mysql_close($this -> link_id);
    }
    
    /**
     * 错误提示
     */
    private function halt($msg = ''){
        $msg .= "\r\n" . mysql_error();
        die($msg);
    }

}

$config = array(
	'db_host' => '127.0.0.1', //主机名
    'db_user' => 'root',	//数据库用户
    'db_psw' => '',	//数据库密码
    'db_name' => 'qingwei',  //数据库名称
    'port' => '3306',  //数据库端口号
    'charset' => 'utf8',	//数据库字符集
    'pconnect' => 0           //默认是数据库正常连接
	);

$ab = new Db($config);

$b = $ab -> getAll('select * from qw_ad');
var_dump($b);