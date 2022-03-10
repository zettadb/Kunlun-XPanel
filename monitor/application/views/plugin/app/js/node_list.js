var host = window.document.location.href.substring(0, window.document.location.href.indexOf(window.document.location.pathname));
var comp_dir='/home/kunlun/data_cn/'
var stor_dir='/home/kunlun/data_sn/'
var log_dir='/home/kunlun/logdir/'
var innodb_dir='/home/kunlun/innodb_log/'
var cluster_id=$('input[name="cluster_id"]').val();
var interval=5;
$(function(){
	getCompNodeList();
	getShardNodeList();
	getMetaNodeList()
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
			//var data = JSON.parse(data);
			//if(data.code=='200'){
			if(data.indexOf("that container to be able to reuse that name")==-1){
				//docker时得去掉端口号
				var http=host.split(':');
				host=http[0]+':'+http[1];
				if(sql=='psql'){
					//进入数据库监控页面
					//window.open(host+':3000/d/postgresql/postgresql?orgId=1&refresh=5s', '_blank');
					window.open(host+':3000/d/000000039/postgresql-database', '_blank');//dashboard:9628 docker
				}else if(sql=='meta'||sql=='data'){
					//进入数据库监控页面
					//window.open( host+':3000/d/mysql/mysql?orgId=1&refresh=10s', '_blank');
					window.open( host+':3000/d/MQWgroiiz/mysql-overview', '_blank');//dashboard:7362 docker
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
//计算节点列表
function getCompNodeList(){
	$.ajax({
		url:getRootPath() +'/index.php/Node/getCompNodeList',
		method:'post',
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
			var data=JSON.parse(data);
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
						window.location = getRootPath()+ "/index.php/Node?id="+cluster_id;
					}
				});
			}else {
				//localStorage.setItem('data',JSON.stringify(data));
				//刷新列表
				refreshlist(data,'计算节点列表');
				getListMore(data);
				//countSecond();
			}
		}
	})
}
//存储shard列表
function getShardNodeList(){
	$.ajax({
		url:getRootPath() +'/index.php/Node/getShardNodeList',
		method:'post',
		success:function (data){
			//先关闭之前的弹出层
			layer.close(layer.index);
			var data=JSON.parse(data);
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
						window.location = getRootPath()+ "/index.php/Node?id="+cluster_id;
					}
				});
			}else {
				//刷新列表
				shardlist(data,'存储shard列表');
			}
		}
	})
}
//元数据节点列表
function getMetaNodeList(){
	$.ajax({
		url:getRootPath() +'/index.php/Node/getMetaNodeList',
		method:'post',
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
			var data=JSON.parse(data);
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
						window.location = getRootPath()+ "/index.php/Node?id="+cluster_id;
					}
				});
			}else {
				//刷新列表节点列表
				metalist(data,'元数据节点列表');
			}
		}
	})
}
//新增计算节点
function refreshlist(data,title) {
	$('.main .main_c').empty();
	var clusters = data;
	var html_tmp = "";
	var src = '/monitor/application/views/plugin/app/images/arrow-right.svg';
	html_tmp='<div class="manage"><a></a>'+title+'('+clusters.length+')</div>';
	for (var i = 0; i < clusters.length; i++) {
		var cluster = clusters[i];
		if(cluster['code']==200){
			html_tmp += '<div class="list"><div class="tisk"><div class="title" data-code="'+cluster['code']+'" data-sql="'+cluster['sql']+'" data-id="'+cluster['id']+'" data-name="'+cluster['comp_name']+'">'
				+ cluster['title']
				+ '</div>'
				+ '<p class="ip_warp"><span>IP：</span><span class="ip">'
				+ cluster['ip']
				+ '</span></p>'
				+ '<p><span>port：</span><span class="port">'
				+ cluster['port']
				+ '</span></p>'
				+ '<p><span>节点名称：</span><span class="comp_name">'
				+ cluster['comp_name']
				+ '</span></p>'
				+ '<p><span>版本信息：</span><span class="version" title="'+cluster['version']+'">'
				+ cluster['version'] + '</span></p>'
				+ '<p><span>创建时间：</span><span class="create_time">'
				+ cluster['create_time']
				+ '</span></p>'
				+'<p class="message '+cluster['id']+'"></p></div>' +
				'<a href="javascript:;" class="option btns" title="进入"><i class="fa fa-arrow-right"></i></a>' +
				'<a href="javascript:;" class="option del" title="删除"><i class="fa fa-trash"></i></a>' +
				'<a href="javascript:;" class="option enble"><i class="fa fa-pause-circle-o"></i></a>' +
				'<a href="javascript:;" class="option edit hi" title="编辑"><i class="icon fa fa-pencil-square-o"></i></a>' +
				'</div>';
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
				+ '<p><span>节点名称：</span><span class="comp_name">'
				+ cluster['comp_name']
				+ '</span></p>'
				+ '<p><span>版本信息：</span><span class="version" style="color: red" title="'+cluster['version']+'">'
				+ cluster['version'] + '</span></p>'
				+ '<p><span>创建时间：</span><span class="create_time">'
				+ cluster['create_time']
				+ '</span></p>'
				+'<p class="message '+cluster['id']+'"></p></div>' +
				'<a href="javascript:;" class="option btns" title="进入"><i class="fa fa-arrow-right"></i></a>' +
				'<a href="javascript:;" class="option del"><i class="fa fa-trash"></i></a>' +
				'</div>';
		}
	}
	html_tmp += '<div class="list"><a class="create create_comp"><i class="fa fa-plus"></i></a></div>';
	if(clusters.length>3){
		html_tmp += '<div class="more_warp more"><i class="fa fa-angle-double-down"></i></div>' +
			'<div class="more_warp back_more"><i class="fa fa-angle-double-up"></i></div>';
	}
	$('.main .main_c').append(html_tmp);
	//更多高度默认设置
	var div = document.getElementById('main_c');
	var height=310-(div.offsetHeight);
	if(div.offsetHeight>=300){
		$('.main_c .back_more').hide();
		$('.main_c').addClass('o_c');
		$('.main_c .more').css("margin-top",height+"px");
	}
	//点击更多（集群管理）
	$('.main_c .back_more').click(function (){
		var div = document.getElementById('main_c');
		var height=310-(div.offsetHeight);
		if(div.offsetHeight>=300){
			$('.main_c').addClass('o_c');
			$('.main_c .more').css("margin-top",height+"px");
			$('.main_c .more').show();
		}
	})
	$('.main_c .more').click(function (){
		var div = document.getElementById('main_c');
		if(div.offsetHeight>=300){
			$('.main_c').removeClass('o_c');
			$('.main_c .back_more').show();
			$('.main_c .more').hide();
		}
	})
	//点击进入,执行操作
	$('.main_c .list .btns').click(function () {
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
	$('.create_comp').click(function (){
		var cluster_name=$('input[name="cluster_name"]').val();
		$('#addCompItem').find('.cluster_name').html(cluster_name);
		$('#addCompItem').modal('show');
	})
	//删除节点
	$('.main_c .del').click(function () {
		var id = $(this).closest('.list').find('.title').data('id');
		var name = $(this).closest('.list').find('.title').data('name');
		var cluster_name=$('input[name="cluster_name"]').val();
		var ip = $(this).closest('.list').find('.ip').html();
		var port = $(this).closest('.list').find('.port').html();
		layer.confirm('确定要删除'+ip+'：'+port+'此节点吗？', {
				title:'删除存储节点信息',
				btn: ['确定','取消'] //按钮
			},
			function(){
				$.ajax({
					url: getRootPath() + '/index.php/Node/delCompNode',
					method: 'post',
					data: {
						name: name,
						cluster_name: cluster_name
					},
					success:function (data){
						var data=JSON.parse(data);
						//先关闭之前的弹出层
						layer.close(layer.index);
						var main='main_c';
						if(data.res.result=='accept'){
							//显示状态
							$('.'+main+'').find('.'+id+'').html('');
							$('.'+main+'').find('.'+id+'').html('(删除中...)');
							//轮询查状态(每隔1秒执行一次，查到返回显示页面上刷新)
							interval=setInterval(function(){
								getStatus(main,id,data.uuid);
							},1000);
						}else if(data.res.result=='busy'){
							//显示状态
							$('.'+main+'').find('.'+id+'').html('');
							$('.'+main+'').find('.'+id+'').html('(系统正在操作中，请等待一会！)');
						} else{
							layer.open({
								type: 1,
								title: false,
								//打开关闭按钮
								closeBtn: 1,
								shadeClose: false,
								skin: 'tanhcuang',
								content: data.res.message,
								cancel: function () {
									window.location = getRootPath()+ "/index.php/Node/shardNode?id="+id;
								}
							})
						}
					}
				})
			},
			function(){
			});
	})
}
//新增元数据节点
function metalist(data,title) {
	$('.main .main_d').empty();
	var clusters = data;
	var html_tmp = "";
	var src = '/monitor/application/views/plugin/app/images/arrow-right.svg';
	html_tmp='<div class="manage"><a></a>'+title+'('+clusters.length+')</div>';
	for (var i = 0; i < clusters.length; i++) {
		var cluster = clusters[i];
		html_tmp += '<div class="list"><div class="tisk"><div class="title" data-code="'+cluster['code']+'" data-sql="'+cluster['sql']+'" data-id="'+cluster['id']+'">'
			+ cluster['title']
			+ '</div>'
			+ '<p class="ip_warp"><span>IP：</span><span class="ip">'
			+ cluster['ip']
			+ '</span></p>'
			+ '<p><span>port：</span><span class="port">'
			+ cluster['port']
			+ '</span></p>'
			+ '<p><span>版本信息：</span><span class="version">'
			+ cluster['version']
			+ '</span></p>'
			+'<p class="message '+cluster['id']+'"></p></div>' +
			'<a href="javascript:;" class="option del" title="删除"><i class="fa fa-trash"></i></a></div>';
	}
	html_tmp += '<div class="list"><a class="create create_meta"><i class="fa fa-plus"></i></a></div>';
	if(clusters.length>3){
		html_tmp += '<div class="more_warp more"><i class="fa fa-angle-double-down"></i></div>' +
			'<div class="more_warp back_more"><i class="fa fa-angle-double-up"></i></div>';
	}
	$('.main .main_d').append(html_tmp);
	//更多高度默认设置
	var div = document.getElementById('main_d');
	var height=310-(div.offsetHeight);
	if(div.offsetHeight>=300){
		$('.main_d .back_more').hide();
		$('.main_d').addClass('o_c');
		$('.main_d .more').css("margin-top",height+"px");
	}
	//点击更多（集群管理）
	$('.main_d .back_more').click(function (){
		var div = document.getElementById('main_d');
		var height=310-(div.offsetHeight);
		if(div.offsetHeight>=300){
			$('.main_d').addClass('o_c');
			$('.main_d .more').css("margin-top",height+"px");
			$('.main_d .more').show();
		}
	})
	$('.main_d .more').click(function (){
		var div = document.getElementById('main_c');
		if(div.offsetHeight>=300){
			$('.main_d').removeClass('o_c');
			$('.main_d .back_more').show();
			$('.main_d .more').hide();
		}
	})
	//点击进入,执行操作
	$('.main_d .list .btns').click(function () {
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
	$('.main_d .create_meta').click(function (){
		var cluster_name=$('input[name="cluster_name"]').val();
		$('#addMetaItem').find('.cluster_name').html(cluster_name);
		$('#addMetaItem').modal('show');
	})
	//删除节点
	$('.main_d .del').click(function () {
		var id = $(this).closest('.list').find('.title').data('id');
		var shard_id=$('input[name="shard_id"]').val();
		var shard_name=$('input[name="shard_name"]').val();
		var cluster_name=$('input[name="cluster_name"]').val();
		layer.confirm('确定要删除'+ip+'：'+port+'此节点吗？', {
				title:'删除存储节点信息',
				btn: ['确定','取消'] //按钮
			},
			function(){
				$.ajax({
					url: getRootPath() + '/index.php/Node/delMetaNode',
					method: 'post',
					data: {
						id: id,
						shard_name: shard_name,
						shard_id: shard_id,
						cluster_name: cluster_name
					},
					success:function (data){
						var data=JSON.parse(data);
						//先关闭之前的弹出层
						layer.close(layer.index);
						var main='main_d';
						if(data.res.result=='accept'){
							//显示状态
							$('.main_d').find('.'+id+'').html('');
							$('.main_d').find('.'+id+'').html('(删除中...)');
							//轮询查状态(每隔10秒执行一次，查到返回显示页面上刷新)
							interval=setInterval(function(){
								getStatus(main,id,data.uuid);
							},10000);
						}else if(data.res.result=='busy'){
							//显示状态
							$('.'+main+'').find('.'+id+'').html('');
							$('.'+main+'').find('.'+id+'').html('(系统正在操作中，请等待一会！)');
						}
						else{
							layer.open({
								type: 1,
								title: false,
								//打开关闭按钮
								closeBtn: 1,
								shadeClose: false,
								skin: 'tanhcuang',
								content: data.message,
								cancel: function () {
									window.location = getRootPath();
								}
							})
						}
					}
				})
			},
			function(){
		});
	})
}
//新增分片
function shardlist(data,title) {
	$('.main .main_n').empty();
	var clusters = data;
	var html_tmp = "";
	var src = '/monitor/application/views/plugin/app/images/arrow-right.svg';
	html_tmp='<div class="manage"><a></a>'+title+'('+clusters.length+')</div>';
	for (var i = 0; i < clusters.length; i++) {
		var cluster = clusters[i];
		html_tmp += '<div class="list"><div class="tisk"><div class="title" data-code="'+cluster['code']+'" data-sql="'+cluster['sql']+'" data-id="'+cluster['shard_id']+'" data-name="'+cluster['shard']+'">'
			+ cluster['shard']
			+ '</div>'
			+ '<p class="ip_warp"><span>分片id：</span><span class="shard_id">'
			+ cluster['shard_id']
			+ '</span></p>'
			+ '<p><span>分片名称：</span><span class="shard">'
			+ cluster['shard']
			+ '</span></p>'
			+ '<p><span>创建时间：</span><span class="create_time">'
			+ cluster['create_time']
			+ '</span></p><p class="message '+cluster['shard_id']+'"></p></div>' +
			'<a href="javascript:;" class="option btns" title="进入"><i class="fa fa-arrow-right"></i></a>' +
			'<a href="javascript:;" class="option del" title="删除"><i class="fa fa-trash"></i></a>' +
			'<a href="javascript:;" class="option enble"><i class="fa fa-pause-circle-o"></i></a>' +
			'<a href="javascript:;" class="option edit hi" title="编辑"><i class="icon fa fa-pencil-square-o"></i></a>' +
			'</div>';
	}
	html_tmp += '<div class="list"><a class="create create_shard"><i class="fa fa-plus"></i></a></div>';
	if(clusters.length>3){
		html_tmp += '<div class="more_warp more"><i class="fa fa-angle-double-down"></i></div>' +
			'<div class="more_warp back_more"><i class="fa fa-angle-double-up"></i></div>';
	}
	$('.main .main_n').append(html_tmp);
	//更多高度默认设置
	var div = document.getElementById('main_n');
	var height=310-(div.offsetHeight);
	if(div.offsetHeight>=300){
		$('.main_n .back_more').hide();
		$('.main_n').addClass('o_c');
		$('.main_n .more').css("margin-top",height+"px");
	}
	//点击更多（集群管理）
	$('.main_n .back_more').click(function (){
		var div = document.getElementById('main_n');
		var height=310-(div.offsetHeight);
		if(div.offsetHeight>=300){
			$('.main_n').addClass('o_c');
			$('.main_n .more').css("margin-top",height+"px");
			$('.main_n .more').show();
		}
	})
	$('.main_n .more').click(function (){
		var div = document.getElementById('main_n');
		if(div.offsetHeight>=300){
			$('.main_n').removeClass('o_c');
			$('.main_n .back_more').show();
			$('.main_n .more').hide();
		}
	})
	//点击进入,执行操作
	$('.main_n .list .btns').click(function () {
		var id=$(this).closest('.list').find('.title').data('id');
		window.location = getRootPath() + "/index.php/Node/shardNode?id="+id;
	})
	//新增分片信息
	$('.create_shard').click(function (){
		var cluster_name=$('input[name="cluster_name"]').val();
		$('#addShards').find('.cluster_name').html(cluster_name);
		$('#addShards').modal('show');
	})
	//删除分片
	$('.main_n .del').click(function () {
		var id = $(this).closest('.list').find('.title').data('id');
		var name = $(this).closest('.list').find('.title').data('name');
		var cluster_name=$('input[name="cluster_name"]').val();
		layer.confirm('确定要删除'+name+'分片吗？', {
				title:'删除分片信息',
				btn: ['确定','取消'] //按钮
			},
			function(){
				$.ajax({
					url: getRootPath() + '/index.php/Node/delShard',
					method: 'post',
					data: {
						name: name,
						cluster_name: cluster_name
					},
					success:function (data){
						var data=JSON.parse(data);
						//先关闭之前的弹出层
						layer.close(layer.index);
						var main='main_n';
						if(data.res.result=='accept'){
							//显示状态
							$('.'+main+'').find('.'+id+'').html('');
							$('.'+main+'').find('.'+id+'').html('(删除中...)');
							//轮询查状态(每隔5秒执行一次，查到返回显示页面上刷新)
							interval=setInterval(function(){
								getStatus(main,id,data.uuid);
							},5000);
						}else if(data.res.result=='busy'){
							//显示状态
							$('.'+main+'').find('.'+id+'').html('');
							$('.'+main+'').find('.'+id+'').html('(系统正在操作中，请等待一会！)');
						}
						else{
							layer.open({
								type: 1,
								title: false,
								//打开关闭按钮
								closeBtn: 1,
								shadeClose: false,
								skin: 'tanhcuang',
								content: data.res.message,
								cancel: function () {
									window.location = getRootPath()+ "/index.php/Node/shardNode?id="+id;
								}
							})
						}
					}
				})
			},
			function(){
			});
	})
}

//切换
$('.back').click(function (){
	window.localStorage.clear();
	window.location = getRootPath();
})
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
		var that='main_c';
		refreshlist(clusters,'计算节点列表',that);
	}
}
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
			//var data = JSON.parse(data);
			//if(data.code=='200'){
			if(data.indexOf("that container to be able to reuse that name")==-1){
				$('#calculate').modal('hide');
				//docker时得去掉端口号
				var http=host.split(':');
				host=http[0]+':'+http[1];
				//进入数据库监控页面
				//window.open(  host+':3000/d/postgresql/postgresql?orgId=1&refresh=5s', '_blank');
				window.open(host+':3000/d/000000039/postgresql-database', '_blank');//dashboard:9628 docker
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

//新增shards，点击确定
$('#addShards .confirm').click(function (){
	var add = $(this).closest('.modal-dialog');
	var cluster_name=add.find('.cluster_name').html();
	var shards_count = add.find('input[name="shards_count"]').val();
	$.ajax({
		url:getRootPath()+'/index.php/Node/inShards',
		method:'POST',
		dataType: 'json',
		data:{
			cluster_name:cluster_name,
			shards_count:shards_count,
		},
		success:function (data){
			//var data=JSON.parse(data);
			//先关闭之前的弹出层
			layer.close(layer.index);
			$('#addShards').modal('hide');
			var main='create_shard';
			if(data.res.result=='accept'){
				//先改状态
				$('.'+main+'').empty();
				$('.'+main+'').removeClass('create');
				$('.'+main+'').addClass('create_result');
				$('.'+main+'').unbind("click");
				$('.'+main+'').html('<span>创建中...</span>');
				//轮询查状态(每隔10秒执行一次，查到返回显示页面上刷新)
				interval=setInterval(function(){
					getStatus(main,'create',data.uuid);
				},10000);
			}else if(data.res.result=='busy'){
				//显示状态
				$('.'+main+'').empty();
				$('.'+main+'').removeClass('create');
				$('.'+main+'').addClass('create_result');
				$('.'+main+'').unbind("click");
				$('.'+main+'').html('<span>系统正在操作中，请等待一会！</span>');
			}
			else {
				layer.open({
					type: 1,
					title: false,
					//打开关闭按钮
					closeBtn: 1,
					shadeClose: false,
					skin: 'tanhcuang',
					content: data.res.message,
					cancel: function () {
						window.location = getRootPath() + "/index.php//Node?id=" + cluster_id;
					}
				})
			}
		}
	})
})
//点击节点类型（列表筛选）
$('.node_type_wrap .ka_drop_list li').click(function(e) {
	var type = $(this).find('a').data('ajax');
	if(type==101){
		getCompNodeList();
		$('.main .main_n').hide();
		$('.main .main_d').hide();
		$('.main .main_c').show();
	}else if(type==102){
		getShardNodeList();
		$('.main .main_c').hide();
		$('.main .main_d').hide();
		$('.main .main_n').show();
	}else if(type==103){
		getMetaNodeList()
		$('.main .main_c').hide();
		$('.main .main_n').hide();
		$('.main .main_d').show();
	}
})

//新增元数据节点保存
$('#addMetaItem .confirm').click(function (){
	var add = $(this).closest('.modal-dialog');
	var cluster_name=add.find('.cluster_name').html();
	var port = add.find('input[name="port"]').val();
	var buffer_pool_size = add.find('input[name="buffer_pool_size"]').val();
	var cpu_cores = add.find('input[name="cpu_cores"]').val();
	var init_storage_size = add.find('input[name="init_storage_size"]').val();
	if(!buffer_pool_size){
		openLayer('请输入缓冲池大小!');
		return;
	}
	if(!cpu_cores){
		openLayer('请输入cpu核数!');
		return;
	}
	if(!init_storage_size){
		openLayer('请输入初始化的存储值!');
		return;
	}
	if(port) {
		if (!(/^[0-9]+$/.test(port))) {
			openLayer('端口只能输入数字!');
			return;
		}
	}
	if(cpu_cores){
		if(!/^[0-9]*$/.test(cpu_cores)){
			openLayer('cpu核数只能输入数字！');
			return;
		}
	}
	if(init_storage_size){
		if(!/^[0-9]*$/.test(init_storage_size)){
			openLayer('初始化的存储值只能输入数字！');
			return;
		}
	}
	$.ajax({
		url:getRootPath()+'/index.php/Node/createMetaNode',
		method:'post',
		data:{
			cluster_name:cluster_name,
			port:port,
			buffer_pool_size:buffer_pool_size,
			cpu_cores:cpu_cores,
			init_storage_size:init_storage_size
		},
		success:function (data) {
			var data=JSON.parse(data);
			//先关闭之前的弹出层
			layer.close(layer.index);
			var main='create_meta';
			if(data.res.result=='accept'){
				//先改状态
				$('.create_meta').empty();
				$('.create_meta').removeClass('create');
				$('.create_meta').addClass('create_result');
				$('.create_meta').unbind("click");
				$('.create_meta').html('<span>创建中...</span>');
				//轮询查状态(每隔10秒执行一次，查到返回显示页面上刷新)
				interval=setInterval(function(){
					getStatus(main,'create_storage',data.uuid);
				},10000);
			}else if(data.res.result=='busy'){
				$('.create_meta').empty();
				$('.create_meta').removeClass('create');
				$('.create_meta').addClass('create_result');
				$('.create_meta').unbind("click");
				$('.create_meta').html('<span>系统正在操作中，请等待一会！</span>');
			}else{
				layer.open({
					type: 1,
					title: false,
					//打开关闭按钮
					closeBtn: 1,
					shadeClose: false,
					skin: 'tanhcuang',
					content: data.res.message,
					cancel: function () {
						window.location = getRootPath()+'/index.php/Node';
					}
				})
			}
		}
	})
})
//计算节点新增保存
$('#addCompItem .confirm').click(function (){
	var add = $(this).closest('.modal-dialog');
	var cluster_name=add.find('.cluster_name').html();
	var comp_count = add.find('input[name="comp_count"]').val();
	if(!comp_count){
		openLayer('请选择节点个数!');
		return;
	}
	$.ajax({
		url:getRootPath()+'/index.php/Node/createCompNode',
		method:'post',
		data:{
			cluster_name:cluster_name,
			comp_count:comp_count
		},
		success:function (data) {
			var data=JSON.parse(data);
			//先关闭之前的弹出层
			layer.close(layer.index);
			$('#addCompItem').modal('hide');
			var main='create_comp';
			if(data.res.result=='accept'){
				//先改状态
				$('.create_comp').empty();
				$('.create_comp').removeClass('create');
				$('.create_comp').addClass('create_result');
				$('.create_comp').unbind("click");
				$('.create_comp').html('<span>创建中...</span>');
				//轮询查状态(每隔2秒执行一次，查到返回显示页面上刷新)
				interval=setInterval(function(){
				getStatus(main,'create',data.uuid);
				},2000);
			}else if(data.res.result=='busy'){
				//先改状态
				$('.create_comp').empty();
				$('.create_comp').removeClass('create');
				$('.create_comp').addClass('create_result');
				$('.create_comp').unbind("click");
				$('.create_comp').html('<span>系统正在操作中，请等待一会！</span>');
			}else{
				layer.open({
					type: 1,
					title: false,
					//打开关闭按钮
					closeBtn: 1,
					shadeClose: false,
					skin: 'tanhcuang',
					content: data.res.message,
					cancel: function () {
						window.location = getRootPath()+'/index.php/Node?id='+cluster_id;
					}
				})
			}
		}
	})
})
//查状态
var i = 0;
function getStatus(main,id,uuid){
	$.ajax({
		url:getRootPath()+'/index.php/Cluster/getStatus',
		method:'POST',
		data:{
			uuid:uuid
		},
		success:function(data){
			var data = JSON.parse(data);
			if(data){
				if(data.res.result=='succeed'||data.res.result=='error'){
					if(id=='create'){
						$('.'+main+'').removeClass('create');
						$('.'+main+'').addClass('create_result');
						$('.'+main+'').html('<span>'+data.res.info+'</span>');
					}else{
						$('.'+main+'').find('.'+id+'').html('');
						$('.'+main+'').find('.'+id+'').html('('+data.res.info+')');
					}
					clearInterval(interval);
				}else if(data.res.result=='busy'){
					i++;
					if(i==40){
						if(id=='create'){
							$('.'+main+'').removeClass('create');
							$('.'+main+'').addClass('create_result');
							$('.'+main+'').html('<p>'+data.res.info+'</p>');
						}else{
							$('.'+main+'').find('.'+id+'').html('');
							$('.'+main+'').find('.'+id+'').html('('+data.res.info+')');
						}
						clearInterval(interval);
					}
				}
			}
		}
	})
}
