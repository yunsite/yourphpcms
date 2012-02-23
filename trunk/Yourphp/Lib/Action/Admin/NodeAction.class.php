<?php
/**
 * 
 * Node (权限管理)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
class NodeAction extends AdminbaseAction {

	protected $dao;
    function _initialize()
    {	
		parent::_initialize();
		$this->dao=D('node');	
    }


	function index(){	 
		$result =$this->dao->select();
		foreach($result as $r) {
			$r['str_manage'] = '<a href="'.U('Node/add',array( 'pid' => $r['id'])).'">'.L('add').'</a> | <a href="'.U('Node/edit',array( 'id' => $r['id'])).'">'.L('edit').'</a> | <a href="javascript:confirm_delete(\''.U('Node/delete',array( 'id' => $r['id'])).'\',\''.L('confirm',array('message'=>$r['cname'])).'\')">'.L('delete').'</a> ';
			$r['parentid']=$r['pid'];
			$r['status']==1 ? $r['status']=L('enable') : $r['status']=L('disable') ;
			$array[] = $r;
		}
 
		$str  = "<tr>
					<td align='center'><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input-text-c'></td>
					<td align='center'>\$id</td>
					<td >\$spacer\$title</td>
					<td >\$name</td>
					<td >&nbsp;\$remark</td>
					<td align='center'>\$status</td>
					<td align='center'>\$str_manage</td>
				</tr>";
		import ( '@.ORG.Tree' );
		$tree = new Tree ($array);	
		$tree->icon = array('&nbsp;&nbsp;&nbsp;'.L('tree_1'),'&nbsp;&nbsp;&nbsp;'.L('tree_2'),'&nbsp;&nbsp;&nbsp;'.L('tree_3'));
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		
		$nodes = $tree->get_tree(0, $str);
		$this->assign('nodes', $nodes); 
		$this->display();
	}
	function _before_insert(){
		if($_POST['pid']){			 
			$level =$this->dao->getById($_POST['pid']);
			$_POST['level']=$level['level']+1;
		}else{
			$_POST['level']=1;
		}

	}

	function _before_update(){		 
		if($_POST['pid']){			 
			$level =$this->dao->getById($_POST['pid']);
			$_POST['level']=$level['level']+1;
		}else{
			$_POST['level']=1;
		}

	}
	function _before_add(){	
		$pid =	intval($_GET['pid']);
		import ( '@.ORG.Tree' );		
		$result = $this->dao->select();
		foreach($result as $r) {
			if($r['status']!=1) continue;
			$r['selected'] = $r['id'] == $pid ? 'selected' : '';
			$r['parentid']=$r['pid'];
			$array[] = $r;
		}
		$str  = "<option value='\$id' \$selected>\$spacer \$title</option>";
		$tree = new Tree ($array);		 
		$nodes  = $tree->get_tree(0, $str,$pid);
		$this->assign('nodes', $nodes);
	}
	function edit(){
		$id =	intval($_GET['id']);;
		$vo = $this->dao->getById($id);
		$pid =	intval($vo['pid']);
		import ( '@.ORG.Tree' );		
		$result = $this->dao->select();
		foreach($result as $r) {
			if($r['status']!=1) continue;
			$r['selected'] = $r['id'] == $pid ? 'selected' : '';
			$r['parentid']=$r['pid'];
			$array[] = $r;
		}
		$str  = "<option value='\$id' \$selected>\$spacer \$title</option>";
		$tree = new Tree ($array);		 
		$nodes = $tree->get_tree(0, $str,$pid);
		$this->assign('nodes', $nodes);
		$this->assign ( 'udate', $vo );
		$this->display ();
 
	}
 
}
?>