<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cluster extends CI_Controller {
	public function __construct(){
		parent::__construct();
		//打开重定向
		header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
		header('Access-Control-Allow-Headers: Content-Type,Content-Length, Origin'); // 设置允许自定义请求头的字段
		header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
		header('Access-Control-Allow-Headers:x-requested-with,content-type,accessToken');//允许接受token
		header('Content-Type: application/json;charset=utf-8');
		$this->key=$this->config->item('key');
		$this->ver=$this->config->item('ver');
		$this->post_url=$this->config->item('post_url');
	}
	public function createCluster(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$job_type='create_cluster';
		$user_name=$string['username'];
		$shards_count = (string)$string['shards_count'];
		$snode_count = $string['snode_count'];
		$comp_count = (string)$string['comp_count'];
		$per_computing_node_max_mem_size = $string['per_computing_node_max_mem_size'];
		$max_connections = $string['max_connections'];
		$buffer_pool = $string['buffer_pool'];
		$per_computing_node_cpu_cores = $string['per_computing_node_cpu_cores'];
		$ha_mode = $string['ha_mode'];
		$machinelist = $string['machinelist'];
		$nick_name = $string['cluster_name'];
		$uuid = $string['uuid'];
		if(empty($per_computing_node_max_mem_size)){
			$per_computing_node_max_mem_size='0';
		}
		if(empty($max_connections)){
			$max_connections='0';
		}
		//调接口
		$this->load->model('Cluster_model');
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'shards'=>$shards_count,
			'nodes'=>$snode_count,
			'comps'=>$comp_count,
			'max_storage_size'=>$per_computing_node_max_mem_size,
			'max_connections'=>$max_connections,
			'innodb_size'=>$buffer_pool,
			'cpu_cores'=>$per_computing_node_cpu_cores,
			'ha_mode'=>$ha_mode,
			'user_name'=>$user_name,
			'machinelist'=>$machinelist,
			'nick_name'=>$nick_name
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		//print_r($post_data);exit;
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		if(!empty($post_arr)){
			if($post_arr['result']=='accept') {
					$data['code'] = 200;
					$data['message'] = '正在新增';
			}elseif($post_arr['result']=='busy'){
				$data['code'] = 501;
				$data['message'] = '系统正在操作中，请等待一会！';
			}else{
				$data['code'] = 500;
				$data['message'] = $post_arr['message'];
			}
		}
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}

	public function clusterList(){
		//GET请求
		$serve=$_SERVER['QUERY_STRING'];
		$string=preg_split('/[=&]/',$serve);
		$arr=array();
		for($i=0;$i<count($string);$i+=2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$pageNo=$arr['pageNo'];
		$pageSize=$arr['pageSize'];
		$username=$arr['name'];
		$effectCluster=$arr['effectCluster'];
		$apply_all_cluster=$arr['apply_all_cluster'];
		$start=($pageNo - 1) * $pageSize;
		//获取用户数据
		if($apply_all_cluster==1){
			if(empty($effectCluster)){
				$sql="select id,name,when_created,nick_name from db_clusters ";
				if(!empty($username)){
					$sql .=" where nick_name like '%$username%'";
				}
			}
		}
		if($apply_all_cluster==2){
			if(empty($effectCluster)){
				$effectCluster='NULL';
			}
			if(strpos($effectCluster,',')==false){
				$sql="select id,name,when_created,nick_name from db_clusters where id=$effectCluster";
			}else{
				$sql="select id,name,when_created,nick_name from db_clusters where id in ($effectCluster) ";
			}
			if(!empty($username)){
				$sql .=" and nick_name like '%$username%'";
			}
		}
		$sql .=" order by id desc limit ".$pageSize." offset ".$start;
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		//total
		if($apply_all_cluster==1){
			if(empty($effectCluster)){
				$total_sql="select count(id) as count from db_clusters ";
				if(!empty($username)){
					$total_sql .=" where name like '%$username%'";
				}
			}
		}
		if($apply_all_cluster==2){
			if(strpos($effectCluster,',')==false){
				$total_sql="select count(id) as count from db_clusters where id=$effectCluster";
			}else{
				$total_sql="select count(id) as count from db_clusters where id in ($effectCluster) ";
			}
			if(!empty($username)){
				$total_sql .=" and nick_name like '%$username%'";
			}
		}
		$res_total=$this->Cluster_model->getList($total_sql);
		if($res===false){
			$res=array();
		}else{
			foreach ($res as $row=>$value){
				foreach ($value as $key2 => $value2) {
					//shard数
					if ($key2 == 'id') {
						if(!empty($value2)) {
							$shardtotal = $this->getThisShards($value2);
							$comptotal= $this->getThisComps($value2);
							//$nodetotal= $this->getThisNodes($value2);
							$res[$row]['shardtotal'] = $shardtotal['nodedetail'];
							$res[$row]['comptotal'] = $comptotal;
							//$res[$row]['nodetotal'] = $nodetotal;
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
	public function delCluster(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$name = $string['name'];
		$job_type='delete_cluster';
		$user_name=$string['username'];
		$this->load->model('Cluster_model');
		$uuid=$this->Cluster_model->create_uuid();
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'cluster_name'=>$name,
			'user_name'=>$user_name
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		if(!empty($post_arr)){
			if($post_arr['result']=='accept'){
				$data['code'] = 200;
				$data['message'] = '正在删除';
			}elseif($post_arr['result']=='busy'){
				$data['code'] = 501;
				$data['message'] = '系统正在操作中，请等待一会！';
			}else{
				$data['code'] = 500;
				$data['message'] = $post_arr['message'];
			}
		}
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}

	public function backUpCluster(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$job_type='backup_cluster';
		$cluster_name=$string['name'];
		$user_name=$string['username'];
		$this->load->model('Cluster_model');
		$uuid=$this->Cluster_model->create_uuid();
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'backup_cluster_name'=>$cluster_name,
			'user_name'=>$user_name
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		if(!empty($post_arr)){
			if($post_arr['result']=='accept'){
				$data['code'] = 200;
				$data['message'] = '正在备份';
			}elseif($post_arr['result']=='busy'){
				$data['code'] = 501;
				$data['message'] = '系统正在操作中，请等待一会！';
			}else{
				$data['code'] = 500;
				$data['message'] = $post_arr['message'];
			}
		}
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public  function getBackUpList(){
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

		//获取集群信息
		$sql="select cluster_id,start_ts,end_ts from cluster_backups ";
		$sql .=" order by id desc limit ".$pageSize." offset ".$start;
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		$total_sql="select count(id) as count from cluster_backups ";
		$res_total=$this->Cluster_model->getList($total_sql);
		if(!empty($res)){
			foreach ($res as $row=>$value){
				foreach ($value as $key2 => $value2) {
					//集群名称
					if ($key2 == 'cluster_id') {
						if(!empty($value2)) {
							$name = $this->getClusterName($value2);
							$res[$row]['cluster_name'] = $name;
						}else{
							$res[$row]['cluster_name'] = '';
						}
					}
				}
			}
		}else{
			$res=array();
		}
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
		print_r(json_encode($data));
	}
	public function getClusterName($id){
		$sql="select name from db_clusters where id='$id'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		return $res[0]['name'];
	}
	public function getShardName($db_cluster_id,$id){
		$sql="select name from shards where id='$id' and db_cluster_id='$db_cluster_id'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		return $res[0]['name'];
	}
	public function ifBackUp(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$cluster_id=$string['id'];
		$sql="select cluster_id,end_ts from cluster_backups where cluster_id='$cluster_id' order by end_ts asc limit 1";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		if(empty($res)){
			$data['code'] = 501;
			$data['message'] = '数据备份文件不存在。请配置相关备份策略，或手动发起备份！';
		}else{
			$data['code'] = 200;
			$data['list'] = $res;
		}
		print_r(json_encode($data));
	}
	public function restoreCluster(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$job_type='restore_new_cluster';
		$now=$string['now'];
		$user_name=$string['username'];
		$old_cluster_name=$string['old_cluster_name'];
		$machinelist=$string['machinelist'];
		$this->load->model('Cluster_model');
		$uuid=$this->Cluster_model->create_uuid();
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'timestamp'=>$now,
			'backup_cluster_name'=>$old_cluster_name,
			'user_name'=>$user_name,
			'machinelist'=>$machinelist
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		if(!empty($post_arr)){
			if($post_arr['result']=='accept'){
				$data['code'] = 200;
				$data['message'] = '正在恢复';
			}elseif($post_arr['result']=='busy'){
				$data['code'] = 501;
				$data['message'] = '系统正在操作中，请等待一会！';
			}else{
				$data['code'] = 500;
				$data['message'] = $post_arr['message'];
			}
		}
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	//获取信息
	public function getAllMachine(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		$sql="select id,hostaddr from server_nodes where hostaddr!='pseudo_server_useless'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = count($res);
		print_r(json_encode($data));
	}
	public function getShards(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$id = $string['id'];
		$sql="select id,name from shards where db_cluster_id='$id'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		$data['code'] = 200;
		$data['list'] = $res;
		print_r(json_encode($data));
	}
	public  function addShards(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$ver = $string['ver'];
		$cluster_name = $string['cluster_name'];
		$job_type = $string['node_type'];
		$shards_count = $string['shards'];
		$machinelist = $string['machinelist'];
		$user_name = $string['username'];
		$uuid = $string['job_id'];
		$this->load->model('Cluster_model');
		//$uuid = $this->Main_model->create_uuid();
		$meta_json=array(
			'ver'=>$ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'cluster_name'=>$cluster_name,
			'shards'=>$shards_count,
			'user_name'=>$user_name,
			'machinelist'=>$machinelist
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		//print_r($post_data);exit;
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		if(!empty($post_arr)){
			if($post_arr['result']=='accept'){
				$data['code'] = 200;
				$data['message'] = '正在新增分片...';
			}elseif($post_arr['result']=='busy'){
				$data['code'] = 501;
				$data['message'] = '系统正在操作中，请等待一会！';
			}else{
				$data['code'] = 500;
				$data['message'] = $post_arr['message'];
			}
		}
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public  function addComps(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$ver = $string['ver'];
		$cluster_name = $string['cluster_name'];
		$job_type = $string['node_type'];
		$shards_count = $string['shards'];
		$machinelist = $string['machinelist'];
		$user_name = $string['username'];
		$uuid = $string['job_id'];
		$this->load->model('Cluster_model');
		//$uuid = $this->Main_model->create_uuid();
		$meta_json=array(
			'ver'=>$ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'cluster_name'=>$cluster_name,
			'comps'=>$shards_count,
			'user_name'=>$user_name,
			'machinelist'=>$machinelist
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		//print_r($post_data);exit;
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		if(!empty($post_arr)){
			if($post_arr['result']=='accept'){
				$data['code'] = 200;
				$data['message'] = '正在新增计算节点...';
			}elseif($post_arr['result']=='busy'){
				$data['code'] = 501;
				$data['message'] = '系统正在操作中，请等待一会！';
			}else{
				$data['code'] = 500;
				$data['message'] = $post_arr['message'];
			}
		}
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public  function addNodes(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$ver = $string['ver'];
		$cluster_name = $string['cluster_name'];
		$job_type = $string['node_type'];
		$shards_count = $string['shards'];
		$machinelist = $string['machinelist'];
		$user_name = $string['username'];
		$uuid = $string['job_id'];
		$shard_name = $string['shard_name'];
		$this->load->model('Cluster_model');
		//$uuid = $this->Main_model->create_uuid();
		$meta_json=array(
			'ver'=>$ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'cluster_name'=>$cluster_name,
			'shard_name'=>$shard_name,
			'nodes'=>$shards_count,
			'user_name'=>$user_name,
			'machinelist'=>$machinelist
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		//print_r($post_data);exit;
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		//print_r($post_arr);exit;
		if(!empty($post_arr)){
			if($post_arr['result']=='accept'){
				$data['code'] = 200;
				$data['message'] = '正在新增存储节点...';
			}elseif($post_arr['result']=='busy'){
				$data['code'] = 501;
				$data['message'] = '系统正在操作中，请等待一会！';
			}else{
				$data['code'] = 500;
				$data['message'] = $post_arr['message'];
			}
		}
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public function getThisShards($id){
		$sql="select id,name from shards where db_cluster_id='$id'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		$count=count($res);
		if($count==1){
			$resnode=$this->getThisNodes($id,$res[0]['id']);
			$data['nodedetail']=$count.'个shard，'.$res[0]['name'].'('.$resnode.'个存储节点)';

		}elseif ($count>1){
			$node='';
			foreach ($res as $key){
				$resnode=$this->getThisNodes($id,$key['id']);
				$node.=$key['name'].'('.$resnode.'个存储节点)，';
			}
			$node=rtrim($node, "，");
			$data['nodedetail']=$count.'个shard，'.$node;
		}else{
			$data['nodedetail']='0个shard0个存储节点';
		}
		return $data;
	}
	public function getThisComps($id){
		$sql="select count(id) as count from comp_nodes where db_cluster_id='$id'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		return $res[0]['count'];
	}
	public function getThisNodes($id,$shard_id){
		$sql="select count(id) as count from shard_nodes where db_cluster_id='$id' and shard_id='$shard_id'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		return $res[0]['count'];
	}
	public  function getClusterNodesList(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$id= $string['id'];
		//获取cluster的name
		$this->load->model('Cluster_model');
		$sql="select name,nick_name from db_clusters where id='$id'";
		$res_name=$this->Cluster_model->getList($sql);
		$cluster_id='cluster';
		$cluster_name=$res_name[0]['name'];
		$cluster_text=$res_name[0]['nick_name'];
		$shards_id='shards';
		$shards_text='shards';
		$comp_id='comps';
		$comp_text='计算节点';
		$arr=array();
		//$nodes=array(array('id'=>$cluster_id, 'text'=>$cluster_text),array('id'=>$shards_id, 'text'=>$shards_text),array('id'=>$comp_id, 'text'=>$comp_text));
		$nodes=array(array('id'=>$cluster_id, 'text'=>$cluster_text,'data'=>array('cluster_name'=>$cluster_name)));
		$links=array();
//		$links=array(array('from'=>$cluster_id, 'to'=>$shards_id),array('from'=>$cluster_id, 'to'=>$comp_id));
		//获取存储节点数据
		$sql="select id,name from shards where db_cluster_id='$id'";
		$res=$this->Cluster_model->getList($sql);
		if($res!==false){
			foreach ($res as $row=>$value){
				foreach ($value as $key2 => $value2) {
					//shard名称
					if ($key2 == 'name') {
						if (!empty($value2)) {
							$shard_node_id='shard'.$res[$row]['id'];
							$shard_node=array('id'=>$shard_node_id, 'text'=>$value2,'data'=>array('name'=>'shard','cluster_name'=>$cluster_name,'nick_name'=>$cluster_text,'cluster_id'=>$id,'shard_id'=>$res[$row]['id']));
							$shard_link=array('from'=>$cluster_id, 'to'=>$shard_node_id);
							array_push($nodes,$shard_node);
							array_push($links,$shard_link);
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
		$sql="select id,name,port,hostaddr,cpu_cores,max_mem_MB,max_conns from comp_nodes where db_cluster_id='$id'";
		$this->load->model('Cluster_model');
		$res_comp=$this->Cluster_model->getList($sql);
		if($res_comp!==false){
			foreach ($res_comp as $row=>$value){
				foreach ($value as $key2 => $value2) {
					//存储节点
					if ($key2 == 'port') {
						if (!empty($value2)) {
							$shard_node_id='cnode'.$res_comp[$row]['id'];
							/*$shard_node_id='comp_name'.$res_comp[$row]['id'];
							$shard_comp_id='comp'.$res_comp[$row]['id'];
							$shard_node1=array('id'=>$shard_node_id, 'text'=>$res_comp[$row]['name']);
							$shard_link1=array('from'=>$comp_id, 'to'=>$shard_node_id);
							array_push($nodes,$shard_node1);
							array_push($links,$shard_link1);*/
							$shard_node=array('id'=>$shard_node_id, 'text'=>$value2,'data'=>array('cluster_name'=>$cluster_name,'nick_name'=>$cluster_text,'port'=>$value2,'hostaddr'=>$res_comp[$row]['hostaddr'],'cpu_cores'=>$res_comp[$row]['cpu_cores'],'max_mem_MB'=>$res_comp[$row]['max_mem_MB'],'max_conns'=>$res_comp[$row]['max_conns'],'name'=>'pgsql','comp'=>$res_comp[$row]['name']));
							$shard_link=array('from'=>$cluster_id, 'to'=>$shard_node_id,'text'=>$res_comp[$row]['hostaddr']);
							array_push($nodes,$shard_node);
							array_push($links,$shard_link);
						}
					}
				}
			}
		}
		array_unique($nodes,SORT_REGULAR);
		array_unique($links,SORT_REGULAR);
		$arr=array('rootId'=>'a','nodes'=>$nodes,'links'=>$links);
		$data['code'] = 200;
		$data['list'] = $arr;
		print_r(json_encode($data));
	}
	public function getShardNode($cluster_id,$id){
		$sql="select id,hostaddr,port,user_name,passwd,cpu_cores,initial_storage_GB,max_storage_GB,innodb_buffer_pool_MB,rocksdb_buffer_pool_MB from shard_nodes where shard_id='$id' and db_cluster_id='$cluster_id'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		return $res;
	}
	public  function postgresEnable(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$ver = $string['ver'];
		$job_type = $string['job_type'];
		$user_name = $string['username'];
		$uuid = $string['job_id'];
		$ip = $string['ip'];
		$port = $string['port'];
		$meta_json=array(
			'ver'=>$ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'user_name'=>$user_name,
			'ip'=>$ip,
			'port'=>$port
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		//print_r($post_data);exit;
		$this->load->model('Cluster_model');
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		//print_r($post_arr);exit;
		if(!empty($post_arr)){
			if($post_arr['result']=='accept'){
				$data['code'] = 200;
				$data['message'] = 'success';
			}elseif($post_arr['result']=='busy'){
				$data['code'] = 501;
				$data['message'] = '系统正在操作中，请等待一会！';
			}else{
				$data['code'] = 500;
				$data['message'] = $post_arr['message'];
			}
		}
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public  function prometheusEnable(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$ver = $string['ver'];
		$job_type = $string['job_type'];
		$user_name = $string['username'];
		$uuid = $string['job_id'];
		$meta_json=array(
			'ver'=>$ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'user_name'=>$user_name,
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		//print_r($post_data);exit;
		$this->load->model('Cluster_model');
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		if(!empty($post_arr)){
			if($post_arr['result']=='accept'){
				$data['code'] = 200;
				$data['message'] = 'success';
			}elseif($post_arr['result']=='busy'){
				$data['code'] = 501;
				$data['message'] = '系统正在操作中，请等待一会！';
			}else{
				$data['code'] = 500;
				$data['message'] = $post_arr['message'];
			}
		}
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public  function mysqlEnable(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$ver = $string['ver'];
		$job_type = $string['job_type'];
		$user_name = $string['username'];
		$uuid = $string['job_id'];
		$ip = $string['ip'];
		$port = $string['port'];
		$meta_json=array(
			'ver'=>$ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'user_name'=>$user_name,
			'ip'=>$ip,
			'port'=>$port
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		//print_r($post_data);exit;
		$this->load->model('Cluster_model');
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		if(!empty($post_arr)){
			if($post_arr['result']=='accept'){
				$data['code'] = 200;
				$data['message'] = 'success';
			}elseif($post_arr['result']=='busy'){
				$data['code'] = 501;
				$data['message'] = '系统正在操作中，请等待一会！';
			}else{
				$data['code'] = 500;
				$data['message'] = $post_arr['message'];
			}
		}
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public  function delShard(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$ver = $string['ver'];
		$job_type = $string['job_type'];
		$user_name = $string['username'];
		$uuid = $string['job_id'];
		$cluster_name = $string['cluster_name'];
		$shard_name = $string['shard_name'];
		$role=$this->getRoleID($token);
		if($role['code']!==200){
			$data['code'] =$role['code'];
			$data['message'] = $role['message'];
			print_r(json_encode($data));return;
		}
		$role_id=$role['role_id'];
		//检验这个用户是否有删除分片的权限
		$sql_role="select shard_drop_priv from kunlun_role_privilege where id='$role_id';";
		$res_role_priv=$this->Login_model->getList($sql_role);
		if($res_role_priv[0]['shard_drop_priv']!=='Y') {
			$data['code'] = 500;
			$data['message'] = '该账户不具备删除分片权限';
			print_r(json_encode($data));return;
		}
		$meta_json=array(
			'ver'=>$ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'user_name'=>$user_name,
			'cluster_name'=>$cluster_name,
			'shard_name'=>$shard_name,
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$this->load->model('Cluster_model');
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		if(!empty($post_arr)){
			if($post_arr['result']=='accept'){
				$data['code'] = 200;
				$data['message'] = 'success';
			}elseif($post_arr['result']=='busy'){
				$data['code'] = 501;
				$data['message'] = '系统正在操作中，请等待一会！';
			}else{
				$data['code'] = 500;
				$data['message'] = $post_arr['message'];
			}
		}
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public  function delComp(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$ver = $string['ver'];
		$job_type = $string['job_type'];
		$user_name = $string['username'];
		$uuid = $string['job_id'];
		$cluster_name = $string['cluster_name'];
		$comps = $string['comp_name'];
		$role=$this->getRoleID($token);
		if($role['code']!==200){
			$data['code'] =$role['code'];
			$data['message'] = $role['message'];
			print_r(json_encode($data));return;
		}
		$role_id=$role['role_id'];
		//检验这个用户是否有删除计算节点的权限
		$sql_role="select compute_node_drop_priv from kunlun_role_privilege where id='$role_id';";
		$res_role_priv=$this->Login_model->getList($sql_role);
		if($res_role_priv[0]['compute_node_drop_priv']!=='Y') {
			$data['code'] = 500;
			$data['message'] = '该账户不具备删除计算节点权限';
			print_r(json_encode($data));return;
		}
		$meta_json=array(
			'ver'=>$ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'user_name'=>$user_name,
			'cluster_name'=>$cluster_name,
			'comp_name'=>$comps,
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$this->load->model('Cluster_model');
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		if(!empty($post_arr)){
			if($post_arr['result']=='accept'){
				$data['code'] = 200;
				$data['message'] = 'success';
			}elseif($post_arr['result']=='busy'){
				$data['code'] = 501;
				$data['message'] = '系统正在操作中，请等待一会！';
			}else{
				$data['code'] = 500;
				$data['message'] = $post_arr['message'];
			}
		}
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public  function delSnode(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$ver = $string['ver'];
		$job_type = $string['job_type'];
		$user_name = $string['username'];
		$uuid = $string['job_id'];
		$cluster_name = $string['cluster_name'];
		$shard_name = $string['shard_name'];
		$ip = $string['ip'];
		$port= $string['port'];
		$role=$this->getRoleID($token);
		if($role['code']!==200){
			$data['code'] =$role['code'];
			$data['message'] = $role['message'];
			print_r(json_encode($data));return;
		}
		$role_id=$role['role_id'];
		//检验这个用户是否有删除存储节点的权限
		$sql_role="select storage_node_drop_priv from kunlun_role_privilege where id='$role_id';";
		$res_role_priv=$this->Login_model->getList($sql_role);
		if($res_role_priv[0]['storage_node_drop_priv']!=='Y') {
			$data['code'] = 500;
			$data['message'] = '该账户不具备删除存储节点权限';
			print_r(json_encode($data));return;
		}
		$meta_json=array(
			'ver'=>$ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'user_name'=>$user_name,
			'cluster_name'=>$cluster_name,
			'shard_name'=>$shard_name,
			'ip'=>$ip,
			'port'=>$port,
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$this->load->model('Cluster_model');
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		if(!empty($post_arr)){
			if($post_arr['result']=='accept'){
				$data['code'] = 200;
				$data['message'] = 'success';
			}elseif($post_arr['result']=='busy'){
				$data['code'] = 501;
				$data['message'] = '系统正在操作中，请等待一会！';
			}else{
				$data['code'] = 500;
				$data['message'] = $post_arr['message'];
			}
		}
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public  function controlInstance(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$ver = $string['ver'];
		$job_type = $string['job_type'];
		$user_name = $string['username'];
		$uuid = $string['job_id'];
		$control = $string['control'];
		$ip = $string['ip'];
		$port= $string['port'];
		$type= $string['type'];
		$role=$this->getRoleID($token);
		if($role['code']!==200){
			$data['code'] =$role['code'];
			$data['message'] = $role['message'];
			print_r(json_encode($data));return;
		}
		$role_id=$role['role_id'];
		if($control=='start'){
			if($type=='pgsql'){
				//检验这个用户是否有启用计算节点的权限
				$sql_role="select compute_enable_priv from kunlun_role_privilege where id='$role_id';";
				$res_role_priv=$this->Login_model->getList($sql_role);
				if($res_role_priv[0]['compute_enable_priv']!=='Y') {
					$data['code'] = 500;
					$data['message'] = '该账户不具备启用计算节点权限';
					print_r(json_encode($data));return;
				}
			}
			if($type=='mysql'){
				//检验这个用户是否有启用存储节点的权限
				$sql_role="select storage_enable_priv from kunlun_role_privilege where id='$role_id';";
				$res_role_priv=$this->Login_model->getList($sql_role);
				if($res_role_priv[0]['storage_enable_priv']!=='Y') {
					$data['code'] = 500;
					$data['message'] = '该账户不具备启用存储节点权限';
					print_r(json_encode($data));return;
				}
			}
		}elseif($control=='stop'){
			if($type=='pgsql'){
				//检验这个用户是否有启用计算节点的权限
				$sql_role="select compute_disable_priv from kunlun_role_privilege where id='$role_id';";
				$res_role_priv=$this->Login_model->getList($sql_role);
				if($res_role_priv[0]['compute_disable_priv']!=='Y') {
					$data['code'] = 500;
					$data['message'] = '该账户不具备禁用计算节点权限';
					print_r(json_encode($data));return;
				}
			}
			if($type=='mysql'){
				//检验这个用户是否有启用存储节点的权限
				$sql_role="select storage_disable_priv from kunlun_role_privilege where id='$role_id';";
				$res_role_priv=$this->Login_model->getList($sql_role);
				if($res_role_priv[0]['storage_disable_priv']!=='Y') {
					$data['code'] = 500;
					$data['message'] = '该账户不具备禁用存储节点权限';
					print_r(json_encode($data));return;
				}
			}
		}
		$meta_json=array(
			'ver'=>$ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'user_name'=>$user_name,
			'control'=>$control,
			'ip'=>$ip,
			'port'=>$port,
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$this->load->model('Cluster_model');
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		if(!empty($post_arr)){
			if($post_arr['result']=='accept'){
				$data['code'] = 200;
				$data['message'] = 'success';
			}elseif($post_arr['result']=='busy'){
				$data['code'] = 501;
				$data['message'] = '系统正在操作中，请等待一会！';
			}else{
				$data['code'] = 500;
				$data['message'] = $post_arr['message'];
			}
		}
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public function getRoleID($token){
		//验证用户是否授权
		$this->load->model('Login_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token';";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
				} else {
					//查看该账户是否有角色的权限
					$user_id = $res[0]['id'];
					$role_sql = "select role_id from kunlun_role_assign where user_id='$user_id';";
					$res_role = $this->Login_model->getList($role_sql);
					if ($res_role[0]['role_id'] === NULL) {
						$data['code'] = 500;
						$data['message'] = '该账户不是角色用户';
					}
					$role_id=$res_role[0]['role_id'];
					$data['code'] = 200;
					$data['role_id'] = $role_id;
				}
				return $data;
			}
		}
	}
	public function getEffectCluster(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		$string=json_decode(@file_get_contents('php://input'),true);
		$effectCluster=$string['effectCluster'];
		$apply_all_cluster=$string['apply_all_cluster'];
		//验证token
		$this->load->model('Login_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token' group by id;";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				} else {
					//获取用户数据
					if($apply_all_cluster==1){
						if(empty($effectCluster)){
							$sql_name="select id,name,when_created,nick_name from db_clusters ";
						}
					}
					if($apply_all_cluster==2) {
						if(empty($effectCluster)){
							$effectCluster='NULL';
						}
						if (strpos($effectCluster, ',') == false) {
							$sql_name = "select id,name,nick_name from db_clusters where id=$effectCluster";
						} else {
							$sql_name = "select id,name,nick_name from db_clusters where id in ($effectCluster) ";
						}
					}
					$this->load->model('Cluster_model');
					$res_name=$this->Cluster_model->getList($sql_name);
					$data['code'] = 200;
					$data['list'] = $res_name;
					$data['total'] = $res_name ? count($res_name) : 0;
					print_r(json_encode($data));
				}
			}
		}
	}
	public function updateAssign(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		$string=json_decode(@file_get_contents('php://input'),true);
		$effectCluster=$string['effectCluster'];
		$cluster_name=$string['cluster_name'];
		$username=$string['username'];
		$type=$string['type'];
		//验证token
		$this->load->model('Login_model');
		$this->load->model('Cluster_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token';";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));return;
				}
				//查集群id
				$sql_id="select id from db_clusters where name='$cluster_name'";
				$res_id=$this->Cluster_model->getList($sql_id);
				if($res_id!==false){
					$cluster_id=$res_id[0]['id'];
					//根据用户id查role_id
					$user_id=$res[0]['id'];
					$sql_role="select role_id from kunlun_role_assign where user_id='$user_id'";
					$res_role=$this->Login_model->getList($sql_role);
					$role_count=count($res_role);
					if($role_count==1){
						$role_id=$res_role[0]['role_id'];
						//$affected_clusters=$effectCluster.','.$cluster_id;
						if($type==='add'){
							$affected_clusters=$effectCluster.','.$cluster_id;
						}elseif($type==='del'){
							$cluster_arr=explode(',', $effectCluster);
							foreach( $cluster_arr as $key=>$value) {
								if($cluster_id == $value) unset($cluster_arr[$key]);
							}
							if(empty($cluster_arr)){
								$affected_clusters='';
							}else{
								$affected_clusters=implode(',',$cluster_arr);
							}
						}
						//更新授权表
						$sql_update="update kunlun_role_assign set affected_clusters='$affected_clusters',update_time=now() where user_id='$user_id' and role_id='$role_id' and apply_all_cluster=2";
						$res_update=$this->Login_model->updateList($sql_update);
						if($res_update==1){
							$data['code'] = 200;
							$data['effectCluster'] = $affected_clusters;
							$data['message'] = '更新成功';
						}else{
							$data['code'] = 500;
							$data['message'] = '更新失败';
						}
					}elseif($role_count>1){
						if($type==='add'){
							$affected_clusters=$effectCluster.','.$cluster_id;
							$cluster_append=','.$cluster_id;
							$sql_update="update kunlun_role_assign set affected_clusters =CONCAT(affected_clusters,'$cluster_append') where user_id='$user_id'  and apply_all_cluster=2";
						}elseif($type==='del'){
							$cluster_arr=explode(',', $effectCluster);
							$cluster_arr=array_unique($cluster_arr);
							foreach( $cluster_arr as $key=>$value) {
								if($cluster_id == $value) unset($cluster_arr[$key]);
							}
							//print_r($cluster_arr);exit;
							if(empty($cluster_arr)){
								$affected_clusters='';
							}else{
								$affected_clusters=implode(',',$cluster_arr);
							}
							$sql_update = "update kunlun_role_assign set affected_clusters = trim(both ',' from replace(concat(',', affected_clusters, ','), ',$cluster_id,', ',')) where user_id='$user_id'  and apply_all_cluster=2";
						}
						$res_update=$this->Login_model->updateList($sql_update);
						if($res_update!==0){
							$data['code'] = 200;
							$data['effectCluster'] = $affected_clusters;
							$data['message'] = '更新成功';
						}else{
							$data['code'] = 500;
							$data['message'] = '更新失败';
						}
					}
				}
				print_r(json_encode($data));
			}
		}
	}
	public function delAssign(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		$string=json_decode(@file_get_contents('php://input'),true);
		$effectCluster=$string['effectCluster'];
		$cluster_name=$string['cluster_name'];
		$username=$string['username'];
		//验证token
		$this->load->model('Login_model');
		$this->load->model('Cluster_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token';";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));return;
				}
				//查集群id
				$sql_id="select id from db_clusters where name='$cluster_name'";
				$res_id=$this->Cluster_model->getList($sql_id);
				if($res_id!==false){
					$cluster_id=$res_id[0]['id'];
					//根据用户id查role_id
					$user_id=$res[0]['id'];
					$sql_role="select role_id from kunlun_role_assign where user_id='$user_id'";
					$res_role=$this->Login_model->getList($sql_role);
					$role_count=count($res_role);
					if($role_count==1){
						$role_id=$res_role[0]['role_id'];
						$affected_clusters=$effectCluster.','.$cluster_id;
						//更新授权表
						$sql_update="update kunlun_role_assign set affected_clusters='$affected_clusters',update_time=now() where user_id='$user_id' and role_id='$role_id' and apply_all_cluster=2";
						$res_update=$this->Login_model->updateList($sql_update);
						if($res_update==1){
							$data['code'] = 200;
							$data['effectCluster'] = $affected_clusters;
							$data['message'] = '更新成功';
						}else{
							$data['code'] = 500;
							$data['message'] = '更新失败';
						}
					}elseif($role_count>1){
						$affected_clusters=$effectCluster.','.$cluster_id;
						$cluster_append=','.$cluster_id;
						$sql_update="update kunlun_role_assign set affected_clusters =CONCAT(affected_clusters,'$cluster_append') where user_id='$user_id'  and apply_all_cluster=2";
						$res_update=$this->Login_model->updateList($sql_update);
						if($res_update!==0){
							$data['code'] = 200;
							$data['effectCluster'] = $affected_clusters;
							$data['message'] = '更新成功';
						}else{
							$data['code'] = 500;
							$data['message'] = '更新失败';
						}
					}
				}
				print_r(json_encode($data));
			}
		}
	}
	public function getStatus(){
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$uuid = $string['uuid'];
		$this->load->model('Cluster_model');
		//$uuid= $this->input->post('uuid');
		$meta_json=array(
			'job_id'=>$uuid,
			'job_type'=>'get_status'
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		//print_r($post_data);exit;
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public function clusterStatus(){
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$ver = $string['ver'];
		$uuid = $string['job_id'];
		$job_type = $string['job_type'];
		$meta_json=array(
			'ver'=>$ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		//print_r($post_data);exit;
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public function machineStatus(){
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$ver = $string['ver'];
		$uuid = $string['job_id'];
		$job_type = $string['job_type'];
		$meta_json=array(
			'ver'=>$ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public function getNode(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$shard_id  = $string['shard_id'];
		$cluster_id = $string['cluster_id'];
		$tree_id = $string['tree_id'];
		$arr=array();
		$nodes=array();
		$links=array();

		$this->load->model('Login_model');
		$this->load->model('Cluster_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token' group by id;";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				} else {
					//获取集群数据
					$shard_arr=$this->getShardNode($cluster_id,$shard_id);
					if($shard_arr!==false){
						foreach ($shard_arr as $key){
							$shard_arr_id=$key['id'];
							$shard_arr_name=$key['port'];
//							$ip=$key['hostaddr'];
//							$username=$key['user_name'];
//							$pwd=$key['passwd'];
//							$per_dbname='performance_schema';
//							$sql_main="select MEMBER_ROLE from replication_group_members where MEMBER_HOST='$ip' and MEMBER_PORT='$shard_arr_name';";
//							$res_main=$this->Cluster_model->getMysql($ip,$shard_arr_name,$username,$pwd,$per_dbname,$sql_main);
//							if($res_main['code']==200){
//								if(isset($res_main[0])){
//									if($res_main[0]){
//										$type=$res_main[0];
//									}
//								}else{
//									$type='OFFLINE';
//								}
//							}else{
//								$type='OFFLINE';
//							}
							$shard_n_id='snode'.$shard_arr_id;
							//获取集群的名称
							$cluster_name=$this->getClusterName($cluster_id);
							//获取shard名称
							$shard_name=$this->getShardName($cluster_id,$shard_id);
							$shard_node=array('id'=>$shard_n_id, 'text'=>$shard_arr_name,'data'=>array('cluster_id'=>$cluster_id,'hostaddr'=>$key['hostaddr'],'port'=>$key['port'],'cpu_cores'=>$key['cpu_cores'],'initial_storage_GB'=>$key['initial_storage_GB'],'max_storage_GB'=>$key['max_storage_GB'],'innodb_buffer_pool_MB'=>$key['innodb_buffer_pool_MB'],'rocksdb_buffer_pool_MB'=>$key['rocksdb_buffer_pool_MB'],'name'=>'mysql','shard_id'=>$shard_id,'cluster_name'=>$cluster_name,'shard_name'=>$shard_name));
							$shard_link=array('from'=>$tree_id, 'to'=>$shard_n_id,'text'=>$key['hostaddr']);
							array_push($nodes,$shard_node);
							array_push($links,$shard_link);
						}

					}
					array_unique($nodes,SORT_REGULAR);
					array_unique($links,SORT_REGULAR);
					$arr=array('nodes'=>$nodes,'links'=>$links);
					$data['code'] = 200;
					$data['list'] = $arr;
					print_r(json_encode($data));
				}
			}
		}
	}
	public function getShardCount(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$shard_id  = $string['shard_id'];
		$cluster_id = $string['cluster_id'];

		$this->load->model('Login_model');
		$this->load->model('Cluster_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token' group by id;";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				} else {
					$total_sql="select count(id) as count from shard_nodes where id='$shard_id' and db_cluster_id='$cluster_id' ";
					$res_total=$this->Cluster_model->getList($total_sql);
					$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
					print_r(json_encode($data));
				}
			}
		}
	}
	public function getStandbyNode(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["accessToken"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$cluster_id = $string['cluster_id'];
		$shard_id = $string['shard_id'];
		$sql="select hostaddr,port,user_name,passwd from shard_nodes where db_cluster_id='$cluster_id' and shard_id='$shard_id'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		$arr=array();
		if(!empty($res)){
			foreach ($res as $row) {
				$ip=$row['hostaddr'];
				$port=$row['port'];
				$username=$row['user_name'];
				$pwd=$row['passwd'];
				$per_dbname='performance_schema';
				$sql_main="select MEMBER_ROLE from replication_group_members where MEMBER_HOST='$ip' and MEMBER_PORT='$port';";
				$res_main=$this->Cluster_model->getMysql($ip,$port,$username,$pwd,$per_dbname,$sql_main);
				if(isset($res_main[0])){
					if($res_main[0]=='SECONDARY'){
						$ip_arr=array('ip'=>$ip, 'port'=>$port,'status'=>$res_main[0]);
						array_push($arr,$ip_arr);
					}
				}else{
					$type='OFFLINE';
					$ip_arr=array('ip'=>$ip, 'port'=>$port,'status'=>$type);
					array_push($arr,$ip_arr);
				}
			}
		}
		$data['code'] = 200;
		$data['list'] = $arr;
		print_r(json_encode($data));
	}
}
