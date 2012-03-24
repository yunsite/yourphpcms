<?php
/**
 * 
 * Attachment(附件管理)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
if(defined('APP_NAME')!='Yourphp' && !defined("YOURPHP"))  exit("Access Denied");
class AttachmentAction extends  Action {

	protected $lang,$dao,$Config,$sysConfig,$isadmin=0,$userid=0,$groupid=0;
    function _initialize()
    {	
		$this->isadmin= $_REQUEST['isadmin'] ? $_REQUEST['isadmin'] : 0;
		$this->sysConfig = F('sys.config');
		if(APP_LANG){
			$this->Lang = F('Lang');
			$this->assign('Lang',$this->Lang);
			if($_GET['l']){
				if(!$this->Lang[$_GET['l']]['status'])$this->error ( L ( 'NO_LANG' ) );
				$lang=$_GET['l'];
			}else{
				$lang=$this->sysConfig['DEFAULT_LANG'];
			}
			define('LANG_NAME', $lang);
			define('LANG_ID', $this->Lang[$lang]['id']);

			$this->Config = F('Config_'.LANG_NAME);			
		}else{
			$this->Config = F('Config');		
		} 
		
		if($_POST['PHPSESSID'] && $_POST['swf_auth_key'] && $_POST['userid']){
			if($_POST['swf_auth_key']==sysmd5($_POST['PHPSESSID'].$_POST['userid'],$this->sysConfig['ADMIN_ACCESS'])){
				$this->userid = $_POST['userid'];
				if(APP_LANG) $this->Config = F('Config_'.$_POST['lang']);
			}
		}		
		if(!$this->userid){
			if($this->isadmin){
				import('@.Action.Adminbase');
				$Adminbase=new AdminbaseAction();
				$Adminbase->_initialize();
				$this->userid=  $_SESSION[C('USER_AUTH_KEY')];
				$this->groupid=  $_SESSION['groupid'];
			}else{
				C('ADMIN_ACCESS',$this->sysConfig['ADMIN_ACCESS']);
				if($_COOKIE['YP_auth']){
					if(!strstr($_SERVER['HTTP_USER_AGENT'],'Flash'))cookie('YP_cookie',$_SERVER['HTTP_USER_AGENT']);
					$HTTP_USER_AGENT = strstr($_SERVER['HTTP_USER_AGENT'],'Flash') ? $_COOKIE['YP_cookie'] : $_SERVER['HTTP_USER_AGENT'];
					$yourphp_auth_key = sysmd5($this->sysConfig['ADMIN_ACCESS'].$HTTP_USER_AGENT);
					list($userid, $groupid ,$password) = explode("-", authcode($_COOKIE['YP_auth'], 'DECODE', $yourphp_auth_key));
					$this->userid = $userid;
					$this->groupid = $groupid; 
				}
				if(!$this->userid){
					$this->assign('jumpUrl',U('User/Login/index'));
					$this->error(L('no_login'));
				}
			}
		}
		$this->assign($this->Config);

		$this->dao=M('Attachment');
    }
	public function index(){

		$auth = str_replace(' ','+',$_REQUEST['auth']) ;
 
		$postd=array('isadmin','more','isthumb','file_limit','file_types','file_size','moduleid');
		foreach((array)$_REQUEST as $key=>$res){
			if(in_array($key,$postd))$postdata[$key]=$res;
		}
		$upsetup = implode('-',$postdata);
		$yourphp_auth_key = sysmd5(C('ADMIN_ACCESS').$_SERVER['HTTP_USER_AGENT']);
		$enupsetup = authcode($auth, 'DECODE', $yourphp_auth_key);
		if(!$enupsetup || $upsetup!=$enupsetup)  $this->error (L('do_empty'));

		$sessid = time();

		$count = $this->dao->where('status=0 and userid ='.$this->userid)->count();
		$this->assign('no_use_files',$count);
		$this->assign('small_upfile_limit',$_REQUEST['file_limit'] - $count);


		$types = '*.'.str_replace(",",";*.",$_REQUEST['file_types']); ;
		$this->assign('moduleid',$_REQUEST['moduleid']);
		$this->assign('file_size',$_REQUEST['file_size']);
		$this->assign('file_limit',$_REQUEST['file_limit']);
		$this->assign('file_types',$types);
		$this->assign('isthumb',$_REQUEST['isthumb']);
		$this->assign('isadmin',$this->isadmin);
		$this->assign('sessid',$sessid);
		$this->assign('lang',LANG_NAME);
		$this->assign('userid',$this->userid);
		$swf_auth_key = sysmd5($sessid.$this->userid);
 
		$this->assign('swf_auth_key',$swf_auth_key);
		$this->assign('more',$_GET['more']);		
		$this->display();
	}

	public function upload(){
		//if($_POST['swf_auth_key']!= sysmd5($_POST['PHPSESSID'].$this->userid)) $this->ajaxReturn(0,'1-'.$_POST['PHPSESSID'],0);
 
		import("@.ORG.UploadFile"); 
        $upload = new UploadFile(); 
		//$upload->supportMulti = false;
        //设置上传文件大小 
        $upload->maxSize = $this->Config['attach_maxsize']; 
		$upload->autoSub = true; 
		$upload->subType = 'date';
		$upload->dateFormat = 'Ym';
        //设置上传文件类型 
        $upload->allowExts = explode(',', $this->Config['attach_allowext']); 
        //设置附件上传目录 
        $upload->savePath = UPLOAD_PATH; 
		 //设置上传文件规则 
        $upload->saveRule = uniqid; 


        //删除原图 
        $upload->thumbRemoveOrigin = true; 
        if (!$upload->upload()) { 
			$this->ajaxReturn(0,$upload->getErrorMsg(),0);
        } else { 
            //取得成功上传的文件信息 
            $uploadList = $upload->getUploadFileInfo(); 
			
			
			if($_REQUEST['addwater']){ //$this->Config['watermark_enable']  $_REQUEST['addwater']
				import("@.ORG.Image");  
				Image::watermark($uploadList[0]['savepath'].$uploadList[0]['savename'],'',$this->Config);
			}
			
			$imagearr = explode(',', 'jpg,gif,png,jpeg,bmp,ttf,tif'); 
			$data=array();
			$model = M('Attachment');
			//保存当前数据对象
			$data['moduleid'] = $_REQUEST['moduleid'];
			$data['catid'] = 0;
			$data['userid'] = $_REQUEST['userid'];
			$data['filename'] = $uploadList[0]['name'];
			$data['filepath'] = substr($uploadList[0]['savepath'].$uploadList[0]['savename'],1);
			$data['filesize'] = $uploadList[0]['size']; 
			$data['fileext'] = $uploadList[0]['extension']; 
			$data['isimage'] = in_array($uploadList[0]['extension'],$imagearr) ? 1 : 0;
			$data['isthumb'] = intval($_REQUEST['isthumb']);
			$data['createtime'] = time();
			$data['uploadip'] = get_client_ip();
			$aid = $model->add($data); 
			$returndata['aid']		= $aid;
			$returndata['filepath'] = $data['filepath'];
			$returndata['fileext']  = $data['fileext'];
			$returndata['isimage']  = $data['isimage'];
			$returndata['filename'] = $data['filename'];
			$returndata['filesize'] = $data['filesize']; 

			$this->ajaxReturn($returndata,L('upload_ok'), '1');
        }	
	}

	public function filelist(){

		$where= $_REQUEST['typeid'] ?  " status=1 " : " status=0 ";
		$where .=" and userid = ".$this->userid ;
		import ( '@.ORG.Page' );
		$count = $this->dao->where($where)->count();
		$page=new Page($count,12); 
		$imagearr = explode(',', 'jpg,gif,png,jpeg,bmp,ttf,tif'); 

		$page->urlrule = 'javascript:ajaxload('.$_REQUEST['typeid'].',{$page},\''.$_REQUEST['inputid'].'\','.$this->isadmin.');';
		$show = $page->show(); 
		$this->assign("page",$show);
		$list=$this->dao->order('aid desc')->where($where)
		->limit($page->firstRow.','.$page->listRows)->select();
		foreach((array)$list as $key=>$r){
		$list[$key]['thumb']=in_array($r['fileext'],$imagearr) ? $r['filepath'] : __ROOT__.'/Public/Images/ext/'.$r['fileext'].'.png'; 
		}
		$this->assign('list',$list);
		$this->display();
	}

	function delfile($aid){
		if(empty($aid)){
		$aid=$_REQUEST['aid'];
		}
		$r = delattach(array('aid'=>$aid,'userid'=>$this->userid));
		if($r){		 
			$this->success ( L ( 'delete_ok' ) );
		}else{
			$this->error ( L ( 'delete_error' ) );
		}
	
	}
	function cleanfile(){

		$r = delattach(array('status'=>0,'userid'=>$this->userid));
		if($r){		 
			$this->success ( L ( 'delete_ok' ) );
		}else{
			$this->error ( L ( 'delete_error' ) );
		}
	}
	
}
?>