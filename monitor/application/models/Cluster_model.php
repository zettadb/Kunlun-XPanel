<?php
class Cluster_model extends CI_Model {
    public function __construct()
    {
    	parent::__construct();
		$this->db=$this->load->database('default',true);
		$this->pg_database=$this->config->item('pg_database');
		$this->pg_username=$this->config->item('pg_username');
		$this->default_username=$this->config->item('default_username');
    }
	//查询数据
	public function getList($sql){
		$q = $this->db->query($sql); //自动转义
		//print_r($q);exit;
		if ( $q->num_rows() > 0 ) {
			$arr=$q->result_array();
			return $arr;
		}
		return false;
	}
	//更新数据
	public function updateList($sql){
		$this->db->query($sql);
		return $this->db->affected_rows();
	}
	public function create_uuid($prefix="") {
		if(PATH_SEPARATOR==':'){
			return uuid_create(1);
		}else{
			$chars = md5(uniqid(mt_rand(), true));
			$uuid = substr ( $chars, 0, 8 ) . '-'
				. substr ( $chars, 8, 4 ) . '-'
				. substr ( $chars, 12, 4 ) . '-'
				. substr ( $chars, 16, 4 ) . '-'
				. substr ( $chars, 20, 12 );
			return $prefix.$uuid ;
		}
	}
	public function postData($post_data,$url){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);   //只需要设置3秒的数量就可以
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0

		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect: ')); //头部要送出'Expect: '
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($post_data)
		));
		$result = curl_exec($ch);
		$curl_errno = curl_errno($ch);
		if (curl_errno($ch)) {
			$data['result'] = 'connection failed';
			$data['message'] = '接口请求超时';
			return json_encode($data);
		}
		curl_close($ch);
		return $result;
	}
	//mysql连接
	public function getMysql($host,$port,$username,$pwd,$dbname,$sql){
		mysqli_report(MYSQLI_REPORT_STRICT);
		try {
			$conn = mysqli_connect("{$host}:{$port}",$username,$pwd,$dbname);
			$query = mysqli_query($conn,$sql);
			$arr=mysqli_fetch_row($query);
			$arr['code']=200;
			return $arr;
		} catch( Exception $e ) {
			$arr[0]=iconv('gbk', 'utf-8',  $e->getMessage());
			$arr['code']=500;
			return $arr;
		}
	}

	//pgsql
	public function sqlUser($sql,$host,$port,$username){
		$database=$this->pg_database;
		$pwd=$username;
		$conn = pg_connect("host=$host dbname=$database user=$username password=$pwd port=$port");
		$query = pg_query($conn,$sql);
		pg_close($conn);
	}
	public function DB($sql,$host,$port,$username){
		$database=$this->pg_database;
		$pwd=$username;
		$conn = pg_connect("host=$host dbname=$database user=$username password=$pwd port=$port");
		@$query = pg_query($conn, $sql);
		$q = pg_result_status($query);
		if (!$query) {
			$data['error'] = pg_last_error($conn);
			$data['code'] = 500;
			return $data;
		} else {
			if ($q == 1) {
				$data['code'] = 200;
				$data['q'] = $q;
				return $data;
			} else {
				//查询语句
				//获取字段的参数名pg_field_name
				$td = array();
				$tdn = pg_num_rows($query);
				for ($i = 0; $i < $tdn; ++$i) {
					$td[] = pg_fetch_object($query, $i);
				}
				$data['code'] = 200;
				$data['arr'] = $td;
				$data['q'] = $q;
				return $td;
			}
			pg_close($conn);
		}
	}
	//计时函数
	function getmicrotime(){
		list($usec, $sec) = explode(" ",microtime());
		return (float)sprintf('%.0f',(floatval($usec)+floatval($sec))*1000);
	}
	public function getResult($text,$host,$port,$username,$dbname){
			//$dbname=$_SESSION['database'];
			//$username=$_SESSION['username'];
			//$host=$this->server_ip;
			//$port=$this->port;
			$pwd=$username;
		$conn = pg_connect("host=$host dbname=$dbname user=$username password=$pwd port=$port");
		//计时开始
		$time_start =$this-> getmicrotime();
		@$query = pg_query($conn,$text);
		//计时结束.
		$time_end= $this->getmicrotime();
		$times = $time_end - $time_start;
		//插入/更新语句
		$q=pg_result_status($query);
		if(!$query) {
			$data['code']=500;
			$data['error'] = pg_last_error($conn);
			return $data;
		}else {
			if ($q == 1) {
				$res['q'] = $q;
				$res['times'] = $times;
				$res['code']=200;
				return $res;
			} else {
				//查询语句
				//获取字段的参数名pg_field_name
				$num = pg_num_fields($query);
				$th = array();
				$td = array();
				for ($j = 0; $j < $num; ++$j) {
					$colname[$j] = pg_field_name($query, $j);
					$th[] = $colname[$j];
				}
				$tdn = pg_num_rows($query);
				for ($i = 0; $i < $tdn; ++$i) {
					$td[] = pg_fetch_object($query, $i);
				}

				if (is_object($td)) {
					$td = (array)$td;
				} else {
					$td = $this->toArray($td);
				}
				$data['title'] = $th;
				$data['arr'] = $td;
				$data['times'] = $times;
				$data['q'] = $q;
				$data['code']=200;
				return $data;
			}
			pg_close($conn);
		}
	}
	public  function toArray($td){
		if(is_array($td)) {
			foreach($td as $key=>$value) {
				$td[$key] = (array)$value;
			}
		}
		return $td;
	}
}

