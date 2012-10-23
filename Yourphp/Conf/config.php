<?php
$database = require (RUNTIME_PATH.'config.php');
$sys_config =  include DATA_PATH.  'sys.config.php';
if(empty($sys_config)){$sys_config=array();$sys_config['LAYOUT_ON']=1;}
if($sys_config['URL_MODEL']) $RULES = include DATA_PATH.  'Routes.php';
$config	= array(
		'DEFAULT_THEME'		=> 'Default',
		'DEFAULT_CHARSET' => 'utf-8',
		'APP_GROUP_LIST' => 'Home,Admin,User',
		'DEFAULT_GROUP' =>'Home',
		'TMPL_FILE_DEPR' => '_',
		'DB_FIELDS_CACHE' => false,
		'DB_FIELDTYPE_CHECK' => true,
		'URL_ROUTER_ON' => true,
		'DEFAULT_LANG'   => 'cn',
		'LANG_SWITCH_ON'		=> true,
		'TAGLIB_LOAD' => true,
		'TAGLIB_PRE_LOAD' => 'Yp',
		'TMPL_ACTION_ERROR' => APP_PATH.'/Tpl/Home/Default/Public/success.html',
		'TMPL_ACTION_SUCCESS' =>  APP_PATH.'/Tpl/Home/Default/Public/success.html',
		'COOKIE_PREFIX'=>'YP_',
		'COOKIE_EXPIRE'=>'',
		'VAR_PAGE' => 'p',
		'LAYOUT_HOME_ON'=>$sys_config['LAYOUT_ON'],
		'URL_ROUTE_RULES' => $RULES,
		'TMPL_EXCEPTION_FILE' => APP_PATH.'/Tpl/Home/Default/Public/exception.html'
);
return array_merge($database, $config ,$sys_config);
?>
