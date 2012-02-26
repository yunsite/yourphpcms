<?php
/**
 * 
 * TagLib (标签库)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
if(defined('APP_NAME')!='Yourphp' && !defined("YOURPHP"))  exit("Access Denied");
class TagLibYp extends TagLib
{
	protected $tags   =  array(
        // 标签定义：
        //attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'list'=>array('attr'=>'name,field,limit,order,catid,thumb,posid,where,sql,key,page,mod,id,ids,status','level'=>3),
		'subcat'=>array('attr'=>'catid,type,self,key,id','level'=>3),
		'catpos' => array('attr'=>'catid,space','close'=>0),
		'nav' => array('attr'=>'catid,bcid,level,id,class,home,enhomefont','close'=>0),
		'link' => array('attr'=>'typeid,linktype,field,limit,order,field','level'=>3),
		'kefu' => array('attr'=>'left,top,level,id,class,type','close'=>0),
		'db' => array('attr'=>'dbname,sql,key,mod,id','level'=>3),
		'block' => array('attr'=>'blockid,pos,key,id','close'=>0),
		'flash' => array('attr'=>'flashid,key,mod,id','close'=>0),
		'tags' => array('attr'=>'keywords,list,key,mod,moduleid,id,limit,order','close'=>3),
    );

	public function _tags($attr,$content) {
		$tag    = $this->parseXmlAttr($attr,'tags');
		$id = !empty($tag['id'])?$tag['id']:'r';  //定义数据查询的结果存放变量
		$key    = !empty($tag['key'])?$tag['key']:'i';
		$mod    = isset($tag['mod'])?$tag['mod']:'2';
		$limit  = isset($tag['limit'])?$tag['limit']: '12';
		$order  = isset($tag['order'])?$tag['order']:'id desc';
		
		$keywords   = !empty($tag['keywords'])? $tag['keywords'] : ''; 
		$list = !empty($tag['list'])? $tag['list'] : ''; 
		$moduleid = !empty($tag['moduleid'])? $tag['moduleid'] : ''; 

		
		if($moduleid && !is_numeric($moduleid)){
			if(substr($moduleid,0,2)=='T['){
				$T = $this->tpl->get('T');
				preg_match_all("/T\[(.*)\]$/",$moduleid,$arr);
				$moduleid=$T[$arr[1][0]];
			}else{
				$moduleid= $this->tpl->get($moduleid);
			}
		}
		preg_match("/[a-zA-Z]+/",$keywords,$keywordsa);
		if($keywordsa[0]){
			$keywords = $this->tpl->get($keywords);
		}
		if($keywords){
			$keyarr = explode(',',$keywords);
			$keywords="'".implode('\',\'',$keyarr)."'";
		}
 
		if(APP_LANG){
			$lang=$this->tpl->get('langid');
			$where= APP_LANG ?  " lang=".$lang : 1 ;
		}
		$where .= $moduleid ? ' and moduleid='.$moduleid : '';
		$where .= $keywords ? " and name in($keywords)" : '';
 
		if($list){ 
			$tagids = M("Tags")->where($where)->order($order)->limit($limit)->select();
			$where = " b.status=1 ";
			if($tagids[0]){
				foreach((array)$tagids as $r)$tagidarr[]=$r['id'];
				$tagid=implode(',',$tagidarr);
				$where .= " and a.tagid in($tagid)";
			}

			$M = $this->tpl->get('Module');
			$prefix=C( "DB_PREFIX" );
			$moduleid = $moduleid ? $moduleid : '2' ;
			$mtable=$prefix.strtolower($M[$moduleid]['name']);
			$field =  'b.id,b.catid,b.userid,b.url,b.username,b.title,b.keywords,b.description,b.thumb,b.createtime';
			$table=$prefix.'tags_data as a';
			$mtable = $mtable." as b on a.id=b.id";
			$sql  = "M(\"Tags_data\")->field(\"{$field}\")->table(\"{$table}\")->join(\"{$mtable}\")->where(\"{$where}\")->order(\"{$order}\")->group(\"b.id\")->limit(\"{$limit}\")->select();";
		}else{
			$sql  = "M(\"Tags\")->where(\"{$where}\")->order(\"{$order}\")->limit(\"{$limit}\")->select();";
		}	 

		//下面拼接输出语句
		$parsestr  = '';
		$parsestr .= '<?php  $_result='.$sql.'; if ($_result): $'.$key.'=0;';
		$parsestr .= 'foreach($_result as $key=>$'.$id.'):';
		$parsestr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>';
		$parsestr .= $content;//解析在article标签中的内容
		$parsestr .= '<?php endforeach; endif;?>';
		return  $parsestr;
	}

	public function _flash($attr,$content) {
		$tag    = $this->parseXmlAttr($attr,'flash');
		$id = !empty($tag['id'])?$tag['id']:'r';  //定义数据查询的结果存放变量
		$key    = !empty($tag['key'])?$tag['key']:'i';
		$mod    = isset($tag['mod'])?$tag['mod']:'2';
		$flashid   = !empty($tag['flashid'])? $tag['flashid'] : '';
		$where = ' status=1 ';
		if(APP_LANG){
			$lang=$this->tpl->get('langid');
			$wherelang = ' and lang='.$lang;
		}

		if($flashid && !is_numeric($flashid)){
			if(substr($flashid,0,2)=='T['){
				$T = $this->tpl->get('T');
				preg_match_all("/T\[(.*)\]$/",$flashid,$arr);
				$flashid=$T[$arr[1][0]];
			}else{
				$flashid= $this->tpl->get($flashid);
			}
		}

		if($flashid)$where .=" and id=$flashid ";
		$flash = M('Slide')->where($where)->find();
		if(empty($flash)) return  '';
		$wherepic = " status=1 and  fid=$flashid ".$wherelang;
		$order=" listorder ASC ,id DESC ";
		$limit= $flash['num'] ? $flash['num'] : 5;
		$sql="M('Slide_data')->where(\"{$wherepic}\")->order(\"{$order}\")->limit(\"{$limit}\")->select();";
		//下面拼接输出语句
		$parsestr  = '';
		$parsestr .= '<?php  $_result='.$sql.';if ($_result): $'.$key.'=0;';
		$parsestr .= 'foreach($_result as $key=>$'.$id.'):';
		$parsestr .= '$'.$key.'++;$mod = ($'.$key.' % '.$mod.' );parse_str($'.$id.'[\'data\'],$'.$id.'[\'param\']);?>';
		$loopend = '<?php endforeach; endif;?>';
 
		$Tpl = TMPL_PATH.'Home/'.THEME_NAME.'/Slide_'.$flash['tpl'].'.html';
		//$flashpath = TMPL_PATH.C('DEFAULT_THEME_NAME').'/Public/flash/';
		$html = file_get_contents($Tpl);
		$html = str_replace(array('{loop}','{/loop}','{$xmlfile}','{$flashfile}','{$flashwidth}','{$flashheight}','{$flashid}'),array($parsestr,$loopend,$flash['xmlfile'],$flash['flashfile'],$flash['width'],$flash['height'],$flashid),$html);

		return  $html;		
	}

	public function _block($attr,$content) {
		$tag    = $this->parseXmlAttr($attr,'block');
		$id = !empty($tag['id'])?$tag['id']:'r';  //定义数据查询的结果存放变量
		$key    = !empty($tag['key'])?$tag['key']:'i';
		$pos   = !empty($tag['pos'])? $tag['pos'] : '';
		$mod    = isset($tag['mod'])?$tag['mod']:'2';
		$blockid   = !empty($tag['blockid'])? $tag['blockid'] : '';

		if($blockid && !is_numeric($blockid)){
			if(substr($blockid,0,2)=='T['){
				$T = $this->tpl->get('T');
				preg_match_all("/T\[(.*)\]$/",$blockid,$arr);
				$blockid=$T[$arr[1][0]];
			}else{
				$blockid= $this->tpl->get($blockid);
			}
		}


		$where = ' 1 ';
		if(APP_LANG){
			$lang=$this->tpl->get('langid');
			$where .= ' and lang='.$lang;
		}
		if($pos)$where .=" and pos='$pos' ";
		if($blockid)$where .=" and id=$blockid ";
		$r = M('Block')->where($where)->find();	
		return  $r['content'];		
	}

	public function _db($attr,$content) {
		$tag    = $this->parseXmlAttr($attr,'db');
		$id = !empty($tag['id'])?$tag['id']:'r';  //定义数据查询的结果存放变量
		$key    = !empty($tag['key'])?$tag['key']:'i';
		$sql   = !empty($tag['sql'])? $tag['sql'] : '';
		$dbname    = isset($tag['dbname'])?$tag['dbname']:'';
		$mod    = isset($tag['mod'])?$tag['mod']:'2';

		$dbsource=  F('Dbsource');
		$db=$dbsource[$dbname];
		if(empty($db) || empty($sql)) return '';
		$sql = str_replace('{tablepre}',$db['dbtablepre'],$sql);
		$db = 'mysql://'.$db['username'].':'.$db['password'].'@'.$db['host'].':'.$db['port'].'/'.$db['dbname'];
		$sql="M()->db(1,\"{$db}\")->query(\"{$sql}\");";

		//下面拼接输出语句
		$parsestr  = '';
		$parsestr .= '<?php  $_result='.$sql.'; if ($_result): $'.$key.'=0;';
		$parsestr .= 'foreach($_result as $key=>$'.$id.'):';
		$parsestr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>';
		$parsestr .= $content;//解析在article标签中的内容
		$parsestr .= '<?php endforeach; endif;?>';
		return  $parsestr;
	}


	public function _nav($attr,$content) {
		$tag        = $this->parseXmlAttr($attr,'nav');
		$level = !empty($tag['level'])? intval($tag['level']) : '1';
		$catid = !empty($tag['catid'])? $tag['catid'] : '0';
		$bcid = !empty($tag['bcid'])? intval($tag['bcid']) : '0';
		$class = !empty($tag['class'])? $tag['class'] : '';
		$id = !empty($tag['id'])? $tag['id'] : 'nav';
		$homefont = !empty($tag['home'])? $tag['home'] : '';
		$enhomefont = !empty($tag['enhome'])? $tag['enhome'] : '';
		$category_arr = $this->tpl->get('Categorys');
        //$site_url = $this->tpl->get('site_url');
		$parsestr ='';
		
		if($catid && !is_numeric($catid)){
			if(substr($catid,0,2)=='T['){
				$T = $this->tpl->get('T');
				preg_match_all("/T\[(.*)\]$/",$catid,$arr);
				$catid=$T[$arr[1][0]];
			}else{
				$catid= $this->tpl->get($catid);
			}
		}
	 
 		if(is_array($category_arr)){
			foreach($category_arr as $r) {
				if(empty($r['ismenu']))  continue;
				$r['name'] = $r['catname'];
				$r['level']=count(explode(',',$r['arrparentid']));
				$array[] = $r;
			}
			import ( '@.ORG.Tree' );
			$tree = new Tree ($array);
			$parsestr = $tree->get_nav($catid,$level,$id,$class,$homefont,FALSE,'',$enhomefont,$lang);
		}
		return  $parsestr;
	}

	public function _subcat($attr,$content) {
		$tag        = $this->parseXmlAttr($attr,'subcat');
		$id = !empty($tag['id'])?$tag['id']:'r'; //定义数据查询的结果存放变量 result
		$type = !empty($tag['type'])?$tag['type']:'';
		$self = !empty($tag['self'])?'1':'';
		$key    = !empty($tag['key'])?$tag['key']:'n';
		$catid = !empty($tag['catid'])? $tag['catid'] : '0';
		if($type) $condition = ' &&  $'.$id.'["type"] == "'.$type.'"';

		
		if($catid && !is_numeric($catid)){
			if(substr($catid,0,2)=='T['){
				$T = $this->tpl->get('T');
				preg_match_all("/T\[(.*)\]$/",$catid,$arr);
				$catid=$T[$arr[1][0]];
			}elseif(substr($catid,0,1)=='$') {
				$catid   = $catid;
			}else{
				$catid= $this->tpl->get($catid);
			}
		}

		if($self){ $ifleft = '('; $selfcondition = ') or (  $'.$id.'[\'ismenu\']==1 && intval('.$catid.')==$'.$id.'["id"])';}
		$parsestr ='';
		$parsestr .= '<?php $'.$key.'=0;';
		$parsestr .= 'foreach($Categorys as $key=>$'.$id.'):';
		$parsestr .= 'if('.$ifleft.' $'.$id.'[\'ismenu\']==1 && intval('.$catid.')==$'.$id.'["parentid"] '.$condition.$selfcondition.' ) :';
		$parsestr .= '++$'.$key.';?>';
		$parsestr .= $content;
		$parsestr .= '<?php endif;?>';
		$parsestr .= '<?php endforeach;?>';
		return  $parsestr;
	}

	public function _catpos($attr) {
		$tag        = $this->parseXmlAttr($attr,'catpos');
		$space		= $tag['space'];


 		if(is_numeric($tag['catid'])){
            $catid   = intval($tag['catid']);
		}else{
			$catid   = $this->tpl->get($tag['catid']);
		}

		$category_arr = $this->tpl->get('Categorys');
		if(!isset($category_arr[$catid])) return '';
		$arrparentid = array_filter(explode(',', $category_arr[$catid]['arrparentid'].','.$catid));
		foreach($arrparentid as $cid) {
			$parsestr .= '<a href="'.$category_arr[$cid]['url'].'">'.$category_arr[$cid]['catname'].'</a>'.$space;
		}
		return  $parsestr;
	}
	public function _link($attr,$content) {
		$tag    = $this->parseXmlAttr($attr,'link');
		$id = !empty($tag['id'])?$tag['id']:'r';  //定义数据查询的结果存放变量
		$key    = !empty($tag['key'])?$tag['key']:'i';
		$mod    = isset($tag['mod'])?$tag['mod']:'2';

		//$typeid    = isset($tag['$typeid'])?$tag['$typeid']:'';
		$linktype    = isset($tag['linktype'])?$tag['linktype']:'';
		$order  = isset($tag['order'])?$tag['order']:'id desc';
		$limit  = isset($tag['limit'])?$tag['limit']: '10';
		$field  = isset($tag['field'])?$tag['field']:'*';
		$where = ' status = 1 ';
		if(APP_LANG){
			$lang=$this->tpl->get('langid');
			$where .= ' and lang='.$lang;
		}

		if(substr($tag['typeid'],0,1)=='$') {
			$where .= ' and  typeid=$tag["typeid"]';
        }elseif(is_numeric($tag['typeid'])){
			$where .= " and typeid=".intval($tag['typeid']);
		}else{
            $typeid   = $this->tpl->get($tag['typeid']);
			if($typeid)	$where .= "  and typeid=".intval($typeid);
        }
		if($linktype){
			$where .=  " and  linktype=".$linktype;
		}
		$sql  = "M(\"Link\")->field(\"{$field}\")->where(\"{$where}\")->order(\"{$order}\")->limit(\"{$limit}\")->select();";

		//下面拼接输出语句
		$parsestr  = '';
		$parsestr .= '<?php  $_result='.$sql.'; if ($_result): $'.$key.'=0;';
		$parsestr .= 'foreach($_result as $key=>$'.$id.'):';
		$parsestr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>';
		$parsestr .= $content;//解析在article标签中的内容
		$parsestr .= '<?php endforeach; endif;?>';
		return  $parsestr;
	}
	public function _kefu($attr,$content) {
		$tag    = $this->parseXmlAttr($attr,'kefu');
		$id = !empty($tag['id'])?$tag['id']:'r';  //定义数据查询的结果存放变量
		$type   = !empty($tag['type'])?$tag['type']: '';
		$order  = isset($tag['order'])?$tag['order']:'listorder desc ,id desc';
		$left =  isset($tag['left'])?$tag['left']: '0';
		$top =  isset($tag['top'])?$tag['top']: '100';
		$where ="status=1";
		if(APP_LANG){
			$lang=$this->tpl->get('langid');
			$where .= ' and lang='.$lang;
		}
		if($type)$where .=" and type in($type)";

		$data  = M("Kefu")->field("*")->where($where)->order($order)->select();
		if(empty($data))return  '';
		$site_name = $this->tpl->get('site_name');
		foreach($data as $key =>$r){
				if($r['name']) $datas.='<li class="tit">'.$r['name'].':</li>';
			if($r['type']==1){
				//http://wpa.qq.com/msgrd?v=3&uin=426507856&site=qq&menu=yes
				$skin =	str_replace('q','',$r['skin']);
				$codes=explode("\n",$r['code']);
				foreach((array)$codes as $code){
					if($code){
						$codearr=explode("|",$code);
						$code= $codearr[0];$codename= $codearr[1];
						$datas.='<li><a href="http://wpa.qq.com/msgrd?v=3&uin='.$code.'&site=qq&menu=yes" rel="nofollow"><img border="0" SRC="http://wpa.qq.com/pa?p=1:'.$code.':'.$skin.'" alt="'.$r['name'].'">'.$codename.'</a>';
					}
				}
			}elseif($r['type']==2){
				$skin =	str_replace('m','',$r['skin']);
				$codes=explode("\n",$r['code']);
				foreach((array)$codes as $code){
					if($code){
						$codearr=explode("|",$code);
						$code= $codearr[0];$codename= $codearr[1];
						$datas.='<li><a href="msnim:chat?contact='.$code.'"><img src="'.__ROOT__.'/Public/Images/kefu/msn'.$skin.'.gif">'.$codename.'</a></li>';
					}
				}
			}elseif($r['type']==3){
				$skin =	str_replace('w','',$r['skin']);
				$codes=explode("\n",$r['code']);
				foreach((array)$codes as $code){
					if($code){
						$codearr=explode("|",$code);
						$code= $codearr[0];$codename= $codearr[1];
						$datas.='<a target="_blank" href="http://www.taobao.com/webww/ww.php?ver=3&touid='.$code.'&siteid=cntaobao&status='.$skin.'&charset=utf-8" rel="nofollow"><img border="0" src="http://amos.alicdn.com/online.aw?v=2&uid='.$code.'&site=cntaobao&s='.$skin.'&charset=utf-8" alt="'.$r['name'].'" />'.$codename.'</a>';
					}
				}
			}elseif($r['type']==4){
				$codes=explode("\n",$r['code']);
				foreach((array)$codes as $code){
					if($code){
						$codearr=explode("|",$code);
						$code= $codearr[0];$codename= $codearr[1];
						if($codename) $codename='<label>'.$codename.'</label>';
						$datas.='<li>'.$codename.$code.'</li>';
					}
				}
			}elseif($r['type']==5){
				$datas.='<li>'.stripcslashes($r['code']).'</li>';
			}elseif($r['type']==6){
				$skin =	str_replace('al','',$r['skin']);
				$codes=explode("\n",$r['code']);
				foreach((array)$codes as $code){
					if($code){
						$codearr=explode("|",$code);
						$code= $codearr[0];$codename= $codearr[1];
						$datas.='<a href="http://web.im.alisoft.com/msg.aw?v=2&uid='.$code.'&site=cnalichn&s=1" target="_blank"><img alt="'.$r['name'].'" src="http://web.im.alisoft.com/online.aw?v=2&uid='.$code.'&site=cnalichn&s='.$skin.'" border="0" />'.$codename.'</a>';
					}
				}
			}else{
				$datas.='<li>'.$r['code'].'</li>';
			}
		}
		$parsestr='';
		$parsestr .='<div class="kefu" id="'.$id.'"><div class="kftop"></div><div class="kfbox"><ul>';
		$parsestr .=$datas;
		$parsestr .='</ul></div><div class="kfbottom"></div></div>';
		$parsestr .='<script> var '.$id.' = new Floaters(); '.$id.'.addItem("'.$id.'",'.$left.','.$top.',"");'.$id.'.play("'.$id.'");</script>';

		return  $parsestr;
	}

	public function _list($attr,$content) {

			$tag    = $this->parseXmlAttr($attr,'list');
			$id = !empty($tag['id'])?$tag['id']:'r';  //定义数据查询的结果存放变量
			$key    = !empty($tag['key'])?$tag['key']:'i';
			$page   = !empty($tag['page'])? '1' : '0';
			$mod    = isset($tag['mod'])?$tag['mod']:'2';
			

			if ($tag['name'] || $tag['catid'])
			{   //根据用户输入的值拼接查询条件
				$sql='';
				$module = $tag['name'];
				$order  = isset($tag['order'])?$tag['order']:'id desc';
				$field  = isset($tag['field'])?$tag['field']:'id,catid,url,title,title_style,keywords,description,thumb,createtime';
				$where  = isset($tag['where'])?$tag['where']: ' 1 ';
				$limit  = isset($tag['limit'])?$tag['limit']: '10';
				$status = isset($tag['status'])? intval($tag['status']) : '1';
				$ids =  isset($tag['ids'])?$tag['ids']:'';
				if(APP_LANG){
					$lang=$this->tpl->get('langid');
					$where .= ' and lang='.$lang;
				}
				if($ids){
					if($ids && !is_numeric($ids)){
						if(substr($ids,0,2)=='T['){
							$T = $this->tpl->get('T');
							preg_match_all("/T\[(.*)\]$/",$ids,$arr);
							$ids=$T[$arr[1][0]];
						}else{
							$ids= $this->tpl->get($ids);
						}
					}
					if(strpos($ids,',')){
						$where .= " AND id in($ids) ";
					}else{
						$where .= " AND id=$ids ";
					}
				}

				if(ucfirst($module)!='Page')$where .= " AND status=$status ";
				if($tag['catid']){
					$onezm  = substr($tag['catid'],0,1);
					if(substr($tag['catid'],0,2)=='T['){
						$T = $this->tpl->get('T');
						preg_match_all("/T\[(.*)\]$/",$tag['catid'],$cidarr);
						$catid=$T[$cidarr[1][0]];
					}elseif(!is_numeric($onezm)) {
						$catid = $this->tpl->get($tag['catid']);

					}else{
						$catid = $tag['catid'];
					}
					if(is_numeric($catid)){
						$category_arr = $this->tpl->get('Categorys');
						$module = $category_arr[$catid]['module'];
						if($category_arr[$catid]['child']){
							$where .= " AND catid in(".$category_arr[$catid]['arrchildid'].")";
						}else{
							$where .=  " AND catid=".$catid;
						}
					}elseif($onezm=='$') {
						$where .=  ' AND catid in('.$tag['catid'].')';
					}else{
							$where .=  ' AND catid in('.strip_tags($tag['catid']).')';
					}
				}


				if($tag['posid']){
					$posid = $tag['posid'];
					if(!is_numeric($posid) && substr($posid,0,2)=='T['){
						$T = $this->tpl->get('T');
						preg_match_all("/T\[(.*)\]$/",$posid,$cidarr);
						$posid=$T[$cidarr[1][0]];
					}
					if(is_numeric($posid)){
						$where .=  '  AND posid ='.$posid;
					}else{
						$where .=  ' AND posid in('.strip_tags($posid).')';
					}
				}
				if($tag['thumb']){
					$where .=  ' AND  thumb !=\'\' ';
				}
				$sql  = "M(\"{$module}\")->field(\"{$field}\")->where(\"{$where}\")->order(\"{$order}\")->limit(\"{$limit}\")->select();";
			}else{
				if (!$tag['sql']) return ''; //排除没有指定model名称，也没有指定sql语句的情况
				$sql = "M()->query(\"{$tag['sql']}\")";
			}

			//下面拼接输出语句
			$parsestr  = '';
			$parsestr .= '<?php  $_result='.$sql.'; if ($_result): $'.$key.'=0;';
			$parsestr .= 'foreach($_result as $key=>$'.$id.'):';
			$parsestr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>';
			$parsestr .= $content;//解析在article标签中的内容
			$parsestr .= '<?php endforeach; endif;?>';
			return  $parsestr;
	}

}
?>