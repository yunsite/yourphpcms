<?php
/**
 * 
 * Form.php (模型表单生成)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
class Form extends Think {
	public $data = array() ,$isadmin=1,$doThumb=1,$doAttach=1,$lang;

    public function __construct($data=array()) {
         $this->data = $data;
		 if(APP_LANG)$this->lang = LANG_NAME;
    }
 
	public function catid($info,$value){
        $validate = getvalidate($info);		
		if(APP_LANG){ 
			$Category = F('Category_'.$this->lang);			
		}else{
			$Category = F('Category');		
		}
		$id = $field = $info['field'];
		$value = $value ? $value : $this->data[$field];
		$moduleid =$info['moduleid'];
		foreach ($Category as $r){
				$postgroup = explode(',',$r['postgroup']);
				if( ($this->isadmin && $_SESSION['groupid']!=1 && !in_array($_SESSION['groupid'],$postgroup)) ||  (empty($this->isadmin) && !in_array( cookie('groupid'),$postgroup)) ) continue;
				//if($r['type']==1) continue;
				$arr= explode(",",$r['arrchildid']);
				$show=0;
				foreach((array)$arr as $rr){
					if($Category[$rr]['moduleid'] ==$moduleid) $show=1;
				}
				if(empty($show))continue; 
				$r['disabled'] = $r['child'] ? ' disabled' :'';
				$array[] = $r;					
		}
		import ( '@.ORG.Tree' );
		$str  = "<option value='\$id' \$disabled \$selected>\$spacer \$catname</option>";
		$tree = new Tree ($array);
		$parseStr .= '<select  id="'.$id.'" name="'.$field.'" class=" '.$info['class'].'"  '.$validate.'>';
		$parseStr .= '<option value="">'.L('please_chose').'</option>';
		$parseStr .= $tree->get_tree(0, $str, $value);
		$parseStr .= '</select>';
		return $parseStr;
	}


	public function title($info,$value){
		$info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
		$thumb=$info['setup']['thumb'];
		$style=$info['setup']['style'];
		$id = $field = $info['field'];
	    $validate = getvalidate($info);
		$value = $value ? $value : $this->data[$field];

		$title_style = explode(';',$this->data['title_style']);
		$style_color = explode(':',$title_style[0]);
		$style_color = $style_color[1];
		$style_bold = explode(':',$title_style[1]);
		$style_bold = $style_bold[1];

		if(empty($info['setup']['upload_maxsize'])){

			if(APP_LANG){ 
				$Config = F('Config_'.$this->lang);			
			}else{
				$Config = F('Config');		
			}
 
			$info['setup']['upload_maxsize'] =  intval(byte_format($Config['attach_maxsize']));
		}

	 
		$yourphp_auth_key = sysmd5(C('ADMIN_ACCESS').$_SERVER['HTTP_USER_AGENT']);
		$yourphp_auth = authcode($this->isadmin.'-1-1-1-jpeg,jpg,png,gif-'.$info['setup']['upload_maxsize'].'-'.$info['moduleid'], 'ENCODE',$yourphp_auth_key);

		$thumb_ico = $this->data['thumb']? $this->data['thumb'] : __ROOT__.'/Public/Images/admin_upload_thumb.png';
		$boldchecked= $style_bold=='bold' ? 'checked' : '';
		$thumbstr ='<div class="thumb_box"  id="thumb_box"><div id="thumb_aid_box"></div>
				<a href="javascript:swfupload(\'thumb_uploadfile\',\'thumb\',\''.L('uploadfiles').'\','.$this->isadmin.',1,1,1,\'jpeg,jpg,png,gif\','.$info['setup']['upload_maxsize'].','.$info['moduleid'].',\''.$yourphp_auth.'\',yesdo,nodo)"><img src="'.$thumb_ico.'" id="thumb_pic" ></a><br> <input type="button" value="'.L('clean_thumb').'" onclick="javascript:clean_thumb(\'thumb\');" class="button" />
				<input type="hidden"  id="thumb" name="thumb"  value="'.$this->data['thumb'].'" /></div>';

		$parseStr   = '<input type="text"   class="input-text input-title f_l" name="'.$field.'"  id="'.$id.'" value="'.$value.'" size="'.$info['setup']['size'].'"  '.$validate.'  /> ';

		$stylestr = '<div id="'.$id.'_colorimg" class="colorimg" style="background-color:'.$style_color.'"><img src="__PUBLIC__/Images/admin_color_arrow.gif"></div><input type="hidden" id="'.$id.'_style_color" name="style_color" value="'.$style_color.'" /><input type="checkbox" class="style_bold" id="style_bold" name="style_bold" value="bold" '.$boldchecked.' /><b>'. L('style_bold').'</b><script>$.showcolor("'.$id.'_colorimg","'.$id.'_style_color");</script>';
		if($thumb &&  $this->doThumb)$parseStr = $thumbstr.$parseStr;
		if($style) $parseStr = $parseStr.$stylestr;
		return $parseStr;
	}

	public function text($info,$value){
		$info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
		$id = $field = $info['field'];
	    $validate = getvalidate($info);
		$info['setup']['ispassword'] ? $inputtext = 'password' : $inputtext = 'text';
        if(ACTION_NAME=='add'){
			$value = $value ? $value : $info['setup']['default'];
        }else{
			$value = $value ? $value : $this->data[$field];
        }
		$parseStr   = '<input type="'.$inputtext.'"   class="input-text '.$info['class'].'" name="'.$field.'"  id="'.$id.'" value="'.stripcslashes($value).'" size="'.$info['setup']['size'].'"  '.$validate.'/> ';
		return $parseStr;
	}



	public function verify($info,$value){
		$info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
		$id = $field = $info['field'];
	    $validate = getvalidate($info);
		$parseStr   = '<input   class="input-text '.$info['class'].'" name="'.$field.'"  id="'.$id.'" value="" size="'.$info['setup']['size'].'"  '.$validate.' /><img src="'.URL('Home-Index/verify').'" onclick="javascript:resetVerifyCode();" class="checkcode" align="absmiddle"  title="点击刷新验证码" id="verifyImage"/>';
		return $parseStr;
	}




	public function number($info,$value){
		$info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
		$id = $field = $info['field'];
	    $validate = getvalidate($info);
		$info['setup']['ispassowrd'] ? $inputtext = 'passowrd' : $inputtext = 'text';
        if(ACTION_NAME=='add'){
			$value = $value ? $value : $info['setup']['default'];
        }else{
			$value = $value ? $value : $this->data[$field];
        }
		$parseStr   = '<input type="'.$inputtext.'"   class="input-text '.$info['class'].'" name="'.$field.'"  id="'.$id.'" value="'.$value.'" size="'.$info['setup']['size'].'"  '.$validate.'/> ';
		return $parseStr;
	}

	public function textarea($info,$value){
		$info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
		$id = $field = $info['field'];
        $validate = getvalidate($info);
        if(ACTION_NAME=='add'){
			$value = $value ? $value : $info['setup']['default'];
        }else{
			$value = $value ? $value : $this->data[$field];
        }

		$parseStr   = '<textarea  class="'.$info['class'].'" name="'.$field.'"  rows="'.$info['setup']['rows'].'" cols="'.$info['setup']['cols'].'"  id="'.$id.'"   '.$validate.'/>'.stripcslashes($value).'</textarea>';
		return $parseStr;
	}


	public function select($info,$value){

		$info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
		$id = $field = $info['field'];
		$validate = getvalidate($info);
        if(ACTION_NAME=='add'){
			$value = $value ? $value : $info['setup']['default'];
        }else{
			$value = $value ? $value : $this->data[$field];
        }
        if($value != '') $value = strpos($value, ',') ? explode(',', $value) : $value;

        if(is_array($info['options'])){
             if($info['options_key']){
				$options_key=explode(',',$info['options_key']);
				foreach((array)$info['options'] as $key=>$res){
					if($options_key[0]=='key'){
						$optionsarr[$key]=$res[$options_key[1]];
					}else{
						$optionsarr[$res[$options_key[0]]]=$res[$options_key[1]];
					}
				}
			}else{
             $optionsarr = $info['options'];
			}
        }else{
            $options    = $info['setup']['options'];
            $options = explode("\n",$info['setup']['options']);
        	foreach($options as $r) {
        		$v = explode("|",$r);
        		$k = trim($v[1]);
        		$optionsarr[$k] = $v[0];
        	}
        }


        if(!empty($info['setup']['multiple'])) {
            $parseStr = '<select id="'.$id.'" name="'.$field.'"  onchange="'.$info['setup']['onchange'].'" class="'.$info['class'].'"  '.$validate.' size="'.$info['setup']['size'].'" multiple="multiple" >';
        }else {
        	$parseStr = '<select id="'.$id.'" name="'.$field.'" onchange="'.$info['setup']['onchange'].'"  class="'.$info['class'].'" '.$validate.'>';
        }

        if(is_array($optionsarr)) {
			foreach($optionsarr as $key=>$val) {
				if(!empty($value)){
				    $selected='';
					if($value==$key || in_array($key,$value)) $selected = ' selected="selected"';
				    $parseStr   .= '<option '.$selected.' value="'.$key.'">'.$val.'</option>';
				}else{
					$parseStr   .= '<option value="'.$key.'">'.$val.'</option>';
				}
			}
		}
        $parseStr   .= '</select>';
        return $parseStr;
	}
	public function checkbox($info,$value){
	     
		$info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
		$id = $field = $info['field'];
		$validate = getvalidate($info);
		if(ACTION_NAME=='add'){
			$value = $value ? $value : $info['setup']['default'];
        }else{
			$value = $value ? $value : $this->data[$field];
        }
        $labelwidth = $info['setup']['labelwidth'];


        if(is_array($info['options'])){
			if($info['options_key']){
				$options_key=explode(',',$info['options_key']);
				foreach((array)$info['options'] as $key=>$res){
					if($options_key[0]=='key'){
						$optionsarr[$key]=$res[$options_key[1]];
					}else{
						$optionsarr[$res[$options_key[0]]]=$res[$options_key[1]];
					}
				}
			}else{
             $optionsarr = $info['options'];
			}
        }else{
            $options    = $info['setup']['options'];
            $options = explode("\n",$info['setup']['options']);
        	foreach($options as $r) {
        		$v = explode("|",$r);
        		$k = trim($v[1]);
        		$optionsarr[$k] = $v[0];
        	}
        }
		if($value != '') $value = (strpos($value, ',') && !is_array($value)) ? explode(',', $value) :  $value ;
		$value = is_array($value) ? $value : array($value);
		$i = 1;
		$onclick = $info['setup']['onclick'] ? ' onclick="'.$info['setup']['onclick'].'" ' : '' ;

		foreach($optionsarr as $key=>$r) {
			$key = trim($key);
            if($i>1) $validate='';
			$checked = ($value && in_array($key, $value)) ? 'checked' : '';
			if($labelwidth) $parseStr .= '<label style="float:left;width:'.$labelwidth.'px" class="checkbox_'.$id.'" >';
			$parseStr .= '<input type="checkbox" class="input_checkbox '.$info['class'].'" name="'.$field.'[]" id="'.$id.'_'.$i.'" '.$checked.$onclick.' value="'.htmlspecialchars($key).'"  '.$validate.'> '.htmlspecialchars($r);
			if($labelwidth) $parseStr .= '</label>';
			$i++;
		}
		return $parseStr;

	}
	public function radio($info,$value){

       $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
		$id = $field = $info['field'];
		$validate = getvalidate($info);
		if(ACTION_NAME=='add'){
			$value = $value ? $value : $info['setup']['default'];
        }else{
			$value = $value ? $value : $this->data[$field];
        }
        $labelwidth = $info['setup']['labelwidth'];

        if(is_array($info['options'])){
             if($info['options_key']){
				$options_key=explode(',',$info['options_key']);
				foreach((array)$info['options'] as $key=>$res){
					if($options_key[0]=='key'){
						$optionsarr[$key]=$res[$options_key[1]];
					}else{
						$optionsarr[$res[$options_key[0]]]=$res[$options_key[1]];
					}
				}
			}else{
             $optionsarr = $info['options'];
			}
        }else{
            $options    = $info['setup']['options'];
            $options = explode("\n",$info['setup']['options']);
        	foreach($options as $r) {
        		$v = explode("|",$r);
        		$k = trim($v[1]);
        		$optionsarr[$k] = $v[0];
        	}
        }
		$onclick = $info['setup']['onclick'] ? ' onclick="'.$info['setup']['onclick'].'" ' : '' ;
        $i = 1;
        foreach($optionsarr as $key=>$r) {
            if($i>1) $validate ='';
			$checked = trim($value)==trim($key) ? 'checked' : '';
			if(empty($value) && empty($key) ) $checked = 'checked';
			if($labelwidth) $parseStr .= '<label style="float:left;width:'.$labelwidth.'px" class="checkbox_'.$id.'" >';
			$parseStr .= '<input type="radio" class="input_radio '.$info['class'].'" name="'.$field.'" id="'.$id.'_'.$i.'" '.$checked.$onclick.' value="'.$key.'" '.$validate.'> '.$r;
			if($labelwidth) $parseStr .= '</label>';
            $i++;
		}
		return $parseStr;
	}


	public function editor($info,$value){
		
		$info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
		$id = $field = $info['field'];
		$validate = getvalidate($info);
		if(ACTION_NAME=='add'){
			$value = $value ? $value : $info['setup']['default'];
        }else{
			$value = $value ? $value : $this->data[$field];
        }
		 //$value = stripslashes(htmlspecialchars_decode($value));
		 $textareaid = $field;
		 $toolbar = $info['setup']['toolbar'];
		 $moduleid = $info['moduleid'];
		 $height = $info['setup']['height'] ? $info['setup']['height'] : 300;
		 $flashupload = $info['setup']['flashupload']==1 ? 1 : '';
		 $alowuploadexts = $info['setup']['alowuploadexts'] ? $info['setup']['alowuploadexts'] :  'jpg,gif,png';
		 $alowuploadlimit=$info['setup']['alowuploadlimit'] ? $info['setup']['alowuploadlimit'] : 20 ;
		 $show_page=$info['setup']['showpage'];

		if(APP_LANG){ 
				$Config = F('Config_'.$this->lang);			
		}else{
				$Config = F('Config');		
		}
		$file_size = intval(byte_format($Config['attach_maxsize']));

		$yourphp_auth_key = sysmd5(C('ADMIN_ACCESS').$_SERVER['HTTP_USER_AGENT']);

		

		$attach_auth = authcode("$this->isadmin-1-0-$alowuploadlimit-$Config[attach_allowext]-$file_size-$moduleid", 'ENCODE',$yourphp_auth_key);
		$yourphp_auth = authcode("$this->isadmin-1-0-$alowuploadlimit-$alowuploadexts-$file_size-$moduleid", 'ENCODE',$yourphp_auth_key);

		$str ='';
		$str .= '<div class="editor_box"><div style="display:none;" id="'.$field.'_aid_box"></div><textarea name="'.$field.'" class="'.$info['class'].'"  id="'.$id.'"  boxid="'.$id.'" '.$validate.'  style="width:99%;height:'.$height.'px;visibility:hidden;">'.$value.'</textarea>';

		$show_page =  $show_page ?  1 :  0;
		//$info['setup']['edittype']='kindeditor';
		if($info['setup']['edittype']=='ckeditor'){
			if($toolbar == 'basic') {
				$toolbar = "['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink' ]\r\n";
			} elseif($toolbar == 'full') {
				$toolbar = "['Source',";
				$toolbar .= "'-','Templates'],
				['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
				['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],['ShowBlocks'],['Image','Flash'],['Maximize'],
				'/',
				['Bold','Italic','Underline','Strike','-'],
				['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
				['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
				['Link','Unlink','Anchor'],
				['Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
				'/',
				['Styles','Format','Font','FontSize'],
				['TextColor','BGColor'],\r\n";
			} elseif($toolbar == 'desc') {
				$toolbar = "['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', '-', 'Image', '-','Source'],\r\n";
			} else {
				$toolbar = '';
			}
			
			$str .= "<script type=\"text/javascript\">\r\n";
			$str .= "CKEDITOR.replace( '$textareaid',{";
			$str .= "height:{$height},isadmin:$this->isadmin,pages:$show_page,flashupload:$flashupload,textareaid:'".$textareaid."',iframeid:'swfupload',file_limit:$alowuploadlimit,file_types:'".$alowuploadexts."',moduleid:$moduleid,auth:'".$yourphp_auth."',file_size:$file_size,yesdo:'insert2editor',nodo:'nodo',\r\n";
			$str .= "toolbar :\r\n";
			$str .= "[\r\n";
			$str .= $toolbar;
			$str .= "]\r\n";
			$str .= "});\r\n";
			$str .= '</script>';	
			$str .='<div class=\'editor_bottom\'>';
			if($info['setup']['show_add_description']) $str .='<input type="checkbox" name="add_description" value="1" checked /> '.L('add_description').' 
				<input type="text" name="description_length" value="200" style="width:24px;" size="3" />'.L('description_length');
			if($info['setup']['show_auto_thumb']) $str .='<input type="checkbox" name="auto_thumb" value="1" checked /> '.L('auto_thumb').' 
				<input type="text" name="auto_thumb_no" value="1" size="1" />'.L('auto_thumb_no'); 
			$str .= '</div></div>';
		}elseif ($info['setup']['edittype']=='Xheditor'){
			

			if($toolbar=='basic'){
				$modtools = 'simple';
			} elseif($toolbar == 'full') {
				$modtools = $this->isadmin ? 'full' : 'mfull';
			} elseif($toolbar == 'desc') {
				$modtools = 'mini';
			} else {
				$modtools = '';
			} 

			$str .="<script type=\"text/javascript\" src=\"".__ROOT__."/Public/Xheditor/xheditor-zh-cn.min.js\"></script>";
			$str .="<script type=\"text/javascript\"> \r\n"; 
			$str .="var editor_".$id."=$('#".$id."').xheditor({ ";
			$str .="plugins:plugins,tools:'".$modtools."',loadCSS:'<style>pre{margin:12px;background:#EFEFEF;border:1px solid #ddd;border-left:3px solid #6CE26C;padding:10px;padding-top: 1px;}</style>',shortcuts:{'ctrl+enter':submitForm},width:\"100%\",height:\"$height\"";

			if($flashupload){
				$str .=",upLinkUrl:\"!".__ROOT__."/index.php?g=Admin&m=Attachment&a=index&isadmin=$this->isadmin&more=1&isthumb=0&file_limit=$alowuploadlimit&file_types=$Config[attach_allowext]&file_size=$file_size&moduleid=$moduleid&auth=$attach_auth&editorid=$id&immediate=1&l=$this->lang\"";
				$str .=",upImgUrl:\"!".__ROOT__."/index.php?g=Admin&m=Attachment&a=index&isadmin=$this->isadmin&more=1&isthumb=0&file_limit=$alowuploadlimit&file_types=$alowuploadexts&file_size=$file_size&moduleid=$moduleid&auth=$yourphp_auth&editorid=$id&immediate=1&l=$this->lang\"";
				
				$yourphp_auth = authcode("$this->isadmin-1-0-$alowuploadlimit-swf-$file_size-$moduleid", 'ENCODE',$yourphp_auth_key);
				$str .=",upFlashUrl:\"!".__ROOT__."/index.php?g=Admin&m=Attachment&a=index&isadmin=$this->isadmin&more=1&isthumb=0&file_limit=$alowuploadlimit&file_types=swf&file_size=$file_size&moduleid=$moduleid&auth=$yourphp_auth&editorid=$id&immediate=1&l=$this->lang\"";

				$yourphp_auth = authcode("$this->isadmin-1-0-$alowuploadlimit-mpg,wmv,avi,wma,mp3,mid,asf,rm-$file_size-$moduleid", 'ENCODE',$yourphp_auth_key);
				$str .=",upMediaUrl:\"!".__ROOT__."/index.php?g=Admin&m=Attachment&a=index&isadmin=$this->isadmin&more=1&isthumb=0&file_limit=$alowuploadlimit&file_types=mpg,wmv,avi,wma,mp3,mid,asf,rm&file_size=$file_size&moduleid=$moduleid&auth=$yourphp_auth&editorid=$id&immediate=1&l=$this->lang\"";

				$str .=",onUpload:upokis,modalWidth:\"600\",modalHeight:\"455\"";
			}

			$str .= "});</script>";

			$str .='<div class=\'editor_bottom1\'>';

			if($show_page) $str .='<a href="javascript:void(0);"  onclick="editor_'.$id.'.pasteText(\'[page]\');return false;">'.L('page_break').'</a>';
			if($info['setup']['show_add_description']) $str .='<input type="checkbox" class="input_radio" name="add_description" value="1" checked /> '.L('add_description').' <input type="text"  name="description_length" value="200" style="width:24px;" size="3" />'.L('description_length');
			if($info['setup']['show_auto_thumb']) $str .='<input type="checkbox" class="input_radio" name="auto_thumb" value="1" checked /> '.L('auto_thumb').'<input   type="text" name="auto_thumb_no" value="1" size="1" />'.L('auto_thumb_no'); 
			$str .= '</div></div>';

		}else{


			
			$upurl= __ROOT__."/index.php?g=Admin&m=Attachment&a=index&isadmin=$this->isadmin&more=1&isthumb=0&file_limit=$alowuploadlimit&file_types=$Config[attach_allowext]&file_size=$file_size&moduleid=$moduleid&auth=$attach_auth&l=$this->lang";

			$yourphp_auth = authcode("$this->isadmin-1-0-1-gif,jpg,jpeg,png,bmp-$file_size-$moduleid", 'ENCODE',$yourphp_auth_key);
			$upImgUrl =__ROOT__."/index.php?g=Admin&m=Attachment&a=index&isadmin=$this->isadmin&more=1&isthumb=0&file_limit=1&file_types=gif,jpg,jpeg,png,bmp&file_size=$file_size&moduleid=$moduleid&auth=$yourphp_auth&l=$this->lang";
				
			$yourphp_auth = authcode("$this->isadmin-1-0-1-swf,flv-$file_size-$moduleid", 'ENCODE',$yourphp_auth_key);
			$upFlashUrl=__ROOT__."/index.php?g=Admin&m=Attachment&a=index&isadmin=$this->isadmin&more=1&isthumb=0&file_limit=1&file_types=swf,flv&file_size=$file_size&moduleid=$moduleid&auth=$yourphp_auth&l=$this->lang";

			$yourphp_auth = authcode("$this->isadmin-1-0-1-mpg,wmv,avi,wma,mp3,mid,asf,rm,rmvb,wav,wma,mp4-$file_size-$moduleid", 'ENCODE',$yourphp_auth_key);
			$upMediaUrl=__ROOT__."/index.php?g=Admin&m=Attachment&a=index&isadmin=$this->isadmin&more=1&isthumb=0&file_limit=1&file_types=mpg,wmv,avi,wma,mp3,mid,asf,rm,rmvb,wav,wma,mp4&file_size=$file_size&moduleid=$moduleid&auth=$yourphp_auth&l=$this->lang";

			$str .="<script type=\"text/javascript\" src=\"".__ROOT__."/Public/Kindeditor/kindeditor-min.js\"></script>";
			$str .= "<script type=\"text/javascript\">\r\n";		
			$str .= "KindEditor.ready(function(K) {\r\n";
			$str .= "K.create('#".$id."', {\r\n";
			$str .= "cssPath : '".__ROOT__."/Public/Kindeditor/plugins/code/prettify.css',";
			//$str .= "uploadJson : '$upurl',";
			$str .= "fileManagerJson:'$upurl',";
			$str .= "editorid:'$id',";
			$str .= "upImgUrl:'$upImgUrl',";
			$str .= "upFlashUrl:'$upFlashUrl',";
			$str .= "upMediaUrl:'$upMediaUrl',";
			$str .= "allowFileManager : true\r\n";

			$str .= "});\r\n";
			$str .= "});\r\n";
			$str .= '</script>';
			$str .='<div  class=\'editor_bottom2\'>';
			if($info['setup']['show_add_description']) $str .='<input type="checkbox" name="add_description" value="1" checked /> '.L('add_description').' 
				<input type="text" name="description_length" value="200" style="width:24px;" size="3" />'.L('description_length');
			if($info['setup']['show_auto_thumb']) $str .='<input type="checkbox" name="auto_thumb" value="1" checked /> '.L('auto_thumb').' 
				<input type="text" name="auto_thumb_no" value="1" size="1" />'.L('auto_thumb_no'); 
			$str .= '</div></div>';
		}


		return $str;
	}
	public function datetime($info,$value){
		$info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
		$id = $field = $info['field'];
		$validate = getvalidate($info);
		if(ACTION_NAME=='add'){
			$value = $value ? $value : $info['setup']['default'];
        }else{
			$value = $value ? $value : $this->data[$field];
        }
		$value = $value ?  toDate($value,"Y-m-d H:i:s") : toDate(time(),"Y-m-d H:i:s");

		$parseStr = '<input  class="Wdate input-text  '.$info['class'].'"  '.$validate.'  name="'.$field.'" type="text" id="'.$id.'" size="25" onFocus="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'
		})" value="'.$value.'" />';
        return $parseStr;
	}
    public function groupid($info,$value){
        $newinfo = $info;
        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $groups=F('Role');$options=array();
        foreach($groups as $key=>$r) {
            if($r['status']){
                $options[$key]=$r['name'];
            }
		}
        $newinfo['options']=$options;
        $fun=$info['setup']['inputtype'];
        return $this->$fun($newinfo,$value);
    }
    public function posid($info,$value){
        $newinfo = $info;
        $posids=F('Posid');
        $options=array();
        $options[0]= L('please_chose');
        foreach($posids as $key=>$r) {
           $options[$key]=$r['name'];
		}
        $newinfo['options']=$options;
        $fun=$info['setup']['inputtype'];
        return $this->select($newinfo,$value);
    }

	public function typeid($info,$value){
        $newinfo = $info;
        $types=F('Type');
	
 
		$info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);	
		$id = $field = $info['field'];
		$value = $value ? $value : $this->data[$field];
		$parentid=$info['setup']['default'];
		$keyid = $types[$parentid]['keyid'];

		$options=array();
        $options[0]= L('please_chose');
		foreach((array)$types as $key => $r) {
			if($r['keyid']!=$keyid) continue;
			$r['id']=$r['typeid'];
			$array[] = $r;
			$options[$key]=$r['name'];
		}

		import ( '@.ORG.Tree' );
		$str  = "<option value='\$typeid' \$selected>\$spacer \$name</option>";
		$tree = new Tree ($array);		 
		$tree->nbsp='&nbsp;&nbsp;';
		$select_type = $tree->get_tree(0, $str,$value);
		
		$fun=$info['setup']['inputtype'];
		if($fun=='select'){
			return '<SELECT  id="'.$id.'" class="'.$info['class'].'"   name="'.$field.'"><option value="0">'.L('please_chose').'</option>'. $select_type.'</select>';
		}else{			
			$newinfo['options']=$options;			
			return $this->$fun($newinfo,$value);
		}
    }

    public function template($info,$value){

        $templates= template_file(MODULE_NAME);
        $newinfo = $info;
        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $options=array();
        $options[0]= L('please_chose');
        foreach($templates as $key=>$r) {
            if(strstr($r['value'],'show')){
                $options[$r['value']]=$r['filename'];
            }
		}
        $newinfo['options']=$options;
        $fun=$info['setup']['inputtype'];
        return $this->select($newinfo,$value);
    }


	public function image($info,$value){
		$info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
		$id = $field = $info['field'];
	    $validate = getvalidate($info);
        if(ACTION_NAME=='add'){
			$value = $value ? $value : $info['setup']['default'];
        }else{
			$value = $value ? $value : $this->data[$field];
        }
		if(empty($info['setup']['upload_maxsize'])){
			if(APP_LANG){ 
				$Config = F('Config_'.$this->lang);			
			}else{
				$Config = F('Config');		
			}
			$info['setup']['upload_maxsize'] =  intval(byte_format($Config['attach_maxsize']));
		}


		$yourphp_auth_key = sysmd5(C('ADMIN_ACCESS').$_SERVER['HTTP_USER_AGENT']);
		$yourphp_auth = authcode($this->isadmin.'-'.$info['setup']['more'].'-0-1-'.$info['setup']['upload_allowext'].'-'.$info['setup']['upload_maxsize'].'-'.$info['moduleid'], 'ENCODE',$yourphp_auth_key);

		$parseStr   = ' <div id="'.$field.'_aid_box"></div><input type="text"   class="input-text '.$info['class'].'" name="'.$field.'"  id="'.$id.'" value="'.$value.'" size="'.$info['setup']['size'].'"  '.$validate.'/> <input type="button" class="button" value="'.L('upload_images').'" onclick="javascript:swfupload(\''.$field.'_uploadfile\',\''.$field.'\',\''.L('uploadfiles').'\','.$this->isadmin.','.$info['setup']['more'].',0,1,\''.$info['setup']['upload_allowext'].'\','.$info['setup']['upload_maxsize'].','.$info['moduleid'].',\''.$yourphp_auth.'\',up_image,nodo)"> ';
		return $parseStr;
	}

	public function images($info,$value){
		$info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
		$id = $field = $info['field'];
	    $validate = getvalidate($info);
        if(ACTION_NAME=='add'){
			$value = $value ? $value : $info['setup']['default'];
        }else{
			$value = $value ? $value : $this->data[$field];
        }
		$data='';
		$i=0;
		if($value){
			$options = explode(":::",$value);
			if(is_array($options)){
				foreach($options as  $r) {
						$v = explode("|",$r);
						$k = trim($v[1]);
						$optionsarr[$k] = $v[0];
						$data .='<div id="uplistd_'.$i.'"><input type="text" size="50" class="input-text" name="'.$field.'[]" value="'.$v[0].'"  /> <input type="text" class="input-text" name="'.$field.'_name[]" value="'.$v[1].'" size="30" /> &nbsp;<a href="javascript:remove_this(\'uplistd_'.$i.'\');">'.L('remove').'</a> </div>';
						$i++;
				}
			}
		}
		if(empty($info['setup']['upload_maxsize'])){
			if(APP_LANG){ 
				$Config = F('Config_'.$this->lang);			
			}else{
				$Config = F('Config');		
			}
			$info['setup']['upload_maxsize'] =  intval(byte_format($Config['attach_maxsize']));
		}
		$yourphp_auth_key = sysmd5(C('ADMIN_ACCESS').$_SERVER['HTTP_USER_AGENT']);
		$yourphp_auth = authcode($this->isadmin.'-'.$info['setup']['more'].'-0-'.$info['setup']['upload_maxnum'].'-'.$info['setup']['upload_allowext'].'-'.$info['setup']['upload_maxsize'].'-'.$info['moduleid'], 'ENCODE',$yourphp_auth_key);

		$parseStr   = '
		<fieldset class="images_box">
        <legend>'.L('upload_images').'</legend><center><div>'.L('upload_maxfiles').' <font color=\'red\'>'.$info['setup']['upload_maxnum'].'</font> '.L('zhang').'</div></center>
		<div id="'.$field.'_images" class="imagesList"><input type="hidden"  name="'.$field.'[]" value=""/><input type="hidden"   name="'.$field.'_name[]" value="" />'.$data.'</div>
		</fieldset>
		<div class="c"></div>
		<input type="button" class="button" value="'.L('upload_images').'" onclick="javascript:swfupload(\''.$field.'_uploadfile\',\''.$field.'\',\''.L('uploadfiles').'\','.$this->isadmin.','.$info['setup']['more'].',0,'.$info['setup']['upload_maxnum'].',\''.$info['setup']['upload_allowext'].'\','.$info['setup']['upload_maxsize'].','.$info['moduleid'].',\''.$yourphp_auth.'\',up_images,nodo)">  ';

		return $parseStr;
	}
	public function file($info,$value){
		$info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
		$id = $field = $info['field'];
	    $validate = getvalidate($info);
        if(ACTION_NAME=='add'){
			$value = $value ? $value : $info['setup']['default'];
        }else{
			$value = $value ? $value : $this->data[$field];
        }
		if(empty($info['setup']['upload_maxsize'])){
			if(APP_LANG){ 
				$Config = F('Config_'.$this->lang);			
			}else{
				$Config = F('Config');		
			}
			$info['setup']['upload_maxsize'] =  intval(byte_format($Config['attach_maxsize']));
		}
		$yourphp_auth_key = sysmd5(C('ADMIN_ACCESS').$_SERVER['HTTP_USER_AGENT']);
		$yourphp_auth = authcode($this->isadmin.'-'.$info['setup']['more'].'-0-1-'.$info['setup']['upload_allowext'].'-'.$info['setup']['upload_maxsize'].'-'.$info['moduleid'], 'ENCODE',$yourphp_auth_key);
		$parseStr   = ' <div id="'.$field.'_aid_box"></div><input type="text"    class="input-text '.$info['class'].'" name="'.$field.'"  id="'.$id.'" value="'.$value.'" size="'.$info['setup']['size'].'"  '.$validate.'/> <input type="button" class="button" value="'.L('upload_files').'" onclick="javascript:swfupload(\''.$field.'_uploadfile\',\''.$field.'\',\''.L('uploadfiles').'\','.$this->isadmin.','.$info['setup']['more'].',0,1,\''.$info['setup']['upload_allowext'].'\','.$info['setup']['upload_maxsize'].','.$info['moduleid'].',\''.$yourphp_auth.'\',up_image,nodo)"> ';
		return $parseStr;
	}

	public function files($info,$value){
		$info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
		$id = $field = $info['field'];
	    $validate = getvalidate($info);
        if(ACTION_NAME=='add'){
			$value = $value ? $value : $info['setup']['default'];
        }else{
			$value = $value ? $value : $this->data[$field];
        }
		if(empty($info['setup']['upload_maxsize'])){
			if(APP_LANG){ 
				$Config = F('Config_'.$this->lang);			
			}else{
				$Config = F('Config');		
			}
			$info['setup']['upload_maxsize'] =  intval(byte_format($Config['attach_maxsize']));
		}
		$yourphp_auth_key = sysmd5(C('ADMIN_ACCESS').$_SERVER['HTTP_USER_AGENT']);
		$yourphp_auth = authcode($this->isadmin.'-'.$info['setup']['more'].'-0-'.$info['setup']['upload_maxnum'].'-'.$info['setup']['upload_allowext'].'-'.$info['setup']['upload_maxsize'].'-'.$info['moduleid'], 'ENCODE',$yourphp_auth_key);

		$parseStr   = '<fieldset class="images_box">
        <legend>'.L('upload_images').'</legend><center><div>'.L('upload_maxfiles').' <font color=\'red\'>'.$info['setup']['upload_maxnum'].'</font> '.L('zhang').'</div></center>
		<div id="'.$field.'_images" class="imagesList"></div>
		</fieldset>
		<input type="button"  style="margin-left:5px;" class="button" value="'.L('upload_files').'" onclick="javascript:swfupload(\''.$field.'_uploadfile\',\''.$field.'\',\''.L('uploadfiles').'\','.$this->isadmin.','.$info['setup']['more'].',0,'.$info['setup']['upload_maxnum'].',\''.$info['setup']['upload_allowext'].'\','.$info['setup']['upload_maxsize'].','.$info['moduleid'].',\''.$yourphp_auth.'\',up_images,nodo)">  ';

		return $parseStr;
	}
}
?>