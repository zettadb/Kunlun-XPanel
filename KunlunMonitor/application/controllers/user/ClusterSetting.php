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

	/**
	 * 获取PG的表列表
	 * @throws
	 * @author wangwentao
	 */
	public function getPGTableList()
	{
		$clusterId = $this->input->get('cluster_id');

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
		$con = get_pg_con($node['hostaddr'], $node['port'], $node['user_name'], $node['passwd'], 'postgres');
		// 查询所有库
		$databases = pg_find($con, 'SELECT datname FROM pg_database WHERE datistemplate = false');
		if (empty($databases)) {
			throw new ApiException('PG数据库为空');
		}
		// 遍历查询所有表信息
		$tableOwners = implode("','", array_column($databases, 'datname'));
		$tableOwners = "'" . $tableOwners . "'";
		$sql = "select schemaname as schema,tablename as table,tableowner as db from pg_tables where tableowner in ({$tableOwners})";
		$tables = pg_find($con, $sql);
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
		throw new ApiException('我的');
		//获取token
		$arr = apache_request_headers();//获取请求头数组
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
		$post_arr = json_decode($post_arr, true);
		$data = $post_arr;
		echo json_encode($data);
	}
}
