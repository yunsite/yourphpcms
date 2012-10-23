<?php

/**
 * 
 * IndexAction.class.php(后台首页)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class IndexAction extends AdminbaseAction
{
	protected   $cache_model;
	function _initialize()
    {
		parent::_initialize();
		unset($_POST['status']);
		unset($_POST['groupid']);
		unset($_POST['amount']);
		unset($_POST['point']);
    }

    public function index()
    {
		$role	=	F("Role");
		$this->assign('usergroup',$role[$_SESSION['groupid']]['name']); 
 

		foreach((array)$_SESSION['_ACCESS_LIST']['ADMIN'] as $key=>$r){$modules[]=ucwords(strtolower($key));}
		$modules=implode("','",$modules);
		$alltopnode= M('Node')->field('groupid')->where("name in('$modules') and level=2")->group('groupid')->select();
		foreach((array)$alltopnode as $key=>$r){$GroupAccessids[]=$r['groupid'];}	 

		foreach($this->menudata as $key=>$module) {
			if($module['parentid'] != 0 || $module['status']==0) continue;		
			if(in_array($key,$GroupAccessids) || $_SESSION[C('ADMIN_AUTH_KEY')]) {
				if(empty($module['action'])) $module['action']='index';		
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
		$this->assign('menuGroupList',$nav); 
		$this->assign($this->Config); 
		foreach($nav as $key=>$r){
			$menu[$r['id']]  = $this->getnav($r['id']);
		}
		$this->assign('menu',$menu);
		$this->display();
    }

	public function cache() {
		dir_delete(RUNTIME_PATH.'Html/');
		dir_delete(RUNTIME_PATH.'Cache/');
		if(is_file(RUNTIME_PATH.'~runtime.php'))@unlink(RUNTIME_PATH.'~runtime.php');
		if(is_file(RUNTIME_PATH.'~allinone.php'))@unlink(RUNTIME_PATH.'~allinone.php');	
		R('Admin/Category/repair');
		R('Admin/Category/repair');

		foreach($this->cache_model as $r){			
			savecache($r);
		}
		$forward = $_GET['forward'] ?   $_GET['forward']  : U('Index/main');
		$this->assign ( 'jumpUrl', $forward );
		$this->success(L('do_success'));
	}

	public function main() {
		
		$db=D('');
		$db =   DB::getInstance();
		$tables = $db->getTables();
		
		$info = array(
           
            'SERVER_SOFTWARE'=>PHP_OS.' '.$_SERVER["SERVER_SOFTWARE"],
            'mysql_get_server_info'=>php_sapi_name(),
			'MYSQL_VERSION' => mysql_get_server_info(),
            'upload_max_filesize'=> ini_get('upload_max_filesize'),
            'max_execution_time'=>ini_get('max_execution_time').L('miao'),
			'disk_free_space'=>round((@disk_free_space(".")/(1024*1024)),2).'M',
            );
		$yourphp_info=array(
			'yourphp_VERSION'=> VERSION.' '.UPDATETIME.' [ <a href="http://www.yourphp.cn" target="_blank">'.L('view_new_VERSION').'</a> ]',			
			'license'=> '<b id="Yourphp_license"></b>',
			'SN'=> '<b id="Yourphp_sn"></b>',
			'update'=>  ' <b id="Yourphp_update"></b>',
			
		);
		$this->assign('yourphp_info',$yourphp_info);
        $this->assign('server_info',$info);		
		foreach ((array)$this->module as $rw){
			if($rw['type']==1){  
				$molule= M($rw['name']);
				$rw['counts'] = $molule->count();;
				$mdata['moduledata'][] = $rw;
			}
        }

		$molule= M('User');
		$counts = $molule->count(); 
		$userinfos = $molule->find($_SESSION['adminid']);
		$mdata['moduledata'][]=array('title'=>L('user_counts'),'counts'=>$counts);
		
		$molule= M('Category');$counts = $molule->count(); 
		$mdata['moduledata'][]=array('title'=>L('Category_counts'),'counts'=>$counts);
		$this->assign($mdata);
		$role =F('Role');
		
		$userinfo=array(
			'username'=>$userinfos['username'],	
			'groupname'=>$role[$userinfos['groupid']]['name'],
			'logintime'=>toDate($userinfos['last_logintime']),			
			'last_ip'=>$userinfos['last_ip'],	
			'login_count'=>$userinfos['login_count'].L('ci'),	
		);
		$this->assign('userinfo',$userinfo);

        $this->display();
    }

 
    // 更换密码
    public function password(){
		if($_POST['dosubmit']){
			if(md5($_POST['verify'])	!= $_SESSION['verify']) {
				$this->error(L('error_verify'));
			}
			if($_POST['password'] != $_POST['repassword']){
				$this->error(L('password_repassword'));
			}
			$map	=	array();
			$map['password']= sysmd5($_POST['oldpassword']);
			if(isset($_POST['username'])) {
				$map['username']	 =	 $_POST['username'];
			}elseif(isset($_SESSION['adminid'])) {
				$map['id']		=	$_SESSION['adminid'];
			}
			//检查用户
			$User    =   M("user");
			if(!$User->where($map)->field('id')->find()) {
				$this->error(L('error_oldpassword'));
			}else {
				$User->updatetime = time();
				$User->password	=	sysmd5($_POST['password']);
				$User->save();
				$this->success(L('do_success'));
			 }
		}else{
			 $this->display();
		}
    }

	// 修改资料
	public function profile() {
		if($_REQUEST['dosubmit']){
			$User	 =	M("User");
			if(!$User->create()) {
				$this->error($User->getError());
			}
			$User->update_time = time();
			$User->last_ip = get_client_ip();
			$result	=	$User->save();
			if(false !== $result) {
				$this->success(L('do_success'));
			}else{
				$this->error(L('do_error'));
			}
		}else{
			$User	 =	 M("user");
			$vo	=	$User->getById($_SESSION['adminid']);
			$this->assign('vo',$vo);
			$this->display();
		}
	}

}
?>