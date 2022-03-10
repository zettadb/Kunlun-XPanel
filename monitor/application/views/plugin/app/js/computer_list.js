var comp_dir='/home/kunlun/data_cn/';
var stor_dir='/home/kunlun/data_sn/';
var log_dir='/home/kunlun/logdir/';
var wal_log_dir='/home/kunlun/wal_log/';
$(function(){
	getComputer();
})
//切换
$('.back').click(function (){
	window.location = getRootPath();
})
function gotoComputer(){
	window.location = getRootPath() + "/index.php/Main";
}

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
				refreshlist(data);
			}
		}
	})
}
function refreshlist(data) {
	$('.main').empty();
	var clusters = data.list;
	var html_tmp = "";
	var src = '/monitor/application/views/plugin/app/images/arrow-right.svg';
	for (var i = 0; i < clusters.length; i++) {
		var cluster = clusters[i];
		html_tmp += '<div class="list"><div class="tisk"><div class="title"  data-id="'+cluster[6]+'" data-ip="'+cluster[0]+'">计算机信息</div>'
			+ '<p class="ip_warp"><span>IP地址：</span><span class="ip">'
			+ cluster[0]
			+ '</span></p>'
			+ '<p><span>数据目录：</span><span class="datadir">'
			+ cluster[1]
			+ '</span></p>'
			+ '<p><span>日志目录：</span><span class="logdir">'
			+ cluster[2]
			+ '</span></p>'
			+ '<p><span>wal日志目录：</span><span class="wal_log_dir">'
			+ cluster[3]
			+ '</span></p>'
			+ '<p class="comp_datadir"><span>计算节点数据目录：</span><span class="wal_log_dir">'
			+ cluster[4]
			+ '</span></p>'
			+ '<p><span>创建时间：</span><span class="comp_datadir">'
			+ cluster[5] + '</span></p></div>' +
			'<a href="javascript:;" class="option btns" title="进入计算机"><i class="fa fa-arrow-right"></i></a>' +
			'<a href="javascript:;" class="option del" title="删除计算机"><i class="fa fa-trash"></i></a>' +
			'</div>';
	}
	html_tmp += '<div class="list"><a class="create"><i class="fa fa-plus"></i></a></div>';
	$('.main').append(html_tmp);

	//点击进入,执行操作
	$('.main .list .btns').click(function () {
		var id = $(this).closest('.list').find('.title').data('id');
		window.location = getRootPath() + "/index.php/Computer/nodeList?id="+id;
		//gotoComputer(ip);
	})
	//新增计算机信息
	$('.create').click(function (){
		$('#addComputer').modal('show');
	})
	$('.del').click(function () {
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
					layer.open({
						type: 1,
						title: false,
						//打开关闭按钮
						closeBtn: 1,
						shadeClose: false,
						skin: 'tanhcuang',
						content: data.message,
						cancel: function () {
							window.location = getRootPath()+'/index.php/Computer';
						}
					})
				}
			})
		},
		function(){

		});
	})

}
//新增计算机，路径目录自动赋值
function autoUrl(){
	//var port = $('#addComputer').find('input[name="port"]').val();
	$('#addComputer').find('input[name="datadir"]').val('');
	$('#addComputer').find('input[name="logdir"]').val('');
	$('#addComputer').find('input[name="wal_log_dir"]').val('');
	$('#addComputer').find('input[name="comp_datadir"]').val('');
	$('#addComputer').find('input[name="datadir"]').val(stor_dir);
	$('#addComputer').find('input[name="logdir"]').val(log_dir);
	$('#addComputer').find('input[name="wal_log_dir"]').val(wal_log_dir);
	$('#addComputer').find('input[name="comp_datadir"]').val(comp_dir);
}

