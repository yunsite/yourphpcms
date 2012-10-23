<?php
/**
 * 
 * Type (类别管理)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class TypeAction extends AdminbaseAction {

	protected $dao,$Type;
    function _initialize()
    {	
		parent::_initialize();
		$this->dao = M(MODULE_NAME);
		$this->Type=F('Type');

    }

	public function _before_index()
    {	
		if($_REQUEST ['parentid']){ 
			$_REQUEST['where'] = "parentid=".intval($_REQUEST ['parentid']);
		}else{
			$_REQUEST['where'] = "parentid=0";
		}
	}

	public function _before_add()
    {
		 
		$parentid =	intval($_GET['parentid']);
		$keyid = intval($_GET['keyid']);
		$this->assign('keyid', $keyid);
		$array=array();		
		if($parentid){
			foreach((array)$this->Type as $key => $r) {
				if($r['keyid']!=$keyid || empty($r['status'])) continue;
				$r['id']=$r['typeid'];
				$array[] = $r;
			}
			import ( '@.ORG.Tree' );
			$str  = "<option value='\$typeid' \$selected>\$spacer \$name</option>";
			$tree = new Tree ($array);		 
			$select_type = $tree->get_tree(0, $str,$parentid);
			$this->assign('select_type', $select_type);
		}
	}

	public function _before_edit()
    {
		$typeid = intval($_GET['typeid']);
		$parentid =	$this->Type[$typeid]['parentid'];
		$keyid = intval($_GET['keyid']);
		$this->assign('keyid', $keyid);
		$array=array();		
		if($parentid){
			foreach((array)$this->Type as $key => $r) {
				if($r['keyid']!=$keyid) continue;
				$r['id']=$r['typeid'];
				$array[] = $r;
			}
			import ( '@.ORG.Tree' );
			$str  = "<option value='\$typeid' \$selected>\$spacer \$name</option>";
			$tree = new Tree ($array);		 
			$tree->nbsp='&nbsp;&nbsp;';
			$select_type = $tree->get_tree(0, $str,$parentid);
			$this->assign('select_type', $select_type);
		}
	}

	public function insert()
    {
		$_POST['status']=1;
		$name = MODULE_NAME;
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}

		$typeid = $model->add() ;
		if ($typeid) {
			if(empty($_POST['keyid'])){
				$data['typeid'] = $data['keyid'] = $typeid; 
				$model->save($data);
			}
			savecache($name);
			$this->assign ( 'jumpUrl', U(MODULE_NAME.'/index') );
			$this->success (L('add_ok'));
		} else {
			$this->error (L('add_error').': '.$model->getDbError());
		}
	}

	public function get_child($linkageid) {
		$where = array('parentid'=>$linkageid);
		$this->childnode[] = intval($linkageid);
		$result = $this->db->select($where);
		if($result) {
			foreach($result as $r) {
				$this->_get_childnode($r['linkageid']);
			}
		}
	}

 

	public function get_arrparentids($pid, $array=array(),$arrparentid='') {
		if(!is_array($array) || !isset($array[$pid])) return $pid;
		$parentid = $array[$pid]['parentid'];
		$arrparentid = $arrparentid ? $parentid.','.$arrparentid : $parentid;
		if($parentid) {
			$arrparentid = $this->get_arrparentids($parentid,$array, $arrparentid);
		}else{
			$data = array();
			$data['bid'] = $pid;
			$data['arrparentid'] = $arrparentid;
		}
		return $data;
	}

	public function get_arrchildid($id, $array=array()) {
		$arrchildid = $id;
 
		foreach($array as $catid => $cat) {
			if($cat['parentid'] && $id != $catid) {
				$arrparentids = explode(',', $cat['arrparentid']);
				if(in_array($id, $arrparentids)) $arrchildid .= ','.$catid;
			}
		} 
		return $arrchildid;
	}
}
?>