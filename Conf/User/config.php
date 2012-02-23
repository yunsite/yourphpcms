<?php
$config=array(
		'URL_ROUTER_ON'		=> false,
		'TMPL_CACHE_ON'		=> false,
		'TMPL_CACHE_TIME'	=> -1,
		'URL_DISPATCH_ON'	=> 0,
		'URL_MODEL'			=> 0,
		'LANG_SWITCH_ON'		=> true  
);
$sys_config = F("sys.config");
return array_merge($sys_config,$config);
?>