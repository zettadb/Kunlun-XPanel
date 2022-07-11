<?php
include(APPPATH.'/libraries/include/phpqrcode/qrlib.php'); 


class Import_model extends CI_Model {
	function __construct(){
//		$this->equipment_type_arr=$this->config->item('equipment_type_arr');
//		$this->equipment_type_sip_arr=$this->config->item('equipment_type_sip_arr');
//        $this->msg_ip=$this->config->item('msg_ip');
		$this->config->load('myconfig');
		$this->post_url=$this->config->item('post_url');
		$this->ver=$this->config->item('ver');
	}

	public function importPersonData($data,$user_name){
		 //print_r($data);exit;
		$i=0;
		$res = '';
		$has_insert_data = false;
		$has_error = false;
		foreach ($data as $row){
			$i++;
			//先检验这条数据的合法性
			$judge = $this->judgePersonLegal($row);
			//有一个字段不符合,就退出,不写入数据
			if($judge==true){
				$res .= "第".$i."条数据有误".",";
				$has_error = true;
			}
			else {
				$now = time();//获取当前时间戳格式
				if(empty($row['rack_id'])){
					$rack_id='0';
				}else{
					$rack_id=(string)$row['rack_id'];
				}
				if($row['machine_type']=='计算'){
					$machine_type='computer';
				}elseif($row['machine_type']=='存储'){
					$machine_type='storage';
				}else{
					$machine_type=$row['machine_type'];
				}
				//写入数据
				$meta_json=array(
					'version'=>$this->ver,
					'job_id'=>'',
					'job_type'=>'create_machine',
					'timestamp'=>(string)$now,
					'user_name'=>$user_name,
					'paras'=>array(
					'hostaddr'=>$row['hostaddr'],
					'machine_type'=>$machine_type,
					'port_range'=>$row['port_range'],
					'rack_id'=>$rack_id,
					'datadir'=>$row['datadir'],
					'logdir'=>$row['logdir'],
					'wal_log_dir'=>$row['wal_log_dir'],
					'comp_datadir'=>$row['comp_datadir'],
					'total_cpu_cores'=>(string)$row['total_cpu_cores'],
					'total_mem'=>(string)$row['total_mem']
					)
				);
				$post_data=str_replace("\\/", "/", json_encode($meta_json));
				//print_r($post_data);exit;
				$post_arr = $this->postData($post_data,$this->post_url);
				$post_arr = json_decode($post_arr, TRUE);
				//print_r($post_arr['status']);exit;
				if($post_arr['error_code']=='0'){
					$if_insert=true;
				}else{
					$res .= "第".$i."条数据写入失败".",";
					$has_error = true;
				}
				if($if_insert==true){
					$has_insert_data = true;
				}
			}
		}
		if($has_insert_data == true){
			if($has_error==true){
				$res .= "其它数据写入成功".",";
			}
			else{
				$res .= "数据写入成功".",";
			}
		}
		$res = substr($res,0,strlen($res)-1); 
		return $res; 
	}
	public function judgePersonLegal($row){
		if(empty($row['hostaddr'])||empty($row['machine_type'])||empty($row['port_range'])||empty($row['total_mem'])||empty($row['total_cpu_cores'])){
			return true;
		}
		if(!empty($row['hostaddr'])){
			if($this->validateip($row['hostaddr'])){
				return false;
			}else{
				return true;
			}
		}
		if(strpos($row['port_range'],'-') == false){
			return true;
		}
		if(!is_numeric($row['total_mem'])||!is_numeric($row['total_cpu_cores'])){
			return true;
		}
		if($row['machine_type']=='计算'&&empty($row['comp_datadir'])){
			return true;
		}
		if($row['machine_type']=='储存'&&(empty($row['logdir'])||empty($row['wal_log_dir'])||empty($row['datadir']))){
			return true;
		}
		if(!empty($row['port_range'])){
			$arr=explode('-',$row['port_range']);
			if(!is_numeric($arr[0])||!is_numeric($arr[1])){
				return true;
			}
		}
		return false;
	}
	public function validateip($ip){
		$preg="/^((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))/";
		preg_match($preg,$ip,$matches);
		if(!empty($matches)) return 1;
		return 0;
	}
	public function postData($post_data,$url){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);   //只需要设置30秒的数量就可以
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0

		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect: ')); //头部要送出'Expect: '
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($post_data)
		));
		$result = curl_exec($ch);
		//print_r($result);exit;
		$curl_errno = curl_errno($ch);
		if (curl_errno($ch)) {
			$data['status'] = 'connection failed';
			$data['error_info'] = '接口请求超时';
			return json_encode($data);
		}
		curl_close($ch);
		return $result;
	}
}
