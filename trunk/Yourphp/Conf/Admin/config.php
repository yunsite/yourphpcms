<?php
$config	= array(
        'DEFAULT_THEME'		=> 'Default',
		'LANG_AUTO_DETECT'=> false,
		'LANG_SWITCH_ON' => true, 
		'DEFAULT_LANG'   => 'zh-cn',
		'URL_ROUTER_ON'		=> false,
		'TMPL_CACHE_ON'		=> false,
		'TMPL_CACHE_TIME'	=> -1,
		'URL_DISPATCH_ON'	=> 0,
		'URL_MODEL'			=> '0',

		'USER_AUTH_ON'			=>true,
		'USER_AUTH_TYPE'		=>1,		// 默认认证类型 1 登录认证 2 实时认证
		'USER_AUTH_KEY'			=>'authId',	// 用户认证SESSION标记
		'ADMIN_AUTH_KEY'		=>'administrator',
		'USER_AUTH_MODEL'		=>'User',	// 默认验证数据表模型
		'AUTH_PWD_ENCODER'		=>'md5',	// 用户认证密码加密方式
		'USER_AUTH_GATEWAY'	    =>'?g=Admin&m=Login',	// 默认认证网关
		'NOT_AUTH_MODULE'		=>'',		// 默认无需认证模块
		'REQUIRE_AUTH_MODULE'	=>'',		// 默认需要认证模块
		'NOT_AUTH_ACTION'		=>'',		// 默认无需认证操作
		'REQUIRE_AUTH_ACTION'	=>'',		// 默认需要认证操作
		'GUEST_AUTH_ON'         => false,    // 是否开启游客授权访问
		'GUEST_AUTH_ID'         =>    0,     // 游客的用户ID
		'DB_LIKE_FIELDS'		=>	'name|remark',
		'RBAC_ROLE_TABLE'		=>	C('DB_PREFIX').'role',
		'RBAC_USER_TABLE'		=>	C('DB_PREFIX').'role_user',
		'RBAC_ACCESS_TABLE'		=>	C('DB_PREFIX').'access',
		'RBAC_NODE_TABLE'		=>  C('DB_PREFIX').'node',	
		'DEFAULT_HOME_THEME' => C('DEFAULT_THEME'),
		'LAYOUT_ON'=> true,
		'TMPL_CACHE_ON'		=> true,
		'TMPL_CACHE_TIME'	=> 3600,
		//'TMPL_DETECT_THEME'     => true
		//'LANG_SWITCH_ON'		=> true,
);
return $config	;
?>