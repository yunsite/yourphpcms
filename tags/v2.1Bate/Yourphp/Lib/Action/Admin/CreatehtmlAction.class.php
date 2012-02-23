<?php
/**
 * 
 * Createhtml(生成静态页)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
class CreatehtmlAction extends AdminbaseAction {
	
    protected  $module;
    public function _initialize()
    {
        parent::_initialize(); 
        foreach ((array)$this->module as $rw){
			if($rw['type']==1  && $rw['status']==1)  $data['module'][$rw['id']] = $rw;
        }
		
		$this->module=$data['module'];
		$this->assign('module',$this->module);
    }

    public function index()
    {
		$this->display('Admin/Createhtml_index');
    }

	public function createindex()
	{	
		$this->create_index();
		$this->assign ( 'jumpUrl', U(MODULE_NAME.'/index') );
		$this->success(L('index_create_OK'));		 
	}

 	public function createlist($_GET)
    {
		if(isset($_GET['dosubmit'])) {
			$this->assign ( 'waitSecond', 2);
			extract($_GET,EXTR_SKIP);
			$moduleid = intval($_GET['moduleid']);
			$doid = $doid ? intval($doid) : 0;

			if($dosubmit!=1){
				if($catids[0] == 0) { 
					$catids=array();
					foreach($this->categorys as $id=>$cat) {
						if($cat['type']!=0  || $cat['ishtml']!=1) continue;
						if($moduleid){									
							if($cat['moduleid']!=$moduleid) continue;
						}
						$catids[] = $id;
					}
				}
				$_SESSION['catids'] = $catids;
			}else{
				$catids =$_SESSION['catids'];	
			}

	
				if(!isset($catids[$doid])){
					unset($_SESSION['catids']);
					$forward = U("Createhtml/".ACTION_NAME);
					$this->assign ( 'jumpUrl', $forward);
					$this->success(L('create_update_success'));
				}else{
					$id = $catids[$doid];		
					$p = max(intval($p), 1);
					$j = 1;
					do {
						$this->create_list($id,$p);					
						$j++;
						$p++;
						$pages = isset($pages) ? $pages : PAGESTOTAL;
						 
					} while ($j <= $pages && $j < $pagesize);

					if($p <= $pages)  {
						$endpage = intval($p+$pagesize);
						$percent = round($p/$pages, 2)*100;
						$urlarray=array(
							'doid' => $doid,
							'dosubmit' => 1,
							'pages' => $pages,
							'p' => $p,
							'pagesize' => $pagesize,
						);						
						$message = L('updating').$this->categorys[$id]['catname'].L('create_update_count').$pages.L('create_update_list_num').$p.L('items_list').$percent.L('items1');
						$forward = U("Createhtml/".ACTION_NAME,$urlarray);
					} else {
						$doid++;
						$urlarray=array(
							'doid' => $doid,
							'dosubmit' => 1,
							'p' => 1,
							'pagesize' => $pagesize,
						);
						$message = L('start_updating').$this->categorys[$id]['catname']." ...";
						$forward = U("Createhtml/".ACTION_NAME,$urlarray);						
					}
					$this->assign ( 'jumpUrl', $forward);
					$this->success($message);
				}
			
		}else{
 
			if($this->categorys){
				foreach ($this->categorys as $r){
					if($r['type']==1 && $r['ishtml']==0) continue;
					if($_GET['moduleid'] && $r['moduleid'] !=  $_GET['moduleid']) continue;
					if(ACTION_NAME=='Updateurl' && $r['module']=='Page') continue;
					if(ACTION_NAME=='Createlist' && $r['ishtml']!=1) continue;
					if((ACTION_NAME=='Createshow' && $r['ishtml']!=1) || (ACTION_NAME=='Createshow' && $r['module']=='Page')) continue;				
					$array[] = $r;
				}
				import ( '@.ORG.Tree' );	
				$str  = "<option value='\$id'  \$disabled>\$spacer \$catname</option>";
				$tree = new Tree ($array);	
				$tree->icon = array('&nbsp;&nbsp;&nbsp;'.L('tree_1'),'&nbsp;&nbsp;&nbsp;'.L('tree_2'),'&nbsp;&nbsp;&nbsp;'.L('tree_3'));
				$select_categorys = $tree->get_tree(0, $str);
				$this->assign('select_categorys', $select_categorys);
			}

			$this->display('Admin/Createhtml_show');	 
		}
    }

	public function updateurl()
    {
	 
		if(isset($_GET['dosubmit'])) {
			$this->assign ( 'waitSecond', 2);
			extract($_GET,EXTR_SKIP);
			$moduleid = intval($_GET['moduleid']);
			if($moduleid){
				$module=$this->module[$moduleid]['name'];
				$dao = M($module);

				$p = max(intval($p), 1);
				$start = $pagesize*($p-1);

				if(is_array($catids) && $catids[0] > 0){
					$catids = implode(',',$catids);
					$where = " catid IN($catids) ";
					$_SESSION['catids'] = $catids;					
				}
				if($_SESSION['catids']){
					$catids = $_SESSION['catids'];
					$where = " catid IN($catids) ";
				}
				if(!isset($count)){
					$count = $dao->where($where)->count();
				}
				$pages = ceil($count/$pagesize);
				
				if($count){
					$list = $dao->field('id,catid,url')->where($where)->limit($start . ',' . $pagesize)->select();				 
					foreach($list as $r) {
						if($r['islink']) continue;
						$url = geturl($this->categorys[$r['catid']],$r,$this->Urlrule);
						unset($r['catid']);
						$r['url'] = $url['0'];
						$dao->save($r);
					}					 
				}

				if($pages > $p) {
					$p++;
					$creatednum = $start + count($list);
					$percent = round($creatednum/$count, 2)*100;
					$urlarray=array(
						'moduleid' => $moduleid,
						'dosubmit' => 1,
						'count' => $count,
						'pages' => $pages,
						'p' => $p,
						'pagesize' => $pagesize,
					);
					 
					$message = L('create_update_count').$count.L('create_update_num').$creatednum.L('items').$percent.L('items1');
					$forward = U("Createhtml/updateurl",$urlarray);
					$this->assign ( 'jumpUrl', $forward);					
					$this->success($message);
				} else {
					unset($_SESSION['catids']);
					$forward = U("Createhtml/updateurl");
					$this->assign ( 'jumpUrl', $forward);
					$this->success(L('create_update_success'));
				}

			}else{
				//按照栏目更新url
				extract($_GET,EXTR_SKIP);
				$doid = $doid ? intval($doid) : 0;
				if(empty($_SESSION['catids']) && $catids){
					if($catids[0] == 0) { 
							foreach($this->categorys as $id=>$cat) {
								if($cat['child'] || $cat['type']!=0 || $cat['module']=='Page') continue;
								$catids[] = $id;
							}
					}
					$_SESSION['catids'] = $catids;
				}else{
					$catids =$_SESSION['catids'];				
				}
				if(!isset($catids[$doid])){
					unset($_SESSION['catids']);
					$forward = U("Createhtml/".ACTION_NAME);
					$this->assign ( 'jumpUrl', $forward);
					$this->success(L('create_update_success'));
				}else{
					$id = $catids[$doid];
					$module=$this->categorys[$id]['module'];
					$dao = M($module);
					$where = "catid=$id";
					$p = max(intval($p), 1);
					$start = $pagesize*($p-1);

					if(!isset($count)){
						$count = $dao->where($where)->count();
					}
					$pages = ceil($count/$pagesize);
					
					if($count){
						$list = $dao->field('id,catid,url')->where($where)->limit($start . ',' . $pagesize)->select();				 
						foreach($list as $r) {
							if($r['islink']) continue;
							$url = geturl($this->categorys[$r['catid']],$r,$this->Urlrule);
							unset($r['catid']);
							$r['url'] = $url['0'];
							$dao->save($r);
						}
					}
 
					if($pages > $p) {
						$p++;
						$creatednum = $start + count($list);
						$percent = round($creatednum/$count, 2)*100;
						$urlarray=array(
							'doid' => $doid,
							'dosubmit' => 1,
							'count' => $count,
							'pages' => $pages,
							'p' => $p,
							'pagesize' => $pagesize,
						);
						 
						$message = L('updating').$this->categorys[$id]['catname'].L('create_update_count').$count.L('create_update_num').$creatednum.L('items').$percent.L('items1');
						$forward = U("Createhtml/".ACTION_NAME,$urlarray);
						$this->assign ( 'jumpUrl', $forward);
						$this->success($message);
					} else {
						$doid++;
						$urlarray=array(
							'doid' => $doid,
							'dosubmit' => 1,
							'p' => 1,
							'pagesize' => $pagesize,
						);
						$message = L('start_updating').$this->categorys[$id]['catname']." ...";
						$forward = U("Createhtml/".ACTION_NAME,$urlarray);
						$this->assign ( 'jumpUrl', $forward);
						$this->success($message);
					}
				}
			}
		}else{				
			if($this->categorys){
				foreach ($this->categorys as $r){
					if($r['type']==1 && $r['ishtml']==0) continue;
					if($_GET['moduleid'] && $r['moduleid'] !=  $_GET['moduleid']) continue;
					if(ACTION_NAME=='Updateurl' && $r['module']=='Page') continue;
					if(ACTION_NAME=='Createlist' && $r['ishtml']!=1) continue;
					if((ACTION_NAME=='Createshow' && $r['ishtml']!=1) || (ACTION_NAME=='Createshow' && $r['module']=='Page')) continue;				
					if($r['child'] && ACTION_NAME!='Createlist'){ 
						$r['disabled'] = 'disabled';
					}else{
						$r['disabled'] = '';
					}
					$array[] = $r;
				}
				import ( '@.ORG.Tree' );	
				$str  = "<option value='\$id'  \$disabled>\$spacer \$catname</option>";
				$tree = new Tree ($array);	
				$tree->icon = array('&nbsp;&nbsp;&nbsp;'.L('tree_1'),'&nbsp;&nbsp;&nbsp;'.L('tree_2'),'&nbsp;&nbsp;&nbsp;'.L('tree_3'));
				$select_categorys = $tree->get_tree(0, $str);
				$this->assign('select_categorys', $select_categorys);
			}
			$this->display('Admin/Createhtml_show');	 
		} 
    }

	public function createshow($_GET)
    {
		if(isset($_GET['dosubmit'])) {
			$this->assign ( 'waitSecond', 2);
			extract($_GET,EXTR_SKIP);
			$moduleid = intval($_GET['moduleid']);
			$doid = $doid ? intval($doid) : 0;

			if($dosubmit!=1){
				if($catids[0] == 0) { 
					$catids=array();
					foreach($this->categorys as $id=>$cat) {
						if($cat['child'] || $cat['type']!=0 || $cat['module']=='Page' || $cat['ishtml']!=1) continue;
						if($moduleid){									
							if($cat['moduleid']!=$moduleid) continue;
						}
						$catids[] = $id;
					}
				}	
				$_SESSION['catids'] = $catids;
			}else{
				$catids =$_SESSION['catids'];	
			}
				if(!isset($catids[$doid])){
					unset($_SESSION['catids']);
					$forward = U("Createhtml/".ACTION_NAME);
					$this->assign ( 'jumpUrl', $forward);
					$this->success(L('create_update_success'));
				}else{
					$id = $catids[$doid];
					$module=$this->categorys[$id]['module'];
					$dao = M($module);
					$where = "catid=$id";
					$p = max(intval($p), 1);
					$start = $pagesize*($p-1);

					if(!isset($count)){
						$count = $dao->where($where)->count();
					}
					$pages = ceil($count/$pagesize);
					
					if($count){
						$list = $dao->field('id,catid,url')->where($where)->limit($start . ',' . $pagesize)->select();				 
						foreach($list as $r) {
							if($r['islink']) continue;
							$module = $this->categorys[$r['catid']]['module'];
							$this->create_show($r['id'],$module);
						}
					}

					if($pages > $p) {
						$p++;
						$creatednum = $start + count($list);
						$percent = round($creatednum/$count, 2)*100;
						$urlarray=array(
							'doid' => $doid,
							'dosubmit' => 1,
							'count' => $count,
							'pages' => $pages,
							'p' => $p,
							'pagesize' => $pagesize,
						);
						 
						$message = L('updating').$this->categorys[$id]['catname'].L('create_update_count').$count.L('create_update_num').$creatednum.L('items').$percent.L('items1');
						$forward = U("Createhtml/".ACTION_NAME,$urlarray);
						$this->assign ( 'jumpUrl', $forward);
						$this->success($message);
					} else {
						$doid++;
						$urlarray=array(
							'doid' => $doid,
							'dosubmit' => 1,
							'p' => 1,
							'pagesize' => $pagesize,
						);
						$message = L('start_updating').$this->categorys[$id]['catname']." ...";
						$forward = U("Createhtml/".ACTION_NAME,$urlarray);
						$this->assign ( 'jumpUrl', $forward);
						$this->success($message);
					}
				}
		}else{
			if($this->categorys){
				foreach ($this->categorys as $r){
					if($r['type']==1 && $r['ishtml']==0) continue;
					if($_GET['moduleid'] && $r['moduleid'] !=  $_GET['moduleid']) continue;
					if(ACTION_NAME=='Updateurl' && $r['module']=='Page') continue;
					if(ACTION_NAME=='Createlist' && $r['ishtml']!=1) continue;
					if((ACTION_NAME=='Createshow' && $r['ishtml']!=1) || (ACTION_NAME=='Createshow' && $r['module']=='Page')) continue;				
					if($r['child'] && ACTION_NAME!='Createlist'){ 
						$r['disabled'] = 'disabled';
					}else{
						$r['disabled'] = '';
					}
					$array[] = $r;
				}
				import ( '@.ORG.Tree' );	
				$str  = "<option value='\$id'  \$disabled>\$spacer \$catname</option>";
				$tree = new Tree ($array);	
				$tree->icon = array('&nbsp;&nbsp;&nbsp;'.L('tree_1'),'&nbsp;&nbsp;&nbsp;'.L('tree_2'),'&nbsp;&nbsp;&nbsp;'.L('tree_3'));
				$select_categorys = $tree->get_tree(0, $str);
				$this->assign('select_categorys', $select_categorys);
			}

			$this->display('Admin/Createhtml_show');	
		}
	}

}
?>