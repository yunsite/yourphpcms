<?php
/**
 *
 * Login(后台登陆页面)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <web@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class LoginAction extends Action{
    private $adminid ,$groupid ,$sysConfig ,$cache_model,$Config,$menudata ;
    function _initialize()
    {
		$this->sysConfig = F('sys.config');
		C('ADMIN_ACCESS',$this->sysConfig['ADMIN_ACCESS']);

		import('@.TagLib.TagLibYP');
        $this->adminid = $_SESSION['adminid'];
        $this->groupid = $_SESSION['groupid'];
    }
    /**
     * 登录页
     *
     */
    public function index()
    {
		if(is_file(RUNTIME_FILE))@unlink(RUNTIME_FILE);
		$this->menudata = F('Menu');
		$this->cache_model=array('Lang','Menu','Config','Module','Role','Category','Posid','Field','Type','Urlrule','Dbsource');
		if(empty($this->sysConfig['ADMIN_ACCESS']) || empty($this->menudata)){
			foreach($this->cache_model as $r){
				savecache($r);
			}
			$this->sysConfig = F('sys.config');
			C('ADMIN_ACCESS',$this->sysConfig['ADMIN_ACCESS']);
		}
		if($this->_adminid){
			$this->assign('jumpUrl',U('Index/index'));
			$this->success(L('logined'));
		}
		$this->assign ( 'admin_verify', $this->sysConfig['ADMIN_VERIFY'] );
        $this->display();
    }

    /**
     * 提交登录
     *
     */
    public function doLogin()
    {

		$dao = M('User');  
		$ip =get_client_ip();

		if(empty($this->sysConfig['ADMIN_ACCESS'])) $this->error(L('NO SYSTEM CONFIG FILE'));
		$username = get_safe_replace(trim($_POST['username']));
        $password = get_safe_replace(trim($_POST['password']));
        $verifyCode = trim($_POST['verifyCode']);

        if(empty($username) || empty($password)){
           $this->error(L('empty_username_empty_password'));
        }elseif($_SESSION['verify'] && $this->sysConfig['ADMIN_VERIFY'] &&  md5($verifyCode) != $_SESSION['verify']){
           $this->error(L('error_verify'));
        }

		$time =time();
		$logwhere=array();
		$logwhere['time']=array('EGT',$time-1800);
		$logwhere['ip']=array('eq',$ip);
		$logwhere['error'] =1;
		$lognum= M('Log')->where($logwhere)->count();
		if($lognum>=5)$this->error(L('Login_error_count'));

        $condition = array();
        $condition['username'] =array('eq',$username);

		import ( '@.ORG.RBAC' );
        $authInfo = RBAC::authenticate($condition);
        //使用用户名、密码和状态的方式进行认证
        if(false === $authInfo) {
			$data=array();
			$data['username']=$username;
			$data['ip']=$ip;
			$data['time']=$time;
			$data['note']=L('empty_userid');
			$data['error'] =1;
			M('Log')->add($data);
            $this->error(L('empty_userid'));
        }else {
            if($authInfo['password'] != sysmd5($password)) {
				$data=array();
				$data['username']=$username;
				$data['ip']=$ip;
				$data['time']=$time;
				$data['note']=L('password_error').':'.$password;
				$data['error'] =1;
				M('Log')->add($data);
            	$this->error(L('password_error'));
            }

			$_SESSION['username'] = $authInfo['username'];
			$_SESSION['adminid'] = $_SESSION['userid'] = $authInfo['id'];
			$_SESSION['groupid'] = $authInfo['groupid'];
			$_SESSION['adminaccess'] = C('ADMIN_ACCESS');
            $_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];
            $_SESSION['email']	=	$authInfo['email'];
            $_SESSION['lastLoginTime']		=	$authInfo['last_logintime'];
			$_SESSION['login_count']	=	$authInfo['login_count']+1;

            if($authInfo['groupid']==1) {
				$_SESSION[C('ADMIN_AUTH_KEY')]=true;
            }

            //保存登录信息
			
			$data = array();
			$data['id']	=	$authInfo['id'];
			$data['last_logintime']	=	$time;
			$data['last_ip']	=	 get_client_ip();
			$data['login_count']	=	array('exp','login_count+1');
			$dao->save($data);

           // 缓存访问权限
            RBAC::saveAccessList();

				$data=array();
				$data['username']=$username;
				$data['ip']=$ip;
				$data['time']=$time;
				$data['note']=L('login_ok');
				M('Log')->add($data);

			if($_POST['ajax']){
				$this->ajaxReturn($authInfo,L('login_ok'),1);
			}else{
				$this->assign('jumpUrl',U('Index/index'));
				$this->success(L('login_ok'));
			}
		}

    }


    /**
     * 退出登录
     *
     */
    public function logout()
    {
		if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			unset($_SESSION[C('USER_AUTH_KEY')]);
			unset($_SESSION);
			session_destroy();
            $this->assign('jumpUrl',U('Login/index'));
			$this->success(L('loginouted'));
        }else {
			$this->assign('jumpUrl',U('Login/index'));
            $this->error(L('logined'));
        }
    }

    function checkEmail(){
		$user=M('User');

        $email=$_GET['email'];
		$userid=intval($_GET['userid']);
		if(empty($userid)){
			if($user->getByEmail($email)){
				 echo 'false';
			}else{
				echo 'true';
			}
		}else{
			//判断邮箱是否已经使用
			if($user->where("id!={$userid} and email='{$email}'")->find()){
				 echo 'false';
			}else{
				echo 'true';
			}
		}
        exit;
	}
}
