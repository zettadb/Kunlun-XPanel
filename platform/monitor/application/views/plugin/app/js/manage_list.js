var comp_dir='/home/kunlun';
var stor_dir='/home/kunlun';
var log_dir='/home/kunlun';
var wal_log_dir='/home/kunlun';
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
	getComputer();
})
function gotoCluster(id){
	//window.location = getRootPath() + "/index.php/Main?id="+id;
	window.location = getRootPath() + "/index.php/Node?id="+id;
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
						window.location = getRootPath();
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
	$('.main .main_c').empty();
	var clusters = data.list;
	var html_tmp = "";
	var src = '/monitor/application/views/plugin/app/images/arrow-right.svg';
	html_tmp='<div class="manage"><a></a>集群管理('+clusters.length+')</div>';
	for (var i = 0; i < clusters.length; i++) {
		var cluster = clusters[i];
		if(cluster[1].length>18){
			var cluster_name=cluster[1].substring(0,7) + "..."+cluster[1].substring(13,25);
		}else{
			var cluster_name=cluster[1];
		}
		html_tmp += '<div class="list "><div class="tisk"><div class="title" data-code="' + cluster['code'] + '" data-id="'+cluster[0]+'" data-name="'+cluster[1]+'">集群信息</div>'
			+ '<p class="ip_warp"><span>id：</span><span class="id">'
			+ cluster[0]
			+ '</span></p>'
			+ '<p title="'+cluster[1]+'"><span>集群名称：</span><span class="name">'
			+ cluster_name
			+ '</span></p>'
			+ '<p><span>创建时间：</span><span class="create_time">'
			+ cluster[2]
			+ '</span></p><p class="message '+cluster[0]+'"></p></div>' +
			'<a href="javascript:;" class="option btns" title="进入集群"><i class="fa fa-arrow-right"></i></a>'+
			'<a href="javascript:;" class="option del" title="删除集群"><i class="fa fa-trash"></i></a>'+
			'<a href="javascript:;" class="option backup" title="备份集群"><i class="fa fa-copy"></i></a>'+
			// '<a href="javascript:;" class="option restore" title="恢复集群"><i class="fa fa-window-restore"></i></a>'+
			'<a href="javascript:;" class="option retreated" title="集群回档"><i class="fa fa-ils"></i></a>'+
			'</div>';
	}
	html_tmp += '<div class="list"><a class="create create_cluster"><i class="fa fa-plus"></i></a></div>';
	if(clusters.length>3){
		html_tmp += '<div class="more_warp more"><i class="fa fa-angle-double-down"></i></div>' +
			'<div class="more_warp back_more"><i class="fa fa-angle-double-up"></i></div>';
	}
	$('.main .main_c').append(html_tmp);
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
					$('#retreatedItem .cluster_id').html(id);
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
	//恢复集群
	/*$('.main_c .list .restore').click(function () {
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
								getStatus(main,id,data.uuid,'cluster');
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
				})
			},
			function(){
			});
	})
	//集群管理
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
	//点击集群管理
	$('.main_c .manage').click(function (){
		window.location = getRootPath() + "/index.php/Cluster/clusterList";
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
								getStatus(main,id,data.uuid,'cluster');
							},10000);
						}else if(data.res.result=='busy'){
							//显示状态
							$('.'+main+'').find('.'+id+'').html('');
							$('.'+main+'').find('.'+id+'').html('(系统正在操作中，请等待一会！)');
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
					//轮询查状态(每隔10秒执行一次，查到返回显示页面上刷新)
					interval=setInterval(function(){
						getStatus(main,'create',data.uuid,'cluster');
					},10000);
				}else if(data.res.result=='busy'){
					//显示状态
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
	var backup_name=add.find('.cluster_name').html();
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
			if(data){
				var main='main_c';
				if(data.res.result=='accept'){
					//显示状态
					$('.main_c').find('.'+id+'').html('');
					$('.main_c').find('.'+id+'').html('(恢复中...)');
					//轮询查状态(每隔10秒执行一次，查到返回显示页面上刷新)
					interval=setInterval(function(){
						getStatus(main,id,data.uuid,'cluster');
					},10000);
				}else if(data.res.result=='busy'){
					//显示状态
					$('.main_c').find('.'+id+'').html('');
					$('.main_c').find('.'+id+'').html('(系统正在操作中，请等待一会！)');
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
						getStatus(main,id,data.uuid,'cluster');
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

//创建集群点击更多
$('#addCluster .more').click(function (){
	$('#addCluster .optional').removeClass('hi');
	$('#addCluster .more').addClass('hi');
})
//创建集群点击返回
$('#addCluster .back').click(function (){
	$('#addCluster .optional').addClass('hi');
	$('#addCluster .more').removeClass('hi');
})
//计算机管理
function getComputer(){
	$.ajax({
		url:getRootPath() +'/index.php/Computer/getComputer',
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
						window.location = getRootPath();
					}
				});
			}else {
				//刷新列表
				refreshComplist(data);
			}
		}
	})
}
function refreshComplist(data) {
	$('.main .main_p').empty();
	var clusters = data.list;
	var html_tmp = "";
	var src = '/monitor/application/views/plugin/app/images/arrow-right.svg';
	html_tmp='<div class="manage">计算机管理('+clusters.length+')</div>';
	for (var i = 0; i < clusters.length; i++) {
		var cluster = clusters[i];
		html_tmp += '<div class="list"><div class="tisk"><div class="title"  data-id="'+cluster[6]+'" data-ip="'+cluster[0]+'" data-datadir="'+cluster[1]+'" data-logdir="'+cluster[2]+'" data-wal_log_dir="'+cluster[3]+'" data-comp_datadir="'+cluster[4]+'">计算机信息</div>'
			+ '<p class="ip_warp"><span>IP地址：</span><span class="ip">'
			+ cluster[0]
			+ '</span></p>'
			+ '<p><span>数据目录：</span><span class="datadir hideText" title="'+cluster[1]+'">'
			+ cluster[1]
			+ '</span></p>'
			+ '<p><span>日志目录：</span><span class="logdir" title="'+cluster[2]+'">'
			+ cluster[2]
			+ '</span></p>'
			+ '<p><span>wal日志目录：</span><span class="wal_log_dir" title="'+cluster[3]+'">'
			+ cluster[3]
			+ '</span></p>'
			+ '<p><span>计算节点数据目录：</span><span class="comp_datadir" title="'+cluster[4]+'">'
			+ cluster[4]
			+ '</span></p>'
			+ '<p><span>创建时间：</span><span class="comp_datadir">'
			+ cluster[5] + '</span></p><p class="message '+cluster[6]+'"></p></div>' +
			'<a href="javascript:;" class="option btns" title="进入计算机"><i class="fa fa-arrow-right"></i></a>' +
			'<a href="javascript:;" class="option del" title="删除计算机"><i class="fa fa-trash"></i></a>' +
			'<a href="javascript:;" class="option edit " title="编辑计算机"><i class="icon fa fa-pencil-square-o"></i></a>' +
			'</div>';
	}
	html_tmp += '<div class="list"><a class="create create_comp"><i class="fa fa-plus"></i></a></div>';
	if(clusters.length>3){
		html_tmp += '<div class="more_warp more"><i class="fa fa-angle-double-down"></i></div>' +
			'<div class="more_warp back_more"><i class="fa fa-angle-double-up"></i></div>';
	}
	$('.main .main_p').append(html_tmp);
	//计算机管理
	var div_p = document.getElementById('main_p');
	var height_p=310-(div_p.offsetHeight);
	if(div_p.offsetHeight>=300){
		$('.main_p .back_more').hide();
		$('.main_p').addClass('o_c');
		$('.main_p .more').css("margin-top",height_p+"px");
	}
	//点击更多（计算机管理）
	$('.main_p .back_more').click(function (){
		var div = document.getElementById('main_p');
		var height=310-(div.offsetHeight);
		if(div.offsetHeight>=300){
			$('.main_p').addClass('o_c');
			$('.main_p .more').css("margin-top",height+"px");
			$('.main_p .more').show();
		}
	})
	$('.main_p .more').click(function (){
		var div = document.getElementById('main_p');
		if(div.offsetHeight>=300){
			$('.main_p').removeClass('o_c');
			$('.main_p .back_more').show();
			$('.main_p .more').hide();
		}
	})
	//点击计算机管理
	$('.main_p .manage').click(function (){
		window.location = getRootPath() + "/index.php/Computer";
	})
	//点击进入,执行操作
	$('.main_p .list .btns').click(function () {
		var id = $(this).closest('.list').find('.title').data('id');
		window.location = getRootPath() + "/index.php/Computer/nodeList?id="+id;
	})
	//新增计算机信息
	$('.create_comp').click(function (){
		$('#addComputer').modal('show');
	})
	//编辑计算机信息
	$('.edit').click(function (){
		var id = $(this).closest('.list').find('.title').data('id');
		var ip = $(this).closest('.list').find('.title').data('ip');
		var datadir = $(this).closest('.list').find('.title').data('datadir');
		var logdir = $(this).closest('.list').find('.title').data('logdir');
		var wal_log_dir = $(this).closest('.list').find('.title').data('wal_log_dir');
		var comp_datadir = $(this).closest('.list').find('.title').data('comp_datadir');
		$('#editComputer .ip').html(ip);
		$('#editComputer .id').html(id);
		//目录赋值
		$('#editComputer .datadir_data ul').empty();
		$('#editComputer .logdir_data ul').empty();
		$('#editComputer .wal_data ul').empty();
		$('#editComputer .comp_data ul').empty();
		var html_datadir = '<li class="data_dir"  data-datadir="'+datadir+'">'+datadir+'<i class="fa fa-close" ></i>'+'</li>';
		$('#editComputer .datadir_data ul').prepend(html_datadir);
		var html_logdir = '<li class="log_dir"  data-logdir="'+logdir+'">'+logdir+'<i class="fa fa-close" ></i>'+'</li>';
		$('#editComputer .logdir_data ul').prepend(html_logdir);
		var html_wal_log_dir = '<li class="wal_dir"  data-wal_log_dir="'+wal_log_dir+'">'+wal_log_dir+'<i class="fa fa-close" ></i>'+'</li>';
		$('#editComputer .wal_data ul').prepend(html_wal_log_dir);
		var html_comp_datadir = '<li class="comp_dir"  data-comp_datadir="'+comp_datadir+'">'+comp_datadir+'<i class="fa fa-close" ></i>'+'</li>';
		$('#editComputer .comp_data ul').prepend(html_comp_datadir);

		$('#editComputer').modal('show');
	})
	$('.main_p .del').click(function () {
		var id = $(this).closest('.list').find('.title').data('id');
		var ip = $(this).closest('.list').find('.ip').html();
		layer.confirm('确定要删除'+ip+'这台计算机吗？', {
				btn: ['确定','取消'] //按钮
			},
			function(){
				$.ajax({
					url: getRootPath() + '/index.php/Computer/delComputer',
					method: 'post',
					data: {
						ip: ip
					},
					success:function (data){
						var data=JSON.parse(data);
						//先关闭之前的弹出层
						layer.close(layer.index);
						var main='main_p';
						if(data.res.result=='accept'){
							//显示状态
							$('.'+main+' ').find('.'+id+'').html('');
							$('.'+main+' ').find('.'+id+'').html('(删除中...)');
							//轮询查状态(每隔2秒执行一次，查到返回显示页面上刷新)
							interval=setInterval(function(){
								getStatus(main,id,data.uuid,'computer');
							},2000);
						}else if(data.res.result=='busy'){
							//显示状态
							$('.'+main+'').find('.'+id+'').html('');
							$('.'+main+'').find('.'+id+'').html('(系统正在操作中，请等待一会！)');
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
//新增计算机，路径目录自动赋值
function autoUrl(){
	var ip=$('#addComputer .ip').val();
	if(!ip){
		openLayer('请输入IP地址');return;
	}
	$('#addComputer').find('input[name="datadir"]').val('');
	$('#addComputer').find('input[name="logdir"]').val('');
	$('#addComputer').find('input[name="wal_log_dir"]').val('');
	$('#addComputer').find('input[name="comp_datadir"]').val('');
	$('#addComputer').find('input[name="datadir"]').val(stor_dir);
	$('#addComputer').find('input[name="logdir"]').val(log_dir);
	$('#addComputer').find('input[name="wal_log_dir"]').val(wal_log_dir);
	$('#addComputer').find('input[name="comp_datadir"]').val(comp_dir);
}
//创建计算机
$('#addComputer .confirm').click(function () {
	var add_cluster = $(this).closest('.modal-dialog');
	var ip = add_cluster.find('input[name="ip"]').val();
	var datadir = add_cluster.find('input[name="datadir"]').val();
	var logdir = add_cluster.find('input[name="logdir"]').val();
	var wal_log_dir = add_cluster.find('input[name="wal_log_dir"]').val();
	var comp_datadir = add_cluster.find('input[name="comp_datadir"]').val();
	var d_bs = $(this).closest('.modal-content').find('.datadir_data ul li');
	var l_bs = $(this).closest('.modal-content').find('.logdir_data ul li');
	var w_bs = $(this).closest('.modal-content').find('.wal_data ul li');
	var c_bs = $(this).closest('.modal-content').find('.comp_data ul li');
	ip=$.trim(ip);
	if(!ip){
		openLayer('请输入IP地址！');
		return;
	}
	if(isValidIP(ip) == false){
		openLayer('输入ip有误!');
		return;
	}
	if(datadir.indexOf("；") != -1){
		openLayer('数据目录不能含有中文分号!');
		return;
	}
	if(/.*[\u4e00-\u9fa5]+.*$/.test(datadir)){
		openLayer('数据目录不能含有中文!');
		return;
	}
	if(logdir.indexOf("；") != -1){
		openLayer('日志目录不能含有中文分号!');
		return;
	}
	if(/.*[\u4e00-\u9fa5]+.*$/.test(logdir)){
		openLayer('日志目录不能含有中文!');
		return;
	}
	if(wal_log_dir.indexOf("；") != -1){
		openLayer('wal目录不能含有中文分号!');
		return;
	}
	if(/.*[\u4e00-\u9fa5]+.*$/.test(wal_log_dir)){
		openLayer('wal目录不能含有中文!');
		return;
	}
	if(comp_datadir.indexOf("；") != -1){
		openLayer('计算节点数据目录不能含有中文分号!');
		return;
	}
	if(/.*[\u4e00-\u9fa5]+.*$/.test(comp_datadir)){
		openLayer('计算节点数据目录不能含有中文!');
		return;
	}
	if(d_bs.length==0){
		openLayer('请输入数据目录!');
		return;
	}
	if(l_bs.length==0){
		openLayer('请输入日志目录!');
		return;
	}
	if(w_bs.length==0){
		openLayer('请输入wal目录!');
		return;
	}
	if(c_bs.length==0){
		openLayer('请输入计算节点数据目录!');
		return;
	}
	var datadir_arr='';
	for(var i=0;i<d_bs.length;i++){
		var p_b = d_bs.eq(i);
		var code = p_b.data('datadir');
		var str=code.substr(code.length-1,1)
		if(str!==';'){
			datadir_arr+=code+';';
		}else{
			datadir_arr+=code;
		}
	}
	var logdir_arr='';
	for(var i=0;i<l_bs.length;i++){
		var p_b = l_bs.eq(i);
		var code = p_b.data('logdir');
		var str=code.substr(code.length-1,1)
		if(str!==';'){
			logdir_arr+=code+';';
		}else{
			logdir_arr+=code;
		}
	}
	var waldir_arr='';
	for(var i=0;i<w_bs.length;i++){
		var p_b = w_bs.eq(i);
		var code = p_b.data('wal_log_dir');
		var str=code.substr(code.length-1,1)
		if(str!==';'){
			waldir_arr+=code+';';
		}else{
			waldir_arr+=code;
		}
	}
	var comp_arr='';
	for(var i=0;i<c_bs.length;i++){
		var p_b = c_bs.eq(i);
		var code = p_b.data('comp_datadir');
		var str=code.substr(code.length-1,1)
		if(str!==';'){
			comp_arr+=code+';';
		}else{
			comp_arr+=code;
		}
	}

	var verifyIP = "";
	//判断ip是否重复
	$.ajax({
		url:getRootPath()+'/index.php/Computer/verifyIP',
		method:'post',
		async: false,
		data:{
			ip:ip
		},
		success:function(data){
			var data = data;
			if(data=="IP地址重复"){
				layer.open({
					type: 1,
					title: false,
					//打开关闭按钮
					closeBtn: 1,
					shadeClose: false,
					skin: 'tanhcuang',
					content: "IP地址重复了，请修改！"
				});
			}
			//没有重复,可以写入信息
			else{
				verifyIP = 1;
			}
		},
		error:function(){
			console.log('验证IP地址出错');
		}
	})
	if(verifyIP==1) {
		$.ajax({
			url: getRootPath() + '/index.php/Computer/createComputer',
			method: 'POST',
			//dataType: 'json',
			data: {
				ip: ip,
				datadir: datadir_arr,
				logdir: logdir_arr,
				wal_log_dir: waldir_arr,
				comp_datadir: comp_arr,
			},
			success: function (data) {
				var data=JSON.parse(data);
				//先关闭之前的弹出层
				layer.close(layer.index);
				$('#addComputer').modal('hide')
				var main='create_comp';
				if(data.res.result=='accept'){
					//先改状态
					$('.'+main+'').empty();
					$('.'+main+'').removeClass('create');
					$('.'+main+'').addClass('create_result');
					$('.'+main+'').unbind("click");
					$('.'+main+'').html('<span>创建中...</span>');
					//轮询查状态(每隔2秒执行一次，查到返回显示页面上刷新)
					interval=setInterval(function(){
						getStatus(main,'create',data.uuid,'computer');
					},2000);
				}else if(data.res.result=='busy'){
					//显示状态
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
	 }
})
//编辑计算机
$('#editComputer .confirm').click(function () {
	var edit = $(this).closest('.modal-dialog');
	var id = edit.find('.id').html();
	var ip = edit.find('.ip').html();
	var datadir = edit.find('input[name="datadir"]').val();
	var logdir = edit.find('input[name="logdir"]').val();
	var wal_log_dir = edit.find('input[name="wal_log_dir"]').val();
	var comp_datadir = edit.find('input[name="comp_datadir"]').val();
	var d_bs = $(this).closest('.modal-content').find('.datadir_data ul li');
	var l_bs = $(this).closest('.modal-content').find('.logdir_data ul li');
	var w_bs = $(this).closest('.modal-content').find('.wal_data ul li');
	var c_bs = $(this).closest('.modal-content').find('.comp_data ul li');
	ip=$.trim(ip);
	if(!ip){
		openLayer('请输入IP地址！');
		return;
	}
	if(isValidIP(ip) == false){
		openLayer('输入ip有误!');
		return;
	}
	if(datadir.indexOf("；") != -1){
		openLayer('数据目录不能含有中文分号!');
		return;
	}
	if(/.*[\u4e00-\u9fa5]+.*$/.test(datadir)){
		openLayer('数据目录不能含有中文!');
		return;
	}
	if(logdir.indexOf("；") != -1){
		openLayer('日志目录不能含有中文分号!');
		return;
	}
	if(/.*[\u4e00-\u9fa5]+.*$/.test(logdir)){
		openLayer('日志目录不能含有中文!');
		return;
	}
	if(wal_log_dir.indexOf("；") != -1){
		openLayer('wal目录不能含有中文分号!');
		return;
	}
	if(/.*[\u4e00-\u9fa5]+.*$/.test(wal_log_dir)){
		openLayer('wal目录不能含有中文!');
		return;
	}
	if(comp_datadir.indexOf("；") != -1){
		openLayer('计算节点数据目录不能含有中文分号!');
		return;
	}
	if(/.*[\u4e00-\u9fa5]+.*$/.test(comp_datadir)){
		openLayer('计算节点数据目录不能含有中文!');
		return;
	}
	if(d_bs.length==0){
		openLayer('请输入数据目录!');
		return;
	}
	if(l_bs.length==0){
		openLayer('请输入日志目录!');
		return;
	}
	if(w_bs.length==0){
		openLayer('请输入wal目录!');
		return;
	}
	if(c_bs.length==0){
		openLayer('请输入计算节点数据目录!');
		return;
	}
	var datadir_arr='';
	for(var i=0;i<d_bs.length;i++){
		var p_b = d_bs.eq(i);
		var code = p_b.data('datadir');
		var str=code.substr(code.length-1,1)
		if(str!==';'){
			datadir_arr+=code+';';
		}else{
			datadir_arr+=code;
		}
	}
	var logdir_arr='';
	for(var i=0;i<l_bs.length;i++){
		var p_b = l_bs.eq(i);
		var code = p_b.data('logdir');
		var str=code.substr(code.length-1,1)
		if(str!==';'){
			logdir_arr+=code+';';
		}else{
			logdir_arr+=code;
		}
	}
	var waldir_arr='';
	for(var i=0;i<w_bs.length;i++){
		var p_b = w_bs.eq(i);
		var code = p_b.data('wal_log_dir');
		var str=code.substr(code.length-1,1)
		if(str!==';'){
			waldir_arr+=code+';';
		}else{
			waldir_arr+=code;
		}
	}
	var comp_arr='';
	for(var i=0;i<c_bs.length;i++){
		var p_b = c_bs.eq(i);
		var code = p_b.data('comp_datadir');
		var str=code.substr(code.length-1,1)
		if(str!==';'){
			comp_arr+=code+';';
		}else{
			comp_arr+=code;
		}
	}
	$.ajax({
		url: getRootPath() + '/index.php/Computer/editComputer',
		method: 'POST',
		data: {
			ip: ip,
			datadir: datadir_arr,
			logdir: logdir_arr,
			wal_log_dir: waldir_arr,
			comp_datadir: comp_arr,
		},
		success: function (data) {
			var data=JSON.parse(data);
			//先关闭之前的弹出层
			layer.close(layer.index);
			$('#editComputer').modal('hide')
			var main='main_p';
			if(data.res.result=='accept'){
				//显示状态
				$('.'+main+' ').find('.'+id+'').html('');
				$('.'+main+' ').find('.'+id+'').html('(编辑中...)');
				//轮询查状态(每隔2秒执行一次，查到返回显示页面上刷新)
				interval=setInterval(function(){
					getStatus(main,id,data.uuid,'computer');
				},2000);
			}else if(data.res.result=='busy'){
				//显示状态
				$('.'+main+'').find('.'+id+'').html('');
				$('.'+main+'').find('.'+id+'').html('(系统正在操作中，请等待一会！)');
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
})
//数据目录添加
$('#addComputer .datadir_warp .add_btn,#editComputer .datadir_warp .add_btn').click(function (){
	var datadir=$(this).closest('.datadir_warp').find('input[name="datadir"]').val();
	var p_bs = $(this).closest('.modal-content').find('.datadir_data ul li');
	if(!datadir){
		openLayer('请输入数据目录!');
		return;
	}
	if(p_bs.length>=5){
		openLayer('数据目录不能超过5个!');
		return;
	}
	if(datadir.indexOf("；") != -1){
		openLayer('数据目录不能含有中文分号!');
		return;
	}
	if(/.*[\u4e00-\u9fa5]+.*$/.test(datadir)){
		openLayer('数据目录不能含有中文!');
		return;
	}
	for(var i=0;i<p_bs.length;i++){
		var p_b = p_bs.eq(i);
		var code = p_b.data('datadir');
		//以分号分割
		if(datadir.indexOf(";") != -1){
			var str=datadir.split(';');
			var len=str.length;
			if(!str[len-1]){
				len=len-1;
			}
			for(var j=0;j<len;j++){
				if($.trim(code)==$.trim(str[j])){
					openLayer('添加的数据目录重复!');
					return;
				}else if(code.indexOf(";") != -1){
					var code_str=code.split(';');
					var code_len=code_str.length;
					if(!code_str[code_len-1]){
						code_len=code_len-1;
					}
					for(var k=0;k<code_len;k++) {
						if ($.trim(code_str[k]) == $.trim(str[j])) {
							openLayer('添加的数据目录重复!');
							return;
						}
					}
				}
			}
		}else{
			if($.trim(code)==$.trim(datadir)){
				openLayer('添加的数据目录重复!');
				return;
			}
		}
	}
	var html = '<li class="data_dir"  data-datadir="'+datadir+'">'+datadir+'<i class="fa fa-close" ></i></li>';
	$(this).closest('.model_content').find('.datadir_data ul').prepend(html);
})
//数据目录点击删除节点
$(document).on('click','.datadir_data li .fa-close',function(){
	$(this).closest('li').remove();
})

//日志目录添加
$('#addComputer .logdir_warp .add_btn,#editComputer .logdir_warp .add_btn').click(function (){
	var datadir=$(this).closest('.logdir_warp').find('input[name="logdir"]').val();
	var p_bs = $(this).closest('.modal-content').find('.logdir_data ul li');
	if(!datadir){
		openLayer('请输入日志目录!');
		return;
	}
	if(p_bs.length>=5){
		openLayer('日志目录不能超过5个!');
		return;
	}
	if(datadir.indexOf("；") != -1){
		openLayer('日志目录不能含有中文分号!');
		return;
	}
	if(/.*[\u4e00-\u9fa5]+.*$/.test(datadir)){
		openLayer('日志目录不能含有中文!');
		return;
	}
	for(var i=0;i<p_bs.length;i++){
		var p_b = p_bs.eq(i);
		var code = p_b.data('logdir');
		//以分号分割
		if(datadir.indexOf(";") != -1){
			var str=datadir.split(';');
			var len=str.length;
			if(!str[len-1]){
				len=len-1;
			}
			for(var j=0;j<len;j++){
				if($.trim(code)==$.trim(str[j])){
					openLayer('添加的日志目录重复!');
					return;
				}else if(code.indexOf(";") != -1){
					var code_str=code.split(';');
					var code_len=code_str.length;
					if(!code_str[code_len-1]){
						code_len=code_len-1;
					}
					for(var k=0;k<code_len;k++) {
						if ($.trim(code_str[k]) == $.trim(str[j])) {
							openLayer('添加的日志目录重复!');
							return;
						}
					}
				}
			}
		}else{
			if($.trim(code)==$.trim(datadir)){
				openLayer('添加的日志目录重复!');
				return;
			}
		}
	}
	var html = '<li class="log_dir"  data-logdir="'+datadir+'">'+datadir+'<i class="fa fa-close" ></i></li>';
	$(this).closest('.model_content').find('.logdir_data ul').prepend(html);
})
//日志目录点击删除节点
$(document).on('click','.logdir_data li .fa-close',function(){
	$(this).closest('li').remove();
})

//wal目录添加
$('#addComputer .wal_log_dir_warp .add_btn,#editComputer .wal_log_dir_warp .add_btn').click(function (){
	var datadir=$(this).closest('.wal_log_dir_warp').find('input[name="wal_log_dir"]').val();
	var p_bs = $(this).closest('.modal-content').find('.wal_data ul li');
	if(!datadir){
		openLayer('请输入wal目录!');
		return;
	}
	if(p_bs.length>=5){
		openLayer('wal目录不能超过5个!');
		return;
	}
	if(datadir.indexOf("；") != -1){
		openLayer('wal目录不能含有中文分号!');
		return;
	}
	if(/.*[\u4e00-\u9fa5]+.*$/.test(datadir)){
		openLayer('wal目录不能含有中文!');
		return;
	}
	for(var i=0;i<p_bs.length;i++){
		var p_b = p_bs.eq(i);
		var code = p_b.data('wal_log_dir');
		//以分号分割
		if(datadir.indexOf(";") != -1){
			var str=datadir.split(';');
			var len=str.length;
			if(!str[len-1]){
				len=len-1;
			}
			for(var j=0;j<len;j++){
				if($.trim(code)==$.trim(str[j])){
					openLayer('添加的wal目录重复!');
					return;
				}else if(code.indexOf(";") != -1){
					var code_str=code.split(';');
					var code_len=code_str.length;
					if(!code_str[code_len-1]){
						code_len=code_len-1;
					}
					for(var k=0;k<code_len;k++) {
						if ($.trim(code_str[k]) == $.trim(str[j])) {
							openLayer('添加的wal目录重复!');
							return;
						}
					}
				}
			}
		}else{
			if($.trim(code)==$.trim(datadir)){
				openLayer('添加的wal目录重复!');
				return;
			}
		}
	}
	var html = '<li class="wal_dir"  data-wal_log_dir="'+datadir+'">'+datadir+'<i class="fa fa-close" ></i></li>';
	$(this).closest('.model_content').find('.wal_data ul').prepend(html);
})
//wal目录点击删除节点
$(document).on('click','.wal_data li .fa-close',function(){
	$(this).closest('li').remove();
})

//计算节点数据目录添加
$('#addComputer .comp_datadir_warp .add_btn,#editComputer .comp_datadir_warp .add_btn').click(function (){
	var datadir=$(this).closest('.comp_datadir_warp').find('input[name="comp_datadir"]').val();
	var p_bs = $(this).closest('.modal-content').find('.comp_data ul li');
	if(!datadir){
		openLayer('请输入计算节点数据目录!');
		return;
	}
	if(p_bs.length>=5){
		openLayer('计算节点数据目录不能超过5个!');
		return;
	}
	if(datadir.indexOf("；") != -1){
		openLayer('计算节点数据目录不能含有中文分号!');
		return;
	}
	if(/.*[\u4e00-\u9fa5]+.*$/.test(datadir)){
		openLayer('计算节点数据目录不能含有中文!');
		return;
	}
	for(var i=0;i<p_bs.length;i++){
		var p_b = p_bs.eq(i);
		var code = p_b.data('comp_datadir');
		//以分号分割
		if(datadir.indexOf(";") != -1){
			var str=datadir.split(';');
			var len=str.length;
			if(!str[len-1]){
				len=len-1;
			}
			for(var j=0;j<len;j++){
				if($.trim(code)==$.trim(str[j])){
					openLayer('添加的计算节点数据目录重复!');
					return;
				}else if(code.indexOf(";") != -1){
					var code_str=code.split(';');
					var code_len=code_str.length;
					if(!code_str[code_len-1]){
						code_len=code_len-1;
					}
					for(var k=0;k<code_len;k++) {
						if ($.trim(code_str[k]) == $.trim(str[j])) {
							openLayer('添加的计算节点数据目录重复!');
							return;
						}
					}
				}
			}
		}else{
			if($.trim(code)==$.trim(datadir)){
				openLayer('添加的计算节点数据目录重复!');
				return;
			}
		}
	}
	var html = '<li class="comp_dir"  data-comp_datadir="'+datadir+'">'+datadir+'<i class="fa fa-close" ></i></li>';
	$(this).closest('.model_content').find('.comp_data ul').prepend(html);
})
//wal目录点击删除节点
$(document).on('click','.comp_data li .fa-close',function(){
	$(this).closest('li').remove();
})
//查状态
var i = 0;
function getStatus(main,id,uuid,target){
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
					if(data.res.result=='succeed'){
						if(target=='computer'){
							//更新配置信息
							$.ajax({
								url: getRootPath() + '/index.php/Computer/updateFile'
							})
						}
					}
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
