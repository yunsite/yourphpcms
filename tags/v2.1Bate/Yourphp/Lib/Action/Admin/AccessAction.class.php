<?php
/**
 * 
 * Access (权限设置)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
if(defined('APP_NAME')!='Yourphp' && !defined("YOURPHP"))  exit("Access Denied");
class AccessAction extends AdminbaseAction {

	protected $dao;
    function _initialize()
    {	
		parent::_initialize();
		$this->dao=M('Access');
    }

	function index(){

		$rid=intval($_GET['rid']);
		$alist = $this->dao->where('role_id = '.$rid)->getField('node_id,role_id');
		$node=M('Node');
		$result =$node->findAll();
		foreach($result as $r) {
			$r['parentid']=$r['pid'];
			$r['selected'] = array_key_exists($r['id'],$alist)   ? 'checked' : ''; 
			$array[] = $r;
		}
		$this->assign('node', $array);
		$this->assign('rid', $rid);	
		$this->display();
	}	
	function insert(){		 
		$rid=$_POST['rid'];
		$nid=$_POST['nid'];
		if(!empty($rid) && !empty($nid)){
			$node_id=implode(',',$nid);
			$node=M('Node');
			$list=$node->where('id in('.$node_id.')')->select();
			$this->dao->where('role_id = '.$rid)->delete();
			foreach($list as $key=> $node){
				$data[$key]['role_id']=$rid;
				$data[$key]['node_id']=$node['id'];
				$data[$key]["level"]=$node['level'];
				$data[$key]["pid"]=$node['pid'];
			}
			if(false!==$this->dao->addAll($data)){				
				$this->success(L('role_ok'));				
			}else{
				$this->error(L('role_error'));
			}
		}else{
			$this->error(L('do_empty'));
		}
	}	
}
?>