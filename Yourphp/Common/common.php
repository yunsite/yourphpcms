<?php
/**
 * 
 * Common.php (项目公共函数库)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
function fieldoption($fields,$value=null,$space=''){
	$options = explode("\n",$fields['setup']['options']);
	foreach($options as $r) {
		$v = explode("|",$r);
		$k = trim($v[1]);
		$optionsarr[$k] = $v[0];
	}
	if(isset($value)){
		if(strpos($value,',')){
			$value =explode(",",$value);
			$data=array();
			foreach((array)$value as $val){
			$data[]= $optionsarr[$val];
			}
			if($space!=''){
			return implode(stripcslashes($space),$data);
			}else{
			return $data;
			}			
		}else{
			return $optionsarr[$value];
		}
	}else{
		return $optionsarr;
	}
}

function picstoarr($str=''){
	$data=array();
	$v = explode(":::",$str);
	foreach((array)$v as $r){
		$r=explode('|',$r);
		$res['file']=$r[0];
		$res['desc']=$r[1];
		$data[]=$res;
	}
	return $data;
}

function get_arrparentid($pid, $array=array(),$arrparentid='') {
		if(!is_array($array) || !isset($array[$pid])) return $pid;
		$parentid = $array[$pid]['parentid'];
		$arrparentid = $arrparentid ? $parentid.','.$arrparentid : $parentid;
		if($parentid) {
			$arrparentid = get_arrparentid($parentid,$array, $arrparentid);
		}else{
			$data = array();
			$data['bid'] = $pid;
			$data['arrparentid'] = $arrparentid;
		}

		return $arrparentid;
}

function getform($form,$info,$value=''){
	return  $form->$info['type']($info,$value);
}

function getvalidate($info){
        $validate_data=array();
        if($info['minlength']) $validate_data['minlength'] = ' minlength:'.$info['minlength'];
		if($info['maxlength']) $validate_data['maxlength'] = ' maxlength:'.$info['maxlength'];
		if($info['required']) $validate_data['required'] = ' required:true';
		if($info['pattern']) $validate_data['pattern'] = ' '.$info['pattern'].':true';
        if($info['errormsg']) $errormsg = ' title="'.$info['errormsg'].'"';
        $validate= implode(',',$validate_data);
        $validate= $validate ? 'validate="'.$validate.'" ' : '';
        $parseStr = $validate.$errormsg;
        return $parseStr;
}

function sendmail($tomail,$subject,$body,$config=''){

		if(!$config)$config = F('Config');

		import("@.ORG.PHPMailer");
		$mail = new PHPMailer();

		if($config['mail_type']==1){
			$mail->IsSMTP();
		}elseif($config['mail_type']==2){
			$mail->IsMail();
		}else{
			if($config['sendmailpath']){
				$mail->Sendmail =$config['mail_sendmail'];
			}else{
				$mail->Sendmail =ini_get('sendmail_path');
			}
			$mail->IsSendmail();
		}
		if($config['mail_auth']){
			$mail->SMTPAuth = true; // 开启SMTP认证
		}else{
			$mail->SMTPAuth = false; // 开启SMTP认证
		}

		$mail->PluginDir=LIB_PATH."ORG/";
		$mail->CharSet='utf-8';
		$mail->SMTPDebug  = false;        // 改为2可以开启调试
		$mail->Host = $config['mail_server'];      // GMAIL的SMTP
		//$mail->SMTPSecure = "ssl"; // 设置连接服务器前缀
		//$mail->Encoding = "base64";
		$mail->Port = $config['mail_port'];    // GMAIL的SMTP端口号
		$mail->Username = $config['mail_user']; // GMAIL用户名,必须以@gmail结尾
		$mail->Password = $config['mail_password']; // GMAIL密码
		//$mail->From ="yourphp@163.com";
		//$mail->FromName = "yourphp企业建站系统";
		$mail->SetFrom($config['mail_from'], $config['site_name']);     //发送者邮箱
		$mail->AddAddress($tomail); //可同时发多个
		//$mail->AddReplyTo('147613338@qq.com', 'yourphp'); //回复到这个邮箱
		//$mail->WordWrap = 50; // 设定 word wrap
		//$mail->AddAttachment("/var/tmp/file.tar.gz"); // 附件1
		//$mail->AddAttachment("/tmp/image.jpg", "new.jpg"); // 附件2
		$mail->IsHTML(true); // 以HTML发送
		$mail->Subject = $subject;
		$mail->Body = $body;
		//$mail->AltBody = "This is the body when user views in plain text format";		//纯文字时的Body
		if(!$mail->Send())
		{
			return false;
		}else{
			return true;
		}
}

function delattach($map=''){
		$model = M('Attachment');
		$att= $model->field('aid,filepath')->where($map)->select();
		$aids=array();
		foreach((array)$att as $key=> $r){
			$aids[]=$r['aid'];
			@unlink(__ROOT__.$r['filepath']);
		}
		$r =$model->delete(implode(',',$aids));
		return  false!==$r ? true : false;
}

function template_file($module='',$path='',$ext='html'){
	$sysConfig = F('sys.config');
	$path= $path ? $path : TMPL_PATH.'Home/'.$sysConfig['DEFAULT_THEME'].'/';
	$tempfiles = dir_list($path,$ext);
	foreach ($tempfiles as $key=>$file){
		$dirname = basename($file);
		if($module){
			if(strstr($dirname,$module.'_')) {
				$arr[$key]['name'] =  substr($dirname,0,strrpos($dirname, '.'));
				$arr[$key]['value'] =  substr($arr[$key]['name'],strpos($arr[$key]['name'], '_')+1);
				$arr[$key]['filename'] = $dirname;
				$arr[$key]['filepath'] = $file;
			}
		}else{
			$arr[$key]['name'] = substr($dirname,0,strrpos($dirname, '.'));
			$arr[$key]['value'] =  substr($arr[$key]['name'],strpos($arr[$key]['name'], '_')+1);
			$arr[$key]['filename'] = $dirname;
			$arr[$key]['filepath'] = $file;
		}
	}
	return  $arr;
}

function fileext($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}

function dir_path($path) {
	$path = str_replace('\\', '/', $path);
	if(substr($path, -1) != '/') $path = $path.'/';
	return $path;
}

function dir_create($path, $mode = 0777) {
	if(is_dir($path)) return TRUE;
	$ftp_enable = 0;
	$path = dir_path($path);
	$temp = explode('/', $path);
	$cur_dir = '';
	$max = count($temp) - 1;
	for($i=0; $i<$max; $i++) {
		$cur_dir .= $temp[$i].'/';
		if (@is_dir($cur_dir)) continue;
		@mkdir($cur_dir, $mode,true);
		@chmod($cur_dir, $mode);
	}
	return is_dir($path);
}

function mk_dir($dir, $mode = 0777) {
    if (is_dir($dir) || @mkdir($dir, $mode))
        return true;
    if (!mk_dir(dirname($dir), $mode))
        return false;
    return @mkdir($dir, $mode);
}

function dir_copy($fromdir, $todir) {
	$fromdir = dir_path($fromdir);
	$todir = dir_path($todir);
	if (!is_dir($fromdir)) return FALSE;
	if (!is_dir($todir)) dir_create($todir);
	$list = glob($fromdir.'*');
	if (!empty($list)) {
		foreach($list as $v) {
			$path = $todir.basename($v);
			if(is_dir($v)) {
				dir_copy($v, $path);
			} else {
				copy($v, $path);
				@chmod($path, 0777);
			}
		}
	}
    return TRUE;
}

function dir_list($path, $exts = '', $list= array()) {
	$path = dir_path($path);
	$files = glob($path.'*');
	foreach($files as $v) {
		$fileext = fileext($v);
		if (!$exts || preg_match("/\.($exts)/i", $v)) {
			$list[] = $v;
			if (is_dir($v)) {
				$list = dir_list($v, $exts, $list);
			}
		}
	}
	return $list;
}
  
function dir_tree($dir, $parentid = 0, $dirs = array()) {
	if ($parentid == 0) $id = 0;
	$list = glob($dir.'*');
	foreach($list as $v) {
		if (is_dir($v)) {
            $id++;
			$dirs[$id] = array('id'=>$id,'parentid'=>$parentid, 'name'=>basename($v), 'dir'=>$v.'/');
			$dirs = dir_tree($v.'/', $id, $dirs);
		}
	}
	return $dirs;
}


function dir_delete($dir) {
	//$dir = dir_path($dir);
	if (!is_dir($dir)) return FALSE; 
	$handle = opendir($dir); //打开目录
	while(($file = readdir($handle)) !== false) {
	        if($file == '.' || $file == '..')continue;
			$d = $dir.DIRECTORY_SEPARATOR.$file;
	        is_dir($d) ? dir_delete($d) : @unlink($d);
	}
	closedir($handle);
	return @rmdir($dir);
}

function toDate($time, $format = 'Y-m-d H:i:s') {
	if (empty ( $time )) {
		return '';
	}
	$format = str_replace ( '#', ':', $format );
	return date ($format, $time );
}
function savecache($name = '',$id='') {
	unlink(RUNTIME_FILE);
	$Model = M ( $name );
	if($name=='Lang'){
		$list = $Model->order('listorder')->select ();
		$pkid = $Model->getPk ();
		$data = array ();
		foreach ( $list as $key => $val ) {
			$data [$val ['mark']] = $val;
		}
		F($name,$data);

	}elseif($name=='Module'){
		$list = $Model->order('listorder')->select ();
		$pkid = $Model->getPk ();
		$data = array ();
		foreach ( $list as $key => $val ){
			$data [$val [$pkid]] = $val;
			$smalldata[$val['name']] =  $val [$pkid];
		}
		F($name,$data);
		F('Mod',$smalldata);
		//savecache

	}elseif($name=='Config'){
		
		$list = $Model->select ();
		$data=$sysdata=$temp=$memberconfig=array();
		foreach($list as $key=>$r) {
			if($r['groupid']==6){
				$sysdata[$r['varname']]=$r['value'];
			}elseif($r['groupid']==3){
				if(APP_LANG)
					$memberconfig_temp[$r['lang']][$r['varname']]=$r['value'];
				else
					$memberconfig[$r['varname']]=$r['value'];
			}else{
				if(APP_LANG)
					if($r['lang']){$temp[$r['lang']][$r['varname']]=$r['value'];}else{$data[$r['varname']]=$r['value'];}
				else
					$data[$r['varname']]=$r['value'];
			}
		}
		if(APP_LANG){
			$lang=F('Lang');
			foreach((array)$lang as $key=>$r){
				$data1=array();
				$data1 = array_merge($temp[$r['id']],$data);
				F('Config_'.$key,$data1);
				F('member.config_'.$key,$memberconfig_temp[$r['id']]);
				if(empty($data1['HOME_ISHTML'])){
					@unlink('./index.html');
					@unlink('./'.$key.'/index.html');
				}
			}
		}else{
			F('Config',$data);
			F('member.config',$memberconfig);
			if(empty($data['HOME_ISHTML']))@unlink('./index.html');
		}

		$langs = M('Lang')->field('mark')->select();foreach((array)$langs as $r ) $lang1[]=$r['mark'];
		$sysdata['LANG_LIST'] = 'zh-cn,'.implode(',',$lang1);
		
		F('sys.config',$sysdata);

	}elseif($name=='Category'){

		$data=$smalldata=$temp=array();

		if(APP_LANG){
			$lang=F('Lang');
			foreach((array)$lang as $key=>$r){
				$langid =$r['id'];
				if($langid){
					$lang = $key;
					$list = $Model->where('lang='.$langid)->order('listorder')->select ();
					$pkid = $Model->getPk ();
					$data = array ();
					foreach ( $list as $key => $val ) {
						$data [$val [$pkid]] = $val;
						$smalldata[$val['catdir']] =  $val [$pkid];
					}
					F('Category_'.$lang,$data);
					F('Cat_'.$lang,$smalldata);
				}
			}
		}else{
			$list = $Model->order('listorder')->select ();
			$pkid = $Model->getPk ();
			$data = array ();
			foreach ( $list as $key => $val ) {
				$data [$val [$pkid]] = $val;
				$smalldata[$val['catdir']] =  $val [$pkid];
			}
			F($name,$data);
			F('Cat',$smalldata);
		}
	
	}elseif($name=='Field'){
		if($id){		
			$list = $Model->order('listorder')->where('moduleid='.$id)->select ();
			$pkid = 'field';
			$data = array ();
			foreach ( $list as $key => $val ) {
				$data [$val [$pkid]] = $val;
			}
			$name=$id.'_'.$name;
			F($name,$data);
		}else{
			$module = F('Module');
			foreach ( $module as $key => $val ) {
				savecache($name,$key);
			}
		}
	}elseif($name=='Dbsource'){
		$list = $Model->select ();
		$data = array ();
		foreach ( $list as $key => $val ) {
			$data [$val ['name']] = $val;
		}
		F($name,$data);		
	}else{		
		$list = $Model->order('listorder')->select ();
		$pkid = $Model->getPk ();
		$data = array ();
		foreach ( $list as $key => $val ) {
			$data [$val [$pkid]] = $val;
		}
		F($name,$data);
		if($name=='Urlrule'){
			$config = F('sys.config');
			if($config['URL_URLRULE'])routes_cache($config['URL_URLRULE']); 
		}
	}
	
	return true;
}


function checkfield($fields,$postdata){
		foreach ( $postdata as $key => $val ) {
				$setup=$fields[$key]['setup'];

				if(!empty($fields[$key]['required']) && empty($postdata[$key])) return '';

				//$setup=string2array($fields[$key]['setup']);
				if($setup['multiple'] || $setup['inputtype']=='checkbox' || $fields[$key]['type']=='checkbox'){
					$postdata[$key] =  safe_replace(strip_tags($postdata[$key]));
					$postdata[$key] = implode(',',$postdata[$key]);		
				}elseif($fields[$key]['type']=='datetime'){
					$postdata[$key] =strtotime($postdata[$key]);
				}elseif($fields[$key]['type']=='textarea'){
					$postdata[$key]=addslashes_array($postdata[$key]);
				}elseif($fields[$key]['type']=='images' || $fields[$key]['type']=='files'){
					$name = $key.'_name';
					$arrdata =array();
					foreach($postdata[$key] as $k=>$res){
						if(!empty($postdata[$key][$k])) $arrdata[]= safe_replace(strip_tags($postdata[$key][$k].'|'.$postdata[$name][$k]));
					}
					$postdata[$key]=implode(':::',$arrdata);
				}elseif($fields[$key]['type']=='editor'){					
					//自动提取摘要
					if(isset($postdata['add_description']) && $postdata['description'] == '' && isset($postdata['content'])) {
						$content = stripslashes($postdata['content']);
						$description_length = intval($postdata['description_length']);
						$postdata['description'] = str_cut(str_replace(array("\r\n","\t",'[page]','[/page]','&ldquo;','&rdquo;'), '', strip_tags($content)),$description_length);
						$postdata['description'] = addslashes_array($postdata['description']);
					}
					//自动提取缩略图
					if(isset($postdata['auto_thumb']) && $postdata['thumb'] == '' && isset($postdata['content'])) {
						$content = $content ? $content : stripslashes($postdata['content']);
						$auto_thumb_no = intval($postdata['auto_thumb_no']) * 3;
						if(preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
							$postdata['thumb'] = $matches[$auto_thumb_no][0];
						}
					}
				}elseif($fields[$key]['type']=='title' || $fields[$key]['type']=='text'){
					$postdata[$key] =  safe_replace(strip_tags($postdata[$key]));
				}
		}
		return $postdata;
}

function string2array($info) {
        if($info == '') return array();
        $info=stripcslashes($info);
        eval("\$r = $info;");
        return $r;
}


function array2string($info) {
	if($info == '') return '';
	if(!is_array($info)) $string = stripslashes($info);
	foreach($info as $key => $val) $string[$key] = stripslashes($val);
	return addslashes(var_export($string, TRUE));
}

/**
	 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
	 +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
	 +----------------------------------------------------------
 * @return string
	 +----------------------------------------------------------
 */
