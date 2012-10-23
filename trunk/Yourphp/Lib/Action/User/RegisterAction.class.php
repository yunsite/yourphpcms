<?php
/**
 * RegisterAction.class.php (前台会员注册模块)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class RegisterAction extends BaseAction
{
	
	function _initialize()
    {
		parent::_initialize();
		$this->dao = M('User');
		$_GET =get_safe_replace($_GET);
		if(empty($this->member_config['member_register'])) $this->error(L('close_reg'));
    }
    public function index()
    {
		if( cookie('auth')){
			$this->assign('forward','');
			$this->assign('jumpUrl','/');
			$this->success(L('login_ok'));
		}
		$this->assign('bcid',0);
        $this->display();
    }

	public function doreg()
	{
		$username = get_safe_replace($_POST['username']);
        $password = get_safe_replace($_POST['password']);
		$email = get_safe_replace($_POST['email']);
        $verifyCode =$_POST['verifyCode'];
        if(empty($username) || empty($password) || empty($email)){
           $this->error(L('empty_username_empty_password_empty_email'));
        }
		if($this->member_config['member_login_verify'] && md5($verifyCode) != $_SESSION['verify']){
           $this->error(L('error_verify'));
        }
		$status = $this->member_config['member_registecheck'] ? 0 : 1;
		if($this->member_config['member_emailcheck']){ $status = 1; $groupid=5 ;}
		$groupid = $groupid ? $groupid : 3;
		$data=array();
		$data['username']= $username;
		$data['email']=$email;
		$data['groupid']=$groupid;
		$data['login_count']=1;
		$data['createtime']=time();
		$data['updatetime']=time();
		$data['last_logintime']=time();
		$data['reg_ip']=get_client_ip();
		$data['status']=$status;
		$authInfo['password'] = $data['password'] = sysmd5($password);
		 
		if($r=$this->dao->create($data)){
			if(false!==$this->dao->add()){
				$authInfo['id'] = $uid=$this->dao->getLastInsID();
				$authInfo['groupid'] = $ru['role_id']=$data['groupid'];
				$ru['user_id']=$uid;
				$roleuser=M('RoleUser');
				$roleuser->add($ru);

				if($this->member_config['member_emailcheck']){
					$yourphp_auth = authcode($uid."-".$username."-".$email, 'ENCODE',$this->sysConfig['ADMIN_ACCESS'],3600*24*3);//3天有效期
					$url = 'http://'.$_SERVER['HTTP_HOST'].U('User/Login/regcheckemail?code='.$yourphp_auth);
					$click = "<a href=\"$url\" target=\"_blank\">".L('CLICK_THIS')."</a>";
					$message = str_replace(array('{click}','{url}','{sitename}'),array($click,$url,$this->Config['site_name']),$this->member_config['member_emailchecktpl']);
					$r = sendmail($email,L('USER_REGISTER_CHECKEMAIL').'-'.$this->Config['site_name'],$message,$this->Config);
					$this->assign('send_ok',1);
					$this->assign('username',$username);
					$this->assign('email',$email);
					$this->display('Login:emailcheck');
					exit;
				}
				
				$yourphp_auth_key = sysmd5($this->sysConfig['ADMIN_ACCESS'].$_SERVER['HTTP_USER_AGENT']);
				$yourphp_auth = authcode($authInfo['id']."-".$authInfo['groupid']."-".$authInfo['password'], 'ENCODE', $yourphp_auth_key);
				

				$authInfo['username'] = $data['username'];
				$authInfo['email'] = $data['email'];
				cookie('auth',$yourphp_auth,$cookietime);
				cookie('username',$authInfo['username'],$cookietime);
				cookie('groupid',$authInfo['groupid'],$cookietime);
				cookie('userid',$authInfo['id'],$cookietime);
				cookie('email',$authInfo['email'],$cookietime);

				$this->assign('jumpUrl',$this->forward);
				$this->success(L('reg_ok'));
			}else{
				$this->error(L('reg_error'));
			}
		}else{
			$this->error($this->dao->getError());
		}
 
	}


	function checkEmail(){
        $email=$_GET['email'];
		$userid=intval($_GET['userid']);
		if(empty($userid)){
			if($this->dao->getByEmail($email)){
				 echo 'false';
			}else{
				echo 'true';
			}
		}else{
			//判断邮箱是否已经使用
			if($this->dao->where("id!={$userid} and email='{$email}'")->find()){
				 echo 'false';
			}else{
				echo 'true';
			}
		}
        exit;
	}

	function checkusername(){
		$username=$_GET['username'];
		if($this->dao->getByUsername($username)){
				 echo 'false';
			}else{
				echo 'true';
		}
		exit;
	}
}
?>