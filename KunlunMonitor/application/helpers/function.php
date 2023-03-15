<?php


if (!function_exists('rewrite_config')) {
	/**
	 * 重写配置
	 * @param $data
	 * @author wangwentao
	 */
	function rewrite_config($data)
	{

		//$f_p = fopen(APPPATH . 'config/myconfig.php', 'w+');
		$newConfig = file_get_contents(APPPATH . 'config/myconfig_tpl.php');
		foreach ($data as $key => $val) {
			$newConfig = str_replace("{{{$key}}}", $val, $newConfig);
		}
		file_put_contents(APPPATH . 'config/myconfig.php', $newConfig);
	}

}


if (!function_exists('get_pg_con')) {
	function get_pg_con($host, $port, $user, $pwd, $db)
	{
		$db = pg_connect("host={$host} port={$port} dbname={$db} user={$user} password={$pwd} connect_timeout=5");
		if (!$db) {
			throw new ApiException("pg 连接失败:{$host}");
		}
		return $db;
	}
}

if (!function_exists('pg_find')) {
	function pg_find($con, $sql)
	{
		$res = pg_query($con, $sql);
		if ($res === false) {
			$err = pg_last_error($con);
			throw new ApiException('PG 查询失败:' . $err);
		}

		return pg_fetch_all($res);
	}
}