function rand_string($len = 6, $type = '', $addChars = '') {
	$str = '';
	switch ($type) {
		case 0 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 1 :
			$chars = str_repeat ( '0123456789', 3 );
			break;
		case 2 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
			break;
		case 3 :
			$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
			break;
	}
	if ($len > 10) { //位数过长重复字符串一定次数
		$chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
	}
	if ($type != 4) {
		$chars = str_shuffle ( $chars );
		$str = substr ( $chars, 0, $len );
	} else {
		// 中文随机字
		for($i = 0; $i < $len; $i ++) {
			$str .= msubstr ( $chars, floor ( mt_rand ( 0, mb_strlen ( $chars, 'utf-8' ) - 1 ) ), 1 );
		}
	}
	return $str;
}
function sysmd5($str,$key='',$type='sha1'){
	$key =  $key ?  $key : C('ADMIN_ACCESS');
	return hash ( $type, $str.$key );
}
function pwdHash($password, $type = 'md5') {
	return hash ( $type, $password );
}

/**
* @param string $string 原文或者密文
* @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE
* @param string $key 密钥
* @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效
* @return string 处理后的 原文或者 经过 base64_encode 处理后的密文
*
* @example
*
*  $a = authcode('abc', 'ENCODE', 'key');
*  $b = authcode($a, 'DECODE', 'key');  // $b(abc)
*
*  $a = authcode('abc', 'ENCODE', 'key', 3600);
*  $b = authcode('abc', 'DECODE', 'key'); // 在一个小时内，$b(abc)，否则 $b 为空
*/
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) 
{
	$ckey_length = 4;   
	// 随机密钥长度 取值 0-32;
	// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
	// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
	// 当此值为 0 时，则不产生随机密钥


	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) 
	{
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) 
	{
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) 
	{
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') 
	{
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) 
		{
			return substr($result, 26);
		} 
		else 
		{
			return '';
		}
	} 
	else 
	{
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}



