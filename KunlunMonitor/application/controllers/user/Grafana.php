<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Grafana extends CI_Controller
{

	public function __construct()
	{
		//session_start();
		parent::__construct();
		header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
		header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
		header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
		header('Access-Control-Allow-Headers:x-requested-with,content-type,Token'); //允许接受token
		header('Content-Type: application/json;charset=utf-8');
		//header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
		$this->config->load('myconfig');
		$this->key = $this->config->item('key');
		$this->post_url = $this->config->item('post_url');

	}

	public function mysqlDashboard()
	{
		//获取token
		$arr = apache_request_headers(); //获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		$this->load->model('Cluster_model');
		$this->load->model('Mysql_model');
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$ip = $string['hostaddr'];
		$port = $string['port'] + 1;
		//$http_data=str_replace("\\/", "/", json_encode($string));
		$if_addSource = true;
		//查prometheus的端口
		$sql = "select hostaddr,prometheus_port from cluster_mgr_nodes where member_state='source'";
		$res = $this->Cluster_model->getList($sql);
		$url = '';
		if (!empty($res)) {
			$url = 'http://' . $res[0]['hostaddr'] . ':' . $res[0]['prometheus_port'];
		}
		//查grafana数据源
		$get_url = 'http://admin:admin@127.0.0.1/api/datasources';
		//调grafana的api查数据源
		$this->load->model('Grafana_model');
		$get_result = $this->Grafana_model->postDataSource($get_url);
		if (!empty($get_result)) {
			$get_arr = json_decode($get_result, true);
			if (count($get_arr) !== 0) {
				foreach ($get_arr as $row) {
					$pro_id = $row['id'];
					if (!empty($row['url'])) {
						$grafana_url = $row['url'];
						if ($grafana_url == $url) {
							$if_addSource = false;
						} else {
							//url不一样需要调grafana接口更新数据源(PUT)
							$update_data = '{"id":' . $pro_id . ',"orgId":"' . $row['typeLogoUrl'] . '","name":"Prometheus","type":"prometheus","typeLogoUrl":"' . $row['typeLogoUrl'] . '","access":"proxy","url":"' . $url . '","basicAuth":false,"isDefault":false,"jsonData":null}';
							$put_url = "http://admin:admin@127.0.0.1:3000/api/datasources/" . $pro_id;
							$put_result = $this->Grafana_model->putData($update_data, $put_url);
							$put_result = json_decode($put_result, true);
							if ($put_result['message'] == 'Datasource updated') {
								$if_addSource = false;
							}
						}
					} else {
						//更新数据源
						$put_data = '{"id":' . $pro_id . ',"orgId":"' . $row['typeLogoUrl'] . '","name":"Prometheus","type":"prometheus","typeLogoUrl":"' . $row['typeLogoUrl'] . '","access":"proxy","url":"' . $url . '","basicAuth":false,"isDefault":false,"jsonData":null}';
						$put_url = "http://admin:admin@127.0.0.1:3000/api/datasources/" . $pro_id;
						$put_result = $this->Grafana_model->putData($put_data, $put_url);
						$put_result = json_decode($put_result, true);
						if ($put_result['message'] == 'Datasource updated') {
							$if_addSource = false;
						}
					}
				}
			} else {
				$post_url = 'http://admin:admin@127.0.0.1:3000/api/datasources';
				$post_data = '{"name":"Prometheus","type":"prometheus","access":"proxy","url":"' . $url . '","basicAuth":false}';
				$post_result = $this->Grafana_model->postData($post_data, $post_url);
				$post_result = json_decode($post_result, true);
				if ($post_result['message'] == 'Datasource added') {
					$if_addSource = false;
				}
			}
		}
		if ($if_addSource === false) {
			//新建bashboard模板
			$host = $ip . ':' . $port;
			$mysqld_data = $this->Mysql_model->mysqlJSON($host);
			//$mysqld_data='{"dashboard": {"id": null,"uid": null,"title": "Production Overview","tags": [ "templated" ],"timezone": "browser","schemaVersion": 16,"version": 0,"refresh": "25s"},"folderId": 0,"overwrite": false}';
			//$mysqld_data = file_get_contents('./json/mysqld.json');
			//print_r($mysqld_data);exit;
			$postdb_url = "http://admin:admin@127.0.0.1:3000/api/dashboards/db";
			$postdb_result = $this->Grafana_model->postData($mysqld_data, $postdb_url);
			$postdb_result = json_decode($postdb_result, true);
			//print_r($postdb_result);exit;
			if ($postdb_result['status'] == 'success') {
				$mysql_url = $postdb_result['url'];
				$data['url'] = $mysql_url;
				$data['code'] = 200;
				print_r(json_encode($data));
			} else if ($postdb_result['status'] == 'name-exists') {
				//查dashboard的id
				$getdb_url = 'http://admin:admin@127.0.0.1/api/search?folderIds=0';
				$getdb_result = $this->Grafana_model->postDataSource($getdb_url);
				$db_id = '';
				$db_uid = '';
				$db_title = '';
				$db_version = '';
				if (!empty($getdb_result)) {
					$getdb_arr = json_decode($getdb_result, true);
					if (count($getdb_arr) !== 0) {
						foreach ($getdb_arr as $dbrow) {
							if ($dbrow['title'] == 'mysql') {
								$db_id = $dbrow['id'];
								$db_uid = $dbrow['uid'];
								$db_title = $dbrow['title'];
							}
						}
						if (!empty($db_id) && !empty($db_uid) && !empty($db_title)) {
							//查dashboard的version
							$getdbv_url = 'http://admin:admin@127.0.0.1//api/dashboards/uid/' . $db_uid;
							$getdbv_result = $this->Grafana_model->postDataSource($getdbv_url);
							if (!empty($getdbv_result)) {
								$getdbv_arr = json_decode($getdbv_result, true);
								if (count($getdbv_arr) !== 0) {
									foreach ($getdbv_arr as $dbv_row) {
										$db_version = $dbv_row['version'];
									}
								}
							}
							//更新
							$update_mysqld_data = $this->Mysql_model->updateMysqlJSON($host, $db_id, $db_uid, $db_title, $db_version);
							$update_postdb_url = "http://admin:admin@127.0.0.1:3000/api/dashboards/db";
							$update_postdb_result = $this->Grafana_model->postData($update_mysqld_data, $update_postdb_url);
							$update_postdb_result = json_decode($update_postdb_result, true);
							//print_r($update_postdb_result);exit;
							if ($update_postdb_result['status'] == 'success') {
								$update_mysql_url = $update_postdb_result['url'];
								$data['url'] = $update_mysql_url;
								$data['code'] = 200;
								print_r(json_encode($data));
							}
						}
					}
				}
			}
		}
	}
	public function pgsqlDashboard()
	{ //前五个templating可用 其他不可用todo
		//获取token
		$arr = apache_request_headers(); //获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;

		}
		$this->load->model('Cluster_model');
		$this->load->model('PGsql_model');
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$ip = $string['hostaddr'];
		$port = $string['port'] + 2;
		//$http_data=str_replace("\\/", "/", json_encode($string));
		$if_addSource = true;
		//查prometheus的端口
		$sql = "select hostaddr,prometheus_port from cluster_mgr_nodes where member_state='source'";
		$res = $this->Cluster_model->getList($sql);
		$url = '';
		if (!empty($res)) {
			$url = 'http://' . $res[0]['hostaddr'] . ':' . $res[0]['prometheus_port'];
		}
		//查grafana数据源
		$get_url = 'http://admin:admin@127.0.0.1/api/datasources';
		//调grafana的api查数据源
		$this->load->model('Grafana_model');
		$get_result = $this->Grafana_model->postDataSource($get_url);
		if (!empty($get_result)) {
			$get_arr = json_decode($get_result, true);
			if (count($get_arr) !== 0) {
				foreach ($get_arr as $row) {
					$pro_id = $row['id'];
					if (!empty($row['url'])) {
						$grafana_url = $row['url'];
						if ($grafana_url == $url) {
							$if_addSource = false;
						} else {
							//url不一样需要调grafana接口更新数据源(PUT)
							$update_data = '{"id":' . $pro_id . ',"orgId":"' . $row['typeLogoUrl'] . '","name":"Prometheus","type":"prometheus","typeLogoUrl":"' . $row['typeLogoUrl'] . '","access":"proxy","url":"' . $url . '","basicAuth":false,"isDefault":false,"jsonData":null}';
							$put_url = "http://admin:admin@127.0.0.1:3000/api/datasources/" . $pro_id;
							$put_result = $this->Grafana_model->putData($update_data, $put_url);
							$put_result = json_decode($put_result, true);
							if ($put_result['message'] == 'Datasource updated') {
								$if_addSource = false;
							}
						}
					} else {
						//更新数据源
						$put_data = '{"id":' . $pro_id . ',"orgId":"' . $row['typeLogoUrl'] . '","name":"Prometheus","type":"prometheus","typeLogoUrl":"' . $row['typeLogoUrl'] . '","access":"proxy","url":"' . $url . '","basicAuth":false,"isDefault":false,"jsonData":null}';
						$put_url = "http://admin:admin@127.0.0.1:3000/api/datasources/" . $pro_id;
						$put_result = $this->Grafana_model->putData($put_data, $put_url);
						$put_result = json_decode($put_result, true);
						if ($put_result['message'] == 'Datasource updated') {
							$if_addSource = false;
						}
					}
				}
			} else {
				$post_url = 'http://admin:admin@127.0.0.1:3000/api/datasources';
				$post_data = '{"name":"Prometheus","type":"prometheus","access":"proxy","url":"' . $url . '","basicAuth":false}';
				$post_result = $this->Grafana_model->postData($post_data, $post_url);
				$post_result = json_decode($post_result, true);
				if ($post_result['message'] == 'Datasource added') {
					//$uid=$post_result['datasource']['uid'];
					$if_addSource = false;
				}
			}
		}
		if ($if_addSource === false) {
			//新建bashboard模板
			$host = $ip . ':' . $port;
			$pgsqld_data = $this->PGsql_model->pgsqlJSON($host);
			//$pgsqld_data='{"dashboard": {"id": null,"uid": null,"title": "postgresql","tags": [ "templated" ],"timezone": "browser","schemaVersion": 16,"version": 0,"refresh": "5s"},"folderId": 0,"overwrite": false}';
			print_r($pgsqld_data);
			$postdb_url = "http://admin:admin@127.0.0.1:3000/api/dashboards/db";
			$postdb_result = $this->Grafana_model->postData($pgsqld_data, $postdb_url);
			$postdb_result = json_decode($postdb_result, true);
			//print_r($postdb_result);exit;
			if ($postdb_result['status'] == 'success') {
				$pgsql_url = $postdb_result['url'];
				$data['url'] = $pgsql_url;
				$data['code'] = 200;
				print_r(json_encode($data));
			} else if ($postdb_result['status'] == 'name-exists') {
				//查dashboard的id
				$getdb_url = 'http://admin:admin@127.0.0.1/api/search?folderIds=0';
				$getdb_result = $this->Grafana_model->postDataSource($getdb_url);
				$db_id = '';
				$db_uid = '';
				$db_title = '';
				$db_version = '';
				//print_r($getdb_result);exit;
				if (!empty($getdb_result)) {
					$getdb_arr = json_decode($getdb_result, true);
					if (count($getdb_arr) !== 0) {
						foreach ($getdb_arr as $dbrow) {
							if ($dbrow['title'] == 'pgsql') {
								$db_id = $dbrow['id'];
								$db_uid = $dbrow['uid'];
								$db_title = $dbrow['title'];
							}
						}
						if (!empty($db_id) && !empty($db_uid) && !empty($db_title)) {
							//查dashboard的version
							$getdbv_url = 'http://admin:admin@127.0.0.1//api/dashboards/uid/' . $db_uid;
							$getdbv_result = $this->Grafana_model->postDataSource($getdbv_url);
							if (!empty($getdbv_result)) {
								$getdbv_arr = json_decode($getdbv_result, true);
								if (count($getdbv_arr) !== 0) {
									foreach ($getdbv_arr as $dbv_row) {
										$db_version = $dbv_row['version'];
									}
								}
							}
							//更新
							$update_mysqld_data = $this->PGsql_model->updatePGsqlJSON($host, $db_id, $db_uid, $db_title, $db_version);
							//$update_mysqld_data = file_get_contents('./json/pgsql.json');
							$update_postdb_url = "http://admin:admin@127.0.0.1:3000/api/dashboards/db";
							$update_postdb_result = $this->Grafana_model->postData($update_mysqld_data, $update_postdb_url);
							//print_r($update_mysqld_data);exit;
							$update_postdb_result = json_decode($update_postdb_result, true);
							//print_r($update_postdb_result);exit;
							if ($update_postdb_result['status'] == 'success') {
								$update_mysql_url = $update_postdb_result['url'];
								$data['url'] = $update_mysql_url;
								$data['code'] = 200;
								print_r(json_encode($data));
							}
						}
					}
				}
			}
		}
	}
	public function nodeDashboard()
	{
		//获取token
		$arr = apache_request_headers(); //获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		$this->load->model('Cluster_model');
		$this->load->model('Node_model');
		//查主机的iplist
		$ipsql = "select hostaddr,nodemgr_prometheus_port from server_nodes where hostaddr!='pseudo_server_useless' and machine_type is not null group  by hostaddr";
		$res_iplist = $this->Cluster_model->getList($ipsql);
		$node = '';
		foreach ($res_iplist as $ip) {
			$host = $ip['hostaddr'];
			$nodeport = $ip['nodemgr_prometheus_port'];
			$ip_list = $host . ':' . $nodeport;
			$node .= $ip_list . ',';
		}
		if (!empty($node)) {
			$node = substr($node, 0, -1);
			$node = '{' . $node . '}';
		}
		$if_addSource = true;
		//查prometheus的端口
		$sql = "select hostaddr,prometheus_port from cluster_mgr_nodes where member_state='source'";
		$res = $this->Cluster_model->getList($sql);
		$url = '';
		if (!empty($res)) {
			$url = 'http://' . $res[0]['hostaddr'] . ':' . $res[0]['prometheus_port'];
		}
		//查grafana数据源
		$get_url = 'http://admin:admin@127.0.0.1/api/datasources';
		//调grafana的api查数据源
		$this->load->model('Grafana_model');
		$get_result = $this->Grafana_model->postDataSource($get_url);
		if (!empty($get_result)) {
			$get_arr = json_decode($get_result, true);
			if (count($get_arr) !== 0) {
				foreach ($get_arr as $row) {
					$pro_id = $row['id'];
					if (!empty($row['url'])) {
						$grafana_url = $row['url'];
						if ($grafana_url == $url) {
							$if_addSource = false;
						} else {

							//url不一样需要调grafana接口更新数据源(PUT)
							$update_data = '{"id":' . $pro_id . ',"orgId":"' . $row['typeLogoUrl'] . '","name":"Prometheus","type":"prometheus","typeLogoUrl":"' . $row['typeLogoUrl'] . '","access":"proxy","url":"' . $url . '","basicAuth":false,"isDefault":false,"jsonData":null}';
							$put_url = "http://admin:admin@127.0.0.1:3000/api/datasources/" . $pro_id;
							$put_result = $this->Grafana_model->putData($update_data, $put_url);
							print_r($put_result);
							$put_result = json_decode($put_result, true);
							if ($put_result['message'] == 'Datasource updated') {
								$if_addSource = false;
							}
						}
					} else {
						//更新数据源
						$put_data = '{"id":' . $pro_id . ',"orgId":"' . $row['typeLogoUrl'] . '","name":"Prometheus","type":"prometheus","typeLogoUrl":"' . $row['typeLogoUrl'] . '","access":"proxy","url":"' . $url . '","basicAuth":false,"isDefault":false,"jsonData":null}';
						$put_url = "http://admin:admin@127.0.0.1:3000/api/datasources/" . $pro_id;
						$put_result = $this->Grafana_model->putData($put_data, $put_url);
						$put_result = json_decode($put_result, true);
						if ($put_result['message'] == 'Datasource updated') {
							$if_addSource = false;
						}
					}

				}
			} else {

				$post_url = 'http://admin:admin@127.0.0.1:5555/api/datasources';
				exit($post_url);
				$post_data = '{"name":"Prometheus","type":"prometheus","access":"proxy","url":"' . $url . '","basicAuth":false}';
				$post_result = $this->Grafana_model->postData($post_data, $post_url);
				$post_result = json_decode($post_result, true);
				if ($post_result['message'] == 'Datasource added') {
					$if_addSource = false;
				}
			}
		}

		if (!$if_addSource) {
			//新建bashboard模板
			//$host=$ip.':'.$port;
			//$pgsqld_data=$this->Node_model->nodeJSON($node);
			//$pgsqld_data='{"dashboard": {"id": null,"uid": null,"title": "node","tags": [ "templated" ],"timezone": "browser","schemaVersion": 16,"version": 0,"refresh": "5s"},"folderId": 0,"overwrite": false}';
			//print_r($pgsqld_data);
			$pgsqld_data = file_get_contents('./json/node.json');
			$postdb_url = "http://admin:12345678@127.0.0.1/api/dashboards/db";
			$postdb_result = $this->Grafana_model->postData($pgsqld_data, $postdb_url);
			$postdb_result = json_decode($postdb_result, true);

			if ($postdb_result['status'] == 'success') {
				$pgsql_url = $postdb_result['url'];
				$data['url'] = $pgsql_url;
				$data['code'] = 200;
				print_r(json_encode($data));
			} else if ($postdb_result['status'] == 'name-exists') {
				//查dashboard的id
				$getdb_url = 'http://admin:admin@127.0.0.1/api/search?folderIds=0';
				$getdb_result = $this->Grafana_model->postDataSource($getdb_url);
				$db_id = '';
				$db_uid = '';
				$db_title = '';
				$db_version = '';
				$db_url = '';
				//print_r($getdb_result);exit;
				if (!empty($getdb_result)) {
					$getdb_arr = json_decode($getdb_result, true);
					if (count($getdb_arr) !== 0) {
						foreach ($getdb_arr as $dbrow) {
							if ($dbrow['title'] == 'node') {
								$db_id = $dbrow['id'];
								$db_uid = $dbrow['uid'];
								$db_title = $dbrow['title'];
								$db_url = $dbrow['url'];
							}
						}
						if (!empty($db_url)) {
							$data['url'] = $db_url;
							$data['code'] = 200;
							print_r(json_encode($data));
						}

					}
				}
			}
		}
	}
}