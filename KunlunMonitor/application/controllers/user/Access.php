<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Access extends CI_Controller {
	public function __construct(){
		parent::__construct();
		header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
		header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
		header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
		header('Access-Control-Allow-Headers:x-requested-with,content-type,Token');//允许接受token
		header('Content-Type: application/json;charset=utf-8');
		$this->config->load('myconfig');
		$this->key=$this->config->item('key');
	}
	public function accessList(){
		//GET请求
		$serve=$_SERVER['QUERY_STRING'];
		$string=preg_split('/[=&]/',$serve);
		$arr=array();
		for($i=0;$i<count($string);$i+=2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$pageNo=$arr['pageNo'];
		$pageSize=$arr['pageSize'];
		$start=($pageNo - 1) * $pageSize;
		$user_name=$arr['user_name'];
		//获取用户数据
		$this->load->model('Login_model');
		if($user_name=='super_dba'){
			$sql="select * from kunlun_role_assign";
			$sql .=" order by update_time desc limit ".$pageSize." offset ".$start;
		}else{
			$sql="select a.* from kunlun_role_assign as a left join kunlun_user as u on a.user_id=u.id where u.name='$user_name'";
			$sql .=" order by a.update_time desc limit ".$pageSize." offset ".$start;
		}
		$res=$this->Login_model->getList($sql);
		if($user_name=='super_dba') {
			$total_sql = "select count(user_id) as count from kunlun_role_assign ";
		}else{
			$total_sql = "select count(a.user_id) as count from kunlun_role_assign as a left join kunlun_user as u on a.user_id=u.id where u.name='$user_name'";
		}
		$res_total=$this->Login_model->getList($total_sql);
		if($res===false){
			$res=array();
		}
		//在res上追加参数
		if(!empty($res)){
		foreach ($res as $row=>$value){
			foreach ($value as $key2 => $value2) {
				//用户名
				if ($key2 == 'user_id') {
					if(!empty($value2)) {
						$username = $this->getUserName($value2);
						$res[$row]['username'] = $username;
					}else{
						$res[$row]['username'] = '';
					}
				}
				//角色名称
				if ($key2 == 'role_id') {
					if(!empty($value2)) {
						$rolename = $this->getRoleName($value2);
						$res[$row]['role_name'] = $rolename;
					}else{
						$res[$row]['role_name'] = '';
					}
				}
				//是否应用于所有集群
				if ($key2 == 'apply_all_cluster') {
					$res[$row]['radio'] = $value2;
				}
				//集群id
				if ($key2 == 'affected_clusters') {
					if(!empty($value2)){
						$res[$row]['checkedCluster'] = $this->getEffectedCluser($value2);
						//$res[$row]['checkedCluster'] = explode(",", $value2);;
					}else{
						$res[$row]['checkedCluster'] = '';
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
	//获取集群信息
	public function getCluster(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
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
					//获取集群数据
					$sql="select id,name,nick_name from db_clusters where memo!='' and memo is not null";
					$this->load->model('Cluster_model');
					$res=$this->Cluster_model->getList($sql);
					$data['code'] = 200;
					$data['list'] = $res;
					$data['total'] = $res ? count($res) : 0;
					print_r(json_encode($data));
				}
			}
		}
	}
	public function add(){
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
		$start_ts = $string['start_ts'];
		$end_ts= $string['end_ts'];
		$role_name = $string['role_name'];
		$username = $string['username'];
		$apply_all_cluster = $string['radio'];
		$affected_clusters = $string['checkedCluster'];
		$valid_period = $string['valid_period'];
		if (empty($role_name)||empty($valid_period)) {
			$data['code'] = 201;
			$data['message'] = '角色类型或有效期类型不能为空';
			print_r(json_encode($data));return;
		}
		if (!empty($valid_period)&&$valid_period=='from_to') {
			if (empty($start_ts)&&empty($end_ts)) {
				$data['code'] = 201;
				$data['message'] = '有效期类型为时间段时，两个时间不能同时为空';
				print_r(json_encode($data));return;
			}
		}
		if(empty($start_ts)){
			$start_ts="NULL";
		}else{
			$start_ts="'".$start_ts."'";
		}
		if(empty($end_ts)){
			$end_ts="NULL";
		}else{
			$end_ts="'".$end_ts."'";
		}
		if(empty($affected_clusters)){
			$affected_clusters='';
		}else{
			$affected_clusters=implode(',',$affected_clusters);
		}
		//验证token
		$this->load->model('Login_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)){
			$sql="select count(id) as count,id from kunlun_user where name='$res_token';";
			$res=$this->Login_model->getList($sql);
			if(!empty($res)){
				if($res[0]['count']==0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				}else{
					//验证权限是否重复添加
					$sql_role_assign="select count(role_id) as count from kunlun_role_assign where role_id='$role_name' and user_id='$username';";
					$res_role_assign=$this->Login_model->getList($sql_role_assign);
					if($res_role_assign[0]['count']==0){
						$sql_update="INSERT INTO kunlun_role_assign(user_id,role_id,valid_period,start_ts,end_ts,apply_all_cluster,affected_clusters) values ('$username','$role_name','$valid_period',$start_ts,$end_ts,$apply_all_cluster,'$affected_clusters');";
						//print_r($sql_update);exit;
						$res_update=$this->Login_model->updateList($sql_update);
						if($res_update==1){
							$data['code'] = 200;
							$data['message'] = '新增成功';
						}else{
							$data['code'] = 501;
							$data['message'] = '新增失败';
						}
					}else{
						$data['code'] = 501;
						$data['message'] = '该授权已经存在，请前往编辑';
					}
					print_r(json_encode($data));

				}
			}
		}else{
			$data['code'] = 500;
			$data['message'] = 'token错误';
			print_r(json_encode($data));
		}
	}
	public function edit(){
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
		//print_r($string);exit;
		$start_ts = $string['start_ts'];
		$end_ts= $string['end_ts'];
		$role_id = $string['role_id'];
		$user_id = $string['user_id'];
		$apply_all_cluster = $string['radio'];
		$affected_clusters = $string['checkedCluster'];
		$valid_period = $string['valid_period'];
		if (!empty($valid_period)&&$valid_period=='from_to') {
			if (empty($start_ts)&&empty($end_ts)) {
				$data['code'] = 201;
				$data['message'] = '有效期类型为时间段时，两个时间不能同时为空';
				print_r(json_encode($data));return;
			}
		}
		if(empty($start_ts)){
			$start_ts="NULL";
		}else{
			$start_ts="'".$start_ts."'";
		}
		if(empty($end_ts)){
			$end_ts="NULL";
		}else{
			$end_ts="'".$end_ts."'";
		}
		if($apply_all_cluster==1){
			$affected_clusters="";
		}
		if($apply_all_cluster==2) {
			if (empty($affected_clusters)) {
				$affected_clusters = "";
			} else {
				$affected_clusters = implode(',', $affected_clusters);
			}
		}
		//验证token
		$this->load->model('Login_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)){
			$sql="select count(id) as count,id from kunlun_user where name='$res_token';";
			$res=$this->Login_model->getList($sql);
			if(!empty($res)){
				if($res[0]['count']==0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				}else{
					$sql_update="update kunlun_role_assign set valid_period='$valid_period',start_ts=$start_ts,end_ts=$end_ts,apply_all_cluster=$apply_all_cluster,affected_clusters='$affected_clusters',update_time=now() where user_id='$user_id' and role_id='$role_id'";
					$res_update=$this->Login_model->updateList($sql_update);
					if($res_update==1){
						$data['code'] = 200;
						$data['message'] = '编辑成功';
					}else{
						$data['code'] = 501;
						$data['message'] = '编辑失败';
					}
					print_r(json_encode($data));
				}
			}
		}else{
			$data['code'] = 500;
			$data['message'] = 'token错误';
			print_r(json_encode($data));
		}
	}
	public function delete(){
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
		$userid = $string['user_id'];
		$roleid = $string['role_id'];
		//验证token
		$this->load->model('Login_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)){
			$sql="select count(id) as count,id from kunlun_user where name='$res_token';";
			$res=$this->Login_model->getList($sql);
			if(!empty($res)){
				if($res[0]['count']==0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				}else{
					//查看该账户是否有角色的权限
					$user_id=$res[0]['id'];
					$role_sql="select role_id from kunlun_role_assign where user_id='$user_id';";
					$res_role=$this->Login_model->getList($role_sql);
					if($res_role[0]['role_id']===NULL) {
						$data['code'] = 500;
						$data['message'] = '该账户不是角色用户';
						print_r(json_encode($data));return;
					}
					$role_id=$res_role[0]['role_id'];
					$sql_role="select role_drop_priv from kunlun_role_privilege where id='$role_id';";
					$res_role_priv=$this->Login_model->getList($sql_role);
					if($res_role_priv[0]['role_drop_priv']!=='Y') {
						$data['code'] = 500;
						$data['message'] = '该账户不具备删除角色权限';
						print_r(json_encode($data));return;
					}
					$sql_update="delete from kunlun_role_assign where user_id='$userid' and role_id='$roleid';";
					$res_update=$this->Login_model->updateList($sql_update);
					if($res_update==1){
						$data['code'] = 200;
						$data['message'] = '删除成功';
					}else{
						$data['code'] = 501;
						$data['message'] = '删除失败';
					}
					print_r(json_encode($data));
				}
			}
		}else{
			$data['code'] = 500;
			$data['message'] = 'token错误';
			print_r(json_encode($data));
		}
	}
	public function queryall(){
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
		$id = $string['id'];
		//验证token
		$this->load->model('Login_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token';";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				} else {
					//获取用户数据
					$sql="select id,role_name as name from kunlun_role_privilege";
					$res=$this->Login_model->getList($sql);
					$data['code'] = 200;
					$data['list'] = $res;
					print_r(json_encode($data));
				}
			}
		}
	}
	public function getAllUser(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//验证token
		$this->load->model('Login_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token';";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				} else {
					//获取用户数据
					$sql="select id,name from kunlun_user";
					$res=$this->Login_model->getList($sql);
					$data['code'] = 200;
					$data['list'] = $res;
					print_r(json_encode($data));
				}
			}
		}
	}
	public function getUserName($id){
		//获取用户数据
		$sql="select name from kunlun_user where id='$id'";
		$this->load->model('Login_model');
		$res=$this->Login_model->getList($sql);
		return $res[0]['name'];
	}
	public function getRoleName($id){
		//获取角色数据
		$this->load->model('Login_model');
		$sql="select role_name from kunlun_role_privilege where id='$id'";
		//print_r($sql);exit;
		$res=$this->Login_model->getList($sql);
		if(empty($res)){
			$role_name='';
		}else{
			$role_name=$res[0]['role_name'];
		}
		return $role_name;
	}
	public function getEffectedCluser($arr){
		//获取集群数据
		$this->load->model('Cluster_model');
		$sql="select id,name from db_clusters where id in ($arr)";
		$res=$this->Cluster_model->getList($sql);
		return $res;
	}
	public function getPremisson(){
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
		$userName= $string['userName'];
		//验证token
		$this->load->model('Login_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token';";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				} else {
					$useid=$res[0]['id'];
					//一个用户对应一个权限的设置
					$sql="select role_id from kunlun_role_assign where user_id = '$useid' order by user_id desc limit 1";
					$res_role_priv=$this->Login_model->getList($sql);
					if(!empty($res_role_priv[0]['role_id'])) {
						$roleid=$res_role_priv[0]['role_id'];
						//获取数据
						$sql="select user_grant_priv from kunlun_role_privilege where id = '$roleid'";
						$res=$this->Login_model->getList($sql);
						$data['code'] = 200;
						$data['list'] = $res;
						print_r(json_encode($data));
					}
				}
			}
		}
	}
	public function getUserPremisson(){
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
		$userName= $string['userName'];
		//验证token
		$this->load->model('Login_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token';";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				} else {
					$useid=$res[0]['id'];
					//一个用户对应一个权限的设置
					$sql="select role_id from kunlun_role_assign where user_id = '$useid' order by user_id desc limit 1";
					$res_role_priv=$this->Login_model->getList($sql);
					if(!empty($res_role_priv[0]['role_id'])) {
						$roleid=$res_role_priv[0]['role_id'];
						//获取数据
						$sql="select user_add_priv,user_drop_priv,user_edit_priv from kunlun_role_privilege where id = '$roleid'";
						$res=$this->Login_model->getList($sql);
						$data['code'] = 200;
						$data['list'] = $res;
						print_r(json_encode($data));
					}
				}
			}
		}
	}
	public function getRolePremisson(){
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
		$userName= $string['userName'];
		//验证token
		$this->load->model('Login_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)) {
			$sql = "select count(id) as count,id from kunlun_user where name='$res_token';";
			$res = $this->Login_model->getList($sql);
			if (!empty($res)) {
				if ($res[0]['count'] == 0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				} else {
					$useid=$res[0]['id'];
					//一个用户对应一个权限的设置
					$sql="select role_id from kunlun_role_assign where user_id = '$useid' order by user_id desc limit 1";
					$res_role_priv=$this->Login_model->getList($sql);
					if(!empty($res_role_priv[0]['role_id'])) {
						$roleid=$res_role_priv[0]['role_id'];
						//获取数据
						$sql="select role_add_priv,role_drop_priv,role_edit_priv from kunlun_role_privilege where id = '$roleid'";
						$res=$this->Login_model->getList($sql);
						$data['code'] = 200;
						$data['list'] = $res;
						print_r(json_encode($data));
					}
				}
			}
		}
	}
}
