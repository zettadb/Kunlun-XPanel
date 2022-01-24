<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role extends CI_Controller {

	public function __construct()
	{
		//session_start();
		parent::__construct();

		header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
		header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
		header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
		header('Access-Control-Allow-Headers:x-requested-with,content-type,accessToken');//允许接受token
		header('Content-Type: application/json;charset=utf-8');
		//header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
		$this->key=$this->config->item('key');
	}
	public function roleList(){
		//GET请求
		$serve=$_SERVER['QUERY_STRING'];
		$string=preg_split('/[=&]/',$serve);
		$arr=array();
		for($i=0;$i<count($string);$i+=2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$pageNo=$arr['pageNo'];
		$pageSize=$arr['pageSize'];
		//$username=$arr['role_name'];
		$start=$pageNo-1;
		//print_r($pageSize);exit;
		//获取用户数据
		$this->load->model('Login_model');
		$sql="select id,role_name,valid_period,start_ts,end_ts,update_time from kunlun_role";
//		if(!empty($username)){
//			$sql .=" where role_name like '%$username%'";
//		}
		$sql .=" order by id desc limit ".$pageSize." offset ".$start;
		//print_r($sql);exit;
		$res=$this->Login_model->getList($sql);
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = count($res);
		print_r(json_encode($data));
	}
	public function add(){
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
		$start_ts = $string['start_ts'];
		$end_ts= $string['end_ts'];
		$role_name = $string['role_name'];
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
		if(empty($start_ts)||empty($end_ts)){
			$start_ts='0000-00-00 00:00:00';
			$end_ts='0000-00-00 00:00:00';
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
					//查看该账户是否有增加角色的权限
					$user_id=$res[0]['id'];
					$role_id="select role_id  from kunlun_cluster_privilege where user_id='$user_id';";
					$res_role=$this->Login_model->getList($role_id);
					if($res_role[0]['role_id']===NULL) {
						$data['code'] = 500;
						$data['message'] = '该账户不是角色用户';
						print_r(json_encode($data));return;
					}
					$role_id=$res_role[0]['role_id'];
					$sql_role="select role_add_priv from kunlun_role_privilege where role_id='$role_id';";
					//print_r($sql_role);exit;
					$res_role_priv=$this->Login_model->getList($sql_role);
					if($res_role_priv[0]['role_add_priv']!=='Y') {
						$data['code'] = 500;
						$data['message'] = '该账户不具备新增角色权限';
						print_r(json_encode($data));return;
					}
					//验证角色类型是否存在
					$sql_user="select count(role_name) as count from kunlun_role where role_name='$role_name';";
					$res_user=$this->Login_model->getList($sql_user);
					if(!empty($res_user)){
						if($res_user[0]['count']==0){
							//获得最大id值
							$sql_id="select max(id) as id from kunlun_role ";
							$res_maxid=$this->Login_model->getList($sql_id);
							if($res_maxid[0]['id']===null){
								$maxid=0;
							}else{
								$maxid=$res_maxid[0]['id']+1;
							}
							$sql_insert="INSERT INTO kunlun_role(id,role_name,valid_period,start_ts,end_ts) values ('$maxid','$role_name','$valid_period','$start_ts','$end_ts');";
							$res_insert=$this->Login_model->updateList($sql_insert);
							if($res_insert==1){
								$data['code'] = 200;
								$data['message'] = '新增成功';
							}else{
								$data['code'] = 501;
								$data['message'] = '新增失败';
							}
						}else{
							$data['code'] = 501;
							$data['message'] = '该角色已经存在';
						}
						print_r(json_encode($data));
					} else {
						$data['code'] = 500;
						$data['message'] = '系统繁忙';
						print_r(json_encode($data));
					}
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
		$token=$arr["accessToken"];
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
		$valid_period = $string['valid_period'];
		$id = $string['id'];
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
		if(empty($start_ts)||empty($end_ts)){
			$start_ts='0000-00-00 00:00:00';
			$end_ts='0000-00-00 00:00:00';
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
					//查看该账户是否有角色的权限
					$user_id=$res[0]['id'];
					$role_id="select role_id  from kunlun_cluster_privilege where user_id='$user_id';";
					$res_role=$this->Login_model->getList($role_id);
					if($res_role[0]['role_id']===NULL) {
						$data['code'] = 500;
						$data['message'] = '该账户不是角色用户';
						print_r(json_encode($data));return;
					}
					$role_id=$res_role[0]['role_id'];
					$sql_role="select role_edit_priv from kunlun_role_privilege where role_id='$role_id';";
					$res_role_priv=$this->Login_model->getList($sql_role);
					if($res_role_priv[0]['role_edit_priv']!=='Y') {
						$data['code'] = 500;
						$data['message'] = '该账户不具备编辑角色权限';
						print_r(json_encode($data));return;
					}
					$sql_update="update kunlun_role set role_name='$role_name',valid_period='$valid_period',start_ts='$start_ts',end_ts='$end_ts',update_time=now() where id='$id';";
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
		$token=$arr["accessToken"];
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
					$role_id="select role_id from kunlun_cluster_privilege where user_id='$user_id';";
					$res_role=$this->Login_model->getList($role_id);
					if($res_role[0]['role_id']===NULL) {
						$data['code'] = 500;
						$data['message'] = '该账户不是角色用户';
						print_r(json_encode($data));return;
					}
					$role_id=$res_role[0]['role_id'];
					$sql_role="select role_drop_priv from kunlun_role_privilege where role_id='$role_id';";
					$res_role_priv=$this->Login_model->getList($sql_role);
					if($res_role_priv[0]['role_drop_priv']!=='Y') {
						$data['code'] = 500;
						$data['message'] = '该账户不具备删除角色权限';
						print_r(json_encode($data));return;
					}
					$sql_update="delete from kunlun_role where id='$id';";
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
		$token=$arr["accessToken"];
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
					$this->load->model('Login_model');
					$sql="select id,role_name as name from kunlun_role_privilege";
					$res=$this->Login_model->getList($sql);
					$data['code'] = 200;
					$data['list'] = $res;
					print_r(json_encode($data));
				}
			}
		}
	}
	public function grantAuth(){
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
					$this->load->model('Login_model');
					$sql="select id,name from kunlun_user";
					$res=$this->Login_model->getList($sql);
					$data['code'] = 200;
					$data['list'] = $res;
					print_r(json_encode($data));
				}
			}
		}
	}
}
