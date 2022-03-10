var host = window.document.location.href.substring(0, window.document.location.href.indexOf(window.document.location.pathname));
var comp_dir='/home/kunlun/data_cn/'
var stor_dir='/home/kunlun/data_sn/'
var log_dir='/home/kunlun/logdir/'
var innodb_dir='/home/kunlun/innodb_log/'
function openLayer(title){
	layer.open({
		type: 1,
		title: false,
		closeBtn: 0,
		shadeClose: true,
		skin: 'tanhcuang',
		content: title
	});
}

$(function(){
	getNodeList()
})
function gotoVillage(ip,port,sql){
	$.ajax({
		url:getRootPath() + "/index.php/Main/enter",
		method:'post',
		data:{
			ip:ip,
			port:port,
			sql:sql
		},
		beforeSend:function(){
			layer.open({
				type: 1,
				title: false,
				closeBtn:0,
				shadeClose: false,
				skin: 'tanhcuang',
				content:'正在加载中...'
			});
		},
		success:function(data){
			//先关闭之前的弹出层
			layer.close(layer.index);
			var data = JSON.parse(data);
			if(data.code=='200'){
				if(sql=='psql'){
					//进入数据库监控页面
					window.open(host+':3000/d/postgresql/postgresql?orgId=1&refresh=5s', '_blank');
				}else if(sql=='meta'||sql=='data'){
					//进入数据库监控页面
					window.open( host+':3000/d/mysql/mysql?orgId=1&refresh=10s', '_blank');
				}
			}else{
				layer.open({
					type: 1,
					title: false,
					//打开关闭按钮
					closeBtn: 1,
					shadeClose: false,
					skin: 'tanhcuang',
					content: '连接失败，请稍后再试！',
					cancel: function () {
					}
				});
			}
		},
		error:function(){
		}
	})
}
//信息确定
$(document).on('click','.content  .confirm',function(){
	var content_wrap = $(this).closest('.content');
	var ip = content_wrap.find('input[name="ip"]').val();
	var port = content_wrap.find('input[name="port"]').val();
	if(!ip){
		openLayer('请输入ip!');
		return;
	}
	if(!port){
		openLayer('请输入端口号!');
		return;
	}
	if(isValidIP(ip) == false){
		openLayer('输入ip有误!');
		return;
	}
	if(!(/^[0-9]+$/.test(port))){
		openLayer('端口号只能输入数字!');
		return;
	}
	getNodeList()
})
$('#calculate .confirm').click(function(){
	var ip=$('#calculate .ip').html();
	var port=$('#calculate .port').html();
	var username=$('#calculate .username').val();
	var pwd=$('#calculate .pwd').val();
	if(!username){
		openLayer('请输入账号!');
		return;
	}
	if(!pwd){
		openLayer('请输入密码!');
		return;
	}
	$.ajax({
		url:getRootPath()+'/index.php/Main/enterPGSQL',
		method:'post',
		data:{
			ip:ip,
			port:port,
			username:username,
			pwd:pwd
		},
		success:function (data){
			//先关闭之前的弹出层
			layer.close(layer.index);
			var data = JSON.parse(data);
			if(data.code=='200'){
				$('#calculate').modal('hide');
				//进入数据库监控页面
				window.open(  host+':3000/d/postgresql/postgresql?orgId=1&refresh=5s', '_blank');
			}else{
				layer.open({
					type: 1,
					title: false,
					//打开关闭按钮
					closeBtn: 1,
					shadeClose: false,
					skin: 'tanhcuang',
					content: '连接失败，请稍后再试！',
					cancel: function () {
					}
				});
			}
		}
	})
})
function getNodeList(node_type){
	$.ajax({
		url:getRootPath() +'/index.php/Main/getNodeList',
		method:'post',
		data: {
			node_type:node_type
		},
		beforeSend:function(){
			//先关闭之前的弹出层
			layer.close(layer.index);
			layer.open({
				type: 1,
				title: false,
				closeBtn:0,
				shadeClose: false,
				skin: 'tanhcuang',
				content:'正在加载中...'
			});
		},
		success:function (data){
			//先关闭之前的弹出层
			layer.close(layer.index);
			//拼接html
			var data=JSON.parse(data)
			if(data.code==500){
				layer.open({
					type: 1,
					title: false,
					//打开关闭按钮
					closeBtn: 1,
					shadeClose: false,
					skin: 'tanhcuang',
					content: data.message,
					cancel: function(){
						window.location = getRootPath()+ "/index.php/Main";
					}
				});
			}else {
				/*localStorage.setItem('ip',ip);
				localStorage.setItem('port',port);*/
				localStorage.setItem('data',JSON.stringify(data));
				//$('.top .login_out').html('exit');
				//刷新列表
				refreshlist(data);
				countSecond();
				//getListMore(data);
			}
		}
	})
}
function refreshlist(data) {
	$('.main').empty();
	var clusters = data;
	var html_tmp = "";
	var src = '/monitor/application/views/plugin/app/images/arrow-right.svg';
	for (var i = 0; i < clusters.length; i++) {
		var cluster = clusters[i];
		if(cluster['code']==200){
			if(cluster['sql']!=='data'){
				html_tmp += '<div class="list"><div class="tisk"><div class="title" data-code="' + cluster['code'] + '" data-sql="' + cluster['sql'] + '">'
					+ cluster['title']
					+ '</div>'
					+ '<p class="ip_warp"><span>IP：</span><span class="ip">'
					+ cluster['ip']
					+ '</span></p>'
					+ '<p><span>port：</span><span class="port">'
					+ cluster['port']
					+ '</span></p>'
					+ '<p><span>版本信息：</span><span class="version">'
					+ cluster['version'] + '</span></p></div>' +
					'<a href="javascript:;" class="option btns" title="进入"><i class="fa fa-arrow-right"></i></a>' +
					'<a href="javascript:;" class="option del" title="删除"><i class="fa fa-trash"></i></a>' +
					'<a href="javascript:;" class="option enble"><i class="fa fa-pause-circle-o"></i></a>' +
					'<a href="javascript:;" class="option edit hi" title="编辑"><i class="icon fa fa-pencil-square-o"></i></a>' +
					'</div>';
			}else{
				html_tmp += '<div class="list"><div class="tisk"><div class="title" data-code="' + cluster['code'] + '" data-sql="' + cluster['sql'] + '">'
					+ cluster['title']
					+ '</div>'
					+ '<p class="ip_warp"><span>IP：</span><span class="ip">'
					+ cluster['ip']
					+ '</span></p>'
					+ '<p><span>port：</span><span class="port">'
					+ cluster['port']
					+ '</span></p>'
					+ '<p><span>shard：</span><span class="shard">'
					+ cluster['shard']
					+ '</span></p>'
					+ '<p><span>版本信息：</span><span class="version">'
					+ cluster['version'] + '</span></p></div>' +
					'<a href="javascript:;" class="option btns" title="进入"><i class="fa fa-arrow-right"></i></a>' +
					'<a href="javascript:;" class="option del" title="删除"><i class="fa fa-trash"></i></a>' +
					'<a href="javascript:;" class="option enble"><i class="fa fa-pause-circle-o"></i></a>' +
					'<a href="javascript:;" class="option edit hi" title="编辑"><i class="icon fa fa-pencil-square-o"></i></a>' +
					'</div>';
			}
		}else {
			html_tmp += '<div class="list"><div class="tisk"><div class="title" data-code="' + cluster['code'] + '" data-sql="' + cluster['sql'] + '">'
				+ cluster['title']
				+ '</div>'
				+ '<p class="ip_warp"><span>IP：</span><span class="ip">'
				+ cluster['ip']
				+ '</span></p>'
				+ '<p><span>port：</span><span class="port">'
				+ cluster['port']
				+ '</span></p>'
				+ '<p><span>版本信息：</span><span class="version" style="color: red">'
				+ cluster['version'] + '</span></p></div>' +
				'<a href="javascript:;" class="option btns" title="进入"><i class="fa fa-arrow-right"></i></a>' +
				'<a href="javascript:;" class="option del"><i class="fa fa-trash"></i></a>' +
				'</div>';
		}
	}
	html_tmp += '<div class="list"><a class="create"><i class="fa fa-plus"></i></a></div>';
	$('.main').append(html_tmp);

	//点击进入,执行操作
	$('.main .list .btns').click(function () {
		var ip = $(this).closest('.list').find('.ip').html();
		var port = $(this).closest('.list').find('.port').html();
		var sql = $(this).closest('.list').find('.title').data('sql');
		var code = $(this).closest('.list').find('.title').data('code');
		if(code==500){
			layer.open({
				type: 1,
				title: false,
				//打开关闭按钮
				closeBtn: 1,
				shadeClose: false,
				skin: 'tanhcuang',
				content: '该节点离线了，检查后重试！'
			});
		}else{
			if(sql=='psql'){
				$('#calculate .ip').html(ip);
				$('#calculate .port').html(port);
				$('#calculate').modal('show');
			}else{
				//验证
				gotoVillage(ip, port, sql);
			}
		}
	})
	//新增节点信息
	$('.create').click(function (){
		$('#addItem').modal('show');
	})
	//删除节点
	$('.del').click(function () {
		var ip = $(this).closest('.list').find('.ip').html();
		var port = $(this).closest('.list').find('.port').html();
		var sql = $(this).closest('.list').find('.title').data('sql');
		var code = $(this).closest('.list').find('.title').data('code');
		//$('#delItem').modal('show');
		layer.confirm('确定要删除'+ip+'：'+port+'此节点吗？', {
			btn: ['确定','取消'] //按钮
		},
		function(){
			$.ajax({
				url: getRootPath() + '/index.php/Main/delNode',
				method: 'post',
				data: {
					ip: ip,
					port: port,
					sql: sql
				},
				success:function (data){
					var data=JSON.parse(data);
					//先关闭之前的弹出层
					layer.close(layer.index);
					if(data.code==200){
						layer.open({
							type: 1,
							title: false,
							//打开关闭按钮
							closeBtn: 1,
							shadeClose: false,
							skin: 'tanhcuang',
							content: data.message,
							cancel: function () {
								window.location = getRootPath()+ "/index.php/Main";
							}
						})
					}else{
						layer.open({
							type: 1,
							title: false,
							//打开关闭按钮
							closeBtn: 1,
							shadeClose: false,
							skin: 'tanhcuang',
							content: data.message,
							cancel: function () {
								$('#addItem').modal('show');
							}
						})
					}
				}
			})
		},
		function(){
			$('#person_detail').modal('show');
		});
	})
}
//切换
$('.back').click(function (){
	window.localStorage.clear();
	window.location = getRootPath()+'/index.php/Main';
})
//局部刷新
function countSecond() {
	setTimeout(function () {
		var data = localStorage.getItem('data');
		getListMore(JSON.parse(data));
	}, 1000)
}
function getListMore(data){
	var clusters=data;
	if(clusters){
		for (var i = 0; i < clusters.length; i++) {
			var cluster = clusters[i];
			var ip = cluster['ip'];
			var port = cluster['port'];
			var sql = cluster['sql'];
			//获取版本
			$.ajax({
				url: getRootPath() + '/index.php/Main/getNodeVersion',
				method: 'post',
				cache: false,
					async: false,
				data: {
					ip: ip,
					port: port,
					sql: sql
				},
				success: function (res) {
					if(res.indexOf("pg_connect(): Unable to connect to PostgreSQL server")!==-1){
						cluster['version'] = 'Connection refused';
						cluster.code = 500;
					} else {
						var res = JSON.parse(res)
						cluster['version'] = res.version;
						cluster.code = res.code;
					}
				},
				error: function () {
					console.log("网络异常，请重试。");
				}
			});
			if (sql == 'meta'||sql=='data') {
				//是否为主副节点
				$.ajax({
					url: getRootPath() + '/index.php/Main/getPrimary',
					method: 'post',
					cache: false,
					async: false,
					data: {
						ip: ip,
						port: port
					},
					success: function (res) {
						var res = JSON.parse(res)
						cluster.title = cluster.title+res.title;
					},
					error: function () {
						console.log("网络异常，请重试。");
					}
				});
			}
		}	//console.log(clusters)
		$('.top .top_wrap').removeClass('hi');
		//刷新列表
		refreshlist(clusters);
	}
}

