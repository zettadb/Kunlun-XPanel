<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AlarmRecord extends CI_Controller {

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
		$this->key=$this->config->item('key');
		$this->post_url=$this->config->item('post_url');
		$this->job_type=$this->config->item('job_type');
	}
	// public function getAlarmRecordList()
	// {
	// 	//GET请求
	// 	$serve=$_SERVER['QUERY_STRING'];
	// 	$string=preg_split('/[=&]/',$serve);
	// 	$arr=array();
	// 	for($i=0;$i<count($string);$i+=2) {
	// 		$arr[$string[$i]] = $string[$i + 1];
	// 	}
	// 	$pageNo=$arr['pageNo'];
	// 	$pageSize=$arr['pageSize'];
	// 	$username=$arr['job_type'];
	// 	$start=($pageNo - 1) * $pageSize;
	// 	$job_type=$this->job_type;
	// 	//print_r($pageSize);exit;
	// 	//获取数据
	// 	$this->load->model('Cluster_model');
	// 	$sql="select id,job_type,user_name,memo,status from cluster_general_job_log where user_name!='internal_user'and status='failed' and job_type in('create_cluster','delete_cluster','add_shards','delete_shard','add_comps','delete_comp','add_nodes','delete_node','manual_switch','rebuild_node','cluster_restore','expand_cluster','manual_backup_cluster')";
	// 	if(!empty($username)){
	// 		$sql .=" and  job_type ='$username'";
	// 	}
	// 	$sql .=" order by id desc limit ".$pageSize." offset ".$start;
	// 	//print_r($sql);exit;
	// 	$res=$this->Cluster_model->getList($sql);
	// 	if($res===false){
	// 		$res=array();
	// 	}else{
	// 		foreach ($res as $row=>$value){
	// 			foreach ($value as $key2 => $value2) {
	// 				if ($key2 == 'job_type') {
	// 					if(!empty($value2)) {
	// 						foreach($job_type as $k2 => $v2){
	// 							if($value2== $v2['code']){
	// 								$res[$row]['job_type'] = $v2['name'];
	// 								break;
	// 							}else{
	// 								$res[$row]['job_type'] = $value2;
	// 							}
	// 						}
	// 					}
	// 				}
	// 				//输入信息
	// 				if ($key2 == 'memo') {
	// 					if(!empty($value2)) {
	// 						$info=json_decode($value2,true);
	// 						if(!empty($info)) {
	// 							// if(!empty($info['cluster_id'])){
	// 							// 	$res[$row]['cluster_id'] = $info['cluster_id'];
	// 							// }else{
	// 							// 	if(!empty($string['paras']['cluster_id'])){
	// 							// 		$res[$row]['cluster_id'] =$string['paras']['cluster_id'];
	// 							// 	}else{
	// 							// 		$res[$row]['cluster_id'] ='';
	// 							// 	}
	// 							// }
	// 							if(!empty($info['error_info'])){
	// 								if($res[$row]['status']=='ongoing'){
	// 									$res[$row]['info'] = '';
	// 								}else{
	// 									$res[$row]['info'] = $info['error_info'];
	// 									//print_r($info['error_info']);exit;
	// 								}
	// 							}else{
	// 								$res[$row]['info'] = '';
	// 							}
	// 						}
	// 					}else{
	// 						$res[$row]['info'] = '';
	// 						// if(!empty($string['paras']['cluster_id'])){
	// 						// 	$res[$row]['cluster_id'] =$string['paras']['cluster_id'];
	// 						// }else{
	// 						// 	$res[$row]['cluster_id'] ='';
	// 						// }
	// 					}
	// 				}
	// 			}
	// 		}
	// 	}
	// 	$sql_total="select count(id) as count from cluster_general_job_log where user_name!='internal_user'and status='failed' and job_type in('create_cluster','delete_cluster','add_shards','delete_shard','add_comps','delete_comp','add_nodes','delete_node','manual_switch','rebuild_node','cluster_restore','expand_cluster','manual_backup_cluster')";
	// 	if(!empty($username)){
	// 		$sql_total .=" and  job_type ='$username'";
	// 	}
	// 	$res_total=$this->Cluster_model->getList($sql_total);
	// 	$data['code'] = 200;
	// 	$data['list'] = $res;
	// 	$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
	// 	print_r(json_encode($data));
	// }
	public function getAlarmRecordList()
	{
		//GET请求
		$serve=$_SERVER['QUERY_STRING'];
		$string=preg_split('/[=&]/',$serve);
		$arr=array();
		for($i=0;$i<count($string);$i+=2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$pageNo=$arr['pageNo'];
		$pageSize=$arr['pageSize'];
		$username=$arr['job_type'];
		$status=$arr['status'];
		$alarm_level=$arr['alarm_level'];
		$start=($pageNo - 1) * $pageSize;
		$job_type=$this->job_type;
		//print_r($pageSize);exit;
		//获取数据
		$this->load->model('Cluster_model');
		//先查表是否存在
		$sqldalay="select TABLE_NAME from information_schema.TABLES where TABLE_NAME = 'cluster_alarm_info';";
		$resdalay=$this->Cluster_model->getList($sqldalay);
		if($resdalay!==false){
			$sql="select id,alarm_type as job_type,status,job_info,handle_time,handler_name from cluster_alarm_info where id is not null";
			if(!empty($username)){
				$sql .=" and  alarm_type ='$username'";
			}
			if(!empty($status)){
				$sql .=" and  status ='$status'";
			}
			if(!empty($alarm_level)){
				$sql .=" and  alarm_level ='$alarm_level'";
			}
			$sql .=" order by id desc limit ".$pageSize." offset ".$start;
			//print_r($sql);exit;
			$res=$this->Cluster_model->getList($sql);
			if($res===false){
				$res=array();
			}else{
				foreach ($res as $row=>$value){
					foreach ($value as $key2 => $value2) {
						$string=json_decode($res[$row]['job_info'],true);
						if ($key2 == 'job_type') {
							if(!empty($value2)) {
								if($value2=='standby_delay'){
									$res[$row]['job_type'] ='主备延迟过大';
									$res[$row]['object']=$string['ip'].'_'.$string['port'];
									$res[$row]['list'] = '<div>集群ID：'.$string["cluster_id"]. '</div><div>shard_id：'.$string["shard_id"].'</div><div>IP：'.$string["ip"].'</div><div>端口：'.$string["port"].'</div>';
								}else if($value2=='storage_node_exception'){
									$res[$row]['job_type'] ='存储节点异常';
									$res[$row]['object']=$string['ip'].'_'.$string['port'];
									$res[$row]['list'] = '<div>集群ID：'.$string["cluster_id"]. '</div><div>shard_id：'.$string["shard_id"].'</div><div>IP：'.$string["ip"].'</div><div>端口：'.$string["port"].'</div>';
								}else if($value2=='comp_node_exception'){
									$res[$row]['job_type'] ='计算节点异常';
									$res[$row]['object']=$string['ip'].'_'.$string['port'];
									$res[$row]['list'] = '<div>集群ID：'.$string["cluster_id"]. '</div><div>IP：'.$string["ip"].'</div><div>端口：'.$string["port"].'</div>';
								}else if($value2=='machine_offline'){
									$res[$row]['job_type'] ='设备离线';
									$res[$row]['object']=$string['ip'];
									$res[$row]['list'] = '<div>IP：'.$string["ip"].'</div>';
								}else if($value2=='shard_coldbackup'){
									$res[$row]['job_type'] ='自动备份';
									$res[$row]['object']=$string['ip'].'_'.$string['port'];
									$arr_list='';
									if($string["compid"]!==null){
										$arr_list='<div>计算节点id：'.$string["compid"]. '</div>';
									}else if($string["shardid"]!==null){
										$arr_list='<div>shard_id：'.$string["shardid"]. '</div>';
									}
									$res[$row]['list'] = '<div>job_id：'.$string["id"].'</div><div>集群ID：'.$string["cluster_id"].'</div><div>IP：'.$string["ip"].'</div><div>端口：'.$string["port"].'</div><div>开始时间：'.$string["when_started"].'</div><div>结束时间：'.$string["when_ended"].'</div>'.$arr_list;
								}
								else{
									foreach($job_type as $k2 => $v2){
										if($value2== $v2['code']){
											$res[$row]['job_type'] = $v2['name'];
											break;
										}else{
											$res[$row]['job_type'] = $value2;
										}
									}
									if($value2=='manual_switch'){
										$switch_type='';
										if($string['sw_type']=='failover'){
											$switch_type='自动';
										}elseif($string['sw_type']=='manual'){
											$switch_type='手动';
										}
										$res[$row]['object']=$string['host'].'_'.$switch_type;
										$res[$row]['list'] = '<div>原主节点：'.$string["host"].'</div><div>taskid：'.$string["taskid"].'</div><div>集群ID：'.$string["cluster_id"].'</div><div>shard_id：'.$string["shard_id"].'</div><div>切换方式：'.$switch_type.'</div>';
									}
									if($value2=='expand_cluster'){
										$cluster_name=$this->getClusterName($string['cluster_id']);
										$shard_name=$this->getShardName($string['cluster_id'],$string['src_shard_id']);
										$res[$row]['object']=$cluster_name.'('.$shard_name.')';
										$drop_old_table='';
									if(isset($string['drop_old_table'])){
										if($string['drop_old_table']=='1'){
											$drop_old_table='否';
										}else{
											$drop_old_table='是';
										}
									}
									$table_list='';
									if(!empty($string['table_list'])){
										$table_list=$string['table_list'];
										$child='';
										foreach ($table_list as $key4) {
											$child.='<div>'.$key4.'</div>';	
										}
									}
										$res[$row]['list'] = '<div>集群ID：' .$string["cluster_id"]. '</div><div>原shard_id：'.$string["src_shard_id"].'</div><div>目标shard_id：'.$string["dst_shard_id"].'</div><div>是否保留原表：' .$drop_old_table . '</div><div>已选原shard表：<div style="margin-left:30px;max-height:250px;overflow-y:auto;">'.$child.'</div></div>';
									}
									if($value2=='create_cluster'){
										$res[$row]['object']=$string['paras']['nick_name'];
										$child='';
										$arr='';
										if(!empty($string['paras']['computer_iplists'])) {
											foreach ($string['paras']['computer_iplists'] as $host) {
												$child.='<div>'.$host.'</div>';
											}
											$arr.='<div>所选计算类型的计算机：<div style="margin-left:30px;">'.$child.'</div></div>';
										}
										$shard_child='';
										$shard_arrs='';
										if(!empty($string['paras']['storage_iplists'])) {
											foreach ($string['paras']['storage_iplists'] as $host1) {
												$shard_child.='<div>'.$host1.'</div>';
											}
											$shard_arrs.='<div>所选存储类型的计算机：<div style="margin-left:30px;">'.$shard_child.'</div></div>';
										}
										$res[$row]['list'] = '<div>job_id：'.$string["id"].'</div><div>业务名称：'.$string["paras"]["nick_name"].'</div><div>shard总个数：'.$string["paras"]["shards"].'</div><div>每个shard含节点总个数：'.$string["paras"]["nodes"].'</div><div>计算节点总个数：'.$string["paras"]["comps"].'</div><div>每计算节点最大连接数：'.$string["paras"]["max_connections"].'</div><div>缓冲池大小：' .$string["paras"]["innodb_size"]. 'MB</div><div>cpu核数：'.$string["paras"]["cpu_cores"].'</div><div>高可用模式：'.$string["paras"]["ha_mode"].'</div>'.$arr.$shard_arrs;
									}
									if($value2=='delete_cluster'){
										$cluster_name=$this->getClusterName($string['paras']['cluster_id']);
										$res[$row]['object']=$cluster_name;
										$child='';
										$arr='';
										if(!empty($string['paras']['computer_iplists'])) {
											foreach ($string['paras']['computer_iplists'] as $host) {
												$child.='<div>'.$host.'</div>';
											}
											$arr.='<div>所选计算类型的计算机：<div style="margin-left:30px;">'.$child.'</div></div>';
										}
										$shard_child='';
										$shard_arrs='';
										if(!empty($string['paras']['storage_iplists'])) {
											foreach ($string['paras']['storage_iplists'] as $host1) {
												$shard_child.='<div>'.$host1.'</div>';
											}
											$shard_arrs.='<div>所选存储类型的计算机：<div style="margin-left:30px;">'.$shard_child.'</div></div>';
										}
										$nick_name='';
										if (array_key_exists("nick_name", $string['paras'])) {
											$nick_name=$string['paras']['nick_name'];
										}
										$res[$row]['list'] = '<div>job_id：'.$string["id"].'</div><div>业务名称：'.$nick_name.'</div><div>shard总个数：'.$string["paras"]["shards"].'</div><div>每个shard含节点总个数：'.$string["paras"]["nodes"].'</div><div>计算节点总个数：'.$string["paras"]["comps"].'</div><div>每计算节点最大连接数：'.$string["paras"]["max_connections"].'</div><div>缓冲池大小：' .$string["paras"]["innodb_size"]. 'MB</div><div>cpu核数：'.$string["paras"]["cpu_cores"].'</div><div>高可用模式：'.$string["paras"]["ha_mode"].'</div>'.$arr.$shard_arrs;
									}
									if($value2=='add_shards'){
										$cluster_name=$this->getClusterName($string['paras']['cluster_id']);
										$res[$row]['object']=$cluster_name;
										$shard_child='';
										$shard_arrs='';
										if(!empty($string['paras']['storage_iplists'])) {
											foreach ($string['paras']['storage_iplists'] as $host1) {
												$shard_child.='<div>'.$host1.'</div>';
											}
											$shard_arrs.='<div>所选计算机：<div style="margin-left:30px;">'.$shard_child.'</div></div>';
										}
										$nick_name='';
										if (array_key_exists("nick_name", $string['paras'])) {
											$nick_name=$string['paras']['nick_name'];
										}
										$res[$row]['list']='<div>job_id：'.$string["id"].'</div><div>业务名称：'.$nick_name.'</div><div>集群ID：'.$string["paras"]["cluster_id"].'</div><div>shard个数：'.$string["paras"]["shards"].'</div><div>副本数：'.$string["paras"]["nodes"].'</div>'.$shard_arrs;
									}
									if($value2=='delete_shard'){
										$shard_name=$this->getShardName($string['paras']['cluster_id'],$string['paras']['shard_id']);
										$res[$row]['object']=$string['paras']['nick_name'].'('.$shard_name.')';
										$res[$row]['list']='<div>job_id：'.$string["id"].'</div><div>集群ID：'.$string["paras"]["cluster_id"].'</div><div>shard_id：'.$string["paras"]["shard_id"].'</div>';
									}
									if($value2=='delete_comp'){
										$cluster_name=$this->getClusterName($string['paras']['cluster_id']);
										if(isset($string['paras']['comp_id'])){
											$comp_id=$string['paras']['comp_id'];
										}else{
											$comp_id='';
										}
										$comp_name=$this->getCompName($string['paras']['cluster_id'],$comp_id);
										$res[$row]['object']=$cluster_name.'('.$comp_name.')';
										$res[$row]['list']='<div>job_id：'.$string["id"].'</div><div>业务名称：'.$string["paras"]["nick_name"].'</div><div>集群ID：'.$string["paras"]["cluster_id"].'</div><div>计算节点id：'.$comp_id.'</div>';
									}
									if($value2=='add_comps'){
										$res[$row]['object']=$string['paras']['nick_name'];
										$child='';
										$arr='';
										if(!empty($string['paras']['computer_iplists'])) {
											foreach ($string['paras']['computer_iplists'] as $host) {
												$child.='<div>'.$host.'</div>';
											}
											$arr.='<div>选择计算机：<div style="margin-left:30px;">'.$child.'</div></div>';
										}
										$res[$row]['list']='<div>job_id：'.$string["id"].'</div><div>业务名称：'.$string["paras"]["nick_name"].'</div><div>集群ID：'.$string["paras"]["cluster_id"].'</div><div>计算节点个数：'.$string["paras"]["comps"].'</div>'.$arr;
									}
									if($value2=='add_nodes'){
										$cluster_name=$this->getClusterName($string['paras']['cluster_id']);
										$shard_name=$this->getShardName($string['paras']['cluster_id'],$string['paras']['shard_id']);
										$res[$row]['object']=$cluster_name.'('.$shard_name.')';
										$child='';
										$arr='';
										if(!empty($string['paras']['storage_iplists'])) {
											foreach ($string['paras']['storage_iplists'] as $host) {
												$child.='<div>'.$host.'</div>';
											}
											$arr.='<div>选择计算机：<div style="margin-left:30px;">'.$child.'</div></div>';
										}
										$nick_name='';
										if (array_key_exists("nick_name", $string['paras'])) {
											$nick_name=$string['paras']['nick_name'];
										}
										$res[$row]['list'] = '<div>job_id：'.$string["id"].'</div><div>集群ID：' .$string["paras"]["cluster_id"]. '</div><div>业务名称：' .$nick_name. '</div><div>shard_id：' . $string["paras"]["shard_id"]. '</div><div>存储节点个数：' .$string["paras"]["nodes"]. '个</div>'.$arr;
									}
									if($value2=='delete_node'){
										$shard_name=$this->getShardName($string['paras']['cluster_id'],$string['paras']['shard_id']);
										$res[$row]['object']=$string['paras']['nick_name'].'('.$shard_name.')';
										$res[$row]['list'] = '<div>job_id：'.$string["id"].'</div><div>集群ID：' .$string['paras']["cluster_id"]. '</div><div>业务名称：' .$string['paras']["nick_name"]. '</div><div>shard_id：' . $string['paras']["shard_id"]. '</div><div>IP：' .$string['paras']["hostaddr"]. '</div><div>端口：' .$string['paras']["port"]. '</div>';
									}

								}
							}
						}
						if($key2 == 'job_info'){
							if(!empty($value2)) {
								$res[$row]['info'] =$string['message'];
							}
						}
					}
				}
			}
			$sql_total="select count(id) as count from cluster_alarm_info where id is not null";
			if(!empty($username)){
				$sql_total .=" and  alarm_type='$username'";
			}
			if(!empty($status)){
				$sql_total .=" and  status ='$status'";
			}
			if(!empty($alarm_level)){
				$sql_total .=" and  alarm_level ='$alarm_level'";
			}
			$res_total=$this->Cluster_model->getList($sql_total);
		}else{
			$res=array();;
			$res_total=0;
		}
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
		print_r(json_encode($data));
	}
	public function update(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$id = $string['id'];
		$user_name = $string['user_name'];
		$status = $string['status'];
		//验证token
		$this->load->model('Login_model');
		$this->load->model('Cluster_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)){
			$sql="select count(id) as count from kunlun_user where name='$res_token';";
			$res=$this->Login_model->getList($sql);
			if(!empty($res)){
				if($res[0]['count']==0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				}else{
					$sql_update="update cluster_alarm_info set handle_time=now(),handler_name='$user_name',status='$status' where id='$id';";
					$res_update=$this->Cluster_model->updateList($sql_update);
					if($res_update==1){
						$data['code'] = 200;
						$data['message'] = '成功';
					}else{
						$data['code'] = 501;
						$data['message'] = '失败';
					}
					print_r(json_encode($data));
				}
			}
		}else{
			$data['code'] = 500;
			$data['message'] = 'token错误';
			print_r(json_encode($data));
		}
	}
	public function getClusterName($id){
		$sql="select name,nick_name from db_clusters where id='$id'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		if(!empty($res)) {
			return $res[0]['nick_name'];
		}else{
			return '';
		}
	}
	public function getShardName($db_cluster_id,$id){
		$sql="select name from shards where id='$id' and db_cluster_id='$db_cluster_id'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		if(!empty($res)) {
			return $res[0]['name'];
		}else{
			return '';
		}
	}
	public function getCompName($db_cluster_id,$id){
		$sql="select name from comp_nodes where id='$id' and db_cluster_id='$db_cluster_id'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		if(!empty($res)) {
			return $res[0]['name'];
		}else{
			return '';
		}
	}
}
