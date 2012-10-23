<?php
/**
 * 
 * Role(会员组管理)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class RoleAction extends AdminbaseAction {

	protected $dao;
    function _initialize()
    {	
		parent::_initialize();		
	
    }

	public function _before_insert()
    {
		$_POST['allowpost'] = $_POST['allowpost'] ? 1 : 0 ;
		$_POST['allowpostverify'] = $_POST['allowpostverify'] ? 1 : 0 ;
		$_POST['allowupgrade'] = $_POST['allowupgrade'] ? 1 : 0 ;
		$_POST['allowsendmessage'] = $_POST['allowsendmessage'] ? 1 : 0 ;
		$_POST['allowattachment'] = $_POST['allowattachment'] ? 1 : 0 ;
		$_POST['allowsearch'] = $_POST['allowsearch'] ? 1 : 0 ;
	}


	public function _before_update()
    {
		$_POST['allowpost'] = $_POST['allowpost'] ? 1 : 0 ;
		$_POST['allowpostverify'] = $_POST['allowpostverify'] ? 1 : 0 ;
		$_POST['allowupgrade'] = $_POST['allowupgrade'] ? 1 : 0 ;
		$_POST['allowsendmessage'] = $_POST['allowsendmessage'] ? 1 : 0 ;
		$_POST['allowattachment'] = $_POST['allowattachment'] ? 1 : 0 ;
		$_POST['allowsearch'] = $_POST['allowsearch'] ? 1 : 0 ;
	}

}
?>