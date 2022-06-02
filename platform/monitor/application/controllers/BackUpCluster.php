<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BackUpCluster extends CI_Controller {

	public function __construct()
	{
		session_start();
		parent::__construct();
		//打开重定向
		//$this->load->helper('form');
		$this->load->helper('url');
		$this->meta_username=$this->config->item('meta_username');
		$this->meta_pwd=$this->config->item('meta_pwd');
		$this->meta_database=$this->config->item('meta_database');
		$this->pg_username=$this->config->item('pg_username');
		$this->pg_pwd=$this->config->item('pg_pwd');
		$this->pg_database=$this->config->item('pg_database');
		$this->meta_ip=$this->config->item('meta_ip');
		$this->meta_port=$this->config->item('meta_port');
		$this->ver=$this->config->item('ver');
		$this->post_url=$this->config->item('post_url');
		$this->user_per_page=$this->config->item('user_per_page');
	}
	public function backeUpCluster(){
		$page = $this->input->get('page');
		$keyword = $this->input->get('keyword');
		$status = $this->input->get('status');
		$this->load->model('Main_model');
		if(is_null($page)||empty($page)) {
			$page=1;
		}
		//得到任务总数
		$sql="select count(0) as count from cluster_backups ";
		$total = $this->Main_model->getListTotal($sql,$this->user_per_page);
		$data['page'] = $page>$total?$total:$page;
		$data['total']=$total;
		$data['keyword']=$keyword;
		$data['status'] = $status;
		$data['pagesize']=$this->user_per_page;
		$this->load->view('app/backup_cluster_list',$data);
	}
	public function getBUClusterList(){
		$page = $this->input->get('page');
		$page = $page?$page:'1';
		$page_size=$this->user_per_page;
		$start=($page-1)*$page_size;
		$this->load->model('Main_model');
		$sql="select cluster_name,when_created,id from cluster_backups limit $page_size offset $start";
		$res = $this->Main_model->getList($sql);
		print_r(json_encode($res));
	}
}
