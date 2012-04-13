<?php
/**
 *
 * index(入口文件)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <web@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
if (!is_file('./config.php')) header("location: ./Install");
header("Content-type: text/html; charset=utf-8");
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('YOURPHP', 'YourPHP');
define('UPLOAD_PATH', './Uploads/');
define('VERSION', 'v2.1 Released');
define('UPDATETIME', '20120412');
define('APP_NAME', 'Yourphp');
define('APP_PATH', './Yourphp/');
define('APP_LANG', true);
define('APP_DEBUG',false);
define('THINK_PATH','./Core/');
require(THINK_PATH.'/Core.php');
?>
