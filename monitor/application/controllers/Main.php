<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

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
	}
	public function index()
	{
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
			$this->load->view('app/monitor_db');
		} else {
			redirect('Cluster');
		}
	}
	public function enter(){
		$ip=$this->input->post('ip');
		$port=$this->input->post('port');
		//$command='export DATA_SOURCE_NAME="pgx:pgx_pwd@tcp('.$ip.':'.$port.')/";/var/www/html/prometheus/mysqld_exporter/mysqld_exporter ';
		$command='docker run -d -p 9104:9104 --net="bridge" --pid="host" --name=mysqld-exporter -e DATA_SOURCE_NAME=\"pgx:pgx_pwd@\('.$ip.':'.$port.'\)/\" prom/mysqld-exporter';
		//exec("'".$command."'",$array, $state);
		//print_r("passthru(\"'".$command."'\")");exit;
		$str=passthru("'".$command."'");
		if(empty($str)){
			$command1='docker rm -f mysqld-exporter;docker run -d -p 9104:9104 --net="bridge" --pid="host" --name=mysqld-exporter -e DATA_SOURCE_NAME=\"pgx:pgx_pwd@\('.$ip.':'.$port.'\)/\" prom/mysqld-exporter';
			passthru("'".$command1."'");
		}

	}

	public function getNodeList(){
		$node_type=$this->input->post('node_type');
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
		if($node_type==101||empty($node_type)) {
			$sql = "select hostaddr as ip,port from comp_nodes where db_cluster_id='$id'";
			$res = $this->Main_model->getNodeList($ip, $port, $username, $pwd, $dbname, $sql);
			if ($res['code'] == 200) {
				foreach ($res['list'] as $row) {
					$data['ip'] = $row[0];
					$data['port'] = $row[1];
					$data['sql'] = 'psql';
					$data['title'] = '计算节点';
					array_push($arr, $data);
					if(empty($node_type)){
						array_push($pgsql, $row[0] . ':9187');
						array_push($node, $row[0] . ':9100');
					}
				}
			} else {
				$data['code'] = $res['code'];
				$data['message'] = $res['error'];
				print_r(json_encode($data));
				exit;
			}
		}
		//获取元数据节点的ip，port
		if($node_type==103||empty($node_type)) {
			$sql_meta = 'select hostaddr as ip,port from meta_db_nodes';
			$res_meta = $this->Main_model->getNodeList($ip, $port, $username, $pwd, $dbname, $sql_meta);
			if ($res_meta['code'] == 200) {
				foreach ($res_meta['list'] as $row) {
					$meta_ip = $row[0];
					$meta_port = $row[1];
					$data_meta['ip'] = $meta_ip;
					$data_meta['port'] = $meta_port;
					$data_meta['sql'] = 'meta';
					$data_meta['title'] = '元数据节点';
					array_push($arr, $data_meta);
					if(empty($node_type)) {
						array_push($mysql, $row[0] . ':9104');
						array_push($node, $row[0] . ':9100');
					}
				}
			}
		}
		//获取存储节点的ip，port
		if($node_type==102||empty($node_type)) {
			$sql_stor = "select hostaddr as ip,port from shard_nodes where db_cluster_id='$id'";
			$res_stor = $this->Main_model->getNodeList($ip, $port, $username, $pwd, $dbname, $sql_stor);
			if ($res_stor['code'] == 200) {
				foreach ($res_stor['list'] as $row) {
					$stor_ip = $row[0];
					$stor_port = $row[1];
					$data_stor['ip'] = $stor_ip;
					$data_stor['port'] = $stor_port;
					$data_stor['sql'] = 'data';
					$data_stor['title'] = '存储节点';
					$sql_shard = "select t1.name from shards t1 LEFT JOIN shard_nodes s on t1.id=s.shard_id and t1.db_cluster_id=s.db_cluster_id where s.hostaddr='$stor_ip' and s.port='$stor_port' and s.db_cluster_id='$id';";
					$res_shard = $this->Main_model->getVersionMysql($ip, $port, $username, $pwd, $dbname, $sql_shard);
					$data_stor['shard'] = $res_shard[0];
					array_push($arr, $data_stor);
					if (empty($node_type)) {
						array_push($mysql, $row[0] . ':9104');
						array_push($node, $row[0] . ':9100');
					}
				}
			}
		}
		if(empty($node_type)) {
			$pgsql = json_encode(array_values(array_unique($pgsql)));
			$mysql = json_encode(array_values(array_unique($mysql)));
			$node = json_encode(array_values(array_unique($node)));
			//print_r(json_encode($pgsql));exit;
			//写入prometheus.yml
			$f = fopen('./json/prometheus.yml', 'w+');
			$file = 'global:
scrape_interval: 15s
  evaluation_interval: 15s
scrape_configs:
  - job_name: "prometheus"
    static_configs:
      - targets: ["localhost:9090"]
  - job_name: "postgres"
    static_configs:
      - targets: ' . $pgsql . '
  - job_name: "mysql"
    static_configs:d
      - targets: ' . $mysql . '
  - job_name: "node"
    static_configs:
      - targets: ' . $node . '';
			fwrite($f, $file);
			fgets($f);
		}
		print_r(json_encode($arr));
	}
	//psql计算节点连接监控
	public function enterPGSQL(){
		$ip=$this->input->post('ip');
		$port=$this->input->post('port');
		$username=$this->input->post('username');
		$pwd=$this->input->post('pwd');
		/*//$command='fuser -k -n tcp 9187;export DATA_SOURCE_NAME="postgresql://'.$username.':'.$pwd.'@'.$ip.':'.$port.'/postgres?sslmode=disable";/var/www/html/prometheus/postgres_exporter/postgres_exporter & 2>1';
		exec($command,$array, $state);
		if(empty($state)){
			$data['code']='200';

		}else{
			$data['code']='500';
		}
		print_r(json_encode($data));*/
		//$command='export DATA_SOURCE_NAME="postgresql://'.$username.':'.$pwd.'@'.$ip.':'.$port.'/postgres?sslmode=disable";/var/www/html/prometheus/postgres_exporter/postgres_exporter & 2>&1';
		$command='docker run -dp 9187:9187  --name=postgres-exporter  -e DATA_SOURCE_NAME="postgresql://'.$username.':'.$pwd.'@'.$ip.':'.$port.'/postgres?sslmode=disable" wrouesnel/postgres_exporter';
		//print_r("passthru(\"'".$command."'\")");exit;
		$str=passthru("'".$command."'");
		if(empty($str)){
			$command1='docker rm -f postgres_exporter;docker run -dp 9187:9187  --name=postgres-exporter  -e DATA_SOURCE_NAME="postgresql://'.$username.':'.$pwd.'@'.$ip.':'.$port.'/postgres?sslmode=disable" wrouesnel/postgres_exporter';
			passthru("'".$command1."'");
		}
		//exec($command,$array, $state);
		//$data['code']='200';
		//print_r(json_encode($data));
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
	//是否为主副节点
	public  function  getPrimary(){
		$ip=$this->input->post('ip');
		$port=$this->input->post('port');
		$this->load->model('Main_model');
		$username=$this->meta_username;
		$pwd=$this->meta_pwd;
		$per_dbname='performance_schema';
		$sql_main="select MEMBER_ROLE from replication_group_members where MEMBER_HOST='$ip' and MEMBER_PORT='$port';";
		$res_main=$this->Main_model->getVersionMysql($ip,$port,$username,$pwd,$per_dbname,$sql_main);
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
		$data['title']='('.$type.')';
		print_r(json_encode($data));
	}

//	public function readFile(){
//		/*$dir_base = "./json/"; 	//文件上传根目录
//		$iscreate=false;
//		$mode=0755; //创建目录的模式
//		if (is_dir($dir_base)) {
//			$iscreate=true;
//		} else {
//			$re=mkdir($dir_base,$mode,true); //第三个参数为true即可以创建多极目录
//			if ($re){
//				$iscreate=true;
//			}else{
//				$iscreate=false;
//			}
//		}*/
//
//		// 从文件中读取数据到PHP变量
//		$json_string = file_get_contents('./json/install.json');
//		$json=json_decode($json_string,true);
//		//$json=($json_string,true);
//		//var_dump(json_decode($json_string));exit;
//		$arr=array();
//		$meta=$json['cluster']['meta']['nodes'];
//		$comp=$json['cluster']['comp']['nodes'];
//		$data_node=$json['cluster']['data'];
//		$this->load->model("Main_model");
//		if($comp){
//			foreach($comp as $node) {
//				$data['title'] = '计算节点';
//				$data['sql'] = 'psql';
//				$data['ip'] = $node['ip'];
//				$data['port'] = $node['port'];
//				array_push($arr, $data);
//			}
//		}
//		if($meta){
//			foreach($meta as $node) {
//				if ($node['is_primary'] == 'true') {
//					$is_primary = '主';
//				} else {
//					$is_primary = '副';
//				}
//				$data['title'] = '元数据节点(' . $is_primary . ')';
//				$data['sql'] = 'meta';
//				$data['ip'] = $node['ip'];
//				$data['port'] = $node['port'];
//				$data['xport'] = $node['xport'];
//				array_push($arr, $data);
//			}
//		}
//		if($data_node){
//			foreach($data_node as $node) {
//				foreach($node as $row) {
//					foreach($row as $key) {
//						//print_r($key);
//						if ($key['is_primary'] == 'true') {
//							$is_primary = '主';
//						} else {
//							$is_primary = '副';
//						}
//						$data['title'] = '存储节点(' . $is_primary . ')';
//						$data['sql'] = 'data';
//						$data['ip'] = $key['ip'];
//						$data['port'] = $key['port'];
//						$data['path'] = $key['data_dir_path'];
//						$data['version'] ='data';
//						array_push($arr, $data);
//					}
//				}
//			}
//		}
//		print_r(json_encode($arr));
//	}
//	public function upload(){
//		//得到服务器地址
//		$url = $_SERVER['SERVER_NAME'];
//		$base_url = 'http://'.$url ;
//		$pushserver_address=$base_url;
//		$parent_dir = dirname(dirname(dirname(__FILE__)));
//		$filepath  = $parent_dir."/json/";
//		if (!file_exists($filepath)){
//			mkdir($filepath,0777,true);
//		}
//		$addtionalname = $_FILES['cardnumber']['name'];
//		$tmp = $_FILES['cardnumber']['tmp_name'];
//		//转化中文路径
//		if(file_exists($filepath.$addtionalname)){
//			$addtionalname = 'install.json';
//		}
//
//		if(move_uploaded_file($tmp,$filepath.$addtionalname)){
//			$message = "上传成功";
//			$first_dir = basename($parent_dir);
//			//复制到服务器文件夹
//			$html_path = $base_url.'/'.$first_dir.'/json/'.$addtionalname;
//			$res = array(array('message'=>"上传成功"),array('name'=>$addtionalname),array('filepath'=>$html_path));
//			echo json_encode($res);
//		}
//		return false;
//	}
	public function insertNode(){
		$node_type=$this->input->post('node_type');
		$ip=$this->input->post('ip');
		$port=$this->input->post('port');
		$xport=$this->input->post('xport');
		$mgr_port=$this->input->post('mgr_port');
		$username=$this->input->post('username');
		$pwd=$this->input->post('pwd');
		$if_primary=$this->input->post('if_primary');
		$datadir=$this->input->post('datadir');
		$logdir=$this->input->post('logdir');
		$innodbdir=$this->input->post('innodbdir');
		$shard=$this->input->post('shard');
		$json_string = file_get_contents('./json/install.json');
		$json=json_decode($json_string,true);
		$data=array();
		$shard=substr($shard,5);
		$this->load->model("Main_model");
		//$time = $this->getUnixTimestamp();
		//$rand=rand(1,10000);
		//$job_id=$time.$rand;
		$job_id=$this->Main_model->create_uuid();
		//$job_id=uuid_create(1);
		//计算节点
		if($node_type==101){
			$isInsert=false;
			$comp=$json['cluster']['comp']['nodes'];
			$comp_id=count($comp)+1;
			if(empty($comp)){
				$isInsert=true;
			}else{
				foreach($comp as $node) {
					if($node['ip']==$ip&&$node['port']==$port){
						$data['message']='新增节点已存在，无需重复添加！';
						$data['code']='500';
						print_r(json_encode($data));return;
					}else{
						$isInsert=true;
					}
				}
			}
			if($isInsert==true){
				$comp_data = array(
					'id'=>$comp_id,
					'name'=>'comp'.$comp_id,
					'ip'=>$ip,
					'port'=>$port,
					'user'=>$username,
					'password'=>$pwd,
					'datadir'=>$datadir
				);
				array_push($json['cluster']['comp']['nodes'], $comp_data);
				$newJsonString = str_replace("\\/", "/", json_encode($json));//去掉反斜杠
				file_put_contents('./json/install.json', $newJsonString);
				//调接口
				/*$comp_json=array(
					'ver'=>'0.1',
					'job_id'=>'comp'.$job_id,
					'job_type'=>'install_computer',
					'nodes'=>array(
						array(
						'id'=>$comp_id,
						'name'=>'comp'.$comp_id,
						'ip'=>$ip,
						'port'=>$port,
						'user'=>$username,
						'password'=>$pwd,
						'datadir'=>$datadir
						)
					)
				);
				$post_data=str_replace("\\/", "/", json_encode($comp_json));
				$this->load->model("Main_model");
				$post_arr = $this->Main_model->setPostData($post_data,'路径');*/

				$data['message']='新增节点成功';
				$data['code']='200';
				print_r(json_encode($data));
			}
		}
		elseif ($node_type==102){
			$data_node=$json['cluster']['data'];
			foreach($data_node as $node=>$n) {
				foreach ($n as $k=>$v) {
					foreach ($v as $k2=>$v2){
						if ($v2['ip'] == $ip && $v2['port'] == $port) {
							$data['message']='新增节点已存在，无需重复添加！';
							$data['code']='500';
							print_r(json_encode($data));return;
						}
						if(($node+1)==$shard){
							$isInsert=true;
							$data_data = array(
								'is_primary'=>$if_primary,
								'ip'=>$ip,
								'port'=>$port,
								'xport'=>$xport,
								'mgr_port'=>$mgr_port,
								'innodb_buffer_pool_size'=>'64MB',
								'data_dir_path'=>$datadir,
								'log_dir_path'=>$logdir,
								'user'=>'kunlun',
								'election_weight'=>50
							);
							array_push($json['cluster']['data'][$node][$k], $data_data);
						}
					}
				}
			}
			if($isInsert==true){
				//去掉反斜杠
				$newJsonString = str_replace("\\/", "/", json_encode($json));
				file_put_contents('./json/install.json', $newJsonString);
				//调接口
				/*$stor_json=array(
					'ver'=>'0.1',
					'job_id'=>'meta'.$job_id,
					'job_type'=>'install_storage',
					'group_uuid'=>$job_id,
					'nodes'=>array(
						array(
							'is_primary'=>$if_primary,
							'ip'=>$ip,
							'port'=>$port,
							'xport'=>$xport,
							'mgr_port'=>$mgr_port,
							'innodb_buffer_pool_size'=>'64MB',
							'innodb_log_dir_path'=>$innodbdir,
							'data_dir_path'=>$datadir,
							'log_dir_path'=>$logdir,
							'user'=>'kunlun',
							'election_weight'=>50
						)
					)
				);
				$post_data=str_replace("\\/", "/", json_encode($stor_json));
				$this->load->model("Main_model");
				$post_arr = $this->Main_model->setPostData($post_data,'路径');*/
				$data['message']='新增节点成功';
				$data['code']='200';
				print_r(json_encode($data));
			}

		}elseif ($node_type==103){
			$isInsert=false;
			$meta=$json['cluster']['meta']['nodes'];
			$meta_id=count($meta)+1;
			if(empty($meta)){
				$isInsert=true;
			}else{
				foreach($meta as $node) {
					if($node['ip']==$ip&&$node['port']==$port){
						$data['message']='新增节点已存在，无需重复添加！';
						$data['code']='500';
						print_r(json_encode($data));return;
					}else{
						$isInsert=true;
					}
				}
			}
			if($isInsert==true){
				$meta_data = array(
					'is_primary'=>$if_primary,
					'ip'=>$ip,
					'port'=>$port,
					'xport'=>$xport,
					'mgr_port'=>$mgr_port,
					'innodb_buffer_pool_size'=>'64MB',
					'data_dir_path'=>$datadir,
					'log_dir_path'=>$logdir,
					'user'=>'kunlun',
					'election_weight'=>50
				);
				array_push($json['cluster']['meta']['nodes'], $meta_data);
				//去掉反斜杠
				$newJsonString = str_replace("\\/", "/", json_encode($json));
				file_put_contents('./json/install.json', $newJsonString);
				//调接口
				/*$meta_json=array(
					'ver'=>'0.1',
					'job_id'=>'meta'.$job_id,
					'job_type'=>'install_storage',
					'group_uuid'=>$job_id,
					'nodes'=>array(
						array(
							'is_primary'=>$if_primary,
							'ip'=>$ip,
							'port'=>$port,
							'xport'=>$xport,
							'mgr_port'=>$mgr_port,
							'innodb_buffer_pool_size'=>'64MB',
							'innodb_log_dir_path'=>$innodbdir,
							'data_dir_path'=>$datadir,
							'log_dir_path'=>$logdir,
							'user'=>'kunlun',
							'election_weight'=>50
						)
					)
				);
				$post_data=str_replace("\\/", "/", json_encode($meta_json));
				$this->load->model("Main_model");
				$post_arr = $this->Main_model->setPostData($post_data,'路径');*/
				$data['message']='新增节点成功';
				$data['code']='200';
				print_r(json_encode($data));
			}
		}
	}
	public function delNode(){
		$this->load->model("Main_model");
		//$uuid =$this->Main_model->create_uuid();//windows  uuid
		/*$uuid = uuid_create(1);//linux uuid
		var_dump($uuid);exit;*/
		$ip=$this->input->post('ip');
		$port=$this->input->post('port');
		$sql=$this->input->post('sql');
		$json_string = file_get_contents('./json/install.json');
		$json=json_decode($json_string,true);
		$data=array();
		$meta=$json['cluster']['meta']['nodes'];
		$comp=$json['cluster']['comp']['nodes'];
		$data_node=$json['cluster']['data'];
		if($sql=='psql'){
			foreach($comp as $node=>$row) {
				if($row['ip']==$ip&&$row['port']==$port){
					array_splice($json['cluster']['comp']['nodes'],$node,1);
				}
			}
			$newJsonString = str_replace("\\/", "/", json_encode($json));//去掉反斜杠
			file_put_contents('./json/install.json', $newJsonString);
			$data['message']='删除节点成功';
			$data['code']='200';
			print_r(json_encode($data));
		}elseif ($sql=='meta'){//需验证
			foreach($meta as $node=>$row) {
				if($row['ip']==$ip&&$row['port']==$port){
					array_splice($json['cluster']['meta']['nodes'],$node,1);
				}
			}
			$newJsonString = str_replace("\\/", "/", json_encode($json));//去掉反斜杠
			file_put_contents('./json/install.json', $newJsonString);
			$data['message']='删除节点成功';
			$data['code']='200';
			print_r(json_encode($data));
		}elseif ($sql=='data'){//需验证
			foreach($data_node as $node=>$no) {
				foreach ($no as $row=>$r) {
					foreach($r as $key=>$value) {
						if ($value['ip'] == $ip && $value['port'] == $port) {
							array_splice($json['cluster']['data'][$node][$row], $key,1);
						}
					}
				}
			}
			$newJsonString = str_replace("\\/", "/", json_encode($json));//去掉反斜杠
			file_put_contents('./json/install.json', $newJsonString);
			$data['message']='删除节点成功';
			$data['code']='200';
			print_r(json_encode($data));
		}
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
		$shard_data=$this->input->post('shard_data');
		//print_r($shard_data);exit;
		$json_string = file_get_contents('./json/install.json');
		$json=json_decode($json_string,true);
		$shard_data=$this->input->post('shard_data');
		array_push($json['cluster']['data']['nodes'], $shard_data);
		//去掉反斜杠
		$newJsonString = str_replace("\\/", "/", json_encode($json));
		file_put_contents('./json/install.json', $newJsonString);
		//调接口
		/*$meta_json=array(
			'ver'=>'0.1',
			'job_id'=>'meta'.$job_id,
			'job_type'=>'install_storage',
			'group_uuid'=>$job_id,
			'nodes'=>array(
				array(
					'is_primary'=>$if_primary,
					'ip'=>$ip,
					'port'=>$port,
					'xport'=>$xport,
					'mgr_port'=>$mgr_port,
					'innodb_buffer_pool_size'=>'64MB',
					'innodb_log_dir_path'=>$innodbdir,
					'data_dir_path'=>$datadir,
					'log_dir_path'=>$logdir,
					'user'=>'kunlun',
					'election_weight'=>50
				)
			)
		);
		$post_data=str_replace("\\/", "/", json_encode($meta_json));
		$this->load->model("Main_model");
		$post_arr = $this->Main_model->setPostData($post_data,'路径');*/
		$data['message']='新增分片成功';
		$data['code']='200';
		print_r(json_encode($data));

	}
}
