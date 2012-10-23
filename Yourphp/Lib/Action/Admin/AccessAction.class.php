<?php
/**
 * 
 * Access (权限设置)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
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

		$r=$node->where("pid=0 and status=1")->select();
 
		$this->assign('topnode', $r);
		 
		$groups[0]=array('id'=>0,'name'=>L('ACCESS_PUBLIC'));
		foreach($this->menudata as $key=>$r){ if($r['parentid']==0)$groups[$r[id]]=$r;}
		$this->assign('groups', $groups);
 

		foreach($groups as $key=>$res){			
			$result=$node->where("groupid=$res[id] and status=1")->select();
			$array=array();			
			foreach($result as $r) {
				$r['parentid']=$r['pid'];
				$r['selected'] = array_key_exists($r['id'],$alist)   ? 'checked' : ''; 
				$array[] = $r;
			}
			$nodes[$res['id']]['data']  =$array;
			$nodes[$res['id']]['groupinfo']=$res;		
		}

		$node_app=$this->dao->where("pid=0 and status=1")->select();
		$this->assign('node_app', $node_app);
		
		$this->assign('alist', $alist);
		$this->assign('node', $nodes);
		$this->assign('rid', $rid);	
		$this->display();
	}

	function insert(){		 
		$rid=$_POST['rid'];
		$nid=$_POST['nid'];
		if(!empty($rid)){
			if($nid){
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
				$r=$this->dao->addAll($data);
			}else{
				$r= $this->dao->where('role_id = '.$rid)->delete();
			}
			if(false!==$r){				
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