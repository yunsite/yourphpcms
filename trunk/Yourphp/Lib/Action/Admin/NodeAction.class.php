<?php
/**
 * 
 * Node (权限管理)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class NodeAction extends AdminbaseAction {

	protected $dao,$groups;
    function _initialize()
    {	
		parent::_initialize();
		$this->dao=D('node');	
 
		$this->groups[0]=array('id'=>0,'name'=>L('ACCESS_PUBLIC'));
		foreach($this->menudata as $key=>$r){ if($r['parentid']==0)$this->groups[$r[id]]=$r;}
		$this->assign('groups', $this->groups);
    }


	function index(){	 
		
		$data[]=array('name'=>'index','title'=>'列表','pid'=>90,'status'=>1,'listorder'=>0,'level'=>3,'groupid'=>3);
		$data[]=array('name'=>'add','title'=>'添加','pid'=>90,'status'=>1,'listorder'=>0,'level'=>3,'groupid'=>3);
		$data[]=array('name'=>'edit','title'=>'修改','pid'=>90,'status'=>1,'listorder'=>0,'level'=>3,'groupid'=>3);
		$data[]=array('name'=>'insert','title'=>'插入','pid'=>90,'status'=>1,'listorder'=>0,'level'=>3,'groupid'=>3);
		$data[]=array('name'=>'update','title'=>'更新','pid'=>90,'status'=>1,'listorder'=>0,'level'=>3,'groupid'=>3);
		$data[]=array('name'=>'delete','title'=>'删除','pid'=>90,'status'=>1,'listorder'=>0,'level'=>3,'groupid'=>3);
		$data[]=array('name'=>'status','title'=>'状态','pid'=>90,'status'=>1,'listorder'=>0,'level'=>3,'groupid'=>3);
		$data[]=array('name'=>'listorder','title'=>'排序','pid'=>90,'status'=>1,'listorder'=>0,'level'=>3,'groupid'=>3);
		//$data[]=array('name'=>'deleteall','title'=>'批量删除','pid'=>90,'status'=>1,'listorder'=>0,'level'=>3,'groupid'=>3);
		//$data[]=array('name'=>'statusallok','title'=>'批量审核','pid'=>90,'status'=>1,'listorder'=>0,'level'=>3,'groupid'=>3);
		
		foreach($data as $sql){
			//$this->dao->add($sql);
		}
		
		

		$str  = "<tr>
					<td align='center'><input name='listorders[\$id]' type='text' size='2' value='\$listorder' class='input-text-c'></td>
					<td >\$spacer\$title</td>
					<td >\$name</td>
					<td align='center'>\$status</td>
					<td align='center'>\$str_manage</td>
				</tr>";
		import ( '@.ORG.Tree' );
		
		
		
		foreach($this->groups as $key=>$res){
			
			$result=$this->dao->where("groupid=$res[id]")->select();
			$array=array();			
			foreach($result as $r) {
				$r['str_manage'] = '<a href="'.U('Node/add',array( 'pid' => $r['id'],'groupid'=>$r['groupid'])).'">'.L('add').'</a> | <a href="'.U('Node/edit',array( 'id' => $r['id'])).'">'.L('edit').'</a> | <a href="javascript:confirm_delete(\''.U('Node/delete',array( 'id' => $r['id'])).'\',\''.L('confirm',array('message'=>$r['cname'])).'\')">'.L('delete').'</a> ';
				$r['parentid']=$r['pid'];
				$r['status']==1 ? $r['status']=L('enable') : $r['status']=L('disable') ;
				$array[] = $r;
			}

			$tree = new Tree ($array);	
			$tree->icon = array('&nbsp;&nbsp;&nbsp;'.L('tree_1'),'&nbsp;&nbsp;&nbsp;'.L('tree_2'),'&nbsp;&nbsp;&nbsp;'.L('tree_3'));
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$data = $tree->get_tree(1, $str);

			$nodes[$res['id']]['data']  = $data;
			$nodes[$res['id']]['groupinfo']=$res;		
		}

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
		$groupid=	intval($_GET['groupid']);
		$pid =	intval($_GET['pid']);
		import ( '@.ORG.Tree' );		
		$result = $this->dao->select();
		foreach($result as $r) {
			if($r['status']!=1 || $r['level']==3) continue;
			$r['selected'] = $r['id'] == $pid ? 'selected' : '';
			$r['parentid']=$r['pid'];
			$array[] = $r;
		}
		$str  = "<option value='\$id' \$selected>\$spacer \$title</option>";
		$tree = new Tree ($array);		 
		$nodes  = $tree->get_tree(0, $str,$pid);
		$this->assign('nodes', $nodes);
		$this->assign('groupid', $groupid);
	}
	function edit(){

		$id =	intval($_GET['id']);;
		$vo = $this->dao->getById($id);
		$this->assign('groupid', $vo['groupid']);
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