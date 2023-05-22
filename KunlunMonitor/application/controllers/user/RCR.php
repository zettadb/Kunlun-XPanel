<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RCR extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->config->load('myconfig');
		$this->key = $this->config->item('key');
		$this->post_url = $this->config->item('post_url');
	}

	public function getRCRList()
	{
		$pageNo = $this->input->get('pageNo');
		$pageSize = $this->input->get('pageSize');
		$hostaddr = $this->input->get('hostaddr');
		$user_name = $this->input->get('user_name');
		$start = ($pageNo - 1) * $pageSize;
		$this->load->model('Login_model');
		$this->load->model('Cluster_model');

		$user_id='';
		$res_user = $this->Login_model->getUserId($user_name);
		if($res_user['code']==200){
			$user_id=$res_user['message'];
		}

		$sql = "select * from cluster_rcr_infos where  id is not null and status!='deleted'";
		if (!empty($hostaddr)) {
			$sql .= " and  master_rcr_meta like '%$hostaddr%'";
		}
		$sql .= " order by id desc limit " . $pageSize . " offset " . $start;
		$res = $this->Cluster_model->getList($sql);
		if ($res === false) {
			$res = array();
		}else{
			foreach ($res as $key => $row) {
				foreach ($row as $k => $v) {
					if ($k == 'master_rcr_meta') {
						$name='';
						if (!empty($v)){
							$meta_name=$this->getMetaName($v,$user_id);
							if(!empty($meta_name)) {
								$name=$meta_name[0]['name'];
							}
						}
						$res[$key]['master_meta_name'] = $name;
					}
					if ($k == 'slave_rcr_meta') {
						$name='';
						if (!empty($v)){
							$meta_name=$this->getMetaName($v,$user_id);
							if(!empty($meta_name)) {
								$name=$meta_name[0]['name'];
							}
						}
						$res[$key]['slave_meta_name'] = $name;
					}
				}
			}
		}
		$sql_total = "select count(id) as count from cluster_rcr_infos where  id is not null and status!='deleted'";
		if (!empty($username)) {
			$sql_total .= " and  hostaddr like '%$username%'";
		}
		$res_total = $this->Cluster_model->getList($sql_total);
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
		print_r(json_encode($data));
	}

	public function createRCR()
	{
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$slave = $string['slave'];
		$postJson=$string['postData'];
		$slave_meta='';$ips='';$ip='';$port='';$string_info='';$post_url='';

		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($postJson));

		//查元数据是否一致
		$sql = "select hostaddr,port from meta_db_nodes; ";
		$res = $this->Cluster_model->getList($sql);
		if ($res !== false) {
			foreach ($res as $key=>$row){
				$ips.=$res[$key]['hostaddr'].':'.$res[$key]['port'].',';
			}
			$slave_meta=substr($ips,0,strlen($ips)-1);
		}
		if($slave===$slave_meta){
			$post_url = $this->post_url;
			$post_arr = $this->Cluster_model->postData($post_data, $post_url);
			$post_arr = json_decode($post_arr, TRUE);
			$data = $post_arr;
			print_r(json_encode($data));
		}else{
			$slave=substr($slave,0,strlen($slave)-1);
			$slave=explode(',',$slave);
			$meta_count=count($slave);
			foreach ($slave as $knode => $vnode) {
				$ips=explode(':',$vnode);
				$sql = "select hostaddr,port from cluster_mgr_nodes where  member_state='source' ";
				$res_meta = $this->getMetaClusters($ips[0], $ips[1],$sql,$string_info);
				if ($res_meta['code'] == 200) {
					$ip = $res_meta['list'][0]['host'];
					$port = $res_meta['list'][0]['port'];
					$post_url = 'http://' .$ip. ':' . $port . '/HttpService/Emit';
					$post_arr = $this->Cluster_model->postData($post_data, $post_url);
					$post_arr = json_decode($post_arr, TRUE);
					$data = $post_arr;
					print_r(json_encode($data));
					break;
				} else if ($meta_count == ($knode + 1)) {
					$data['code'] = 500;
					$data['message'] = $res_meta[0] . $vnode['hostaddr'] . '(' . $vnode['port'] . ')';
					print_r(json_encode($data));
					return;
				} else {
					continue;
				}
			}
		}
	}

	public function editMachine()
	{
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function deleteMachine()
	{
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data = $post_arr;
		print_r(json_encode($data));
	}

	public function getMetaMachine()
	{
		$arr = apache_request_headers(); //获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		$sql = "select id,rcr_meta,name from cluster_meta_info ";
		$this->load->model('Login_model');
		$res = $this->Login_model->getList($sql);
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = count($res);
		print_r(json_encode($data));
	}
	public function getStandbyMeta()
	{
		$arr = apache_request_headers(); //获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$cluster_id = $string['cluster_id'];
		$sql = "select id,name,nick_name from db_clusters where  memo!='' and memo is not null and status!='deleted' ";
		if(!empty($cluster_id)){
			$sql .= " and id!='$cluster_id'";
		}
		$this->load->model('Cluster_model');
		$res = $this->Cluster_model->getList($sql);
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = count($res);
		print_r(json_encode($data));
	}
	public function createMetaTable()
	{
		$arr = apache_request_headers(); //获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$user_name = $string['user_name'];
		$this->load->model('Login_model');
		//查user_id
		$user_sql = "select id from kunlun_user where name='$user_name';";
		$res_user = $this->Login_model->getList($user_sql);
		if (!empty($res_user)) {
			$user_id = $res_user[0]['id'];
		} else {
			$data['code'] = 500;
			$data['message'] = '该用户不存在';
			print_r(json_encode($data));
		}
		//查当前元数据ips
		$this->load->model('Cluster_model');
		$sql_meta = "select id,hostaddr,port from meta_db_nodes ";
		$res_meta = $this->Cluster_model->getList($sql_meta);
		$ips='';
		foreach ($res_meta as $row=>$value) {
			$ip='';$port='';
			foreach ($value as $key1=>$value1) {
				if($key1 == 'hostaddr'){
					$ip=$value1;
				}
				if($key1 == 'port') {
					$port = $value1;
				}
			}
			$ips.=$ip.':'.$port.',';
		}
		$rcr_meta=substr($ips,0,strlen($ips)-1);
		//如不存在创建表
		$sql = "CREATE TABLE IF NOT EXISTS cluster_meta_info (id int unsigned NOT NULL AUTO_INCREMENT,user_id int unsigned NOT NULL,name varchar(120) DEFAULT NULL, rcr_meta varchar(255) COLLATE utf8mb4_0900_as_cs DEFAULT '',when_created timestamp NULL DEFAULT CURRENT_TIMESTAMP,update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb3;";
		$res = $this->Login_model->updateList($sql);
		if ($res == 0) {
			//先查是否存在此条数据，存在更新 不存在insert
			$select_sql = "select id from cluster_meta_info where name='system'  and user_id='$user_id' and rcr_meta='$rcr_meta'";
			$res_select = $this->Login_model->getList($select_sql);
			 if (empty($res_select))  {
				$insert_sql = "insert into cluster_meta_info(user_id,name,rcr_meta) values ('$user_id','system','$rcr_meta'); ";
				$this->Login_model->updateList($insert_sql);
			}
		}
	}
	public function getMetaList()
	{
		$pageNo = $this->input->get('pageNo');
		$pageSize = $this->input->get('pageSize');
		$user_name = $this->input->get('user_name');
		$this->load->model('Login_model');
		$user_id='';
		$res_user = $this->Login_model->getUserId($user_name);
		if($res_user['code']==200){
			$user_id=$res_user['message'];
		}else{
			print_r(json_decode($res_user));
		}
		$start = ($pageNo - 1) * $pageSize;
		//获取用户数据
		$sql = "select * from cluster_meta_info where user_id='$user_id' ";
		$sql .= " order by id desc limit " . $pageSize . " offset " . $start;
		$res = $this->Login_model->getList($sql);
		if ($res === false) {
			$res = array();
		}
		$sql_total = "select count(id) as count from cluster_meta_info where user_id='$user_id' ";
		$res_total = $this->Login_model->getList($sql_total);
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
		print_r(json_encode($data));
	}
	public function setRCRMaxDalay()
	{
		$arr = apache_request_headers(); //获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$rcr_id = $string['rcr_id'];
		$maxtime = $string['maxtime'];
		$user_name = $string['user_name'];
		$this->load->model('Login_model');
		//查用户id
		$user_id='';
		$res_user = $this->Login_model->getUserId($user_name);
		if($res_user['code']==200){
			$user_id=$res_user['message'];
		}else{
			print_r(json_decode($res_user));
		}
		//如不存在创建表
		$sql = "CREATE TABLE IF NOT EXISTS rcr_shard_max_dalay (id int unsigned NOT NULL AUTO_INCREMENT,rcr_id int unsigned NOT NULL,user_id int unsigned NOT NULL,max_delay_time int NOT NULL DEFAULT '100',when_created timestamp NULL DEFAULT CURRENT_TIMESTAMP,update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb3;";
		$res = $this->Login_model->updateList($sql);
		if ($res == 0) {
			//先查是否存在此条数据，存在更新 不存在insert
			$select_sql = "select id from rcr_shard_max_dalay where rcr_id='$rcr_id'  and user_id='$user_id'";
			$res_select = $this->Login_model->getList($select_sql);
			if (!empty($res_select)) {
				$update_sql = "update rcr_shard_max_dalay set max_delay_time='$maxtime',update_time=now() where  rcr_id='$rcr_id' and user_id='$user_id'; ";
				$res_update = $this->Login_model->updateList($update_sql);
				if ($res_update == 1) {
					$data['code'] = 200;
					$data['message'] = '设置成功';
				} else {
					$data['code'] = 500;
					$data['message'] = '设置失败';
				}
				print_r(json_encode($data));
			} else {
				$insert_sql = "insert into rcr_shard_max_dalay(rcr_id,user_id,max_delay_time) values ('$rcr_id','$user_id','$maxtime'); ";
				$res_insert = $this->Login_model->updateList($insert_sql);
				if ($res_insert == 1) {
					$data['code'] = 200;
					$data['message'] = '设置成功';
				} else {
					$data['code'] = 500;
					$data['message'] = '设置失败';
				}
				print_r(json_encode($data));
			}
		} else {
			print_r(json_encode($res));
		}
	}
	public function findName()
	{
		//GET请求
		$serve = $_SERVER['QUERY_STRING'];
		$string = preg_split('/[=&]/', $serve);
		$arr = array();
		for ($i = 0; $i < count($string); $i += 2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$name = $arr['name'];
		$user_name = $arr['user_name'];
		$this->load->model('Login_model');
		//查用户id
		$user_id='';
		$res_user = $this->Login_model->getUserId($user_name);
		if($res_user['code']==200){
			$user_id=$res_user['message'];
		}else{
			print_r(json_decode($res_user));
		}

		$sql_total = "select count(*) as count from cluster_meta_info where user_id='$user_id' and name='$name' ";
		$res_total = $this->Login_model->getList($sql_total);
		$data['code'] = 200;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
		print_r(json_encode($data));
	}
	public function addMetaList()
	{
		$arr = apache_request_headers(); //获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$user_name = $string['user_name'];
		$ips = $string['ips'];
		$meta_name = $string['meta_name'];
		$this->load->model('Login_model');
		$res_token = $this->Login_model->getToken($token, 'D', $this->key);
		if (!empty($res_token)) {
			$sql = "select count(id) as count from kunlun_user where name='$res_token';";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				} else {
					$sql_user = "select id from kunlun_user where name='$user_name';";
					$res_user = $this->Login_model->getList($sql_user);
					$user_id=$res_user[0]['id'];
					if (!empty($res_user)) {
						//查数据是否存在该元数据
						$select_sql = "select id from cluster_meta_info where  user_id='$user_id' and rcr_meta='$ips'";
						$res_select = $this->Login_model->getList($select_sql);
						if(empty($res_select)){
							$sql_update = "INSERT INTO cluster_meta_info(`name`,`user_id`,`rcr_meta`) values ('$meta_name','$user_id','$ips');";
							$res_update = $this->Login_model->updateList($sql_update);
							if ($res_update == 1) {
								$data['code'] = 200;
								$data['message'] = '新增成功';
							} else {
								$data['code'] = 501;
								$data['message'] = '新增失败';
							}
						}else{
							$data['code'] = 201;
							$data['message'] = '该元数据已经存在，无需重复添加';
						}
						print_r(json_encode($data));
					} else if($res_user==false){
						$data['code'] = 201;
						$data['message'] = '用户不存在';
						print_r(json_encode($data));
					}else{
						$data['code'] = 500;
						$data['message'] = '系统繁忙';
						print_r(json_encode($data));
					}
				}
			}
		} else {
			$data['code'] = 500;
			$data['message'] = 'token错误';
			print_r(json_encode($data));
		}
	}
	public function deleteMeta()
	{
		//获取token
		$arr = apache_request_headers(); //获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		$id = $string['id'];
		//验证token
		$this->load->model('Login_model');
		$res_token = $this->Login_model->getToken($token, 'D', $this->key);
		if (!empty($res_token)) {
			$sql = "select count(id) as count from kunlun_user where name='$res_token';";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				} else {
					$sql_update = "delete from cluster_meta_info where id='$id';";
					$res_update = $this->Login_model->updateList($sql_update);
					if ($res_update == 1) {
						$data['code'] = 200;
						$data['message'] = '删除成功';
					}

					print_r(json_encode($data));
				}
			}
		} else {
			$data['code'] = 500;
			$data['message'] = 'token错误';
			print_r(json_encode($data));
		}
	}
	public function findMetaDB()
	{
		$this->load->model('Cluster_model');
		$sql = "select hostaddr,port from meta_db_nodes;  ";
		$res = $this->Cluster_model->getList($sql);
		$ips='';
		if ($res !== false) {
			foreach ($res as $key=>$row){
				$ips.=$res[$key]['hostaddr'].':'.$res[$key]['port'].',';
			}
		}
		$rcr_meta=substr($ips,0,strlen($ips)-1);
		$data['code'] = 200;
		$data['ips'] =$rcr_meta ;
		$data['res'] =$res ;
		print_r(json_encode($data));
	}
	public function findMetaCluster()
	{
		//获取token
		$arr = apache_request_headers(); //获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));
			return;
		}
		$string = json_decode(@file_get_contents('php://input'), true);
		$meta= $string['res'];
		$cluster_id = $string['cluster_id'];
		$meta_count = count($meta);
		$res=array();
		$meta=substr($meta,0,strlen($meta)-1);
		$meta_res=explode(',',$meta);
		$string='cluster_info';
		$this->load->model('Change_model');
		foreach ($meta_res as $knode => $vnode) {
			$ips=explode(':',$vnode);
			$sql = "select id,name,nick_name from db_clusters where  memo!='' and memo is not null and status!='deleted' ";
			if(!empty($cluster_id)){
				$sql .= " and id!='$cluster_id'";
			}
			$res_meta = $this->getMetaClusters($ips[0], $ips[1],$sql,$string);
			if ($res_meta['code'] == 200) {
				$res=$res_meta;
				break;
			} else if ($meta_count == ($knode + 1)) {
				$data['code'] = 500;
				$data['message'] = $res_meta[0] . $vnode['hostaddr'] . '(' . $vnode['port'] . ')';
				print_r(json_encode($data));
				return;
			} else {
				continue;
			}
		}
		$data['code'] = $res_meta['code'];
		$data['res'] = $res;
		print_r(json_encode($data));
	}
	public function getMetaClusters($ip, $port,$sql,$string)
	{
		$this->load->model('Change_model');
		$res = $this->Change_model->getAllMysql($ip, $port, 'pgx', 'pgx_pwd', 'kunlun_metadata_db', $sql,$string);
		return $res;
	}
	public function getMetaName($meta, $user)
	{
		$this->load->model('Login_model');
		$sql = "select name from cluster_meta_info where user_id='$user' and rcr_meta='$meta';  ";
		$res = $this->Login_model->getList($sql);
		return $res;
	}
	public function getRCRRelater(){
		$cluster_id=$this->input->get('cluster_id');
		$this->load->model('Cluster_model');
		$sql = "select count(*) as count from cluster_rcr_infos where status!='deleted' and (master_cluster_id='$cluster_id' or  slave_cluster_id='$cluster_id');  ";
		$res = $this->Cluster_model->getList($sql);
		$data['total'] =$res ? (int) $res[0]['count'] : 0;
		print_r(json_encode($data));
	}
	public function findRcrRelat()
	{
		$meta_db=$this->input->get('meta_db');
		$this->load->model('Cluster_model');
		$sql = "select count(*) as count from cluster_rcr_infos  where status!='deleted' and (master_rcr_meta='$meta_db' or slave_rcr_meta='$meta_db');";
		$res = $this->Cluster_model->getList($sql);
		$data['code'] = 200;
		$data['total'] = $res ? (int) $res[0]['count'] : 0;
		print_r(json_encode($data));
	}

}
