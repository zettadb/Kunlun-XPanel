$(function(){
	getCompNodeList();
})
//切换
$('.back').click(function (){
	window.location = getRootPath()+'/index.php/Computer';
})
function gotoComputer(){
	window.location = getRootPath() + "/index.php/Main";
}

function getCompNodeList(){
	$.ajax({
		url:getRootPath() +'/index.php/Computer/getCompNodeList',
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
						window.location = getRootPath()+'/index.php/Computer';
					}
				});
			}else {
				//刷新列表
				refreshlist(data);
				getListMore(data);
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
			if(cluster['sql']=='psql') {
				html_tmp += '<div class="list"><div class="tisk"><div class="title" data-code="' + cluster['code'] + '" data-sql="' + cluster['sql'] + '">'
					+ cluster['title']
					+ '</div>'
					+ '<p class="ip_warp"><span>IP：</span><span class="ip">'
					+ cluster['ip']
					+ '</span></p>'
					+ '<p><span>port：</span><span class="port">'
					+ cluster['port']
					+ '</span></p>'
					+ '<p><span>版本信息：</span><span class="version" title="' + cluster['version'] + '">'
					+ cluster['version']
					+ '</span></p>'
					+ '<p><span>创建时间：</span><span class="create_time">'
					+ cluster['create_time']
					+ '</span></p>'
					+ '</div></div>';
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
					+ '<p><span>分片名称：</span><span class="shard">'
					+ cluster['shard']
					+ '</span></p>'
					+ '<p><span>版本信息：</span><span class="version" title="' + cluster['version'] + '">'
					+ cluster['version']
					+ '</span></p>'
					+ '<p><span>创建时间：</span><span class="create_time">'
					+ cluster['create_time']
					+ '</span></p>'
					+ '</div></div>';
			}
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
				+ '<p><span>版本信息：</span><span class="version" title="' + cluster['version'] + '" style="color: red">'
				+ cluster['version']
				+ '</span></p>'
				+ '<p><span>创建时间：</span><span class="create_time">'
				+ cluster['create_time']
				+ '</span></p>'
				+ '</div></div>';
		}
	}
	$('.main').append(html_tmp);
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
		var that='main_c';
		refreshlist(clusters,'计算节点列表',that);
	}
}
