<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH.'/libraries/include/Classes/PHPExcel/IOFactory.php';
error_reporting(E_ERROR);
date_default_timezone_set('Asia/ShangHai');

class Import extends CI_Controller {

	public function __construct(){
		parent::__construct();
		header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
		header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
		header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
		header('Access-Control-Allow-Headers:x-requested-with,content-type,Token');//允许接受token
		header('Content-Type: application/json;charset=utf-8');
		$this->config->load('myconfig');
		$this->key=$this->config->item('key');
	}

	//数据导入页面
	public function importData(){
		//获取token
		$arr = apache_request_headers();//获取请求头数组
		$token=$arr["Token"];
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = 'token不能为空';
			print_r(json_encode($data));return;
		}
		//验证token
		$this->load->model('Login_model');
		$res_token=$this->Login_model->getToken($token,'D',$this->key);
		$type="person";
		set_time_limit(3800);
		if(PHP_OS=='Linux'){
			$this->ImportLinux($type,$res_token);
		}
		else{
			$this->ImportWindows($type,$res_token);
		}
	}

	public function ImportWindows($type,$res_token){
		/*$res = "数据加载成功,<br />第1条数据有误";
		$data['message'] = $res;
		print_r(json_encode($data));exit;*/
		if(isset($_FILES["file"]["type"])===false){
			$res = "请选择文件！";	
		}
		else {
			if((($_FILES["file"]["type"] == "application/vnd.ms-excel")||($_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"))&&($_FILES["file"]["size"] < 10*1024*1024)){
				if ($_FILES["file"]["error"] > 0){
					$res = "上传文件出错！请重试。";
				}
				else{
                    //如果文件夹不存在,创建文件夹
				    if(!file_exists("upload/")){
                        mkdir ("upload/",0777,true);
                    }
					if (file_exists("upload/" . $_FILES["file"]["name"])){
						move_uploaded_file($_FILES["file"]["tmp_name"],"upload/" . $_FILES["file"]["name"]);
					}
					else{
						move_uploaded_file($_FILES["file"]["tmp_name"],"upload/" . $_FILES["file"]["name"]);
					}
					//echo $_FILES["file"]["name"];exit;
					$sheet_data=$this->excelImportData("upload/" . $_FILES["file"]["name"]);
					//print_r($sheet_data);exit;
					//根据type不一样,导入不一样的数据
					$this->load->model('Import_model');
					if($type=="person"){
						$results = $this->Import_model->importPersonData($sheet_data,$res_token);
					}

					$res = $results;										
				}
			}
			elseif($_FILES["file"]["size"] >= 10*1024*1024){
				$res = "上传的文件太大了！";	
			}
			else{
				$res = "请上传xls,xlsx等格式的文件！";
			}
		}
		$data['message'] = $res;
		$data['code'] = 200;
		print_r(json_encode($data));
	}

	public function ImportLinux($type,$res_token){
		if(isset($_FILES["file"]["type"])===false){
			$res = "请选择文件！";
			/*echo '
            <script language="javascript"> 
                alert("请选择文件！"); 
                window.location.href=\''.base_url().'index.php/Main\'; 
            </script> ';*/		
		}
		else {
			if((($_FILES["file"]["type"] == "application/vnd.ms-excel")||($_FILES["file"]["type"]=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"))&&($_FILES["file"]["size"]<10*1024*1024)){
				if ($_FILES["file"]["error"] > 0){
					$res = "上传文件出错！请重试。";
					/*echo '
		            <script language="javascript"> 
		                alert("上传文件出错！请重试。"); 
		                window.location.href=\''.base_url().'index.php/Import/importData\'; 
		            </script> ';*/		
				}
				else{
				    //如果文件夹不存在,创建文件夹
                    if(!file_exists("upload/")){
                        mkdir ("upload/",0777,true);
                    }
					if (file_exists("/tmp/" . $_FILES["file"]["name"])){
						move_uploaded_file($_FILES["file"]["tmp_name"],"/tmp/" . $_FILES["file"]["name"]);
					}
					else{
						move_uploaded_file($_FILES["file"]["tmp_name"],"/tmp/" . $_FILES["file"]["name"]);
					}
					$sheet_data=$this->excelImportData("/tmp/" . $_FILES["file"]["name"]);
					//根据type不一样,导入不一样的数据
					$this->load->model('Import_model');
					if($type=="person"){
						$results = $this->Import_model->importPersonData($sheet_data,$res_token);
					}
					$res = $results;
					/*echo '
		            <script language="javascript"> 
		                alert("'.$results.'"); 
		                window.location.href=\''.base_url().'index.php/Import/importData\'; 
		            </script> ';*/
				}
			}
			else if($_FILES["file"]["size"] >= 10*1024*1024){
				$res = "上传的文件太大了！";
				/*echo '
	            <script language="javascript"> 
	                alert("上传的文件太大了！"); 
	                window.location.href=\''.base_url().'index.php/Import/importData\'; 
	            </script> ';*/		
			}
			else{
				$res = "请上传xls,xlsx等格式的文件！";
				/*echo '
	            <script language="javascript"> 
	                alert("请上传xls,xlsx等格式的文件！"); 
	                window.location.href=\''.base_url().'index.php/Import/importData\'; 
	            </script> ';*/		
			}
		}
		$data['message'] = $res;
		$data['code'] = 200;
		print_r(json_encode($data));
	}

	public function excelImportData($file){
		// Check prerequisites
		if (!file_exists($file)) {
			exit("not found device.xlsx.\n");
		}
		$objPHPExcel = PHPExcel_IOFactory::load($file);
		$sheet = $objPHPExcel->getSheet(0);

		//获取行数与列数,注意列数需要转换
		$highestRowNum = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		$highestColumnNum = PHPExcel_Cell::columnIndexFromString($highestColumn);
		
		//取得字段，这里测试表格中的第一行为数据的字段，因此先取出用来作后面数组的键名
		$filed = array();
		for($i=0; $i<$highestColumnNum;$i++){
			$cellName = PHPExcel_Cell::stringFromColumnIndex($i).'1';
			$cellVal = $sheet->getCell($cellName)->getValue();//取得列内容
			$filed []= $cellVal;
		}
		$sheet_table_arr = array(array('code'=>'IP地址','name'=>'hostaddr'),array('code'=>'机器类型','name'=>'machine_type'),array('code'=>'端口范围','name'=>'port_range'),array('code'=>'日志目录','name'=>'logdir'),array('code'=>'wal日志目录','name'=>'wal_log_dir'),array('code'=>'存储节点数据目录','name'=>'datadir'),array('code'=>'计算节点数据目录','name'=>'comp_datadir'),array('code'=>'总内存','name'=>'total_mem'),array('code'=>'cpu核数','name'=>'total_cpu_cores'),array('code'=>'机架编号','name'=>'rack_id'));
		$tablekey=array();
		for($i=0;$i<sizeof($filed);$i++){
			foreach($sheet_table_arr as $key => $row){
				if($filed[$i]==$row['code']){
					$tablekey[]=$row['name'];
				}
			}

		}
		//开始取出数据并存入数组
		$data = array();
		for($i=2;$i<=$highestRowNum;$i++){//ignore row 1
			$row = array();
			for($j=0; $j<$highestColumnNum;$j++){
				$cellName = PHPExcel_Cell::stringFromColumnIndex($j).$i;
				$cellVal = $sheet->getCell($cellName)->getValue();
				$row[ $tablekey[$j] ] = $cellVal;
				/*if(!empty($cellVal)){
				}*/
			}
			$data []= $row;
		}
		// print_r($data);exit;
		return $data;
	}
}
