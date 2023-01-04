<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cluster extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//打开重定向
		header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
		header('Access-Control-Allow-Headers: Content-Type,Content-Length, Origin'); // 设置允许自定义请求头的字段
		header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
		header('Access-Control-Allow-Headers:x-requested-with,content-type,Token');//允许接受token
		header('Content-Type: application/json;charset=utf-8');
		//$this->ver=$this->config->item('ver');
		//$this->post_url=$this->config->item('post_url');
		$this->config->load('myconfig');
		$this->key = $this->config->item('key');
		$this->post_url = $this->config->item('post_url');
		$this->default_username = $this->config->item('default_username');
		$this->pg_username = $this->config->item('pg_username');
		$this->db_prefix = $this->config->item('db_prefix');
	}

	public function createCluster()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//print_r(json_encode($string));exit;
		//验证该账户是否有创建集群的权限
		$this->load->model('Login_model');
		$res_priv = $this->Login_model->authority($string['user_name'], 'cluster_creata_priv');
		if ($res_priv == true) {
			//调接口
			$this->load->model('Cluster_model');
			$post_data = str_replace("\\/", "/", json_encode($string));
			$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
			$post_arr = json_decode($post_arr, TRUE);
			$data = $post_arr;
		} else {
			$data['error_info'] = '该帐户不具备新增集群权限';
		}
		print_r(json_encode($data));
	}

	public function clusterList()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$pageNo = isset($arr['pageNo']) ? $arr['pageNo'] : 1;
		$pageSize = isset($arr['pageSize']) ? $arr['pageSize'] : 10;
		$username = $arr['name'];
		$user_name = $arr['user_name'];
		$effectCluster = $arr['effectCluster'] == 'null' ? null : $arr['effectCluster'];
		$apply_all_cluster = isset($arr['apply_all_cluster']) ? $arr['apply_all_cluster'] : 1;
		$start = ($pageNo - 1) * $pageSize;
		//获取用户数据
		if ($apply_all_cluster == 1) {
			if (empty($effectCluster)) {
				$sql = "select * from db_clusters where memo!='' and memo is not null and status!='deleted'";
				if (!empty($username)) {
					$sql .= " and nick_name like '%$username%'";
				}
			}
		}
		if ($apply_all_cluster == 2) {
			if (empty($effectCluster)) {
				$effectCluster = 'NULL';
			}
			if (strpos($effectCluster, ',') == false) {
				$sql = "select id,name,when_created,nick_name,ha_mode,memo from db_clusters where id=$effectCluster and memo!='' and memo is not null and status!='deleted'";
			} else {
				$sql = "select id,name,when_created,nick_name,ha_mode,memo from db_clusters where id in ($effectCluster) and memo!='' and memo is not null and status!='deleted'";
			}
			if (!empty($username)) {
				$sql .= " and nick_name like '%$username%'";
			}
		}
		$sql .= " order by id desc limit " . $pageSize . " offset " . $start;
//		print_r($sql);exit;
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		//total
		if ($apply_all_cluster == 1) {
			if (empty($effectCluster)) {
				$total_sql = "select count(id) as count from db_clusters where memo!='' and memo is not null and status!='deleted'";
				if (!empty($username)) {
					$total_sql .= " and name like '%$username%'";
				}
			}
		}
		if ($apply_all_cluster == 2) {
			if (strpos($effectCluster, ',') == false) {
				$total_sql = "select count(id) as count from db_clusters where id=$effectCluster  and memo!='' and memo is not null and status!='deleted'";
			} else {
				$total_sql = "select count(id) as count from db_clusters where id in ($effectCluster) and memo!='' and memo is not null and status!='deleted'";
			}
			if (!empty($username)) {
				$total_sql .= " and nick_name like '%$username%'";
			}
		}
		$res_total = $this->Cluster_model->getList($total_sql);
		if ($res === false) {
			$res = array();
		} else {
			foreach ($res as $row => $value) {
				foreach ($value as $key2 => $value2) {
					//shard数
					if ($key2 == 'id') {
						if (!empty($value2)) {
							$shardtotal = $this->getThisShards($value2, $user_name);
							$comptotal = $this->getThisComps($value2);
							//print_r($shardtotal);exit;
							$first_backup = $this->getLastBackup($value2);
							//$nodetotal= $this->getThisNodes($value2);
							$res[$row]['shardList'] = $shardtotal['list'];
							$res[$row]['compList'] = $comptotal['list'];
							$res[$row]['shardtotal'] = $shardtotal['nodedetail'];
							$res[$row]['comp_count'] = $comptotal['list'] !== false ? count($comptotal['list']) : 0;
							$res[$row]['first_backup'] = $first_backup;
							$status = '';
							$abnormal = '';
							if (empty($comptotal['status']) && empty($shardtotal['status'])) {
								$status = '运行中';
								// $abnormal='shard_1的192.168.0.125(57001)异常;shard_2的192.168.0.136(57004)延迟过大;';
							} else {
								$status = '异常';
								$abnormal = $comptotal['status'] . $shardtotal['status'];
								// $abnormal='shard_1的192.168.0.125(57001)异常;shard_2的192.168.0.136(57004)延迟过大;';
							}
							$res[$row]['status'] = $status;
							$res[$row]['abnormal'] = $abnormal;
						}
					}
					if ($key2 == 'memo') {
						$string = json_decode($res[$row]['memo'], true);
						if (!empty($string)) {
							// if(!empty(['nodes'])){
							// 	$res[$row]['snode_count'] = $string['nodes'];
							// }else{
							// 	$res[$row]['snode_count'] = '';
							// }
							if (!empty($string['innodb_size'])) {
								$res[$row]['buffer_pool'] = $string['innodb_size'];
							} else {
								$res[$row]['buffer_pool'] = '';
							}
							if (!empty($string['machinelist'])) {
								$res[$row]['machinelist'] = $string['machinelist'];
							} else {
								$res[$row]['machinelist'] = '';
							}
							// if(!empty($string['shards'])){
							// 	$res[$row]['shards_count'] = $string['shards'];
							// }else{
							// 	$res[$row]['shards_count'] = '';
							// }
							if (!empty($string['fullsync_level'])) {
								$res[$row]['fullsync_level'] = $string['fullsync_level'];
							} else {
								$res[$row]['fullsync_level'] = '';
							}
						} else {
							// $res[$row]['snode_count'] = '';
							$res[$row]['buffer_pool'] = '';
							$res[$row]['machinelist'] = '';
							// $res[$row]['shards_count'] = '';
							$res[$row]['fullsync_level'] = '';
						}
					}
				}
			}
		}
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
		print_r(json_encode($data));
	}

	public function delCluster()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//print_r(json_encode($string));exit;
		//验证该账户是否有删除集群的权限
		$this->load->model('Login_model');
		$res_priv = $this->Login_model->authority($string['user_name'], 'cluster_drop_priv');
		if ($res_priv == true) {
			//调接口
			$this->load->model('Cluster_model');
			$post_data = str_replace("\\/", "/", json_encode($string));
			$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
			$post_arr = json_decode($post_arr, TRUE);
			$data = $post_arr;
		} else {
			$data['error_info'] = '该帐户不具备删除集群权限';
		}
		print_r(json_encode($data));
	}

	public function backUpCluster()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//验证该账户是否有备份集群的权限
		$this->load->model('Login_model');
		$res_priv = $this->Login_model->authority($string['user_name'], 'backup_priv');
		if ($res_priv) {
			//调接口
			$this->load->model('Cluster_model');
			$post_data = str_replace("\\/", "/", json_encode($string));
			$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
			$post_arr = json_decode($post_arr, TRUE);
			$data = $post_arr;
		} else {
			$data['error_info'] = '该帐户不具备备份集群权限';
		}
		print_r(json_encode($data));
	}

	public function getBackUpList()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$pageNo = $arr['pageNo'];
		$pageSize = $arr['pageSize'];
		$username = $arr['username'];
		$status = $arr['status'];
		$backup_type = $arr['backup_type'];
		$start = ($pageNo - 1) * $pageSize;
		$this->load->model('Cluster_model');
		//查cluster_id
		$sql_clusterid = "select id from db_clusters where memo!='' and memo is not null and status!='deleted'";
		$res_clusterid = $this->Cluster_model->getList($sql_clusterid);
		$cluster_id = '';
		if (!empty($res_clusterid)) {
			$clusterid_count = count($res_clusterid);
		} else {
			$clusterid_count = 0;
		}
		if ($clusterid_count > 1) {
			foreach ($res_clusterid as $row) {
				$cluster_id .= $row['id'] . ',';
			}
			$cluster_id = substr($cluster_id, 0, strlen($cluster_id) - 1);
		} else if ($clusterid_count == 1) {
			$cluster_id = $res_clusterid[0]['id'];
		} else {
			$data['code'] = 200;
			$data['list'] = array();
			$data['total'] = 0;
			print_r(json_encode($data));
			return;
		}
		//获取集群信息
		$sql = "select cluster_id,shard_id,comp_id,start_ts,end_ts,backup_type,status,memo from cluster_coldbackups where cluster_id in ($cluster_id)";
		//print_r($sql);exit;
		if (!empty($status)) {
			$sql .= " and  status='$status'";
		}
		if (!empty($backup_type)) {
			$sql .= " and  backup_type='$backup_type'";
		}
		$sql .= " order by id desc limit " . $pageSize . " offset " . $start;
		//echo $sql;exit;
		$res = $this->Cluster_model->getList($sql);
		$total_sql = "select count(id) as count from cluster_coldbackups  where cluster_id in ($cluster_id)";
		if (!empty($status)) {
			$total_sql .= " and  status='$status'";
		}
		if (!empty($backup_type)) {
			$total_sql .= " and  backup_type='$backup_type'";
		}
		$res_total = $this->Cluster_model->getList($total_sql);
		if (!empty($res)) {
			foreach ($res as $row => $value) {
				foreach ($value as $key2 => $value2) {
					//集群名称
					if ($key2 == 'cluster_id') {
						if (!empty($value2)) {
							$name = $this->getClusterName($value2);
							if (!empty($name)) {
								$res[$row]['cluster_name'] = $name[0]['name'];
								$res[$row]['nick_name'] = $name[0]['nick_name'];
							} else {
								$res[$row]['cluster_name'] = '';
								$res[$row]['nick_name'] = '';
							}
						} else {
							$res[$row]['cluster_name'] = '';
							$res[$row]['nick_name'] = '';
						}
					}
					//shar名称
					if ($key2 == 'shard_id') {
						if (!empty($value2)) {
							$name = $this->getShardName($res[$row]['cluster_id'], $value2);
							$res[$row]['shard_name'] = $name;
						} else {
							$res[$row]['shard_name'] = '';
						}
					}
					//comp名称
					if ($key2 == 'comp_id') {
						if (!empty($value2)) {
							$name = $this->getCompName($res[$row]['cluster_id'], $value2);
							$res[$row]['comp_name'] = $name;
						} else {
							$res[$row]['comp_name'] = '';
						}
					}
					//结果信息
					if ($key2 == 'memo') {
						if (!empty($value2)) {
							$newline = array("\r\n", "\n", "\r");
							$value2 = str_replace($newline, '', $value2);
							$value3 = json_decode($value2, true);
							$res[$row]['info'] = $value3['response_array'][0]['info']['info'];
						} else {
							$res[$row]['info'] = '';
						}
					}
				}
			}
		} else {
			$res = array();
		}
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
		print_r(json_encode($data));
	}

	public function getClusterName($id)
	{
		$sql = "select name,nick_name from db_clusters where id='$id' and status!='deleted'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		return $res;
	}

	public function getShardName($db_cluster_id, $id)
	{
		$sql = "select name from shards where id='$id' and db_cluster_id='$db_cluster_id' and status!='deleted'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		if (!empty($res)) {
			return $res[0]['name'];
		} else {
			return '';
		}
	}

	public function getCompName($db_cluster_id, $id)
	{
		$sql = "select name from comp_nodes where id='$id' and db_cluster_id='$db_cluster_id' and status!='deleted'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		if ($res == false) {
			return '';
		}
		return $res[0]['name'];
	}

	public function ifBackUp()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$cluster_id = $string['id'];
		$sql = "select cluster_id,end_ts from cluster_coldbackups where cluster_id='$cluster_id' order by end_ts asc limit 1";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		if (empty($res)) {
			$data['code'] = 501;
			$data['message'] = '数据备份文件不存在。请配置相关备份策略，或手动发起备份！';
		} else {
			$data['code'] = 200;
			$data['list'] = $res;
		}
		print_r(json_encode($data));
	}

	public function restoreCluster()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//验证该账户是否有恢复集群的权限
		$this->load->model('Login_model');
		$res_priv = $this->Login_model->authority($string['user_name'], 'restore_priv');
		if ($res_priv == true) {
			//调接口
			$this->load->model('Cluster_model');
			$post_data = str_replace("\\/", "/", json_encode($string));
			$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
			$post_arr = json_decode($post_arr, TRUE);
			$data = $post_arr;
		} else {
			$data['error_info'] = '该帐户不具备恢复集群权限';
		}
		print_r(json_encode($data));
	}

	//获取信息
	public function getAllMachine()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		$sql = "select id,hostaddr from server_nodes where hostaddr!='pseudo_server_useless'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = count($res);
		print_r(json_encode($data));
	}

	public function getShards()
	{

		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$id = $string['id'];
		$sql = "select id,name from shards where db_cluster_id='$id' and status!='deleted'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$data['code'] = 200;
		$data['list'] = $res;
		print_r(json_encode($data));
	}

	public function addShards()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//验证该账户是否有新增shard的权限
		$this->load->model('Login_model');
		$res_priv = $this->Login_model->authority($string['user_name'], 'shard_create_priv');
		if ($res_priv == true) {
			//调接口
			$this->load->model('Cluster_model');
			$post_data = str_replace("\\/", "/", json_encode($string));
			$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
			$post_arr = json_decode($post_arr, TRUE);
			$data = $post_arr;
		} else {
			$data['error_info'] = '该帐户不具备新增shard权限';
		}
		print_r(json_encode($data));
	}


	public function SetCpuCgroup()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//验证该账户是否有新增计算节点的权限
		$this->load->model('Login_model');
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));

	}

	public function addComps()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//验证该账户是否有新增计算节点的权限
		$this->load->model('Login_model');
		$res_priv = $this->Login_model->authority($string['user_name'], 'compute_node_create_priv');
		if ($res_priv == true) {
			//调接口
			$this->load->model('Cluster_model');
			$post_data = str_replace("\\/", "/", json_encode($string));
			$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
			$post_arr = json_decode($post_arr, TRUE);
			$data = $post_arr;
		} else {
			$data['error_info'] = '该帐户不具备新增计算节点权限';
		}
		print_r(json_encode($data));
	}

	public function addNodes()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//验证该账户是否有新增存储节点的权限
		$this->load->model('Login_model');
		$res_priv = $this->Login_model->authority($string['user_name'], 'storage_node_create_priv');
		if ($res_priv == true) {
			//调接口
			$this->load->model('Cluster_model');
			$post_data = str_replace("\\/", "/", json_encode($string));
			$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
			$post_arr = json_decode($post_arr, TRUE);
			$data = $post_arr;
		} else {
			$data['error_info'] = '该帐户不具备新增存储节点权限';
		}
		print_r(json_encode($data));
	}

	public function getThisShards($id, $user_name)
	{
		$sql = "select * from shards where db_cluster_id='$id' and status!='deleted'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$count = 0;
		$list = array();
		$status = '';
		if ($res !== false) {
			$count = count((array)$res);
		}
		$data['shards_count'] = $count;
		if ($count == 1) {
			$resnode = $this->getThisNodes($id, $res[0]['id']);
			//print_r($resnode);exit;
			$data['nodedetail'] = $count . '个shard，' . $res[0]['name'] . '(' . count($resnode) . '个副本)';
			foreach ($resnode as &$item) {
				$item['name'] = $res[0]['name'];
			}
			$data['list'] = $resnode;
			foreach ($resnode as $i) {
				//异常
				if ($i['status'] == 'inactive') {
					$status .= $i['name'] . '(' . $i['ip'] . '_' . $i['port'] . ')' . '异常;';
				}
				//获取shard的最大延迟时间
				//查user_id
				$this->load->model('Login_model');
				$user_sql = "select id from kunlun_user where name='$user_name';";
				$res_user = $this->Login_model->getList($user_sql);
				if (!empty($res_user)) {
					$user_id = $res_user[0]['id'];
				}
				$result = '100';
				//先查表是否存在
				$sqldalay = "select TABLE_NAME from information_schema.TABLES where TABLE_NAME = 'shard_max_dalay';";
				$resdalay = $this->Login_model->getList($sqldalay);
				if ($resdalay !== false) {
					//表存在的情况
					$shard_id = $res[0]['id'];
					$sql_select = "select max_delay_time from shard_max_dalay where db_cluster_id='$id' and shard_id='$shard_id' and user_id='$user_id'";
					$res_select = $this->Login_model->getList($sql_select);
					if ($res_select !== false) {
						$result = $res_select[0]['max_delay_time'];
					}
				}
				if ($i['replica_delay'] > $result) {
					$status .= $i['name'] . '(' . $i['ip'] . '_' . $i['port'] . ')' . '延迟过大;';
				}
			}
			$data['status'] = $status;
		} elseif ($count > 1) {
			$node = '';
			foreach ($res as $key) {
				$resnode = $this->getThisNodes($id, $key['id']);
				$node .= $key['name'] . '(' . count($resnode) . '个副本)，';
				//$data[$key['name']]=$resnode;
				foreach ($resnode as &$item) {
					$item['name'] = $key['name'];
				}
				foreach ($resnode as $i) {
					$list[] = $i;
					if ($i['status'] == 'inactive') {
						$status .= $i['name'] . '(' . $i['ip'] . '_' . $i['port'] . ')' . '异常;';
					}
					//获取shard的最大延迟时间
					//查user_id
					$this->load->model('Login_model');
					$user_sql = "select id from kunlun_user where name='$user_name';";
					$res_user = $this->Login_model->getList($user_sql);
					if (!empty($res_user)) {
						$user_id = $res_user[0]['id'];
					}
					$result = '100';
					//先查表是否存在
					$sqldalay = "select TABLE_NAME from information_schema.TABLES where TABLE_NAME = 'shard_max_dalay';";
					$resdalay = $this->Login_model->getList($sqldalay);
					if ($resdalay !== false) {
						//表存在的情况
						$shard_id = $key['id'];
						$sql_select = "select max_delay_time from shard_max_dalay where db_cluster_id='$id' and shard_id='$shard_id' and user_id='$user_id'";
						$res_select = $this->Login_model->getList($sql_select);
						if ($res_select !== false) {
							$result = $res_select[0]['max_delay_time'];
						}
					}
					if ($i['replica_delay'] > $result) {
						$status .= $i['name'] . '(' . $i['ip'] . '_' . $i['port'] . ')' . '延迟过大;';
					}
				}
			}
			$data['list'] = $list;
			$node = rtrim($node, "，");
			$data['nodedetail'] = $count . '个shard，' . $node;
			$data['status'] = $status;
		} else {
			$data['nodedetail'] = '';
			$data['list'] = false;
			$data['status'] = $status;
		}
		return $data;
	}

	public function getThisComps($id)
	{
		$sql = "select * from comp_nodes where db_cluster_id='$id'  and status!='deleted'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$status = '';
		if ($res !== false) {
			foreach ($res as $i) {
				if ($i['status'] == 'inactive') {
					$status .= '计算节点(' . $i['ip'] . '_' . $i['port'] . ')' . '异常;';
				}
			}
		}
		$data['list'] = $res;
		$data['status'] = $status;
		return $data;
	}

	public function getThisNodes($id, $shard_id)
	{
		$sql = "select * from shard_nodes where db_cluster_id='$id' and shard_id='$shard_id' and status!='deleted'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		return $res;
	}

	public function getLastBackup($id)
	{
		$sql = "select end_ts from cluster_coldbackups where cluster_id='$id' order by end_ts desc limit 1";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$time = '';
		if ($res == false) {
			$time = '';
		} else {
			$end_ts = explode(".", $res[0]['end_ts']);
			$time = $end_ts[0];
		}
		return $time;
	}

	public function getClusterNodesList()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}

		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$id = $string['id'];
		$this->load->model('Login_model');
		//获取cluster的name
		$this->load->model('Cluster_model');
		$sql = "select name,nick_name,ha_mode from db_clusters where id='$id'";
		$res_name = $this->Cluster_model->getList($sql);
		$cluster_id = 'cluster';
		$clusterID = $id;
		$cluster_name = $res_name[0]['name'];
		$cluster_text = $res_name[0]['nick_name'];
		$ha_mode = $res_name[0]['ha_mode'];
		$shards_id = 'shards';
		$shards_text = 'shards';
		$comp_id = 'comps';
		$comp_text = '计算节点';
		$arr = array();
		//nodes'下加文本宽度表示超出省略：width'=>120；form下的text是显示线条文字;'disableDrag'=>true，为禁止拖动
		//$nodes=array(array('id'=>$cluster_id, 'text'=>$cluster_text),array('id'=>$shards_id, 'text'=>$shards_text),array('id'=>$comp_id, 'text'=>$comp_text));
		$nodes = array(array('id' => $cluster_id, 'text' => $cluster_text, 'disableDrag' => true, 'data' => array('cluster_name' => $cluster_name, 'ha_mode' => $ha_mode)));
		$links = array();
