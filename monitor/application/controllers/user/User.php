<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
		header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
		header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
		header('Access-Control-Allow-Headers:x-requested-with,content-type,accessToken');//允许接受token
		header('Content-Type: application/json;charset=utf-8');
		$this->key=$this->config->item('key');
		$this->db=$this->load->database('role',true);
	}
	public function userList(){
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
		//获取用户数据
		$this->load->model('Login_model');
		$sql="select id, name as username,password,email,phone_number,wechat_number,update_time from kunlun_user";
		if(!empty($username)){
			$sql .=" where  name like '%$username%'";
		}
		$sql .=" order by id desc limit ".$pageSize." offset ".$start;
		$res=$this->Login_model->getList($sql);
		if($res===false){
			$res=array();
		}
		$total_sql="select count(id) as count from kunlun_user ";
		if(!empty($username)){
			$total_sql .=" where  name like '%$username%'";
		}
		$res_total=$this->Login_model->getList($total_sql);
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
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
		$user_name = $string['username'];
		$password = $string['password'];
		$phone_number = $string['phone_number'];
		$email = $string['email'];
		$wechat_number = $string['wechat_number'];
		if (empty($user_name)||empty($password)) {
			$data['code'] = 201;
			$data['message'] = '账户或密码不能为空';
			print_r(json_encode($data));return;
		}
		//验证token
		$this->load->model('Login_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)){
			$sql="select count(id) as count from kunlun_user where name='$res_token';";
			$res=$this->Login_model->getList($sql);
			if(!empty($res)){
				if($res[0]['count']==0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				}else{
					//验证用户名是否存在
					$sql_user="select count(id) as count from kunlun_user where name='$user_name';";
					$res_user=$this->Login_model->getList($sql_user);
					if(!empty($res_user)){
						if($res_user[0]['count']==0){
							$sql_update="INSERT INTO kunlun_user(name,password,email,phone_number,wechat_number) values ('$user_name','$password','$email','$phone_number','$wechat_number');";
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
							$data['message'] = '该用户已经存在';
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
		$id = $string['id'];
		$user_name = $string['username'];
		$password = $string['password'];
		$phone_number = $string['phone_number'];
		$email = $string['email'];
		$wechat_number = $string['wechat_number'];
		if (empty($user_name)||empty($user_name)) {
			$data['code'] = 201;
			$data['message'] = '账户或密码不能为空';
			print_r(json_encode($data));return;
		}
		//验证token
		$this->load->model('Login_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)){
			$sql="select count(id) as count from kunlun_user where name='$res_token';";
			$res=$this->Login_model->getList($sql);
			if(!empty($res)){
				if($res[0]['count']==0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				}else{
					$sql_update="update kunlun_user set name='$user_name',password='$password',email='$email',phone_number='$phone_number',wechat_number='$wechat_number',update_time=now() where id='$id';";
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
			$sql="select count(id) as count from kunlun_user where name='$res_token';";
			$res=$this->Login_model->getList($sql);
			if(!empty($res)){
				if($res[0]['count']==0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));
				}else{
					//先查授权信息
					$select_sql="select count(*) as count from kunlun_role_assign where user_id='$id';";
					$res_select=$this->Login_model->updateList($select_sql);
					$this->db->trans_start();
					if(!empty($res_select)){
						if($res_select>0) {
							$sql_update="delete from kunlun_user where id='$id';";
							$res_update=$this->Login_model->updateList($sql_update);
							if($res_update==1){
								$data['code'] = 200;
								$data['message'] = '删除成功';
								//删除授权信息
								$sql_assign="delete from kunlun_role_assign where user_id='$id';";
								$res_assign=$this->Login_model->updateList($sql_assign);
								$this->db->trans_complete();
							}else{
								$data['code'] = 501;
								$data['message'] = '删除失败';
								$this->db->trans_rollback();
							}
						}else{
							$sql_update="delete from kunlun_user where id='$id';";
							$res_update=$this->Login_model->updateList($sql_update);
							if($res_update==1){
								$data['code'] = 200;
								$data['message'] = '删除成功';
								$this->db->trans_complete();
							}else{
								$data['code'] = 501;
								$data['message'] = '删除失败';
							}
						}
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
	public function checkMobile(){
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
		$phone_number = $string['phone_number'];
		//验证token
		$this->load->model('Login_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		if(!empty($res_token)) {
			$sql = "select id from kunlun_user where phone_number='$phone_number';";
			$res = $this->Login_model->getList($sql);
			if (empty($res)) {
				$data['code'] = 200;
			}else{
				$data['code'] = 501;
				$data['message'] = '手机号码重复';
			}
			print_r(json_encode($data));
		}
	}
}
