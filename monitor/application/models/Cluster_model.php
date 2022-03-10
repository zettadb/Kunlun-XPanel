<?php
class Cluster_model extends CI_Model {
    public function __construct()
    {

    	parent::__construct();
		$this->db=$this->load->database('default',true);
    }
	//查询数据
	public function getList($sql){
		$q = $this->db->query($sql); //自动转义
		if ( $q->num_rows() > 0 ) {
			$arr=$q->result_array();
			return $arr;
		}
		return false;
		//return $this->db->_error_message();
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
			//print_r(json_encode($data));
			return json_encode($data);
		}
		curl_close($ch);
		return $result;

	}
}

