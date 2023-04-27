<?php


defined('BASEPATH') or exit('No direct script access allowed');

class ClusterSetting extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->config->load('myconfig');
		$this->key = $this->config->item('key');
		$this->post_url = $this->config->item('post_url');
	}

	public function getDBName($ip, $port, $user_name)
	{
		$this->load->model('Cluster_model');
		$sql_main = "select datname from pg_database where datname not in('template0','template1');";
		$res = $this->Cluster_model->DB($sql_main, $ip, $port, $user_name);
		return $res;
	}

	/**
	 * 获取PG的表列表
	 * @throws
	 * @author wangwentao
	 */
	public function getPGTableList()
	{
		$clusterId = $this->input->get('cluster_id');
		$all = $this->input->get('all');

		$result = [];
		$sql = "select port,hostaddr,user_name from comp_nodes where db_cluster_id='$clusterId' and status='active' ";
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
					foreach ($res_main as $knode => $item) {
						$tmp = [];
						$tmp["ip"] = $ip;
						$tmp["port"] = $port;
						$tmp["user"] = $name;
						$tmp["value"] = $item->datname;
						$tmp["desc"] = $item->datname;
						$tmp["label"] = $item->datname;
						$this->load->model('Cluster_model');
						$schema = "select oid,* from pg_catalog.pg_namespace where nspname not in('pg_toast','pg_temp_1','pg_catalog','information_schema');";
						$scheam = $this->Cluster_model->getResult($schema, $ip, $port, $name, $item->datname);
						if ($scheam["code"] == 200) {
							$tmp["children"] = [];
							foreach ($scheam["arr"] as $v => $s) {
								$_schema = [];
								$_schema["label"] = $s["nspname"];
								$_schema["value"] = $s["nspname"];
								$_schema["desc"] = $s["nspname"];
								$_schema["children"] = [];
								$sql_main = "select relname,n.nspname,relshardid From pg_class,pg_namespace n where relnamespace = n.oid and nspname = '" . $s['nspname'] . "';";
								$res = $this->Cluster_model->getResult($sql_main, $ip, $port, $name, $item->datname);
								if ($res["code"] == 200) {
									foreach ($res["arr"] as $v => $val) {
										$_table = [];
										$_table["label"] = $val["relname"];
										$_table["value"] = $val["relname"];
										$_table["desc"] = $val["relname"];
										$_schema["children"][] = $_table;
										//print_r($_schema);
									}
								}
								$tmp["children"][] = $_schema;
							}
						}
						$result[] = $tmp;
					}
					break;
				}
			}
		} else {
			$data['code'] = 500;
			$data['message'] = '该集群无可用的计算节点';
			print_r(json_encode($data));
			return;
		}

		echo json_encode([
			'code' => 200,
			'list' => $result,
		]);
		exit();
		$sql = "select id,name from db_clusters where memo!='' and memo is not null and status!='deleted' and id = '{$clusterId}'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);


		if ($res === false) {
			throw new ApiException('所选集群不存在');
		}
		$nodeSql = "select hostaddr,port,user_name,passwd from comp_nodes where db_cluster_id = {$clusterId} and status <> 'deleted' order by id asc limit 1";
		$res = $this->Cluster_model->getList($nodeSql);
		if ($res === false) {
			throw new ApiException('未查询到PG节点');
		}
		$node = current($res);

		//print_r($node);

		$con = get_pg_con($node['hostaddr'], $node['port'], $node['user_name'], $node['passwd'], 'postgres');
		// 获取所有分区表
		$zoneTables = pg_find($con, "SELECT oid, relname from pg_class where relkind = 'p'");
		if (!$zoneTables) {
			$zoneTables = [];
		}

		// 遍历查询所有表信息
		$zoneTables = implode("','", array_column($zoneTables, 'relname'));
		if (!$zoneTables && $all !== '1') {
			$zoneTables = 1;
		}
		$sql = "select schemaname as schema,tablename as table,tableowner as db from pg_tables";
		if ($zoneTables) {
			$zoneTables = "'" . $zoneTables . "'";
			$sql .= " where tablename in ({$zoneTables})";
		}

		$tables = pg_find($con, $sql);

		//print_r($tables);
		//exit();
		$tableNodes = [];
		foreach ($tables as $table) {
			$tableNodes[$table['db']][$table['schema']][$table['table']] = $table['table'];
		}
		$data = [];
		foreach ($tableNodes as $key => $tableNode) {
			$list = [
				'value' => $key,
				'label' => $key,
				'desc' => 'db',
			];
			$children = [];
			foreach ($tableNode as $sk => $schema) {
				$schemaList = [
					'value' => $sk,
					'label' => $sk,
					'desc' => 'schema',
				];
				$schemaChildren = [];
				foreach ($schema as $tKey => $tableName) {
					$schemaChildren[] = [
						'value' => $tKey,
						'label' => $tKey,
						'desc' => 'table',
					];
				}
				$schemaList['children'] = $schemaChildren;
				$children[] = $schemaList;
			}
			$list['children'] = $children;
			$data[] = $list;
		}
		echo json_encode([
			'code' => 200,
			'list' => $data,
		]);
	}

	public function getDataCenters()
	{
		$sql = "select * from data_centers";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		if ($res === false) {
			$res = [];
		}
		$relation = [];
		foreach ($res as $key => $value) {

		}
		foreach ($res as $key => $value) {
			$res[$key]['node_num'] = 2;
			$res[$key]['master'] = false;
			$res[$key]['id'] = $value['name'];
		}
		;
		echo json_encode([
			'code' => 200,
			'list' => [
				'res' => $res,
				'r' => $relation,
			],
			'message' => 'success',
		]);
	}

	/**
	 * 集群下拉选项
	 * @author wangwentao
	 */
	public function clusterOptions()
	{
		$username = $this->input->get('name');
		$filterId = $this->input->get('filter_id');
		$sql = "select id,name from db_clusters where memo!='' and memo is not null and status!='deleted'";
		if (!empty($username)) {
			$sql .= " and nick_name like '%$username%'";
		}
		if ($filterId) {
			$sql .= " and id <> '{$filterId}'";
		}
		$sql .= ' order by id desc';
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		if ($res === false) {
			$res = [];
		}
		echo json_encode([
			'code' => 200,
			'list' => $res,
			'message' => 'success',
		]);
	}

	/**
	 * 表重分布
	 * @author wangwentao
	 */
	public function tableRepartition()
	{
		//获取token
		$arr = apache_request_headers(); //获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			throw new ApiException('token不能为空', 201);
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$data = json_decode($post_arr, true);
		$data['code'] = 200;
		if ($data['error_code'] !== '0') {
			$data['code'] = 500;
			$data['message'] = isset($data['error_info']) ? $data['error_info'] : '系统错误';
		}
		echo json_encode($data);
	}
}