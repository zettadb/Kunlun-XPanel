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
