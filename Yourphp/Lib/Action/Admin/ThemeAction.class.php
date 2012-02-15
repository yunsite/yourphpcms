<?php
/**
 * 
 * Theme(风格选择)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
class ThemeAction extends AdminbaseAction
{
	 protected $filepath,$publicpath;
    function _initialize()
    {	
		parent::_initialize();
		$this->filepath = TMPL_PATH.$this->sysConfig['DEFAULT_THEME'].'/Home/';
		$this->publicpath = TMPL_PATH.$this->sysConfig['DEFAULT_THEME'].'/Public/';
    }

    public function index()
    {
		$filed = glob(TMPL_PATH.'*');
		foreach ($filed as $key=>$v) {
			$arr[$key]['name'] =  basename($v);
			if(is_file(TMPL_PATH.$arr[$key]['name'].'/Home/preview.jpg')){
				$arr[$key]['preview']=TMPL_PATH.$arr[$key]['name'].'/Home/preview.jpg';
			}else{
				$arr[$key]['preview']=WEB_PUBLIC_PATH.'/Images/nopic.jpg';
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