$('#upload .confirm').click(function(){
	var file=$('input[name="upfile"]').val();
	if(!file){
		openLayer('请先上传文件!');
		return;
	}
	//先关闭之前的弹出层
	layer.close(layer.index);
	getList()

})
function  getList(){
	$.ajax({
		method:"post",
		url:getRootPath() + "/index.php/Main/readFile",
		beforeSend:function(){
			$('#upload').modal('hide');
			layer.open({
				type: 1,
				title: false,
				closeBtn:0,
				shadeClose: false,
				skin: 'tanhcuang',
				content:'正在加载中...'
			});
		},
		success:function(data){
			//先关闭之前的弹出层
			layer.close(layer.index);
			var data=JSON.parse(data)
			if(data!=='[]'||!data){
				$('#upload').modal('hide');
				var ipmeta='';var portmeta='';var titlemeta='';
				for (var i = 0; i < data.length; i++) {
					var cluster = data[i];
					var ip = cluster['ip'];
					var port = cluster['port'];
					var sql = cluster['sql'];
					//获取版本
					$.ajax({
						url: getRootPath() + '/index.php/Main/getNodeVersion',
						method: 'post',
						cache: false,
						async: false,
						data: {
							ip: ip,
							port: port,
							sql: sql
						},
						success: function (res) {
							if (res.indexOf("pg_connect(): Unable to connect to PostgreSQL server") !== -1) {
								cluster['version'] = 'Connection refused';
								cluster.code = 500;
							} else {
								var res = JSON.parse(res)
								cluster['version'] = res.version;
								cluster.code = res.code;
							}
						},
						error: function () {
							console.log("网络异常，请重试。");
						}
					});

					if(cluster['title']=='元数据节点(主)'){
						ipmeta = cluster['ip'];
						portmeta = cluster['port'];
						titlemeta = cluster['title'];
					 }
				}
				if(titlemeta=='元数据节点(主)'){
					localStorage.setItem('ip',ipmeta);
					localStorage.setItem('port',portmeta);
					localStorage.setItem('json','json');
				}
				refreshlist(data);

			}else{
				//先关闭之前的弹出层
				layer.close(layer.index);
				$('#upload').modal('show');
			}
		}

	})
}
//新增节点信息保存
$('#addItem  .confirm').click(function (){
	var add_node = $(this).closest('.modal-dialog');
	var node_type = add_node.find('input[name="node_type"]').data('ajax');
	var ip = add_node.find('input[name="ip"]').val();
	var port = add_node.find('input[name="port"]').val();
	var xport = add_node.find('input[name="xport"]').val();
	var mgr_port = add_node.find('input[name="mgr_port"]').val();
	var dataurl = add_node.find('input[name="dataurl"]').val();
	var logurl = add_node.find('input[name="logurl"]').val();
	var innodburl = add_node.find('input[name="innodburl"]').val();
	var if_primary='';
	var shard='';
	if(!node_type){
		openLayer('请选择节点类型!');
		return;
	}
	if(!ip){
		openLayer('请输入ip!');
		return;
	}
	if(!port){
		openLayer('请输入port!');
		return;
	}
	if(isValidIP(ip) == false){
		openLayer('输入ip有误!');
		return;
	}
	if(!(/^[0-9]+$/.test(port))){
		openLayer('port只能输入数字!');
		return;
	}
	if(node_type==101){
		var username=add_node.find('input[name="username"]').val();
		var pwd=add_node.find('input[name="pwd"]').val();
		if(!username){
			openLayer('请输入账号!');
			return;
		}
		if(!pwd){
			openLayer('请输入密码!');
			return;
		}
		if(!dataurl){
			dataurl=comp_dir+port;
		}
	}else{
		var username='';
		var pwd='';
		//主节点
		if($('#addItem .if_primary_wrap input[type="radio"]').eq(0).is(':checked')){
			if_primary = true;
		}
		else{
			if_primary = false;
		}
		if(!dataurl){
			dataurl=stor_dir+port;
		}
		if(!logurl){
			logurl=log_dir+port;
		}
		if(!innodburl){
			innodburl=innodb_dir+port;
		}
		if(node_type==102){
			shard=add_node.find('input[name="node_shard"]').val();
			if(!shard){
				openLayer('请选择shard!');
				return;
			}
		}
		if(!xport){
			openLayer('请输入xport!');
			return;
		}
		if(!mgr_port){
			openLayer('请输入mgr_port!');
			return;
		}
		if(!(/^[0-9]+$/.test(xport))){
			openLayer('xport只能输入数字!');
			return;
		}
		if(!(/^[0-9]+$/.test(mgr_port))){
			openLayer('mgr_port只能输入数字!');
			return;
		}
	}
	$.ajax({
		url:getRootPath()+'/index.php/Main/insertNode',
		method:'post',
		data:{
			node_type:node_type,
			ip:ip,
			port:port,
			xport:xport,
			mgr_port:mgr_port,
			username:username,
			pwd:pwd,
			if_primary:if_primary,
			datadir:dataurl,
			logdir:logurl,
			innodbdir:innodburl,
			shard:shard,
		},
		beforeSend:function(){
			$('#addItem').modal('hide');
			layer.open({
				type: 1,
				title: false,
				closeBtn:0,
				shadeClose: false,
				skin: 'tanhcuang',
				content:'正在新增节点...'
			});
		},
		success:function (data) {
			var data=JSON.parse(data);
			//先关闭之前的弹出层
			layer.close(layer.index);
			if(data.code==200){
				layer.open({
					type: 1,
					title: false,
					//打开关闭按钮
					closeBtn: 1,
					shadeClose: false,
					skin: 'tanhcuang',
					content: data.message,
					cancel: function () {
						window.location = getRootPath()+ "/index.php/Main";
					}
				})
			}else{
				layer.open({
					type: 1,
					title: false,
					//打开关闭按钮
					closeBtn: 1,
					shadeClose: false,
					skin: 'tanhcuang',
					content: data.message,
					cancel: function () {
						$('#addItem').modal('show');
					}
				})
			}
		}
	})
})
//点击节点类型（新增）
$('#addItem .node_type_warp .node_type li').click(function(e) {
	var type = $(this).find('a').data('ajax');
	//端口清空
	$('#addItem').find('input[name="port"]').val('');
	if(type==101){
		//用户名密码
		$('#addItem .user_warp').show();
		$('#addItem .pwd_warp').show();
		//是否主节点
		$('#addItem .if_primary_wrap').hide();
		//日志、innodb路径
		$('#addItem .logurl_warp').hide();
		$('#addItem .innodburl_warp').hide();
		$('#addItem').find('input[name="dataurl"]').val('');
		$('#addItem').find('input[name="dataurl"]').val(comp_dir);
		//xport、mgr_port
		$('#addItem .xport_warp').hide();
		$('#addItem .mgr_port_warp').hide();
		//shard
		$('#addItem .node_shard_warp').hide();
		$('#addItem .node_shard_warp .ka_drop_list ul').empty();
	}else if(type==102){
		//用户名密码
		$('#addItem .user_warp').hide();
		$('#addItem .pwd_warp').hide();
		//是否主节点
		$('#addItem .if_primary_wrap').show();
		//日志目录
		$('#addItem .logurl_warp').show();
		$('#addItem .innodburl_warp').show();
		$('#addItem').find('input[name="dataurl"]').val('');
		$('#addItem').find('input[name="logurl"]').val('');
		$('#addItem').find('input[name="innodburl"]').val('');
		$('#addItem').find('input[name="dataurl"]').val(stor_dir);
		$('#addItem').find('input[name="logurl"]').val(log_dir);
		$('#addItem').find('input[name="innodburl"]').val(innodb_dir);
		//xport、mgr_port
		$('#addItem .xport_warp').show();
		$('#addItem .mgr_port_warp').show();
		//shard
		$('#addItem .node_shard_warp').show();
		/*var ip=localStorage.getItem('ip');
		var port=localStorage.getItem('port');*/
		$.ajax({
			type: "post",
			dataType: "json",
			url: getRootPath() + '/index.php/Main/getShards',
			/*data:{
				ip:ip,
				port:port
			},*/
			success: function (data) {
				if(data.code==200){
					var shards=data.shards;
					var html_tmp='';
					for(var k=0;k<shards.length;k++){
						var r = shards[k][0];
						html_tmp += "<li><a href='javascript:;' data-ajax='"+k+"'>"+r+"</a></li>";
					}
					$('#addItem .node_shard_warp .ka_drop_list ul').empty();
					$('#addItem .node_shard_warp .ka_drop_list ul').append(html_tmp);
				}else{
					layer.open({
						type: 1,
						title: false,
						//打开关闭按钮
						closeBtn: 1,
						shadeClose: false,
						skin: 'tanhcuang',
						content: data.message
					})
				}
			}
		})

	}else if(type==103){
		//用户名密码
		$('#addItem .user_warp').hide();
		$('#addItem .pwd_warp').hide();
		//是否主节点
		$('#addItem .if_primary_wrap').show();
		//日志路径
		$('#addItem .logurl_warp').show();
		$('#addItem .innodburl_warp').show();
		$('#addItem').find('input[name="dataurl"]').val('');
		$('#addItem').find('input[name="logurl"]').val('');
		$('#addItem').find('input[name="innodburl"]').val('');
		$('#addItem').find('input[name="dataurl"]').val(stor_dir);
		$('#addItem').find('input[name="logurl"]').val(log_dir);
		$('#addItem').find('input[name="innodburl"]').val(innodb_dir);
		//xport、mgr_port
		$('#addItem .xport_warp').show();
		$('#addItem .mgr_port_warp').show();
		//shard
		$('#addItem .node_shard_warp').hide();
		$('#addItem .node_shard_warp .ka_drop_list ul').empty();
	}
})

