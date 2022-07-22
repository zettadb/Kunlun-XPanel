<?php
class Cluster_model extends CI_Model {
    public function __construct()
    {
    	parent::__construct();
		$i=1;
		$this->db=$this->load->database('default',true);
		$this->config->load('myconfig');
		$this->pg_database=$this->config->item('pg_database');
		$this->pg_username=$this->config->item('pg_username');
		$this->default_username=$this->config->item('default_username');
		$this->post_url=$this->config->item('post_url');
    }
	//查询数据
	public function getList($sql){
		$q = $this->db->query($sql); //自动转义
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
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);   //只需要设置30秒的数量就可以
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0

		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect: ')); //头部要送出'Expect: '
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($post_data)
		));
		$result = curl_exec($ch);
		$curl_errno = curl_errno($ch);
		if ($curl_errno) {
			//print_r(11);exit;
			//重新查一次cluster_mgr的主节点
			//1.如果cluster_mgr只有一条数据，且和当前$url一致，则报接口请求超时
			//2.如果cluster_mgr有多条数据，查出来的数据和当前的$url比较，看是否一致，一致报超时；不一致改myconfig.php和database.php文件，然后再次请求
			//分割字符串
			$old_url=explode('/',$url);
			$sql="select hostaddr,port from cluster_mgr_nodes ";
			$res=$this->getList($sql);
			$total=count($res);
			if($total==1){
				$sqls="select hostaddr,port from cluster_mgr_nodes where member_state='source'";
				$ress=$this->getList($sqls);
				if(!empty($ress)) {
					if($old_url[2]==$ress[0]['hostaddr'].':'.$ress[0]['port']){
						$data['status'] = 'connection failed';
						$data['error_info'] = '接口请求超时'.$old_url[2];
						return json_encode($data);
					}else{
						$data['status'] = 'connection failed';
						$data['error_info'] = '接口请求超时'.$old_url[2];
						return json_encode($data);
					}
				}else{
					$data['status'] = 'connection failed';
					$data['error_info'] = '接口请求超时，cluste_mgr没主节点';
					return json_encode($data);
				}
			}else if($total>1){
				$sqls="select hostaddr,port from cluster_mgr_nodes where member_state='source'";
				$ress=$this->getList($sqls);
				if(!empty($ress)) {
					if ($old_url[2] == $ress[0]['hostaddr'] . ':' . $ress[0]['port']) {
						$data['status'] = 'connection failed';
						$data['error_info'] = '接口请求超时'.$old_url[2];
						return json_encode($data);
					} else {
						//修改配置文件，再次请求
						//修改配置文件myconfig.php
						$post_url = 'http://' . $ress[0]['hostaddr'] . ':' . $ress[0]['port'] . '/HttpService/Emit';
						//print_r($post_url);exit;
						$f_p = fopen('./application/config/myconfig.php', 'w+');
						$file_p = "<?php
	\$config['post_url'] = '$post_url';
	\$config['key']='zetta@509';
	\$config['pg_username'] = 'abc';
	\$config['default_username'] = 'player';
	\$config['pg_database'] = 'postgres';
	\$config['db_prefix'] = 'kunlundb_';
	\$config['job_type'] = array(array('code'=>'create_cluster','name'=>'新增集群'),array('code'=>'delete_cluster','name'=>'删除集群'),array('code'=>'add_shards','name'=>'新增shard'),array('code'=>'delete_shard','name'=>'删除shard'),array('code'=>'backup_cluster','name'=>'备份集群'),array('code'=>'restore_new_cluster','name'=>'恢复集群'),array('code'=>'add_comps','name'=>'增加计算节点'),array('code'=>'delete_comp','name'=>'删除计算节点'),array('code'=>'add_nodes','name'=>'增加存储节点'),array('code'=>'delete_node','name'=>'删除存储节点'),array('code'=>'mysqld_exporter','name'=>'监控存储节点'),array('code'=>'postgres_exporter','name'=>'监控计算节点'),array('code'=>'update_prometheus','name'=>'重置prometheus'),array('code'=>'update_machine','name'=>'编辑计算机'),array('code'=>'delete_machine','name'=>'删除计算机'),array('code'=>'control_instance','name'=>'控制实例'),array('code'=>'delete_backup_storage','name'=>'删除备份存储目标'),array('code'=>'create_backup_storage','name'=>'新增备份存储目标'),array('code'=>'update_backup_storage','name'=>'编辑备份存储目标'),array('code'=>'manual_switch','name'=>'主备切换'),array('code'=>'rebuild_node','name'=>'重做备机节点'),array('code'=>'create_machine','name'=>'新增计算机'),array('code'=>'cluster_restore','name'=>'回档集群'),array('code'=>'expand_cluster','name'=>'集群扩容'));";
						fwrite($f_p, $file_p);
						fgets($f_p);
						return $this->postData($post_data, $post_url);
					}
				}else{
					$data['status'] = 'connection failed';
					$data['error_info'] = '接口请求超时，cluste_mgr没主节点'.$old_url[2];
					return json_encode($data);
				}
			}else{
				$data['status'] = 'connection failed';
				$data['error_info'] = '接口请求超时,请检查cluster_mgr是否异常'.$old_url[2];
				return json_encode($data);
			}
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
			$list=array();
			if ($result=mysqli_query($conn,$sql)) {
				// 一条条获取
				while ($row=mysqli_fetch_row($result)) {
					$row_num=array('TABLE_SCHEMA'=>$row[0],'TABLE_NAME'=>$row[1]);
					array_push($list,$row_num);
				}
				// 释放结果集合
				mysqli_free_result($result);
			}
			$arr['code']=200;
			$arr['list']=$list;
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
		error_reporting(0);//禁止显示PHP警告提示
		$conn = pg_connect("host=$host dbname=$database user=$username password=$pwd port=$port");
		if(!$conn){
			$data['error'] = 'pg_connect(): Unable to connect to PostgreSQL server';
			$data['code'] = 500;
			return $data;
		}

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
//	public function authority($user_name){
//		//user_id
//
//	}
}

