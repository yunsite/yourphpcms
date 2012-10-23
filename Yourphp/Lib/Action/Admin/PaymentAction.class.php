<?php
/**
 * 
 * Posid (推荐位管理)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class PaymentAction extends AdminbaseAction {

	protected $dao,$path;
    function _initialize()
    {	
		parent::_initialize();
		$this->path = './Yourphp/Lib/Pay/';
		$this->dao= M('Payment');
    }

	function index()
    {
		$tempfiles = dir_list($this->path,'php');

		$list = $this->dao->Field('id,pay_code,status,listorder,pay_name')->select();
		foreach((array)$list as $key=>$r){
			 $installed[$r['pay_code']] = $r;
		}
		foreach($tempfiles as $r){
			$filename = basename($r);
			$pay_code = str_replace('.class.php','',$filename);
			
			import("@.Pay.".$pay_code);
			$pay=new $pay_code();
			$paylist[$pay_code] = $pay->setup();
			if($installed[$pay_code]){
				$paylist[$pay_code]['id'] = $installed[$pay_code]['id'];
				$paylist[$pay_code]['status'] = $installed[$pay_code]['status'];
				$paylist[$pay_code]['listorder'] = $installed[$pay_code]['listorder'];
				$paylist[$pay_code]['pay_name'] = $installed[$pay_code]['pay_name'];
			}
		}
		 
		$this->assign('list',$paylist);
	    $this->display();
    }
	function add()
    {
		$code = $_REQUEST['code'];
		if(is_file($this->path.$code.'.class.php')){
			import("@.Pay.".$code);
			$pay=new $code();
			$setup = $pay->setup();
			$this->assign('vo',$setup);
		}else{
			$this->error(L('do_empty'));
		}
	 
		$this->display ('edit');
	}
	function edit()
	{
		$id=intval($_REQUEST['id']);
		$data = $this->dao->find($id);
		$data['pay_config'] = unserialize($data['pay_config']);
		$code= $data['pay_code'];
		if(is_file($this->path.$code.'.class.php')){
				import("@.Pay.".$code);
				$pay=new $code();
				$setup = $pay->setup();
		}
		foreach($setup['config'] as $key=>$r){
			$r['value'] = $data['pay_config'][$r['name']];
			$setup['config'][$key] = $r;
		}
		$data = $data+$setup;
		$this->assign('vo',$data);
		$this->display ();
	}
	function _before_insert()
	{
			$_POST['pay_config']=serialize($_POST['pay_config']);
			$_POST['pay_fee'] = $_POST['pay_fee_type'] ? $_POST['pay_fix'] : $_POST['pay_rate'] ;
			 
	}

	function _before_update()
	{
		$_POST['pay_config']=serialize($_POST['pay_config']);
		$_POST['pay_fee'] = $_POST['pay_fee_type'] ? $_POST['pay_fix'] : $_POST['pay_rate'] ;
		
	}
}
?>