<?php 
/**
 * 
 * Menu(菜单管理)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <web@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class MenuAction extends AdminbaseAction
{
    public $dao;
	function _initialize()
	{
		parent::_initialize();
		$this->dao = D('Admin/menu');
		$this->assign('actionname',$this->getActionName());
 
	}

	/**
     * 列表
     *
     */
	public function index()
	{		
		$result = $this->menudata;
		foreach($result as $r) {
			if($r['type']!=1) continue;
			$r['str_manage'] = '<a href="'.U('Menu/add',array( 'parentid' => $r['id'])).'">'.L('menu_add_submenu').'</a> | <a href="'.U('Menu/edit',array( 'id' => $r['id'])).'">'.L('edit').'</a> | <a href="javascript:confirm_delete(\''.U('Menu/delete',array( 'id' => $r['id'])).'\')">'.L('delete').'</a> ';
			$r['status'] ? $r['status']='<font color="green">'.L('enable').'</font>' : $r['status']='<font color="red">'.L(' disable').'</font>' ;
			$array[] = $r;
		}
 
		$str  = "<tr>					
					<td width='40' align='center'><input name='listorders[\$id]' type='text' size='3' value='\$listorder'></td>
					<td align='center'>\$id</td>
					<td >\$spacer\$name</td>
					<td align='center'>\$status</td>
					<td align='center'>\$str_manage</td>
				</tr>";
		import ( '@.ORG.Tree' );
		$tree = new Tree ($array);	
		$tree->icon = array('&nbsp;&nbsp;&nbsp;'.L('tree_1'),'&nbsp;&nbsp;&nbsp;'.L('tree_2'),'&nbsp;&nbsp;&nbsp;'.L('tree_3'));
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';		
		$select_categorys = $tree->get_tree(0, $str);
		$this->assign('select_categorys', $select_categorys);
		$this->display();
	}

	/**
     * 提交
     *
     */
	public function _before_add()
	{
		$parentid =	intval($_GET['parentid']);
		import ( '@.ORG.Tree' );		
		$result = $this->menudata;
		foreach($result as $r) {
			if($r['type']!=1) continue;
			$r['selected'] = $r['id'] == $parentid ? 'selected' : '';
			$array[] = $r;
		}
		
		$str  = "<option value='\$id' \$selected>\$spacer \$name</option>";

		$tree = new Tree ($array);	
		$tree->icon = array(L('tree_1'),L('tree_2'),L('tree_3'));
		$select_categorys = $tree->get_tree(0, $str,$parentid);
		$this->assign('select_categorys', $select_categorys);
	}


 
	function edit() {
		 
		$id =	intval($_GET['id']);;
		$vo = $this->menudata[$id];
		$parentid =	intval($vo['parentid']);
		import ( '@.ORG.Tree' );		
		$result = $this->menudata;
		foreach($result as $r) {
			if($r['type']!=1) continue;
			$r['selected'] = $r['id'] == $parentid ? 'selected' : '';
			$array[] = $r;
		}
		$str  = "<option value='\$id' \$selected>\$spacer \$name</option>";
		$tree = new Tree ($array);
		$tree->icon = array(L('tree_1'),L('tree_2'),L('tree_3'));
		$select_categorys = $tree->get_tree(0, $str,$parentid);
		$this->assign('select_categorys', $select_categorys);
		$this->assign ( 'vo', $vo );
		$this->display ();
	}


}
?>