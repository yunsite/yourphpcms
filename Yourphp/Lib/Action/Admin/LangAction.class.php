<?php
/**
 * 
 * Urlrule(URL规则)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
if(defined('APP_NAME')!='Yourphp' && !defined("YOURPHP"))  exit("Access Denied");
class LangAction extends AdminbaseAction {

	protected  $langpath,$lang;
    function _initialize()
    {	
		parent::_initialize();
		$this->langpath = LANG_PATH.LANG_NAME.'/';
    }

	function _before_insert(){
		$lang_path =LANG_PATH.$_POST['mark'].'/';
		$r =dir_copy(LANG_PATH.'cn/',$lang_path);
	}

	function param()
	{
		$files = glob($this->langpath.'*');
		$lang_files=array();
		foreach($files as $key => $file) {
			//$filename = basename($file);
			$filename = pathinfo($file);
	 		$lang_files[$key]['filename'] = $filename['filename'];
			$lang_files[$key]['filepath'] = $file;
			$temp = explode('_',$lang_files[$key]['filename']);
			$lang_files[$key]['name'] = count($temp)>1 ? $temp[0].L('LANG_module') : L('LANG_common') ;
		}
		$this->assign ( 'id', $id );
		$this->assign ( 'lang', LANG_NAME );
		$this->assign ( 'files', $lang_files );
		$this->display();
		
	}
	function editparam()
	{
		$file=  $_REQUEST['file'];
		$value = F($file, $value='', $this->langpath); 
		$this->assign ( 'id', $id );
		$this->assign ( 'file', $file );
		$this->assign ( 'lang', LANG_NAME );
		$this->assign ( 'list', $value );
		$this->display();
	}

	function updateparam()
	{
		$file=  $_REQUEST['file'];
		unset($_POST[C('TOKEN_NAME')]);

		foreach($_POST as $key=>$r){
			if($r)$data[strtoupper($key)]=$r;
		}
		$r = F($file,$data, $this->langpath); 
		if($r){
			$this->success(L('do_ok'));
		}else{
			$this->error(L('add_error'));
		 }
	}
}
?>