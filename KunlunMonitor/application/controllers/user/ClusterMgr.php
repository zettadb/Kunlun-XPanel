<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ClusterMgr extends CI_Controller {

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
		$this->config->load('myconfig');
		$this->key=$this->config->item('key');
		$this->post_url=$this->config->item('post_url');
	}
	
	public  function getClusterMgrList(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		$root='cluster_mgr';
		$arr=array();
		$nodes=array(array('id'=>$root, 'text'=>$root));
		$links=array();
		$post_url=$this->post_url;
		$url=explode('/',$post_url);
		if(!empty($url)){
			$current_url=$url[2];
		}else{
			$current_url='';
		}
		//获取计算节点数据
		$sql="select id,hostaddr,port,member_state from cluster_mgr_nodes";
		$this->load->model('Cluster_model');
		$res=$this->Cluster_model->getList($sql);
		if($res!==false){
			foreach ($res as $row=>$value){
				foreach ($value as $key2 => $value2) {
					if ($key2 == 'port') {
						if (!empty($value2)) {
							$status='';
							if($res[$row]['member_state']=='source'){
								$status='主';
							}else if($res[$row]['member_state']=='replica'){
								$status='备';
							}
							if($current_url==$res[$row]['hostaddr'].':'.$value2){
								$nodetext=':当前连接';
							}else{
								$nodetext='';
							}
							$nodeid='cnode'.$res[$row]['id'];
							$shard_node=array('id'=>$nodeid, 'text'=>$value2.'('.$status.$nodetext.')','data'=>array('member_state'=>$res[$row]['member_state'],'ip'=>$res[$row]['hostaddr'],'port'=>$value2,'current_connet'=>$current_url));
							$shard_link=array('from'=>$root, 'to'=>$nodeid,'text'=>$res[$row]['hostaddr']);
							array_push($nodes,$shard_node);
							array_push($links,$shard_link);
						}
					}
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
	public function raftClusterMgr(){
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
		//调接口
		$this->load->model('Cluster_model');
		$post_data=str_replace("\\/", "/", json_encode($string));
		//print_r($post_data);exit;
		$post_arr = $this->Cluster_model->postData($post_data,$this->post_url);
		$post_arr = json_decode($post_arr, TRUE);
		$data=$post_arr;
		print_r(json_encode($data));
	}

}
