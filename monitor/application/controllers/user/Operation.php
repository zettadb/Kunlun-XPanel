<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operation extends CI_Controller {

	public function __construct()
	{
		session_start();
		parent::__construct();
		header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
		header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
		header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
		header('Access-Control-Allow-Headers:x-requested-with,content-type,accessToken');//允许接受token
		header('Content-Type: application/json;charset=utf-8');
		//header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
		$this->key=$this->config->item('key');
	}

	public function getOperationList(){
		//GET请求
		$serve=$_SERVER['QUERY_STRING'];
		$string=preg_split('/[=&]/',$serve);
		$arr=array();
		for($i=0;$i<count($string);$i+=2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$pageNo=$arr['pageNo'];
		$pageSize=$arr['pageSize'];
		$username=$arr['username'];
		$start=($pageNo - 1) * $pageSize;
		$this->load->model('Cluster_model');
		//获取任务信息
		$sql="select job_type,when_started,when_ended,memo,status,job_info,user_name from cluster_general_job_log";
		if($username=='super_dba'){
			$sql.="";
		}else{
			$sql.=" where user_name='$username'";
		}
		$sql.="  order by id desc limit $pageSize offset $start";
		$res = $this->Cluster_model->getList($sql);
		$total_sql="select count(id) as count from cluster_general_job_log";
		if($username=='super_dba'){
			$total_sql.="";
		}else{
			$total_sql.=" where user_name='$username'";
		}
		$res_total=$this->Cluster_model->getList($total_sql);
		if($res===false){
			$res=array();
		}else{
			foreach ($res as $row=>$value){
				foreach ($value as $key2 => $value2) {
					$string=json_decode($res[$row]['memo'],true);
					//输入信息
//					if ($key2 == 'memo') {
//						if(!empty($value2)) {
//							print_r($value2['cluster_name']);exit;
//							$res[$row]['username'] = $value2;
//						}else{
//							$res[$row]['username'] = '';
//						}
//					}
					if ($key2 == 'job_type') {
						if(!empty($value2)) {
							if($value2=='create_cluster'){
								$res[$row]['job_type'] = '新增集群';
								$ip='';
								if(!empty($string['machinelist'])){
									foreach ($string['machinelist'] as $host){
										$ip.=$host['hostaddr'].',';
									}
									$ip=rtrim($ip, ",");
								}
								$res[$row]['list']='<div>shard总个数：'.$string['shards'].'</div><div>每个shard含节点总个数：'.$string['nodes'].'</div><div>计算节点总个数：'.$string['comps'].'</div><div>每计算节点最大连接数：'.$string['max_connections'].'</div><div>缓冲池大小：'.$string['innodb_size'].'</div><div>cpu核数：'.$string['cpu_cores'].'</div><div>复制模式：'.$string['ha_mode'].'</div><div>选择计算机：'.$ip.'</div>';
								//$res[$row]['list']=array('shard总个数'=>$string['shards'],'每个shard含节点总个数'=>$string['nodes'],'计算节点总个数'=>$string['comps'],'每计算节点最大连接数'=>$string['max_connections'],'缓冲池大小'=>$string['innodb_size'],'cpu核数'=>$string['cpu_cores'],'ha_mode'=>$string['ha_mode'],'选择计算机'=>$ip);
							}
							if($value2=='delete_cluster'){
								$res[$row]['job_type'] = '删除集群';
								$res[$row]['list']='<div>集群名称：'.$string['cluster_name'].'</div>';
								//$res[$row]['list']=array('集群名称'=>$string['cluster_name']);

							}
							if($value2=='add_shards'){
								$res[$row]['job_type'] = '新增shards';
								$ip='';
								if(!empty($string['machinelist'])) {
									foreach ($string['machinelist'] as $host) {
										$ip .= $host['hostaddr'] . ',';
									}
								}
								$ip=rtrim($ip, ",");
								$res[$row]['list']='<div>集群名称：'.$string['cluster_name'].'</div><div>选择计算机：'.$ip.'</div><div>shard个数：'.$string['shards'].'</div>';
								//$res[$row]['list']=array('集群名称'=>$string['cluster_name'],'选择计算机'=>$ip,'shard个数'=>$string['shards']);
							}
							if($value2=='delete_shard'){
								$res[$row]['job_type'] = '删除shard';
								$res[$row]['list']='<div>集群名称：'.$string['cluster_name'].'</div><div>shard名称：'.$string['shard_name'].'</div>';
								//$res[$row]['list']=array('集群名称'=>$string['cluster_name'],'shard名称'=>$string['shard_name']);
							}
							if($value2=='backup_cluster'){
								$res[$row]['job_type'] = '备份集群';
								$res[$row]['list']='<div>原集群名称：'.$string['backup_cluster_name'].'</div>';
								//$res[$row]['list']=array('原集群名称'=>$string['backup_cluster_name']);
							}
							if($value2=='restore_new_cluster'){
								$res[$row]['job_type'] = '恢复集群';
								$res[$row]['list']='<div>备份集群名称：'.$string['backup_cluster_name'].'</div>';
								//$res[$row]['list']=array('备份集群名称'=>$string['backup_cluster_name']);
							}
							if($value2=='add_comps'){
								$res[$row]['job_type'] = '增加计算节点';
								$ip='';
								if(!empty($string['machinelist'])) {
									foreach ($string['machinelist'] as $host) {
										$ip .= $host['hostaddr'] . ',';
									}
								}
								$ip=rtrim($ip, ",");
								$res[$row]['list']='<div>集群名称：'.$string['cluster_name'].'</div><div>选择计算机：'.$ip.'</div><div>计算节点个数：'.$string['comps'].'</div>';
								//$res[$row]['list']=array('集群名称'=>$string['cluster_name'],'选择计算机'=>$ip,'计算节点个数'=>$string['comps']);

							}
							if($value2=='delete_comp'){
								$res[$row]['job_type'] = '删除计算节点';
							}
							if($value2=='add_nodes'){
								$res[$row]['job_type'] = '增加存储节点';
								$ip='';
								if(!empty($string['machinelist'])) {
									foreach ($string['machinelist'] as $host) {
										$ip .= $host['hostaddr'] . ',';
									}
								}
								$ip=rtrim($ip, ",");
								$res[$row]['list']='<div>集群名称：'.$string['cluster_name'].'</div><div>选择计算机：'.$ip.'</div><div>shard名称：'.$string['shard_name'].'</div><div>节点个数：'.$string['nodes'].'</div>';
								//$res[$row]['list']=array('集群名称'=>$string['cluster_name'],'选择计算机'=>$ip,'shard名称'=>$string['shard_name'],'节点个数'=>$string['nodes']);

							}
							if($value2=='delete_node'){
								$res[$row]['job_type'] = '删除存储节点';
								$res[$row]['list']='<div>集群名称：'.$string['cluster_name'].'</div><div>shard名称：'.$string['shard_name'].'</div><div>ip：'.$string['ip'].'</div><div>端口：'.$string['port'].'</div>';
								//$res[$row]['list']=array('集群名称'=>$string['cluster_name'],'shard名称'=>$string['shard_name'],'ip'=>$string['ip'],'端口'=>$string['port']);
							}
							if($value2=='mysqld_exporter'){
								$res[$row]['job_type'] = '监控存储节点';
								$res[$row]['list']='<div>ip：'.$string['ip'].'</div><div>端口：'.$string['port'].'</div>';
								//$res[$row]['list']=array('ip'=>$string['ip'],'端口'=>$string['port']);
							}
							if($value2=='postgres_exporter'){
								$res[$row]['job_type'] = '监控计算节点';
								$res[$row]['list']='<div>ip：'.$string['ip'].'</div><div>端口：'.$string['port'].'</div>';
								//$res[$row]['list']=array('ip'=>$string['ip'],'端口'=>$string['port']);
							}
							if($value2=='update_prometheus'){
								$res[$row]['job_type'] = '重置prometheus';
							}
							if($value2=='create_machine'){
								$res[$row]['job_type'] = '新增计算机';
							}
							if($value2=='update_machine'){
								$res[$row]['job_type'] = '编辑计算机';
							}
							if($value2=='delete_machine'){
								$res[$row]['job_type'] = '删除计算机';
							}
							if($value2=='control_instance'){
								$res[$row]['job_type'] = '控制实例';
								$res[$row]['list']='<div>操作：'.$string['control'].'</div><div>ip：'.$string['ip'].'</div><div>端口：'.$string['port'].'</div>';
								//$res[$row]['list']=array('操作'=>$string['control'],'ip'=>$string['ip'],'端口'=>$string['port']);
							}

						}else{
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
	public function operationList(){
		$page = $this->input->get('page');
		$keyword = $this->input->get('keyword');
		$status = $this->input->get('status');
		$this->load->model('Main_model');
		if(is_null($page)||empty($page)) {
			$page=1;
		}
		//得到任务总数
		$sql="select count(0) as count from operation_record ";
		$total = $this->Main_model->getListTotal($sql,$this->user_per_page);
		$data['page'] = $page>$total?$total:$page;
		$data['total']=$total;
		$data['keyword']=$keyword;
		$data['status'] = $status;
		$data['pagesize']=$this->user_per_page;
		$this->load->view('app/operation_list',$data);
	}
}
