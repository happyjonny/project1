<?php 

	// 设置字符集
	header('content-type: text/html; charset=utf-8');

	// 设置时区
	date_default_timezone_set('PRC');

	// 开启session
	session_start();

	// 设置错误级别
	error_reporting(E_ALL ^E_NOTICE);

	// 数据库配置
	define('DSN', 'mysql:dbname=s73;host=localhost;charset=utf8');
  define('USER', 'jonny');
	define('PWD', '');

	// Admin 下的css/js/images
	define('AC', '/Admin/Resource/css/');
	define('AJ', '/Admin/Resource/js/');
	define('AI', '/Admin/Resource/images/');

	// 每页显示的条数
  define('ROWS', 12);