//		$links=array(array('from'=>$cluster_id, 'to'=>$shards_id),array('from'=>$cluster_id, 'to'=>$comp_id));
		//获取存储节点数据
		$sql = "select id,name from shards where db_cluster_id='$id' and status!='deleted'";
		$res = $this->Cluster_model->getList($sql);
		if ($res !== false) {
			foreach ($res as $row => $value) {
				foreach ($value as $key2 => $value2) {
					//shard名称
					if ($key2 == 'name') {
						if (!empty($value2)) {
							$shard_node_id = 'shard' . $res[$row]['id'];
							$shard_node = array('id' => $shard_node_id, 'text' => $value2, 'data' => array('name' => 'shard', 'cluster_name' => $cluster_name, 'nick_name' => $cluster_text, 'cluster_id' => $id, 'shard_id' => $res[$row]['id'], 'ha_mode' => $ha_mode));
							$shard_link = array('from' => $cluster_id, 'to' => $shard_node_id);
							array_push($nodes, $shard_node);
							array_push($links, $shard_link);
						}
					}
					//节点名称
					/*if ($key2 == 'id') {
						if (!empty($value2)) {
							$shard_node_id='shard'.$res[$row]['id'];
							$shard_arr=$this->getShardNode($value2);
							if($shard_arr!==false){
								foreach ($shard_arr as $key){
									$shard_arr_id=$key['id'];
									$shard_arr_name=$key['port'];
									$shard_n_id='snode'.$shard_arr_id;
									$shard_node=array('id'=>$shard_n_id, 'text'=>$shard_arr_name,'data'=>array('cluster_name'=>$cluster_text,'hostaddr'=>$key['hostaddr'],'port'=>$key['port'],'cpu_cores'=>$key['cpu_cores'],'initial_storage_GB'=>$key['initial_storage_GB'],'max_storage_GB'=>$key['max_storage_GB'],'innodb_buffer_pool_MB'=>$key['innodb_buffer_pool_MB'],'rocksdb_buffer_pool_MB'=>$key['rocksdb_buffer_pool_MB'],'name'=>'mysql','shard_name'=>$res[$row]['name']));
									$shard_link=array('from'=>$shard_node_id, 'to'=>$shard_n_id,'text'=>$key['hostaddr']);
									array_push($nodes,$shard_node);
									array_push($links,$shard_link);
								}

							}
						}
					}*/

				}
			}
			//array_unique($nodes);
		}
		//获取计算节点数据
		$sql = "select id,name,port,hostaddr,cpu_cores,max_mem_MB,max_conns,status from comp_nodes where db_cluster_id='$id' and status!='deleted'";
		$this->load->model('Cluster_model');
		$res_comp = $this->Cluster_model->getList($sql);
		if ($res_comp !== false) {
			foreach ($res_comp as $row => $value) {
				foreach ($value as $key2 => $value2) {
					//存储节点
					if ($key2 == 'port') {
						if (!empty($value2)) {
							$shard_node_id = 'cnode' . $res_comp[$row]['id'];
							/*$shard_node_id='comp_name'.$res_comp[$row]['id'];
							$shard_comp_id='comp'.$res_comp[$row]['id'];
							$shard_node1=array('id'=>$shard_node_id, 'text'=>$res_comp[$row]['name']);
							$shard_link1=array('from'=>$comp_id, 'to'=>$shard_node_id);
							array_push($nodes,$shard_node1);
							array_push($links,$shard_link1);*/
							$shard_node = array('id' => $shard_node_id, 'text' => $value2, 'data' => array('cluster_name' => $cluster_name, 'nick_name' => $cluster_text, 'port' => $value2, 'hostaddr' => $res_comp[$row]['hostaddr'], 'cpu_cores' => $res_comp[$row]['cpu_cores'], 'max_mem_MB' => $res_comp[$row]['max_mem_MB'], 'max_conns' => $res_comp[$row]['max_conns'], 'name' => 'pgsql', 'comp' => $res_comp[$row]['name'], 'status' => $res_comp[$row]['status'], 'cluster_id' => $clusterID, 'comp_id' => $res_comp[$row]['id']));
							$shard_link = array('from' => $cluster_id, 'to' => $shard_node_id, 'text' => $res_comp[$row]['hostaddr'] . '(计算节点)');
							array_push($nodes, $shard_node);
							array_push($links, $shard_link);
						}
					}
				}
			}
		}
		array_unique($nodes, SORT_REGULAR);
		array_unique($links, SORT_REGULAR);
		$arr = array('rootId' => 'a', 'nodes' => $nodes, 'links' => $links);
		$data['code'] = 200;
		$data['list'] = $arr;
		print_r(json_encode($data));
	}

	public function getShardNode($cluster_id, $id)
	{
		$sql = "select id,hostaddr,port,user_name,passwd,cpu_cores,initial_storage_GB,max_storage_GB,innodb_buffer_pool_MB,rocksdb_buffer_pool_MB,sync_state,replica_delay from shard_nodes where shard_id='$id' and db_cluster_id='$cluster_id' and status!='deleted'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		return $res;
	}

	public function getShardNodeIp($cluster_id, $id, $ip, $master, $status)
	{
		$sql = "select id,hostaddr,port,user_name,passwd,cpu_cores,initial_storage_GB,max_storage_GB,innodb_buffer_pool_MB,rocksdb_buffer_pool_MB,sync_state,replica_delay from shard_nodes where shard_id='$id' and db_cluster_id='$cluster_id' and status!='deleted' ";
		if (!empty($ip)) {
			$sql .= " and hostaddr like '%$ip%'";
		}
		if (!empty($master)) {
			$sql .= " and member_state ='$master'";
		}
		if (!empty($status)) {
			$sql .= " and status ='$status'";
		}
		//echo $sql;exit;
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		return $res;
	}

	//获取当前元数据的集群信息
	public function getMetaCluster()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$user_name = $arr['user_name'];
		//查user_id
		$this->load->model('Login_model');
		$user_sql = "select id from kunlun_user where name='$user_name';";
		$res_user = $this->Login_model->getList($user_sql);
		if (!empty($res_user)) {
			$user_id = $res_user[0]['id'];
		} else {
			$data['code'] = 500;
			$data['message'] = '该用户不存在';
			print_r(json_encode($data));
			return;
		}
		$sql = "select db_cluster_id,shard_id from shard_nodes where status!='deleted' group by db_cluster_id,shard_id;";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$error_list = array();
		$error_msg = array();
		foreach ($res as $row) {
			$res_cluster = $this->getClusterName($row['db_cluster_id']);
			if (!empty($res_cluster)) {
				$cluster_name = $res_cluster[0]['nick_name'];
			} else {
				$cluster_name = '';
			}
			$shard_name = $this->getShardName($row['db_cluster_id'], $row['shard_id']);
			if (!empty($cluster_name) && $shard_name) {
				$error = $this->shardError($row['db_cluster_id'], $row['shard_id'], $user_id, $shard_name, $cluster_name);
				foreach ($error as $error_row) {
					array_push($error_list, $error_row);
				}
				//$error_list=array_merge($error);
			}
		}
		$error_list = array_unique($error_list);
		print_r(json_encode($error_list));

	}

	public function shardError($cluster_id, $id, $user_id, $shard_name, $cluster_name)
	{
		//查是否存在最大延迟值
		$this->load->model('Login_model');
		//如不存在创建表
		$sql_table = "CREATE TABLE IF NOT EXISTS shard_max_dalay (id int unsigned NOT NULL AUTO_INCREMENT,shard_id int unsigned NOT NULL,db_cluster_id int unsigned NOT NULL,user_id int unsigned NOT NULL,max_delay_time int NOT NULL DEFAULT '100',when_created timestamp NULL DEFAULT CURRENT_TIMESTAMP,update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb3;";
		$this->Login_model->updateList($sql_table);
		$select_sql = "select max_delay_time from shard_max_dalay where shard_id='$id' and db_cluster_id='$cluster_id' and user_id='$user_id'";
		$res_select = $this->Login_model->getList($select_sql);
		if (!empty($res_select)) {
			$maxtime = $res_select[0]['max_delay_time'];
		} else {
			$maxtime = '100';
		}
		$this->load->model('Cluster_model');
		$sql = "select hostaddr,port,replica_delay from shard_nodes where shard_id='$id' and db_cluster_id='$cluster_id' and replica_delay>'$maxtime' and status!='deleted'";
		$res = $this->Cluster_model->getList($sql);
		$error = array();
		if (!empty($res)) {
			foreach ($res as $row) {
				array_push($error, $cluster_name . '(' . $cluster_id . ')集群的' . $shard_name . '中,' . $row['hostaddr'] . '(' . $row['port'] . ')' . '当前延迟为' . $row['replica_delay'] . 's,超过延迟最大值' . $maxtime . 's');
			}
		}
		//存储节点异常
		$sql_storage = "select hostaddr,port from shard_nodes where shard_id='$id' and db_cluster_id='$cluster_id' and status='inactive'";
		$res_storage = $this->Cluster_model->getList($sql_storage);
		if (!empty($res_storage)) {
			foreach ($res_storage as $row_stor) {
				array_push($error, $cluster_name . '(' . $cluster_id . ')集群的' . $shard_name . '中,' . $row_stor['hostaddr'] . '(' . $row_stor['port'] . ')' . '存储节点异常');
			}
		}
		return $error;
	}

	public function postgresEnable()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$post_data = str_replace("\\/", "/", json_encode($string));
		$this->load->model('Cluster_model');
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}
//	public  function prometheusEnable(){
//		//获取token
//		$arr = apache_request_headers();//获取请求头数组
//		$token=$arr["Token"];
//		if (empty($token)) {
//			$data['code'] = 201;
//			$data['message'] = 'token不能为空';
//			print_r(json_encode($data));return;
//		}
//		//判断参数
//		$string=json_decode(@file_get_contents('php://input'),true);
//		$ver = $string['ver'];
//		$job_type = $string['job_type'];
//		$user_name = $string['username'];
//		$uuid = $string['job_id'];
//		$meta_json=array(
//			'ver'=>$ver,
//			'job_id'=>$uuid,
//			'job_type'=>$job_type,
//			'user_name'=>$user_name,
//		);
//		$post_data=str_replace("\\/", "/", json_encode($meta_json));
//		//print_r($post_data);exit;
//		$this->load->model('Cluster_model');
//		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
//		$post_arr = json_decode($post_arr, TRUE);
//		if(!empty($post_arr)){
//			if($post_arr['result']=='accept'){
//				$data['code'] = 200;
//				$data['message'] = 'success';
//			}elseif($post_arr['result']=='busy'){
//				$data['code'] = 501;
//				$data['message'] = '系统正在操作中，请等待一会！';
//			}else{
//				$data['code'] = 500;
//				$data['message'] = $post_arr['message'];
//			}
//		}
//		$data['res']=$post_arr;
//		$data['uuid']=$uuid;
//		print_r(json_encode($data));
//	}
	public function mysqlEnable()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$post_data = str_replace("\\/", "/", json_encode($string));
		$this->load->model('Cluster_model');
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function delShard()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//验证该账户是否有删除shard的权限
		$this->load->model('Login_model');
		$res_priv = $this->Login_model->authority($string['user_name'], 'shard_drop_priv');
		if ($res_priv == true) {
			//调接口
			$this->load->model('Cluster_model');
			$post_data = str_replace("\\/", "/", json_encode($string));
			$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
			$post_arr = json_decode($post_arr, TRUE);
			$data = $post_arr;
		} else {
			$data['error_info'] = '该帐户不具备删除shard权限';
		}
		print_r(json_encode($data));
	}

	public function delComp()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//验证该账户是否有删除计算节点的权限
		$this->load->model('Login_model');
		$res_priv = $this->Login_model->authority($string['user_name'], 'compute_node_drop_priv');
		if ($res_priv == true) {
			//调接口
			$this->load->model('Cluster_model');
			$post_data = str_replace("\\/", "/", json_encode($string));
			$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
			$post_arr = json_decode($post_arr, TRUE);
			$data = $post_arr;
		} else {
			$data['error_info'] = '该帐户不具备删除计算节点权限';
		}
		print_r(json_encode($data));
	}

	public function delSnode()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//验证该账户是否有删除储存节点的权限
		$this->load->model('Login_model');
		$res_priv = $this->Login_model->authority($string['user_name'], 'storage_node_drop_priv');
		if ($res_priv == true) {
			//调接口
			$this->load->model('Cluster_model');
			$post_data = str_replace("\\/", "/", json_encode($string));
			$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
			$post_arr = json_decode($post_arr, TRUE);
			$data = $post_arr;
		} else {
			$data['error_info'] = '该帐户不具备删除存储节点权限';
		}
		print_r(json_encode($data));
	}

	public function controlInstance()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$control = $string['list']['paras']['control'];
		$type = $string['type'];
		$this->load->model('Login_model');
		if ($control == 'start') {
			if ($type == 'pgsql') {
				//检验这个用户是否有启用计算节点的权限
				$res_priv = $this->Login_model->authority($string['list']['user_name'], 'compute_enable_priv');
				if ($res_priv == false) {
					$data['error_info'] = '该账户不具备启用计算节点权限';
					print_r(json_encode($data));
					return;
				}
			}
			if ($type == 'mysql') {
				//检验这个用户是否有启用存储节点的权限
				$res_priv = $this->Login_model->authority($string['list']['user_name'], 'storage_enable_priv');
				if ($res_priv == false) {
					$data['error_info'] = '该账户不具备启用存储节点权限';
					print_r(json_encode($data));
					return;
				}
			}
		} elseif ($control == 'stop') {
			if ($type == 'pgsql') {
				//检验这个用户是否有禁用计算节点的权限
				$res_priv = $this->Login_model->authority($string['list']['user_name'], 'compute_disable_priv');
				if ($res_priv == false) {
					$data['error_info'] = '该账户不具备禁用计算节点权限';
					print_r(json_encode($data));
					return;
				}
			}
			if ($type == 'mysql') {
				//检验这个用户是否有禁用存储节点的权限
				$res_priv = $this->Login_model->authority($string['list']['user_name'], 'storage_disable_priv');
				if ($res_priv == false) {
					$data['error_info'] = '该账户不具备禁用存储节点权限';
					print_r(json_encode($data));
					return;
				}
			}
		} elseif ($control == 'restart') {
			if ($type == 'pgsql') {
				//检验这个用户是否有重启计算节点的权限
				if ($string['list']['user_name'] !== 'super_dba') {
					$data['error_info'] = 'super_dba账户才具备重启计算节点权限';
					print_r(json_encode($data));
					return;
				}
			}
			if ($type == 'mysql') {
				//检验这个用户是否有重启存储节点的权限
				if ($string['list']['user_name'] !== 'super_dba') {
					$data['error_info'] = 'super_dba账户才具备重启存储节点权限';
					print_r(json_encode($data));
					return;
				}
			}
		}
		$post_data = str_replace("\\/", "/", json_encode($string['list']));
		$this->load->model('Cluster_model');
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}
//	public function getRoleID($token){
//		//验证用户是否授权
//		$this->load->model('Login_model');
//		$res_token=$this->Login_model->getToken($token,'D',$this->key);
//		if(!empty($res_token)) {
//			$sql = "select count(id) as count,id from kunlun_user where name='$res_token';";
//			$res = $this->Login_model->getList($sql);
//			if (!empty($res)) {
//				if ($res[0]['count'] == 0) {
//					$data['code'] = 500;
//					$data['message'] = 'token错误';
//				} else {
//					//查看该账户是否有角色的权限
//					$user_id = $res[0]['id'];
//					$role_sql = "select role_id from kunlun_role_assign where user_id='$user_id';";
//					$res_role = $this->Login_model->getList($role_sql);
//					if ($res_role[0]['role_id'] === NULL) {
//						$data['code'] = 500;
//						$data['message'] = '该账户不是角色用户';
//					}
//					$role_id=$res_role[0]['role_id'];
//					$data['code'] = 200;
//					$data['role_id'] = $role_id;
//				}
//				return $data;
//			}
//		}
//	}
	public function getEffectCluster()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		$string = json_decode(@file_get_contents('php://input'), true);
		$effectCluster = $string['effectCluster'];
		$apply_all_cluster = $string['apply_all_cluster'];
		//验证token
		$this->load->model('Login_model');
		$res_token = $this->Login_model->getToken($token, 'D', $this->key);
		if (!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token' group by id;";

			$res = $this->Login_model->getList($sql);
//			print_r($sql);
//			exit;
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				} else {
					//获取用户数据
					if ($apply_all_cluster == "1") {
						if ($effectCluster == 'null') {
							$sql_name = "select id,name,when_created,nick_name from db_clusters where  memo!='' and memo is not null and status!='deleted'";
						}
					}
					if ($apply_all_cluster == "2") {
						if (empty($effectCluster)) {
							$effectCluster = 'NULL';
						}
						if (strpos($effectCluster, ',') == false) {
							$sql_name = "select id,name,nick_name from db_clusters where id=$effectCluster and  memo!='' and memo is not null and status!='deleted'";
						} else {
							$sql_name = "select id,name,nick_name from db_clusters where id in ($effectCluster) and  memo!='' and memo is not null and status!='deleted'";
						}
					}
					$this->load->model('Cluster_model');

					//exit($sql_name);
					$res_name = $this->Cluster_model->getList($sql_name);
					$data['code'] = 200;
					$data['list'] = $res_name;
					$data['total'] = $res_name ? count($res_name) : 0;
					print_r(json_encode($data));
				}
			}
		}
	}

	public function updateAssign()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		$string = json_decode(@file_get_contents('php://input'), true);
		$effectCluster = $string['effectCluster'];
		$cluster_name = $string['cluster_name'];
		$username = $string['username'];
		$type = $string['type'];
		//验证token
		$this->load->model('Login_model');
		$this->load->model('Cluster_model');
		$res_token = $this->Login_model->getToken($token, 'D', $this->key);
		if (!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token';";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
					return;
				}
				//查集群id
				$sql_id = "select id from db_clusters where name='$cluster_name'";
				$res_id = $this->Cluster_model->getList($sql_id);
				if ($res_id !== false) {
					$cluster_id = $res_id[0]['id'];
					//根据用户id查role_id
					$user_id = $res[0]['id'];
					$sql_role = "select role_id from kunlun_role_assign where user_id='$user_id'";
					$res_role = $this->Login_model->getList($sql_role);
					$role_count = count($res_role);
					if ($role_count == 1) {
						$role_id = $res_role[0]['role_id'];
						//$affected_clusters=$effectCluster.','.$cluster_id;
						if ($type === 'add') {
							$affected_clusters = $effectCluster . ',' . $cluster_id;
						} elseif ($type === 'del') {
							$cluster_arr = explode(',', $effectCluster);
							foreach ($cluster_arr as $key => $value) {
								if ($cluster_id == $value) unset($cluster_arr[$key]);
							}
							if (empty($cluster_arr)) {
								$affected_clusters = '';
							} else {
								$affected_clusters = implode(',', $cluster_arr);
							}
						}
						//更新授权表
						$sql_update = "update kunlun_role_assign set affected_clusters='$affected_clusters',update_time=now() where user_id='$user_id' and role_id='$role_id' and apply_all_cluster=2";
						//print_r($sql_update);exit;
						$res_update = $this->Login_model->updateList($sql_update);
						if ($res_update == 1) {
							$data['code'] = 200;
							$data['effectCluster'] = $affected_clusters;
							$data['message'] = '更新成功';
						} else {
							$data['code'] = 500;
							$data['message'] = '更新失败';
						}
					} elseif ($role_count > 1) {
						if ($type === 'add') {
							$affected_clusters = $effectCluster . ',' . $cluster_id;
							$cluster_append = ',' . $cluster_id;
							$sql_update = "update kunlun_role_assign set affected_clusters =CONCAT(affected_clusters,'$cluster_append') where user_id='$user_id'  and apply_all_cluster=2";
						} elseif ($type === 'del') {
							$cluster_arr = explode(',', $effectCluster);
							$cluster_arr = array_unique($cluster_arr);
							foreach ($cluster_arr as $key => $value) {
								if ($cluster_id == $value) unset($cluster_arr[$key]);
							}
							//print_r($cluster_arr);exit;
							if (empty($cluster_arr)) {
								$affected_clusters = '';
							} else {
								$affected_clusters = implode(',', $cluster_arr);
							}
							$sql_update = "update kunlun_role_assign set affected_clusters = trim(both ',' from replace(concat(',', affected_clusters, ','), ',$cluster_id,', ',')) where user_id='$user_id'  and apply_all_cluster=2";
						}
						$res_update = $this->Login_model->updateList($sql_update);
						if ($res_update !== 0) {
							$data['code'] = 200;
							$data['effectCluster'] = $affected_clusters;
							$data['message'] = '更新成功';
						} else {
							$data['code'] = 500;
							$data['message'] = '更新失败';
						}
					}
				}
				print_r(json_encode($data));
			}
		}
	}

	public function delAssign()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		$string = json_decode(@file_get_contents('php://input'), true);
		$effectCluster = $string['effectCluster'];
		$cluster_name = $string['cluster_name'];
		$username = $string['username'];
		//验证token
		$this->load->model('Login_model');
		$this->load->model('Cluster_model');
		$res_token = $this->Login_model->getToken($token, 'D', $this->key);
		if (!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token';";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
					return;
				}
				//查集群id
				$sql_id = "select id from db_clusters where name='$cluster_name'";
				$res_id = $this->Cluster_model->getList($sql_id);
				if ($res_id !== false) {
					$cluster_id = $res_id[0]['id'];
					//根据用户id查role_id
					$user_id = $res[0]['id'];
					$sql_role = "select role_id from kunlun_role_assign where user_id='$user_id'";
					$res_role = $this->Login_model->getList($sql_role);
					$role_count = count($res_role);
					if ($role_count == 1) {
						$role_id = $res_role[0]['role_id'];
						$affected_clusters = $effectCluster . ',' . $cluster_id;
						//更新授权表
						$sql_update = "update kunlun_role_assign set affected_clusters='$affected_clusters',update_time=now() where user_id='$user_id' and role_id='$role_id' and apply_all_cluster=2";
						$res_update = $this->Login_model->updateList($sql_update);
						if ($res_update == 1) {
							$data['code'] = 200;
							$data['effectCluster'] = $affected_clusters;
							$data['message'] = '更新成功';
						} else {
							$data['code'] = 500;
							$data['message'] = '更新失败';
						}
					} elseif ($role_count > 1) {
						$affected_clusters = $effectCluster . ',' . $cluster_id;
						$cluster_append = ',' . $cluster_id;
						$sql_update = "update kunlun_role_assign set affected_clusters =CONCAT(affected_clusters,'$cluster_append') where user_id='$user_id'  and apply_all_cluster=2";
						$res_update = $this->Login_model->updateList($sql_update);
						if ($res_update !== 0) {
							$data['code'] = 200;
							$data['effectCluster'] = $affected_clusters;
							$data['message'] = '更新成功';
						} else {
							$data['code'] = 500;
							$data['message'] = '更新失败';
						}
					}
				}
				print_r(json_encode($data));
			}
		}
	}

	public function getStatus()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		//print_r($post_arr);exit;
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}
//	public function clusterStatus(){
//		//判断参数
//		$string=json_decode(@file_get_contents('php://input'),true);
//		$ver = $string['ver'];
//		$uuid = $string['job_id'];
//		$job_type = $string['job_type'];
//		$meta_json=array(
//			'ver'=>$ver,
//			'job_id'=>$uuid,
//			'job_type'=>$job_type
//		);
//		$post_data=str_replace("\\/", "/", json_encode($meta_json));
//		//print_r($post_data);exit;
//		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
//		$post_arr = json_decode($post_arr, TRUE);
//		$data['res']=$post_arr;
//		$data['uuid']=$uuid;
//		print_r(json_encode($data));
//	}
//	public function machineStatus(){
//		//判断参数
//		$string=json_decode(@file_get_contents('php://input'),true);
//		$ver = $string['ver'];
//		$uuid = $string['job_id'];
//		$job_type = $string['job_type'];
//		$meta_json=array(
//			'ver'=>$ver,
//			'job_id'=>$uuid,
//			'job_type'=>$job_type
//		);
//		$post_data=str_replace("\\/", "/", json_encode($meta_json));
//		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
//		$post_arr = json_decode($post_arr, TRUE);
//		$data['res']=$post_arr;
//		$data['uuid']=$uuid;
//		print_r(json_encode($data));
//	}
	public function getNode()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$shard_id = $string['shard_id'];
		$cluster_id = $string['cluster_id'];
		$tree_id = $string['tree_id'];
		$ha_mode = $string['ha_mode'];
		$arr = array();
		$nodes = array();
		$links = array();

		$this->load->model('Login_model');
		$this->load->model('Cluster_model');
		$res_token = $this->Login_model->getToken($token, 'D', $this->key);
		if (!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token' group by id;";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				} else {
					$master = '';
					$status = '';
					//获取集群数据
					$shard_arr = $this->getShardNode($cluster_id, $shard_id);
					if ($shard_arr !== false) {
						foreach ($shard_arr as $key) {
							$shard_arr_id = $key['id'];
							$shard_arr_name = $key['port'];
							$ip = $key['hostaddr'];
							$username = $key['user_name'];
							$pwd = $key['passwd'];
							if ($ha_mode == 'rbr') {
								$sql_rbr = "select member_state,status from shard_nodes where db_cluster_id='$cluster_id' and shard_id='$shard_id' and hostaddr='$ip' and port='$shard_arr_name' and status!='deleted'";
								$res_rbr = $this->Cluster_model->getList($sql_rbr);
								if ($res_rbr[0]['member_state'] == 'source') {
									$master = 'true';
								} else {
									$master = 'false';
								}
								// if($res_rbr[0]['status']=='active'){
								// 	$status='online';
								// }elseif($res_rbr[0]['status']=='manual_stop'){
								// 	$status='offline';
								// }else{
								// 	$status=$res_rbr[0]['status'];
								// }
								$status = $res_rbr[0]['status'];
							} elseif ($ha_mode == 'mgr') {
								$per_dbname = 'performance_schema';
								$sql_main = "select MEMBER_ROLE,MEMBER_STATE from replication_group_members where MEMBER_HOST='$ip' and MEMBER_PORT='$shard_arr_name';";
								$res_main = $this->Cluster_model->getMysql($ip, $shard_arr_name, $username, $pwd, $per_dbname, $sql_main);
								if ($res_main['code'] == 200) {
									if (isset($res_main[0])) {
										if ($res_main[0] == 'PRIMARY') {
											$master = 'true';
										} else {
											$master = 'false';
										}
									} else {
										$master = '';
									}
									if (isset($res_main[1])) {
										if ($res_main[1] == 'ONLINE') {
											$status = 'online';
										} else {
											$status = 'offline';
										}
									} else {
										$master = '';
										$status = 'offline';
									}
								} else {
									$master = '';
									$status = 'offline';
								}
							}
							$shard_n_id = 'snode' . $shard_arr_id;
							//获取集群的名称
							$c_name = $this->getClusterName($cluster_id);
							$cluster_name = $c_name[0]['name'];
							$nick_name = $c_name[0]['nick_name'];
							//获取shard名称
							$shard_name = $this->getShardName($cluster_id, $shard_id);
							$shard_node = array('id' => $shard_n_id, 'text' => $shard_arr_name, 'data' => array('cluster_id' => $cluster_id, 'hostaddr' => $key['hostaddr'], 'port' => $key['port'], 'cpu_cores' => $key['cpu_cores'], 'initial_storage_GB' => $key['initial_storage_GB'], 'max_storage_GB' => $key['max_storage_GB'], 'innodb_buffer_pool_MB' => $key['innodb_buffer_pool_MB'], 'rocksdb_buffer_pool_MB' => $key['rocksdb_buffer_pool_MB'], 'name' => 'mysql', 'shard_id' => $shard_id, 'cluster_name' => $cluster_name, 'nick_name' => $nick_name, 'shard_name' => $shard_name, 'status' => $status, 'master' => $master, 'delay' => $key['replica_delay'], 'sync_status' => $key['sync_state']));
							$shard_link = array('from' => $tree_id, 'to' => $shard_n_id, 'text' => $key['hostaddr'] . '(存储节点)');
							array_push($nodes, $shard_node);
							array_push($links, $shard_link);
						}

					}
					array_unique($nodes, SORT_REGULAR);
					array_unique($links, SORT_REGULAR);
					$arr = array('nodes' => $nodes, 'links' => $links);
					$data['code'] = 200;
					$data['list'] = $arr;
					print_r(json_encode($data));
				}
			}
		}
	}


	public function getShardCount()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$shard_id = $string['shard_id'];
		$cluster_id = $string['cluster_id'];

		$this->load->model('Login_model');
		$this->load->model('Cluster_model');
		$res_token = $this->Login_model->getToken($token, 'D', $this->key);
		if (!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token' group by id;";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				} else {
					$total_sql = "select count(id) as count from shard_nodes where id='$shard_id' and db_cluster_id='$cluster_id' ";
					$res_total = $this->Cluster_model->getList($total_sql);
					$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
					print_r(json_encode($data));
				}
			}
		}
	}

	public function getStandbyNode()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//print_r($string);exit;
		$cluster_id = $string['cluster_id'];
		$shard_id = $string['shard_id'];
		$ha_mode = $string['ha_mode'];
		if ($ha_mode == 'mgr') {
			$sql = "select hostaddr,port,user_name,passwd from shard_nodes where db_cluster_id='$cluster_id' and shard_id='$shard_id' and status!='deleted'";
			$this->load->model('Cluster_model');
			$res = $this->Cluster_model->getList($sql);
			$arr = array();
			if (!empty($res)) {
				foreach ($res as $row) {
					$ip = $row['hostaddr'];
					$port = $row['port'];
					$username = $row['user_name'];
					$pwd = $row['passwd'];
					$per_dbname = 'performance_schema';
					$sql_main = "select MEMBER_ROLE from replication_group_members where MEMBER_HOST='$ip' and MEMBER_PORT='$port';";
					$res_main = $this->Cluster_model->getMysql($ip, $port, $username, $pwd, $per_dbname, $sql_main);
					if (isset($res_main[0])) {
						if ($res_main[0] == 'SECONDARY') {
							$ip_arr = array('ip' => $ip, 'port' => $port, 'status' => $res_main[0]);
							array_push($arr, $ip_arr);
						}
					} else {
						$type = 'OFFLINE';
						$ip_arr = array('ip' => $ip, 'port' => $port, 'status' => $type);
						array_push($arr, $ip_arr);
					}
				}
			}
		}
		if ($ha_mode == 'rbr') {
			$sql = "select hostaddr as ip,port from shard_nodes where db_cluster_id='$cluster_id' and shard_id='$shard_id' and member_state='replica' and status not in('inactive','deleted')";
			$this->load->model('Cluster_model');
			$res = $this->Cluster_model->getList($sql);
			$arr = $res;
		}
		$data['code'] = 200;
		$data['list'] = $arr;
		print_r(json_encode($data));
	}

	public function getShardPrimary()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$cluster_id = $string['cluster_id'];
		$shard_id = $string['shard_id'];
		$ha_mode = $string['ha_mode'];
		if ($ha_mode == 'mgr') {
			$sql = "select hostaddr,port,user_name,passwd from shard_nodes where db_cluster_id='$cluster_id' and shard_id='$shard_id'";
			$this->load->model('Cluster_model');
			$res = $this->Cluster_model->getList($sql);
			$arr = array();
			if (!empty($res)) {
				foreach ($res as $row) {
					$ip = $row['hostaddr'];
					$port = $row['port'];
					$username = $row['user_name'];
					$pwd = $row['passwd'];
					$per_dbname = 'performance_schema';
					$sql_main = "select MEMBER_ROLE from replication_group_members where MEMBER_HOST='$ip' and MEMBER_PORT='$port';";
					$res_main = $this->Cluster_model->getMysql($ip, $port, $username, $pwd, $per_dbname, $sql_main);
					if (isset($res_main[0])) {
						if ($res_main[0] == 'PRIMARY') {
							$ip_arr = array('ip' => $ip, 'port' => $port, 'status' => $res_main[0]);
							array_push($arr, $ip_arr);
						}
					} else {
						$type = 'OFFLINE';
						$ip_arr = array('ip' => $ip, 'port' => $port, 'status' => $type);
						array_push($arr, $ip_arr);
					}
				}
			}
		}
		if ($ha_mode == 'rbr') {
			$sql = "select hostaddr as ip,port from shard_nodes where db_cluster_id='$cluster_id' and shard_id='$shard_id' and member_state='source' ";
			$this->load->model('Cluster_model');
			$res = $this->Cluster_model->getList($sql);
			$arr = $res;
		}
		$data['code'] = 200;
		$data['list'] = $arr;
		print_r(json_encode($data));
	}

	function getUnixTimestamp()
	{
		list($s1, $s2) = explode(' ', microtime());
		return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
	}

	public function getEffectComp()
	{
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$effectCluster = $string['effectCluster'] == 'null' ? null : $string['effectCluster'];
		$user_name = $string['user_name'] == 'null' ? null : $string['user_name'];
		$this->load->model('Cluster_model');
		if (empty($effectCluster)) {
			$sql = "select hostaddr,port from comp_nodes where status ='active'  ORDER BY id desc;";
		} else {
			$s = explode(",", $effectCluster);
			$effect = "'" . implode("','", $s) . "'";
			$sql = "select hostaddr,port from comp_nodes where db_cluster_id in ($effect) and status ='active' ORDER BY id desc;";
		}
		$res = $this->Cluster_model->getList($sql);
		if (!empty($res)) {
			$host = $res[0]['hostaddr'];
			$port = $res[0]['port'];
			//先创建用户
			$username = $this->default_username;
			$pgusername = $this->pg_username;
			$db_prefix = $this->db_prefix;
			//$user="DO \$body$ BEGIN IF NOT EXISTS (SELECT * FROM   pg_catalog.pg_user WHERE  usename = '$username') THEN CREATE ROLE $username LOGIN PASSWORD '$username'; END IF;END \$body$;";
			$user = "SELECT usename FROM  pg_catalog.pg_user WHERE  usename = '$username'";
			$res_usename = $this->Cluster_model->DB($user, $host, $port, $pgusername);
			/*if($res_usename['code']==500){
				$data['message'] = $res_usename['error'];
				$data['code'] = $res_usename['code'];
				print_r(json_encode($data));return;
			}else{
				if(empty($res_usename['arr'])){*/
			if (empty($res_usename)) {
				$createuser = "CREATE ROLE $username LOGIN PASSWORD '$username';";
				$res_cuser = $this->Cluster_model->DB($createuser, $host, $port, $pgusername);
				if ($res_cuser['code'] == 500) {
					$data['message'] = $res_cuser['error'];
					$data['code'] = $res_cuser['code'];
					print_r(json_encode($data));
					return;
				}
			}
//			}
			//创建数据库
			$db_name = $db_prefix . $user_name;
			//var_dump($db_name);exit;
			//$sqls="DO \$body$ BEGIN IF NOT EXISTS (select 1 from pg_database where datname ='$db_name' ) THEN CREATE DATABASE $db_name; END IF;END \$body$;";
			$selectdb = "select 1 from pg_database where datname ='$db_name';";
			$res_user = $this->Cluster_model->DB($selectdb, $host, $port, $pgusername);
			if (empty($res_user)) {
				$createdb = "CREATE DATABASE  $db_name;";
				$res_cdb = $this->Cluster_model->DB($createdb, $host, $port, $pgusername);
				if ($res_cdb['code'] == 500) {
					$data['message'] = $res_cdb['error'];
					$data['code'] = $res_cdb['code'];
					print_r(json_encode($data));
					return;
				}
				$this->Cluster_model->DB("DROP DATABASE  $db_prefix;", $host, $port, $pgusername);
				$command = "/var/www/html/sysbench-tpcc/prepare.sh $host $port $db_name $username $username 2>&1";
				exec($command, $array, $state);
			}
			$node = array();
			//获取表信息
			$tablesql = "select tablename from pg_tables where schemaname='public';";
			$res_table = $this->Cluster_model->getResult($tablesql, $host, $port, $username, $db_name);
			if ($res_table !== false) {
				foreach ($res_table as $key => $value) {
					if ($key == 'arr') {
						foreach ($value as $key2 => $value2) {
							if (is_object($value2)) {
								$value2 = (array)$value2;
							}
							if (is_array($value2)) {
								$table = $value2['tablename'];
								//print_r($table);exit;
								//获取表的shard
								$sql1 = "select t1.name from pg_shard t1 join pg_class t2 on t2.relshardid=t1.id and t2.relname='$table';";
								$res1 = $this->Cluster_model->getResult($sql1, $host, $port, $username, $db_name);
								if (!empty($res1['arr'])) {
									$shard = (array)$res1['arr'][0];
									$tablename = $table . '(' . $shard['name'] . ')';
								} else {
									$tablename = $table . '()';
								}
								//获取表字段
								$field = "select a.attname AS column_name,concat_ws('',t.typname,SUBSTRING(format_type(a.atttypid,a.atttypmod) from '\(.*\)')) as udt_name from pg_class c, pg_attribute a , pg_type t where  c.relname = '$table' and a.attnum>0 and a.attrelid = c.oid and a.atttypid = t.oid ;";
								$res_field = $this->Cluster_model->getResult($field, $host, $port, $username, $db_name);
								$field_arr = array();
								foreach ($res_field['arr'] as $key3 => $value3) {
									$field_one = $value3['column_name'] . '----' . $value3['udt_name'];
									$list = array('id' => $key3, 'label' => $field_one);
									array_push($field_arr, $list);

								}
								$arr = array('id' => $key2, 'label' => $tablename, 'children' => $field_arr);
								array_push($node, $arr);
							}
						}
					}
				}
			}
			$nodes = array('id' => 0, 'label' => $db_name, 'children' => $node);
			$data['code'] = 200;
			$data['res'] = $nodes;
			$data['ip'] = $host;
			$data['port'] = $port;
			$data['db'] = $db_name;
			print_r(json_encode($data));
		} else {
			$data['code'] = 501;
			$data['message'] = '请先创建集群再体验!';
			print_r(json_encode($data));
		}
	}

	public function countStr($str)
	{
		$search_char = "'";
		$count = 0;
		for ($x = 0; $x < strlen($str); $x++) {
			if (substr($str, $x, 1) == $search_char) {
				$count = $count + 1;
			}
		}
		return $count;
	}

	public function getExperience()
	{
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$text = $string['text'];
		$ip = $string['ip'];
		$port = $string['port'];
		$db = $string['db'];
		$username = $this->default_username;
		$this->load->model("Cluster_model");
		//判断字符串的长度不能超过2000
		$len = strlen($text);
		if ($len > 2000) {
			$data['message'] = '请输入2000字节以内的sql语句！';
		} else {
			//请求时控制重复执行不能小于1秒
			if (!isset($_SESSION['last_time'])) {
				//先获取时间戳
				$time = $this->getUnixTimestamp();
				$_SESSION['last_time'] = $time;
			} else {
				$last_time = $_SESSION['last_time'];
				$current_time = $this->getUnixTimestamp();
				$min = $current_time - $last_time;
				$_SESSION['last_time'] = $current_time;
				if ($min <= 1000) {
					$data['message'] = '操作太频繁，请1秒后重试！';
					$data['code'] = 200;
					print_r(json_encode($data));
					return;
				}
			}
			//如果多条语句一起执行时，需要增加影响行数总数：affected rows和rows
			//按分号分割字符串，引号内的分号不做处理
			//$matches = preg_split('/(?<!\d)(;)/', $text);
//			$arr = str_getcsv($text,';');
			//引号内多个分号todo
			$matches = preg_split("/;(?=[^'(.*?)'])/", $text);
			//preg_match_all("#([^;]+;'[^(]+'[')])#isU",$text,$matches);
			//preg_match_all("/;(?=[^'(.*?)[^;*]'])/",$text,$matches);
//			$matches=array();
//			for ($i=0;$i<count($arr);$i++){
//				$count=$this->countStr($arr[$i]);
//				if($count%2==0){
//					array_push($matches,$arr[$i]);
//				}else{
//					$next=$arr[$i].';'.$arr[$i+1];
//					$next_count=$this->countStr($next);
//					if($next_count%2!==0){
//					}else{
//						if((strpos($next,'insert') !== false||strpos($next,'INSERT') !== false)&&(strpos($next,'values') !== false||strpos($next,'VALUES') !== false)&&strpos($next,'(') !== false&&strpos($next,')') !== false){
//							array_push($matches,$next);
//						}
//					}
//				}
//			}

			//print_r($matches);;exit;
			$length = count($matches);
			//print_r($matches);;exit;
			$all = $matches[$length - 1] ? $length - 1 : $length - 2;
			$count = 0;
			$len = '';
			$time = 0;
			$history = array();
			$res_date = array();
			$now = date('Y-m-d H:i:s', time());
			$res_date1 = '';
			for ($i = 0; $i <= $all; $i++) {
				$len1 = '';
				$time1 = '';
				$count1 = 0;
				$result = '';
				//语句添加分号处理
				if ($all == $length - 2) {
					$sql = $matches[$i] . ';';
				} else {
					if ($all == $i) {
						$sql = $matches[$i];
					} else {
						$sql = $matches[$i] . ';';
					}
				}
				//查询
				if (stripos($sql, 'select') !== false) {
					$res_count = $this->Cluster_model->getResult($sql, $ip, $port, $username, $db);
					if ($res_count['code'] == 200) {
						$select_count = count($res_count['arr']);
						//执行时间
						$time1 = $res_count['times'];
						//影响的行数
						//$len1='<p><span class="e">===Success===</span><span class="e">[SQL]:'.$sql.'</span><span class="e">影响行数：'.$select_count.'</span><span class="e">时间：'.$time1.'ms</span></p>';
						$len1 = '<p><span class="e">===Success===</span><span class="e">[SQL]:' . $sql . '</span><span class="e">影响行数：' . $select_count . '</span><span class="e">时间：' . $time1 . 'ms</span></p>';
						//执行历史表格里的行数
						$count1 = $select_count;
						$data['list'] = $res_count;
						$result = '执行成功';
						$data['code'] = $res_count['code'];
						//查询列表
						$res_date1 = $res_count;
					} else {
						//$len1='<p><span class="r">===Error===</span><span class="e">[SQL]:'.$sql.'</span><span class="e">[Err]：'.$res_count['error'].'</span></p>';
						$len1 = '<p><span class="r">===Error===</span><span class="e">[SQL]:' . $sql . '</span><span class="e">[Err]：' . $res_count['error'] . '</span></p>';
						$data['code'] = $res_count['code'];
						$data['error'] = $res_count['error'];
						$result = $res_count['error'];
						$res_date1 = '';
					}
				} else {
					//其他sql
					$res_q = $this->Cluster_model->getResult($sql, $ip, $port, $username, $db);
					if ($res_q['code'] == 200) {
						$time1 = $res_q['times'];
						$len1 = '<p><span class="e">===Success===</span><span class="e">[SQL]:' . $sql . '</span><span class="e">影响行数：' . $res_q['q'] . '</span><span style="display: block">时间：' . $time1 . 'ms</span></p>';
						$count1 = 1;
						$data['result'] = '执行成功';
						$data['code'] = $res_q['code'];
						$result = '执行成功';
						$res_date1 = '';
					} else {
						$len1 = '<p><span class="r">===Error===</span><span class="e">[SQL]:' . $sql . '</span><span class="e">[Err]：' . $res_q['error'] . '</span></p>';
						$data['code'] = $res_q['code'];
						$data['error'] = $res_q['error'];
						$result = $res_q['error'];
						$res_date1 = '';
					}
				}
				$arr = array('sql' => $sql, 'result' => $result, 'time' => $now, 'ms' => $time1, 'lines' => $count1);
				array_push($history, $arr);
				if (!empty($res_date1)) {
					$res_date[] = $res_date1;
				}
				$len .= $len1;
				//$time+=$time1;
				$count = $count1;
				$time = $time1;
			}
			$data['count'] = $count;
			$data['len'] = $len;
			$data['times'] = $time;
			$data['history'] = $history;
			$data['res_date'] = $res_date;
			$data['code'] = 200;
		}
		print_r(json_encode($data));
	}

	public function getClusterShards()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$cluster_id = $string['id'];
		$this->load->model('Cluster_model');
		//nodes
		$sql_nodes = "select count(id) as count from shard_nodes where db_cluster_id='$cluster_id' and status!='deleted'";
		$nodes = $this->Cluster_model->getList($sql_nodes);
		//shard
		$sql_shard = "select count(id) as count from shards where db_cluster_id='$cluster_id' and status!='deleted'";
		$shards = $this->Cluster_model->getList($sql_shard);
		//comp
		$sql_comp = "select count(id) as count from comp_nodes where db_cluster_id='$cluster_id' and status!='deleted'";
		$comp = $this->Cluster_model->getList($sql_comp);
		$data['code'] = 200;
		$data['shard'] = (int)$shards[0]['count'];
		$data['nodes'] = (int)$nodes[0]['count'];
		$data['comp'] = (int)$comp[0]['count'];
		print_r(json_encode($data));
	}

	public function timediff($begin_time, $end_time)
	{
		if ($begin_time < $end_time) {
			$starttime = $begin_time;
			$endtime = $end_time;
		} else {
			$starttime = $end_time;
			$endtime = $begin_time;
		}
		//计算天数
		$timediff = $endtime - $starttime;
		$days = intval($timediff / 86400);
		//计算小时数
		$remain = $timediff % 86400;
		$hours = intval($remain / 3600);
		//计算分钟数
		$remain = $remain % 3600;
		$mins = intval($remain / 60);
		//计算秒数
		$secs = $remain % 60;
		//$res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs);
		if (empty($days) && empty($hours) && empty($mins) && !empty($secs)) {
			$res = $secs . '秒';
		} elseif (empty($days) && empty($hours) && !empty($mins)) {
			$res = $mins . '分钟' . $secs . '秒';
		} elseif (empty($days) && !empty($hours)) {
			$res = $hours . '小时' . $mins . '分钟' . $secs . '秒';
		} else {
			$res = $days . '天' . $hours . '小时' . $mins . '分钟' . $secs . '秒';
		}
		return $res;
	}

	public function getSwitcheOverList()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$pageNo = $arr['pageNo'];
		$pageSize = $arr['pageSize'];
		$username = $arr['hostaddr'];
		$startTime = $arr['startTime'];
		$endTime = $arr['endTime'];
		$id = $arr['id'];
		$start = $pageNo - 1;
		//获取用户数据
		$this->load->model('Cluster_model');
		$sql = "select id,host,taskid,timestamp from rbr_consfailover where taskid is not null and cluster_id='$id'";