//新增节点，port鼠标移开，路径目录自动赋值
function autoUrl(){
	var node_type=$('#addItem').find('input[name="node_type"]').val();
	var port = $('#addItem').find('input[name="port"]').val();

	if(node_type==101){
		$('#addItem').find('input[name="dataurl"]').val('');
		$('#addItem').find('input[name="logurl"]').val('');
		$('#addItem').find('input[name="innodburl"]').val('');
		$('#addItem').find('input[name="dataurl"]').val(comp_dir+port);
	}else{
		$('#addItem').find('input[name="dataurl"]').val('');
		$('#addItem').find('input[name="logurl"]').val('');
		$('#addItem').find('input[name="innodburl"]').val('');
		$('#addItem').find('input[name="dataurl"]').val(stor_dir+port);
		$('#addItem').find('input[name="logurl"]').val(log_dir+port);
		$('#addItem').find('input[name="innodburl"]').val(innodb_dir+port);
	}
}
//新增shard，port鼠标移开，路径目录自动赋值
function autoUrlShard(){
	var port = $('#addShards').find('input[name="port"]').val();
	$('#addShards').find('input[name="dataurl"]').val('');
	$('#addShards').find('input[name="logurl"]').val('');
	$('#addShards').find('input[name="innodburl"]').val('');
	$('#addShards').find('input[name="dataurl"]').val(stor_dir+port);
	$('#addShards').find('input[name="logurl"]').val(log_dir+port);
	$('#addShards').find('input[name="innodburl"]').val(innodb_dir+port);
}
//点击添加shard
$('#addItem .add_btn').click(function () {
	var that = $(this);
	$('#addItem').modal('hide');
	$('#addShards').modal('show');
})
//新增shard 添加
$('#addShards .add_btn').click(function (){
	var add_shard = $('#addShards');
	var ip = add_shard.find('input[name="ip"]').val();
	var port = add_shard.find('input[name="port"]').val();
	var xport = add_shard.find('input[name="xport"]').val();
	var mgr_port = add_shard.find('input[name="mgr_port"]').val();
	var dataurl = add_shard.find('input[name="dataurl"]').val();
	var logurl = add_shard.find('input[name="logurl"]').val();
	var innodburl = add_shard.find('input[name="innodburl"]').val();
	var if_primary_name='';
	var if_primary='';
	var p_bs = $('#addShards').find('.person_building_data ul li');
	if(!ip){
		openLayer('请输入ip!');
		return;
	}
	if(!port){
		openLayer('请输入port!');
		return;
	}
	if(!xport){
		openLayer('请输入xport!');
		return;
	}
	if(!mgr_port){
		openLayer('请输入mgr_port!');
		return;
	}
	if(isValidIP(ip) == false){
		openLayer('输入ip有误!');
		return;
	}
	if(!(/^[0-9]+$/.test(port))){
		openLayer('port只能输入数字!');
		return;
	}
	if(!(/^[0-9]+$/.test(xport))){
		openLayer('xport只能输入数字!');
		return;
	}
	if(!(/^[0-9]+$/.test(mgr_port))){
		openLayer('mgr_port只能输入数字!');
		return;
	}
	//主节点
	if($('#addShards .if_primary_wrap input[type="radio"]').eq(0).is(':checked')){
		if_primary = true;
		if_primary_name = '是';
	}
	else{
		if_primary = false;
		if_primary_name = '否';
	}
	if(!dataurl){
		dataurl=stor_dir+port;
	}
	if(!logurl){
		logurl=log_dir+port;
	}
	if(!innodburl){
		innodburl=innodb_dir+port;
	}
	for(var i=0;i<p_bs.length;i++){
		var p_b = p_bs.eq(i);
		var ip_l = p_b.data('ip');
		var port_l = p_b.data('port');
		var primary_l = p_b.data('primary');
		if($.trim(ip)==$.trim(ip_l)&&$.trim(port)==$.trim(port_l)){
			openLayer('添加的ip、port不能同时重复!');
			return;
		}
		if(if_primary==true){
			if($.trim(if_primary)===$.trim(primary_l)){
				openLayer('主节点不能重复添加!');
				return;
			}
		}

	}
	var html = '<li data-ip="'+ip+'" data-port="'+port+'" data-xport="'+xport+'" data-mgr_port="'+mgr_port+'" data-primary="'+if_primary+'" data-dataurl="'+dataurl+'" data-logurl="'+logurl+'" data-innodburl="'+innodburl+'">' +
		'<p class="li_p"><span>IP:</span><span>'+ip+'</span><span class="del_i"><i class="fa fa-close"></i></span></p>' +
		'<p class="li_p"><span>port:</span><span>'+port+'</span></p>' +
		'<p class="li_p"><span>xport:</span><span>'+xport+'</span></p>' +
		'<p class="li_p"><span>mgr_port:</span><span>'+mgr_port+'</span></p>' +
		'<p class="li_p"><span>主节点:</span><span>'+if_primary_name+'</span></p>' +
		'<p class="li_p"><span>数据存放目录:</span><span>'+dataurl+'</span></p>' +
		'<p class="li_p"><span>日志存放目录:</span><span>'+logurl+'</span></p>' +
		'<p class="li_p"><span>innodb日志存放目录:</span><span>'+innodburl+'</span></p>' +
		'</li>';
	$('#addShards .person_building_data ul').prepend(html);
})
//新增shards,点击删除节点
$(document).on('click','#addShards .person_building_data ul li .fa-close',function(){
	$(this).closest('li').remove();
})
//新增shards，点击确定
$('#addShards .confirm').click(function (){
	var p_bs = $('#addShards').find('.person_building_data ul li');
	if(p_bs.length==0){
		openLayer('请添加节点!');
		return;
	}
	var count = 0;
	var shard_data=[];
	for(var i=0;i<p_bs.length;i++){
		var p_b = p_bs.eq(i);
		var ip = p_b.data('ip');
		var port = p_b.data('port');
		var xport= p_b.data('xport');
		var mgr_port= p_b.data('mgr_port');
		var primary= p_b.data('primary');
		var dataurl= p_b.data('dataurl');
		var logurl= p_b.data('logurl');
		var innodburl= p_b.data('innodburl');
		if(primary===true){
			count++;
		}
		shard_data.push({ip:ip,port:port,xport:xport,mgr_port:mgr_port,is_primary:primary,data_dir_path:dataurl,log_dir_path:logurl,innodb_log_dir_path:innodburl})
	}
	//console.log(shard_data);
	if(count==0){
		openLayer('新增片中，必须包含一个主节点!');
		return;
	}else if(count>1){
		openLayer('新增片中，主节点不能超过一个!');
		return;
	}
	$.ajax({
		url:getRootPath()+'/index.php/Main/inShards',
		method:'POST',
		dataType: 'json',
		data:{shard_data:JSON.stringify(shard_data)},
		beforeSend:function(){
			$('#addShards').modal('hide');
			layer.open({
				type: 1,
				title: false,
				closeBtn:0,
				shadeClose: false,
				skin: 'tanhcuang',
				content:'正在新增shard...'
			});
		},
		success:function (data){
			var data=JSON.parse(data);
			//先关闭之前的弹出层
			layer.close(layer.index);
			layer.open({
				type: 1,
				title: false,
				//打开关闭按钮
				closeBtn: 1,
				shadeClose: false,
				skin: 'tanhcuang',
				content: data.message,
				cancel: function () {
					window.location = getRootPath()+ "/index.php/Main";
				}
			})
		}
	})
})
//点击节点类型（列表筛选）
$('.node_type_wrap .ka_drop_list li').click(function(e) {
	var type = $(this).find('a').data('ajax');
	getNodeList(type);
})

