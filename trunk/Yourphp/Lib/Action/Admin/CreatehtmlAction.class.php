<?php
/**
 * 
 * Createhtml(生成静态页)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
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
		$this->assign('menuid',intval($_GET['menuid']));
    }

    public function index()
    {
		$this->display('Createhtml:index');
    }

	public function docreateindex()
	{
		$this->create_index();
		$this->assign ( 'jumpUrl', U(MODULE_NAME.'/index') );
		$this->success(L('index_create_OK'));		 
	}

 	public function createlist()
    {
		$moduleid = intval($_GET['moduleid']);
			if($this->categorys){
				foreach ($this->categorys as $r){
					if($r['type']==1 && $r['ishtml']==0) continue;
					if($moduleid && $r['moduleid'] !=  $moduleid) continue;
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

			$this->display('Createhtml:show');	 
		 
    }


	public function doCreatelist()
    {
			$this->assign ( 'waitSecond', 0);
			extract($_GET,EXTR_SKIP);
			$moduleid = intval($_GET['moduleid']);
			$doid = $doid ? intval($doid) : 0;
			$count = intval($_GET['count']);
			if($dosubmit!=1){
				$catids=array();
				if($_GET['catids'][0]){
					$catids = $_SESSION['catids'] = $_GET['catids'];
				}else{
					foreach($this->categorys as $id=>$cat) {
						if($cat['type']!=0  || $cat['ishtml']!=1) continue;
						if($moduleid){									
							if($cat['moduleid']!=$moduleid) continue;
						}
						$catids[] = $id;
					}
					$catids = $_SESSION['catids'] = $catids;
				}
			}else{
				$catids =$_SESSION['catids'];	
			}
			if(!isset($catids[$doid])){
					unset($_SESSION['catids']);
					$forward = U("Createhtml/createlist");
					$this->assign ( 'jumpUrl', $forward);
					$this->success(L('create_update_success'));
			}else{
					$id = $catids[$doid];					
					if(empty($count)){
						$module = $this->categorys[$id]['module'];
						$dao= M($module);
						$where['status']=1;
						if(empty($this->categorys[$id]['listtype'])){
							if($this->categorys[$id]['child']){
								$where['catid']=array('in',$this->categorys[$id]['arrchildid']);
							}else{
								$where['catid']=$id;
							}							
							$count = $dao->where($where)->count();
						}else{
							$count=1;
						}
								
					}
					if(empty($pages)){
						$cat_pagesize =  !empty($this->categorys[$id]['pagesize']) ? $this->categorys[$id]['pagesize'] : C('PAGE_LISTROWS');
						$pages = ceil($count/$cat_pagesize);
					}

					$p = max(intval($p), 1);
					$j = 1;
					do {
						$this->create_list($id,$p,$count);					
						$j++;
						$p++;
						$pages = isset($pages) ? $pages : PAGESTOTAL;
						 
					} while ($p <= $pages && $j < $pagesize);

					if($p <= $pages)  {
						$endpage = intval($p+$pagesize);
						$percent = round($p/$pages, 2)*100;
						$urlarray=array(
							'count' => $count,
							'doid' => $doid,
							'dosubmit' => 1,
							'pages' => $pages,
							'p' => $p,
							'pagesize' => $pagesize,
							'iscreatehtml'=>1,
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
							'iscreatehtml'=>1,
						);
						$message = L('start_updating').$this->categorys[$id]['catname']." ...";
						$forward = U("Createhtml/".ACTION_NAME,$urlarray);						
					}
					$this->assign ( 'jumpUrl', $forward);
					$this->success($message);
			}
	}


	public function doUpdateurl()
    {
		$this->assign ( 'waitSecond', 0);
		$moduleid = intval($_GET['moduleid']);
		extract($_GET,EXTR_SKIP);
		if($moduleid<=0 && $catids[0] <= 0){
				
			if($this->module && !$_SESSION['moduleids']){
					foreach($this->module as $moduleid=>$r){
						$tablename=C('DB_PREFIX').$this->module[$moduleid]['name'];
						$db=D('');
						$db =   DB::getInstance();
						$tables = $db->getTables();			
						$Fields=$db->getFields($tablename); 
						foreach ( $Fields as $key =>$r){
							if($key=='url') $_SESSION['moduleids'][] = $moduleid;
						}
					}
			}
			$doid = $doid ? intval($doid) : 0;
			if(!isset($_SESSION['moduleids'][$doid])){
					unset($_SESSION['moduleids']);
					$forward = U("Createhtml/updateurl");
					$this->assign ( 'jumpUrl', $forward);
					$this->success(L('create_update_success'));
			}else{
					$moduleid = $_SESSION['moduleids'][$doid];
					$module=$this->module[$moduleid]['name'];
					$dao = M($module);
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
							 
							$message = L('updating').$this->module[$moduleid]['title'].L('create_update_count').$count.L('create_update_num').$creatednum.L('items').$percent.L('items1');
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
							$message = L('start_updating').$this->module[$moduleid]['title']." ...";
							$forward = U("Createhtml/".ACTION_NAME,$urlarray);
							$this->assign ( 'jumpUrl', $forward);
							$this->success($message);
						}
				}
			}elseif($moduleid){
				$module=$this->module[$moduleid]['name'];
				$dao = M($module);

				$p = max(intval($p), 1);
				$start = $pagesize*($p-1);

				if(is_array($catids) && $catids[0] > 0){
					$cids = implode(',',$catids);
					$where = " catid IN($cids) ";
					$_SESSION['catids'] = $catids;					
				}
				if(!$catids && $_SESSION['catids'] && $_SESSION['catids'][0] > 0){
					$catids = implode(',',$_SESSION['catids']);;
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
					$forward = U("Createhtml/updateurl");
					$this->assign ( 'jumpUrl', $forward);
					$this->success(L('create_update_success'));
				}elseif($catids[$doid]<=0){
					$forward = U("Createhtml/updateurl");
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
	}

	public function updateurl()
    {
			$moduleid = intval($_GET['moduleid']);
			$this->assign('moduleid',$moduleid);
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
			$this->display('Createhtml:show');	 
		 
    }

	public function createshow()
    { 
		$moduleid = intval($_GET['moduleid']);
			if($this->categorys){
				foreach ($this->categorys as $r){
					if($r['type']==1 && $r['ishtml']==0) continue;
					if($moduleid && $r['moduleid'] !=  $moduleid) continue;
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
			$this->display('Createhtml:show');	 
	}


	public function doCreateshow()
    {
		 
			$this->assign ( 'waitSecond', 0);
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
					$forward = U("Createhtml/Createshow");
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
							'iscreatehtml'=>1,
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
							'iscreatehtml'=>1,
						);
						$message = L('start_updating').$this->categorys[$id]['catname']." ...";
						$forward = U("Createhtml/".ACTION_NAME,$urlarray);
						$this->assign ( 'jumpUrl', $forward);
						$this->success($message);
					}
			}

		}

		public function createsitemap()
		{

			foreach((array)$this->module as $r){
					if($r['issearch'])$search_module[$r['name']] =  $r;
			}
			$this->assign('module',$search_module);

			$xmlmap=file_exists('./sitemap.xml');
			$htmlmap=file_exists('./sitemap.html');
			$this->assign('siteurl',$this->Config['site_url']);
			$this->assign('xmlmap',$xmlmap);
			$this->assign('htmlmap',$htmlmap); 
			$this->assign('yesorno',array(0 => L('no'),1  => L('yes')));
			$this->display('Createhtml:sitemap');	 
			 
		}

		public function docreatesitemap()
		{
			if($_GET['htmlmap']){
				$r = $this->create_index(1);
			}

			if($_GET['xmlmap']){
				import("@.ORG.Cxml");
				$array=array();
				
 
				$array[0]['NodeName']['value'] ='url';
				$array[0]['loc']['value']=$this->Config['site_url'];
				$array[0]['lastmod']['value']= date('Y-m-d',time());
				$array[0]['changefreq']['value'] ='weekly';
				$array[0]['priority']['value'] =1;

				foreach((array)$this->module as $r){
					if($r['issearch']){
						$num = intval($_GET[$r['name']]);
						if(!$num) continue;
						$data = M($r['name'])->field('id,title,url,createtime')->where("status=1")->order('id desc')->limit('0,'.$num)->select();
						foreach($data as $key=> $res){
							$arraya[$key]['NodeName']['value'] ='url';
							$arraya[$key]['loc']['value'] = $this->Config['site_url'].$res['url'];					
							$arraya[$key]['lastmod']['value'] = date('Y-m-d',$res['createtime']);					
							$arraya[$key]['changefreq']['value'] ='weekly';
							$arraya[$key]['priority']['value'] =0.7;
						}
						$array =array_merge($array,$arraya);						
					}
				}
				
				$Cxml = new Cxml();
				$Cxml->root='urlset';
				$Cxml->root_attributes=array('xmlns'=>'http://www.sitemaps.org/schemas/sitemap/0.9');
				$xmldata = $Cxml->Cxml($array,'./sitemap.xml');
				$d=file_exists('./sitemap.xml');;
			}
			if(($_GET['htmlmap'] && $r) || ($_GET['xmlmap']&& $d)){$this->success(L('DO_OK'));}else{$this->error(L('Create error.'));}
			 
		}

}
?>