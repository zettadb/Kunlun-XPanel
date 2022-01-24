<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		//session_start();
		parent::__construct();
		//打开重定向
		$this->load->helper('form');
		$this->load->helper('url');
		header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
		header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
		header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
		header('Access-Control-Allow-Headers:x-requested-with,content-type,accessToken');//允许接受token
		//header('Content-Type: text/html;charset=utf-8');
		//header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
		$this->key=$this->config->item('key');
	}
	public function userCheck(){
		$string=json_decode(@file_get_contents('php://input'),true);
		$user_name = $string['username'];
		$password = $string['password'];
		if (empty($user_name)||empty($user_name)) {
			$data['code'] = 201;
			$data['message'] = '账户或密码不能为空';
			print_r(json_encode($data));return;
		}
		//验证用户名和密码
		$this->load->model('Login_model');
		if($user_name=='super_dba'&&$password=='super_dba'){
			$sql_super="select count(id) as count,id from kunlun_user where name='$user_name' and password='$password';";
			$res_super=$this->Login_model->getList($sql_super);
		   if($res_super[0]['count']==1){
			   $token=$this->Login_model->getToken($user_name,'E',$this->key);
			   $data['accessToken'] =$token;
			   $data['num'] = 2;
			   $data['code'] = 200;
			   $data['userName'] = $user_name;
			   $data['message'] ='修改密码' ;
			   print_r(json_encode($data));return;
		   }
		}
		$sql="select count(id) as count,id from kunlun_user where name='$user_name' and password='$password';";
		$res=$this->Login_model->getList($sql);
		if(!empty($res)){
			if($res[0]['count']>1){
				$token=$this->Login_model->getToken($user_name,'E',$this->key);
				$data['accessToken'] =$token;
				$data['num'] = $res[0]['count'];
				$data['code'] = 200;
				$data['message'] = '用户重复';
				print_r(json_encode($data));
			}elseif($res[0]['count']==1){
				$userId=$res[0]['id'];
				//获取角色id
				$sql_role="select role_id,cluster_id from kunlun_cluster_privilege where user_id='$userId';";
				$res_role=$this->Login_model->getList($sql_role);
				if(!empty($res_role)) {
					$role_id = $res_role[0]['role_id'];
					$cluster_id = $res_role[0]['cluster_id'];
					if($cluster_id<=0){
						$token=$this->Login_model->getToken($user_name,'E',$this->key);
						$data['accessToken'] =$token;
						$data['code'] = 200;
						$data['num'] = 1;
						$data['userName'] = $user_name;
						$data['message'] = 'success';
						print_r(json_encode($data));
					}
				}
			}else{
				$data['code'] = 500;
				$data['message'] = '账户或密码错误';
				print_r(json_encode($data));
			}
		} else {
			$data['code'] = 500;
			$data['message'] = '账户或密码错误';
			print_r(json_encode($data));
		}
	}
	public function changePassword()
	{
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		//echo $arr["accessToken"];//输出Token
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
		if (empty($user_name)||empty($user_name)) {
			$data['code'] = 201;
			$data['message'] = '账户或密码不能为空';
			print_r(json_encode($data));return;
		}
		//验证token
		$this->load->model('Login_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		//var_dump($res_token);exit;
		if(!empty($res_token)){
			$sql="select count(id) as count from kunlun_user where name='$res_token';";
			$res=$this->Login_model->getList($sql);
			if(!empty($res)){
				if($res[0]['count']==0) {
					$data['code'] = 500;
					$data['message'] = 'token错误';
					print_r(json_encode($data));return;
				}else{
					//验证用户名是否存在
					$sql_user="select count(id) as count from kunlun_user where name='$user_name' and password='$password'";
					$res_user=$this->Login_model->getList($sql_user);
					if(!empty($res_user)){
						if($res_user[0]['count']==0){
							$sql_update="update kunlun_user set password='$password' where name='$user_name';";
							$res_update=$this->Login_model->updateList($sql_update);
							//print_r($res_update);exit;
							if($res_update==1){
								$data['code'] = 200;
								$data['message'] = '修改密码成功';
							}else{
								$data['code'] = 501;
								$data['message'] = '修改密码失败';
							}
						}elseif($res_user[0]['count']==1){
							$data['code'] = 500;
							$data['message'] = '新旧密码不能一致';
						}else{
							$data['code'] = 501;
							$data['message'] = '存在多条相同的账户数据';
						}
						print_r(json_encode($data));
					} else {
						$data['code'] = 500;
						$data['message'] = '账户或密码错误';
						print_r(json_encode($data));
					}
				}
			}
		}else{
			$data['code'] = 500;
			$data['message'] = 'token错误';
			print_r(json_encode($data));return;
		}
	}
}
