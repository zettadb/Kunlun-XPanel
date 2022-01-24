<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operation extends CI_Controller {

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

	public function getOperationList(){
		$page = $this->input->get('page');
		$keyword = $this->input->get('keyword');
		$status = $this->input->get('status');
		$page = $page?$page:'1';
		$page_size=$this->user_per_page;
		$this->load->model('Main_model');
		$start=($page-1) * $page_size;
		//获取任务信息
		$sql="select job_type,when_created,operation,result,info,job_id,info_other from operation_record order by id desc limit $page_size offset $start";
		$res = $this->Main_model->getList($sql);
		if(!empty($res)){
			foreach($res as $k => $row){
				//创建集群任务名称
				if($row['job_type']=='create_cluster'){
					$res[$k]['opration_name']='新增('.$row['info_other'].')集群';
				}
				//删除集群任务名称
				if($row['job_type']=='delete_cluster'){
					$res[$k]['opration_name']='删除('.$row['info_other'].')集群';
				}
				//备份集群任务名称
				if($row['job_type']=='backup_cluster'){
					$res[$k]['opration_name']='备份('.$row['info_other'].')集群';
				}
				//恢复集群任务名称
				if($row['job_type']=='restore_cluster'){
					$a=substr($row['operation'],1,-1);
					$s=explode(',',$a);
					foreach ($s as $key){
						$key=str_replace('"', '', $key);
						$key=str_replace(array("\n\t","\t","\n"), '', $key);
						$str=explode(':',$key);
						if($str[0]=='backup_cluster_name'){
							if(!empty($str[1])){
								$opration_name1='把('.$str[1].')集群';
							}
						}
						if($str[0]=='restore_cluster_name'){
							if(!empty($str[1])){
								$opration_name2='恢复到('.$str[1].')集群';
							}
							if(empty($opration_name2)){
								$opration_name2=$row['info_other'];
							}
						}
					}
					$opration_name=$opration_name1.$opration_name2;
					$res[$k]['opration_name']=$opration_name;
				}
				// 回档集群任务名称
				if($row['job_type']=='restore_new_cluster'){
					$a=substr($row['operation'],1,-1);
					$s=explode(',',$a);
					$backup_cluster_name='';
					$restore_cluster_name='';
					foreach ($s as $key){
						$key=str_replace('"', '', $key);
						$key=str_replace(array("\n\t","\t","\n"), '', $key);
						$str=explode(':',$key);
						if($str[0]=='backup_cluster_name'){
							if(!empty($str[1])){
								$backup_cluster_name=$str[1];
							}
						}
						if($str[0]=='restore_cluster_name'){
							if(!empty($str[1])){
								$restore_cluster_name=$str[1];
							}
						}
					}
					if(empty($restore_cluster_name)){
						$restore_cluster_name=$row['info_other'];
					}
					$opration_name='从('.$backup_cluster_name.')集群回档到('.$restore_cluster_name.')集群';
					$res[$k]['opration_name']=$opration_name;
				}
				//新增计算节点任务名称
				if($row['job_type']=='add_comps'){
					$res[$k]['opration_name']='新增('.$row['info_other'].')计算节点';
				}
				//删除计算节点任务名称
				if($row['job_type']=='delete_comp'){
					$res[$k]['opration_name']='删除('.$row['info_other'].')计算节点';
				}
				//新增分片任务名称
				if($row['job_type']=='add_shards'){
					$res[$k]['opration_name']='新增('.$row['info_other'].')分片';
				}
				//删除分片任务名称
				if($row['job_type']=='delete_shard'){
					$res[$k]['opration_name']='删除('.$row['info_other'].')分片';
				}
				//状态
				if($row['result']=='succeed'){
					$res[$k]['result']='成功';
				}elseif($row['result']=='error'){
					$res[$k]['result']='失败';
				}elseif($row['result']=='busy'){
					$res[$k]['result']='正在执行';
				}
				//新增机器
				if($row['job_type']=='create_machine'){
					$res[$k]['opration_name']='新增('.$row['info_other'].')计算机';
				}
				//删除机器
				if($row['job_type']=='delete_machine'){
					$res[$k]['opration_name']='删除('.$row['info_other'].')计算机';
				}
				//更新机器
				if($row['job_type']=='update_machine'){
					$res[$k]['opration_name']='更新('.$row['info_other'].')计算机';
				}
			}
		}
		print_r(json_encode($res));
	}
	public function operationList(){
		$page = $this->input->get('page');
		$keyword = $this->input->get('keyword');
		$status = $this->input->get('status');
		$this->load->model('Main_model');
		if(is_null($page)||empty($page)) {
			$page=1;
		}
		//得到任务总数
		$sql="select count(0) as count from operation_record ";
		$total = $this->Main_model->getListTotal($sql,$this->user_per_page);
		$data['page'] = $page>$total?$total:$page;
		$data['total']=$total;
		$data['keyword']=$keyword;
		$data['status'] = $status;
		$data['pagesize']=$this->user_per_page;
		$this->load->view('app/operation_list',$data);
	}
}
