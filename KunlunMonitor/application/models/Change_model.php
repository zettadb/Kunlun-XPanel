<?php

class Change_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
//		$this->db=$this->load->database('default',true);
//		$this->pg_database=$this->config->item('pg_database');
//		$this->pg_username=$this->config->item('pg_username');
//		$this->default_username=$this->config->item('default_username');
	}

	//mysql连接
	public function getMysql($host, $port, $username, $pwd, $dbname, $sql)
	{

		mysqli_report(MYSQLI_REPORT_STRICT);
		try {
			//exit(print_r([$host, $port, $username, $pwd, $dbname, $sql]));
			$conn = mysqli_connect("{$host}:{$port}", $username, $pwd, $dbname);

			$query = mysqli_query($conn, $sql);
			$arr = mysqli_fetch_row($query);
			$arr['code'] = 200;
			return $arr;
		} catch (Exception $e) {
			$arr[0] = iconv('gbk', 'utf-8', $e->getMessage());
			$arr['code'] = 500;
			return $arr;
		}
	}

	//mysql连接
	public function getAllMysql($host, $port, $username, $pwd, $dbname, $sql,$string)
	{
		mysqli_report(MYSQLI_REPORT_STRICT);
		try {

			$conn = mysqli_connect("{$host}:{$port}", $username, $pwd, $dbname);
//			$query = mysqli_query($conn,$sql);
//			$arr=mysqli_fetch_row($query);
			$lists = array();
			$list = array();
			if ($result = mysqli_query($conn, $sql)) {
				// 一条条获取
				while ($row = mysqli_fetch_row($result)) {
					if($string=='cluster_info'){
						$list = array('cluster_id' => $row[0], 'name' => $row[1],'nick_name' => $row[2]);
					}else{
						$list = array('host' => $row[0], 'port' => $row[1]);
					}
					$lists[] = $list;
				}
				// 释放结果集合
				mysqli_free_result($result);
			}
			$arr['list'] = $lists;
			$arr['code'] = 200;
			return $arr;
		} catch (Exception $e) {
			$arr[0] = iconv('gbk', 'utf-8', $e->getMessage());
			$arr['code'] = 500;
			return $arr;
		}
	}
}

