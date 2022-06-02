
var interval=5;
/*jeDate("#restoreItem .restore_time",{
	festival:false,
	minDate:"1900-01-01",              //最小日期
	maxDate:"2099-12-31",              //最大日期
	method:{
		choose:function (params) {
		}
	},
	format: "YYYY-MM-DD hh:mm:ss",
});*/
jeDate("#retreatedItem .restore_time",{
	festival:false,
	minDate:"1900-01-01",              //最小日期
	maxDate:"2099-12-31",              //最大日期
	method:{
		choose:function (params) {
		}
	},
	format: "YYYY-MM-DD hh:mm:ss",
});
$(function(){
	getCluster();
})
function gotoCluster(id){
	window.location = getRootPath() + "/index.php/Main?id="+id;
}
function getCluster(){
	$.ajax({
		url:getRootPath() +'/index.php/Cluster/getCluster',
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
			//拼接html
			var data=JSON.parse(data)
			//console.log(data);
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
						window.location = getRootPath()+'/index.php/Cluster/clusterList';
					}
				});
			}else {
				//刷新列表
				refreshlist(data);
			}
		}
	})
}
function refreshlist(data) {
	$('.main_c').empty();
	var clusters = data.list;
	var html_tmp = "";
	var src = '/monitor/application/views/plugin/app/images/arrow-right.svg';
	for (var i = 0; i < clusters.length; i++) {
		var cluster = clusters[i];
		if(cluster[1].length>18){
			var cluster_name=cluster[1].substring(0,7) + "..."+cluster[1].substring(13,25);
		}else{
			var cluster_name=cluster[1];
		}
		html_tmp += '<div class="list"><div class="tisk"><div class="title" data-code="' + cluster['code'] + '" data-id="'+cluster[0]+'" data-name="'+cluster[1]+'">集群信息</div>'
			+ '<p class="ip_warp"><span>id：</span><span class="id">'
			+ cluster[0]
			+ '</span></p>'
			+ '<p title="'+cluster[1]+'"><span>集群名称：</span><span class="name">'
			+ cluster_name
			+ '</span></p>'
			+ '<p><span>创建时间：</span><span class="create_time">'
			+ cluster[2] + '</span></p></div>' +
			'<a href="javascript:;" class="option btns" title="进入集群"><i class="fa fa-arrow-right"></i></a>'+
			'<a href="javascript:;" class="option del" title="删除集群"><i class="fa fa-trash"></i></a>'+
			'<a href="javascript:;" class="option backup" title="备份集群"><i class="fa fa-copy"></i></a>'+
			// '<a href="javascript:;" class="option restore" title="恢复集群"><i class="fa fa-window-restore"></i></a>'+
			'<a href="javascript:;" class="option retreated" title="集群回档"><i class="fa fa-ils"></i></a>'+
			'</div>';
	}
	html_tmp += '<div class="list"><a class="create create_cluster"><i class="fa fa-plus"></i></a></div>';
	$('.main_c').append(html_tmp);
	//集群回档
	$('.main_c .list .retreated').click(function () {
		var id = $(this).closest('.list').find('.title').data('id');
		var name = $(this).closest('.list').find('.title').data('name');
		$.ajax({
			url: getRootPath() + '/index.php/Cluster/getBackUpCluster',
			method: 'post',
			data:{
				name:name
			},
			success:function (data){
				var data=JSON.parse(data);
				//先关闭之前的弹出层
				layer.close(layer.index);
				if(data.length>0){
					$('#restoreItem .cluster_id').html(id);
					$('#retreatedItem .backup_time').html(data[0].create_time);
					$('#retreatedItem').find('.cluster_name').html(name);
					var now = getNowFormatDate();
					$('#retreatedItem .restore_time').val(now);
					$('#retreatedItem').modal('show');
				}else{
					openLayer('数据备份文件不存在。请配置相关备份策略，或手动发起备份！');
					return;
				}
			}
		})
	})
	/*//恢复集群
	$('.main_c .list .restore').click(function () {
		var id = $(this).closest('.list').find('.title').data('id');
		var name = $(this).closest('.list').find('.title').data('name');
		//查backup_record，备份集群名称
		$.ajax({
			url: getRootPath() + '/index.php/Cluster/getBackUpCluster',
			method: 'post',
			data:{
				name:name
			},
			success:function (data){
				var data=JSON.parse(data);
				//console.log(data.length);
				//先关闭之前的弹出层
				layer.close(layer.index);
				if(data.length>0){
					layer.confirm('恢复集群后将覆盖原有集群数据，确定要进行此操作吗？', {
							title:'恢复集群',
							area:['390px','152px'], //弹框的大小
							btn: ['确定','取消'] //按钮
						},
						function(){
							$.ajax({
								url: getRootPath() + '/index.php/Cluster/getRestoreCluster',
								method: 'post',
								success:function (res){
									var res=JSON.parse(res);
									//先关闭之前的弹出层
									layer.close(layer.index);
									//备份集群名称
									var html_tmp='';
									for(var k=0;k<res.length;k++){
										var r = res[k];
										html_tmp += "<li><a href='javascript:;' data-ajax='"+r+"'>"+r+"</a></li>";
									}
									$('#restoreItem .restore_warp .ka_drop_list ul').empty();
									$('#restoreItem .restore_warp .ka_drop_list ul').append(html_tmp);
								}
							})
							//恢复时，本集群id赋值
							$('#restoreItem .cluster_id').html(id);
							$('#restoreItem .cluster_name').html(name);
							$('#restoreItem .backup_time').html(data[0].create_time);
							var now = getNowFormatDate();
							$('#restoreItem .restore_time').val(now);
							$('#restoreItem').modal('show');
						},
						function(){
						});

				}else{
					openLayer('数据备份文件不存在。请配置相关备份策略，或手动发起备份！');
					return;
				}
			}
		})
	})*/
	//备份集群
	$('.main_c .list .backup').click(function () {
		var id = $(this).closest('.list').find('.title').data('id');
		var name = $(this).closest('.list').find('.title').data('name');
		layer.confirm('确定要备份'+name+'这个集群吗？', {
				title:'备份集群',
				btn: ['确定','取消'] //按钮
			},
			function(){
				$.ajax({
					url: getRootPath() + '/index.php/Cluster/backUpCluster',
					method: 'post',
					data: {
						cluster_name: name,
					},
					success:function (data){
						var data=JSON.parse(data);
						//先关闭之前的弹出层
						layer.close(layer.index);
						$('#backupItem').modal('hide');
						var main='main_c';
						if(data.res.result=='accept'){
							//显示状态
							$('.main_c').find('.'+id+'').html('');
							$('.main_c').find('.'+id+'').html('(备份中...)');
							//轮询查状态(每隔10秒执行一次，查到返回显示页面上刷新)
							interval=setInterval(function(){
								getStatus(main,id,data.uuid);
							},10000);
						}else if(data.res.result=='busy'){
							$('.main_c').find('.'+id+'').html('');
							$('.main_c').find('.'+id+'').html('(系统正在操作中，请等待一会！)');
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
	//点击进入,执行操作
	$('.main_c .list .btns').click(function () {
		var code = $(this).closest('.list').find('.title').data('code');
		var id = $(this).closest('.list').find('.title').data('id');
		if(code==500){
			layer.open({
				type: 1,
				title: false,
				//打开关闭按钮
				closeBtn: 1,
				shadeClose: false,
				skin: 'tanhcuang',
				content: '该集群离线了，检查后重试！'
			});
		}else{
			gotoCluster(id);
		}
	})
	//新增集群信息
	$('.create_cluster').click(function (){
		$('#addCluster').find('.buffer_pool').val(1);
		$('#addCluster').modal('show');
	})
	//删除集群
	$('.main_c .del').click(function () {
		var id = $(this).closest('.list').find('.title').data('id');
		var name = $(this).closest('.list').find('.title').data('name');
		layer.confirm('确定要删除'+name+'这个集群吗？', {
				title:'删除集群',
				btn: ['确定','取消'] //按钮
			},
			function(){
				$.ajax({
					url: getRootPath() + '/index.php/Cluster/delCluster',
					method: 'post',
					data: {
						name: name
					},
					success:function (data){
						var data=JSON.parse(data);
						//先关闭之前的弹出层
						layer.close(layer.index);
						var main='main_c';
						if(data.res.result=='accept'){
							//显示状态
							$('.main_c ').find('.'+id+'').html('');
							$('.main_c').find('.'+id+'').html('(删除中...)');
							//轮询查状态(每隔10秒执行一次，查到返回显示页面上刷新)
							interval=setInterval(function(){
								getStatus(main,id,data.uuid);
							},10000);
						}else if(data.res.result=='busy'){
							$('.main_c').find('.'+id+'').html('');
							$('.main_c').find('.'+id+'').html('(系统正在操作中，请等待一会！)');
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
//创建集群
$('#addCluster .confirm').click(function () {
	var add_cluster = $(this).closest('.modal-dialog');
	var buffer_pool = add_cluster.find('input[name="buffer_pool"]').val();
	var shards_count = add_cluster.find('input[name="shards_count"]').val();
	var snode_count = add_cluster.find('input[name="snode_count"]').val();
	var max_storage_size = add_cluster.find('input[name="max_storage_size"]').val();
	var comp_count = add_cluster.find('input[name="comp_count"]').val();
	var max_connections = add_cluster.find('input[name="max_connections"]').val();
	var cpu_cores = add_cluster.find('input[name="cpu_cores"]').val();
	var ha_mode = add_cluster.find('input[name="ha_mode"]').val();
	if(!ha_mode){
		openLayer('请选择ha模式!');
		return;
	}
	if(!shards_count){
		openLayer('请选择存储分片总个数！');
		return;
	}
	if(ha_mode=='mgr'){
		if(!snode_count){
			openLayer('请选择每分片含节点总个数！');
			return;
		}
	}else if(ha_mode=='no_rep'){
		snode_count=add_cluster.find('.snode_warp .snode_count').html();
	}
	if(!comp_count){
		openLayer('请输入计算节点总个数！');
		return;
	}
	/*if(!max_connections){
		openLayer('请输入计算节点最大连接数！');
		return;
	}
	if(!cpu_cores){
		openLayer('请输入cpu核数！');
		return;
	}*/
	if(!buffer_pool){
		openLayer('请输入缓冲池大小！');
		return;
	}
	/*if(!(/^[A-Za-z0-9_]+$/.test(cluster_name))){
		openLayer('集群名称只能输入数字、字母、下划线!');
		return;
	}*/
	if(max_storage_size){
		if(!/^[0-9]*$/.test(max_storage_size)){
			openLayer('所有分片的最大存储值只能输入数字！');
			return;
		}
	}
	if(!/^[0-9]*$/.test(comp_count)){
		openLayer('计算节点总个数只能输入数字！');
		return;
	}
	/*if(!/^[0-9]*$/.test(max_connections)){
		openLayer('计算节点最大连接数只能输入数字！');
		return;
	}
	if(!/^[0-9]*$/.test(cpu_cores)){
		openLayer('cpu核数只能输入数字！');
		return;
	}*/
	if(!/^[0-9]*$/.test(buffer_pool)){
		openLayer('缓冲池大小只能输入数字！');
		return;
	}
	var verifyClusterName = "";
	//判断集群名称是否重复
	/*$.ajax({
		url:getRootPath()+'/index.php/Cluster/verifyClusterName',
		method:'post',
		async: false,
		data:{
			cluster_name:cluster_name
		},
		success:function(data){
			var data = data;
			if(data=="集群名称重复"){
				layer.open({
					type: 1,
					title: false,
					//打开关闭按钮
					closeBtn: 1,
					shadeClose: false,
					skin: 'tanhcuang',
					content: "集群名称重复了，请修改！"
				});
			}
			//没有重复,可以写入信息
			else{
				verifyClusterName = 1;
			}
		},
		error:function(){
			console.log('验证集群名称出错');
		}
	})
	if(verifyClusterName==1) {*/
	$.ajax({
		url: getRootPath() + '/index.php/Cluster/createCluster',
		method: 'POST',
		dataType: 'json',
		data: {
			buffer_pool: buffer_pool,
			shards_count: shards_count,
			snode_count: snode_count,
			max_storage_size: 1,
			comp_count: comp_count,
			max_connections: 1,
			cpu_cores: 1,
			ha_mode: ha_mode,
		},
		success: function (data) {
			//var data=JSON.parse(data);
			//console.log(data.result);
			//先关闭之前的弹出层
			layer.close(layer.index);
			$('#addCluster').modal('hide');
			var main='create_cluster';
			if(data.res.result=='accept'){
				//先改状态
				$('.'+main+'').empty();
				$('.'+main+'').removeClass('create');
				$('.'+main+'').addClass('create_result');
				$('.'+main+'').unbind("click");
				$('.'+main+'').html('<span>创建中...</span>');
				//轮询查状态(每隔10秒执行一次，查到返回显示页面上刷新)</span>
				interval=setInterval(function(){
					getStatus(main,'create',data.uuid);
				},10000);
			}else if(data.res.result=='busy'){
				$('.'+main+'').html('');
				$('.'+main+'').empty();
				$('.'+main+'').removeClass('create');
				$('.'+main+'').addClass('create_result');
				$('.'+main+'').unbind("click");
				$('.'+main+'').html('<span>系统正在操作中，请等待一会！</span>');
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
						window.location = getRootPath();
					}
				})
			}
		}
	})
	// }
})
//恢复集群保存
$('#restoreItem .confirm').click(function (){
	var add = $(this).closest('.modal-dialog');
	var backup_name=add.find('.cluster_name').val();
	var restore_name=add.find('input[name="restore"]').val();
	var restore_time=add.find('input[name="restore_time"]').val();
	var backup_time=add.find('.backup_time').html();
	var id=add.find('.cluster_id').html()
	//当前时间加一个月
	var nowDate = new Date();
	var nextMon=GetPMDate(nowDate,1);
	if(!restore_time){
		openLayer('请选择恢复时间！');
		return;
	}
	if(restore_time<backup_time){
		openLayer('恢复时间不能小于最早备份时间!');
		return;
	}
	if(restore_time>nextMon){
		openLayer('恢复时间不能大于当前系统一个月内的时间!');
		return;
	}
	if(!restore_name){
		openLayer('请选择恢复集群名称！');
		return;
	}
	$.ajax({
		url:getRootPath()+'/index.php/Cluster/restoreCluster',
		method:'POST',
		data:{
			restore_time:restore_time,
			backup_name:backup_name,
			restore_name:restore_name
		},
		success:function (data){
			var data=JSON.parse(data);
			//先关闭之前的弹出层
			layer.close(layer.index);
			$('#restoreItem').modal('hide');
			var main='main_c';
			if(data){
				if(data.res.result=='accept'){
					//显示状态
					$('.main_c').find('.'+id+'').html('');
					$('.main_c').find('.'+id+'').html('(恢复中...)');
					//轮询查状态(每隔10秒执行一次，查到返回显示页面上刷新)
					interval=setInterval(function(){
						getStatus(main,id,data.uuid);
					},10000);
				}else if(data.res.result=='busy'){
					$('.main_c').find('.'+id+'').html('');
					$('.main_c').find('.'+id+'').html('(系统正在操作中，请等待一会！)');
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
							window.location = getRootPath();
						}
					})
				}
			}
		}
	})
})
//集群回档保存
$('#retreatedItem .confirm').click(function (){
	var add = $(this).closest('.modal-dialog');
	var backup_name=add.find('.cluster_name').html();
	var restore_time=add.find('input[name="restore_time"]').val();
	var backup_time=add.find('.backup_time').html();
	var id=add.find('.cluster_id').html();
	//当前时间加一个月
	var nowDate = new Date();
	var nextMon=GetPMDate(nowDate,1);
	if(!restore_time){
		openLayer('请选择回档时间！');
		return;
	}
	if(restore_time<backup_time){
		openLayer('回档时间不能小于最早备份时间!');
		return;
	}
	if(restore_time>nextMon){
		openLayer('回档时间不能大于当前系统一个月内的时间!');
		return;
	}
	$.ajax({
		url:getRootPath()+'/index.php/Cluster/retreatedCluster',
		method:'POST',
		data:{
			restore_time:restore_time,
			backup_name:backup_name
		},
		success:function (data){
			var data=JSON.parse(data);
			//先关闭之前的弹出层
			layer.close(layer.index);
			$('#retreatedItem').modal('hide');
			if(data){
				var main='main_c';
				if(data.res.result=='accept'){
					//显示状态
					$('.main_c').find('.'+id+'').html('');
					$('.main_c').find('.'+id+'').html('(回档中...)');
					//轮询查状态(每隔10秒执行一次，查到返回显示页面上刷新)
					interval=setInterval(function(){
						getStatus(main,id,data.uuid);
					},10000);
				}else if(data.res.result=='busy'){
					//显示状态
					$('.main_c').find('.'+id+'').html('');
					$('.main_c').find('.'+id+'').html('(系统正在操作中，请等待一会！)');
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
							window.location = getRootPath();
						}
					})
				}
			}
		}
	})
})

//新建集群信息，选择复制模式
$(document).on('click','#addCluster .ha_mode_warp .ka_drop_list li',function(){
	var ha_mode = $(this).find('a').data('ajax');
	if(ha_mode=='101'){
		$('#addCluster .snode_count_warp').removeClass('hi');
		$('#addCluster .snode_warp').addClass('hi');
	}else if(ha_mode=='102'){
		$('#addCluster .snode_count_warp').addClass('hi');
		$('#addCluster .snode_warp').removeClass('hi');

	}
})

//切换
$('.back').click(function (){
	window.location = getRootPath();
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
