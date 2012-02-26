<?php
$default_theme = is_dir(TMPL_PATH.'User/'.C('DEFAULT_THEME')) ? C('DEFAULT_THEME') : 'Default';
$config=array(
		'DEFAULT_THEME'		=> $default_theme,
		'URL_ROUTER_ON'		=> false,
		'TMPL_CACHE_ON'		=> true,
		'TMPL_CACHE_TIME'	=> 3600,
		'URL_MODEL'			=> 0,
		'LANG_SWITCH_ON'		=> true  
);
return $config;
?>