//字符串截取
function str_cut($sourcestr,$cutlength,$suffix='...')
{
	$str_length = strlen($sourcestr);
	if($str_length <= $cutlength) {
		return $sourcestr;
	}
	$returnstr='';	
	$n = $i = $noc = 0;
	while($n < $str_length) {
			$t = ord($sourcestr[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$i = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$i = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$i = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$i = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$i = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$i = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $cutlength) {
				break;
			}
	}
	if($noc > $cutlength) {
			$n -= $i;
	}
	$returnstr = substr($sourcestr, 0, $n);
 

	if ( substr($sourcestr, $n, 6)){
          $returnstr = $returnstr . $suffix;//超过长度时在尾处加上省略号
      }
	return $returnstr;
}

function IP($ip='',$file='UTFWry.dat') {
	import("@.ORG.IpLocation");
	$iplocation = new IpLocation($file);
	$location = $iplocation->getlocation($ip);
	return $location;
}

function byte_format($input, $dec=0)
{
  $prefix_arr = array("B", "K", "M", "G", "T");
  $value = round($input, $dec);
  $i=0;
  while ($value>1024)
  {
     $value /= 1024;
     $i++;
  }
  $return_str = round($value, $dec).$prefix_arr[$i];
  return $return_str;
}

/**
 +----------------------------------------------------------
 * 获取登录验证码 默认为4位数字
 +----------------------------------------------------------
 * @param string $fmode 文件名
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function build_verify ($length=4,$mode=1) {
    return rand_string($length,$mode);
}


function make_urlrule($url,$lang,$action,$MOREREQUEST=''){
		preg_match_all ("/{([\w\$]+)}/",$url, $matches);
		//$REQUEST= implode(',',$matches[0]);
	
		if(strstr($url,'{$parentdir') && C('URL_PATHINFO_DEPR')=='/'){
			if(APP_LANG){
				foreach((array)$lang as $r){
					$Category = F('Category_'.$r);
					foreach((array)$Category as $key =>$r){
						if($r['parentid']==0)$pcatdir[]=$r['catdir'];				
					}
				}
			}else{
				$Category = F('Category');
				foreach((array)$Category as $key =>$r){
					if($r['parentid']==0)$pcatdir[]=$r['catdir'];				
				}
			}
			unset($Category);		
			$parent_rule = '('.implode('|',$pcatdir).')\/';
			//if(preg_match("/^[\w]+$/",$str)){ }
		}

		$REQUEST=str_replace(array('{$parentdir}','{$module}','{$moduleid}','{$catdir}','{$year}','{$month}','{$day}','{$catid}','{$id}','{$page}'),array('','module','moduleid','catdir','year','month','day','catid','id',C('VAR_PAGE')),$matches[0]);
		$rule=str_replace(array('{$parentdir}','{$module}','{$moduleid}','{$catdir}','{$year}','{$month}','{$day}','{$catid}','{$id}','{$page}','/',C('URL_HTML_SUFFIX')),array('','([A-Z]{1}[a-z]+)','(\d+)','([\w^_]+)','(\d+)','(\d+)','(\d+)','(\d+)','(\d+)','(\d+)','\/',''),$url);
		
		$i=0;$j=1;$k=2;$n=3;$m=4;
		foreach($REQUEST as $key =>$r){
			if($r){
			$i=$i+1;
			$request .=$r.'=:'.$i.'&';
			$j=$j+1;
			$request_lang .=$r.'=:'.$j.'&';
			$k=$k+1;
			$request_lang_2 .=$r.'=:'.$k.'&'; //二级
			$n=$n+1;
			$request_lang_3 .=$r.'=:'.$n.'&'; //三级
			}
		}

		if(APP_LANG){
			$langrule = '('.implode('|',$lang).')\/';
			
			if($parent_rule){
				$data[] = '\'/^'.$langrule.$parent_rule.'([\w^_]+)\/'.$rule.'$/\' => \'Urlrule/'.$action.'?l=:1&parentdir=:2&'.$request_lang_3.$MOREREQUEST.$langrequest.'\'';
				$data[] = '\'/^'.$langrule.$parent_rule.$rule.'$/\' => \'Urlrule/'.$action.'?l=:1&parentdir=:2&'.$request_lang_2.$MOREREQUEST.$langrequest.'\'';
				$data[] = '\'/^'.$parent_rule.'([\w^_]+)\/'.$rule.'$/\' => \'Urlrule/'.$action.'?parentdir=:1&'.$request_lang_2.$MOREREQUEST.$langrequest.'\'';	
				$data[] = '\'/^'.$parent_rule.$rule.'$/\' => \'Urlrule/'.$action.'?parentdir=:1&'.$request_lang.$MOREREQUEST.$langrequest.'\'';
				
				if(strstr($url,'{$page')){
					$data[] = '\'/^'.$langrule.$parent_rule.'(\d+)$/\' => \'Urlrule/'.$action.'?l=:1&catdir=:2&p=:3\'';	 
					$data[] = '\'/^'.$parent_rule.'(\d+)$/\' => \'Urlrule/'.$action.'?catdir=:1&p=:2\'';
				}else{
					$data[] = '\'/^'.$langrule.$parent_rule.'$/\' => \'Urlrule/'.$action.'?l=:1&catdir=:2\'';	 
					$data[] = '\'/^'.$parent_rule.'$/\' => \'Urlrule/'.$action.'?catdir=:1\'';
				}
			}else{
				$data[] = '\'/^'.$langrule.$rule.'$/\' => \'Urlrule/'.$action.'?l=:1&'.$request_lang.$MOREREQUEST.$langrequest.'\'';	 
				$data[]='\'/^'.$rule.'$/\' => \'Urlrule/'.$action.'?'.$request.$MOREREQUEST.'\'';
			}
			$data = str_replace('\/$','$',$data);
			$data= implode(",\n",$data);
		}else{
			if($parent_rule){
				$data[] = '\'/^'.$parent_rule.'([\w^_]+)\/'.$rule.'$/\' => \'Urlrule/'.$action.'?parentdir=:1&'.$request_lang_2.$MOREREQUEST.$langrequest.'\'';	
				$data[] = '\'/^'.$parent_rule.$rule.'$/\' => \'Urlrule/'.$action.'?parentdir=:1&'.$request_lang.$MOREREQUEST.$langrequest.'\'';			
				if(strstr($url,'{$page')){				
					$data[] = '\'/^'.$parent_rule.'(\d+)$/\' => \'Urlrule/'.$action.'?catdir=:1&p=:2\'';
				}else{ 
					$data[] = '\'/^'.$parent_rule.'$/\' => \'Urlrule/'.$action.'?catdir=:1\'';
				}
			}else{
				$urlrule='\'/^'.$rule.'$/\' => \'Urlrule/'.$action.'?'.$request.$MOREREQUEST.'\'';
				$data = str_replace('\/$','$',$urlrule);
			}
		}
		return $data;
}

function routes_cache($URL_URLRULE=''){

			$urlstr .=  '\':l'.C('URL_PATHINFO_DEPR').'Tags'.C('URL_PATHINFO_DEPR').':module'.C('URL_PATHINFO_DEPR').':tag'.C('URL_PATHINFO_DEPR').':p\' => \'Home/Tags/index\','."\n";
			$urlstr .=  '\':l'.C('URL_PATHINFO_DEPR').'Tags'.C('URL_PATHINFO_DEPR').':tag'.C('URL_PATHINFO_DEPR').':p\' => \'Home/Tags/index\','."\n";
			$urlstr .=  '\':l'.C('URL_PATHINFO_DEPR').'Tags'.C('URL_PATHINFO_DEPR').':module'.C('URL_PATHINFO_DEPR').':tag\' => \'Home/Tags/index\','."\n";
			$urlstr .=  '\':l'.C('URL_PATHINFO_DEPR').'Tags'.C('URL_PATHINFO_DEPR').':p\d\' => \'Home/Tags/index\','."\n";
			$urlstr .=  '\':l'.C('URL_PATHINFO_DEPR').'Tags'.C('URL_PATHINFO_DEPR').':tag\' => \'Home/Tags/index\','."\n";
			$urlstr .=  '\':l'.C('URL_PATHINFO_DEPR').'Tags\' => \'Home/Tags/index\','."\n";

			$urlstr .=  '\'Tags'.C('URL_PATHINFO_DEPR').':module'.C('URL_PATHINFO_DEPR').':tag'.C('URL_PATHINFO_DEPR').':p\' => \'Home/Tags/index\','."\n";
			$urlstr .=  '\'Tags'.C('URL_PATHINFO_DEPR').':tag'.C('URL_PATHINFO_DEPR').':p\' => \'Home/Tags/index\','."\n";
			$urlstr .=  '\'Tags'.C('URL_PATHINFO_DEPR').':module'.C('URL_PATHINFO_DEPR').':tag\' => \'Home/Tags/index\','."\n";
			$urlstr .=  '\'Tags'.C('URL_PATHINFO_DEPR').':p\d\' => \'Home/Tags/index\','."\n";
			$urlstr .=  '\'Tags'.C('URL_PATHINFO_DEPR').':tag\' => \'Home/Tags/index\','."\n";
			$urlstr .=  '\'Tags\' => \'Home/Tags/index\','."\n";
			/*
			$urlstr .=  '\'^Tags$\' => \'Home/Tags/index\','."\n";
			$urlstr .=  '\'/^Tags\/(\d+).html$/\' => \'Home/Tags/index?p=:1\','."\n";
			*/
			if(APP_LANG){
				$Lang=F('Lang');
				foreach((array)$Lang as $key =>$r){$langarr[]=$key;}
				$urlstr .=  '\'/^('.implode('|',$langarr).')$/\' => \'Index/index?l=:1\','."\n";
			}
			
		

			$URL_URLRULE = $URL_URLRULE ? $URL_URLRULE : C('URL_URLRULE'); 
			$urlrule = is_array($URL_URLRULE) ?  $URL_URLRULE : explode(':::',$URL_URLRULE);
			$list=explode('|',$urlrule[1]);
			$show=explode('|',$urlrule[0]);
			$listurls[]= make_urlrule($show[1],$langarr,'show');
			$listurls[]= make_urlrule($show[0],$langarr,'show');
			$listurls[]= make_urlrule($list[1],$langarr,'index');
			$listurls[]= make_urlrule($list[0],$langarr,'index');		
			
			$url = implode(",\n",$listurls);
			file_put_contents(DATA_PATH.'Routes.php', "<?php\nreturn array(\n" . $urlstr.$url . "\n);\n?>");
			if(is_file(RUNTIME_PATH.'~runtime.php'))@unlink(RUNTIME_PATH.'~runtime.php');
			if(is_file(RUNTIME_PATH.'~allinone.php'))@unlink(RUNTIME_PATH.'~allinone.php');
}

