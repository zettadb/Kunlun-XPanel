<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Computer extends CI_Controller {

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
		unset($_SESSION['ip']);
		$this->load->view('app/computer_list');
	}

	public function createComputer(){
		$ip=$this->input->post('ip');
		$datadir=$this->input->post('datadir');
		$logdir=$this->input->post('logdir');
		$wal_log_dir=$this->input->post('wal_log_dir');
		$comp_datadir=$this->input->post('comp_datadir');
		$this->load->model('Main_model');
		$uuid=$this->Main_model->create_uuid();
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>'create_machine',
			'hostaddr'=>$ip,
			'datadir'=>$datadir,
			'logdir'=>$logdir,
			'wal_log_dir'=>$wal_log_dir,
			'comp_datadir'=>$comp_datadir
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$post_arr = $this->Main_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public function getComputer(){
		$ip=$this->meta_ip;
		$port=$this->meta_port;
		$this->load->model('Main_model');
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		//获取集群信息
		$sql='select hostaddr,datadir,logdir,wal_log_dir,comp_datadir,when_created,id from server_nodes order by id desc';
		$res=$this->Main_model->getNodeList($ip,$port,$username,$pwd,$dbname,$sql);
		if($res['code']==200){
			print_r(json_encode($res));
		}else{
			$data['code']=$res['code'];
			$data['message']=$res['error'];
			print_r(json_encode($data));
		}
	}
	//验证IP地址是否重复
	public function verifyIP(){
		$ip= $this->input->post('ip');
		$ip_connet=$this->meta_ip;
		$port=$this->meta_port;
		$this->load->model('Main_model');
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		//获取IP信息
		$sql="select hostaddr from server_nodes where hostaddr='$ip';";
		$res=$this->Main_model->getNodeList($ip_connet,$port,$username,$pwd,$dbname,$sql);
		if(!empty($res['list'])){
			echo 'IP地址重复';
		}
		else {
			echo 'IP地址不重复';
		}
	}
	public function delComputer(){
		$ip= $this->input->post('ip');
		$this->load->model('Main_model');
		$uuid=$this->Main_model->create_uuid();
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>'delete_machine',
			'hostaddr'=>$ip
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$post_arr = $this->Main_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));

	}
	public function editComputer(){
		$ip=$this->input->post('ip');
		$datadir=$this->input->post('datadir');
		$logdir=$this->input->post('logdir');
		$wal_log_dir=$this->input->post('wal_log_dir');
		$comp_datadir=$this->input->post('comp_datadir');
		$this->load->model('Main_model');
		$uuid=$this->Main_model->create_uuid();
		$meta_json=array(
			'ver'=>$this->ver,
			'job_id'=>$uuid,
			'job_type'=>'update_machine',
			'hostaddr'=>$ip,
			'datadir'=>$datadir,
			'logdir'=>$logdir,
			'wal_log_dir'=>$wal_log_dir,
			'comp_datadir'=>$comp_datadir
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$post_arr = $this->Main_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data['res']=$post_arr;
		$data['uuid']=$uuid;
		print_r(json_encode($data));
	}
	public  function nodeList(){
		$id= $this->input->get('id');
		//获取计算机ip
		$ip_connet=$this->meta_ip;
		$port_connet=$this->meta_port;
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$dbname=$this->meta_database;
		$this->load->model('Main_model');
		$sql="select hostaddr from server_nodes where id='$id';";
		$res=$this->Main_model->getNodeList($ip_connet,$port_connet,$username,$pwd,$dbname,$sql);
		if(!empty($res['list'])){
			$_SESSION['ip']=$res['list'][0][0];
			$this->load->view('app/comp_node_list');
		} else {
			redirect('Computer');
		}
	}
	public function getCompNodeList()
	{
		$ip = $_SESSION['ip'];
		$ip_connet=$this->meta_ip;
		$port = $this->meta_port;
		$this->load->model('Main_model');
		$username = $this->meta_username;
		$pwd = $this->meta_pwd;
		$dbname = $this->meta_database;
		$arr = array();
		//获取计算节点的ip，port
		$sql = "select hostaddr as ip,port,when_created from comp_nodes where hostaddr='$ip'";
		$res = $this->Main_model->getNodeList($ip_connet,$port,$username,$pwd,$dbname,$sql);
		if ($res['code'] == 200) {
			if(!empty($res['list'])){
				foreach ($res['list'] as $row) {
					$data['ip'] = $row[0];
					$data['port'] = $row[1];
					$data['sql'] = 'psql';
					$data['title'] = '计算节点';
					$data['create_time'] = $row[2];
					array_push($arr, $data);
				}
			}
		} else {
			$data['code'] = $res['code'];
			$data['message'] = $res['error'];
			print_r(json_encode($arr));
			exit;
		}
		/*//获取元数据节点的ip，port
		$sql_meta="select hostaddr as ip,port from meta_db_nodes where hostaddr='$ip'";
		$res_meta=$this->Main_model->getNodeList($ip_connet,$port,$username,$pwd,$dbname,$sql_meta);
		if($res_meta['code']==200){
			if(!($res_meta['list'])) {
				foreach ($res_meta['list'] as $row) {
					$meta_ip = $row[0];
					$meta_port = $row[1];
					$data_meta['ip'] = $meta_ip;
					$data_meta['port'] = $meta_port;
					$data_meta['sql'] = 'meta';
					$data_meta['title'] = '元数据节点';
					array_push($arr, $data_meta);
				}
			}
		}*/
		//获取存储节点的ip，port
		$sql_stor = "select hostaddr as ip,port,when_created from shard_nodes where hostaddr='$ip'";
		$res_stor = $this->Main_model->getNodeList($ip_connet, $port, $username, $pwd, $dbname, $sql_stor);
		if ($res_stor['code'] == 200) {
			if(!empty($res_stor['list'])) {
				foreach ($res_stor['list'] as $row) {
					$stor_ip = $row[0];
					$stor_port = $row[1];
					$data_stor['ip'] = $stor_ip;
					$data_stor['port'] = $stor_port;
					$data_stor['sql'] = 'data';
					$data_stor['title'] = '存储节点';
					$data_stor['create_time'] = $row[2];
					$sql_shard = "select t1.name from shards t1 LEFT JOIN shard_nodes s on t1.id=s.shard_id where s.hostaddr='$stor_ip' and s.port='$stor_port';";
					$res_shard = $this->Main_model->getVersionMysql($ip_connet, $port, $username, $pwd, $dbname, $sql_shard);
					$data_stor['shard'] = $res_shard[0];
					//版本
					$per_dbname='performance_schema';
					$sql_version='select version();';
					$res=$this->Main_model->getVersionMysql($ip_connet,$port,$username,$pwd,$per_dbname,$sql_version);
					$data_meta['version']=$res[0];
					$data_meta['code']=$res['code'];
					array_push($arr, $data_stor);
				}
			}
		}
		print_r(json_encode($arr));
	}
	public function updateFile(){
		//从数据库中获取计算机ip
		$nodes=array();
		$pgsqls=array();
		$mysqls=array();
		$this->load->model('Main_model');
		$sql = "select hostaddr from server_nodes";
		$res = $this->Main_model->getList($sql);
		foreach ($res as $row){
			$node=$row['hostaddr'].':9100';
			//$pgsql=$row['hostaddr'].':9187';
			//$mysql=$row['hostaddr'].':9104';
			array_push($nodes, $node);
		}
		$pgsql=$_SERVER['SERVER_NAME'].':9187';
		$mysql=$_SERVER['SERVER_NAME'].':9104';
		array_push($pgsqls, $pgsql);
		array_push($mysqls, $mysql);
		$url='./json/prometheus.yml';
		if(file_exists($url)) {
			$fp = fopen($url, 'w+');//打开写入文件
			$contents = 'global:
 scrape_interval: 15s
 evaluation_interval: 15s 
scrape_configs: 
 - job_name: "prometheus" 
   static_configs:
    - targets: ["localhost:9090"]
 - job_name: "postgres"
   static_configs:
    - targets: ' . json_encode($pgsqls) . '
 - job_name: "mysql"
   static_configs:
    - targets: ' . json_encode($mysqls) . '
 - job_name: "node"
   static_configs:
    - targets: ' . json_encode($nodes) . '';
			fwrite($fp, $contents);
			fgets($fp);
		}
		 $command='docker restart prometheus';
		//exec($command,$array,$state);
		passthru($command);
	}
}
