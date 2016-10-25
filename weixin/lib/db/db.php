<?php
class DB {
	
	private $pdo;
	
	function __construct(){
	}

	function connect() {
		require_once(dirname(__FILE__)."/config.php");
		$dsn = "mysql:host=".DATABASE_HOST."; port=".DATABASE_PORT."; dbname=".DATABASE_NAME;
		
		try 
		{
			$this->pdo = new PDO($dsn, DATABASE_USER, DATABASE_PASSWORD);
		}
		catch (PDOException $exception)
		{
			$this->pdo=null;
			var_dump($exception);
		}
	}
	
	function disconnect()  
	{
	    $this->pdo = null;  
	}  

	function query($sql) {
	    $this->pdo->query('set character set utf8');
		$rs = $this->pdo->query($sql);
		$result=array();
		while($row = $rs->fetch(PDO::FETCH_ASSOC)) {
			$r = array();
			foreach($row as $k=>$v) {
				$r[$k] = $v;
			}
			$result[] = $r;
		}
		return $result;
	}
    function execute($sql) {
        $this->pdo->prepare($sql)->execute();
        $ret = $this->pdo->lastInsertId();
        return $ret;
    }
	function insert($table, $data) {
	    
		$keys = array_keys($data);
		$fields = '`'.implode('`, `',$keys).'`';
		$placeholder = substr(str_repeat('?,',count($keys)),0,-1);
		$this->pdo->query('set names utf8;');
		$this->pdo->prepare("INSERT INTO `".TABLEPRE."$table`($fields) VALUES($placeholder)")->execute(array_values($data));
		$ret = $this->pdo->lastInsertId();
		return $ret;
	}
	
	function update($table, $data, $where) {
		$setvalues = array();
		foreach($data as $k=>$v) {
			$setvalues[] = " `". addslashes($k)."`='".addslashes($v)."' ";
		}
		$setvaluestr = implode(",", $setvalues);
		$ret = $this->pdo->exec("UPDATE `".TABLEPRE."$table` set $setvaluestr where $where");		
		$this->addToLog2("UPDATE `".TABLEPRE."$table` set $setvaluestr where $where");
		return $ret;
	}
	function addToLog2($content,$uid=0) {
	    date_default_timezone_set("Asia/Shanghai");
	    $data=array("content"=>$content,
	        "access"=> date('Y-m-d H:i:s'),
	        "uid"=>$uid,
	        "uri"=>$_SERVER["REQUEST_URI"],
	        "remoteip"=>$_SERVER["REMOTE_ADDR"]
	    );
	    $id=$this->insert("log",$data);
	    return $id;
	}
	function replace($table, $data, $where) {
	    $setvalues = array();

	    foreach($data as $k=>$v) {
	        $keys[]=addslashes($k);
	        $values[]="'".addslashes($v)."'";
	    }
	    
	    $keystr=implode(",",$keys);
	    $valuestr=implode(",",$values);
	    
	    $ret = $this->pdo->exec("INSERT INTO `".TABLEPRE."$table` ($keystr) select $valuestr from DUAL WHERE NOT EXISTS (select * from `".TABLEPRE."$table` where $where)");
	    
	    if($ret==0){
	        return $this->update($table, $data, $where);
	    }
	    return $ret;
	}
	
	function delete($table, $where) {
	    $ret = $this->pdo->exec("delete from `".TABLEPRE."$table` where $where");
	    return $ret;
	}
		
	function get($table, $fields="*", $cond, $order="", $limit="") {
		$sql = "SELECT $fields FROM ".TABLEPRE."$table  ";
			
		if(count($cond) > 0) {
			$sql .= " WHERE 1=1 ";
			foreach($cond as $k => $v) {
				$sql .= " AND ".addslashes($k)." = '".addslashes($v)."'";
			}
		}
		if($order && $order !="") {
		    $sql .= " ". $order. " ";
		}

		if(count($cond) <=0) {
			$sql .= " LIMIT 0,10";
		} else {
		    if($limit !="") {
		        $sql .=  " ". $limit." ";
		    }
		}
		return $this->query($sql);
	}
	/**
	 * 返回数组的维度
	 * @param  [type] $arr [description]
	 * @return [type]      [description]
	 */
	function arrayLevel($arr){
	    $al = array(0);
	    function aL($arr,&$al,$level=0){
	        if(is_array($arr)){
	            $level++;
	            $al[] = $level;
	            foreach($arr as $v){
	                aL($v,$al,$level);
	            }
	        }
	    }
	    aL($arr,$al);
	    return max($al);
	}
	
}