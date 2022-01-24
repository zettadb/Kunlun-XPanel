<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Node extends CI_Controller {

	public function __construct()
	{
		session_start();
		parent::__construct();
		//打开重定向
		$this->load->helper('form');
		$this->load->helper('url');
		$this->meta_username=$this->config->item('meta_username');
		$this->meta_pwd=$this->config->item('meta_pwd');
		$this->meta_database=$this->config->item('meta_database');
		$this->pg_username=$this->config->item('pg_username');
		$this->pg_pwd=$this->config->item('pg_pwd');
		$this->pg_database=$this->config->item('pg_database');
		$this->meta_ip=$this->config->item('meta_ip');
		$this->meta_port=$this->config->item('meta_port');
		$this->ver=$this->config->item('ver');
		$this->post_url=$this->config->item('post_url');
	}
	public function index()
	{
		if ( !isset($_SESSION['cluster_name'])||!isset($_SESSION['cluster_id'])) {
			redirect('Cluster');
		}
		$id=$this->input->get('id');
		//获取计算机
		$ip_connet=$this->meta_ip;
		$port_connet=$this->meta_port;
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		$this->load->model('Main_model');
		$sql="select name from db_clusters where id='$id';";
		$res=$this->Main_model->getNodeList($ip_connet,$port_connet,$username,$pwd,$dbname,$sql);
		if(!empty($res['list'])){
			$_SESSION['cluster_name']=$res['list'][0][0];
			$_SESSION['cluster_id']=$id;
			$this->load->view('app/node_list');
		} else {
			redirect('Cluster');
		}
	}
	public function enter(){
		$ip=$this->input->post('ip');
		$port=$this->input->post('port');
		$command='export DATA_SOURCE_NAME="pgx:pgx_pwd@tcp('.$ip.':'.$port.')/";/var/www/html/prometheus/mysqld_exporter/mysqld_exporter ';
		exec($command,$array, $state);
		$data['code']='200';
		print_r(json_encode($data));
	}

	// 获取版本信息
	public function  getNodeVersion(){
		$ip=$this->input->post('ip');
		$port=$this->input->post('port');
		$sql=$this->input->post('sql');
		$this->load->model('Main_model');
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		$sql_version='select version();';
		$per_dbname='performance_schema';
		if($sql=='psql'){
			$res=$this->Main_model->getVersion($ip,$port,$this->pg_username,$this->pg_pwd,$this->pg_database,$sql_version);

			$data['version']=$res[0]['version'];
			$data['code']=200;
		}elseif($sql=='meta'||$sql=='data'){
			$res=$this->Main_model->getVersionMysql($ip,$port,$username,$pwd,$per_dbname,$sql_version);
			$data['version']=$res[0];
			$data['code']=$res['code'];
		}
		print_r(json_encode($data));
	}

	public function getUnixTimestamp (){
		list($s1, $s2) = explode(' ', microtime());
		return (float)sprintf('%.0f',(floatval($s1) + floatval($s2)) * 1000);
	}
	public function getShards(){
		$id=$_SESSION['cluster_id'];
		$ip=$this->meta_ip;
		$port=$this->meta_port;
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		$this->load->model('Main_model');
		$sql_shard="select name from shards; where db_cluster_id='$id'";
		$res_shard=$this->Main_model->getNodeList($ip,$port,$username,$pwd,$dbname,$sql_shard);
		if($res_shard['code']==200) {
			$data['shards'] = $res_shard['list'];
			$data['code'] = $res_shard['code'];
		}else{
			$data['code'] = $res_shard['code'];
			$data['message'] = $ip.'离线了,请切换ip登陆';
		}
		print_r(json_encode($data));
	}
	public  function inShards(){
		$cluster_name=$this->input->post('cluster_name');
		$shards_count= $this->input->post('shards_count');
		$this->load->model('Main_model');
		$uuid = $this->Main_model->create_uuid();
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>'add_shards',
			'cluster_name'=>$cluster_name,
			'shards'=>$shards_count
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$post_arr = $this->Main_model->postData($post_data,$this->post_url);
		$data['res']=json_decode($post_arr);
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}


	//计算节点列表
	public function getCompNodeList(){
		$id=$_SESSION['cluster_id'];
		$ip=$this->meta_ip;
		$port=$this->meta_port;
		$this->load->model('Main_model');
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		$arr=array();
		$pgsql=array();
		$mysql=array();
		$node=array();
		//获取计算节点的ip，port
		$sql = "select hostaddr as ip,port,id,when_created,name from comp_nodes where db_cluster_id='$id'";
		$res = $this->Main_model->getNodeList($ip, $port, $username, $pwd, $dbname, $sql);
		if ($res['code'] == 200) {
			foreach ($res['list'] as $row) {
				$data['ip'] = $row[0];
				$data['port'] = $row[1];
				$data['id'] = $row[2];
				$data['create_time'] = $row[3];
				$data['comp_name'] = $row[4];
				$data['sql'] = 'psql';
				$data['title'] = '计算节点';
				array_push($arr, $data);
			}
			print_r(json_encode($arr));
		} else {
			$data['code'] = $res['code'];
			$data['message'] = $res['error'];
			print_r(json_encode($data));
			exit;
		}
	}
	//存储分片
	public function getShardNodeList(){
		$id=$_SESSION['cluster_id'];
		$ip=$this->meta_ip;
		$port=$this->meta_port;
		$this->load->model('Main_model');
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		$arr=array();
		//获取存储节点的ip，port
		$sql_stor = "select id,name,when_created from shards where db_cluster_id='$id'";
		$res_stor = $this->Main_model->getNodeList($ip, $port, $username, $pwd, $dbname, $sql_stor);
		if ($res_stor['code'] == 200) {
			foreach ($res_stor['list'] as $row) {
				$id = $row[0];
				$shard = $row[1];
				$when_created = $row[2];
				$data_stor['shard_id'] = $id;
				$data_stor['shard'] = $shard;
				$data_stor['create_time'] = $when_created;
				$data_stor['sql'] = 'data';
				$data_stor['title'] = '存储分片';
				array_push($arr, $data_stor);
			}
			print_r(json_encode($arr));
		}
		else {
			$data['code'] = $res_stor['code'];
			$data['message'] = $res_stor['error'];
			print_r(json_encode($data));
		}
	}
	//元数据节点
	public function getMetaNodeList(){
		$id=$_SESSION['cluster_id'];
		$ip=$this->meta_ip;
		$port=$this->meta_port;
		$this->load->model('Main_model');
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		$arr=array();
		//获取元数据节点的ip，port
		$sql_meta = 'select hostaddr,port,id from meta_db_nodes';
		$res_meta = $this->Main_model->getNodeList($ip, $port, $username, $pwd, $dbname, $sql_meta);
		if ($res_meta['code'] == 200) {
			foreach ($res_meta['list'] as $row) {
				$meta_ip = $row[0];
				$meta_port = $row[1];
				$data_meta['ip'] = $meta_ip;
				$data_meta['port'] = $meta_port;
				$data_meta['id'] = $row[2];
				$data_meta['sql'] = 'meta';
				//主节点副节点
				$per_dbname='performance_schema';
				$sql_main="select MEMBER_ROLE from replication_group_members where MEMBER_HOST='$meta_ip' and MEMBER_PORT='$meta_port';";
				$res_main=$this->Main_model->getVersionMysql($meta_ip,$meta_port,$username,$pwd,$per_dbname,$sql_main);
				if(isset($res_main[0])){
					if($res_main[0]=='PRIMARY'){
						$type='主';
					}elseif($res_main[0]=='SECONDARY'){
						$type='副';
					}else{
						$type='<span style="color: red;">'.$res_main[0].'</span>';
					}
				}else{
					$type='<span style="color: red;">OFFLINE</span>';
				}
				$data_meta['title'] = '元数据节点('.$type.')';
				//版本
				$sql_version='select version();';
				$res=$this->Main_model->getVersionMysql($ip,$port,$username,$pwd,$per_dbname,$sql_version);
				$data_meta['version']=$res[0];
				$data_meta['code']=$res['code'];
				array_push($arr, $data_meta);
			}
			print_r(json_encode($arr));
		} else {
			$data['code'] = $res_meta['code'];
			$data['message'] = $res_meta['error'];
			print_r(json_encode($data));
		}
	}
	public function shardNode()
	{
		if ( !isset($_SESSION['shard_name'])||!isset($_SESSION['shard_id'])) {
			redirect('Cluster');
		}
		$id=$this->input->get('id');
		//获取计算机
		$ip_connet=$this->meta_ip;
		$port_connet=$this->meta_port;
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		$this->load->model('Main_model');
		$sql="select name from shards where id='$id';";
		$res=$this->Main_model->getNodeList($ip_connet,$port_connet,$username,$pwd,$dbname,$sql);
		if(!empty($res['list'])){
			$_SESSION['shard_name']=$res['list'][0][0];
			$_SESSION['shard_id']=$id;
			$this->load->view('app/shard_node_list');
		} else {
			redirect('Node');
		}
	}
	//存储节点
	public function getStrogeNodeList(){
		$id=$_SESSION['cluster_id'];
		$shard_id=$this->input->post('id');
		$ip=$this->meta_ip;
		$port=$this->meta_port;
		$this->load->model('Main_model');
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		$arr=array();
		//获取存储节点的ip，port
		$sql_meta = "select hostaddr,port,id,when_created from shard_nodes where db_cluster_id='$id' and shard_id='$shard_id'";
		$res_meta = $this->Main_model->getNodeList($ip, $port, $username, $pwd, $dbname, $sql_meta);
		if ($res_meta['code'] == 200) {
			foreach ($res_meta['list'] as $row) {
				$meta_ip = $row[0];
				$meta_port = $row[1];
				$data_meta['ip'] = $meta_ip;
				$data_meta['port'] = $meta_port;
				$data_meta['id'] = $row[2];
				$data_meta['create_time'] = $row[3];
				$data_meta['sql'] = 'data';
				//主节点副节点
				$per_dbname='performance_schema';
				$sql_main="select MEMBER_ROLE from replication_group_members where MEMBER_HOST='$meta_ip' and MEMBER_PORT='$meta_port';";
				$res_main=$this->Main_model->getVersionMysql($meta_ip,$meta_port,$username,$pwd,$per_dbname,$sql_main);
				if(isset($res_main[0])){
					if($res_main[0]=='PRIMARY'){
						$type='主';
					}elseif($res_main[0]=='SECONDARY'){
						$type='副';
					}else{
						$type='<span style="color: red;">'.$res_main[0].'</span>';
					}
				}else{
					$type='<span style="color: red;">OFFLINE</span>';
				}
				$data_meta['title'] = '存储节点('.$type.')';
				//版本
				$sql_version='select version();';
				$res=$this->Main_model->getVersionMysql($ip,$port,$username,$pwd,$per_dbname,$sql_version);
				$data_meta['version']=$res[0];
				$data_meta['code']=$res['code'];
				array_push($arr, $data_meta);
			}
			print_r(json_encode($arr));
		} else {
			$data['code'] = $res_meta['code'];
			$data['message'] = $res_meta['error'];
			print_r(json_encode($data));
		}
	}

	public function createCompNode(){
		$cluster_name=$this->input->post('cluster_name');
		$comp_count=$this->input->post('comp_count');
		$this->load->model("Main_model");
		$uuid = $this->Main_model->create_uuid();
		//调接口
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>'add_comps',
			'cluster_name'=>$cluster_name,
			'comps'=>$comp_count
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$post_arr = $this->Main_model->postData($post_data,$this->post_url);
		$data['res']=json_decode($post_arr);
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public function delCompNode(){
		$cluster_name= $this->input->post('cluster_name');
		$name= $this->input->post('name');
		$this->load->model('Main_model');
		$uuid = $this->Main_model->create_uuid();
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>'delete_comp',
			'cluster_name'=>$cluster_name,
			'comp_name'=>$name
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$post_arr = $this->Main_model->postData($post_data,$this->post_url);
		$data['res']=json_decode($post_arr);
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public function delShard(){
		$cluster_name= $this->input->post('cluster_name');
		$name= $this->input->post('name');
		$this->load->model('Main_model');
		$uuid = $this->Main_model->create_uuid();
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>'delete_shard',
			'cluster_name'=>$cluster_name,
			'shard_name'=>$name
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$post_arr = $this->Main_model->postData($post_data,$this->post_url);
		$data['res']=json_decode($post_arr);
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
}
