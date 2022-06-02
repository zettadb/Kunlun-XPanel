<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine extends CI_Controller {

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
		$this->key=$this->config->item('key');
		$this->post_url=$this->config->item('post_url');
	}
	public function getMachineList()
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
		$username=$arr['hostaddr'];
		$start=$pageNo-1;
		//print_r($pageSize);exit;
		//获取用户数据
		$this->load->model('Cluster_model');
		$sql="select id, hostaddr,rack_id,total_cpu_cores,comp_datadir,datadir,logdir,wal_log_dir,total_mem from server_nodes where hostaddr!='pseudo_server_useless'";
		if(!empty($username)){
			$sql .=" and  hostaddr like '%$username%'";
		}
		$sql .=" order by id desc limit ".$pageSize." offset ".$start;
		//print_r($sql);exit;
		$res=$this->Cluster_model->getList($sql);
		if($res===false){
			$res=array();
		}
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] =  $res ? count($res) : 0;;
		print_r(json_encode($data));
	}
	public function createMachine(){
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
		$job_type=$string['job_type'];
		$user_name=$string['username'];
		$uuid = $string['job_id'];
		$ver = $string['ver'];
		$hostaddr = $string['hostaddr'];
		$rack_id = $string['rack_id'];
		$datadir = $string['datadir'];
		$logdir = $string['logdir'];
		$wal_log_dir = $string['wal_log_dir'];
		$comp_datadir = $string['comp_datadir'];
		$total_cpu_cores = $string['total_cpu_cores'];
		$total_mem = $string['total_mem'];
		//调接口
		$this->load->model('Cluster_model');
		$meta_json=array(
			'ver'=>$ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'hostaddr'=>$hostaddr,
			'rack_id'=>$rack_id,
			'datadir'=>$datadir,
			'logdir'=>$logdir,
			'wal_log_dir'=>$wal_log_dir,
			'comp_datadir'=>$comp_datadir,
			'total_cpu_cores'=>$total_cpu_cores,
			'total_mem'=>$total_mem,
			'user_name'=>$user_name,

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
	public function editMachine(){
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
		$job_type=$string['job_type'];
		$user_name=$string['username'];
		$uuid = $string['job_id'];
		$ver = $string['ver'];
		$hostaddr = $string['hostaddr'];
		$rack_id = $string['rack_id'];
		$datadir = $string['datadir'];
		$logdir = $string['logdir'];
		$wal_log_dir = $string['wal_log_dir'];
		$comp_datadir = $string['comp_datadir'];
		$total_cpu_cores = $string['total_cpu_cores'];
		$total_mem = $string['total_mem'];
		//调接口
		$this->load->model('Cluster_model');
		$meta_json=array(
			'ver'=>$ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'hostaddr'=>$hostaddr,
			'rack_id'=>$rack_id,
			'datadir'=>$datadir,
			'logdir'=>$logdir,
			'wal_log_dir'=>$wal_log_dir,
			'comp_datadir'=>$comp_datadir,
			'total_cpu_cores'=>$total_cpu_cores,
			'total_mem'=>$total_mem,
			'user_name'=>$user_name,

		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		//print_r($post_data);exit;
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		if(!empty($post_arr)){
			if($post_arr['result']=='accept') {
				$data['code'] = 200;
				$data['message'] = '正在编辑';
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
	public function deleteMachine(){
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
		$job_type=$string['job_type'];
		$user_name=$string['username'];
		$uuid = $string['job_id'];
		$ver = $string['ver'];
		$hostaddr = $string['hostaddr'];
		//调接口
		$this->load->model('Cluster_model');
		$meta_json=array(
			'ver'=>$ver,
			'job_id'=>$uuid,
			'job_type'=>$job_type,
			'hostaddr'=>$hostaddr,
			'user_name'=>$user_name,

		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		//print_r($post_data);exit;
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		if(!empty($post_arr)){
			if($post_arr['result']=='accept') {
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
	public  function getMachineNodesList(){
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
		$ip= $string['ip'];
		$ip_id='ip';
		$storage_id='storage';
		$storage_text='存储节点';
		$comp_id='comps';
		$comp_text='计算节点';
		$arr=array();
		$nodes=array(array('id'=>$ip_id, 'text'=>$ip),array('id'=>$storage_id, 'text'=>$storage_text),array('id'=>$comp_id, 'text'=>$comp_text));
		$links=array(array('from'=>$ip_id, 'to'=>$storage_id),array('from'=>$ip_id, 'to'=>$comp_id));
		//获取存储节点数据
		$sql="select id,port,shard_id,db_cluster_id from shard_nodes where hostaddr='$ip'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		if($res!==false){
			foreach ($res as $row=>$value){
				foreach ($value as $key2 => $value2) {
					//存储节点
					if ($key2 == 'port') {
						if (!empty($value2)) {
							$shard_node_id='snode'.$res[$row]['id'];
							//获取shard_name
							$shard_arr=$this->getShard($res[$row]['shard_id']);
							if($shard_arr!==false){
								//$shard_arr_id=$shard_arr[0]['id'];
								$shard_arr_name=$shard_arr[0]['name'];
							}
							//获取cluster_name
							$cluster_arr=$this->getCluster($res[$row]['db_cluster_id']);
							if($cluster_arr!==false){
								//$cluster_arr_id=$shard_arr[0]['id'];
								$cluster_arr_name=$cluster_arr[0]['name'];
							}
							$shard_node=array('id'=>$shard_node_id, 'text'=>$value2 ,'data'=>array('shard_name'=>$shard_arr_name,'cluster_name'=>$cluster_arr_name,'ip'=>$ip));
							$shard_link=array('from'=>$storage_id, 'to'=>$shard_node_id);
							array_push($nodes,$shard_node);
							array_push($links,$shard_link);
						}
					}
					//shard名称
					/*if ($key2 == 'shard_id') {
						if (!empty($value2)) {
							$shard_node_id='s'.$res[$row]['id'];
							$shard_arr=$this->getShard($value2);
							if($shard_arr!==false){
								$shard_arr_id=$shard_arr[0]['id'];
								$shard_arr_name=$shard_arr[0]['name'];
							}
							$shard_n_id='shard'.$shard_arr_id;
							$shard_node=array('id'=>$shard_n_id, 'text'=>$shard_arr_name);
							$shard_link=array('from'=>$shard_node_id, 'to'=>$shard_n_id);
							array_push($nodes,$shard_node);
							array_push($links,$shard_link);
						}
					}*/
					//shard名称
//					if ($key2 == 'db_cluster_id') {
//						if (!empty($value2)) {
//							$shard_node_id='shard'.$res[$row]['shard_id'];
//							$shard_arr=$this->getCluster($value2);
//							if($shard_arr!==false){
//								$shard_arr_id=$shard_arr[0]['id'];
//								$shard_arr_name=$shard_arr[0]['name'];
//							}
//							$shard_n_id='c'.$shard_arr_id;
//							$shard_node=array('id'=>$shard_n_id, 'text'=>$shard_arr_name);
//							$shard_link=array('from'=>$shard_node_id, 'to'=>$shard_n_id);
//							array_push($nodes,$shard_node);
//							array_push($links,$shard_link);
//						}
//					}
				}
			}
			//array_unique($nodes);
		}
		//获取计算节点数据
		$sql="select id,name,port,db_cluster_id from comp_nodes where hostaddr='$ip'";
		$this->load->model('Cluster_model');
		$res_comp=$this->Cluster_model->getList($sql);
		if($res_comp!==false){
			foreach ($res_comp as $row=>$value){
				foreach ($value as $key2 => $value2) {
					//存储节点
					if ($key2 == 'port') {
						if (!empty($value2)) {
							$shard_node_id='cnode'.$res_comp[$row]['id'];
							//获取cluster_name
							$cluster_comp_arr=$this->getCluster($res_comp[$row]['db_cluster_id']);
							if($cluster_comp_arr!==false){
								//$cluster_arr_id=$shard_arr[0]['id'];
								$cluster_arr_name=$cluster_comp_arr[0]['name'];
							}
							$shard_node=array('id'=>$shard_node_id, 'text'=>$value2,'data'=>array('cluster_name'=>$cluster_arr_name,'ip'=>$ip));
							$shard_link=array('from'=>$comp_id, 'to'=>$shard_node_id);
							array_push($nodes,$shard_node);
							array_push($links,$shard_link);
//							$shard_comp_id='comp_name'.$res_comp[$row]['id'];
//							$shard_node1=array('id'=>$shard_comp_id, 'text'=>$res_comp[$row]['name']);
//							$shard_link1=array('from'=>$shard_node_id, 'to'=>$shard_comp_id);
//							array_push($nodes,$shard_node1);
//							array_push($links,$shard_link1);
						}
					}
					//集群名称
//					if ($key2 == 'db_cluster_id') {
//						if (!empty($value2)) {
//							$shard_node_id='comp_name'.$res_comp[$row]['id'];
//							$shard_arr=$this->getCluster($value2);
//							if($shard_arr!==false){
//								$shard_arr_id=$shard_arr[0]['id'];
//								$shard_arr_name=$shard_arr[0]['name'];
//							}
//							$shard_n_id='c'.$shard_arr_id;
//							$shard_node=array('id'=>$shard_n_id, 'text'=>$shard_arr_name);
//							$shard_link=array('from'=>$shard_node_id, 'to'=>$shard_n_id);
//							array_push($nodes,$shard_node);
//							array_push($links,$shard_link);
//						}
//					}
				}
			}
		}
		array_unique($nodes,SORT_REGULAR);
		array_unique($links,SORT_REGULAR);
		$arr=array('rootId'=>'1','nodes'=>$nodes,'links'=>$links);
		$data['code'] = 200;
		$data['list'] = $arr;
		print_r(json_encode($data));
	}

	public function getShard($id){
		$sql="select id,name from shards where id='$id'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		return $res;
	}
	public function getCluster($id){
		$sql="select id,name from db_clusters where id='$id'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		return $res;
	}
	public function getNodeCount(){
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
		$ip= $string['ip'];
		$sql="SELECT COUNT(id)as count from shard_nodes where hostaddr='$ip' UNION SELECT COUNT(id)as count from comp_nodes where hostaddr='$ip'";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		$data['total']=$res[0]['count'];
		print_r(json_encode($data));
	}
	public  function getNodeList(){
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
		$hostAddrList= $string['hostAddrList'];
		//print_r($hostAddrList);exit;
		$this->load->model('Cluster_model');
		$comp=array();
		$storage=array();
		if(!empty($hostAddrList)){
			foreach ($hostAddrList as $row){
				$sql_storage="SELECT COUNT(id)as count from shard_nodes where hostaddr='$row' ";
				$res_storage=$this->Cluster_model->getList($sql_storage);
				array_push($storage,(int)$res_storage[0]['count']);
				$sql_comp="SELECT COUNT(id)as count from comp_nodes where hostaddr='$row'";
				$res_comp=$this->Cluster_model->getList($sql_comp);
				array_push($comp,(int)$res_comp[0]['count']);
			}

		}

		$data['comp']=$comp;
		$data['storage']=$storage;
		print_r(json_encode($data));
	}
	public  function getUsedList(){
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
		$hostAddrList= $string['hostAddrList'];
		//print_r($hostAddrList);exit;
		$this->load->model('Cluster_model');
		$used=array();
		$avail=array();
		if(!empty($hostAddrList)){
			$sql="select id from server_nodes where hostaddr='$hostAddrList'";
			$res=$this->Cluster_model->getList($sql);
			if(!empty($res)){
				$id=$res[0]['id'];
			}else{
				$data['used']=false;
				$data['avail']=false;
				print_r(json_encode($data));
			}
			$sql_storage="SELECT comp_datadir_used,comp_datadir_avail,datadir_used,datadir_avail,wal_log_dir_used,wal_log_dir_avail,log_dir_used,log_dir_avail from server_nodes_stats where id='$id' ";
			$res_storage=$this->Cluster_model->getList($sql_storage);
			array_push($used,($res_storage[0]['comp_datadir_used']/1024));
			array_push($used,($res_storage[0]['datadir_used']/1024));
			array_push($used,($res_storage[0]['wal_log_dir_used']/1024));
			array_push($used,($res_storage[0]['log_dir_used']/1024));

			array_push($avail,($res_storage[0]['comp_datadir_avail']/1024));
			array_push($avail,($res_storage[0]['datadir_avail']/1024));
			array_push($avail,($res_storage[0]['wal_log_dir_avail']/1024));
			array_push($avail,($res_storage[0]['log_dir_avail']/1024));
		}

		$data['used']=$used;
		$data['avail']=$avail;
		print_r(json_encode($data));
	}
}
