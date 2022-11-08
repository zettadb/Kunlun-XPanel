<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/


$hook['pre_controller'][] = function () {
	header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
	header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
	header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
	header('Access-Control-Allow-Headers:x-requested-with,content-type,Token');//允许接受token
	header('Content-Type: application/json;charset=utf-8');
};

$hook['pre_controller'][] = [
	'class'    => 'ExceptionHook',
	'function' => 'SetExceptionHandler',
	'filename' => 'ExceptionHook.php',
	'filepath' => 'exceptions'
];
