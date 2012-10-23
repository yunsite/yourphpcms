<?php
/**
 *
 * Login(后台登陆记录)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <web@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class LogAction extends  AdminbaseAction {
    function _initialize()
    {	
		parent::_initialize();
    }
	function delete(){
		$day=intval($_GET['day']);
		if($day==1){
			$time = time()-60*60*24*30;
		}elseif($day==2){
			$time =  time()-60*60*24*90;
		}else{
			$this->error (L('do_empty'));
		}

		M(MODULE_NAME)->where("time < $time")->delete();
		$this->success(L('delete_ok'));

	}
 
}