function HOMEURL($lang){
	if(C('URL_M')==1)$index='/index.php/';
	$lang= C('URL_LANG')!=$lang ? $lang : '';
	if(C('URL_M') > 0){
		$url =$lang ? __ROOT__.$index.$lang.'/' :  __ROOT__.'/';
	}else{	
		if(C('HOME_ISHTML')){
			$url = $lang ? '/'.$lang.'/' : '/';
		}else{
			$url =$lang ?  __ROOT__.'/index.php?l='.$lang :  __ROOT__.'/';
		}
	}
	return $url;
}
function URL($url='',$params=array()) { 
	
	if(APP_LANG)$lang = getlang();

	if(!empty($url)){
		list($path, $query) = explode('?',$url);
		list($group, $a) = explode('/',$path);
		list($g, $m) = explode('-',$group);
		$params= http_build_query($params);
		$params = !empty($params) ? '&' . $params : '';
		$query =  !empty($query) ? '&'.$query : '';
		//parse_str($_SERVER['QUERY_STRING'],$urlarr);	
		if($lang) $langurl = '&l='.$lang;
		if (strcasecmp($g,'Home')== 0){
			$url = __ROOT__.'/index.php?m='.$m.'&a='.$a.$query.$params.$langurl;
		}else{
			$url = __ROOT__.'/index.php?g='.$g.'&m='.$m.'&a='.$a.$query.$params.$langurl;
		}
	}else{
		if(C('URL_M')==1)$index='/index.php/';
		if(C('URL_M') > 0){
			$url = $lang ? __ROOT__.$index.$lang.'/' :  __ROOT__.'/';
		}else{
			$url = $lang ? __ROOT__.'/index.php?l='.$lang :  __ROOT__.'/';
		}
	}
	return $url;
}



