<?php 
class Online extends Think {

   protected $lifeTime='1800' ,$sessionid='' ,$dao;


   public function __construct(&$params='') {
		$this->lifeTime = C('EXPIRE_TIME') ?  C('EXPIRE_TIME') : 1800;

		if($_COOKIE['YP_onlineid']){
			$this->sessionid = $_COOKIE['YP_onlineid'];
		}else{
			$this->sessionid = substr(MD5(session_id()), 0, 32);
			cookie('onlineid',$this->sessionid,0);
		}
		$this->dao = M('Online');
        //session_set_save_handler(array(&$this,'open'), array(&$this,'close'), array(&$this,'read'), array(&$this,'write'), array(&$this,'destroy'), array(&$this,'gc'));
		
		$this->write($this->sessionid);
		$this->gc($this->lifeTime);
    }

    public function open($savePath, $sessName) {
       return true; 
    } 

   public function close() { 
	   return $this->gc($this->lifetime);
   } 

   public function read($sessID) { 
	   $r = $this->dao->find($sessID);
		return $r ? $r['data'] : '';
   } 

   public function write($sessID,$sessData) {
		$ip = get_client_ip();
		$username = $_COOKIE['YP_username'] ? $_COOKIE['YP_username'] : '';
		$groupid = $_COOKIE['YP_groupid'] ? intval($_COOKIE['YP_groupid']) : 4;
		$sessiondata = array(
							'sessionid'=>$sessID,
							'userid'=>intval($_COOKIE['YP_userid']),
							'username'=>$username,
							'ip'=>$ip,
							'lastvisit'=>time(),
							'groupid'=> $groupid,
							'data'=> '',
		);
		return $this->dao->add($sessiondata,'',true);
   } 

 
   public function destroy($sessID) { 
	   return $this->dao->delete($this->sessionid);
   } 

   public function gc($sessMaxLifeTime) { 
	   $expiretime = time() -$sessMaxLifeTime; 
		$r =  $this->dao->where(" lastvisit < $expiretime")->delete();
		return $r;
   } 

}