//		$sql="select id,host,taskid,timestamp from rbr_consfailover where taskid is not null ";
		if (!empty($username)) {
			$sql .= " and host like '%$username%'";
		}
		if (!empty($startTime)) {
			$sql .= " and timestamp between '$startTime' and '$endTime' ";
		}
		$sql .= " group by taskid order by id desc limit " . $pageSize . " offset " . $start;
		//echo $sql;exit;
		$res = $this->Cluster_model->getList($sql);
		if ($res === false) {
			$res = array();
		} else {
			foreach ($res as $row => $value) {
				foreach ($value as $key2 => $value2) {
					//开始时间
					if ($key2 == 'timestamp') {
						if (!empty($value2)) {
							$res[$row]['start_time'] = $value2;
						}
					}
					if ($key2 == 'taskid') {
						if (!empty($value2)) {
							$arr = $this->getNewHost($value2);
							$res[$row]['end_time'] = $arr[0]['timestamp'];
							$res[$row]['new_master_host'] = $arr[0]['new_master_host'];
							$res[$row]['cluster_id'] = $arr[0]['cluster_id'];
							$res[$row]['shard_id'] = $arr[0]['shard_id'];
							$res[$row]['shard_name'] = $this->getShardName($arr[0]['cluster_id'], $arr[0]['shard_id']);
							$res[$row]['err_code'] = $arr[0]['err_code'];
							if ($arr[0]['err_code'] == '0') {
								$res[$row]['status'] = '成功';
							} else {
								$res[$row]['status'] = '失败';
							}
							$res[$row]['err_msg'] = $arr[0]['err_msg'];
							//耗时
							$res[$row]["count_time"] = $this->timediff(strtotime($arr[0]['timestamp']), strtotime($res[$row]["timestamp"]));
						}
					}
				}
			}
		}
		$total_sql = "select count(id) as count from (select id from rbr_consfailover  where taskid is not null and cluster_id='$id' ";
		if (!empty($username)) {
			$total_sql .= " and host like '%$username%'";
		}
		if (!empty($startTime)) {
			$total_sql .= " and timestamp between '$startTime' and '$endTime' ";
		}
		$total_sql .= " group by taskid) a";
		$res_total = $this->Cluster_model->getList($total_sql);
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
		print_r(json_encode($data));
	}

	public function getNewHost($id)
	{
		$sql = "select new_master_host,timestamp,err_code,err_msg,shard_id,cluster_id from rbr_consfailover where taskid='$id' order by id desc limit 1";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		return $res;
	}

	public function getTaskList()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$taskid = $arr['taskid'];
		$this->load->model('Cluster_model');
		$sql = "select * from rbr_consfailover where taskid ='$taskid'";
		$res = $this->Cluster_model->getList($sql);
		$data['code'] = 200;
		$data['list'] = $res;
		print_r(json_encode($data));
	}

	public function getStroMachine()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		$sql = "select id,hostaddr from server_nodes where machine_type is not null and machine_type='storage' and node_stats='running'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res ? count($res) : 0;
		print_r(json_encode($data));
	}

	public function getCompMachine()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		$sql = "select id,hostaddr from server_nodes where machine_type is not null and machine_type='computer' and node_stats='running'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = count($res);
		print_r(json_encode($data));
	}

	public function getBackUpStorage()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function getClusterDetail()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function switchShard()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		if ($string['user_name'] !== 'super_dba') {
			$data['error_info'] = 'super_dba账户才具备主备切换权限';
			print_r(json_encode($data));
			return;
		}
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function getStorageList()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function addStorage()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function updateStorage()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function delStorage()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function rebuildNode()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		if ($string['user_name'] !== 'super_dba') {
			$data['error_info'] = 'super_dba账户才具备重做备机节点权限';
			print_r(json_encode($data));
			return;
		}
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		//print_r($post_data);exit;
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function getMetaPrimary()
	{
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function getShardsJobLog()
	{
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$id = $string['id'];
		//调接口
		$this->load->model('Cluster_model');
		$sql_nodes = "select job_info from mysql_pg_install_log where general_log_id='$id'";
		$nodes = $this->Cluster_model->getList($sql_nodes);
		$shard_nodes = array();
		if (!empty($nodes)) {
			if (!empty($nodes[0]['job_info'])) {
				$node_list = json_decode($nodes[0]['job_info']);
				foreach ($node_list as $row => $value) {
					foreach ($value as $key => $value2) {
						if ($key == 'piece') {
							array_push($shard_nodes, $value2);
						}
					}
				}

			}
		}
		$data['shard_nodes'] = $shard_nodes;
		print_r(json_encode($data));
	}

	public function getShardsCount()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$cluster_id = $arr['id'];
		$sql = "select count(id) as count from shards where db_cluster_id='$cluster_id' and status!='deleted'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		//print_r($res);exit;
		$data['code'] = 200;
		$data['total'] = $res ? (int)$res[0]['count'] : 0;
		print_r(json_encode($data));
	}

	public function getCompsCount()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$cluster_id = $arr['id'];
		$sql = "select count(id) as count from comp_nodes where db_cluster_id='$cluster_id' and status!='deleted'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		//print_r($res);exit;
		$data['code'] = 200;
		$data['total'] = $res ? (int)$res[0]['count'] : 0;
		print_r(json_encode($data));
	}

	public function getNodesCount()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$cluster_id = $arr['id'];
		$shard_id = $arr['shard_id'];
		$sql = "select count(id) as count from shard_nodes where db_cluster_id='$cluster_id' and shard_id='$shard_id'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		//print_r($res);exit;
		$data['code'] = 200;
		$data['total'] = $res ? (int)$res[0]['count'] : 0;
		print_r(json_encode($data));
	}

	public function getOldCluster()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$cluster_id = $arr['cluster_id'];
		$sql = "select id,name,nick_name	 from db_clusters where id!='$cluster_id' and memo!='' and memo is not null and status!='deleted'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		//print_r($res);exit;
		$data['code'] = 200;
		$data['list'] = $res;
		print_r(json_encode($data));
	}

	public function getBackupStorageList()
	{
		$sql = "select id,hostaddr,port from backup_storage GROUP BY hostaddr,port";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		//print_r($res);exit;
		$data['code'] = 200;
		$data['list'] = $res;
		print_r(json_encode($data));
	}

	public function getBackStorageList()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$pageNo = $arr['pageNo'];
		$pageSize = $arr['pageSize'];
		$name = $arr['name'];
		$start = ($pageNo - 1) * $pageSize;

		$this->load->model('Cluster_model');
		$sql = "select id,hostaddr,port,name,stype from backup_storage ";
		if (!empty($name)) {
			$sql .= " where name like '%$name%'";
		}
		$sql .= " GROUP BY hostaddr,port order by id desc limit " . $pageSize . " offset " . $start;
		//echo $sql;exit;
		$res = $this->Cluster_model->getList($sql);
		$total_sql = "select count(id) as count from backup_storage ";
		if (!empty($name)) {
			$total_sql .= " where name like '%$name%'";
		}
		$total_sql .= " GROUP BY hostaddr,port";
		$res_total = $this->Cluster_model->getList($total_sql);
		//print_r($res);exit;
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
		print_r(json_encode($data));
	}

	//异常集群列表
	public function clusterListError()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$pageNo = $arr['pageNo'];
		$pageSize = $arr['pageSize'];
		$username = $arr['name'];
		$user_name = $arr['user_name'];
		$effectCluster = $arr['effectCluster'] == 'null' ? null : $arr['effectCluster'];
		$apply_all_cluster = $arr['apply_all_cluster'];
		$start = ($pageNo - 1) * $pageSize;
		//获取用户数据
		if ($apply_all_cluster == 1) {
			if (empty($effectCluster)) {
				$sql = "select id,name,when_created,nick_name,ha_mode,memo from db_clusters where memo='' or memo is  null and status!='deleted'";
				if (!empty($username)) {
					$sql .= " and nick_name like '%$username%'";
				}
			}
		}
		if ($apply_all_cluster == 2) {
			if (empty($effectCluster)) {
				$effectCluster = 'NULL';
			}
			if (!strpos($effectCluster, ',')) {
				$sql = "select id,name,when_created,nick_name,ha_mode,memo from db_clusters where id=$effectCluster and memo='' or memo is  null and status!='deleted'";
			} else {
				$sql = "select id,name,when_created,nick_name,ha_mode,memo from db_clusters where id in ($effectCluster) and memo='' or memo is  null and status!='deleted'";
			}
			if (!empty($username)) {
				$sql .= " and nick_name like '%$username%'";
			}
		}
		$sql .= " order by id desc limit " . $pageSize . " offset " . $start;
		//print_r($sql);exit;
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		//total
		if ($apply_all_cluster == 1) {
			if (empty($effectCluster)) {
				$total_sql = "select count(id) as count from db_clusters where memo='' or memo is null and status!='deleted'";
				if (!empty($username)) {
					$total_sql .= " and name like '%$username%'";
				}
			}
		}
		if ($apply_all_cluster == 2) {
			if (!strpos($effectCluster, ',')) {
				$total_sql = "select count(id) as count from db_clusters where id=$effectCluster  and memo='' or memo is  null and status!='deleted'";
			} else {
				$total_sql = "select count(id) as count from db_clusters where id in ($effectCluster) and memo='' or memo is null and status!='deleted'";
			}
			if (!empty($username)) {
				$total_sql .= " and nick_name like '%$username%'";
			}
		}
		$res_total = $this->Cluster_model->getList($total_sql);
		if ($res === false) {
			$res = array();
		} else {
			foreach ($res as $row => $value) {
				foreach ($value as $key2 => $value2) {
					//shard数
					if ($key2 == 'id') {
						if (!empty($value2)) {
							$shardtotal = $this->getThisShards($value2, $user_name);
							$comptotal = $this->getThisComps($value2);
							$first_backup = $this->getLastBackup($value2);
							//$nodetotal= $this->getThisNodes($value2);
							$res[$row]['shardtotal'] = $shardtotal['nodedetail'];
							$res[$row]['comptotal'] = $comptotal['list'] !== false ? count($comptotal['list']) : 0;
							$res[$row]['first_backup'] = $first_backup;

						}
					}
					if ($key2 == 'memo') {
						$sitrng = json_decode($res[$row]['memo'], true);
						if (!empty($sitrng)) {
							if (!empty($string['nodes'])) {
								$res[$row]['snode_count'] = $string['nodes'];
							} else {
								$res[$row]['snode_count'] = '';
							}
							if (!empty($string['innodb_size'])) {
								$res[$row]['buffer_pool'] = $string['innodb_size'];
							} else {
								$res[$row]['buffer_pool'] = '';
							}
							if (!empty($string['machinelist'])) {
								$res[$row]['machinelist'] = $string['machinelist'];
							} else {
								$res[$row]['machinelist'] = '';
							}
						} else {
							$res[$row]['snode_count'] = '';
							$res[$row]['buffer_pool'] = '';
							$res[$row]['machinelist'] = '';
						}
					}
				}
			}
		}
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
		print_r(json_encode($data));
	}

	public function getShardsName()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$cluster_id = $arr['id'];
		$sql = "select id,name,db_cluster_id as cluster_id from shards where  db_cluster_id='$cluster_id' and status!='deleted'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$data['code'] = 200;
		$data['list'] = $res;
		print_r(json_encode($data));
	}

	public function getCompDBName()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$cluster_id = $arr['id'];
		//取任意一个计算节点
		$ip = '';
		$port = '';
		$name = '';
		$result = false;
		$sql = "select port,hostaddr,user_name from comp_nodes where db_cluster_id='$cluster_id' and status='active' ";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$res_count = count($res);
		if (!empty($res)) {
			foreach ($res as $knode => $vnode) {
				//连接该计算节点取库名
				$res_main = $this->getDBName($vnode['hostaddr'], $vnode['port'], $vnode['user_name']);
				if ($res_main['code'] == 500) {
					if ($res_count == ($knode + 1)) {
						$data['code'] = 500;
						$data['message'] = '计算节点连接异常';
						print_r(json_encode($data));
						return;
					} else {
						continue;
					}
				} else {
					$ip = $vnode['hostaddr'];
					$port = $vnode['port'];
					$name = $vnode['user_name'];
					$result = $res_main;
					break;
				}
			}
		} else {
			$data['code'] = 500;
			$data['message'] = '该集群无可用的计算节点';
			print_r(json_encode($data));
			return;
		}
		$data['code'] = 200;
		$data['list'] = $result;
		$data['ip'] = $ip;
		$data['port'] = $port;
		$data['name'] = $name;
		print_r(json_encode($data));
	}

	public function getCompDBTable()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$database = $arr['database'];
		$ip = $arr['ip'];
		$port = $arr['port'];
		$username = $arr['name'];
		$this->load->model('Cluster_model');
		$sql_main = "select relname,n.nspname,relshardid From pg_class,pg_namespace n where relnamespace = n.oid and relshardid != 0;";
		$res = $this->Cluster_model->getResult($sql_main, $ip, $port, $username, $database);
		$data['code'] = 200;
		$data['list'] = $res;
		print_r(json_encode($data));
	}

	public function getShardTable()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$shard_id = $arr['id'];
		$cluster_id = $arr['cluster_id'];
		$sql = "select hostaddr,port,user_name,passwd from shard_nodes where  db_cluster_id='$cluster_id' and shard_id='$shard_id' and status!='inactive' and member_state='source' ";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		//print_r($res);exit;
		if (!empty($res)) {
			$sql_main = "select TABLE_SCHEMA,TABLE_NAME from TABLES WHERE TABLE_SCHEMA NOT IN('mysql','information_schema','kunlun_dba_tools_db','kunlun_metadata_db','kunlun_sysdb','sys','performance_schema');";
			$res_main = $this->Cluster_model->getMysql($res[0]['hostaddr'], $res[0]['port'], $res[0]['user_name'], $res[0]['passwd'], 'information_schema', $sql_main);
			//print_r($res_main);exit;
			if ($res_main['code'] == 200) {
				if (isset($res_main['list'])) {
					$tablelist = $res_main['list'];
				} else {
					$tablelist = false;
				}
				$data['code'] = 200;
				$data['list'] = $tablelist;
			} else {
				$data['code'] = 500;
				$data['message'] = $res_main[0];
			}
		} else {
			$data['code'] = 500;
			$data['message'] = '该shard没有主节点';
		}
		print_r(json_encode($data));
	}

	public function getOtherShards()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$cluster_id = $arr['cluster_id'];
		$shard_id = $arr['shard_id'];
		$sql = "select id,name from shards where  db_cluster_id='$cluster_id' and id!='$shard_id' and status!='deleted'";
		//echo $sql;exit;
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$data['code'] = 200;
		$data['list'] = $res;
		print_r(json_encode($data));
	}

	public function expandCluster()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//验证该账户是否有恢复集群的权限
		$this->load->model('Login_model');
		$res_priv = $this->Login_model->authority($string['user_name'], 'expand_cluster_priv');
		if ($res_priv == true) {
			//调接口
			$this->load->model('Cluster_model');
			$post_data = str_replace("\\/", "/", json_encode($string));
			$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
			$post_arr = json_decode($post_arr, TRUE);
			$data = $post_arr;
		} else {
			$data['error_info'] = '该帐户不具备集群扩容权限';
		}
		print_r(json_encode($data));
	}

	public function getExpandTableList()
	{//无判断权限
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function setMaxDalay()
	{//无判断权限
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$cluster_id = $string['cluster_id'];
		$shard_id = $string['shard_id'];
		$maxtime = $string['maxtime'];
		$user_name = $string['user_name'];
		$this->load->model('Login_model');
		//查user_id
		$user_sql = "select id from kunlun_user where name='$user_name';";
		$res_user = $this->Login_model->getList($user_sql);
		if (!empty($res_user)) {
			$user_id = $res_user[0]['id'];
		} else {
			$data['code'] = 500;
			$data['message'] = '该用户不存在';
			print_r(json_encode($data));
		}
		//如不存在创建表
		$sql = "CREATE TABLE IF NOT EXISTS shard_max_dalay (id int unsigned NOT NULL AUTO_INCREMENT,shard_id int unsigned NOT NULL,db_cluster_id int unsigned NOT NULL,user_id int unsigned NOT NULL,max_delay_time int NOT NULL DEFAULT '100',when_created timestamp NULL DEFAULT CURRENT_TIMESTAMP,update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb3;";
		$res = $this->Login_model->updateList($sql);
		if ($res == 0) {
			//先查是否存在此条数据，存在更新 不存在insert
			$select_sql = "select id from shard_max_dalay where shard_id='$shard_id' and db_cluster_id='$cluster_id' and user_id='$user_id'";
			$res_select = $this->Login_model->getList($select_sql);
			if (!empty($res_select)) {
				$update_sql = "update shard_max_dalay set max_delay_time='$maxtime',update_time=now() where  shard_id='$shard_id' and db_cluster_id='$cluster_id' and user_id='$user_id'; ";
				$res_update = $this->Login_model->updateList($update_sql);
				if ($res_update == 1) {
					$data['code'] = 200;
					$data['message'] = '设置成功';
				} else {
					$data['code'] = 500;
					$data['message'] = '该shard的最大延迟时间已经存在，无需重复设置';
				}
				print_r(json_encode($data));
			} else {
				$insert_sql = "insert into shard_max_dalay(shard_id,db_cluster_id,user_id,max_delay_time) values ('$shard_id','$cluster_id','$user_id','$maxtime'); ";
				$res_insert = $this->Login_model->updateList($insert_sql);
				if ($res_insert == 1) {
					$data['code'] = 200;
					$data['message'] = '设置成功';
				} else {
					$data['code'] = 500;
					$data['message'] = '设置失败';
				}
				print_r(json_encode($data));
			}
		} else {
			print_r(json_encode($res));
		}
	}

	public function getMaxDalay()
	{//无判断权限
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$cluster_id = $string['cluster_id'];
		$shard_id = $string['shard_id'];
		$user_name = $string['user_name'];
		$this->load->model('Login_model');
		//查user_id
		$user_sql = "select id from kunlun_user where name='$user_name';";
		$res_user = $this->Login_model->getList($user_sql);
		if (!empty($res_user)) {
			$user_id = $res_user[0]['id'];
		} else {
			$data['code'] = 500;
			$data['message'] = '该用户不存在';
			print_r(json_encode($data));
		}
		$result = array();
		//先查表是否存在
		$sql = "select TABLE_NAME from information_schema.TABLES where TABLE_NAME = 'shard_max_dalay';";
		$res = $this->Login_model->getList($sql);
		if ($res !== false) {
			//表存在的情况
			$sql_select = "select max_delay_time from shard_max_dalay where db_cluster_id='$cluster_id' and shard_id='$shard_id' and user_id='$user_id'";
			$res_select = $this->Login_model->getList($sql_select);
			$result = $res_select;
		} else {
			$result = array();
		}
		$data['code'] = 200;
		$data['list'] = $result;
		print_r(json_encode($data));
	}

	public function setVariable()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		if ($string['user_name'] !== 'super_dba') {
			$data['error_info'] = 'super_dba账户才具备设置实例变量权限';
			print_r(json_encode($data));
			return;
		}
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		//print_r($post_data);exit;
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function getVariable()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		if ($string['user_name'] !== 'super_dba') {
			$data['error_info'] = 'super_dba账户才具备查看实例变量权限';
			print_r(json_encode($data));
			return;
		}
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		//print_r($post_data);exit;
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function getWorkMode()
	{
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		//print_r($post_data);exit;
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function getOneBackUpList()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$pageNo = $arr['pageNo'];
		$pageSize = $arr['pageSize'];
		$username = $arr['username'];
		$status = $arr['status'];
		$backup_type = $arr['backup_type'];
		$id = $arr['id'];
		$start = ($pageNo - 1) * $pageSize;
		$this->load->model('Cluster_model');
		//查cluster_id
		$sql_clusterid = "select id from db_clusters where memo!='' and memo is not null and status!='deleted' and id='$id'";
		$res_clusterid = $this->Cluster_model->getList($sql_clusterid);
		$cluster_id = '';
		if (!empty($res_clusterid)) {
			$clusterid_count = count($res_clusterid);
		} else {
			$clusterid_count = 0;
		}
		if ($clusterid_count > 1) {
			foreach ($res_clusterid as $row) {
				$cluster_id .= $row['id'] . ',';
			}
			$cluster_id = substr($cluster_id, 0, strlen($cluster_id) - 1);
		} else if ($clusterid_count == 1) {
			$cluster_id = $res_clusterid[0]['id'];
		} else {
			$data['code'] = 200;
			$data['list'] = array();
			$data['total'] = 0;
			print_r(json_encode($data));
			return;
		}
		//获取集群信息
		$sql = "select cluster_id,shard_id,comp_id,start_ts,end_ts,backup_type,status,memo from cluster_coldbackups where cluster_id in ($cluster_id)";
		//print_r($sql);exit;
		if (!empty($status)) {
			$sql .= " and  status='$status'";
		}
		if (!empty($backup_type)) {
			$sql .= " and  backup_type='$backup_type'";
		}
		$sql .= " order by id desc limit " . $pageSize . " offset " . $start;
		//echo $sql;exit;
		$res = $this->Cluster_model->getList($sql);
		$total_sql = "select count(id) as count from cluster_coldbackups  where cluster_id in ($cluster_id)";
		if (!empty($status)) {
			$total_sql .= " and  status='$status'";
		}
		if (!empty($backup_type)) {
			$total_sql .= " and  backup_type='$backup_type'";
		}
		$res_total = $this->Cluster_model->getList($total_sql);
		if (!empty($res)) {
			foreach ($res as $row => $value) {
				foreach ($value as $key2 => $value2) {
					//集群名称
					if ($key2 == 'cluster_id') {
						if (!empty($value2)) {
							$name = $this->getClusterName($value2);
							if (!empty($name)) {
								$res[$row]['cluster_name'] = $name[0]['name'];
								$res[$row]['nick_name'] = $name[0]['nick_name'];
							} else {
								$res[$row]['cluster_name'] = '';
								$res[$row]['nick_name'] = '';
							}
						} else {
							$res[$row]['cluster_name'] = '';
							$res[$row]['nick_name'] = '';
						}
					}
					//shar名称
					if ($key2 == 'shard_id') {
						if (!empty($value2)) {
							$name = $this->getShardName($res[$row]['cluster_id'], $value2);
							$res[$row]['shard_name'] = $name;
						} else {
							$res[$row]['shard_name'] = '';
						}
					}
					//comp名称
					if ($key2 == 'comp_id') {
						if (!empty($value2)) {
							$name = $this->getCompName($res[$row]['cluster_id'], $value2);
							$res[$row]['comp_name'] = $name;
						} else {
							$res[$row]['comp_name'] = '';
						}
					}
					//结果信息
					if ($key2 == 'memo') {
						if (!empty($value2)) {
							$newline = array("\r\n", "\n", "\r");
							$value2 = str_replace($newline, '', $value2);
							$value3 = json_decode($value2, true);
							$res[$row]['info'] = $value3['response_array'][0]['info']['info'];
						} else {
							$res[$row]['info'] = '';
						}
					}
				}
			}
		} else {
			$res = array();
		}
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
		print_r(json_encode($data));
	}

	public function computeList()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$pageNo = $arr['pageNo'];
		$pageSize = $arr['pageSize'];
		$username = $arr['hostaddr'];
		$status = $arr['status'];
		$id = $arr['id'];
		$start = ($pageNo - 1) * $pageSize;
		//print_r($pageSize);exit;
		//获取用户数据
		$this->load->model('Cluster_model');
		$sql = "select * from comp_nodes where status!='deleted' and db_cluster_id='$id'";
		if (!empty($username)) {
			$sql .= " and  hostaddr like '%$username%'";
		}
		if (!empty($status)) {
			$sql .= " and  status ='$status'";
		}
		$sql .= " order by id desc limit " . $pageSize . " offset " . $start;
		//print_r($sql);exit;
		$res = $this->Cluster_model->getList($sql);
		if ($res === false) {
			$res = array();
		}
		$sql_total = "select count(id) as count from comp_nodes where status!='deleted' and db_cluster_id='$id'";
		if (!empty($username)) {
			$sql_total .= " and  hostaddr like '%$username%'";
		}
		$res_total = $this->Cluster_model->getList($sql_total);
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
		print_r(json_encode($data));
	}

	public function shardList()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$pageNo = $arr['pageNo'];
		$pageSize = $arr['pageSize'];
		$user_name = $arr['hostaddr'];
		$id = $arr['id'];
		$ha_mode = $arr['ha_mode'];
		$master_all = $arr['master'];
		$status_all = $arr['status'];
		$start = ($pageNo - 1) * $pageSize;
		$this->load->model('Cluster_model');
		$sql = "select * from shards where status!='deleted' and db_cluster_id='$id'";
		if (!empty($user_name) || !empty($master_all) || !empty($status_all)) {
			//通过ip查出shard_id
			$sql_shard = "select shard_id from shard_nodes where db_cluster_id='$id'  and status!='deleted' ";
			if (!empty($user_name)) {
				$sql_shard .= " and  hostaddr like '%$user_name%' ";
			}
			if (!empty($master_all)) {
				$sql_shard .= " and member_state='$master_all' ";
			}
			if (!empty($status_all)) {
				$sql_shard .= " and status='$status_all' ";
			}
			$sql_shard .= " group by shard_id";
			$res_shard = $this->Cluster_model->getList($sql_shard);
			$shard_arrs = '';
			if ($res_shard !== false) {
				foreach ($res_shard as $key) {
					$shard_arrs .= $key['shard_id'] . ',';
				}
			}
			if (!empty($shard_arrs)) {
				$shard_arrs = substr($shard_arrs, 0, strlen($shard_arrs) - 1);
				$sql .= " and id in ($shard_arrs)";
			} else {
				$sql .= " ";
			}
		}
		$sql .= " order by id desc limit " . $pageSize . " offset " . $start;
		//print_r($sql);exit;
		$res = $this->Cluster_model->getList($sql);
		$sql_total = "select count(id) as count from shards where status!='deleted' and db_cluster_id='$id'";
		if (!empty($user_name) || !empty($master_all) || !empty($status_all)) {
			//通过ip查出shard_id
			$sql_shard = "select shard_id from shard_nodes where db_cluster_id='$id'  and status!='deleted' ";
			if (!empty($user_name)) {
				$sql_shard .= " and  hostaddr like '%$user_name%' ";
			}
			if (!empty($master_all)) {
				$sql_shard .= " and member_state='$master_all' ";
			}
			if (!empty($status_all)) {
				$sql_shard .= " and status='$status_all' ";
			}
			$sql_shard .= " group by shard_id";
			$res_shard = $this->Cluster_model->getList($sql_shard);
			$shard_arrs = '';
			if ($res_shard !== false) {
				foreach ($res_shard as $key) {
					$shard_arrs .= $key['shard_id'] . ',';
				}
			}
			if (!empty($shard_arrs)) {
				$shard_arrs = substr($shard_arrs, 0, strlen($shard_arrs) - 1);
				$sql_total .= " and id in ($shard_arrs)";
			} else {
				$sql_total .= " ";
			}
		}
		$res_total = $this->Cluster_model->getList($sql_total);
		if ($res === false) {
			$res = array();
		} else {
			foreach ($res as $row => $value) {
				foreach ($value as $key2 => $value2) {
					//获取shard下的节点信息（ip，port，status，replica_delay，member_statemember_state是否是主节点）
					if ($key2 == 'id') {
						if (!empty($value2)) {
							$master = '';
							$status = '';
							$nodes = array();
							$shard_arr = false;
							if (!empty($user_name) || !empty($master_all) || !empty($status_all)) {
								$shard_arr = $this->getShardNodeIp($id, $value2, $user_name, $master_all, $status_all);
							} else {
								$shard_arr = $this->getShardNode($id, $value2);
							}
							//print_r($shard_arr);
							if ($shard_arr !== false) {
								foreach ($shard_arr as $key) {
									$shard_arr_id = $key['id'];
									$shard_arr_name = $key['port'];
									$ip = $key['hostaddr'];
									$username = $key['user_name'];
									$pwd = $key['passwd'];
									if ($ha_mode == 'rbr') {
										$sql_rbr = "select member_state,status from shard_nodes where db_cluster_id='$id' and shard_id='$value2' and hostaddr='$ip' and port='$shard_arr_name' and status!='deleted'";
										$res_rbr = $this->Cluster_model->getList($sql_rbr);
										if ($res_rbr[0]['member_state'] == 'source') {
											$master = 'true';
										} else {
											$master = 'false';
										}
										if ($res_rbr[0]['status'] == 'active') {
											$status = 'online';
										} elseif ($res_rbr[0]['status'] == 'manual_stop') {
											$status = 'offline';
										} else {
											$status = $res_rbr[0]['status'];
										}
									} elseif ($ha_mode == 'mgr') {
										$per_dbname = 'performance_schema';
										$sql_main = "select MEMBER_ROLE,MEMBER_STATE from replication_group_members where MEMBER_HOST='$ip' and MEMBER_PORT='$shard_arr_name';";
										$res_main = $this->Cluster_model->getMysql($ip, $shard_arr_name, $username, $pwd, $per_dbname, $sql_main);
										if ($res_main['code'] == 200) {
											if (isset($res_main[0])) {
												if ($res_main[0] == 'PRIMARY') {
													$master = 'true';
												} else {
													$master = 'false';
												}
											} else {
												$master = '';
											}
											if (isset($res_main[1])) {
												if ($res_main[1] == 'ONLINE') {
													$status = 'online';
												} else {
													$status = 'offline';
												}
											} else {
												$master = '';
												$status = 'offline';
											}
										} else {
											$master = '';
											$status = 'offline';
										}
									}
									$shard_node = array('cluster_id' => $id, 'hostaddr' => $key['hostaddr'], 'port' => $key['port'], 'cpu_cores' => $key['cpu_cores'], 'initial_storage_GB' => $key['initial_storage_GB'], 'max_storage_GB' => $key['max_storage_GB'], 'innodb_buffer_pool_MB' => $key['innodb_buffer_pool_MB'], 'rocksdb_buffer_pool_MB' => $key['rocksdb_buffer_pool_MB'], 'shard_id' => $value2, 'status' => $status, 'master' => $master, 'delay' => $key['replica_delay'], 'sync_status' => $key['sync_state']);
									array_push($nodes, $shard_node);
								}
							}
							$res[$row]['shardList'] = $nodes;
						}
					}
				}
			}
		}
		//print_r($res_total);exit;
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
		print_r(json_encode($data));
	}

	public function getNoSwitchList()
	{
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		//print_r($post_data);exit;
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function setNoSwitch()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function delSwitch()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function getThisShardNodes()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//print_r($string);exit;
		$cluster_id = $string['cluster_id'];
		$id = $string['shard_id'];
		$sql = "select hostaddr as ip,port from shard_nodes where shard_id='$id' and db_cluster_id='$cluster_id' and status!='deleted'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$data['code'] = 200;
		$data['list'] = $res;
		print_r(json_encode($data));
	}

	//监控主备延迟，节点异常，机器离线，主备切换失败
	public function getClusterMonitor()
	{
		set_time_limit(0);
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$user_name = $arr['user_name'];
		//查user_id
		$this->load->model('Login_model');
		$user_sql = "select id from kunlun_user where name='$user_name';";
		$res_user = $this->Login_model->getList($user_sql);
		if (!empty($res_user)) {
			$user_id = $res_user[0]['id'];
		} else {
			$data['code'] = 500;
			$data['message'] = '该用户不存在';
			print_r(json_encode($data));
			return;
		}
		$sql = "select db_cluster_id,shard_id from shard_nodes where status!='deleted' group by db_cluster_id,shard_id;";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$error_list = array();
		if (!empty($res)) {
			//如不存在创建表
			$sql_table = "CREATE TABLE IF NOT EXISTS cluster_alarm_info (id bigint unsigned NOT NULL AUTO_INCREMENT,job_id VARCHAR (128) DEFAULT NULL,alarm_level enum('FATAL','ERROR','WARNING') NOT NULL DEFAULT 'WARNING',alarm_type varchar(128) DEFAULT NULL,status enum('unhandled','handled','ignore') NOT NULL DEFAULT 'unhandled',occur_timestamp timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),job_info text,handle_time timestamp(6) NULL DEFAULT NULL,handler_name varchar(128) DEFAULT NULL,cluster_id int unsigned DEFAULT NULL,shardid int unsigned DEFAULT NULL,compnodeid int unsigned DEFAULT NULL,svrid int unsigned DEFAULT NULL,PRIMARY KEY (id),UNIQUE KEY `id` (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb3;";
			$this->Cluster_model->updateList($sql_table);
			foreach ($res as $row) {
				$cluster_id = $row['db_cluster_id'];
				$shard_id = $row['shard_id'];
				$res_cluster = $this->getClusterName($row['db_cluster_id']);
				if (!empty($res_cluster)) {
					$cluster_name = $res_cluster[0]['nick_name'];
				} else {
					$cluster_name = '';
				}
				$shard_name = $this->getShardName($row['db_cluster_id'], $row['shard_id']);
				$error_msg = array();
				//计算节点异常
				$comp_node_error = $this->compNodeError($row['db_cluster_id'], $cluster_name);
				if (!empty($comp_node_error)) {
					//如果多个,instert多条语句；
					foreach ($comp_node_error as $comp_row) {
						$comp_id = $comp_row['id'];
						$arr = explode(',', $comp_row);
						$arr_ip = explode('(', $arr[1]);
						$ip = $arr_ip[0];
						$arr_port = explode(')', $arr_ip[1]);
						$port = $arr_port[0];
						$msg_data = urldecode(json_encode(array(
							'cluster_id' => $row['db_cluster_id'],
							'message' => urlencode($comp_row),
							'ip' => $ip,
							'port' => $port
						)));
						$select_comp_sql = "select job_info from cluster_alarm_info where job_info='$msg_data' and status='unhandled' ";
						$res_comp_select = $this->Cluster_model->getList($select_comp_sql);
						if ($res_comp_select == false) {
							$insert_comp_sql = "insert into cluster_alarm_info(alarm_type,job_info,occur_timestamp,cluster_id,compnodeid) values ('comp_node_exception','$msg_data',now(),'$cluster_id','$comp_id');";
							$res_comp_insert = $this->Cluster_model->updateList($insert_comp_sql);
						}
					}
				}
				//主备延迟,存储节点异常
				if (!empty($cluster_name) && $shard_name) {
					$error = $this->shardError($row['db_cluster_id'], $row['shard_id'], $user_id, $shard_name, $cluster_name);
					if (!empty($error)) {
						//如果多个,instert多条语句；
						foreach ($error as $error_row) {
							if (strpos($error_row, '延迟') !== false) {
								$arr = explode(',', $error_row);
								$arr_ip = explode('(', $arr[1]);
								$ip = $arr_ip[0];
								$arr_port = explode(')', $arr_ip[1]);
								$port = $arr_port[0];
								$msg_data = urldecode(json_encode(array(
									'cluster_id' => $row['db_cluster_id'],
									'shard_id' => $row['shard_id'],
									'message' => urlencode($error_row),
									'ip' => $ip,
									'port' => $port
								)));
								$select_sql = "select job_info from cluster_alarm_info where job_info='$msg_data' and status='unhandled' ";
								$res_select = $this->Cluster_model->getList($select_sql);
								if ($res_select == false) {
									$insert_sql = "insert into cluster_alarm_info(alarm_type,job_info,occur_timestamp,cluster_id,shardid) values ('standby_delay','$msg_data',now(),'$cluster_id','$shard_id');";
									$res_insert = $this->Cluster_model->updateList($insert_sql);
								}
							} else if (strpos($error_row, '存储节点异常') !== false) {
								$arr = explode(',', $error_row);
								$arr_ip = explode('(', $arr[1]);
								$ip = $arr_ip[0];
								$arr_port = explode(')', $arr_ip[1]);
								$port = $arr_port[0];
								$msg_data = urldecode(json_encode(array(
									'cluster_id' => $row['db_cluster_id'],
									'shard_id' => $row['shard_id'],
									'message' => urlencode($error_row),
									'ip' => $ip,
									'port' => $port
								)));
								$select_storage_sql = "select job_info from cluster_alarm_info where job_info='$msg_data' and status='unhandled' ";
								$res_storage_select = $this->Cluster_model->getList($select_storage_sql);
								if ($res_storage_select == false) {
									$insert_storage_sql = "insert into cluster_alarm_info(alarm_type,job_info,occur_timestamp,cluster_id,shardid) values ('storage_node_exception','$msg_data',now(),'$cluster_id','$shard_id');";
									$res_storage_insert = $this->Cluster_model->updateList($insert_storage_sql);
								}
							}
						}
					}
				}
			}
		}
		//设备离线
		$machine_error = $this->machineError();
		if (!empty($machine_error)) {
			foreach ($machine_error as $machine_row) {
				$arr = explode(',', $machine_row);
				$ip = $arr[0];
				$msg_data = urldecode(json_encode(array(
					'message' => urlencode($machine_row),
					'ip' => $ip
				)));
				$select_machine_sql = "select job_info from cluster_alarm_info where job_info='$msg_data' and status='unhandled' ";
				$res_machine_select = $this->Cluster_model->getList($select_machine_sql);
				if ($res_machine_select == false) {
					$insert_machine_sql = "insert into cluster_alarm_info(alarm_type,job_info,occur_timestamp) values ('machine_offline','$msg_data',now());";
					$res_machine_insert = $this->Cluster_model->updateList($insert_machine_sql);
				}
			}
		}
		//主备切换失败
		$manual_switch_error = $this->manualSwitchError();
		if (!empty($manual_switch_error)) {
			foreach ($manual_switch_error as $switch_row) {
				//print_r($switch_row['shard_id']);exit;
				$switch_cluster_id = $switch_row['cluster_id'];
				$switch_shard_id = $switch_row['shard_id'];
				$switch_row = json_encode($switch_row);
				$select_switch_sql = "select job_info from cluster_alarm_info where job_info='$switch_row' ";
				$res_select_switch = $this->Cluster_model->getList($select_switch_sql);
				if ($res_select_switch == false) {
					$insert_switch_sql = "insert into cluster_alarm_info(alarm_type,job_info,occur_timestamp,cluster_id,shardid) values ('manual_switch','$switch_row',now(),'$switch_cluster_id','$switch_shard_id');";
					$res_insert_switch = $this->Cluster_model->updateList($insert_switch_sql);
				}
			}
		}
		//搬表失败
		$table_move_error = $this->tableMoveError();
		if (!empty($table_move_error)) {
			foreach ($table_move_error as $move_row) {
				$move_cluster_id = $move_row['cluster_id'];
				$move_job_id = $move_row['id'];
				$move_shard_id = $move_row['src_shard_id'];
				//print_r($move_row);exit;
				$move_row = json_encode($move_row);
				$select_move_sql = "select job_id from cluster_alarm_info where job_id='$move_job_id' ";
				$res_move = $this->Cluster_model->getList($select_move_sql);
				if ($res_move == false) {
					$insert_move_sql = "insert into cluster_alarm_info(alarm_type,job_info,occur_timestamp,cluster_id,shardid,job_id) values ('expand_cluster','$move_row',now(),'$move_cluster_id','$move_shard_id','$move_job_id');";
					$res_insert_move = $this->Cluster_model->updateList($insert_move_sql);
				}
			}
		}

		//备份失败
		$table_backup_error = $this->backupError();
		if (!empty($table_backup_error)) {
			foreach ($table_backup_error as $backup_row) {
				$backup_job_id = $backup_row['id'];
				if (!empty($backup_row['cluster_id'])) {
					$backup_cluster_id = $backup_row['cluster_id'];
					$backup_comp_id = $backup_row['compid'];
					$backup_shard_id = $backup_row['shardid'];
					$backup_row = json_encode($backup_row);
					$select_backup_sql = "select job_id from cluster_alarm_info where job_id='$backup_job_id' ";
					$res_backup = $this->Cluster_model->getList($select_backup_sql);
					if ($res_backup == false) {
						$insert_backup_sql = "insert into cluster_alarm_info(alarm_type,job_info,occur_timestamp,job_id,cluster_id,shardid,compnodeid) values ('shard_coldbackup','$backup_row',now(),'$backup_job_id','$backup_cluster_id','$backup_shard_id','$backup_comp_id');";
						$res_insert_backup = $this->Cluster_model->updateList($insert_backup_sql);
					}
				}
			}
		}
		//增删集群，增删shard，增删nodes，全量备份失败
		$table_cluster_error = $this->clusterJobError();
		if (!empty($table_cluster_error)) {
			foreach ($table_cluster_error as $cluster_row) {
				if (isset($cluster_row['paras']['cluster_id'])) {
					$cluster_id = $cluster_row['paras']['cluster_id'];
				} else {
					$cluster_id = null;
				}
				$job_id = $cluster_row['id'];
				$job_type = $cluster_row['job_type'];
				if (isset($cluster_row['paras']['shard_id'])) {
					$shard_id = $cluster_row['paras']['shard_id'];
				} else {
					$shard_id = null;
				}
				$cluster_row = json_encode($cluster_row);
				$select_cluster_sql = "select job_id from cluster_alarm_info where job_id='$job_id' ";
				$res_cluster = $this->Cluster_model->getList($select_cluster_sql);
				if ($res_cluster == false) {
					$insert_cluster_sql = "insert into cluster_alarm_info(alarm_type,job_info,occur_timestamp,cluster_id,shardid,job_id) values ('$job_type','$cluster_row',now(),'$cluster_id','$shard_id','$job_id');";
					$res_insert_cluster = $this->Cluster_model->updateList($insert_cluster_sql);
				}
			}
		}
		//sleep(2);
	}

	public function storageNodeError($cluster_id, $id, $shard_name, $cluster_name)
	{
		$sql = "select hostaddr,port from shard_nodes where shard_id='$id' and db_cluster_id='$cluster_id' and status='inactive'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$error = array();
		if (!empty($res)) {
			foreach ($res as $row) {
				array_push($error, $cluster_name . '(' . $cluster_id . ')集群的' . $shard_name . '中,' . $row['hostaddr'] . '(' . $row['port'] . ')' . '存储节点异常');
			}
		}
		return $error;
	}

	public function compNodeError($cluster_id, $cluster_name)
	{
		$sql = "select id,hostaddr,port from comp_nodes where db_cluster_id='$cluster_id' and status='inactive'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$error = array();
		if (!empty($res)) {
			foreach ($res as $row) {
				array_push($error, $cluster_name . '(' . $cluster_id . ')集群中,' . $row['hostaddr'] . '(' . $row['port'] . ')' . '计算节点异常');
			}
		}
		return $error;
	}

	public function machineError()
	{
		$this->load->model('Cluster_model');
		$sql = "select hostaddr from server_nodes where node_stats='dead' GROUP BY hostaddr ";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$error = array();
		if (!empty($res)) {
			foreach ($res as $row) {
				array_push($error, $row['hostaddr'] . ',该设备离线了');
			}
		}
		return $error;
	}

	public function manualSwitchError()
	{
		$this->load->model('Cluster_model');
		$sql = "select host,shard_id,cluster_id,taskid,err_msg as message,consfailover_msg from rbr_consfailover where err_code!=0 ";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		foreach ($res as $row => $value) {
			foreach ($value as $key2 => $value2) {
				$string = preg_replace('/\r|\n/', '\n', trim($res[$row]['consfailover_msg']));
				$string = json_decode($string, true);
				if (!empty($string['sw_type'])) {
					$res[$row]['sw_type'] = $string['sw_type'];
				} else {
					$res[$row]['sw_type'] = '';
				}
			}
			unset($res[$row]['consfailover_msg']);
		}
		return $res;
	}

	public function tableMoveError()
	{
		$this->load->model('Cluster_model');
		$sql = "select id,when_started,when_ended,memo,job_info,user_name from cluster_general_job_log where status='failed' and job_type='expand_cluster'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		foreach ($res as $row => $value) {
			foreach ($value as $key2 => $value2) {
				$string = preg_replace('/\r|\n/', '\n', trim($res[$row]['memo']));
				$string = json_decode($string, true);
				if (!empty($string['error_info'])) {
					$res[$row]['message'] = $string['error_info'];
				} else {
					$res[$row]['message'] = '';
				}
				$job_info = preg_replace('/\r|\n/', '\n', trim($res[$row]['job_info']));
				$job_info = json_decode($job_info, true);
				if (!empty($job_info)) {
					$res[$row]['cluster_id'] = $job_info['paras']['cluster_id'];
					$res[$row]['drop_old_table'] = $job_info['paras']['drop_old_table'];
					$res[$row]['dst_shard_id'] = $job_info['paras']['dst_shard_id'];
					$res[$row]['src_shard_id'] = $job_info['paras']['src_shard_id'];
					$res[$row]['table_list'] = $job_info['paras']['table_list'];
				}
			}
			unset($res[$row]['memo']);
			unset($res[$row]['job_info']);
		}
		return $res;
	}

	public function backupError()
	{
		$this->load->model('Cluster_model');
		$sql = "select id,job_type,memo,when_started,when_ended,job_info,user_name from cluster_general_job_log where status='failed' and job_type='shard_coldbackup'; ";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		foreach ($res as $row => $value) {
			foreach ($value as $key2 => $value2) {
				$string = preg_replace('/\r|\n/', '\n', trim($res[$row]['memo']));
				$string = json_decode($string, true);
				if (!empty($string['error_info'])) {
					$res[$row]['message'] = $string['error_info'];
				} else {
					$res[$row]['message'] = '';
				}
				$job_info = preg_replace('/\r|\n/', '\n', trim($res[$row]['job_info']));
				$job_info = json_decode($job_info, true);
				if (!empty($job_info)) {
					//取出ip，port去查shard_node和comp_nodes表是否存在
					$if_exist = $this->getIpPort($job_info['paras']['ip'], $job_info['paras']['port']);
					if ($if_exist) {
						if ($if_exist[0]['type'] == 'compute') {
							$res[$row]['ip'] = $job_info['paras']['ip'];
							$res[$row]['port'] = $job_info['paras']['port'];
							$res[$row]['compid'] = $if_exist[0]['id'];
							$res[$row]['shardid'] = null;
							$res[$row]['cluster_id'] = $if_exist[0]['db_cluster_id'];
						} else {
							$res[$row]['ip'] = $job_info['paras']['ip'];
							$res[$row]['port'] = $job_info['paras']['port'];
							$res[$row]['compid'] = null;
							$res[$row]['shardid'] = $if_exist[0]['id'];
							$res[$row]['cluster_id'] = $if_exist[0]['db_cluster_id'];
						}
					} else {
						$res[$row]['ip'] = '';
						$res[$row]['port'] = '';
						$res[$row]['compid'] = '';
						$res[$row]['shardid'] = '';
						$res[$row]['cluster_id'] = '';
					}
				}
			}
			unset($res[$row]['memo']);
			unset($res[$row]['job_info']);
		}
		return $res;
	}

	public function clusterJobError()
	{
		$this->load->model('Cluster_model');
		$sql = "select id,job_type,memo,when_started,when_ended,job_info,user_name from cluster_general_job_log where status='failed' and job_type in('create_cluster','delete_cluster','add_shards','delete_shard','add_comps','delete_comp','add_nodes','delete_node','manual_backup_cluster');";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		foreach ($res as $row => $value) {
			foreach ($value as $key2 => $value2) {
				$string = preg_replace('/\r|\n/', '\n', trim($res[$row]['memo']));
				$string = json_decode($string, true);
				//if($value2!=='manual_backup_cluster'){
				if (!empty($string['error_info'])) {
					$res[$row]['message'] = $string['error_info'];
				} else {
					$res[$row]['message'] = '';
				}
				//}
				$job_info = preg_replace('/\r|\n/', '\n', trim($res[$row]['job_info']));
				$job_info = json_decode($job_info, true);
				//if($value2!=='manual_backup_cluster'){
				if (!empty($job_info['paras'])) {
					$res[$row]['paras'] = $job_info['paras'];
				} else {
					$res[$row]['paras'] = '';
				}
				//}
			}
			unset($res[$row]['memo']);
			unset($res[$row]['job_info']);
		}
		return $res;
	}

	public function getIpPort($ip, $port)
	{
		$sql = "select db_cluster_id,id from comp_nodes where hostaddr='$ip' and port='$port' and status!='deleted'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		if (!empty($res)) {
			foreach ($res as $row => $value) {
				$res[$row]['type'] = 'compute';
			}
			return $res;
		} else {
			$sql_shard = "select db_cluster_id,shard_id as id from shard_nodes where hostaddr='$ip' and port='$port' and status!='deleted'";
			$res_shard = $this->Cluster_model->getList($sql_shard);
			if (!empty($res_shard)) {
				foreach ($res_shard as $row1 => $value1) {
					$res_shard[$row1]['type'] = 'storage';
				}
				return $res_shard;
			} else {
				return '';
			}
		}
	}

	public function getDBName($ip, $port, $user_name)
	{
		$this->load->model('Cluster_model');
		$sql_main = "select datname from pg_database where datname not in('template0','template1');";
		$res = $this->Cluster_model->DB($sql_main, $ip, $port, $user_name);
		return $res;
	}
}
