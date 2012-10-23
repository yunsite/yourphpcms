<?php
/**
 * 
 * Template(模板管理)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class TemplateAction extends AdminbaseAction
{
	protected $filepath,$publicpath;
    function _initialize()
    {
		parent::_initialize();
		$this->filepath = TMPL_PATH.'Home/'.$this->sysConfig['DEFAULT_THEME'].'/'; 
		$this->publicpath = TMPL_PATH.'Home/'.$this->sysConfig['DEFAULT_THEME'].'/Public/';
    }

    public function index()
    {
		$exts = array('html','css','js');
		$type=  $_GET['type'] ? $_GET['type'] : 'html';
		if($type=='html'){
			$path=$this->filepath;
		}else{
			$path=$this->publicpath.$type.'/';
		}

		$files = dir_list($path,$type);

		foreach ($files as $key=>$file){
			$filename = basename($file);
			$templates[$key]['value'] =  substr($filename,0,strrpos($filename, '.'));
			$templates[$key]['filename'] = $filename;
			$templates[$key]['filepath'] = $file;
			$templates[$key]['filesize']=byte_format(filesize($file));
			$templates[$key]['filemtime']=filemtime($file);
			$templates[$key]['ext'] = strtolower(substr($filename,strrpos($filename, '.')-strlen($filename)));
		}

		$this->assign ( 'templates',$templates );
        $this->display ();
    }

	public function images(){
		$path = $this->publicpath.'images/'.$_GET['folder'];

		
		$this->assign ( 'Public',$this->publicpath);
		
		$uppath = explode('/',$_GET['folder']);
		$leve = count($uppath)-1;;unset($uppath[$leve]);
		if($leve>1){
			unset($uppath[$leve-1]);
			$uppath = implode('/',$uppath).'/';
		}else{
			$uppath = '';
		}

		$this->assign ( 'leve',$leve);
		$this->assign ( 'uppath',$uppath);

		if($_GET['delete']){
			$file=$path.$_GET['filename'];
			if(file_exists($file)){
				is_dir($file) ? dir_delete($file) : unlink($file);
				$this->success(L('delete_ok'));
			}else{
				$this->error(L('file_no_find'));
			}
		}

		$files = glob($path.'*');
		$folders=array();
		foreach($files as $key => $file) {
			$filename = basename($file);
			if(is_dir($file)){
				$folders[$key]['filename'] = $filename;
				$folders[$key]['filepath'] = $file;
				$folders[$key]['ext'] = 'folder';
			}else{
				$templates[$key]['filename'] = $filename;
				$templates[$key]['filepath'] = $file;
				$templates[$key]['ext'] = strtolower(substr($filename,strrpos($filename, '.')-strlen($filename)+1));
				if(!in_array($templates[$key]['ext'],array('gif','jpg','png','bmp'))) $templates[$key]['ico'] =1;
			}
		}
		$this->assign ( 'path',$path);
		$this->assign ( 'folders',$folders );
		$this->assign ( 'files',$templates );
		$this->display ();

	}
	public function _before_add()
    {
		if (!is_writable($this->filepath)) $this->error(L('file_no_find'));
	}

 	public function edit(){
		$exts = array('html','css','js');

		$filename = $_REQUEST['file'];
		if($_POST['type']){
			$type = $_POST['type'];
		}else{
			$type = strtolower(substr($filename,strrpos($filename, '.')-strlen($filename)+1));
		}
		$path = $type=='html' ?  $this->filepath : $this->publicpath.$type.'/';
 		$file = $path.$filename;
		if($_REQUEST['dosubmit']){

			if(C('TOKEN_ON') && !M()->autoCheckToken($_POST))$this->error (L('_TOKEN_ERROR_'));

			if($_POST['type']){
 				$file = $path.$filename.'.'.$type;
				file_put_contents($file,htmlspecialchars_decode(stripslashes($_POST['content'])));
				$this->assign('jumpUrl',U($module_name.'/index?type='.$type));
				$this->success(L('add_ok'));
			}else{
				if(file_exists($file)){
					file_put_contents($file,htmlspecialchars_decode(stripslashes($_POST['content'])));
					$this->success(L('edit_ok'));
				}else{
					$this->error(L('file_no_find'));
				}
			}
		}else{
			if(file_exists($file)){
				$content = htmlspecialchars(file_get_contents($file));
				$this->assign ( 'filename',$filename );
				$this->assign ( 'file',$file );
				$this->display ();
				echo '<textarea id="contentbox" style="display:none;" >'.$content.'</textarea><script>$("#content").val($("#contentbox").val());</script>';
			}else{
				$this->error(L('file_no_find'));
			}
		}
	}

	public function delete(){

		$exts = array('html','css','js');
		$filename = $_REQUEST['file'];
		$type = strtolower(substr($filename,strrpos($filename, '.')-strlen($filename)+1));
		$path = $type=='html' ? $path=$this->filepath : $this->publicpath.$type.'/';
 		$file = $path.$filename;

		if(file_exists($file)){
			unlink($file);
            $this->assign('jumpUrl',U($module_name.'/index?type='.$type));
			$this->success(L('delete_ok'));
		}else{
            $this->assign('jumpUrl',U($module_name.'/index?type='.$type));
			$this->error(L('file_no_find'));
		}
	}
	public function config(){

		$lang= APP_LANG ? LANG_NAME : $this->sysConfig['DEFAULT_LANG'];
		if($_GET['isajax']){
			if(empty($_POST['value'])){ echo '0';exit;}

			$data = F('config_'.$lang, $value='', $this->filepath); 
			$data[$_POST['key']]=$_POST['value'];
			$r = F('config_'.$lang, $data, $this->filepath);
			echo $r ? 1 : 0;
			exit;
		}
		if($_POST['dosubmit']){
			$file=  $_REQUEST['file'];
			unset($_POST[C('TOKEN_NAME')]);
			unset($_POST['dosubmit']);
				// strtoupper
			foreach($_POST as $key=>$r){
				if($r)$data[strtolower($key)]=$r;
			}
			$r = F('config_'.$lang, $data, $this->filepath);
			if($r){
				$this->success(L('do_ok'));
			}else{
				$this->error(L('add_error'));
			 }
			
		}else{
			$data = F('config_'.$lang, $value='', $this->filepath); 
			$this->assign ( 'list', $data );
		}



		$this->display ();
	}
}
?>