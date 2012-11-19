<?php
/**
 * 
 * Database(数据库)
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");

class DatabaseAction extends AdminbaseAction{

    protected $db = '', $datadir = '' , $startrow=0,$startfrom=0, $complete=true;
    function _initialize()
    {
		parent::_initialize();
		$this->datadir = RUNTIME_PATH.'Backup/';
		if(!is_dir($this->datadir))mkdir($this->datadir,0755,true);
		$db=D('');
		$this->db =   DB::getInstance();
    }

    public function index()
    {
        $dataList = $this->db->query("SHOW TABLE STATUS LIKE '".C('DB_PREFIX')."%'");
        foreach ($dataList as $row){
            $total += $row['Data_length'];
        }
        $this->assign('totalSize', $total);
        $this->assign("dataList", $dataList);
        $this->display();
    }

    public function excuteQuery($sql='')
    {
        if(empty($sql)) {$this->error(L('do_empty'));}
        $queryType = 'INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|LOAD DATA|SELECT .* INTO|COPY|ALTER|GRANT|TRUNCATE|REVOKE|LOCK|UNLOCK';
        if (preg_match('/^\s*"?(' . $queryType . ')\s+/i', $sql)) {
            $data['result'] = $this->db->execute($sql);
            $data['type'] = 'execute';
        }else {
				
            $data['result'] = $this->db->query($sql);
            $data['type'] = 'query';
		
        }
        return $data;
    }

    public function query(){
        $this->display();
    }

    public function doquery(){
		$sqls=explode("\n",stripcslashes($_POST['sql']));
		foreach ((array)$sqls as $sql){
			if($sql)$r =$this->excuteQuery($sql); 
		}
		if($r['result']!=''){
			$this->success(L('do_ok'));	
		}else{
			if($r['dberror']) $this->error(L($r['dberror']));				 
		}
    }

	public function recover(){
		if($_GET['do']=='delete'){

			foreach ((array)$_POST['files'] as $r){
				unlink($r);
			}
			$this->success(L('do_ok'));	
		}elseif($_GET['do']=='import'){
			header('Content-Type: text/html; charset=UTF-8');
			$filename = $_GET['filename'];
			$filelist = dir_list($this->datadir);
			foreach ((array)$filelist as $r){
				$file = explode('-',basename($r));
				if($file[0] ==$filename){
					$files[]  = $r;
				}
			}
			foreach((array)$files as $file){
				//读取数据文件
				$sqldata = file_get_contents($file);
				$sqlFormat = sql_split($sqldata, C('DB_PREFIX'));
				foreach ((array)$sqlFormat as $sql){
						$sql = trim($sql);
						if (strstr($sql, 'CREATE TABLE')){
							preg_match('/CREATE TABLE `([^ ]*)`/', $sql, $matches);
							$ret =$this->excuteQuery($sql);
							//if($ret){echo   L('CREATE_TABLE_OK').$matches[0].' <br />';}else{echo 'Error sql:'.$sql;}exit;
						}else{
							$ret = $this->excuteQuery($sql);
						}
				}
				echo L('CREATE_TABLE_OK').basename($file).'<br>';
			}

		}else{
			$filelist = dir_list($this->datadir);
			foreach ((array)$filelist as $r){
				$filename = explode('-',basename($r));
				$files[] = array('path'=> $r,'file'=>basename($r),'name' => $filename[0], 'size' => filesize($r), 'time' => filemtime($r));
			}
			$this->assign('files',$files);
			$this->display();
		}
	}

	public function docommand()
    {
        $tables = $_POST['tables'];
        $do= trim($_GET['do']);
		if(empty($do) || empty($tables)) $this->error(L('do_empty'));
		if($do=='show'){
			 foreach ((array)$tables as $t){
				$this->db->execute("SHOW COLUMNS FROM {$t}");
			}
		}else{
			$tables = implode(',',$tables);
			$r=$this->db->execute($do.' TABLE '.$tables);
			if(false != $r){ $this->success(L('do_ok'));}else{ $this->error($r['dbError']);}
		}
    }

	public function backup(){
		$tableid = intval($_GET['tableid']);
		$this->startfrom = intval($_GET['startfrom']);
		$sizelimit = intval($_REQUEST['sizelimit']);
		$volume = intval($_GET['volume']) + 1;

		$dataList = $this->db->query("SHOW TABLE STATUS LIKE '".C('DB_PREFIX')."%'");
		foreach ($dataList as $row){
				$table_info[$row['Name']]=$row;
		}		
		$tables = S('backuptables');
		if(empty( $_POST['tables']) && empty($tables)) {	
			foreach ($dataList as $row){
				$tables[]= $row['Name'];
			}
		}else{
			$tables = array();
			if(!$tableid) {
				$tables=$_POST['tables'];
				S('backuptables',$tables);
			} else {
				$tables = S('backuptables');
			}
			if( !is_array($tables) || empty($tables)) {
				$this->success(L('do_empty'));	
			}
		}
		unset($dataList);
		$sql='';
		if(!$tableid) {
				$sql .= "-- Yourphp SQL Backup\n-- Time:".toDate(time())."\n-- http://www.yourphp.cn \n\n";
				foreach($tables as $key=>$table) {
					$sql .= "--\n-- Yourphp Table `$table`\n-- \n";
					$sql .= "DROP TABLE IF EXISTS `$table`;\n";
					$info = $this->db->query("SHOW CREATE TABLE  $table");					
					$sql .= str_replace(array('USING BTREE','ROW_FORMAT=DYNAMIC'),'',$info[0]['Create Table']).";\n";
				}
		}

		for(; $this->complete && $tableid < count($tables) && strlen($sql) + 500 < $sizelimit * 1000; $tableid++) {
			if($table_info[$tables[$tableid]]['Rows']>0){
				$sql .=  $this->dumptablesql($tables[$tableid], $this->startfrom, strlen($sql),$table_info[$tables[$tableid]]['Auto_increment']);
				if($this->complete) {
					$this->startfrom = 0;
				}
				
			}
		}
		!$this->complete && $tableid--;
		$filename = htmlspecialchars(strip_tags($_GET['filename']));
		$filename = !$filename ? 'Yp_'.rand_string(10).'_'.date('YmdH') : $filename;
		$filename_valume = sprintf($filename."-%s".'.sql', $volume);
 
		if(trim($sql)){
			$putfile=$this->datadir . $filename_valume;
			$r= file_put_contents($putfile , trim($sql));
		}
	 

		if($tableid < count($tables) || $r){

			$this->assign ( 'waitSecond', 0);
			$urlarray=array(
						'tableid'   => $tableid,
						'startfrom' => $this->startfrom,
						'sizelimit' => $sizelimit,
						'volume'	=> $volume,
						'filename'  => $filename,
					);
			$message = $filename_valume.' File Create Ok!';
			$forward = U("Database/backup",$urlarray);
			$this->assign ( 'jumpUrl', $forward);
			$this->success($message);
		 
		}else{ 
			S('backuptables',null);
			$this->assign ( 'jumpUrl', U(MODULE_NAME.'/recover') );
			$this->success(L('do_ok'));	
		}
		
	}

	public function dumptablesql($table, $startfrom = 0, $currsize = 0,$auto_increment=0) {
		$offset = 300;
		$insertsql = '';
		$sizelimit = intval($_REQUEST['sizelimit']);
		if(C('DB_PREFIX').'online'==$tbale)return '';
		
		$modelname=str_replace(C('DB_PREFIX'),'',$table);
		$model = M($modelname);
		$keyfield=$model->getPk ();
		$rows = $offset;
		while($currsize + strlen($insertsql) + 500 < $sizelimit * 1000 && $rows == $offset) {
				if($auto_increment) {
					$selectsql = "SELECT * FROM $table WHERE $keyfield > $startfrom ORDER BY $keyfield LIMIT $offset";
				} else {
					$selectsql = "SELECT * FROM $table LIMIT $startfrom, $offset";
				}
				$tabledumped = 1;
				$row = $this->db->query($selectsql);
				$rows = count($row);
				foreach($row as $key=>$val) {
					foreach ($val as $k=>$field){
						if(is_string($field)) {
							$val[$k] = '\''. $this->db->escapeString($field).'\'';
						}elseif(empty($field)){
							$val[$k] = 'NULL';
						}
					}
					if($currsize + strlen($insertsql) + 500 < $sizelimit * 1000) {
						if($auto_increment) {
							$startfrom = $row[$key][$keyfield];
						} else {
							$startfrom++;
						}
						$insertsql .= "INSERT INTO `$table` VALUES (".implode(',', $val).");\n";
					} else {
						$this->complete=false;
						break 2;
					}
				}
				
		}
		$this->startfrom= $startfrom;
		return $insertsql;
	}

}

?>