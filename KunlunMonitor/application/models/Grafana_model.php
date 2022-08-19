<?php
class Grafana_model extends CI_Model {
    public function __construct()
    {
		$this->config->load('myconfig');
		$this->key=$this->config->item('grafana_key');
		//$this->key= 'Bearer eyJrIjoiblQxTUdTVUR0c0xBWDRkUTlkRjVxTkNXRUVVWXNWSlgiLCJuIjoiYXBpa2V5Y3VybCIsImlkIjoxfQ==';
    	parent::__construct();
    }

	public function postDataSource($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Accept: application/json',
			'Content-Type: application/json',
			'Authorization:'.$this->key
		));
		$result = curl_exec($ch);
		if(curl_errno($ch)){
			return curl_error($ch);
		}
		curl_close($ch);

		return $result;
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
			'Accept: application/json',
			'Content-Type: application/json',
			'Authorization:'.$this->key,
			'Content-Length: ' . strlen($post_data)
		));
		$result = curl_exec($ch);
		$curl_errno = curl_errno($ch);
		if ($curl_errno) {
			$data['status'] = 'connection failed';
			$data['error_info'] = '接口请求超时';
			return json_encode($data);
		}
		curl_close($ch);
		return $result;
	}
	public  function putData($put_data,$url)
	{
		//$key= 'Bearer eyJrIjoiblQxTUdTVUR0c0xBWDRkUTlkRjVxTkNXRUVVWXNWSlgiLCJuIjoiYXBpa2V5Y3VybCIsImlkIjoxfQ==';
		$ch = curl_init();
		// $header[] = "Content-type:image/jpeg"; //定义header，可以加多个
		curl_setopt($ch, CURLOPT_URL, $url); //定义请求地址
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); //定义请求类型，当然那个提交类型那一句就不需要了
		curl_setopt($ch, CURLOPT_HEADER, 0); //定义是否显示状态头 1：显示 ； 0：不显示
		//curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //定义header
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //定义是否直接输出返回流
		curl_setopt($ch, CURLOPT_POSTFIELDS, $put_data); //定义提交的数据
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Accept: application/json',
			'Content-Type: application/json',
			'Authorization:'.$this->key
		));
		$res = curl_exec($ch);
		$curl_errno = curl_errno($ch);
		if ($curl_errno) {
			$data['status'] = 'connection failed';
			$data['error_info'] = '接口请求超时';
			return json_encode($data);
		}
		curl_close($ch); //关闭
		return $res;
	}
	public function getKey($post_data,$url){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);   //只需要设置30秒的数量就可以
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0

		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect: ')); //头部要送出'Expect: '
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json'
		));
		$result = curl_exec($ch);
		$curl_errno = curl_errno($ch);
		if ($curl_errno) {
			$data['status'] = 'connection failed';
			$data['error_info'] = '接口请求超时';
			return json_encode($data);
		}
		curl_close($ch);
		return $result;
	}
}

