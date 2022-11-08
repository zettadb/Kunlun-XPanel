<?php


defined('BASEPATH') or exit('No direct script access allowed');

class ClusterSetting extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->config->load('myconfig');
		$this->key = $this->config->item('key');
		$this->post_url = $this->config->item('post_url');
	}


	/**
	 * 集群下拉选项
	 * @author wangwentao
	 */
	public function clusterOptions()
	{

	}

	/**
	 * 表重分布
	 * @author wangwentao
	 */
	public function tableRepartition()
	{
		throw new ApiException('我的');
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token = $arr["Token"];
		if (empty($token)) {
			throw new ApiException('token不能为空', 201);
		}
		//判断参数
		$string = json_decode(@file_get_contents('php://input'), true);
		//调接口
		$this->load->model('Cluster_model');
		$post_data = str_replace("\\/", "/", json_encode($string));
		$post_arr = $this->Cluster_model->postData($post_data, $this->post_url);
		$post_arr = json_decode($post_arr, true);
		$data = $post_arr;
		echo json_encode($data);
	}
}