function TAGURL($data,$p=''){
	$index= C('URL_M')==1 ? __ROOT__.'index.php/' : __ROOT__.'/';
	if(APP_LANG)$lang=getlang();
	if(C('URL_M')==0){
			if($data['moduleid'] > 0 && $data['moduleid']!=2) $params['moduleid']=$data['moduleid'] ;
			if($data['slug']) $params['tag']=$data['slug'] ;
			if($lang)$params['l']=$lang;			
			$url=URL('Home-Tags/index',$params);
			if($p)$url=$url.'&p={$page}';
	}else{
			$tag = $data['slug'] ? '/'.$data['slug'] : '';
			$module = ($data['moduleid'] > 0 && $data['moduleid']!=2) ?  '/'.$data['module'] : ''; 
			$langurl = $lang ? $lang.'/' : '' ;
			$url=$index.$langurl.'Tags'.$module.$tag.'/';
			if($p)$url=$url.'{$page}'.C('URL_HTML_SUFFIX');
	}
	return $url;
}
	 
function getlang($have=''){
	if($have){
		if(strcasecmp(GROUP_NAME,'Admin')== 0)
			$lang =  LANG_NAME;
		else
			$lang =  $_REQUEST['l'] ? $_REQUEST['l'] : C('URL_LANG');
	}else{
		if(strcasecmp(GROUP_NAME,'Admin')== 0)
			$lang =  C('URL_LANG')!= LANG_NAME ? LANG_NAME : '';
		else
			$lang = $_REQUEST['l'] && C('URL_LANG')!=$_REQUEST['l'] ? $_REQUEST['l'] : '';
	}
	return $lang;
}

