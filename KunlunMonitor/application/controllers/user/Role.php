<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
		header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
		header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
		header('Access-Control-Allow-Headers:x-requested-with,content-type,Token');//允许接受token
		header('Content-Type: application/json;charset=utf-8');
		//header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
		$this->config->load('myconfig');
		$this->key=$this->config->item('key');
		$this->db=$this->load->database('role',true);
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
		$start=($pageNo - 1) * $pageSize;
		$user_name=$arr['user_name'];
		//获取用户数据
		$this->load->model('Login_model');
		if($user_name=='super_dba'){
			$sql="select * from kunlun_role_privilege";
			$total_sql="select count(id) as count from kunlun_role_privilege ";
		}else{
			//先通过用户名获取id
			$sql_userid="select id from kunlun_user where name='$user_name'";
			$res_userid=$this->Login_model->getList($sql_userid);
			$user_id='';
			if(!empty($res_userid)){
				$userid_count=count($res_userid);
			}else{
				$userid_count=0;
			}
			if($userid_count>1){
				foreach ($res_userid as $row){
					$user_id.=$row['id'].',';
				}
				$user_id=substr($user_id,0,strlen($user_id)-1);
				
			}else if($userid_count==1){
				$user_id=$res_userid[0]['id'];
			}else{
				$data['code'] = 200;
				$data['list'] =array();
				$data['total'] =  0;
				print_r(json_encode($data));return;
			}
			//再获取role_id
			$sql_roleid="select role_id from kunlun_role_assign where user_id in ($user_id)";
			$res_roleid=$this->Login_model->getList($sql_roleid);
			$role_id='';
			$roleid_count=count($res_roleid);
			if($roleid_count>1){
				foreach ($res_roleid as $row){
					$role_id.=$row['role_id'].',';
				}
				$role_id=substr($role_id,0,strlen($role_id)-1);
			}else if($roleid_count==1){
				$role_id=$res_roleid[0]['role_id'];
			}else{
				$data['code'] = 200;
				$data['list'] =array();
				$data['total'] = 0;
				print_r(json_encode($data));
			}
			$sql="select * from kunlun_role_privilege where id in($role_id) ";
			$total_sql="select count(id) as count from kunlun_role_privilege where id in($role_id)";
		}
		$sql .=" order by id desc limit ".$pageSize." offset ".$start;
		$res=$this->Login_model->getList($sql);
		$res_total=$this->Login_model->getList($total_sql);
		if($res===false){
			$res=array();
		}
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = $res_total ? (int)$res_total[0]['count'] : 0;
		print_r(json_encode($data));
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
		$role_name = $string['role_name'];
		$priv = $string['priv']['permissionIds'];
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
		if(!empty($priv)){
			//逗号分割
			$arr = explode(',',$priv);
			//print_r($arr);exit;
			foreach ($arr as $row ){
				//用户
				if($row=='user_add'){
					$user_add_priv='Y';
				}
				if($row=='user_del'){
					$user_drop_priv='Y';
				}
				if($row=='user_edit'){
					$user_edit_priv='Y';
				}
				if($row=='user_grant'){
					$user_grant_priv='Y';
				}
				//角色
				if($row=='role_add'){
					$role_add_priv='Y';
				}
				if($row=='role_del'){
					$role_drop_priv='Y';
				}
				if($row=='role_edit'){
					$role_edit_priv='Y';
				}
				//集群
				if($row=='cluster_add'){
					$cluster_creata_priv='Y';
				}
				if($row=='cluster_del'){
					$cluster_drop_priv='Y';
				}
				if($row=='cluster_backeup'){
					$backup_priv='Y';
				}
				if($row=='cluster_restore'){
					$restore_priv='Y';
				}
				if($row=='cluster_expand'){
					$expand_cluster_priv='Y';
				}
				if($row=='cluster_shrink'){
					$shrink_cluster_priv='Y';
				}
				if($row=='backup_service_en'){
					$backup_service_enable_priv='Y';
					$backup_service_disable_priv='Y';
				}
				//存储节点
				if($row=='storage_node_create'){
					$storage_node_create_priv='Y';
				}
				if($row=='storage_node_drop'){
					$storage_node_drop_priv='Y';
				}
				if($row=='storage_en'){
					$storage_enable_priv='Y';
					$storage_disable_priv='Y';
				}
				//存储分片
				if($row=='shard_add'){
					$shard_create_priv='Y';
				}
				if($row=='shard_del'){
					$shard_drop_priv='Y';
				}
				//计算节点
				if($row=='comp_add'){
					$compute_node_create_priv='Y';
				}
				if($row=='comp_del'){
					$compute_node_drop_priv='Y';
				}
				if($row=='comp_en'){
					$compute_enable_priv='Y';
					$compute_disable_priv='Y';
				}
				//计算机
				if($row=='equip_add'){
					$machine_add_priv='Y';
				}
				if($row=='equip_edit'){
					$machine_priv='Y';
				}
				if($row=='equip_del'){
					$machine_drop_priv='Y';
				}
			}
		}
		/*$valid_period = $string['valid_period'];
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
		}*/
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
					$role_id="select role_id  from kunlun_role_assign where user_id='$user_id';";
					$res_role=$this->Login_model->getList($role_id);
					if($res_role[0]['role_id']===NULL) {
						$data['code'] = 500;
						$data['message'] = '该账户不是角色用户';
						print_r(json_encode($data));return;
					}
					$role_id=$res_role[0]['role_id'];
					$sql_role="select role_add_priv from kunlun_role_privilege where id='$role_id';";
					//print_r($sql_role);exit;
					$res_role_priv=$this->Login_model->getList($sql_role);
					if($res_role_priv[0]['role_add_priv']!=='Y') {
						$data['code'] = 500;
						$data['message'] = '该账户不具备新增角色权限';
						print_r(json_encode($data));return;
					}
					//验证角色类型是否存在
					$sql_user="select count(role_name) as count from kunlun_role_privilege where role_name='$role_name';";
					//echo $sql_user;exit;
					$res_user=$this->Login_model->getList($sql_user);
					if(!empty($res_user)){
						if($res_user[0]['count']==0){
							//获得最大id值
							$sql_id="select max(id) as id from kunlun_role_privilege ";
							$res_maxid=$this->Login_model->getList($sql_id);
							if($res_maxid[0]['id']===null){
								$maxid=0;
							}else{
								$maxid=$res_maxid[0]['id']+1;
							}
							$sql_insert="INSERT INTO kunlun_role_privilege(id,role_name,user_add_priv,user_drop_priv,user_grant_priv,user_edit_priv,role_add_priv,role_drop_priv,role_edit_priv,cluster_creata_priv,cluster_drop_priv,backup_priv,restore_priv,expand_cluster_priv,shrink_cluster_priv,backup_service_enable_priv,backup_service_disable_priv,shard_create_priv,shard_drop_priv,storage_node_create_priv,storage_node_drop_priv,storage_enable_priv,storage_disable_priv,compute_node_create_priv,compute_node_drop_priv,compute_enable_priv,compute_disable_priv,machine_add_priv,machine_drop_priv,machine_priv) values ('$maxid','$role_name','$user_add_priv','$user_drop_priv','$user_grant_priv','$user_edit_priv','$role_add_priv','$role_drop_priv','$role_edit_priv','$cluster_creata_priv','$cluster_drop_priv','$backup_priv','$restore_priv','$expand_cluster_priv','$shrink_cluster_priv','$backup_service_enable_priv','$backup_service_disable_priv','$shard_create_priv','$shard_drop_priv','$storage_node_create_priv','$storage_node_drop_priv','$storage_enable_priv','$storage_disable_priv','$compute_node_create_priv','$compute_node_drop_priv','$compute_enable_priv','$compute_disable_priv','$machine_add_priv','$machine_drop_priv','$machine_priv');";
							//echo $sql_insert;exit;
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
		$token=$arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		$id = $string['id'];
		$role_name = $string['role_name'];
		$priv = $string['priv']['permissionIds'];
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
		if(!empty($priv)){
			//逗号分割
			$arr = explode(',',$priv);
			//print_r($arr);exit;
			foreach ($arr as $row ){
				//用户
				if($row=='user_add'){
					$user_add_priv='Y';
				}
				if($row=='user_del'){
					$user_drop_priv='Y';
				}
				if($row=='user_edit'){
					$user_edit_priv='Y';
				}
				if($row=='user_grant'){
					$user_grant_priv='Y';
				}
				//角色
				if($row=='role_add'){
					$role_add_priv='Y';
				}
				if($row=='role_del'){
					$role_drop_priv='Y';
				}
				if($row=='role_edit'){
					$role_edit_priv='Y';
				}
				//集群
				if($row=='cluster_add'){
					$cluster_creata_priv='Y';
				}
				if($row=='cluster_del'){
					$cluster_drop_priv='Y';
				}
				if($row=='cluster_backeup'){
					$backup_priv='Y';
				}
				if($row=='cluster_restore'){
					$restore_priv='Y';
				}
				if($row=='cluster_expand'){
					$expand_cluster_priv='Y';
				}
				if($row=='cluster_shrink'){
					$shrink_cluster_priv='Y';
				}
				if($row=='backup_service_en'){
					$backup_service_enable_priv='Y';
					$backup_service_disable_priv='Y';
				}
				//存储节点
				if($row=='storage_node_create'){
					$storage_node_create_priv='Y';
				}
				if($row=='storage_node_drop'){
					$storage_node_drop_priv='Y';
				}
				if($row=='storage_en'){
					$storage_enable_priv='Y';
					$storage_disable_priv='Y';
				}
				//存储分片
				if($row=='shard_add'){
					$shard_create_priv='Y';
				}
				if($row=='shard_del'){
					$shard_drop_priv='Y';
				}
				//计算节点
				if($row=='comp_add'){
					$compute_node_create_priv='Y';
				}
				if($row=='comp_del'){
					$compute_node_drop_priv='Y';
				}
				if($row=='comp_en'){
					$compute_enable_priv='Y';
					$compute_disable_priv='Y';
				}
				//计算机
				if($row=='equip_add'){
					$machine_add_priv='Y';
				}
				if($row=='equip_edit'){
					$machine_priv='Y';
				}
				if($row=='equip_del'){
					$machine_drop_priv='Y';
				}
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
					//查看该账户是否有编辑角色的权限
					$user_id=$res[0]['id'];
					$role_id="select role_id  from kunlun_role_assign where user_id='$user_id';";
					$res_role=$this->Login_model->getList($role_id);
					if($res_role[0]['role_id']===NULL) {
						$data['code'] = 500;
						$data['message'] = '该账户不是角色用户';
						print_r(json_encode($data));return;
					}
					$role_id=$res_role[0]['role_id'];
					$sql_role="select role_edit_priv from kunlun_role_privilege where id='$role_id';";
					//print_r($sql_role);exit;
					$res_role_priv=$this->Login_model->getList($sql_role);
					if($res_role_priv[0]['role_edit_priv']!=='Y') {
						$data['code'] = 500;
						$data['message'] = '该账户不具备编辑角色权限';
						print_r(json_encode($data));return;
					}
					$sql_insert="update kunlun_role_privilege set role_name='$role_name',user_add_priv='$user_add_priv',user_drop_priv='$user_drop_priv',user_grant_priv='$user_grant_priv',user_edit_priv='$user_edit_priv',role_add_priv='$role_add_priv',role_drop_priv='$role_drop_priv',role_edit_priv='$role_edit_priv',cluster_creata_priv='$cluster_creata_priv',cluster_drop_priv='$cluster_drop_priv',backup_priv='$backup_priv',restore_priv='$restore_priv',expand_cluster_priv='$expand_cluster_priv',shrink_cluster_priv='$shrink_cluster_priv',backup_service_enable_priv='$backup_service_enable_priv',backup_service_disable_priv='$backup_service_disable_priv',shard_create_priv='$shard_create_priv',shard_drop_priv='$shard_drop_priv',storage_node_create_priv='$storage_node_create_priv',storage_node_drop_priv='$storage_node_drop_priv',storage_enable_priv='$storage_enable_priv',storage_disable_priv='$storage_disable_priv',compute_node_create_priv='$compute_node_create_priv',compute_node_drop_priv='$compute_node_drop_priv',compute_enable_priv='$compute_enable_priv',compute_disable_priv='$compute_disable_priv',machine_add_priv='$machine_add_priv',machine_drop_priv='$machine_drop_priv',machine_priv='$machine_priv' where id='$id';";
					//echo $sql_insert;exit;
					$res_insert=$this->Login_model->updateList($sql_insert);
					if($res_insert==1){
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
					$role_id="select role_id from kunlun_role_assign where role_id='$user_id';";
					$res_role=$this->Login_model->getList($role_id);
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
					//先查授权信息
					$select_sql="select count(*) as count from kunlun_role_assign where role_id='$id';";
					$res_select=$this->Login_model->updateList($select_sql);
					$this->db->trans_start();
					if($res_select>0) {
						$sql_update="delete from kunlun_role_privilege where id='$id';";
						$res_update=$this->Login_model->updateList($sql_update);
						if($res_update==1){
							$data['code'] = 200;
							$data['message'] = '删除成功';
							//删除授权信息
							$sql_assign="delete from kunlun_role_assign where role_id='$id';";
							$res_assign=$this->Login_model->updateList($sql_assign);
							$this->db->trans_complete();
						}else{
							$data['code'] = 501;
							$data['message'] = '删除失败';
						}
					}else{
						$sql_update="delete from kunlun_role_privilege where id='$id';";
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
		//$id = $string['id'];
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
					$sql="select id,role_name as name from kunlun_role_privilege where role_name!='super_dba'";
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
		//判断参数
		$string=json_decode(@file_get_contents('php://input'),true);
		//$id = $string['id'];
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
					$sql="select id,name from kunlun_user where name!='super_dba'";
					$res=$this->Login_model->getList($sql);
					$data['code'] = 200;
					$data['list'] = $res;
					print_r(json_encode($data));
				}
			}
		}
	}
	public function rolePermission(){
		//GET请求
		$serve=$_SERVER['QUERY_STRING'];
		$string=preg_split('/[=&]/',$serve);
		$arr=array();
		for($i=0;$i<count($string);$i+=2) {
			$arr[$string[$i]] = $string[$i + 1];
		}
		$id=$arr['roleId'];
		$this->load->model('Login_model');
		$sql="select * from kunlun_role_privilege where id='$id'";
		//print_r($sql);exit;
		$res=$this->Login_model->getList($sql);
		//print_r($res);exit;
		$arr=array();
		if(!empty($res)) {
			//foreach ($res as $key => $value) {
				//foreach ($res as $key2 => $value2) {
					//用户
					if($res[0]['user_add_priv']=='Y'){
						$user_add_priv='user_add';
						array_push($arr,$user_add_priv);
					}
					if($res[0]['user_drop_priv']=='Y'){
						$user_drop_priv = 'user_del';
						array_push($arr,$user_drop_priv);
					}
					if($res[0]['user_edit_priv']=='Y'){
						$user_edit_priv = 'user_edit';
						array_push($arr,$user_edit_priv);
					}
					if($res[0]['user_grant_priv']=='Y'){
						$user_grant_priv = 'user_grant';
						array_push($arr,$user_grant_priv);
					}
					//角色
					if($res[0]['role_add_priv']=='Y'){
						$role_add_priv = 'role_add';
						array_push($arr,$role_add_priv);
					}
					if($res[0]['role_drop_priv']=='Y'){
						$role_drop_priv = 'role_del';
						array_push($arr,$role_drop_priv);
					}
					if($res[0]['role_edit_priv']=='Y'){
						$role_edit_priv = 'role_edit';
						array_push($arr,$role_edit_priv);
					}
					//集群
					if($res[0]['cluster_creata_priv']=='Y'){
						$cluster_creata_priv = 'cluster_add';
						array_push($arr,$cluster_creata_priv);
					}
					if($res[0]['cluster_drop_priv']=='Y'){
						$cluster_drop_priv = 'cluster_del';
						array_push($arr,$cluster_drop_priv);
					}
					if($res[0]['backup_priv']=='Y'){
						$backup_priv = 'cluster_backeup';
						array_push($arr,$backup_priv);
					}
					if($res[0]['restore_priv']=='Y'){
						$restore_priv = 'cluster_restore';
						array_push($arr,$restore_priv);
					}
					if($res[0]['expand_cluster_priv']=='Y'){
						$expand_cluster_priv = 'cluster_expand';
						array_push($arr,$expand_cluster_priv);
					}
					if($res[0]['shrink_cluster_priv']=='Y'){
						$shrink_cluster_priv = 'cluster_shrink';
						array_push($arr,$shrink_cluster_priv);
					}
					if($res[0]['backup_service_enable_priv']=='Y'){
						$backup_service_enable_priv = 'backup_service_en';
						//$backup_service_disable_priv = 'Y';
						array_push($arr,$backup_service_enable_priv);
					}
					//存储节点
					if($res[0]['storage_node_create_priv']=='Y'){
						$storage_node_create_priv = 'storage_node_create';
						array_push($arr,$storage_node_create_priv);
					}
					if($res[0]['storage_node_drop_priv']=='Y'){
						$storage_node_drop_priv = 'storage_node_drop';
						array_push($arr,$storage_node_drop_priv);
					}
					if($res[0]['storage_enable_priv']=='Y'){
						$storage_enable_priv = 'storage_en';
						//$storage_disable_priv = 'Y';
						array_push($arr,$storage_enable_priv);
					}
					//存储分片
					if($res[0]['shard_create_priv']=='Y'){
						$shard_create_priv='shard_add';
						array_push($arr,$shard_create_priv);
					}
					if($res[0]['shard_drop_priv']=='Y'){
						$shard_drop_priv = 'shard_del';
						array_push($arr,$shard_drop_priv);
					}
					//计算节点
					if($res[0]['compute_node_create_priv']=='Y'){
						$compute_node_create_priv = 'comp_add';
						array_push($arr,$compute_node_create_priv);
					}
					if($res[0]['compute_node_drop_priv']=='Y'){
						$compute_node_drop_priv = 'comp_del';
						array_push($arr,$compute_node_drop_priv);
					}
					if($res[0]['compute_enable_priv']=='Y'){
						$compute_enable_priv = 'comp_en';
						//$compute_disable_priv = 'Y';
						array_push($arr,$compute_enable_priv);
					}
					//计算机
					if($res[0]['machine_add_priv']=='Y'){
						$machine_add_priv = 'equip_add';
						array_push($arr,$machine_add_priv);
					}
					if($res[0]['machine_priv']=='Y'){
						$machine_priv = 'equip_edit';
						array_push($arr,$machine_priv);
					}
					if($res[0]['machine_drop_priv']=='Y'){
						$machine_drop_priv = 'equip_del';
						array_push($arr,$machine_drop_priv);
					}
					$res[0]['checkkeys'] = $arr;
				//}
			//}
		}
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] = count($res);
		print_r(json_encode($data));
	}
//	public function grantAuth(){
//		//获取token
//		$arr = apache_request_headers();//获取请求头数组
//		$token=$arr["Token"];
//		if (empty($token)) {
//			$data['code'] = 201;
//			$data['message'] = 'token不能为空';
//			print_r(json_encode($data));return;
//		}
//		//判断参数
//		$string=json_decode(@file_get_contents('php://input'),true);
//		$id = $string['id'];
//		//验证token
//		$this->load->model('Login_model');
//		$res_token=$this->Login_model->getToken($token,'D',$this->key);
//		if(!empty($res_token)) {
//			$sql = "select count(id) as count,id from kunlun_user where name='$res_token';";
//			$res = $this->Login_model->getList($sql);
//			if (!empty($res)) {
//				if ($res[0]['count'] == 0) {
//					$data['code'] = 500;
//					$data['message'] = 'token错误';
//					print_r(json_encode($data));
//				} else {
//					//获取用户数据
//					$this->load->model('Login_model');
//					$sql="select id,name from kunlun_user";
//					$res=$this->Login_model->getList($sql);
//					$data['code'] = 200;
//					$data['list'] = $res;
//					print_r(json_encode($data));
//				}
//			}
//		}
//	}
}
