<?php
$database = require ('./config.php');
$config	= array(
		'DEFAULT_THEME'		=> 'Default',
		'DEFAULT_CHARSET' => 'utf-8',
		'APP_GROUP_LIST' => 'Home,Admin,User,Blog',
		'DEFAULT_GROUP' =>'Home',
		'TMPL_FILE_DEPR' => '_',
		'DB_FIELDS_CACHE' => false,
		'DB_FIELDTYPE_CHECK' => true,
		'URL_CASE_INSENSITIVE'=>true,
		'URL_ROUTER_ON' => true,
		'URL_AUTO_REDIRECT' => false,
		'DEFAULT_LANG'   =>    'cn',//默认语言
		//'APP_AUTOLOAD_PATH'=> 'Think.Util.,@.TagLib.',
		'TAGLIB_LOAD' => true,
		'TAGLIB_PRE_LOAD' => 'html,yp',
		'TOKEN_ON' => false ,//表单验证
		'TMPL_ACTION_ERROR' => APP_PATH.'/Tpl/Default/Public/success.html',
		'TMPL_ACTION_SUCCESS' =>  APP_PATH.'/Tpl/Default/Public/success.html',
		//'APP_DEBUG'=>true,
		//'SESSION_TYPE'=>'DB',
		'COOKIE_PREFIX'=>'YP_',
		'COOKIE_EXPIRE'=>''
);
return array_merge($database, $config);
?>