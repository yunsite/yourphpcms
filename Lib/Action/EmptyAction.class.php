<?php
/**
 * 
 * Empty (空模块)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
if(!defined("YOURPHP")) exit("Access Denied");
class EmptyAction extends Action
{	
	public function _empty()
	{
		//空操作 空模块
		if(MODULE_NAME!='Urlrule'){
			$Mod = F('Mod');			
			if(!$Mod[MODULE_NAME]){ 
				throw_exception('404');
			}
		}

		if(GROUP_NAME=='Admin'){
			R('Admin/Content/'.ACTION_NAME);
		}else{
			$a=ACTION_NAME;
			$id =  intval($_REQUEST['id']);
			$catid = intval($_REQUEST['catid']);
			$moduleid =  intval($_REQUEST['moduleid']);
			if(MODULE_NAME=='Urlrule'){
				if(APP_LANG){
					$lang= $_REQUEST['l'] ? '_'.$_REQUEST['l'] : '_'.C('DEFAULT_LANG');
				}
				if($_REQUEST['catdir']){
					$Cat = F('Cat'.$lang);
					$catid = $catid ? $catid : $Cat[$_REQUEST['catdir']];
					unset($Cat);
				}
				if($_REQUEST['module']){
					$m=$_REQUEST['module'];						
				}elseif($moduleid){
					$Module =F('Module');
					$m=$Module[$moduleid]['module'];
					unset($Module);
				}elseif($catid){
					$Category = F('Category'.$lang);
					$m=$Category[$catid]['module'];
					unset($Category);
				}else{
					throw_exception('404');
				}
				if($a=='index') $id=$catid;
			}else{				
				if(empty($id)){
					$Cat = F('Cat'.$lang);
					$id = $Cat[$_REQUEST['id']];
					unset($Cat);
				}
				$m=MODULE_NAME;			
			}
			import('@.Action.Base');
			$bae=new BaseAction();
			if(!method_exists($bae,$a)){
				throw_exception('404');
			}
			$bae->$a($id,$m);
		}
	}
}
?>