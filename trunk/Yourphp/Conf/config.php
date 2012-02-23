<?php
$database = require ('./config.php');
$RULES = F('Routes');
$sys_config = F("sys.config");
$config	= array(
		'DEFAULT_THEME'		=> 'Default',
		'DEFAULT_CHARSET' => 'utf-8',
		'APP_GROUP_LIST' => 'Home,Admin,User',
		'DEFAULT_GROUP' =>'Home',
		'TMPL_FILE_DEPR' => '_',
		'DB_FIELDS_CACHE' => false,
		'DB_FIELDTYPE_CHECK' => true,
		//'URL_CASE_INSENSITIVE'=>true,
		'URL_ROUTER_ON' => true,
		'DEFAULT_LANG'   => 'cn',//默认语言
		'LANG_SWITCH_ON'		=> true,
		'LANG_LIST'=>'cn,zh-cn,en',//必须写可允许的语言列表
		'TAGLIB_LOAD' => true,
		'TAGLIB_PRE_LOAD' => 'Yp',
		'TMPL_ACTION_ERROR' => APP_PATH.'/Tpl/Home/Default/Public/success.html',
		'TMPL_ACTION_SUCCESS' =>  APP_PATH.'/Tpl/Home/Default/Public/success.html',
		//'APP_DEBUG'=>true,
		//'SESSION_TYPE'=>'DB',
		'COOKIE_PREFIX'=>'YP_',
		'COOKIE_EXPIRE'=>'',
		'VAR_PAGE' => 'p',		
		'LAYOUT_ON'=>true,
		//'TMPL_EXCEPTION_FILE'=>'./App/Tpl/Public/error.html' 
		'URL_ROUTE_RULES' => $RULES,
		'TMPL_EXCEPTION_FILE' => APP_PATH.'/Tpl/Home/Default/Public/exception.html',
);
unset($RULES);
if($sys_config)
return array_merge($database, $config ,$sys_config);
else
return array_merge($database, $config);

?>
