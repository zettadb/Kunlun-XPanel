<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MetaCluster extends CI_Controller {

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
	public function getMetaClusterList()
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
		$this->load->model('Cluster_model');
		$sql="select id,hostaddr,port,member_state,sync_state,replica_delay from meta_db_nodes ";
		if(!empty($username)){
			$sql .=" where  hostaddr like '%$username%'";
		}
		$sql .=" order by id desc limit ".$pageSize." offset ".$start;
		//print_r($sql);exit;
		$res=$this->Cluster_model->getList($sql);
		if($res===false){
			$res=array();
		}
		$data['code'] = 200;
		$data['list'] = $res;
		$data['total'] =  $res ? count($res) : 0;
		print_r(json_encode($data));
	}
	public function getMetaPrimary(){
		$this->load->model('Cluster_model');
		$sql="select hostaddr as ip,port from meta_db_nodes where member_state='source'";
		$res=$this->Cluster_model->getList($sql);
		if($res===false){
			$res=array();
		}
		$data['code'] = 200;
		$data['list'] = $res;
		print_r(json_encode($data));
	}
	public function getMetaStandbyNode(){
		$this->load->model('Cluster_model');
		$sql="select hostaddr as ip,port from meta_db_nodes where member_state='replica'";
		$res=$this->Cluster_model->getList($sql);
		if($res===false){
			$res=array();
		}
		$data['code'] = 200;
		$data['list'] = $res;
		print_r(json_encode($data));
	}
}