function geturl($cat,$data='',$Urlrule=''){
		//$Urlrule =F('Urlrule');
		$id=$data['id']; 
		$URL_MODEL =C('URL_M');
		if(APP_LANG)$lang = getlang();

		$parentdir = $cat['parentdir'];
		$catdir = $cat['catdir'];
		$year = date('Y',$data['createtime']);
		$month = date('m',$data['createtime']);
		$day = date('d',$data['createtime']);
		$module = $cat['module'];
		$moduleid =$cat['moduleid'];
		$catid=$cat['id'];
		
		if($cat['ishtml']){
			if($cat['urlruleid'] && $Urlrule){
				$showurlrule = $Urlrule[$cat['urlruleid']]['showurlrule'];
				$listurlrule = $Urlrule[$cat['urlruleid']]['listurlrule'];
			}else{
				echo 'This cat has not urlruleid or no Urlrule.';exit;
			}
		}else{
			if($URL_MODEL==0){
				$langurl = $lang ?  '&l='.LANG_NAME : '';
				if($id){
					$url[] = U("Home/$cat[module]/show?id=$id".$langurl);
					$url[] = U("Home/$cat[module]/show?id=".$id.$langurl.'&'.C('VAR_PAGE').'={$page}');	
				}else{
					$url[] = U("Home/$cat[module]/index?id=$cat[id]".$langurl);
					$url[] = U("Home/$cat[module]/index?id=$cat[id]$langurl&".C('VAR_PAGE').'={$page}');
				}
				$urls = str_replace('g=Admin&','',$url);
				$urls = str_replace('g=Home&','',$url);
			}else{
				$urlrule = explode(':::',C('URL_URLRULE'));
				$showurlrule = $urlrule[0];
				$listurlrule = $urlrule[1];
			}
		}
		if(empty($urls)){
			$index =  $URL_MODEL==1 ? __ROOT__.'/index.php/' : __ROOT__.'/';
			$langurl = $lang ? $lang.'/' : '';
			if($id){
				$urls = str_replace(array('{$parentdir}','{$module}','{$moduleid}','{$catdir}','{$year}','{$month}','{$day}','{$catid}','{$id}'),array($parentdir,$module,$moduleid,$catdir,$year,$month,$day,$catid,$id),$showurlrule);
			}else{
				$urls = str_replace(array('{$parentdir}','{$module}','{$moduleid}','{$catdir}','{$year}','{$month}','{$day}','{$catid}','{$id}'),array($parentdir,$module,$moduleid,$catdir,$year,$month,$day,$catid,$id),$listurlrule);
			}
			$urls = explode('|',$urls);
			$urls[0]=$index.$langurl.$urls[0];
			$urls[1]=$index.$langurl.$urls[1];		
		}
		return $urls;
}


function content_pages($num, $p,$pageurls) {

	$multipage = '';
	$page = 11;
	$offset = 4;
	$pages = $num;
	$from = $p - $offset;
	$to = $p + $offset;
	$more = 0;
	if($page >= $pages) {
		$from = 2;
		$to = $pages-1;
	} else {
		if($from <= 1) {
			$to = $page-1;
			$from = 2;
		} elseif($to >= $pages) {
			$from = $pages-($page-2);
			$to = $pages-1;
		}
		$more = 1;
	}
	if($p>0) {
		$perpage = $p == 1 ? 1 : $p-1;
		if($perpage==1){
			$multipage .= '<a class="a1" href="'.$pageurls[$perpage][0].'">'.L('previous').'</a>';
		}else{
			$multipage .= '<a class="a1" href="'.$pageurls[$perpage][1].'">'.L('previous').'</a>';
		}
		if($p==1) {
			$multipage .= ' <span>1</span>';
		} elseif($p>6 && $more) {
			$multipage .= ' <a href="'.$pageurls[1][0].'">1</a>..';
		} else {
			$multipage .= ' <a href="'.$pageurls[1][0].'">1</a>';
		}
	}
	for($i = $from; $i <= $to; $i++) {
		if($i != $p) {
			$multipage .= ' <a href="'.$pageurls[$i][1].'">'.$i.'</a>';
		} else {
			$multipage .= ' <span>'.$i.'</span>';
		}
	}
	if($p<$pages) {
		if($p<$pages-5 && $more) {
			$multipage .= ' ..<a href="'.$pageurls[$pages][1].'">'.$pages.'</a> <a class="a1" href="'.$pageurls[$p+1][1].'">'.L('next').'</a>';
		} else {
			$multipage .= ' <a href="'.$pageurls[$pages][1].'">'.$pages.'</a> <a class="a1" href="'.$pageurls[$p+1][1].'">'.L('next').'</a>';
		}
	} elseif($p==$pages) {
		$multipage .= ' <span>'.$pages.'</span> <a class="a1" href="'.$pageurls[$p][1].'">'.L('next').'</a>';
	}
	return $multipage;
}

function thumb($f, $tw=300, $th=300 ,$autocat=0, $nopic = 'nopic.jpg',$t=''){
	if(strstr($f,'://')) return $f;
	if(empty($f)) return __ROOT__.'/Public/Images/'.$nopic;
	$f= '.'.str_replace(__ROOT__,'',$f);
	
	$temp = array(1=>'gif', 2=>'jpeg', 3=>'png');
	list($fw, $fh, $tmp) = getimagesize($f);
	if(empty($t)){
		if($fw>$tw && $fh>$th){
			$pathinfo = pathinfo($f);
			$t = $pathinfo['dirname'].'/thumb_'.$tw.'_'.$th.'_'.$pathinfo['basename'];
			if(is_file($t)){
				return  __ROOT__.substr($t,1);
			}
		}else{
			return  __ROOT__.substr($f,1);
		}		
	}
	
	if(!$temp[$tmp]){	return false; }

	if($autocat){
		if($fw/$tw > $fh/$th){
		$fw = $tw * ($fh/$th);
		}else{
		$fh = $th * ($fw/$tw);
		}
	}else{

		 $scale = min($tw/$fw, $th/$fh); // 计算缩放比例
            if($scale>=1) {
                // 超过原图大小不再缩略
                $tw   =  $fw;
                $th  =  $fh;
            }else{
                // 缩略图尺寸
                $tw  = (int)($fw*$scale);
                $th = (int)($fh*$scale);
            }


	}

	$tmp = $temp[$tmp];
	$infunc = "imagecreatefrom$tmp";
	$outfunc = "image$tmp";
	$fimg = $infunc($f);

	if($tmp != 'gif' && function_exists('imagecreatetruecolor')){
		$timg = imagecreatetruecolor($tw, $th);
	}else{
		$timg = imagecreate($tw, $th);
	}


	if(function_exists('imagecopyresampled'))
		imagecopyresampled($timg, $fimg, 0,0, 0,0, $tw,$th, $fw,$fh);
	else
		imagecopyresized($timg, $fimg, 0,0, 0,0, $tw,$th, $fw,$fh);
	if($tmp=='gif' || $tmp=='png') {
		$background_color  =  imagecolorallocate($timg,  0, 255, 0);  //  指派一个绿色
		imagecolortransparent($timg, $background_color);  //  设置为透明色，若注释掉该行则输出绿色的图
	}
	$outfunc($timg, $t);
	imagedestroy($timg);
	imagedestroy($fimg);
	return  __ROOT__.substr($t,1);
}




