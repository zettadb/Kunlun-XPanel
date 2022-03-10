var host = window.document.location.href.substring(0, window.document.location.href.indexOf(window.document.location.pathname));
var id=$('input[name="shard_id"]').val();
$(function(){
	getNodeList();
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
//存储节点列表
function getNodeList(){
	$.ajax({
		url:getRootPath() +'/index.php/Node/getStrogeNodeList',
		method:'post',
		data:{
			id:id
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
						window.location = getRootPath()+ "/index.php/Node/shardNode?id="+id;
					}
				});
			}else {
				//刷新列表节点列表
				metalist(data);
			}
		}
	})
}
//新增元数据节点
function metalist(data) {
	$('.main .main_c').empty();
	var shard_name=$('input[name="shard_name"]').val();
	var clusters = data;
	var html_tmp = "";
	var src = '/monitor/application/views/plugin/app/images/arrow-right.svg';
	html_tmp='<div class="manage"><a></a>'+shard_name+'节点列表('+clusters.length+')</div>';
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
			+ '<p><span>创建时间：</span><span class="create_time">'
			+ cluster['create_time']
			+ '</span></p>'
			+'<p class="message '+cluster['id']+'"></p></div>' +
			'<a href="javascript:;" class="option btns" title="进入"><i class="fa fa-arrow-right"></i></a>' +
			'<a href="javascript:;" class="option del" title="删除"><i class="fa fa-trash"></i></a>' +
			'<a href="javascript:;" class="option enble"><i class="fa fa-pause-circle-o"></i></a>' +
			'<a href="javascript:;" class="option edit hi" title="编辑"><i class="icon fa fa-pencil-square-o"></i></a>' +
			'</div>';
	}
	html_tmp += '<div class="list"><a class="create create_storage"><i class="fa fa-plus"></i></a></div>';
	$('.main .main_c').append(html_tmp);
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
	$('.create_storage').click(function (){
		var cluster_name=$('input[name="cluster_name"]').val();
		var shard_name=$('input[name="shard_name"]').val();
		$('#addShardNode').find('.cluster_name').html(cluster_name);
		$('#addShardNode').find('.shard_name').html(shard_name);
		$('#addShardNode').modal('show');
	})
	//删除节点
	$('.main_c .del').click(function () {
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
					url: getRootPath() + '/index.php/Node/delShardNode',
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
						if(data.result=='accept'){
							//显示状态
							$('.main_c').find('.'+id+'').html('');
							$('.main_c').find('.'+id+'').html('(删除中...)');
							var main='main_c';
							//轮询查状态(每隔1秒执行一次，查到返回显示页面上刷新)
							interval=setInterval(function(){
								getStatus(main,id);
							},1000);

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
	var cluster_id=$('input[name="cluster_id"]').val();
	window.location = getRootPath()+'/index.php/Node?id='+cluster_id;
})
//新增存储节点保存
$('#addShardNode .confirm').click(function (){
	var add = $(this).closest('.modal-dialog');
	var cluster_name=add.find('.cluster_name').html();
	var shard_name=add.find('.shard_name').html();
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
	}if(init_storage_size){
		if(!/^[0-9]*$/.test(init_storage_size)){
			openLayer('初始化的存储值只能输入数字！');
			return;
		}
	}
	$.ajax({
		url:getRootPath()+'/index.php/Node/createStorageNode',
		method:'post',
		data:{
			cluster_name:cluster_name,
			shard_name:shard_name,
			port:port,
			buffer_pool_size:buffer_pool_size,
			cpu_cores:cpu_cores,
			init_storage_size:init_storage_size
		},
		success:function (data) {
			var data=JSON.parse(data);
			//先关闭之前的弹出层
			layer.close(layer.index);
			if(data.result=='accept'){
				//先改状态
				$('.create_storage').empty();
				$('.create_storage').removeClass('create');
				$('.create_storage').addClass('create_result');
				$('.create_storage').unbind("click");
				$('.create_storage').html('<span>创建中...</span>');
				var main='create_storage';
				//轮询查状态(每隔1秒执行一次，查到返回显示页面上刷新)
				interval=setInterval(function(){
					getStatus(main,'create_storage');
				},1000);
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
						window.location = getRootPath()+ "/index.php/Node/shardNode?id="+id;
					}
				})
			}
		}
	})
})
