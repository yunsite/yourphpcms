<?php
/**
 * 
 * Theme(风格选择)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
 if(!defined("Yourphp")) exit("Access Denied");
class ThemeAction extends AdminbaseAction
{
	 protected $filepath,$publicpath;
    function _initialize()
    {	
		parent::_initialize();
		$this->filepath = TMPL_PATH.'Home/'.$this->sysConfig['DEFAULT_THEME'].'/'; 
		$this->publicpath = TMPL_PATH.'Home/'.$this->sysConfig['DEFAULT_THEME'].'/Public/';
		$this->tplpath = TMPL_PATH.'Home/';
    }

    public function index()
    {
		$filed = glob($this->tplpath.'*');
		foreach ($filed as $key=>$v) {
			$arr[$key]['name'] =  basename($v);
			if(is_file($this->tplpath.$arr[$key]['name'].'/preview.jpg')){
				$arr[$key]['preview']=$this->tplpath.$arr[$key]['name'].'/preview.jpg';
			}else{
				$arr[$key]['preview']=__ROOT__.'/Public/Images/nopic.jpg';
			}
			if($this->sysConfig['DEFAULT_THEME']==$arr[$key]['name']) $arr[$key]['use']=1;
		}

		$this->assign ( 'themes',$arr );
        $this->display ();
    }
	public function chose()
	{
		$theme = $_GET['theme'];
		if($theme){
			$Model = M('Config');
			$r = $Model->where("varname='DEFAULT_THEME'")->setField('value',$theme);
			savecache('Config');
			$this->success(L('do_ok'));
		}else{
			$this->error(L('do_empty'));
		}
	}
	public function upload()
	{
		$this->display ();
	}
}
?>