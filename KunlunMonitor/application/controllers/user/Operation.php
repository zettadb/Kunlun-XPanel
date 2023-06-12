<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Operation extends CI_Controller
{

	public function __construct()
	{
		//session_start();
		parent::__construct();
		header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
		header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
		header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
		header('Access-Control-Allow-Headers:x-requested-with,content-type,Token');//允许接受token
		header('Content-Type: application/json;charset=utf-8');
		//header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
		$this->config->load('myconfig');
		$this->key = $this->config->item('key');
		$this->job_type = $this->config->item('job_type');
		$this->post_url = $this->config->item('post_url');
	}

	public function getOperationList()
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
		$start = ($pageNo - 1) * $pageSize;
		$job_type = $this->job_type;
		$this->load->model('Cluster_model');
		//获取任务信息
		$sql = "select id,job_type,when_started,when_ended,memo,status,job_info,user_name from cluster_general_job_log where user_name!='internal_user'";
		if ($username == 'super_dba') {
			$sql .= "";
		} else {
			$sql .= " and user_name='$username'";
		}
		$sql .= "  order by id desc limit $pageSize offset $start";
		$res = $this->Cluster_model->getList($sql);
		$total_sql = "select count(id) as count from cluster_general_job_log where user_name!='internal_user'";
		if ($username == 'super_dba') {
			$total_sql .= "";
		} else {
			$total_sql .= " and user_name='$username'";
		}
		$res_total = $this->Cluster_model->getList($total_sql);
		if ($res === false) {
			$res = array();
		} else {
			foreach ($res as $row => $value) {
				foreach ($value as $key2 => $value2) {
					$string = json_decode($res[$row]['job_info'], true);
					//输入信息
					if ($key2 == 'memo') {
						if (!empty($value2)) {
							$info = json_decode($value2, true);
							if (!empty($info)) {
								if (!empty($info['cluster_id'])) {
									$res[$row]['cluster_id'] = $info['cluster_id'];
								} else {
									if (!empty($string['paras']['cluster_id'])) {
										$res[$row]['cluster_id'] = $string['paras']['cluster_id'];
									} else {
										if ($res[$row]['job_type'] == '回档集群' || $res[$row]['job_type'] == 'cluster_restore') {
											if (!empty($string['paras']['dst_cluster_id'])) {
												$res[$row]['cluster_id'] = $string['paras']['dst_cluster_id'];
											} else {
												$res[$row]['cluster_id'] = '';
											}
										} else {
											$res[$row]['cluster_id'] = '';
										}
									}
								}
								if (!empty($info['error_info'])) {
									if ($res[$row]['status'] == 'ongoing') {
										$res[$row]['info'] = '';
									} else {
										$res[$row]['info'] = $info['error_info'];
										//print_r($info['error_info']);exit;
									}
								} else {
									$res[$row]['info'] = '';
								}
							}
						} else {
							$res[$row]['info'] = '';
							if (!empty($string['paras']['cluster_id'])) {
								$res[$row]['cluster_id'] = $string['paras']['cluster_id'];
							} else {
								$res[$row]['cluster_id'] = '';
							}
						}
					}
					if ($key2 == 'job_type') {
						if (!empty($value2)) {
							foreach ($job_type as $k2 => $v2) {
								if ($value2 == $v2['code']) {
									$res[$row]['job_type'] = $v2['name'];
									break;
								} else {
									$res[$row]['job_type'] = $value2;
								}
							}
							if ($value2 == 'create_cluster') {
								$child = '';
								$arr = '';
								if (!empty($string['paras']['computer_iplists'])) {
									foreach ($string['paras']['computer_iplists'] as $host) {
										$child .= '<div>' . $host . '</div>';
									}
									$arr .= '<div>所选计算类型的计算机：<div style="margin-left:30px;">' . $child . '</div></div>';
								}
								$shard_child = '';
								$shard_arrs = '';
								if (!empty($string['paras']['storage_iplists'])) {
									foreach ($string['paras']['storage_iplists'] as $host1) {
										$shard_child .= '<div>' . $host1 . '</div>';
									}
									$shard_arrs .= '<div>所选存储类型的计算机：<div style="margin-left:30px;">' . $shard_child . '</div></div>';
								}
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_name'])) {
										$nick_name = $string['paras']['cluster_name'];
									}
									$shards = '';
									if (!empty($string['paras']['shards'])) {
										$shards = $string['paras']['shards'];
									}
									$nodes = '';
									if (!empty($string['paras']['nodes'])) {
										$nodes = $string['paras']['nodes'];
									}
									$comps = '';
									if (!empty($string['paras']['comps'])) {
										$comps = $string['paras']['comps'];
									}
									$max_connections = '';
									if (!empty($string['paras']['max_connections'])) {
										$max_connections = $string['paras']['max_connections'];
									}
									$innodb_size = '';
									if (!empty($string['paras']['innodb_size'])) {
										$innodb_size = $string['paras']['innodb_size'];
									}
									$cpu_cores = '';
									if (!empty($string['paras']['cpu_cores'])) {
										$cpu_cores = $string['paras']['cpu_cores'];
									}
									$ha_mode = '';
									if (!empty($string['paras']['ha_mode'])) {
										$ha_mode = $string['paras']['ha_mode'];
									}
									//get_status获取ip
									$computer_hosts = '';
									$shard_hosts = '';
									$child_comp = '';
									$arr_comp = '';
									$child_shard = '';
									$arr_shard = '';
									$shard_arr = '';
									//调接口
									$this->load->model('Cluster_model');
									$data_string = array(
										'version' => '1.0',
										'job_id' => $res[$row]['id'],
										'job_type' => 'get_status',
										'timestamp' => '1435749309',
										'paras' => '{}'
									);
									$post_data = json_encode($data_string);
									$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
									$post_arr = json_decode($post_arr, TRUE);
									if (!empty($post_arr['attachment']['computer_step'][0]['computer_hosts'])) {
										$computer_hosts = $post_arr['attachment']['computer_step'][0]['computer_hosts'];
										rtrim($computer_hosts, ';');
										$comparr = explode(';', $computer_hosts);
										for ($i = 0; $i < count($comparr); $i++) {
											$child_comp .= '<div>' . $comparr[$i] . '</div>';
										}
										$arr_comp .= '<div>计算节点ip：<div style="margin-left:30px;">' . $child_comp . '</div></div>';
									}
									if (!empty($post_arr['attachment']['shard_step'][0]['shard_hosts'])) {
										$shard_hosts = $post_arr['attachment']['shard_step'][0]['shard_hosts'][0];
										//存在多个shard时，todo
										foreach ($shard_hosts as $key => $value) {
											$shard_arr .= $value;
										}
										rtrim($shard_arr, ';');
										$shardarr = explode(';', $shard_arr);
										for ($i = 0; $i < count($shardarr); $i++) {
											$child_shard .= '<div>' . $shardarr[$i] . '</div>';
										}
										$arr_shard .= '<div>存储节点ip：<div style="margin-left:30px;">' . $child_shard . '</div></div>';
									}
									$res[$row]['list'] = '<div>业务名称：' . $nick_name . '</div><div>shard总个数：' . $shards . '</div><div>每个shard含节点总个数：' . $nodes . '</div><div>计算节点总个数：' . $comps . '</div><div>每计算节点最大连接数：' . $max_connections . '</div><div>缓冲池大小：' . $innodb_size . '</div><div>cpu核数：' . $cpu_cores . '</div><div>高可用模式：' . $ha_mode . '</div>' . $arr . $shard_arrs . $arr_comp . $arr_shard;
									//$res[$row]['list']=array('shard总个数'=>$string['shards'],'每个shard含节点总个数'=>$string['nodes'],'计算节点总个数'=>$string['comps'],'每计算节点最大连接数'=>$string['max_connections'],'缓冲池大小'=>$string['innodb_size'],'cpu核数'=>$string['cpu_cores'],'ha_mode'=>$string['ha_mode'],'选择计算机'=>$ip);
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['list'] = '<div>业务名称：</div><div>shard总个数：</div><div>每个shard含节点总个数：</div><div>计算节点总个数：</div><div>每计算节点最大连接数：</div><div>缓冲池大小：</div><div>cpu核数：</div><div>复制模式：</div><div>选择计算机：</div>';
								}
							}
							if ($value2 == 'delete_cluster') {
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_name'])) {
										$nick_name = $string['paras']['cluster_name'];
									}
									$cluster_id = '';
									if (!empty($string['paras']['cluster_id'])) {
										$cluster_id = $string['paras']['cluster_id'];
									}
									//get_status获取ip
									$computer_hosts = '';
									$shard_hosts = '';
									$child_comp = '';
									$arr_comp = '';
									$child_shard = '';
									$arr_shard = '';
									$shard_arr = '';
									//调接口
									$this->load->model('Cluster_model');
									$data_string = array(
										'version' => '1.0',
										'job_id' => $res[$row]['id'],
										'job_type' => 'get_status',
										'timestamp' => '1435749309',
										'paras' => '{}'
									);
									$post_data = json_encode($data_string);
									$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
									$post_arr = json_decode($post_arr, TRUE);
									if (!empty($post_arr['attachment']['computer_step'][0]['computer_hosts'])) {
										$computer_hosts = $post_arr['attachment']['computer_step'][0]['computer_hosts'];
										rtrim($computer_hosts, ';');
										$comparr = explode(';', $computer_hosts);
										for ($i = 0; $i < count($comparr); $i++) {
											$child_comp .= '<div>' . $comparr[$i] . '</div>';
										}
										$arr_comp .= '<div>计算节点ip：<div style="margin-left:30px;">' . $child_comp . '</div></div>';
									}
									if (!empty($post_arr['attachment']['shard_step'][0]['storage_hosts'])) {
										$shard_hosts = $post_arr['attachment']['shard_step'][0]['storage_hosts'];
										rtrim($shard_hosts, ';');
										$shardarr = explode(';', $shard_hosts);
										for ($i = 0; $i < count($shardarr); $i++) {
											$child_shard .= '<div>' . $shardarr[$i] . '</div>';
										}
										$arr_shard .= '<div>存储节点ip：<div style="margin-left:30px;">' . $child_shard . '</div></div>';
									}
									$res[$row]['list'] = '<div>集群ID:' . $cluster_id . '</div><div>业务名称：' . $nick_name . '</div>' . $arr_comp . $arr_shard;
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['list'] = '<div>业务名称：</div>';
								}
							}
							if ($value2 == 'add_shards') {
								$shard_child = '';
								$shard_arrs = '';
								if (!empty($string['paras']['storage_iplists'])) {
									foreach ($string['paras']['storage_iplists'] as $host1) {
										$shard_child .= '<div>' . $host1 . '</div>';
									}
									$shard_arrs .= '<div>所选计算机：<div style="margin-left:30px;">' . $shard_child . '</div></div>';
								}
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_id'])) {
										$name = $this->getClusterName($string['paras']['cluster_id']);
										if (!empty($name)) {
											$nick_name = $name[0]['nick_name'];
										} else {
											$nick_name = '';
										}
									}
									$shards = '';
									if (!empty($string['paras']['shards'])) {
										$shards = $string['paras']['shards'];
									}
									//get_status获取ip
									$computer_hosts = '';
									$child_comp = '';
									$arr_comp = '';
									$shard_arr = '';
									//调接口
									$this->load->model('Cluster_model');
									$data_string = array(
										'version' => '1.0',
										'job_id' => $res[$row]['id'],
										'job_type' => 'get_status',
										'timestamp' => '1435749309',
										'paras' => '{}'
									);
									$post_data = json_encode($data_string);
									$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
									$post_arr = json_decode($post_arr, TRUE);
									//print_r($post_arr);exit;
									if (!empty($post_arr['attachment']['shard_hosts'])) {
										$computer_hosts = $post_arr['attachment']['shard_hosts'][0];
										//存在多个shard时，todo
										foreach ($computer_hosts as $key => $value) {
											$shard_arr .= $value;
										}
										rtrim($shard_arr, ';');
										$comparr = explode(';', $shard_arr);
										for ($i = 0; $i < count($comparr); $i++) {
											$child_comp .= '<div>' . $comparr[$i] . '</div>';
										}
										$arr_comp .= '<div>存储节点ip：<div style="margin-left:30px;">' . $child_comp . '</div></div>';
									}
									$res[$row]['list'] = '<div>业务名称：' . $nick_name . '</div><div>shard个数：' . $shards . '</div>' . $arr_comp . $shard_arrs;
									//$res[$row]['list']=array('业务名称'=>$string['cluster_name'],'选择计算机'=>$ip,'shard个数'=>$string['shards']);
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['list'] = '<div>业务名称：</div><div>shard个数：</div>';
								}
							}
							if ($value2 == 'delete_shard') {
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_id'])) {
										$name = $this->getClusterName($string['paras']['cluster_id']);
										if (!empty($name)) {
											$nick_name = $name[0]['nick_name'];
										} else {
											$nick_name = '';
										}
									}
									$shard_name = '';
									if (!empty($string['paras']['shard_name'])) {
										$shard_name = $string['paras']['shard_name'];
									} else {
										if (!empty($string['paras']['shard_id']) && !empty($string['paras']['cluster_id'])) {
											$shard_name = $this->getShardName($string['paras']['cluster_id'], $string['paras']['shard_id']);
										}
									}
									$res[$row]['list'] = '<div>业务名称：' . $nick_name . '</div><div>shard名称：' . $shard_name . '</div>';
									//$res[$row]['list']=array('业务名称'=>$string['cluster_name'],'shard名称'=>$string['shard_name']);
									$res[$row]['object'] = $nick_name . '(' . $shard_name . ')';
								} else {
									$res[$row]['list'] = '<div>业务名称：</div><div>shard名称：</div>';
								}
							}
							if ($value2 == 'backup_cluster') {
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['backup_cluster_name'])) {
										$nick_name = $string['paras']['backup_cluster_name'];
									}
									$res[$row]['list'] = '<div>原业务名称：' . $nick_name . '</div>';
									//$res[$row]['list']=array('原业务名称'=>$string['backup_cluster_name']);
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['list'] = '<div>原业务名称：</div>';
								}
							}
							if ($value2 == 'restore_new_cluster') {
								if (!empty($string)) {
									$backup_cluster_name = '';
									if (!empty($string['paras']['backup_cluster_name'])) {
										$backup_cluster_name = $string['paras']['backup_cluster_name'];
									}
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_name'])) {
										$nick_name = $string['paras']['cluster_name'];
									}
									$res[$row]['list'] = '<div>备份业务名称：' . $backup_cluster_name . '</div><div>新业务名称：' . $nick_name . '</div>';
									//$res[$row]['list']=array('备份业务名称'=>$string['paras']['backup_cluster_name']);
									$res[$row]['object'] = $backup_cluster_name;
								} else {
									$res[$row]['list'] = '<div>备份业务名称：</div><div>新业务名称：</div>';
								}
							}
							if ($value2 == 'add_comps') {
								$child = '';
								$arr = '';
								if (!empty($string['paras']['computer_iplists'])) {
									foreach ($string['paras']['computer_iplists'] as $host) {
										$child .= '<div>' . $host . '</div>';
									}
									$arr .= '<div>选择计算机：<div style="margin-left:30px;">' . $child . '</div></div>';
								}
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_id'])) {
										$name = $this->getClusterName($string['paras']['cluster_id']);
										if (!empty($name)) {
											$nick_name = $name[0]['nick_name'];
										} else {
											$nick_name = '';
										}
									}
									$cluster_id = '';
									if (!empty($string['paras']['cluster_id'])) {
										$cluster_id = $string['paras']['cluster_id'];
									}
									$comps = '';
									if (!empty($string['paras']['comps'])) {
										$comps = $string['paras']['comps'];
									}
									//get_status获取ip
									$computer_hosts = '';
									$child_comp = '';
									$arr_comp = '';
									//调接口
									$this->load->model('Cluster_model');
									$data_string = array(
										'version' => '1.0',
										'job_id' => $res[$row]['id'],
										'job_type' => 'get_status',
										'timestamp' => '1435749309',
										'paras' => '{}'
									);
									$post_data = json_encode($data_string);
									$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
									$post_arr = json_decode($post_arr, TRUE);
									if (!empty($post_arr['attachment']['computer_hosts'])) {
										$computer_hosts = $post_arr['attachment']['computer_hosts'];
										rtrim($computer_hosts, ';');
										$comparr = explode(';', $computer_hosts);
										for ($i = 0; $i < count($comparr); $i++) {
											$child_comp .= '<div>' . $comparr[$i] . '</div>';
										}
										$arr_comp .= '<div>计算节点ip：<div style="margin-left:30px;">' . $child_comp . '</div></div>';
									}

									$res[$row]['list'] = '<div>集群ID：' . $cluster_id . '</div><div>业务名称：' . $nick_name . '</div><div>计算节点个数：' . $comps . '</div>' . $arr . $arr_comp;
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['list'] = '<div>业务名称：</div><div>选择计算机：</div><div>计算节点个数：</div>';
								}
							}
							if ($value2 == 'delete_comp') {
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_id'])) {
										$name = $this->getClusterName($string['paras']['cluster_id']);
										if (!empty($name)) {
											$nick_name = $name[0]['nick_name'];
										} else {
											$nick_name = '';
										}
									}
									$cluster_id = '';
									if (!empty($string['paras']['cluster_id'])) {
										$cluster_id = $string['paras']['cluster_id'];
									}
									$comp_id = '';
									if (!empty($string['paras']['comp_id'])) {
										$comp_id = $string['paras']['comp_id'];
									}
									$comp_name = '';
									$ip = '';
									$port = '';
									if (!empty($string['paras']['comp_id'])) {
										$comp = $this->getCompName($string['paras']['cluster_id'], $string['paras']['comp_id']);
										if (!empty($comp)) {
											$comp_name = $comp[0]['name'];
											$ip = $comp[0]['hostaddr'];
											$port = $comp[0]['port'];
										} else {
											$comp_name = '';
											$ip = '';
											$port = '';
										}
									}
									$res[$row]['list'] = '<div>集群ID：' . $cluster_id . '</div><div>业务名称：' . $nick_name . '</div><div>计算节点ID：' . $comp_id . '</div><div>计算节点名称：' . $comp_name . '</div><div>ip：' . $ip . '</div><div>端口：' . $port . '</div>';
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['list'] = '<div>集群ID：</div><div>业务名称：</div><div>计算节点ID：</div><div>计算节点名称：</div><div>ip：</div><div>端口：</div>';
								}
							}
							if ($value2 == 'add_nodes') {
								$child = '';
								$arr = '';
								if (!empty($string['paras']['storage_iplists'])) {
									foreach ($string['paras']['storage_iplists'] as $host) {
										$child .= '<div>' . $host . '</div>';
									}
									$arr .= '<div>选择计算机：<div style="margin-left:30px;">' . $child . '</div></div>';
								}
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_id'])) {
										$name = $this->getClusterName($string['paras']['cluster_id']);
										if (!empty($name)) {
											$nick_name = $name[0]['nick_name'];
										} else {
											$nick_name = '';
										}
									}
									$shard_name = '';
									if (!empty($string['paras']['shard_id'])) {
										$shard_name = $this->getShardName($string['paras']['cluster_id'], $string['paras']['shard_id']);
									}
									$shard_id = '';
									if (!empty($string['paras']['shard_id'])) {
										$shard_id = $string['paras']['shard_id'];
									}
									$cluster_id = '';
									if (!empty($string['paras']['cluster_id'])) {
										$cluster_id = $string['paras']['cluster_id'];
									}
									$nodes = '';
									if (!empty($string['paras']['nodes'])) {
										$nodes = $string['paras']['nodes'];
									}
									//get_status获取ip
									$computer_hosts = '';
									$child_comp = '';
									$arr_comp = '';
									$shard_arr = '';
									//调接口
									$this->load->model('Cluster_model');
									$data_string = array(
										'version' => '1.0',
										'job_id' => $res[$row]['id'],
										'job_type' => 'get_status',
										'timestamp' => '1435749309',
										'paras' => '{}'
									);
									$post_data = json_encode($data_string);
									$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
									$post_arr = json_decode($post_arr, TRUE);
									//print_r($post_arr);exit;
									if (!empty($post_arr['attachment']['shard_hosts'])) {
										$computer_hosts = $post_arr['attachment']['shard_hosts'][0];
										//print_r($computer_hosts);exit;
										foreach ($computer_hosts as $key => $value) {
											$shard_arr .= $value;
										}
										rtrim($shard_arr, ';');
										$comparr = explode(';', $shard_arr);
										for ($i = 0; $i < count($comparr); $i++) {
											$child_comp .= '<div>' . $comparr[$i] . '</div>';
										}
										$arr_comp .= '<div>存储节点ip：<div style="margin-left:30px;">' . $child_comp . '</div></div>';
									}
									$res[$row]['list'] = '<div>集群ID：' . $cluster_id . '</div><div>业务名称：' . $nick_name . '</div><div>shard_id：' . $shard_id . '</div><div>shard名称：' . $shard_name . '</div><div>个数：' . $nodes . '个</div>' . $arr . $arr_comp;
									//$res[$row]['list']=array('业务名称'=>$string['cluster_name'],'shard名称'=>$string['shard_name'],'ip'=>$string['ip'],'端口'=>$string['port']);
									$res[$row]['object'] = $nick_name . '(' . $shard_name . ')';
								} else {
									$res[$row]['list'] = '<div>业务名称：</div><div>选择计算机：</div><div>shard名称：</div><div>节点个数：</div>';
								}
							}
							if ($value2 == 'delete_node') {
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_id'])) {
										$name = $this->getClusterName($string['paras']['cluster_id']);
										if (!empty($name)) {
											$nick_name = $name[0]['nick_name'];
										} else {
											$nick_name = '';
										}
									}
									$shard_name = '';
									if (!empty($string['paras']['shard_id'])) {
										$shard_name = $this->getShardName($string['paras']['cluster_id'], $string['paras']['shard_id']);
									}
									$shard_id = '';
									if (!empty($string['paras']['shard_id'])) {
										$shard_id = $string['paras']['shard_id'];
									}
									$cluster_id = '';
									if (!empty($string['paras']['cluster_id'])) {
										$cluster_id = $string['paras']['cluster_id'];
									}
									$hostaddr = '';
									if (!empty($string['paras']['hostaddr'])) {
										$hostaddr = $string['paras']['hostaddr'];
									}
									$port = '';
									if (!empty($string['paras']['port'])) {
										$port = $string['paras']['port'];
									}
									$res[$row]['list'] = '<div>集群ID：' . $cluster_id . '</div><div>业务名称：' . $nick_name . '</div><div>shard_id：' . $shard_id . '</div><div>shard名称：' . $shard_name . '</div><div>ip：' . $hostaddr . '</div><div>端口：' . $port . '</div>';
									//$res[$row]['list']=array('业务名称'=>$string['cluster_name'],'shard名称'=>$string['shard_name'],'ip'=>$string['ip'],'端口'=>$string['port']);
									$res[$row]['object'] = $nick_name . '(' . $shard_name . ')';
								} else {
									$res[$row]['list'] = '<div>业务名称：</div><div>shard名称：</div><div>ip：</div><div>端口：</div>';
								}
							}
							if ($value2 == 'mysqld_exporter') {
								if (!empty($string)) {
									$hostaddr = '';
									if (!empty($string['paras']['hostaddr'])) {
										$hostaddr = $string['paras']['hostaddr'];
									}
									$port = '';
									if (!empty($string['paras']['port'])) {
										$port = $string['paras']['port'];
									}
									$res[$row]['list'] = '<div>ip：' . $hostaddr . '</div><div>端口：' . $port . '</div>';
									//$res[$row]['list']=array('ip'=>$string['ip'],'端口'=>$string['port']);
									$res[$row]['object'] = $hostaddr . ':' . $port;
								} else {
									$res[$row]['list'] = '<div>ip：</div><div>端口：</div>';
								}
							}
							if ($value2 == 'postgres_exporter') {
								if (!empty($string)) {
									$hostaddr = '';
									if (!empty($string['paras']['hostaddr'])) {
										$hostaddr = $string['paras']['hostaddr'];
									}
									$port = '';
									if (!empty($string['paras']['port'])) {
										$port = $string['paras']['port'];
									}
									$res[$row]['list'] = '<div>ip：' . $hostaddr . '</div><div>端口：' . $port . '</div>';
									$res[$row]['object'] = $hostaddr . ':' . $port;
								} else {
									$res[$row]['list'] = '<div>ip：</div><div>端口：</div>';
								}
							}
							if ($value2 == 'create_machine') {
								if (!empty($string)) {
									$hostaddr = '';
									if (!empty($string['paras']['hostaddr'])) {
										$hostaddr = $string['paras']['hostaddr'];
									}
									$rack_id = '';
									if (!empty($string['paras']['rack_id'])) {
										$rack_id = $string['paras']['rack_id'];
									}
									$datadir = '';
									if (!empty($string['paras']['datadir'])) {
										$datadir = $string['paras']['datadir'];
									}
									$logdir = '';
									if (!empty($string['paras']['logdir'])) {
										$logdir = $string['paras']['logdir'];
									}
									$wal_log_dir = '';
									if (!empty($string['paras']['wal_log_dir'])) {
										$wal_log_dir = $string['paras']['wal_log_dir'];
									}
									$comp_datadir = '';
									if (!empty($string['paras']['comp_datadir'])) {
										$comp_datadir = $string['paras']['comp_datadir'];
									}

									$res[$row]['list'] = '<div>ip：' . $hostaddr . '</div><div>机架编号：' . $rack_id . '</div><div>存储节点数据目录：' . $datadir . '</div><div>日志目录：' . $logdir . '</div><div>wal日志目录：' . $wal_log_dir . '</div><div>计算节点数据目录：' . $comp_datadir . '</div>';
									$res[$row]['object'] = $hostaddr;
								} else {
									$res[$row]['list'] = '<div>ip：</div><div>机架编号：</div><div>存储节点数据目录：</div><div>日志目录：</div><div>wal日志目录：</div><div>计算节点数据目录：</div>';
								}
							}
							if ($value2 == 'update_machine') {
								if (!empty($string)) {
									$hostaddr = '';
									if (!empty($string['paras']['hostaddr'])) {
										$hostaddr = $string['paras']['hostaddr'];
									}
									$rack_id = '';
									if (!empty($string['paras']['rack_id'])) {
										$rack_id = $string['paras']['rack_id'];
									}
									$datadir = '';
									if (!empty($string['paras']['datadir'])) {
										$datadir = $string['paras']['datadir'];
									}
									$logdir = '';
									if (!empty($string['paras']['logdir'])) {
										$logdir = $string['paras']['logdir'];
									}
									$wal_log_dir = '';
									if (!empty($string['paras']['wal_log_dir'])) {
										$wal_log_dir = $string['paras']['wal_log_dir'];
									}
									$comp_datadir = '';
									if (!empty($string['paras']['comp_datadir'])) {
										$comp_datadir = $string['paras']['comp_datadir'];
									}
									$res[$row]['list'] = '<div>ip：' . $hostaddr . '</div><div>机架编号：' . $rack_id . '</div><div>存储节点数据目录：' . $datadir . '</div><div>日志目录：' . $logdir . '</div><div>wal日志目录：' . $wal_log_dir . '</div><div>计算节点数据目录：' . $comp_datadir . '</div>';
									$res[$row]['object'] = $hostaddr;
								} else {
									$res[$row]['list'] = '<div>ip：</div><div>机架编号：</div><div>存储节点数据目录：</div><div>日志目录：</div><div>wal日志目录：</div><div>计算节点数据目录：</div>';
								}
							}
							if ($value2 == 'delete_machine') {
								if (!empty($string)) {
									$hostaddr = '';
									if (!empty($string['paras']['hostaddr'])) {
										$hostaddr = $string['paras']['hostaddr'];
									}
									$res[$row]['list'] = '<div>ip：' . $hostaddr . '</div>';
									$res[$row]['object'] = $hostaddr;
								} else {
									$res[$row]['list'] = '<div>ip：</div>';
								}
							}
							if ($value2 == 'control_instance') {
								if (!empty($string)) {
									$control = '';
									$action = '';
									$object = '';
									//$res[$row]['job_type'] = '';
									if (!empty($string['paras']['control'])) {
										if ($string['paras']['control'] == 'start') {
											$control = '启用';
										} else if ($string['paras']['control'] == 'stop') {
											$control = '禁用';
										} else if ($string['paras']['control'] == 'restart') {
											$control = '重启';
										}
									}
									$hostaddr = '';
									if (!empty($string['paras']['hostaddr'])) {
										$hostaddr = $string['paras']['hostaddr'];
									}
									$port = '';
									if (!empty($string['paras']['port'])) {
										$port = $string['paras']['port'];
									}
									$cluster_id = '';
									if (!empty($string['paras']['cluster_id'])) {
										$cluster_id = $string['paras']['cluster_id'];
										$res[$row]['cluster_id'] = $cluster_id;
									}
									$nick_name = '';
									if (!empty($string['paras']['cluster_id'])) {
										$name = $this->getClusterName($string['paras']['cluster_id']);
										if (!empty($name)) {
											$nick_name = $name[0]['nick_name'];
										} else {
											$nick_name = '';
										}
										$object .= $nick_name;
									}
									$machine_type = '';
									$child = '';
									if (!empty($string['paras']['machine_type'])) {
										if ($string['paras']['machine_type'] == 'storage') {
											$machine_type = '存储节点';
											$shard_id = '';
											if (!empty($string['paras']['shard_id'])) {
												$shard_id = $string['paras']['shard_id'];
											}
											$shard_name = '';
											if (!empty($string['paras']['shard_id'])) {
												$shard_name = $this->getShardName($string['paras']['cluster_id'], $string['paras']['shard_id']);
												$object .= '(' . $shard_name . ')';
											}
											$child = '<div>shard_id：' . $shard_id . '</div><div>shard名称：' . $shard_name . '</div>';
										} else if ($string['paras']['machine_type'] == 'computer') {
											$machine_type = '计算节点';
										}
									}
									$action = $control . $machine_type;
									$res[$row]['job_type'] = $action;
									$res[$row]['list'] = '<div>操作：' . $action . '</div><div>集群ID：' . $cluster_id . '</div><div>业务名称：' . $nick_name . '</div>' . $child . '<div>ip：' . $hostaddr . '</div><div>端口：' . $port . '</div>';
									//$res[$row]['list']=array('操作'=>$string['control'],'ip'=>$string['ip'],'端口'=>$string['port']);
									$res[$row]['object'] = $object;
								} else {
									$res[$row]['list'] = '<div>操作：</div><div>ip：</div><div>端口：</div>';
								}
							}
							if ($value2 == 'delete_backup_storage') {
								if (!empty($string)) {
									$name = '';
									if (!empty($string['paras']['name'])) {
										$name = $string['paras']['name'];
									}

									$res[$row]['list'] = '<div>目标名称：' . $name . '</div>';
									//$res[$row]['list']=array('操作'=>$string['control'],'ip'=>$string['ip'],'端口'=>$string['port']);
									$res[$row]['object'] = $name;
								} else {
									$res[$row]['list'] = '<div>目标名称：</div>';
								}
							}
							if ($value2 == 'create_backup_storage') {
								if (!empty($string)) {
									$name = '';
									if (!empty($string['paras']['name'])) {
										$name = $string['paras']['name'];
									}
									$hostaddr = '';
									if (!empty($string['paras']['hostaddr'])) {
										$hostaddr = $string['paras']['hostaddr'];
									}
									$port = '';
									if (!empty($string['paras']['port'])) {
										$port = $string['paras']['port'];
									}
									$stype = '';
									if (!empty($string['paras']['stype'])) {
										$stype = $string['paras']['stype'];
									}
									$res[$row]['list'] = '<div>目标名称：' . $name . '</div><div>目标类型：' . $stype . '</div><div>ip：' . $hostaddr . '</div><div>端口：' . $port . '</div>';
									//$res[$row]['list']=array('操作'=>$string['control'],'ip'=>$string['ip'],'端口'=>$string['port']);
									$res[$row]['object'] = $name . '(' . $stype . ')';
								} else {
									$res[$row]['list'] = '<div>目标名称：</div><div>目标类型：</div><div>ip：</div><div>端口：</div>';
								}
							}
							if ($value2 == 'update_backup_storage') {
								if (!empty($string)) {
									$name = '';
									if (!empty($string['paras']['name'])) {
										$name = $string['paras']['name'];
									}
									$hostaddr = '';
									if (!empty($string['paras']['hostaddr'])) {
										$hostaddr = $string['paras']['hostaddr'];
									}
									$port = '';
									if (!empty($string['paras']['port'])) {
										$port = $string['paras']['port'];
									}
									$stype = '';
									if (!empty($string['paras']['stype'])) {
										$stype = $string['paras']['stype'];
									}
									$res[$row]['list'] = '<div>目标名称：' . $name . '</div><div>目标类型：' . $stype . '</div><div>ip：' . $hostaddr . '</div><div>端口：' . $port . '</div>';
									//$res[$row]['list']=array('操作'=>$string['control'],'ip'=>$string['ip'],'端口'=>$string['port']);
									$res[$row]['object'] = $name . '(' . $stype . ')';
								} else {
									$res[$row]['list'] = '<div>目标名称：</div><div>目标类型：</div><div>ip：</div><div>端口：</div>';
								}
							}
							if ($value2 == 'manual_switch') {
								if (!empty($string)) {
									$assign_hostaddr = '';
									if (!empty($string['paras']['assign_hostaddr'])) {
										$assign_hostaddr = $string['paras']['assign_hostaddr'];
									} else {
										$this->load->model('Cluster_model');
										$data_string = array(
											'version' => '1.0',
											'job_id' => $res[$row]['id'],
											'job_type' => 'get_status',
											'timestamp' => '1435749309',
											'paras' => '{}'
										);
										$post_data = json_encode($data_string);
										$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
										$post_arr = json_decode($post_arr, TRUE);
										//print_r($post_arr);exit;
										if (!empty($post_arr['attachment']['new_master_host'])) {
											$assign_hostaddr = $post_arr['attachment']['new_master_host'];
										}
									}
									$master_hostaddr = '';
									if (!empty($string['paras']['master_hostaddr'])) {
										$master_hostaddr = $string['paras']['master_hostaddr'];
									}
									$cluster_id = '';
									if (!empty($string['paras']['cluster_id'])) {
										$cluster_id = $string['paras']['cluster_id'];
									}
									$shard_id = '';
									if (!empty($string['paras']['shard_id'])) {
										$shard_id = $string['paras']['shard_id'];
									}
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_id'])) {
										$name = $this->getClusterName($string['paras']['cluster_id']);
										if (!empty($name)) {
											$nick_name = $name[0]['nick_name'];
										} else {
											$nick_name = '';
										}
									}
									$shard_name = '';
									if (!empty($string['paras']['shard_id']) && !empty($string['paras']['cluster_id'])) {
										$shard_name = $this->getShardName($string['paras']['cluster_id'], $string['paras']['shard_id']);
									}
									$res[$row]['list'] = '<div>集群ID：' . $cluster_id . '</div><div>shard_id：' . $shard_id . '</div><div>原主节点：' . $master_hostaddr . '</div><div>新主节点：' . $assign_hostaddr . '</div>';
									$res[$row]['object'] = $nick_name . '(' . $shard_name . ')';
								} else {
									$res[$row]['list'] = '<div>集群ID：</div><div>shard_id：</div><div>原主节点：</div><div>新主节点：</div>';
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'rebuild_node') {
								if (!empty($string)) {
									$cluster_id = '';
									if (!empty($string['paras']['cluster_id'])) {
										$cluster_id = $string['paras']['cluster_id'];
									}
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_id'])) {
										$name = $this->getClusterName($string['paras']['cluster_id']);
										if (!empty($name)) {
											$nick_name = $name[0]['nick_name'];
										} else {
											$nick_name = '';
										}
									}
									$shard_id = '';
									if (!empty($string['paras']['shard_id'])) {
										$shard_id = $string['paras']['shard_id'];
									}
									$shard_name = '';
									if (!empty($string['paras']['shard_id']) && !empty($string['paras']['cluster_id'])) {
										$shard_name = $this->getShardName($string['paras']['cluster_id'], $string['paras']['shard_id']);
									}
									// 是否从主节点上拉取数据
									$allow_pull_from_master = '';
									//主备延迟
									$allow_replica_delay = '';
									$arr = '<div>集群ID：' . $cluster_id . '</div><div>业务名称：' . $nick_name . '</div><div>shard_id：' . $shard_id . '</div><div>shard名称：' . $shard_name . '</div>';
									if (isset($string['paras']['allow_pull_from_master'])) {
										if ($string['paras']['allow_pull_from_master'] == '1') {
											$allow_pull_from_master = '是';
											$arr .= '<div>是否从主节点上拉取数据：' . $allow_pull_from_master . '</div>';
										} else {
											if (!empty($string['paras']['allow_replica_delay'])) {
												$allow_replica_delay = $string['paras']['allow_replica_delay'];
											} else {
												$allow_replica_delay = '';
											}
											$allow_pull_from_master = '否';
											$arr .= '<div>是否从主节点上拉取数据：' . $allow_pull_from_master . '</div><div>主备延迟：' . $allow_replica_delay . 's</div>';
										}
									}
									//限速
									$pv_limit = '';
									$hostaddr = '';
									$port = '';
									$need_backup = '';
									$hdfs_host = '';
									$redo_arr = '';
									if (!empty($string['paras']['rb_nodes'])) {
										foreach ($string['paras']['rb_nodes'] as $key3 => $value3) {
											$arr_child = '';
											//print_r($value3['hostaddr']);exit;
											if (!empty($value3['hostaddr'])) {
												$hostaddr = $value3['hostaddr'];
											} else {
												$hostaddr = '';
											}
											if (!empty($value3['port'])) {
												$port = $value3['port'];
											} else {
												$port = '';
											}
											if (!empty($value3['pv_limit'])) {
												$pv_limit = $value3['pv_limit'];
											} else {
												$pv_limit = '';
											}
											$arr_child .= '<div>ip：' . $hostaddr . '</div><div>端口：' . $port . '</div><div>限速：' . $pv_limit . 'KB/s</div>';
											if (isset($value3['need_backup'])) {
												if ($value3['need_backup'] == '1') {
													$need_backup = '是';
													if (!empty($value3['hdfs_host'])) {
														$hdfs_host = $value3['hdfs_host'];
													} else {
														$hdfs_host = '';
													}
													$arr_child .= '<div>是否备份：' . $need_backup . '</div><div>备份存储目标：' . $hdfs_host . '</div>';
												} else {
													$need_backup = '否';
													$arr_child .= '<div>是否备份：' . $need_backup . '</div>';
												}
											} else {
												$need_backup = '';
											}
											$redo_arr .= '<div>需重做备机节点' . ($key3 + 1) . '：<div style="margin-left:30px;">' . $arr_child . '</div></div>';
										}
									}
									//$res[$row]['list'] = '<div>集群ID：' .$cluster_id. '</div><div>业务名称：' .$nick_name . '</div><div>shard_id：'.$shard_id.'</div><div>shard名称：'.$shard_name.'</div><div>需重做的备机节点：'.$shard_name.'</div><div>是否从主节点上拉取数据：'.$shard_name.'</div><div>主备延迟：'.$shard_name.'</div><div>是否备份：'.$shard_name.'</div><div>备份存储目标：'.$shard_name.'</div><div>限速：'.$shard_name.'</div>';
									$res[$row]['list'] = $arr . '<div style="max-height:250px;overflow-y:auto;">' . $redo_arr . '</div>';
									$res[$row]['object'] = $nick_name . '(' . $shard_name . ')';
								} else {
									$res[$row]['list'] = '<div>集群ID：</div><div>业务名称：</div>';
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'cluster_restore') {
								if (!empty($string)) {
									$dst_cluster_id = '';
									if (!empty($string['paras']['dst_cluster_id'])) {
										$dst_cluster_id = $string['paras']['dst_cluster_id'];
									}
									$src_cluster_id = '';
									$src_nick_name = '';
									if (!empty($string['paras']['src_cluster_id'])) {
										$src_cluster_id = $string['paras']['src_cluster_id'];
										$src_name = $this->getClusterName($src_cluster_id);
										if (!empty($src_name)) {
											$src_nick_name = $src_name[0]['nick_name'];
										} else {
											$src_nick_name = '';
										}
									}
									$restore_time = '';
									if (!empty($string['paras']['restore_time'])) {
										$restore_time = $string['paras']['restore_time'];
									}
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['dst_cluster_id'])) {
										$name = $this->getClusterName($string['paras']['dst_cluster_id']);
										if (!empty($name)) {
											$nick_name = $name[0]['nick_name'];
										} else {
											$nick_name = '';
										}
									}
									// if(!empty($dst_cluster_id)){
									// 	$res[$row]['cluster_id']=$dst_cluster_id;
									// }
									$res[$row]['list'] = '<div>原集群ID：' . $src_cluster_id . '</div><div>原集群业务名称：' . $src_nick_name . '</div><div>目标集群ID：' . $dst_cluster_id . '</div><div>目标集群业务名称：' . $nick_name . '</div><div>回档时间：' . $restore_time . '</div>';
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['list'] = '<div>原集群ID：</div><div>原集群业务名称：</div><div>目标集群ID：</div><div>目标集群业务名称：</div><div>回档时间：</div>';
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'manual_backup_cluster') {
								if (!empty($string)) {
									$cluster_id = '';
									if (!empty($string['paras']['cluster_id'])) {
										$cluster_id = $string['paras']['cluster_id'];
									}
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_id'])) {
										$name = $this->getClusterName($string['paras']['cluster_id']);
										if (!empty($name)) {
											$nick_name = $name[0]['nick_name'];
										} else {
											$nick_name = '';
										}
									}
									$res[$row]['list'] = '<div>集群ID：' . $cluster_id . '</div><div>业务名称：' . $nick_name . '</div>';
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['list'] = '<div>集群ID：</div><div>业务名称：</div>';
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'expand_cluster') {
								if (!empty($string)) {
									$cluster_id = '';
									if (!empty($string['paras']['cluster_id'])) {
										$cluster_id = $string['paras']['cluster_id'];
									}
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_id'])) {
										$name = $this->getClusterName($string['paras']['cluster_id']);
										if (!empty($name)) {
											$nick_name = $name[0]['nick_name'];
										} else {
											$nick_name = '';
										}
									}
									$src_shard_id = '';
									if (!empty($string['paras']['src_shard_id'])) {
										$src_shard_id = $string['paras']['src_shard_id'];
									}
									$src_shard_name = '';
									if (!empty($string['paras']['src_shard_id'])) {
										$src_shard_name = $this->getShardName($string['paras']['cluster_id'], $string['paras']['src_shard_id']);;
									}
									$dst_shard_name = '';
									if (!empty($string['paras']['dst_shard_id'])) {
										$dst_shard_name = $this->getShardName($string['paras']['cluster_id'], $string['paras']['dst_shard_id']);;
									}
									$dst_shard_id = '';
									if (!empty($string['paras']['dst_shard_id'])) {
										$dst_shard_id = $string['paras']['dst_shard_id'];
									}
									$drop_old_table = '';
									if (isset($string['paras']['drop_old_table'])) {
										if ($string['paras']['drop_old_table'] == '1') {
											$drop_old_table = '否';
										} else {
											$drop_old_table = '是';
										}
									}
									$table_list = '';
									if (!empty($string['paras']['table_list'])) {
										$table_list = $string['paras']['table_list'];
										$child = '';
										foreach ($table_list as $key4) {
											$child .= '<div>' . $key4 . '</div>';
										}
									}
									$res[$row]['list'] = '<div>集群ID：' . $cluster_id . '</div><div>业务名称：' . $nick_name . '</div><div>原shard_id：' . $src_shard_id . '</div><div>原shard名称：' . $src_shard_name . '</div><div>目标shard_id：' . $dst_shard_id . '</div><div>目标shard名称：' . $dst_shard_name . '</div><div>是否保留原表：' . $drop_old_table . '</div><div>已选原shard表：<div style="margin-left:30px;max-height:250px;overflow-y:auto;">' . $child . '</div></div>';
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['list'] = '';
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == "logical_backup") {
								$res[$row]['job_type'] = '表逻辑备份';

								//$data=json_decode()

							}
							if ($value2 == 'delete_rcr') {
								$res[$row]['job_type'] = '刪除RCR';
								if (!empty($string)) {
									$slave_cluster_id = '';$meta_db='';$master_cluster_id='';$slave_meta_db='';$sync_host_delay='';$delay_sync='';$child_slave_db='';$child_master_db='';
									if (!empty($string['paras']['cluster_id'])) {
										$slave_cluster_id = $string['paras']['cluster_id'];
									}
									if (!empty($string['paras']['master_info']['meta_db'])) {
										//$meta_db = $string['paras']['master_info']['meta_db'];
										$comparr = explode(',', $string['paras']['master_info']['meta_db']);
										for ($i = 0; $i < count($comparr); $i++) {
											$child_master_db .= '<div>' . $comparr[$i] . '</div>';
										}
										$meta_db = '<div>元数据主：<div style="margin-left:30px;">' . $child_master_db. '</div></div>';
									}
									if (!empty($string['paras']['master_info']['cluster_id'])) {
										$master_cluster_id = $string['paras']['master_info']['cluster_id'];
									}
									if (!empty($string['paras']['slave_rcr_meta'])) {
										//$slave_meta_db = $string['paras']['slave_rcr_meta'];
										$comparr = explode(',', $string['paras']['slave_rcr_meta']);
										for ($i = 0; $i < count($comparr); $i++) {
											$child_slave_db .= '<div>' . $comparr[$i] . '</div>';
										}
										$slave_meta_db = '<div>元数据主：<div style="margin-left:30px;">' . $child_slave_db. '</div></div>';
									}
									if (!is_null($string['paras']['sync_host_delay'])) {
										$sync_host_delay = '<div>shard延迟时间(主)：'.$string['paras']['sync_host_delay'].'s</div>';
									}
									if (!is_null($string['paras']['delay_sync'])) {
										$delay_sync= '<div>延迟复制时间：'.$string['paras']['delay_sync'].'s</div>';
									}
									$res[$row]['list'] = $meta_db . '<div>主集群ID：' . $master_cluster_id . '</div>' . $slave_meta_db . '<div>备集群ID：' . $slave_cluster_id . '</div>' . $sync_host_delay  . $delay_sync ;
									$res[$row]['object'] = $master_cluster_id.'→'.$slave_cluster_id;
								} else {
									$res[$row]['list'] = '';
								}
							}
							if ($value2 == 'create_rcr') {
								$res[$row]['job_type'] = '新增RCR';
								if (!empty($string)) {
									$slave_cluster_id = '';$meta_db='';$master_cluster_id='';$slave_meta_db='';$sync_host_delay='';$delay_sync='';$child_slave_db='';$child_master_db='';
									if (!empty($string['paras']['cluster_id'])) {
										$slave_cluster_id = $string['paras']['cluster_id'];
									}
									if (!empty($string['paras']['master_info']['meta_db'])) {
										$comparr = explode(',', $string['paras']['master_info']['meta_db']);
										for ($i = 0; $i < count($comparr); $i++) {
											$child_master_db .= '<div>' . $comparr[$i] . '</div>';
										}
										$meta_db = '<div>元数据主：<div style="margin-left:30px;">' . $child_master_db. '</div></div>';
									}
									if (!empty($string['paras']['master_info']['cluster_id'])) {
										$master_cluster_id = $string['paras']['master_info']['cluster_id'];
									}
									if (!empty($string['paras']['slave_rcr_meta'])) {
										$comparr = explode(',', $string['paras']['slave_rcr_meta']);
										for ($i = 0; $i < count($comparr); $i++) {
											$child_slave_db .= '<div>' . $comparr[$i] . '</div>';
										}
										$slave_meta_db = '<div>元数据主：<div style="margin-left:30px;">' . $child_slave_db. '</div></div>';
									}
									if (!is_null($string['paras']['sync_host_delay'])) {
										$sync_host_delay = '<div>shard延迟时间(主)：'.$string['paras']['sync_host_delay'].'s</div>';
									}
									if (!is_null($string['paras']['delay_sync'])) {
										$delay_sync= '<div>延迟复制时间：'.$string['paras']['delay_sync'].'s</div>';
									}
									$res[$row]['list'] = $meta_db . '<div>主集群ID：' . $master_cluster_id . '</div>' . $slave_meta_db . '<div>备集群ID：' . $slave_cluster_id . '</div>' . $sync_host_delay  . $delay_sync ;
									$res[$row]['object'] = $master_cluster_id.'→'.$slave_cluster_id;
								} else {
									$res[$row]['list'] = '';
								}
							}
							if ($value2 == 'modify_rcr') {

								$res[$row]['job_type'] = '设置RCR';
							}
							if ($value2 == 'manualsw_rcr') {
//								if (!empty($string['paras']['rcr_id'])) {
//									$rcr_id = $string['paras']['rcr_id'];
//								}
								$res[$row]['job_type'] = '手动切换RCR';
//								$res[$row]['object'] =
							}

						} else {
							$res[$row]['job_type'] = '';
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

	public function getNickName($cluster_name)
	{
		$sql = "select nick_name from db_clusters where name='$cluster_name' ";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		return $res[0]['nick_name'];
	}

	public function getHomeOperationList()
	{
		$job_type = $this->job_type;
		$this->load->model('Cluster_model');
		//获取任务信息
		$sql = "select job_type,when_started,status,job_info,user_name from cluster_general_job_log where user_name!='internal_user'";
		$sql .= "  order by id desc limit 100";
		$res = $this->Cluster_model->getList($sql);
		if ($res === false) {
			$res = array();
		} else {
			foreach ($res as $row => $value) {
				foreach ($value as $key2 => $value2) {
					$string = json_decode($res[$row]['job_info'], true);
					if ($key2 == 'job_type') {
						if (!empty($value2)) {
							foreach ($job_type as $k2 => $v2) {
								if ($value2 == $v2['code']) {
									$res[$row]['job_type'] = $v2['name'];
									break;
								} else {
									$res[$row]['job_type'] = $value2;
								}
							}
							if ($value2 == 'create_cluster') {
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_name'])) {
										$nick_name = $string['paras']['cluster_name'];
									}
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'delete_cluster') {
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_name'])) {
										$nick_name = $string['paras']['cluster_name'];
									}
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'add_shards') {
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_name'])) {
										$nick_name = $string['paras']['cluster_name'];
									}
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'delete_shard') {
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_name'])) {
										$nick_name = $string['paras']['cluster_name'];
									}
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'backup_cluster') {
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['backup_cluster_name'])) {
										$nick_name = $string['paras']['backup_cluster_name'];
									}
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'restore_new_cluster') {
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_name'])) {
										$nick_name = $string['paras']['cluster_name'];
									}
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'add_comps') {
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_id'])) {
										$name = $this->getClusterName($string['paras']['cluster_id']);
										if (!empty($name)) {
											$nick_name = $name[0]['nick_name'];
										} else {
											$nick_name = '';
										}
									}
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'delete_comp') {
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_name'])) {
										$nick_name = $string['paras']['cluster_name'];
									}
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'add_nodes') {
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_name'])) {
										$nick_name = $string['paras']['cluster_name'];
									}
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'delete_node') {
								if (!empty($string)) {
									$nick_name = '';
									if (!empty($string['paras']['nick_name'])) {
										$nick_name = $string['paras']['nick_name'];
									} else if (!empty($string['paras']['cluster_name'])) {
										$nick_name = $string['paras']['cluster_name'];
									}
									$res[$row]['object'] = $nick_name;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'mysqld_exporter') {
								if (!empty($string)) {
									$hostaddr = '';
									if (!empty($string['paras']['hostaddr'])) {
										$hostaddr = $string['paras']['hostaddr'];
									}
									$port = '';
									if (!empty($string['paras']['port'])) {
										$port = $string['paras']['port'];
									}
									$res[$row]['object'] = $hostaddr . ':' . $port;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'postgres_exporter') {
								if (!empty($string)) {
									$hostaddr = '';
									if (!empty($string['paras']['hostaddr'])) {
										$hostaddr = $string['paras']['hostaddr'];
									}
									$port = '';
									if (!empty($string['paras']['port'])) {
										$port = $string['paras']['port'];
									}
									$res[$row]['object'] = $hostaddr . ':' . $port;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'create_machine') {
								if (!empty($string)) {
									$hostaddr = '';
									if (!empty($string['paras']['hostaddr'])) {
										$hostaddr = $string['paras']['hostaddr'];
									}
									$res[$row]['object'] = $hostaddr;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'update_machine') {
								if (!empty($string)) {
									$hostaddr = '';
									if (!empty($string['paras']['hostaddr'])) {
										$hostaddr = $string['paras']['hostaddr'];
									}
									$res[$row]['object'] = $hostaddr;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'delete_machine') {
								if (!empty($string)) {
									$hostaddr = '';
									if (!empty($string['paras']['hostaddr'])) {
										$hostaddr = $string['paras']['hostaddr'];
									}
									$res[$row]['object'] = $hostaddr;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'control_instance') {
								if (!empty($string)) {
									$control = '';
									if (!empty($string['paras']['control'])) {
										$control = $string['paras']['control'];
									}
									$res[$row]['object'] = $control;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'delete_backup_storage') {
								if (!empty($string)) {
									$name = '';
									if (!empty($string['paras']['name'])) {
										$name = $string['paras']['name'];
									}
									$res[$row]['object'] = $name;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'create_backup_storage') {
								if (!empty($string)) {
									$name = '';
									if (!empty($string['paras']['name'])) {
										$name = $string['paras']['name'];
									}
									$res[$row]['object'] = $name;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'update_backup_storage') {
								if (!empty($string)) {
									$name = '';
									if (!empty($string['paras']['name'])) {
										$name = $string['paras']['name'];
									}
									$res[$row]['object'] = $name;
								} else {
									$res[$row]['object'] = '';
								}
							}
							if ($value2 == 'manual_switch') {
								$res[$row]['object'] = '';
							}
						} else {
							$res[$row]['job_type'] = '';
						}
					}

				}
			}
		}
		$data['code'] = 200;
		$data['list'] = $res;
		print_r(json_encode($data));
	}

	public function getOptionCount()
	{
		$job_type = $this->job_type;
		$this->load->model('Cluster_model');
		//获取任务信息
		$sql = "select job_type,count(job_type) as count from cluster_general_job_log  GROUP BY job_type;";
		$res = $this->Cluster_model->getList($sql);
		if ($res === false) {
			$res = array();
		} else {
			$type = array();
			$per_total = array();
			$numbers = array();
			foreach ($res as $key => $row) {
				foreach ($row as $k1 => $v1) {
					$job_type_arr = '';
					if ($k1 == "job_type") {
						$person_count = $this->getTypePer($v1);
						foreach ($job_type as $k2 => $v2) {
							if ($v1 == $v2['code']) {
								$job_type_arr = $v2['name'];
								break;
							}
						}
						$type[] = $job_type_arr;
						$per_total[] = (int)$person_count;
					}
					if ($k1 == "count") {
						$numbers[] = (int)$v1;
					}
				}
			}
			$data['code'] = 200;
			$data['type'] = $type;
			$data['numbers'] = $numbers;
			$data['per_total'] = $per_total;
			print_r(json_encode($data));
		}
	}

	public function getTypePer($job_type)
	{
		$this->load->model('Cluster_model');
		$sql = "select count(user_name) as count from ( select user_name from cluster_general_job_log where job_type='$job_type' GROUP BY user_name) as a;";
		$res = $this->Cluster_model->getList($sql);
		if ($res === false) {
			$count = 0;
		} else {
			$count = $res[0]['count'];
		}
		return $count;
	}

	public function getClusterName($id)
	{
		$sql = "select name,nick_name from db_clusters where id='$id'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		return $res;
	}

	public function getShardName($db_cluster_id, $id)
	{
		$sql = "select name from shards where id='$id' and db_cluster_id='$db_cluster_id'";
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
		$sql = "select name,hostaddr,port from comp_nodes where id='$id' and db_cluster_id='$db_cluster_id'";
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		return $res;
	}
	public function delTable()
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


}
