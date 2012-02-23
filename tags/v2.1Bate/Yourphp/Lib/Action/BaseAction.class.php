<?php
/**
 * 
 * Base (前台公共模块)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
if(defined('APP_PATH')!='./Yourphp' && !defined("YOURPHP"))  exit("Access Denied");

class BaseAction extends Action
{
	protected   $Config ,$sysConfig,$categorys,$module,$moduleid,$mod,$dao,$Type,$Role,$_userid,$_groupid,$_email,$_username ,$forward ,$user_menu,$Lang,$member_config;
    public function _initialize() {

			$this->sysConfig = F('sys.config');
			$this->module = F('Module');
			$this->Role = F('Role');
			$this->Type =F('Type');
			$this->mod= F('Mod');
			$this->moduleid=$this->mod[MODULE_NAME];

			if(APP_LANG){
				$this->Lang = F('Lang');
				$this->assign('Lang',$this->Lang);
				if($_GET['l']){
					if(!$this->Lang[$_GET['l']]['status'])$this->error ( L ( 'NO_LANG' ) );
					$lang=$_GET['l'];
				}else{
					$lang=$this->sysConfig['DEFAULT_LANG'];
				}
				define('LANG_NOW', $lang);
				$this->langid = $this->Lang[$lang]['id'];
				$this->lang=$lang;
				$this->categorys = F('Category_'.$lang);
				$this->Config = F('Config_'.$lang);
				$this->assign('l',$lang);
				$this->assign('langid',$this->langid);
				$T = F('config_'.$lang,'', './Yourphp/Tpl/'.$this->sysConfig['DEFAULT_THEME'].'/Home/');

				C('TMPL_CACHFILE_SUFFIX',$lang.C('TMPL_CACHFILE_SUFFIX'));
			}else{
				$T = F('config_'.$this->sysConfig['DEFAULT_LANG'],'', './Yourphp/Tpl/'.$this->sysConfig['DEFAULT_THEME'].'/Home/');
				$this->categorys = F('Category');
				$this->Config = F('Config');
			}



			$this->assign('T',$T);
			$this->assign($this->Config);
			$this->assign('Role',$this->Role);
			$this->assign('Type',$this->Type);
			$this->assign('Module',$this->module);
			$this->assign('Categorys',$this->categorys);
			import("@.ORG.Form");			
			$this->assign ( 'form',new Form());
			import("@.TagLib.TagLibYp");


			C('PAGE_LISTROWS',$this->sysConfig['PAGE_LISTROWS']);
			C('URL_M',$this->sysConfig['URL_MODEL']);
			C('URL_M_PATHINFO_DEPR',$this->sysConfig['URL_PATHINFO_DEPR']);
			C('URL_M_HTML_SUFFIX',$this->sysConfig['URL_HTML_SUFFIX']);
			C('URL_LANG',$this->sysConfig['DEFAULT_LANG']);
			C('DEFAULT_THEME_NAME',$this->sysConfig['DEFAULT_THEME']);


			import("@.ORG.Online");
			$session = new Online();
 

			if($_COOKIE['YP_auth']){
				$yourphp_auth_key = sysmd5($this->sysConfig['ADMIN_ACCESS'].$_SERVER['HTTP_USER_AGENT']);
				list($userid,$groupid, $password) = explode("-", authcode($_COOKIE['YP_auth'], 'DECODE', $yourphp_auth_key));
				$this->_userid = $userid;
				$this->_username = $_COOKIE['YP_username'];
				$this->_groupid = $groupid; 
				$this->_email = $_COOKIE['YP_email'];
			}else{
				$this->_groupid = $_COOKIE['YP_groupid']=4;
				$this->_userid =0;
			}


			foreach((array)$this->module as $r){
				if($r['issearch'])$search_module[$r['name']] = L($r['name']);
				if($r['ispost'] && (in_array($this->_groupid,explode(',',$r['postgroup']))))$this->user_menu[$r['id']]=$r;
			}
			if(GROUP_NAME=='User'){
				$langext = $lang ? '_'.$lang : '';
				$this->member_config=F('member.config'.$langext);
				$this->assign('member_config',$this->member_config);
				$this->assign('user_menu',$this->user_menu);
				if($this->_groupid=='5' &&  MODULE_NAME!='Login'){ 
					$this->assign('jumpUrl',URL('User-Login/emailcheck'));
					$this->assign('waitSecond',3);
					$this->success(L('no_regcheckemail'));
				}
			}
			if($_GET['forward'] || $_POST['forward']){	
				$this->forward = $_GET['forward'].$_POST['forward'];
			}else{
				if(MODULE_NAME!='Register' || MODULE_NAME!='Login' )
				$this->forward =isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] :  $this->Config['site_url'];
			}
			$this->assign('forward',$this->forward);

			$this->assign('search_module',$search_module);
			$this->assign('module_name',MODULE_NAME);
			$this->assign('action_name',ACTION_NAME);
 
	}
	public function tags($tag)
	{
		$slug = $tag ? $tag : $_REQUEST['tag'];
		if(C('URL_MODEL')==0){
			$module = $_REQUEST['moduleid'] ? $this->module[$_REQUEST['moduleid']]['name'] : '';
			$module = $_REQUEST['module'] ? $_REQUEST['module'] : $module;
			$p= max(intval($_REQUEST[C('VAR_PAGE')]),1);
		}elseif($this->mod[ucfirst($slug)]){
			$module=ucfirst($slug);
			$index= C('URL_MODEL')==1 ? __ROOT__.'/index.php/' : __ROOT__.'/';
			$slug = str_replace($index.'Tags/'.ucfirst($slug).'/','',$_SERVER['REQUEST_URI']);			
			if(strpos($slug,'/')){
				$pagearr = explode('/',$slug);
				$slug =$pagearr[0];
				$p =intval($pagearr[1]);
			}
		}else{
			$index= C('URL_MODEL')==1 ? __ROOT__.'/index.php/' : __ROOT__.'/';
			$pagearr = str_replace($index.'Tags/'.$slug.'/','',$_SERVER['REQUEST_URI']);
			$p =intval($pagearr);
		}

		$prefix=C( "DB_PREFIX" );
		$Tags=M('Tags');
		$Tags_data=M('Tags_data');
		$where= APP_LANG ?  " lang=".$this->langid : 1 ;

		if($slug){
			$module= $module ? $module :'Article';
			$moduleid=$this->mod[$module];
			$data = $Tags->where($where." and moduleid = $moduleid and slug='".$slug."'")->find();
			$this->assign ('seo_title',$data['name']);
			$this->assign ('seo_keywords',$data['name']);
			$this->assign ('seo_description',$data['name']);
			$tagid=$data['id'];
			$this->assign ('data',$data);
			$mtable=$prefix.strtolower($module);
			$count = $Tags_data->table($prefix.'tags_data as a')->join($mtable." as b on a.id=b.id ")->where("a.tagid=".$tagid)->count();
			if($count){
				import ( "@.ORG.Page" );
				$listRows =  C('PAGE_LISTROWS'); //C('PAGE_LISTROWS')
				$page = new Page ( $count, $listRows,$p );
				$page->urlrule = TAGURL($data,1);
				$pages = $page->show();
				$field =  'b.id,b.catid,b.userid,b.url,b.username,b.title,b.keywords,b.description,b.thumb,b.createtime';
				$list = $Tags_data->field($field)->table($prefix.'tags_data as a')->join($mtable." as b on a.id=b.id")->where($where." and a.tagid=".$tagid)->order('b.listorder desc,b.id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
				$this->assign('pages',$pages);
				$this->assign('list',$list);
			}
		}else{
			$moduleid=$this->mod[$module];
			$where .= $moduleid ? ' and moduleid='.$moduleid : '';
			$count = $Tags->where($where)->count();
			if($count){
				import ( "@.ORG.Page" );
				$listRows =  50;
				$page = new Page ( $count, $listRows );
				$page->urlrule = TAGURL(array('moduleid'=>$moduleid),1); 
				$pages = $page->show();
				$list = $Tags->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
				foreach($list as $key=>$r){ $list[$key]['module']=$this->module[$r['moduleid']]['name'];}
				$this->assign('pages',$pages);
				$this->assign('list',$list);
			}
		}
		$this->assign('bcid',0);//顶级栏目 
		$template = $slug ? 'Tags_list' : 'Tags_index';
		$this->display($template);
	}

    public function index($catid='',$module='')
    {
		$this->Urlrule =F('Urlrule');
		if(empty($catid)) $catid =  intval($_REQUEST['id']);
		$p= max(intval($_REQUEST[C('VAR_PAGE')]),1);
		if($catid){
			$cat = $this->categorys[$catid];
			$bcid = explode(",",$cat['arrparentid']); 
			$bcid = $bcid[1]; 
			if($bcid == '') $bcid=intval($catid);
			if(empty($module))$module=$cat['module'];
			$this->assign('module_name',$module);
			unset($cat['id']);
			$this->assign($cat);
			$cat['id']=$catid;
			$this->assign('catid',$catid);
			$this->assign('bcid',$bcid);
		}
		if($cat['readgroup'] && $this->_groupid!=1 && !in_array($this->_groupid,explode(',',$cat['readgroup']))){$this->assign('jumpUrl',URL('User-Login/index'));$this->error (L('NO_READ'));}
		$fields = F($this->mod[$module].'_Field');
		foreach($fields as $key=>$r){
			$fields[$key]['setup'] =string2array($fields[$key]['setup']);
		}
		$this->assign ( 'fields', $fields); 

		if($module=='Guestbook'){
			$where['status']=array('eq',1);
			$this->dao= M($module);
			$count = $this->dao->where($where)->count();
			if($count){
				import ( "@.ORG.Page" );
				$listRows =  !empty($cat['pagesize']) ? $cat['pagesize'] : C('PAGE_LISTROWS');		
				$page = new Page ( $count, $listRows );
				$page->urlrule = geturl($cat,'');
				$pages = $page->show();
				$field =  $this->module[$cat['moduleid']]['listfields'];
				$field =  $field ? $field : '*';
				$list = $this->dao->field($field)->where($where)->order('listorder desc,id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
				$this->assign('pages',$pages);
				$this->assign('list',$list);
			}
			$template = $cat['module']=='Guestbook' && $cat['template_list'] ? $cat['template_list'] : 'Guestbook_index';

			$this->display($template);
		}elseif($module=='Feedback'){
			$template = $cat['module']=='Feedback' && $cat['template_list'] ? $cat['template_list'] : 'Feedback_index' ;
			$this->display($template);
		}elseif($module=='Page'){
			$module=M('Page');
			$data = $module->find($catid);
			
			$seo_title = $cat['title'] ? $cat['title'] : $data['title'];
			$this->assign ('seo_title',$seo_title);
			$this->assign ('seo_keywords',$data['keywords']);
			$this->assign ('seo_description',$data['description']);

			unset($data['id']);

			//分页
			$CONTENT_POS = strpos($data['content'], '[page]');
			if($CONTENT_POS !== false) {			
				$urlrule = geturl($cat,'',$this->Urlrule);
				$urlrule[0] =  urldecode($urlrule[0]);
				$urlrule[1] =  urldecode($urlrule[1]);
				$contents = array_filter(explode('[page]',$data['content']));
				$pagenumber = count($contents);
				for($i=1; $i<=$pagenumber; $i++) {
					$pageurls[$i] = str_replace('{$page}',$i,$urlrule);
				} 
				$pages = content_pages($pagenumber,$p, $pageurls);
				//判断[page]出现的位置
				if($CONTENT_POS<7) {
					$data['content'] = $contents[$p];
				} else {
					$data['content'] = $contents[$p-1];
				}
				$this->assign ('pages',$pages);	
			}

			$template = $cat['template_list'] ? $cat['template_list'] : $cat['module'] ;
			$this->assign ($data);		
			$this->display($template);

		}else{
			
			if($catid){
				$seo_title = $cat['title'] ? $cat['title'] : $cat['catname'];
				$this->assign ('seo_title',$seo_title);
				$this->assign ('seo_keywords',$cat['keywords']);
				$this->assign ('seo_description',$cat['description']);
				

				$where = " status=1 ";
				if($cat['child']){							
					$where .= " and catid in(".$cat['arrchildid'].")";			
				}else{
					$where .=  " and catid=".$catid;			
				}
				if(empty($cat['listtype'])){
					$this->dao= M($module);
					$count = $this->dao->where($where)->count();
					if($count){
						import ( "@.ORG.Page" );
						$listRows =  !empty($cat['pagesize']) ? $cat['pagesize'] : C('PAGE_LISTROWS');
						$page = new Page ( $count, $listRows );
						$page->urlrule = geturl($cat,'',$this->Urlrule);
						$pages = $page->show();
						$field =  $this->module[$this->mod[$module]]['listfields'];
						$field =  $field ? $field : 'id,catid,userid,url,username,title,title_style,keywords,description,thumb,createtime,hits';
						$list = $this->dao->field($field)->where($where)->order('listorder desc,id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
						$this->assign('pages',$pages);
						$this->assign('list',$list);
					}
					$template_r = '_list';
				}else{
					$template_r = '_index';
				}
			}else{
				$template_r = '_list';
			}
			$template = $cat['template_list'] ? $cat['template_list'] : $module.$template_r;
			$this->display($template);
		}
    }

 

	public function show($id='',$module='')
    {
		$this->Urlrule =F('Urlrule');
		$p= max(intval($_REQUEST[C('VAR_PAGE')]),1);		
		$id = $id ? $id : intval($_REQUEST['id']);
		$module = $module ? $module : MODULE_NAME;
		$this->assign('module_name',$module);
		$this->dao= M($module);;
		$data = $this->dao->find($id);
		
		
		$catid = $data['catid'];
		$cat = $this->categorys[$data['catid']];
		if(empty($cat['ishtml']))$this->dao->setInc('hits',"id=".$id); //添加点击次数
		$bcid = explode(",",$cat['arrparentid']); 
		$bcid = $bcid[1]; 
		if($bcid == '') $bcid=intval($catid);

		if($data['readgroup']){
			if($this->_groupid!=1 && !in_array($this->_groupid,explode(',',$data['readgroup'])) )$noread=1;
		}elseif($cat['readgroup']){
			if($this->_groupid!=1 && !in_array($this->_groupid,explode(',',$cat['readgroup'])) )$noread=1;
		}
		if($noread==1){$this->assign('jumpUrl',URL('User-Login/index'));$this->error (L('NO_READ'));}

		$chargepoint = $data['readpoint'] ? $data['readpoint'] : $cat['chargepoint']; 
		if($chargepoint && $data['userid'] !=$this->_userid){
			$user = M('User');
			$userdata =$user->find($this->_userid);
			if($cat['paytype']==1 && $userdata['point']>=$chargepoint){
				$chargepointok = $user->setDec('point',"id=".$this->_userid,$chargepoint);
			}elseif($cat['paytype']==2 && $userdata['amount']>=$chargepoint){
				$chargepointok = $user->setDec('amount',"id=".$this->_userid,$chargepoint);
			}else{
				$this->error (L('NO_READ'));
			}
		}
	
		$seo_title = $data['title'].'-'.$cat['catname'];
		$this->assign ('seo_title',$seo_title);
		$this->assign ('seo_keywords',$data['keywords']);
		$this->assign ('seo_description',$data['description']);
		$this->assign ( 'fields', F($cat['moduleid'].'_Field') ); 
		

		$fields = F($this->mod[$module].'_Field');
		foreach($data as $key=>$c_d){
			$setup='';
			$fields[$key]['setup'] =$setup=string2array($fields[$key]['setup']);
			if($setup['fieldtype']=='varchar' && $fields[$key]['type']!='text'){
				$data[$key.'_old_val'] =$data[$key];
				$data[$key]=fieldoption($fields[$key],$data[$key]);
			}elseif($fields[$key]['type']=='images' || $fields[$key]['type']=='files'){ 
				if(!empty($data[$key])){
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
			}
			unset($setup);
		}
		$this->assign('fields',$fields); 


		//手动分页
		$CONTENT_POS = strpos($data['content'], '[page]');
		if($CONTENT_POS !== false) {
			
			$urlrule = geturl($cat,$data,$this->Urlrule);
			$urlrule =  str_replace('%7B%24page%7D','{$page}',$urlrule); 
			$contents = array_filter(explode('[page]',$data['content']));
			$pagenumber = count($contents);
			for($i=1; $i<=$pagenumber; $i++) {
				$pageurls[$i] = str_replace('{$page}',$i,$urlrule);
			} 
			$pages = content_pages($pagenumber,$p, $pageurls);
			//判断[page]出现的位置是否在文章开始
			if($CONTENT_POS<7) {
				$data['content'] = $contents[$p];
			} else {
				$data['content'] = $contents[$p-1];
			}
			$this->assign ('pages',$pages);	
		}

		if(!empty($data['template'])){
			$template = $data['template'];
		}elseif(!empty($cat['template_show'])){
			$template = $cat['template_show'];
		}else{
			$template = $cat['module'].'_show';
		}

		$this->assign('catid',$catid);
		$this->assign ($cat);
		$this->assign('bcid',$bcid);

		$this->assign ($data);
        $this->display($template);	 
    }

	public function down()
	{

		$module = $module ? $module : MODULE_NAME;
		$id = $id ? $id : intval($_REQUEST['id']);
		$this->dao= M($module);
		$filepath = $this->dao->getField('file',"id=".$id);
		$this->dao->setInc('downs',"id=".$id);

		if(strpos($filepath, ':/')) { 
			header("Location: $filepath");
		} else {			
			if(!$filename) $filename = basename($filepath);
			$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
			if(strpos($useragent, 'msie ') !== false) $filename = rawurlencode($filename);
			$filetype = strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
			$filesize = sprintf("%u", filesize($filepath));
			if(ob_get_length() !== false) @ob_end_clean();
			header('Pragma: public');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: pre-check=0, post-check=0, max-age=0');
			header('Content-Transfer-Encoding: binary');
			header('Content-Encoding: none');
			header('Content-type: '.$filetype);
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Content-length: '.$filesize);
			readfile($filepath);
		}
		exit;
	}

	public function hits()
	{
		$module = $module ? $module : MODULE_NAME;
		$id = $id ? $id : intval($_REQUEST['id']);
		$this->dao= M($module);
		$this->dao->setInc('hits',"id=".$id);

		if($module=='Download'){
			$r = $this->dao->find($id);
			echo '$("#hits").html('.$r['hits'].');$("#downs").html('.$r['downs'].');';
		}else{
			$hits = $this->dao->getField('hits',"id=".$id);
			echo '$("#hits").html('.$hits.');';
		}
		exit;
	}
	public function verify()
    {
        $type	 =	 isset($_GET['type'])?$_GET['type']:'gif';
        import("@.ORG.Image");
        Image::buildImageVerify(4,1,$type);
    }
}
?>