/*
//创建计算机
$('#addComputer .confirm').click(function () {
	var add_cluster = $(this).closest('.modal-dialog');
	var ip = add_cluster.find('input[name="ip"]').val();
	var datadir = add_cluster.find('input[name="datadir"]').val();
	var logdir = add_cluster.find('input[name="logdir"]').val();
	var wal_log_dir = add_cluster.find('input[name="wal_log_dir"]').val();
	var comp_datadir = add_cluster.find('input[name="comp_datadir"]').val();
	ip=$.trim(ip);
	if(!ip){
		openLayer('请输入IP地址！');
		return;
	}
	if(isValidIP(ip) == false){
		openLayer('输入ip有误!');
		return;
	}
	var verifyIP = "";
	//判断集群名称是否重复
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
			dataType: 'json',
			data: {
				ip: ip,
				datadir: datadir,
				logdir: logdir,
				wal_log_dir: wal_log_dir,
				comp_datadir: comp_datadir,
			},
			success: function (data) {
				//先关闭之前的弹出层
				layer.close(layer.index);
				$('#addComputer').modal('hide')
				layer.open({
					type: 1,
					title: false,
					//打开关闭按钮
					closeBtn: 1,
					shadeClose: false,
					skin: 'tanhcuang',
					content: data.message,
					cancel: function () {
						window.location = getRootPath()+'/index.php/Computer';
					}
				})

			}
		})
	}

})
*/

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
			dataType: 'json',
			data: {
				ip: ip,
				datadir: datadir_arr,
				logdir: logdir_arr,
				wal_log_dir: waldir_arr,
				comp_datadir: comp_arr,
			},
			success: function (data) {
				//先关闭之前的弹出层
				layer.close(layer.index);
				$('#addComputer').modal('hide')
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
		})
	}
})

//数据目录添加
$('.datadir_warp .add_btn').click(function (){
	var datadir=$('#addComputer ').find('input[name="datadir"]').val();
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
	var html = '<li class="data_dir"  data-datadir="'+datadir+'">'+datadir+'<i class="fa fa-close" ></i>'+'</li>';
	$('#addComputer .datadir_data ul').prepend(html);
})
//数据目录点击删除节点
$(document).on('click','.datadir_data li .fa-close',function(){
	$(this).closest('li').remove();
})

//日志目录添加
$('.logdir_warp .add_btn').click(function (){
	var datadir=$('#addComputer ').find('input[name="logdir"]').val();
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
	/*if(datadir && !(/^(?=.*[a-zA-Z])(?=.*_)[a-zA-Z0-9_\\\\]+$/.test(datadir))){
		openLayer('日志目录只允许输入数字、字母、横线、下划线！');return;
	}*/
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
	var html = '<li class="log_dir"  data-logdir="'+datadir+'">'+datadir+'<i class="fa fa-close" ></i>'+'</li>';
	$('#addComputer .logdir_data ul').prepend(html);
})
//日志目录点击删除节点
$(document).on('click','.logdir_data li .fa-close',function(){
	$(this).closest('li').remove();
})

//wal目录添加
$('.wal_log_dir_warp .add_btn').click(function (){
	var datadir=$('#addComputer ').find('input[name="wal_log_dir"]').val();
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
	var html = '<li class="wal_dir"  data-wal_log_dir="'+datadir+'">'+datadir+'<i class="fa fa-close" ></i>'+'</li>';
	$('#addComputer .wal_data ul').prepend(html);
})
//wal目录点击删除节点
$(document).on('click','.wal_data li .fa-close',function(){
	$(this).closest('li').remove();
})

//计算节点数据目录添加
$('.comp_datadir_warp .add_btn').click(function (){
	var datadir=$('#addComputer ').find('input[name="comp_datadir"]').val();
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
	var html = '<li class="comp_dir"  data-comp_datadir="'+datadir+'">'+datadir+'<i class="fa fa-close" ></i>'+'</li>';
	$('#addComputer .comp_data ul').prepend(html);
})
//wal目录点击删除节点
$(document).on('click','.comp_data li .fa-close',function(){
	$(this).closest('li').remove();
})
