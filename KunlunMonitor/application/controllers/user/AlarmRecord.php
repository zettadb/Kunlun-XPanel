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
		$start=($pageNo - 1) * $pageSize;
		$job_type=$this->job_type;
		//print_r($pageSize);exit;
		//获取数据
		$this->load->model('Cluster_model');
		$sql="select id,job_type,user_name,memo,status from cluster_general_job_log where user_name!='internal_user'and status='failed' and job_type in('create_cluster','delete_cluster','add_shards','delete_shard','add_comps','delete_comp','add_nodes','delete_node','manual_switch','rebuild_node','cluster_restore','expand_cluster','manual_backup_cluster')";
		if(!empty($username)){
			$sql .=" and  job_type ='$username'";
		}
		$sql .=" order by id desc limit ".$pageSize." offset ".$start;
		//print_r($sql);exit;
		$res=$this->Cluster_model->getList($sql);
		if($res===false){
			$res=array();
		}else{
			foreach ($res as $row=>$value){
				foreach ($value as $key2 => $value2) {
					if ($key2 == 'job_type') {
						if(!empty($value2)) {
							foreach($job_type as $k2 => $v2){
								if($value2== $v2['code']){
									$res[$row]['job_type'] = $v2['name'];
									break;
								}else{
									$res[$row]['job_type'] = $value2;
								}
							}
						}
					}
					//输入信息
					if ($key2 == 'memo') {
						if(!empty($value2)) {
							$info=json_decode($value2,true);
							if(!empty($info)) {
								// if(!empty($info['cluster_id'])){
								// 	$res[$row]['cluster_id'] = $info['cluster_id'];
								// }else{
								// 	if(!empty($string['paras']['cluster_id'])){
								// 		$res[$row]['cluster_id'] =$string['paras']['cluster_id'];
								// 	}else{
								// 		$res[$row]['cluster_id'] ='';
								// 	}
								// }
								if(!empty($info['error_info'])){
									if($res[$row]['status']=='ongoing'){
										$res[$row]['info'] = '';
									}else{
										$res[$row]['info'] = $info['error_info'];
										//print_r($info['error_info']);exit;
									}
								}else{
									$res[$row]['info'] = '';
								}
							}
						}else{
							$res[$row]['info'] = '';
							// if(!empty($string['paras']['cluster_id'])){
							// 	$res[$row]['cluster_id'] =$string['paras']['cluster_id'];
							// }else{
							// 	$res[$row]['cluster_id'] ='';
							// }
						}
					}
				}
			}
		}
		$sql_total="select count(id) as count from cluster_general_job_log where user_name!='internal_user'and status='failed' and job_type in('create_cluster','delete_cluster','add_shards','delete_shard','add_comps','delete_comp','add_nodes','delete_node','manual_switch','rebuild_node','cluster_restore','expand_cluster','manual_backup_cluster')";
		if(!empty($username)){
			$sql_total .=" and  job_type ='$username'";
		}
		$res_total=$this->Cluster_model->getList($sql_total);
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
		print_r(json_encode($data));
	}
	
}
