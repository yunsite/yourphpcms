<?php
/**
 * 
 * Adminbase (后台公共模块)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
if(!defined("YOURPHP")) exit("Access Denied");
if(defined('APP_PATH')!='./Yourphp') exit("Access Denied");
class AdminbaseAction extends Action
{
	protected   $mod,$Config,$sysConfig,$nav,$menudata,$cache_model,$categorys,$module,$moduleid,$Type,$Urlrule,$Lang;
	function _initialize()
	{
 
		$this->sysConfig = F('sys.config');
		$this->menudata = F('Menu');		
		$this->module = F('Module');
		$this->Type =F('Type');
		$this->Urlrule =F('Urlrule');
		$this->mod = F('Mod');


		if(APP_LANG){
			$this->Lang = F('Lang');
			if($_GET['l']){
				if($this->Lang[$_GET['l']]['id']){
					$_SESSION['YP_lang'] = $_GET['l'];
					$_SESSION['YP_langid'] = $this->Lang[$_GET['l']]['id'];
				}else{
					$this->error ( L ( 'NO_LANG' ) );
				}
			}elseif(!$_SESSION['YP_lang'] || !$_SESSION['YP_langid']){
				$_SESSION['YP_lang'] = $this->sysConfig['DEFAULT_LANG'];
				$_SESSION['YP_langid'] = $this->Lang[$this->sysConfig['DEFAULT_LANG']]['id'];
			}
			define('LANG_NAME',$_SESSION['YP_lang']);
			define('LANG_ID',$_SESSION['YP_langid']);
 
			$this->assign('l',LANG_NAME);
			$this->assign('langid',LANG_ID);
			$this->categorys = F('Category_'.LANG_NAME);
			$this->Config = F('Config_'.LANG_NAME);
			$this->assign('Lang',$this->Lang);
		}else{
			$this->Config = F('Config');
			$this->categorys = F('Category');
		}
	
		$this->assign('module_name',MODULE_NAME);
		$this->assign('action_name',ACTION_NAME);
		$this->cache_model=array('Lang','Menu','Config','Module','Role','Category','Posid','Field','Type','Urlrule','Dbsource');

		C('PAGE_LISTROWS',$this->sysConfig['PAGE_LISTROWS']);
		C('URL_LANG',$this->sysConfig['DEFAULT_LANG']);
		C('URL_M',$this->sysConfig['URL_MODEL']);
		C('URL_M_PATHINFO_DEPR',$this->sysConfig['URL_PATHINFO_DEPR']);
		C('URL_M_HTML_SUFFIX',$this->sysConfig['URL_HTML_SUFFIX']);
		C('URL_URLRULE',$this->sysConfig['URL_URLRULE']);	

		
		C('ADMIN_ACCESS',$this->sysConfig['ADMIN_ACCESS']);
		// 用户权限检查
		if (C ( 'USER_AUTH_ON' ) && !in_array(MODULE_NAME,explode(',',C('NOT_AUTH_MODULE')))) {
			import ( '@.ORG.RBAC' );
			if (! RBAC::AccessDecision ('Admin')) {
				//检查认证识别号

				if (! $_SESSION [C ( 'USER_AUTH_KEY' )]) {
					//跳转到认证网关
					redirect ( PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
				}
				// 没有权限 抛出错误
				if (C ( 'RBAC_ERROR_PAGE' )) {
					// 定义权限错误页面
					redirect ( C ( 'RBAC_ERROR_PAGE' ) );
				} else {
					if (C ( 'GUEST_AUTH_ON' )) {
						$this->assign ( 'jumpUrl', PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
					}
					// 提示错误信息
					$this->error ( L ( '_VALID_ACCESS_' ) );
				}
			}
		}

	 	$menuid = intval($_GET['menuid']);
		if(empty($menuid)) $menuid = cookie('menuid');
		if(!empty($menuid)){
			$this->nav = $this->getnav($menuid,1);
			if($this->nav)$this->assign('nav', $this->nav);
		}

		if($this->mod[MODULE_NAME]){
			$this->moduleid = $this->mod[MODULE_NAME];
			$this->m = $this->module[$this->moduleid];
			$this->assign('moduleid',$this->moduleid);
			$this->Type = F('Type');
			$this->assign('Type',$this->Type);

			if($this->module[$this->moduleid]['type']==1 && ACTION_NAME=='index'){

				if($this->categorys){
					foreach ($this->categorys as $r){
						
						if($_SESSION['groupid']!=1 && !in_array($_SESSION['groupid'],explode(',',$r['postgroup']))) continue;
						if($r['moduleid'] != $this->moduleid || $r['child']){
							$arr= explode(",",$r['arrchildid']);
							$show=0;
							foreach((array)$arr as $rr){
								if($this->categorys[$rr]['moduleid'] ==$this->moduleid) $show=1;
							}
							if(empty($show))continue;
							$r['disabled'] =  $r['child'] ? ' disabled' : '';
						}else{
							$r['disabled'] = '';
						}
						$array[] = $r;
					}
					import ( '@.ORG.Tree' );
					$str  = "<option value='\$id' \$disabled \$selected>\$spacer \$catname</option>";
					$tree = new Tree ($array);
					$select_categorys = $tree->get_tree(0, $str);
					$this->assign('select_categorys', $select_categorys);
					$this->assign('categorys', $this->categorys);
				}

				
				$this->assign('posids', F('Posid'));
			}
		}
		import("@.ORG.Form");
		$this->assign('Form', new Form());
	}

	function getnav($menuid,$isnav=0){

			if($menuid){
				$bnav = $this->menudata[$menuid];
				if(empty($bnav['action']))$bnav['action'] ='index';
				$array = array('menuid'=> $bnav['id']);
				parse_str($bnav['data'],$c);
				$bnav['data'] = $c + $array;
			}

			if($this->menudata){
				$accessList = $_SESSION['_ACCESS_LIST'];
				foreach($this->menudata as $key=>$module) {
					if($module['parentid'] != $menuid || $module['status']==0) continue;
					if(isset($accessList[strtoupper('Admin')][strtoupper($module['model'])]) || $_SESSION[C('ADMIN_AUTH_KEY')]) {
						//设置模块访问权限$module['access'] =   1;
						if(empty($module['action'])) $module['action']='index';
						//检测动作权限
						if(isset($accessList[strtoupper('Admin')][strtoupper($module['model'])][strtoupper($module['action'])]) || $_SESSION[C('ADMIN_AUTH_KEY')]){
							$nav[$key]  = $module;
							if($isnav){
								$array=array('menuid'=> $nav[$key]['parentid']);
								cookie('menuid',$nav[$key]['parentid']);
								//$_SESSION['menuid'] = $nav[$key]['parentid'];
							}else{
								 $array=array('menuid'=> $nav[$key]['id']);
							}
							if(empty($menuid) && empty($isnav)) $array=array();
							$c=array();
							parse_str($nav[$key]['data'],$c);
							$nav[$key]['data'] = $c + $array;
						}
					}
				}
			}
			$navdata['bnav']=$bnav;
			$navdata['nav']=$nav;
			return $navdata;
	}

	function _list($modelname, $map = '', $sortBy = '', $asc = false ,$listRows = 15) {
		$model = M($modelname);
		$id=$model->getPk ();
		$this->assign ( 'pkid', $id );
		
		if (isset ( $_REQUEST ['order'] )) {
			$order = $_REQUEST ['order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $id;
		}
		if (isset ( $_REQUEST ['sort'])) {
			$_REQUEST ['sort']=='asc' ? $sort = 'asc' : $sort = 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}


		$_REQUEST ['sort'] = $sort;
		$_REQUEST ['order'] = $order;

		$keyword=$_REQUEST['keyword'];
		$searchtype=$_REQUEST['searchtype'];
		$groupid =intval($_REQUEST['groupid']);
		$catid =intval($_REQUEST['catid']);
		$posid =intval($_REQUEST['posid']);
		$typeid =intval($_REQUEST['typeid']);

		if(APP_LANG)if($this->moduleid)$map['lang']=array('eq',LANG_ID);


		if(!empty($keyword) && !empty($searchtype)){
			$map[$searchtype]=array('like','%'.$keyword.'%');
		}
		if($groupid)$map['groupid']=$groupid;
		if($catid)$map['catid']=$catid;
		if($posid)$map['posid']=$posid;
		if($typeid) $map['typeid']=$typeid;

		$tables = $model->getDbFields();
 

		foreach($_REQUEST['map'] as $key=>$res){
				if(  ($res==='0' || $res>0) || !empty($res) ){					 
					if($_REQUEST['maptype'][$key]){
						$map[$key]=array($_REQUEST['maptype'][$key],$res);
					}else{
						$map[$key]=intval($res);
					}
					$_REQUEST[$key]=$res;
				}else{					
					unset($_REQUEST[$key]);
				}
		}
 

		$this->assign($_REQUEST);

		//取得满足条件的记录总数
		$count = $model->where ( $map )->count ( $id );//echo $model->getLastsql();

		if ($count > 0) {
			import ( "@.ORG.Page" );
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			}
			$p = new Page ( $count, $listRows );
			//分页查询数据

			$field=$this->module[$this->moduleid]['listfields'];
			$field= (empty($field) || $field=='*') ? '*' : 'id,catid,url,posid,title,thumb,title_style,userid,username,hits,createtime,updatetime,status,listorder' ;
			$voList = $model->field($field)->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}

			$map[C('VAR_PAGE')]='{$page}';
			$page->urlrule = U($modelname.'/index', $map);

			//分页显示
			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'page', $page );
		}
		return;
	}


	/**
     * 添加
     *
     */

	function add() {
		$name = MODULE_NAME;
		$this->display ('edit');
	}


	function insert() {

		if($_POST['setup']) $_POST['setup']=array2string($_POST['setup']);
		$name = MODULE_NAME;
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		$id = $model->add();
		if ($id !==false) {
			if(in_array($name,$this->cache_model)) savecache($name);
			if($_POST['aid']){
				$Attachment =M('Attachment');		
				$aids =  implode(',',$_POST['aid']);
				$data['id']= $id;
				$data['catid']= intval($_POST['catid']);
				$data['status']= '1';
				$Attachment->where("aid in (".$aids.")")->save($data);
			}
			if($_POST['isajax'])$this->assign('dialog','1');
			$jumpUrl = $_POST['forward'] ? $_POST['forward'] : U(MODULE_NAME.'/index');
			$this->assign ( 'jumpUrl',$jumpUrl );
			$this->success (L('add_ok'));
		} else {
			$this->error (L('add_error').': '.$model->getDbError());
		}
	}

	/**
     * 更新
     *
     */

	function edit() {
		$name = MODULE_NAME;
		$model = M ( $name );
		$pk=ucfirst($model->getPk ());
		$id = $_REQUEST [$model->getPk ()];
		if(empty($id))   $this->error(L('do_empty'));
		$do='getBy'.$pk;
		$vo = $model->$do ( $id );
		if($vo['setup']) $vo['setup']=string2array($vo['setup']);
		$this->assign ( 'vo', $vo );
		$this->display ();
	}

	function update() {
		if($_POST['setup']) $_POST['setup']=array2string($_POST['setup']);
		$name = MODULE_NAME;
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		if (false !== $model->save ()) {
			if(in_array($name,$this->cache_model)) savecache($name);
			if($_POST['aid']){
				$Attachment =M('Attachment');		
				$aids =  implode(',',$_POST['aid']);
				$data['id']= $_POST['id'];
				$data['catid']= intval($_POST['catid']);
				$data['status']= '1';
				$Attachment->where("aid in (".$aids.")")->save($data);
			}
			if($_POST['isajax'])$this->assign('dialog','1');
			$jumpUrl = $_POST['forward'] ? $_POST['forward'] : U(MODULE_NAME.'/index');
			$this->assign ( 'jumpUrl', $jumpUrl);
			$this->success (L('edit_ok'));
		} else {
			$this->success (L('edit_error').': '.$model->getDbError());
		}
	}

	/**
     * 删除
     *
     */
	function delete(){
		$name = MODULE_NAME;
		$model = M ( $name );
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		if (isset ( $id )) {
			if(false!==$model->delete($id)){
				if(in_array($name,$this->cache_model)) savecache($name);
				if($this->moduleid)delattach(array('moduleid'=>$this->moduleid,'id'=>$id));
				$this->success(L('delete_ok'));
			}else{
				$this->error(L('delete_error').': '.$model->getDbError());
			}
		}else{
			$this->error (L('do_empty'));
		}
	}

	/**
     * 批量删除
     *
     */

	function deleteall(){

		$name = MODULE_NAME;
		$model = M ( $name );
		$ids=$_POST['ids'];
		if(!empty($ids) && is_array($ids)){
			$id=implode(',',$ids);
			if(false!==$model->delete($id)){
				if(in_array($name,$this->cache_model)) savecache($name);
				if($this->moduleid)delattach("moduleid=$this->moduleid and id in($id)");
				$this->success(L('delete_ok'));
			}else{
				$this->error(L('delete_error').': '.$model->getDbError());
			}
		}else{
			$this->error(L('do_empty'));
		}
	}

	/**
     * 批量操作
     *
     */
	public function listorder()
	{
		$name = MODULE_NAME;
		$model = M ( $name );
		$pk = $model->getPk ();
		$ids = $_POST['listorders'];
		foreach($ids as $key=>$r) {
			$data['listorder']=$r;
			$model->where($pk .'='.$key)->save($data);
		}
		if(in_array($name,$this->cache_model)) savecache($name);
		$this->success (L('do_ok'));

	}

	/*状态*/

	public function status(){
		$name = MODULE_NAME;
		$model = D ($name);
		if($model->save($_GET)){
			savecache(MODULE_NAME);
			$this->success(L('do_ok'));
		}else{
			$this->error(L('do_error'));
		}
	}

	/**
     * 默认操作
     *
     */
	public function index() {
        $name = MODULE_NAME;
		$model = M ($name);
        $list = $model->where($_REQUEST['where'])->select();
        $this->assign('list', $list);
        $this->display();
    }


	public function create_show($id,$module)
    {
		C('DEFAULT_THEME_NAME',$this->sysConfig['DEFAULT_THEME']);
		C('HTML_FILE_SUFFIX',$this->sysConfig['HTML_FILE_SUFFIX']);
		C('TMPL_FILE_NAME',str_replace('Admin/Default','Home/'.$this->sysConfig['DEFAULT_THEME'],C('TMPL_FILE_NAME')));

		
		if(APP_LANG){
			$lang =  C('URL_LANG')!=LANG_NAME ? $lang = LANG_NAME.'/' : '';
			L(include LANG_PATH.LANG_NAME.'/common.php');
			$T = F('config_'.LANG_NAME,'', './Yourphp/Tpl/Home/'.$this->sysConfig['DEFAULT_THEME'].'/');
		}else{
			L(include LANG_PATH.$this->sysConfig['DEFAULT_LANG'].'/common.php');
			$T = F('config_'.$this->sysConfig['DEFAULT_LANG'],'', './Yourphp/Tpl/Home/'.$this->sysConfig['DEFAULT_THEME'].'/');
		}
		$this->assign('T',$T);
		foreach((array)$this->module as $r){
			if($r['issearch'])$search_module[$r['name']] = L($r['name']);
		}
		$this->assign('search_module',$search_module);
		$this->assign ( 'form',new Form());
		$p =1;
		$id=intval($id);
		if(empty($id)) $this->success (L('do_empty'));;
		$this->assign($this->Config);
		$this->assign('Categorys',$this->categorys);
		$this->assign('Module',$this->module);
		$this->assign('Type',$this->Type);
		$this->assign('module_name',$module);
		$dao= M($module);
		$data = $dao->find($id);

		$catid = $data['catid'];
		$this->assign('catid',$catid);
		$cat = $this->categorys[$data['catid']];
		$this->assign ($cat);
		$bcid = explode(",",$cat['arrparentid']);
		$bcid = $bcid[1];
		if($bcid == '') $bcid=intval($catid);
		$this->assign('bcid',$bcid);

		$seo_title = $data['title'].'-'.$cat['catname'];
		$this->assign ('seo_title',$seo_title);
		$this->assign ('seo_keywords',$data['keywords']);
		$this->assign ('seo_description',$data['description']);

		$fields = F($this->mod[$module].'_Field');
		foreach($data as $key=>$c_d){
			$setup='';
			$fields[$key]['setup'] =$setup=string2array($fields[$key]['setup']);
			if($setup['fieldtype']=='varchar' && $fields[$key]['type']!='text'){
				$data[$key.'_old_val'] =$data[$key];
				$data[$key]=fieldoption($fields[$key],$data[$key]);
			}elseif($fields[$key]['type']=='images' || $fields[$key]['type']=='files'){
				$p_data=explode(':::',$data[$key]);
				$data[$key]=array();
				foreach($p_data as $k=>$res){
					$p_data_arr=explode('|',$res);
					$data[$key][$k]['filepath'] = $p_data_arr[0];
					$data[$key][$k]['filename'] = $p_data_arr[1];
				}
				unset($p_data);
				unset($p_data_arr);
			}
			unset($setup);
		}
		$this->assign('fields',$fields);
		$this->assign ('form',new Form());

		$urlrule = geturl($cat,$data,$this->Urlrule);

		if(!empty($data['template'])){
			$template = $cat['module'].'_'.$data['template'];
		}elseif(!empty($cat['template_show'])){
			$template = $cat['module'].'_'.$cat['template_show'];
		}else{
			$template = $cat['module'].'_show';
		}
		//手动分页
		$CONTENT_POS = strpos($data['content'], '[page]');
		if($CONTENT_POS !== false){
			
				$pageurls=array();
				$contents = array_filter(explode('[page]',$data['content']));
				$pagenumber = count($contents);
				for($i=1; $i<=$pagenumber; $i++) {
					$pageurls[$i] = str_replace('{$page}',$i,$urlrule);
				}
				//生成分页
				foreach ($pageurls as $p=>$urls) {
					$pages = content_pages($pagenumber,$p, $pageurls);
					$this->assign ('pages',$pages);
					$data['content'] = $contents[$p-1];
					$this->assign ($data);
					$url= ($p > 1 ) ? $urls[1] :  $urls[0];
					if(strstr($url,C('HTML_FILE_SUFFIX'))){
						$filename = basename($url,C('HTML_FILE_SUFFIX'));
						$dir = dirname($url).'/';
					}else{
						$filename = 'index';
						$dir= $url; 
					}
					$dir = substr($dir,strlen(__ROOT__.'/'));
					$this->buildHtml($filename,$dir,'./Yourphp/Tpl/Home/'.$this->sysConfig['DEFAULT_THEME'].'/'.$template.C('TMPL_TEMPLATE_SUFFIX'));
				}
		}else{
				$url = str_replace('{$page}', $p, $urlrule[0]);
				if(strstr($url,C('HTML_FILE_SUFFIX'))){
					$filename = basename($url,C('HTML_FILE_SUFFIX'));
					$dir = dirname($url).'/';
				}else{
					$filename = 'index';
					$dir= $url; 
				}
				$this->assign ('pages','');
				$this->assign ($data);
				$dir = substr($dir,strlen(__ROOT__.'/'));
				$this->buildHtml($filename,$dir,'./Yourphp/Tpl/Home/'.$this->sysConfig['DEFAULT_THEME'].'/'.$template.C('TMPL_TEMPLATE_SUFFIX'));
		}
 
		return true;
    }

	public function create_list($catid,$p=1,$count=0)
    {
		C('DEFAULT_THEME_NAME',$this->sysConfig['DEFAULT_THEME']);
		C('HTML_FILE_SUFFIX',$this->sysConfig['HTML_FILE_SUFFIX']);
		C('TMPL_FILE_NAME',str_replace('Admin/Default','Home/'.$this->sysConfig['DEFAULT_THEME'],C('TMPL_FILE_NAME')));

		if(APP_LANG){
			$lang =  C('URL_LANG')!=LANG_NAME ? $lang = LANG_NAME.'/' : '';
			L(include LANG_PATH.LANG_NAME.'/common.php');
			$T = F('config_'.LANG_NAME,'', './Yourphp/Tpl/Home/'.$this->sysConfig['DEFAULT_THEME'].'/'); 
		}else{
			L(include LANG_PATH.$this->sysConfig['DEFAULT_LANG'].'/common.php');
			$T = F('config_'.$this->sysConfig['DEFAULT_LANG'],'', './Yourphp/Tpl/Home/'.$this->sysConfig['DEFAULT_THEME'].'/');
		}
		$this->assign('T',$T);
		foreach((array)$this->module as $r){
			if($r['issearch'])$search_module[$r['name']] = L($r['name']);
		}
		$this->assign('search_module',$search_module);
		$this->assign ( 'form',new Form());

		$this->assign($this->Config);
		$this->assign('Categorys',$this->categorys);
		$this->assign('Module',$this->module);
		$this->assign('Type',$this->Type);
		$catid =intval($catid);
		if(empty($catid)) $this->success (L('do_empty'));

		$cat = $this->categorys[$catid];
		$this->assign('catid',$catid);
		if($cat['type']) return;
		if(empty($cat['ishtml'])) return;
		unset($cat['id']);
		$this->assign($cat);
		$cat['id']=$catid;
		$bcid = explode(",",$cat['arrparentid']);
		$bcid = $bcid[1];
		if($bcid == '') $bcid=intval($catid);
		$this->assign('bcid',$bcid);
		
		$urlrule = geturl($cat,'',$this->Urlrule);
		$url= ($p > 1 ) ? $urlrule[1] :  $urlrule[0];
		$url = str_replace('{$page}', $p, $url);
		if(strstr($url,C('HTML_FILE_SUFFIX'))){
			$filename = basename($url,C('HTML_FILE_SUFFIX'));
			$dir = dirname($url).'/';
		}else{
			$filename = 'index';
			$dir= $url; 
		}
		$dir = substr($dir,strlen(__ROOT__.'/'));
		if(empty($module))$module=$cat['module'];
		$this->assign('module_name',$module);


		$this->assign ( 'fields', F($cat['moduleid'].'_Field') ); 
		$this->assign ( 'form',new Form());


		if($cat['moduleid']==1){
			$cat['listtype']=2;
			$module = $cat['module'];
			$dao= M($module);
			$data = $dao->find($catid);
			$seo_title = $cat['title'] ? $cat['title'] : $data['title'];
			$this->assign ('seo_title',$seo_title);
			$this->assign ('seo_keywords',$data['keywords']);
			$this->assign ('seo_description',$data['description']);

			$template = $cat['template_list']? $cat['template_list'] : 'index';
			//手动分页
			$CONTENT_POS = strpos($data['content'], '[page]');

			if($CONTENT_POS !== false){

					$contents = array_filter(explode('[page]',$data['content']));
					$pagenumber = count($contents);
					for($i=1; $i<=$pagenumber; $i++) {
						$pageurls[$i] = str_replace('{$page}',$i,$urlrule);
					}
					//生成分页
					foreach ($pageurls as $p=>$urls) {
						$pages = content_pages($pagenumber,$p, $pageurls);
						$this->assign ('pages',$pages);
						$data['content'] = $contents[$p-1];
						$this->assign ($data);
						if($p > 1)$filename = basename($pageurls[$p]['1'],C('HTML_FILE_SUFFIX'));
						//$this->buildHtml($filename,$dir,'Home/'.$template);
						$r=$this->buildHtml($filename,$dir,'./Yourphp/Tpl/Home/'.$this->sysConfig['DEFAULT_THEME'].'/Page_'.$template.C('TMPL_TEMPLATE_SUFFIX'));
					}
			}else{
					$this->assign($data);
					//$r=$this->buildHtml($filename,$dir,'Home/'.$template);
					$r=$this->buildHtml($filename,$dir,'./Yourphp/Tpl/Home/'.$this->sysConfig['DEFAULT_THEME'].'/Page_'.$template.C('TMPL_TEMPLATE_SUFFIX'));
			}
			 
		}else{

			$seo_title = $cat['title'] ? $cat['title'] : $cat['catname'];
			$this->assign ('seo_title',$seo_title);
			$this->assign ('seo_keywords',$cat['keywords']);
			$this->assign ('seo_description',$cat['description']);

			if($cat['listtype']==1){
				$template_r = 'index';
			}else{
				$where = " status=1 ";
				if($cat['child']){
					$where .= " and catid in(".$cat['arrchildid'].")";
				}else{
					$where .=  " and catid=".$catid;
				}

				$module = $cat['module'];
				$dao= M($module);
				if(empty($count))$count = $dao->where($where)->count();
				if($count){
					import ( "@.ORG.Page" );
					$listRows =  !empty($cat['pagesize']) ? $cat['pagesize'] : C('PAGE_LISTROWS');
					$page = new Page ( $count, $listRows ,$p );
					$page->urlrule = $urlrule;
					$pages = $page->show();
					$field =  $this->module[$this->mod[$module]]['listfields'];
					$field =  $field ? $field : 'id,catid,userid,url,username,title,title_style,keywords,description,thumb,createtime,hits';

					$list = $dao->field($field)->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
					$this->assign('pages',$pages);
					$this->assign('list',$list);
				}
				$template_r = 'list';
			}

			$template = $cat['template_list']? $cat['template_list'] : $template_r;
			$r=$this->buildHtml($filename,$dir,'./Yourphp/Tpl/Home/'.$this->sysConfig['DEFAULT_THEME'].'/'.$cat['module'].'_'.$template.C('TMPL_TEMPLATE_SUFFIX'));

		}
		if($r) return true;
	}

	public function create_index()
    {
		C('HTML_FILE_SUFFIX',$this->sysConfig['HTML_FILE_SUFFIX']);
		C('DEFAULT_THEME_NAME',$this->sysConfig['DEFAULT_THEME']);
		C('TMPL_FILE_NAME',str_replace('Admin/Default','Home/'.$this->sysConfig['DEFAULT_THEME'],C('TMPL_FILE_NAME')));

		if(APP_LANG){
			$lang =  C('URL_LANG')!=LANG_NAME ? $lang = LANG_NAME.'/' : '';
			L(include LANG_PATH.LANG_NAME.'/common.php');
			$T = F('config_'.LANG_NAME,'', './Yourphp/Tpl/Home/'.$this->sysConfig['DEFAULT_THEME'].'/'); 
		}else{
			L(include LANG_PATH.$this->sysConfig['DEFAULT_LANG'].'/common.php');
			$T = F('config_'.$this->sysConfig['DEFAULT_LANG'],'', './Yourphp/Tpl/Home/'.$this->sysConfig['DEFAULT_THEME'].'/');
		}
		$this->assign('T',$T);
		foreach((array)$this->module as $r){
			if($r['issearch'])$search_module[$r['name']] = L($r['name']);
		}
		$this->assign('search_module',$search_module);
 
		
		$this->assign ( 'form',new Form());
		//cookie('think_template',$this->sysConfig['DEFAULT_THEME']);
		if(!$this->sysConfig['HOME_ISHTML']) $this->error(L('NO_HOME_ISHTML'));
		$this->assign('bcid',0);
		$this->assign('Module',$this->module);
		$this->assign('Type',$this->Type);
		$this->assign($this->Config);
		$this->assign('Categorys',$this->categorys);
 		//$r=$this->buildHtml('index','./','Home/Index_index');
		$r=$this->buildHtml('index','./'.$lang,'./Yourphp/Tpl/Home/'.$this->sysConfig['DEFAULT_THEME'].'/Index_index'.C('TMPL_TEMPLATE_SUFFIX'));
		if($r) return true;
    }

	function clisthtml($id){
			$pagesize= 10;
			$p = max(intval($p), 1);
			$j = 1;
			do {
				$this->create_list($id,$p);
				$j++;
				$p++;
				$pages = isset($pages) ? $pages : PAGESTOTAL;
			} while ($j <= $pages && $j < $pagesize);
	}

}
?>