/*!
 * ubb2html support for php
 * @requires xhEditor
 * 
 * @author Yanis.Wang<yanis.wang@gmail.com>
 * @site http://xheditor.com/
 * @licence LGPL(http://www.opensource.org/licenses/lgpl-license.php)
 * 
 * @Version: 0.9.10 (build 110801)
 */
function ubb2html($sUBB)
{	
	$sHtml=$sUBB;
	
	global $emotPath,$cnum,$arrcode,$bUbb2htmlFunctionInit;$cnum=0;$arrcode=array();
	$emotPath='../xheditor_emot/';//表情根路径
	
	if(!$bUbb2htmlFunctionInit){
	function saveCodeArea($match)
	{
		global $cnum,$arrcode;
		$cnum++;$arrcode[$cnum]=$match[0];
		return "[\tubbcodeplace_".$cnum."\t]";
	}}
	$sHtml=preg_replace_callback('/\[code\s*(?:=\s*((?:(?!")[\s\S])+?)(?:"[\s\S]*?)?)?\]([\s\S]*?)\[\/code\]/i','saveCodeArea',$sHtml);
	
	$sHtml=preg_replace("/&/",'&amp;',$sHtml);
	$sHtml=preg_replace("/</",'&lt;',$sHtml);
	$sHtml=preg_replace("/>/",'&gt;',$sHtml);
	$sHtml=preg_replace("/\r?\n/",'<br />',$sHtml);
	
	$sHtml=preg_replace("/\[(\/?)(b|u|i|s|sup|sub)\]/i",'<$1$2>',$sHtml);
	$sHtml=preg_replace('/\[color\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]/i','<span style="color:$1;">',$sHtml);
	if(!$bUbb2htmlFunctionInit){
	function getSizeName($match)
	{
		$arrSize=array('10px','13px','16px','18px','24px','32px','48px');
		if(preg_match("/^\d+$/",$match[1]))$match[1]=$arrSize[$match[1]-1];
		return '<span style="font-size:'.$match[1].';">';
	}}
	$sHtml=preg_replace_callback('/\[size\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]/i','getSizeName',$sHtml);
	$sHtml=preg_replace('/\[font\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]/i','<span style="font-family:$1;">',$sHtml);
	$sHtml=preg_replace('/\[back\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]/i','<span style="background-color:$1;">',$sHtml);
	$sHtml=preg_replace("/\[\/(color|size|font|back)\]/i",'</span>',$sHtml);
	
	for($i=0;$i<3;$i++)$sHtml=preg_replace('/\[align\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\](((?!\[align(?:\s+[^\]]+)?\])[\s\S])*?)\[\/align\]/','<p align="$1">$2</p>',$sHtml);
	$sHtml=preg_replace('/\[img\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/img\]/i','<img src="$1" alt="" />',$sHtml);
	if(!$bUbb2htmlFunctionInit){
	function getImg($match)
	{
		$alt=$match[1];$p1=$match[2];$p2=$match[3];$p3=$match[4];$src=$match[5];
		$a=$p3?$p3:(!is_numeric($p1)?$p1:'');
		return '<img src="'.$src.'" alt="'.$alt.'"'.(is_numeric($p1)?' width="'.$p1.'"':'').(is_numeric($p2)?' height="'.$p2.'"':'').($a?' align="'.$a.'"':'').' />';
	}}
	$sHtml=preg_replace_callback('/\[img\s*=([^,\]]*)(?:\s*,\s*(\d*%?)\s*,\s*(\d*%?)\s*)?(?:,?\s*(\w+))?\s*\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*)?\s*\[\/img\]/i','getImg',$sHtml);
	if(!$bUbb2htmlFunctionInit){
	function getEmot($match)
	{
		global $emotPath;
		$arr=split(',',$match[1]);
		if(!isset($arr[1])){$arr[1]=$arr[0];$arr[0]='default';}
		$path=$emotPath.$arr[0].'/'.$arr[1].'.gif';
		return '<img src="'.$path.'" alt="'.$arr[1].'" />';
	}}
	$sHtml=preg_replace_callback('/\[emot\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\/\]/i','getEmot',$sHtml);
	$sHtml=preg_replace('/\[url\]\s*(((?!")[\s\S])*?)(?:"[\s\S]*?)?\s*\[\/url\]/i','<a href="$1">$1</a>',$sHtml);
	$sHtml=preg_replace('/\[url\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]\s*([\s\S]*?)\s*\[\/url\]/i','<a href="$1">$2</a>',$sHtml);
	$sHtml=preg_replace('/\[email\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/email\]/i','<a href="mailto:$1">$1</a>',$sHtml);
	$sHtml=preg_replace('/\[email\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]\s*([\s\S]+?)\s*\[\/email\]/i','<a href="mailto:$1">$2</a>',$sHtml);
	$sHtml=preg_replace("/\[quote\]([\s\S]*?)\[\/quote\]/i",'<blockquote>$1</blockquote>',$sHtml);
	if(!$bUbb2htmlFunctionInit){
	function getFlash($match)
	{
		$w=$match[1];$h=$match[2];$url=$match[3];
		if(!$w)$w=480;if(!$h)$h=400;
		return '<embed type="application/x-shockwave-flash" src="'.$url.'" wmode="opaque" quality="high" bgcolor="#ffffff" menu="false" play="true" loop="true" width="'.$w.'" height="'.$h.'" />';
	}}
	$sHtml=preg_replace_callback('/\[flash\s*(?:=\s*(\d+)\s*,\s*(\d+)\s*)?\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/flash\]/i','getFlash',$sHtml);
	if(!$bUbb2htmlFunctionInit){
	function getMedia($match)
	{
		$w=$match[1];$h=$match[2];$play=$match[3];$url=$match[4];
		if(!$w)$w=480;if(!$h)$h=400;
		return '<embed type="application/x-mplayer2" src="'.$url.'" enablecontextmenu="false" autostart="'.($play=='1'?'true':'false').'" width="'.$w.'" height="'.$h.'" />';
	}}
	$sHtml=preg_replace_callback('/\[media\s*(?:=\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*(\d+)\s*)?)?\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/media\]/i','getMedia',$sHtml);
	if(!$bUbb2htmlFunctionInit){
	function getTable($match)
	{
		return '<table'.(isset($match[1])?' width="'.$match[1].'"':'').(isset($match[2])?' bgcolor="'.$match[2].'"':'').'>';
	}}
	$sHtml=preg_replace_callback('/\[table\s*(?:=(\d{1,4}%?)\s*(?:,\s*([^\]"]+)(?:"[^\]]*?)?)?)?\s*\]/i','getTable',$sHtml);
	if(!$bUbb2htmlFunctionInit){
	function getTR($match){return '<tr'.(isset($match[1])?' bgcolor="'.$match[1].'"':'').'>';}}
	$sHtml=preg_replace_callback('/\[tr\s*(?:=(\s*[^\]"]+))?(?:"[^\]]*?)?\s*\]/i','getTR',$sHtml);
	if(!$bUbb2htmlFunctionInit){
	function getTD($match){
		$col=isset($match[1])?$match[1]:0;$row=isset($match[2])?$match[2]:0;$w=isset($match[3])?$match[3]:null;
		return '<td'.($col>1?' colspan="'.$col.'"':'').($row>1?' rowspan="'.$row.'"':'').($w?' width="'.$w.'"':'').'>';
	}}
	$sHtml=preg_replace_callback("/\[td\s*(?:=\s*(\d{1,2})\s*,\s*(\d{1,2})\s*(?:,\s*(\d{1,4}%?))?)?\s*\]/i",'getTD',$sHtml);
	$sHtml=preg_replace("/\[\/(table|tr|td)\]/i",'</$1>',$sHtml);
	$sHtml=preg_replace("/\[\*\]((?:(?!\[\*\]|\[\/list\]|\[list\s*(?:=[^\]]+)?\])[\s\S])+)/i",'<li>$1</li>',$sHtml);
	if(!$bUbb2htmlFunctionInit){
	function getUL($match)
	{
		$str='<ul';
		if(isset($match[1]))$str.=' type="'.$match[1].'"';
		return $str.'>';
	}}
	$sHtml=preg_replace_callback('/\[list\s*(?:=\s*([^\]"]+))?(?:"[^\]]*?)?\s*\]/i','getUL',$sHtml);
	$sHtml=preg_replace("/\[\/list\]/i",'</ul>',$sHtml);
	$sHtml=preg_replace("/\[hr\/\]/i",'<hr />',$sHtml);

	for($i=1;$i<=$cnum;$i++)$sHtml=str_replace("[\tubbcodeplace_".$i."\t]", $arrcode[$i],$sHtml);

	if(!$bUbb2htmlFunctionInit){
	function fixText($match)
	{
		$text=$match[2];
		$text=preg_replace("/\t/",'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$text);
		$text=preg_replace("/ /",'&nbsp;',$text);
		return $match[1].$text;
	}}
	$sHtml=preg_replace_callback('/(^|<\/?\w+(?:\s+[^>]*?)?>)([^<$]+)/i','fixText',$sHtml);
	
	$bUbb2htmlFunctionInit=true;
	
	return $sHtml;
}


function Pinyin($_String) {
$_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha".
   "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|".
   "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er".
   "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui".
   "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang".
   "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang".
   "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue".
   "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne".
   "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen".
   "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang".
   "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|".
   "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|".
   "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu".
   "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you".
   "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|".
   "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
$_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990".
   "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725".
   "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263".
   "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003".
   "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697".
   "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211".
   "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922".
   "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468".
   "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664".
   "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407".
   "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959".
   "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652".
   "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369".
   "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128".
   "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914".
   "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645".
   "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149".
   "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087".
   "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658".
   "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340".
   "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888".
   "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585".
   "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847".
   "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055".
   "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780".
   "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274".
   "|-10270|-10262|-10260|-10256|-10254";
$_TDataKey   = explode('|', $_DataKey);
$_TDataValue = explode('|', $_DataValue);
$_Data =  array_combine($_TDataKey, $_TDataValue);
arsort($_Data);
reset($_Data);
$_String= auto_charset($_String,'utf-8','gbk');
$_Res = '';
for($i=0; $i<strlen($_String); $i++) {
      $_P = ord(substr($_String, $i, 1));
      if($_P>160) { $_Q = ord(substr($_String, ++$i, 1)); $_P = $_P*256 + $_Q - 65536; }
      $_Res .= _Pinyin($_P, $_Data);
}
return preg_replace("/[^a-z0-9]*/", '', $_Res);
}

// 自动转换字符集 支持数组转换
function auto_charset($fContents, $from='gbk', $to='utf-8') {
    $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
    $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
    if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    if (is_string($fContents)) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($fContents, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } elseif (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if ($key != $_key)
                unset($fContents[$key]);
        }
        return $fContents;
    }
    else {
        return $fContents;
    }
}

