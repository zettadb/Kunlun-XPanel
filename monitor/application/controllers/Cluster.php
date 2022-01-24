<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cluster extends CI_Controller {

	public function __construct()
	{
		session_start();
		parent::__construct();
		//打开重定向
		//$this->load->helper('form');
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
		$this->load->model("Main_model");
		//$uuid = uuid_create(1);//linux uuid
		//$uuid = $this->Main_model->create_uuid();//windows
		//print_r($_SESSION['uuid']);
		/*if (!isset($_SESSION['uuid']) ) {
			$_SESSION['uuid']=$uuid;
		}*/
		$this->load->view('app/manage_list');
	}
	public function clusterList()
	{
		$this->load->view('app/cluster_list');
	}

	public function createCluster(){
		$buffer_pool=$this->input->post('buffer_pool');
		$shards_count=$this->input->post('shards_count');
		$snode_count=$this->input->post('snode_count');
		$max_storage_size=$this->input->post('max_storage_size');
		$comp_count=$this->input->post('comp_count');
		$max_connections=$this->input->post('max_connections');
		$cpu_cores=$this->input->post('cpu_cores');
		$ha_mode=$this->input->post('ha_mode');
		$this->load->model("Main_model");
		//$uuid = $_SESSION['uuid'];//windows  uuid
		//调接口
		$uuid=$this->Main_model->create_uuid();
		//$uuid = uuid_create(1);//linux uuid
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>'create_cluster',
			'shards'=>$shards_count,
			'nodes'=>$snode_count,
			'comps'=>$comp_count,
			'max_storage_size'=>$max_storage_size,
			'max_connections'=>$max_connections,
			'innodb_size'=>$buffer_pool,
			'cpu_cores'=>$cpu_cores,
			'ha_mode'=>$ha_mode,
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		//print_r($post_data);exit;
		$post_arr = $this->Main_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));

	}
	public function getCluster(){
		$ip=$this->meta_ip;
		$port=$this->meta_port;
		$this->load->model('Main_model');
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		//获取集群信息
		$sql='select id,name,when_created from db_clusters order by id desc';
		$res=$this->Main_model->getNodeList($ip,$port,$username,$pwd,$dbname,$sql);
		if($res['code']==200){
			print_r(json_encode($res));
		}else{
			$data['code']=$res['code'];
			$data['message']=$res['error'];
			print_r(json_encode($data));
		}
	}
	//验证集群名称是否重复
	/*public function verifyClusterName(){
		$cluster_name= $this->input->post('cluster_name');
		$ip=$this->meta_ip;
		$port=$this->meta_port;
		$this->load->model('Main_model');
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		//获取集群信息
		$sql="select name from db_clusters where name='$cluster_name';";
		$res=$this->Main_model->getNodeList($ip,$port,$username,$pwd,$dbname,$sql);
		if(!empty($res['list'])){
			echo '集群名称重复';
		}
		else {
			echo '集群名称不重复';
		}
	}*/
	public function backUpCluster(){
		//$backup_name= $this->input->post('backup_name');
		$cluster_name= $this->input->post('cluster_name');
		$this->load->model('Main_model');
		$uuid=$this->Main_model->create_uuid();
		//$uuid = uuid_create(1);//linux uuid
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>'backup_cluster',
			//'backup_info'=>$backup_name,
			'backup_cluster_name'=>$cluster_name
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		//print_r($post_data);exit;
		$post_arr = $this->Main_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public function restoreCluster(){
		//$backup_id= $this->input->post('backup_id');
		$restore_name= $this->input->post('restore_name');
		$backup_name= $this->input->post('backup_name');
		$restore_time= $this->input->post('restore_time');
		$this->load->model('Main_model');
		$uuid=$this->Main_model->create_uuid();
		//$uuid = uuid_create(1);//linux uuid
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>'restore_cluster',
			'timestamp'=>$restore_time,
			'backup_cluster_name'=>$backup_name,
			'restore_cluster_name'=>$restore_name
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$post_arr = $this->Main_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public function retreatedCluster(){
		$backup_name= $this->input->post('backup_name');
		$restore_time= $this->input->post('restore_time');
		$this->load->model('Main_model');
		$uuid=$this->Main_model->create_uuid();
		//$uuid = uuid_create(1);//linux uuid
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>'restore_new_cluster',
			'timestamp'=>$restore_time,
			'backup_cluster_name'=>$backup_name,
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		//print_r($post_data);exit;
		$post_arr = $this->Main_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public function delCluster(){
		$name= $this->input->post('name');
		$this->load->model('Main_model');
		$uuid=$this->Main_model->create_uuid();
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>'delete_cluster',
			'cluster_name'=>$name
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$post_arr = $this->Main_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public  function getBackUpCluster(){
		$name= $this->input->post('name');
		$ip=$this->meta_ip;
		$port=$this->meta_port;
		$this->load->model('Main_model');
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		$arr=array();
		//获取集群信息
		$sql="select cluster_name,when_created from cluster_backups where cluster_name='$name' order by when_created asc limit 1";
		$res=$this->Main_model->getNodeList($ip,$port,$username,$pwd,$dbname,$sql);
		if($res['code']==200){
			if(!empty($res['list'])){
				foreach ($res['list'] as $row) {
					$id_arr = array('cluster_name' => $row[0],'create_time' => $row[1]);
					array_push($arr, $id_arr);
				}
			}
			print_r(json_encode($arr));
		}else{
			$data['code']=$res['code'];
			$data['message']=$res['error'];
			print_r(json_encode($data));
		}
	}
	public  function getRestoreCluster(){
		$ip=$this->meta_ip;
		$port=$this->meta_port;
		$this->load->model('Main_model');
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		$arr=array();
		//获取集群信息
		$sql="select name from db_clusters ";
		$res=$this->Main_model->getNodeList($ip,$port,$username,$pwd,$dbname,$sql);
		if(!empty($res['list'])){
			foreach ($res['list'] as $row) {
				array_push($arr, $row[0]);
			}
		}
		if($res['code']==200){
			print_r(json_encode($arr));
		}else{
			$data['code']=$res['code'];
			$data['message']=$res['error'];
			print_r(json_encode($data));
		}
	}
	/*public  function getBackTime(){
		$id=$this->input->post('id');
		$ip=$this->meta_ip;
		$port=$this->meta_port;
		$this->load->model('Main_model');
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		$arr=array();
		//获取集群信息
		$sql="select backup_timestamp from cluster_backups where backupid='$id' ";
		$res=$this->Main_model->getNodeList($ip,$port,$username,$pwd,$dbname,$sql);
		if(!empty($res['list'])){
			foreach ($res['list'] as $row) {
				array_push($arr, $row[0]);
			}
		}
		if($res['code']==200){
			print_r(json_encode($arr));
		}else{
			$data['code']=$res['code'];
			$data['message']=$res['error'];
			print_r(json_encode($data));
		}
	}*/
	//验证备份集群名称是否重复
	public function verifyBackUpName(){
		$backup_name= $this->input->post('backup_name');
		$ip=$this->meta_ip;
		$port=$this->meta_port;
		$this->load->model('Main_model');
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		//获取集群信息
		$sql="select backup_info from cluster_backups where backup_info='$backup_name';";
		$res=$this->Main_model->getNodeList($ip,$port,$username,$pwd,$dbname,$sql);
		if(!empty($res['list'])){
			echo '集群名称重复';
		}
		else {
			echo '集群名称不重复';
		}
	}
	public function getStatus(){
		$this->load->model('Main_model');
		$uuid= $this->input->post('uuid');
		$meta_json=array(
			'job_id'=>$uuid,
			'job_type'=>'get_status'
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$post_arr = $this->Main_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
}
