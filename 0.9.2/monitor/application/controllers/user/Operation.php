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
		header('Access-Control-Allow-Headers:x-requested-with,content-type,Token');//允许接受token
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
					$string=json_decode($res[$row]['job_info'],true);
					//输入信息
					if ($key2 == 'memo') {
						if(!empty($value2)) {
							$info=json_decode($value2,true);
							$res[$row]['info'] = $info['error_info'];
						}else{
							$res[$row]['info'] = '';
						}
					}
					if ($key2 == 'job_type') {
						if(!empty($value2)) {
							if($value2=='create_cluster'){
								$res[$row]['job_type'] = '新增集群';
								$ip='';
								if(!empty($string['paras']['machinelist'])){
									foreach ($string['paras']['machinelist'] as $host){
										if(!empty($host['hostaddr'])){
											$ip.=$host['hostaddr'].',';
										}else{
											$ip.=$host.',';
										}
									}
									$ip=rtrim($ip, ",");
								}
								if(!empty($string)) {
									$nick_name='';
									if(!empty($string['paras']['nick_name'])){
										$nick_name=$string['paras']['nick_name'];
									}else if(!empty($string['paras']['cluster_name'])){
										$nick_name=$string['paras']['cluster_name'];
									}
									$shards='';
									if(!empty($string['paras']['shards'])){
										$shards=$string['paras']['shards'];
									}
									$nodes='';
									if(!empty($string['paras']['nodes'])){
										$nodes=$string['paras']['nodes'];
									}
									$comps='';
									if(!empty($string['paras']['comps'])){
										$comps=$string['paras']['comps'];
									}
									$max_connections='';
									if(!empty($string['paras']['max_connections'])){
										$max_connections=$string['paras']['max_connections'];
									}
									$innodb_size='';
									if(!empty($string['paras']['innodb_size'])){
										$innodb_size=$string['paras']['innodb_size'];
									}
									$cpu_cores='';
									if(!empty($string['paras']['cpu_cores'])){
										$cpu_cores=$string['paras']['cpu_cores'];
									}
									$ha_mode='';
									if(!empty($string['paras']['ha_mode'])){
										$ha_mode=$string['paras']['ha_mode'];
									}
									$res[$row]['list'] = '<div>集群名称：' . $nick_name . '</div><div>shard总个数：' . $shards . '</div><div>每个shard含节点总个数：' .$nodes . '</div><div>计算节点总个数：' . $comps. '</div><div>每计算节点最大连接数：' . $max_connections . '</div><div>缓冲池大小：' . $innodb_size . '</div><div>cpu核数：' . $cpu_cores. '</div><div>复制模式：' . $ha_mode. '</div><div>选择计算机：' . $ip . '</div>';
									//$res[$row]['list']=array('shard总个数'=>$string['shards'],'每个shard含节点总个数'=>$string['nodes'],'计算节点总个数'=>$string['comps'],'每计算节点最大连接数'=>$string['max_connections'],'缓冲池大小'=>$string['innodb_size'],'cpu核数'=>$string['cpu_cores'],'ha_mode'=>$string['ha_mode'],'选择计算机'=>$ip);
								}else{
									$res[$row]['list'] = '<div>集群名称：</div><div>shard总个数：</div><div>每个shard含节点总个数：</div><div>计算节点总个数：</div><div>每计算节点最大连接数：</div><div>缓冲池大小：</div><div>cpu核数：</div><div>复制模式：</div><div>选择计算机：</div>';
								}
							}
							if($value2=='delete_cluster'){
								$res[$row]['job_type'] = '删除集群';
								if(!empty($string)){
									$nick_name='';
									if(!empty($string['paras']['nick_name'])){
										$nick_name=$string['paras']['nick_name'];
									}else if(!empty($string['paras']['cluster_name'])){
										$nick_name=$string['paras']['cluster_name'];
									}
									$res[$row]['list']='<div>集群名称：'.$nick_name.'</div>';
								}else{
									$res[$row]['list']='<div>集群名称：</div>';
								}
							}
							if($value2=='add_shards'){
								$res[$row]['job_type'] = '新增shards';
								$ip='';
								if(!empty($string['paras']['machinelist'])) {
									foreach ($string['paras']['machinelist'] as $host) {
										$ip .= $host['hostaddr'] . ',';
									}
								}
								$ip=rtrim($ip, ",");
								if(!empty($string)){
									$nick_name='';
									if(!empty($string['paras']['nick_name'])){
										$nick_name=$string['paras']['nick_name'];
									}else if(!empty($string['paras']['cluster_name'])){
										$nick_name=$string['paras']['cluster_name'];
									}
									$shards='';
									if(!empty($string['paras']['shards'])){
										$shards=$string['paras']['shards'];
									}
									$res[$row]['list']='<div>集群名称：'.$nick_name.'</div><div>选择计算机：'.$ip.'</div><div>shard个数：'.$shards.'</div>';
									//$res[$row]['list']=array('集群名称'=>$string['cluster_name'],'选择计算机'=>$ip,'shard个数'=>$string['shards']);
								}else{
									$res[$row]['list']='<div>集群名称：</div><div>选择计算机：'.$ip.'</div><div>shard个数：</div>';
								}
							}
							if($value2=='delete_shard'){
								$res[$row]['job_type'] = '删除shard';
								if(!empty($string)) {
									$nick_name='';
									if(!empty($string['paras']['nick_name'])){
										$nick_name=$string['paras']['nick_name'];
									}else if(!empty($string['paras']['cluster_name'])){
										$nick_name=$string['paras']['cluster_name'];
									}
									$shard_name='';
									if(!empty($string['paras']['shard_name'])){
										$shard_name=$string['paras']['shard_name'];
									}
									$res[$row]['list'] = '<div>集群名称：' . $nick_name . '</div><div>shard名称：' . $shard_name . '</div>';
									//$res[$row]['list']=array('集群名称'=>$string['cluster_name'],'shard名称'=>$string['shard_name']);
								}else{
									$res[$row]['list'] = '<div>集群名称：</div><div>shard名称：</div>';
								}
							}
							if($value2=='backup_cluster'){
								$res[$row]['job_type'] = '备份集群';
								if(!empty($string)) {
									$nick_name='';
									if(!empty($string['paras']['nick_name'])){
										$nick_name=$string['paras']['nick_name'];
									}else if(!empty($string['paras']['backup_cluster_name'])){
										$nick_name=$string['paras']['backup_cluster_name'];
									}
									$res[$row]['list'] = '<div>原集群名称：' . $nick_name . '</div>';
									//$res[$row]['list']=array('原集群名称'=>$string['backup_cluster_name']);
								}else{
									$res[$row]['list'] = '<div>原集群名称：</div>';
								}
							}
							if($value2=='restore_new_cluster'){
								$res[$row]['job_type'] = '恢复集群';
								if(!empty($string)) {
									$backup_cluster_name='';
									if(!empty($string['paras']['backup_cluster_name'])){
										$backup_cluster_name=$string['paras']['backup_cluster_name'];
									}
									$nick_name='';
									if(!empty($string['paras']['nick_name'])){
										$nick_name=$string['paras']['nick_name'];
									}else if(!empty($string['paras']['cluster_name'])){
										$nick_name=$string['paras']['cluster_name'];
									}
									$res[$row]['list'] = '<div>备份集群名称：' . $backup_cluster_name. '</div><div>新集群名称：' .$nick_name. '</div>';
									//$res[$row]['list']=array('备份集群名称'=>$string['paras']['backup_cluster_name']);
								}else{
									$res[$row]['list'] = '<div>备份集群名称：</div><div>新集群名称：</div>';
								}
							}
							if($value2=='add_comps'){
								$res[$row]['job_type'] = '增加计算节点';
								$ip='';
								if(!empty($string['paras']['machinelist'])) {
									foreach ($string['paras']['machinelist'] as $host) {
										$ip .= $host['hostaddr'] . ',';
									}
								}
								$ip=rtrim($ip, ",");
								if(!empty($string)) {
									$nick_name='';
									if(!empty($string['paras']['nick_name'])){
										$nick_name=$string['paras']['nick_name'];
									}else if(!empty($string['paras']['cluster_name'])){
										$nick_name=$string['paras']['cluster_name'];
									}
									$comps='';
									if(!empty($string['paras']['comps'])){
										$comps=$string['paras']['comps'];
									}
									$res[$row]['list'] = '<div>集群名称：' . $nick_name . '</div><div>选择计算机：' . $ip . '</div><div>计算节点个数：' . $comps . '</div>';
									//$res[$row]['list']=array('集群名称'=>$string['cluster_name'],'选择计算机'=>$ip,'计算节点个数'=>$string['comps']);
								}else{
									$res[$row]['list'] = '<div>集群名称：</div><div>选择计算机：' . $ip . '</div><div>计算节点个数：</div>';
								}
							}
							if($value2=='delete_comp'){
								$res[$row]['job_type'] = '删除计算节点';
								if(!empty($string)) {
									$nick_name='';
									if(!empty($string['paras']['nick_name'])){
										$nick_name=$string['paras']['nick_name'];
									}else if(!empty($string['paras']['cluster_name'])){
										$nick_name=$string['paras']['cluster_name'];
									}
									$comp_name='';
									if(!empty($string['paras']['comp_name'])){
										$comp_name=$string['paras']['comp_name'];
									}
									$res[$row]['list'] = '<div>集群名称：' . $nick_name . '</div><div>计算节点名称：' . $comp_name . '</div>';
								}else{
									$res[$row]['list'] = '<div>集群名称：</div><div>计算节点名称：</div>';
								}
							}
							if($value2=='add_nodes'){
								$res[$row]['job_type'] = '增加存储节点';
								$ip='';
								if(!empty($string['paras']['machinelist'])) {
									foreach ($string['paras']['machinelist'] as $host) {
										$ip .= $host['hostaddr'] . ',';
									}
								}
								$ip=rtrim($ip, ",");
								if(!empty($string)) {
									$nick_name='';
									if(!empty($string['paras']['nick_name'])){
										$nick_name=$string['paras']['nick_name'];
									}else if(!empty($string['paras']['cluster_name'])){
										$nick_name=$string['paras']['cluster_name'];
									}
									$shard_name='';
									if(!empty($string['paras']['shard_name'] )){
										$shard_name=$string['paras']['shard_name'] ;
									}else{
										$shard_name='';
									}
									$nodes='';
									if(!empty($string['paras']['nodes'])){
										$nodes=$string['paras']['nodes'];
									}
									$res[$row]['list'] = '<div>集群名称：' . $nick_name . '</div><div>选择计算机：' . $ip . '</div><div>shard名称：' .$shard_name. '</div><div>节点个数：' .$nodes  . '</div>';
									//$res[$row]['list']=array('集群名称'=>$string['cluster_name'],'选择计算机'=>$ip,'shard名称'=>$string['shard_name'],'节点个数'=>$string['nodes']);
								}else{
									$res[$row]['list'] = '<div>集群名称：</div><div>选择计算机：' . $ip . '</div><div>shard名称：</div><div>节点个数：</div>';
								}
							}
							if($value2=='delete_node'){
								$res[$row]['job_type'] = '删除存储节点';
								if(!empty($string)) {
									$nick_name='';
									if(!empty($string['paras']['nick_name'])){
										$nick_name=$string['paras']['nick_name'];
									}else if(!empty($string['paras']['cluster_name'])){
										$nick_name=$string['paras']['cluster_name'];
									}
									$shard_name='';
									if(!empty($string['paras']['shard_name'])){
										$shard_name=$string['paras']['shard_name'];
									}
									$hostaddr='';
									if(!empty($string['paras']['hostaddr'])){
										$hostaddr=$string['paras']['hostaddr'];
									}
									$port='';
									if(!empty($string['paras']['port'])){
										$port=$string['paras']['port'];
									}
									$res[$row]['list'] = '<div>集群名称：' . $nick_name. '</div><div>shard名称：' . $shard_name . '</div><div>ip：' . $hostaddr . '</div><div>端口：' . $port. '</div>';
									//$res[$row]['list']=array('集群名称'=>$string['cluster_name'],'shard名称'=>$string['shard_name'],'ip'=>$string['ip'],'端口'=>$string['port']);
								}else{
									$res[$row]['list']='<div>集群名称：</div><div>shard名称：</div><div>ip：</div><div>端口：</div>';
								}
							}
							if($value2=='mysqld_exporter'){
								$res[$row]['job_type'] = '监控存储节点';
								if(!empty($string)) {
									$hostaddr='';
									if(!empty($string['paras']['hostaddr'])){
										$hostaddr=$string['paras']['hostaddr'];
									}
									$port='';
									if(!empty($string['paras']['port'])){
										$port=$string['paras']['port'];
									}
									$res[$row]['list'] = '<div>ip：' . $hostaddr . '</div><div>端口：' .$port  . '</div>';
									//$res[$row]['list']=array('ip'=>$string['ip'],'端口'=>$string['port']);
								}else{
									$res[$row]['list'] = '<div>ip：</div><div>端口：</div>';
								}
							}
							if($value2=='postgres_exporter'){
								$res[$row]['job_type'] = '监控计算节点';
								if(!empty($string)) {
									$hostaddr='';
									if(!empty($string['paras']['hostaddr'])){
										$hostaddr=$string['paras']['hostaddr'];
									}
									$port='';
									if(!empty($string['paras']['port'])){
										$port=$string['paras']['port'];
									}
									$res[$row]['list'] = '<div>ip：' .$hostaddr . '</div><div>端口：' .$port . '</div>';
									//$res[$row]['list']=array('ip'=>$string['ip'],'端口'=>$string['port']);
								}else{
									$res[$row]['list'] = '<div>ip：</div><div>端口：</div>';
								}
							}
							if($value2=='update_prometheus'){
								$res[$row]['job_type'] = '重置prometheus';
							}
							if($value2=='create_machine'){
								$res[$row]['job_type'] = '新增计算机';
								if(!empty($string)) {
									$hostaddr='';
									if(!empty($string['paras']['hostaddr'])){
										$hostaddr=$string['paras']['hostaddr'];
									}
									$rack_id='';
									if(!empty($string['paras']['rack_id'])){
										$rack_id=$string['paras']['rack_id'];
									}
									$datadir='';
									if(!empty($string['paras']['datadir'])){
										$datadir= $string['paras']['datadir'];
									}
									$logdir='';
									if(!empty($string['paras']['logdir'])){
										$logdir= $string['paras']['logdir'];
									}
									$wal_log_dir='';
									if(!empty($string['paras']['wal_log_dir'])){
										$wal_log_dir= $string['paras']['wal_log_dir'];
									}
									$comp_datadir='';
									if(!empty($string['paras']['comp_datadir'])){
										$comp_datadir= $string['paras']['comp_datadir'];
									}

									$res[$row]['list'] = '<div>ip：' . $hostaddr. '</div><div>机架编号：' . $rack_id . '</div><div>存储节点数据目录：' . $datadir . '</div><div>日志目录：' . $logdir . '</div><div>wal日志目录：' . $wal_log_dir . '</div><div>计算节点数据目录：' . $comp_datadir . '</div>';
								}else{
									$res[$row]['list']='<div>ip：</div><div>机架编号：</div><div>存储节点数据目录：</div><div>日志目录：</div><div>wal日志目录：</div><div>计算节点数据目录：</div>';
								}
							}
							if($value2=='update_machine'){
								$res[$row]['job_type'] = '编辑计算机';
								if(!empty($string)) {
									$hostaddr='';
									if(!empty($string['paras']['hostaddr'])){
										$hostaddr=$string['paras']['hostaddr'];
									}
									$rack_id='';
									if(!empty($string['paras']['rack_id'])){
										$rack_id=$string['paras']['rack_id'];
									}
									$datadir='';
									if(!empty($string['paras']['datadir'])){
										$datadir= $string['paras']['datadir'];
									}
									$logdir='';
									if(!empty($string['paras']['logdir'])){
										$logdir= $string['paras']['logdir'];
									}
									$wal_log_dir='';
									if(!empty($string['paras']['wal_log_dir'])){
										$wal_log_dir= $string['paras']['wal_log_dir'];
									}
									$comp_datadir='';
									if(!empty($string['paras']['comp_datadir'])){
										$comp_datadir= $string['paras']['comp_datadir'];
									}
									$res[$row]['list'] = '<div>ip：' . $hostaddr. '</div><div>机架编号：' . $rack_id . '</div><div>存储节点数据目录：' . $datadir . '</div><div>日志目录：' . $logdir . '</div><div>wal日志目录：' . $wal_log_dir . '</div><div>计算节点数据目录：' . $comp_datadir . '</div>';
								}else{
									$res[$row]['list']='<div>ip：</div><div>机架编号：</div><div>存储节点数据目录：</div><div>日志目录：</div><div>wal日志目录：</div><div>计算节点数据目录：</div>';
								}
							}
							if($value2=='delete_machine'){
								$res[$row]['job_type'] = '删除计算机';
								if(!empty($string)) {
									$hostaddr='';
									if(!empty($string['paras']['hostaddr'])){
										$hostaddr=$string['paras']['hostaddr'];
									}
									$res[$row]['list'] = '<div>ip：' . $hostaddr. '</div>';
								}else{
									$res[$row]['list'] = '<div>ip：</div>';
								}
							}
							if($value2=='control_instance'){
								$res[$row]['job_type'] = '控制实例';
								if(!empty($string)) {
									$control='';
									if(!empty($string['paras']['control'])){
										$control=$string['paras']['control'] ;
									}
									$hostaddr='';
									if(!empty($string['paras']['hostaddr'])){
										$hostaddr=$string['paras']['hostaddr'];
									}
									$port='';
									if(!empty($string['paras']['port'])){
										$port=$string['paras']['port'];
									}
									$res[$row]['list'] = '<div>操作：' .$control . '</div><div>ip：' .$hostaddr  . '</div><div>端口：' . $port . '</div>';
									//$res[$row]['list']=array('操作'=>$string['control'],'ip'=>$string['ip'],'端口'=>$string['port']);
								}else{
									$res[$row]['list']='<div>操作：</div><div>ip：</div><div>端口：</div>';
								}
							}
							if($value2=='delete_backup_storage'){
								$res[$row]['job_type'] = '删除备份存储目标';
								if(!empty($string)) {
									$name='';
									if(!empty($string['paras']['name'])){
										$name=$string['paras']['name'] ;
									}

									$res[$row]['list'] = '<div>目标名称：' .$name . '</div>';
									//$res[$row]['list']=array('操作'=>$string['control'],'ip'=>$string['ip'],'端口'=>$string['port']);
								}else{
									$res[$row]['list']='<div>目标名称：</div>';
								}
							}
							if($value2=='create_backup_storage'){
								$res[$row]['job_type'] = '新增备份存储目标';
								if(!empty($string)) {
									$name='';
									if(!empty($string['paras']['name'])){
										$name=$string['paras']['name'] ;
									}
									$hostaddr='';
									if(!empty($string['paras']['hostaddr'])){
										$hostaddr=$string['paras']['hostaddr'];
									}
									$port='';
									if(!empty($string['paras']['port'])){
										$port=$string['paras']['port'];
									}
									$stype='';
									if(!empty($string['paras']['stype'])){
										$stype=$string['paras']['stype'];
									}
									$res[$row]['list'] = '<div>目标名称：' .$name . '</div><div>目标类型：' .$stype  . '</div><div>ip：' .$hostaddr  . '</div><div>端口：' . $port . '</div>';
									//$res[$row]['list']=array('操作'=>$string['control'],'ip'=>$string['ip'],'端口'=>$string['port']);
								}else{
									$res[$row]['list']='<div>目标名称：</div><div>目标类型：</div><div>ip：</div><div>端口：</div>';
								}
							}
							if($value2=='update_backup_storage'){
								$res[$row]['job_type'] = '编辑备份存储目标';
								if(!empty($string)) {
									$name='';
									if(!empty($string['paras']['name'])){
										$name=$string['paras']['name'] ;
									}
									$hostaddr='';
									if(!empty($string['paras']['hostaddr'])){
										$hostaddr=$string['paras']['hostaddr'];
									}
									$port='';
									if(!empty($string['paras']['port'])){
										$port=$string['paras']['port'];
									}
									$stype='';
									if(!empty($string['paras']['stype'])){
										$stype=$string['paras']['stype'];
									}
									$res[$row]['list'] = '<div>目标名称：' .$name . '</div><div>目标类型：' .$stype  . '</div><div>ip：' .$hostaddr  . '</div><div>端口：' . $port . '</div>';
									//$res[$row]['list']=array('操作'=>$string['control'],'ip'=>$string['ip'],'端口'=>$string['port']);
								}else{
									$res[$row]['list']='<div>目标名称：</div><div>目标类型：</div><div>ip：</div><div>端口：</div>';
								}
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
	public function getNickName($cluster_name){
		$sql="select nick_name from db_clusters where name='$cluster_name' ";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		return $res[0]['nick_name'];
	}
	/*public function operationList(){
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
	}*/
}
