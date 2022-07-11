<?php
class Login_model extends CI_Model {
    public function __construct()
    {
    	parent::__construct();
		$this->db=$this->load->database('role',true);
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
		//先查看那个是主节点,根据主节点连接数据库todo
		$mysql="select * from performance_schema.replication_group_members;";
		$this->db->query($sql);
		return $this->db->affected_rows();
	}
	function getToken($string,$operation,$key)
	{
		$key = md5($key);
		$key_length = strlen($key);
		$string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
		$string_length = strlen($string);
		$rndkey = $box = array();
		$result = '';
		for ($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($key[$i % $key_length]);
			$box[$i] = $i;
		}
		for ($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
		for ($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
		if ($operation == 'D') {
			if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
				return substr($result, 8);
			} else {
				return '';
			}
		} else {
			return str_replace('=', '', base64_encode($result));
		}
	}
	public function authority($user_name,$priv){
		//user_id
		$sql="select id from kunlun_user where name='$user_name' ";
		$res=$this->getList($sql);
		if(!empty($res)){
			$user_id=$res[0]['id'];
			$sql_role="select role_id from kunlun_role_assign where user_id='$user_id' and valid_period='permanent' ";
			$sql_role.=" union select role_id from kunlun_role_assign where user_id='$user_id' and valid_period='from_to' and start_ts is not null and start_ts<now() and end_ts is null";
			$sql_role.=" union select role_id from kunlun_role_assign where user_id='$user_id' and valid_period='from_to' and end_ts is not null and end_ts>now() and start_ts is null";
			$sql_role.=" union select role_id from kunlun_role_assign where user_id='$user_id' and valid_period='from_to' and end_ts is not null  and start_ts is not null and start_ts<now() and end_ts>now();";
			$res_role=$this->getList($sql_role);
			if(!empty($res_role)){
				//如果只有一条数据
				$role_count=count($res_role);
				if($role_count==1){
					$role_id = $res_role[0]['role_id'];
				}elseif($role_count>1){
					$role_id='';
					foreach ($res_role as $key=>$row){
						foreach ($row as $key2=>$value2){
							if($key2=='role_id'){
								if(!empty($value2)){
									$role_id.=$value2.',';
								}
							}
						}
					}
					$role_id=substr($role_id,0,-1);
				}
				//获取该用户权限（多条记录求并集）
				$sql_priv="select count(*) as count from kunlun_role_privilege where id in($role_id) and $priv='Y';";
				$res_priv=$this->getList($sql_priv);
				if($res_priv[0]['count']>0){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}

