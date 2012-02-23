<?php
class UserModel extends Model {
	/*
	 * 表单验证
	 */
	protected  $_validate = array(	
		array('username','require','{%user_is_empty}',1,'regex',1),
		array('username','','{%user_is_have}',1,'unique',1),
		array('pwd','require','{%user_password_empty}',0,'regex',1),		
		array('email','require','{%email_is_empty}',1,'regex',3),
		array('email','email','{%email_is_not}'),
		array('email','checkEmail','{%email_is_have}',1,'callback',3),
	);
	
	/*
	 * 字段映射
	 */
	protected $_map=array(
		'pwd'=>'password',
	);
	
	/*
	 * 自动完成
	 */
	protected $_auto=array(	
		array('password','sysmd5',1,'function'),
		array('createtime','time',1,'function'),
		array('updatetime','time',2,'function'),
		array('reg_ip','get_client_ip',1,'function'),
	);

	function checkEmail(){
		$user=M('User');
		if(empty($_POST['id'])){
			if($user->getByEmail($_POST['email'])){
				return false;
			}else{
				return true;
			}
		}else{
			//判断邮箱是否已经使用
			if($user->where("id!={$_POST['id']} and email='{$_POST['email']}'")->find()){
				return false;
			}else{
				return true;
			}
		}
	}
}
?>