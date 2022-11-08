<?php


if (!function_exists('rewrite_config')) {
	/**
	 * 重写配置
	 * @param $data
	 * @author wangwentao
	 */
	function rewrite_config($data)
	{
		$f_p = fopen(APPPATH . 'config/myconfig.php', 'w+');
		$newConfig = file_get_contents(APPPATH . 'config/myconfig_tpl.php');

		foreach ($data as $key => $val) {
			$newConfig = str_replace("{{{$key}}}", $val, $newConfig);
		}
		fwrite($f_p, $newConfig);
		fgets($f_p);
	}

}


if (!function_exists('get_pg_con')) {
	function get_pg_con($host, $port, $user, $pwd, $db)
	{
		$db = pg_connect("host={$host} port={$port} dbname={$db} user={$user} password={$pwd}");
		if (!$db) {
			throw new ApiException("pg 连接失败:{$host}");
		}

		return $db;
	}
}