function _Pinyin($_Num, $_Data) {
   if    ($_Num>0      && $_Num<160   ) return chr($_Num);
   elseif($_Num<-20319 || $_Num>-10247) return '';
   else {
        foreach($_Data as $k=>$v){ if($v<=$_Num) break; }
        return $k;
   }
}

function return_url($code){		
		$config = APP_LANG ?  F('Config_'.LANG_NAME) :  F('Config');
		return $config['site_url'].'/index.php?g=User&m=Pay&a=respond&code='.$code;
}
function order_pay_status($sn,$value){
	$cart['status'] =1;
	$cart['pay_status'] =$value;
	if($value==2)$cart['pay_time'] =time();
	$r = M('Order')->where("sn='{$sn}'")->save($cart);
	return $r;
}

function addslashes_array($array){
	if(get_magic_quotes_gpc())return $array;
	if(!is_array($array))return addslashes($array);
	foreach($array as $k => $val) $array[$k] = addslashes_array($val);
	return $array;
}

function stripslashes_array($array) {
	if(!is_array($array)) return stripslashes($array);
	foreach($array as $k => $val) $array[$k] = stripslashes_array($val);
	return $array;
}

function htmlspecialchars_array($array) {
	if(!is_array($array)) return htmlspecialchars($array,ENT_QUOTES);
	foreach($array as $k => $val) $array[$k] = htmlspecialchars_array($val);
	return $array;
}
 
function safe_replace($string) {
	$string = trim($string);
	$string = str_replace(array('\\',';','\'','%2527','%27','%20','&', '"', '<', '>'), array('','','','','','','&amp;', '&quot;', '&lt;', '&gt;'), $string);
	$string=nl2br($string); 
	return $string;
}	
function get_safe_replace($array){
	if(!is_array($array)) return safe_replace(strip_tags($array));
	foreach($array as $k => $val) $array[$k] = get_safe_replace($val);
	return $array;
}

function  sql_split($sql,$tablepre) {

	if($tablepre != "yourphp_") $sql = str_replace("yourphp_", $tablepre, $sql);
	//$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8",$sql);
	
	if($r_tablepre != $s_tablepre) $sql = str_replace($s_tablepre, $r_tablepre, $sql);
	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query)
	{
		$ret[$num] = '';
		$queries = explode("\n", trim($query));
		$queries = array_filter($queries);
		foreach($queries as $query)
		{
			$str1 = substr($query, 0, 1);
			if($str1 != '#' && $str1 != '-') $ret[$num] .= $query;
		}
		$num++;
	}
	return $ret;
}
?>