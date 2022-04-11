<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Login extends CI_Controller {
	public function __construct()
	{
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
				$sql_role="select role_id,apply_all_cluster,affected_clusters from kunlun_role_assign where user_id='$userId' and valid_period='permanent' ";
				$sql_role.=" union select role_id,apply_all_cluster,affected_clusters from kunlun_role_assign where user_id='$userId' and valid_period='from_to' and start_ts is not null and start_ts<now() and end_ts is null";
				$sql_role.=" union select role_id,apply_all_cluster,affected_clusters from kunlun_role_assign where user_id='$userId' and valid_period='from_to' and end_ts is not null and end_ts>now() and start_ts is null";
				$sql_role.=" union select role_id,apply_all_cluster,affected_clusters from kunlun_role_assign where user_id='$userId' and valid_period='from_to' and end_ts is not null  and start_ts is not null and start_ts<now() and end_ts>now();";
				$res_role=$this->Login_model->getList($sql_role);
				$token=$this->Login_model->getToken($user_name,'E',$this->key);
				$apply_all_cluster='';
				$affected_clusters='';
				if(!empty($res_role)) {
					//如果只有一条数据
					$role_count=count($res_role);
					$arr=array();
					if($role_count==1){
						$role_id = $res_role[0]['role_id'];
						$apply_all_cluster = $res_role[0]['apply_all_cluster'];
						$affected_clusters = $res_role[0]['affected_clusters'];
					}elseif($role_count>1){
						$role_id='';
						foreach ($res_role as $key=>$row){
							foreach ($row as $key2=>$value2){
								if($key2=='role_id'){
									if(!empty($value2)){
										$role_id.=$value2.',';
									}
								}
								//应用集群，求并集
								if($key2=='apply_all_cluster'){
									if(!empty($value2)&&$value2==1){
										$apply_all_cluster=$row['apply_all_cluster'];
										$affected_clusters=$row['affected_clusters'];
										break;
									}elseif(!empty($value2)&&$value2==2){
										$apply_all_cluster=$row['apply_all_cluster'];
										$affected_clusters.=$row['affected_clusters'].',';
									}
								}
							}
						}
						$role_id=substr($role_id,0,-1);
					}
					//获取该用户权限（多条记录求并集）
					$sql_priv="select user_add_priv,user_drop_priv,user_grant_priv,user_edit_priv,role_add_priv,role_drop_priv,role_edit_priv,machine_priv,cluster_creata_priv,cluster_drop_priv,shard_create_priv,shard_drop_priv,storage_node_create_priv,storage_node_drop_priv,compute_node_create_priv,compute_node_drop_priv,machine_add_priv,machine_drop_priv,backup_service_enable_priv,backup_service_disable_priv,storage_enable_priv,storage_disable_priv,compute_enable_priv,compute_disable_priv,backup_priv,restore_priv,expand_cluster_priv,shrink_cluster_priv from kunlun_role_privilege where id in($role_id);";
					$res_priv=$this->Login_model->getList($sql_priv);
					if(!empty($res_priv)){
						$priv_count=count($res_priv);
						if($priv_count==1){
							$data['priv'] = $res_priv[0];
						}elseif($priv_count>1){
							$arr=array();
							//初始化
							$user_add_priv='N';
							$user_drop_priv='N';
							$user_edit_priv='N';
							$user_grant_priv='N';
							$role_add_priv='N';
							$role_drop_priv='N';
							$role_edit_priv='N';
							$cluster_creata_priv='N';
							$cluster_drop_priv='N';
							$backup_priv='N';
							$restore_priv='N';
							$expand_cluster_priv='N';
							$shrink_cluster_priv='N';
							$storage_node_create_priv='N';
							$storage_node_drop_priv='N';
							$storage_enable_priv='N';
							$storage_disable_priv='N';
							$shard_create_priv='N';
							$shard_drop_priv='N';
							$compute_node_create_priv='N';
							$compute_node_drop_priv='N';
							$compute_enable_priv='N';
							$compute_disable_priv='N';
							$machine_add_priv='N';
							$machine_priv='N';
							$machine_drop_priv='N';
							$backup_service_enable_priv='N';
							$backup_service_disable_priv='N';
							foreach ($res_priv as $key=>$value){
								foreach ($value as $key2=>$value2){
									if($key2=='user_add_priv'){
										if($value2=='Y'){
											$user_add_priv=$value2;
										}
									}
									if($key2=='user_drop_priv'){
										if($value2=='Y'){
											$user_drop_priv=$value2;
										}
									}
									if($key2=='user_edit_priv'){
										if($value2=='Y'){
											$user_edit_priv=$value2;
										}
									}
									if($key2=='user_grant_priv'){
										if($value2=='Y'){
											$user_grant_priv=$value2;
										}
									}
									if($key2=='role_add_priv'){
										if($value2=='Y'){
											$role_add_priv=$value2;
										}
									}
									if($key2=='role_drop_priv'){
										if($value2=='Y'){
											$role_drop_priv=$value2;
										}
									}
									if($key2=='role_edit_priv'){
										if($value2=='Y'){
											$role_edit_priv=$value2;
										}
									}
									if($key2=='cluster_creata_priv'){
										if($value2=='Y'){
											$cluster_creata_priv=$value2;
										}
									}
									if($key2=='cluster_drop_priv'){
										if($value2=='Y'){
											$cluster_drop_priv=$value2;
										}
									}
									if($key2=='backup_priv'){
										if($value2=='Y'){
											$backup_priv=$value2;
										}
									}
									if($key2=='restore_priv'){
										if($value2=='Y'){
											$restore_priv=$value2;
										}
									}
									if($key2=='expand_cluster_priv'){
										if($value2=='Y'){
											$expand_cluster_priv=$value2;
										}
									}if($key2=='shrink_cluster_priv'){
										if($value2=='Y'){
											$shrink_cluster_priv=$value2;
										}
									}if($key2=='expand_cluster_priv'){
										if($value2=='Y'){
											$expand_cluster_priv=$value2;
										}
									}
									if($key2=='storage_node_create_priv'){
										if($value2=='Y'){
											$storage_node_create_priv=$value2;
										}
									}
									if($key2=='storage_node_drop_priv'){
										if($value2=='Y'){
											$storage_node_drop_priv=$value2;
										}
									}
									if($key2=='storage_enable_priv'){
										if($value2=='Y'){
											$storage_enable_priv=$value2;
										}
									}if($key2=='storage_enable_priv'){
										if($value2=='Y'){
											$storage_enable_priv=$value2;
										}
									}
									if($key2=='storage_disable_priv'){
										if($value2=='Y'){
											$storage_disable_priv=$value2;
										}
									}
									if($key2=='shard_create_priv'){
										if($value2=='Y'){
											$shard_create_priv=$value2;
										}
									}
									if($key2=='shard_drop_priv'){
										if($value2=='Y'){
											$shard_drop_priv=$value2;
										}
									}
									if($key2=='compute_node_create_priv'){
										if($value2=='Y'){
											$compute_node_create_priv=$value2;
										}
									}
									if($key2=='compute_node_drop_priv'){
										if($value2=='Y'){
											$compute_node_drop_priv=$value2;
										}
									}
									if($key2=='compute_enable_priv'){
										if($value2=='Y'){
											$compute_enable_priv=$value2;
										}
									}
									if($key2=='compute_disable_priv'){
										if($value2=='Y'){
											$compute_disable_priv=$value2;
										}
									}if($key2=='machine_add_priv'){
										if($value2=='Y'){
											$machine_add_priv=$value2;
										}
									}
									if($key2=='machine_priv'){
										if($value2=='Y'){
											$machine_priv=$value2;
										}
									}
									if($key2=='machine_drop_priv'){
										if($value2=='Y'){
											$machine_drop_priv=$value2;
										}
									}
									if($key2=='backup_service_enable_priv'){
										if($value2=='Y'){
											$backup_service_enable_priv=$value2;
										}
									}
									if($key2=='backup_service_disable_priv'){
										if($value2=='Y'){
											$backup_service_disable_priv=$value2;
										}
									}
								}
							}
							$arr['user_add_priv']=$user_add_priv;
							$arr['user_drop_priv']=$user_drop_priv;
							$arr['user_edit_priv']=$user_edit_priv;
							$arr['user_grant_priv']=$user_grant_priv;
							$arr['role_add_priv']=$role_add_priv;
							$arr['role_drop_priv']=$role_drop_priv;
							$arr['role_edit_priv']=$role_edit_priv;
							$arr['cluster_creata_priv']=$cluster_creata_priv;
							$arr['cluster_drop_priv']=$cluster_drop_priv;
							$arr['backup_priv']=$backup_priv;
							$arr['restore_priv']=$restore_priv;
							$arr['expand_cluster_priv']=$expand_cluster_priv;
							$arr['shrink_cluster_priv']=$shrink_cluster_priv;
							$arr['storage_node_create_priv']=$storage_node_create_priv;
							$arr['storage_node_drop_priv']=$storage_node_drop_priv;
							$arr['storage_enable_priv']=$storage_enable_priv;
							$arr['storage_disable_priv']=$storage_disable_priv;
							$arr['shard_create_priv']=$shard_create_priv;
							$arr['shard_drop_priv']=$shard_drop_priv;
							$arr['compute_node_create_priv']=$compute_node_create_priv;
							$arr['compute_node_drop_priv']=$compute_node_drop_priv;
							$arr['compute_enable_priv']=$compute_enable_priv;
							$arr['compute_disable_priv']=$compute_disable_priv;
							$arr['machine_add_priv']=$machine_add_priv;
							$arr['machine_priv']=$machine_priv;
							$arr['machine_drop_priv']=$machine_drop_priv;
							$arr['backup_service_enable_priv']=$backup_service_enable_priv;
							$arr['backup_service_disable_priv']=$backup_service_disable_priv;
							$data['priv'] = $arr;
						}
					}
					$affected_clusters=rtrim($affected_clusters, ',');
					//字符串去重
					$affected_clusters=$this->unique($affected_clusters);
					$data['accessToken'] =$token;
					$data['code'] = 200;
					$data['num'] = 1;
					$data['userName'] = $user_name;
					$data['apply_all_cluster'] = $apply_all_cluster;
					$data['affected_clusters'] = $affected_clusters;
					$data['message'] = 'success';
					print_r(json_encode($data));
				}else{
					$data['code'] = 500;
					$data['message'] = '该用户未授权，没有登录权限';
					print_r(json_encode($data));
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
			print_r(json_encode($data));
		}
	}
	public function unique($str){
		$arr = explode(',', $str);
		$arr = array_unique($arr);//内置数组去重算法
		$data = implode(',', $arr);
		$data = trim($data,',');//trim — 去除字符串首尾处的空白字符
		return $data;
	}
	public function  modifyParam(){
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$ip = $string['ip'];
		$port = $string['port'];
		$f = fopen('./application/config/database.php', 'w+');
		$file="<?php
		defined('BASEPATH') OR exit('No direct script access allowed');
		\$active_group = 'default';
		\$query_builder = TRUE;
		\$db_debug=TRUE;
		\$db['role']=array(
			'dsn'	=> '',
			'hostname' => '$ip',
			'port' => $port,
			'username' => 'pgx',
			'password' => 'pgx_pwd',
			'database' => 'kunlun_dba_tools_db',
			'dbdriver' => 'mysqli',
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => TRUE,
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'encrypt' => FALSE,
			'compress' => FALSE,
			'stricton' => FALSE,
			'failover' => array(),
			'options' => array(PDO::ATTR_TIMEOUT => 5),
			'save_queries' => TRUE,
		);
		\$db['default']=array(
			'dsn'	=> '',
			'hostname' => '$ip',
			'port' => $port,
			'username' => 'pgx',
			'password' => 'pgx_pwd',
			'database' => 'kunlun_metadata_db',
			'dbdriver' => 'mysqli',
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => TRUE,
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'encrypt' => FALSE,
			'compress' => FALSE,
			'stricton' => FALSE,
			'failover' => array(),
			'options' => array(PDO::ATTR_TIMEOUT => 5),
			'save_queries' => TRUE,
		);";
		fwrite($f, $file);
		fgets($f);
		$data['code'] = 200;
		$data['message'] = 'success';
		print_r(json_encode($data));
	}
}
