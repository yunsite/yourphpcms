<?php
/**
 * 
 * IndexAction.class.php (前台首页)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class TagsAction extends BaseAction
{
    public function index()
    {
		$slug = $tag ? $tag :get_safe_replace($_REQUEST['tag']);
		$module = get_safe_replace($_REQUEST['module']);
		$module = 	$module ? ucfirst($module) : '';
		if(C('URL_MODEL')==0){
			$moduleid = intval($_REQUEST['moduleid']);
			$module_name = $moduleid ? $this->module[$moduleid]['name'] : '';
			$module =$module ? $module : $module_name;
			$p= max(intval($_REQUEST[C('VAR_PAGE')]),1);
		}elseif($this->mod[ucfirst($slug)]){
			$module=ucfirst($slug);
			unset($slug);
		}

		$prefix=C( "DB_PREFIX" );
		$Tags=M('Tags');
		$Tags_data=M('Tags_data');
		$where= APP_LANG ?  " lang=".LANG_ID : 1 ;

		if($slug){
			$module= $module ? $module :'Article';
			$moduleid=$this->mod[$module];
			$data = $Tags->where($where." and moduleid = $moduleid and slug='".$slug."'")->find();
			$this->assign ('seo_title',$data['name']);
			$this->assign ('seo_keywords',$data['name']);
			$this->assign ('seo_description',$data['name']);
			if($data){
				$tagid=$data['id'];
				$this->assign ('data',$data);
				$mtable=$prefix.strtolower($module);
				$count = $Tags_data->table($prefix.'tags_data as a')->join($mtable." as b on a.id=b.id ")->where("a.tagid=".$tagid)->count();
				if($count){
					import ( "@.ORG.Page" );
					$listRows =  C('PAGE_LISTROWS'); //C('PAGE_LISTROWS')
					$page = new Page ( $count, $listRows,$p );
					$page->urlrule = TAGURL($data,1);
					$pages = $page->show();
					$field =  'b.id,b.catid,b.userid,b.url,b.username,b.title,b.keywords,b.description,b.thumb,b.createtime';
					$list = $Tags_data->field($field)->table($prefix.'tags_data as a')->join($mtable." as b on a.id=b.id")->where($where." and a.tagid=".$tagid)->order('b.listorder desc,b.id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
					$this->assign('pages',$pages);
					$this->assign('list',$list);
				}
			}
		}else{
			$moduleid=$this->mod[$module];
			$where .= $moduleid ? ' and moduleid='.$moduleid : '';
			$count = $Tags->where($where)->count();
			if($count){
				import ( "@.ORG.Page" );
				$listRows =  50;
				$page = new Page ( $count, $listRows );
				$page->urlrule = TAGURL(array('moduleid'=>$moduleid),1); 
				$pages = $page->show();
				$list = $Tags->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
				foreach($list as $key=>$r){ $list[$key]['module']=$this->module[$r['moduleid']]['name'];}
				$this->assign('pages',$pages);
				$this->assign('list',$list);
			}
		}
		$this->assign('bcid',0);//顶级栏目 
		$template = $slug ? 'list' : 'index';
		$this->display("Tags:".$template);
    }
 
}
?>