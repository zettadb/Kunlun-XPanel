<?php
		defined('BASEPATH') OR exit('No direct script access allowed');
		$active_group = 'default';
		$query_builder = TRUE;
		$db_debug=TRUE;
		$db['role']=array(
			'dsn'	=> '',
			'hostname' => '192.168.0.127',
			'port' => 55100,
			'username' => 'pgx',
			'password' => 'pgx_pwd',
			'database' => 'kunlun_dba_tools_db',
			'dbdriver' => 'mysqli',
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => TRUE,
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'encrypt' => FALSE,
			'compress' => FALSE,
			'stricton' => FALSE,
			'failover' => array(),
			'options' => array(PDO::ATTR_TIMEOUT => 5),
			'save_queries' => TRUE,
		);
		$db['default']=array(
			'dsn'	=> '',
			'hostname' => '192.168.0.127',
			'port' => 55100,
			'username' => 'pgx',
			'password' => 'pgx_pwd',
			'database' => 'kunlun_metadata_db',
			'dbdriver' => 'mysqli',
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => TRUE,
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'encrypt' => FALSE,
			'compress' => FALSE,
			'stricton' => FALSE,
			'failover' => array(),
			'options' => array(PDO::ATTR_TIMEOUT => 5),
			'save_queries' => TRUE,
		);