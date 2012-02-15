<?php
$config=array(
	'LANG_SWITCH_ON'		=> true,
	//'TMPL_DETECT_THEME'     => true,
);
$sys_config = F("sys.config");
return array_merge($sys_config,$config);
?>

