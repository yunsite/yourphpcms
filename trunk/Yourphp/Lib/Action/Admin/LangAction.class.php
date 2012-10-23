<?php
/**
 * 
 * Urlrule(URL规则)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class LangAction extends AdminbaseAction {

	protected  $langpath,$lang;
    function _initialize()
    {	
		parent::_initialize();
		$this->langpath = LANG_PATH.LANG_NAME.'/';
    }


	function insert() {


		$lang_path =LANG_PATH.$_POST['mark'].'/';
		$r =dir_copy(LANG_PATH.'cn/',$lang_path); 

		$name = MODULE_NAME;
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		$id = $model->add();
		if ($id !==false) {
			$db=D('');
			$db =   DB::getInstance();
			$db->execute("INSERT INTO `yourphp_config`  ('varname','info','groupid','value','lang') VALUES ('site_name','网站名称','2','','".$id."'),
			('site_url','网站网址','2','','".$id."'),
			('logo','网站LOGO','2','./Public/Images/logo.gif','".$id."'),
			('site_email','站点邮箱','2','admin@yourphp.cn','".$id."'),
			('seo_title','网站标题','2','','".$id."'),
			('seo_keywords','关键词','2','','".$id."'),
			('seo_description','网站简介','2','','".$id."'),
			('member_register','允许新会员注册','3','1','".$id."'),
			('member_emailcheck','新会员注册需要邮件验证','3','0','".$id."'),
			('member_registecheck','新会员注册需要审核','3','1','".$id."'),
			('member_login_verify','注册登陆开启验证码','3','1','".$id."'),
			('member_emailchecktpl','邮件认证模板','3','','".$id."'),
			('member_getpwdemaitpl','密码找回邮件内容','3','','".$id."')
			;");
			if(in_array($name,$this->cache_model)) savecache($name);			
			$jumpUrl = $_POST['forward'] ? $_POST['forward'] : U(MODULE_NAME.'/index');
			$this->assign ( 'jumpUrl',$jumpUrl );
			$this->success (L('add_ok'));
		} else {
			$this->error (L('add_error').': '.$model->getDbError());
		}
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