//后端设置的分页参数
var pagesize = $('input[name="pagesize"]').val();
var $table = $('#table');
jeDate("#restoreItem .restore_time",{
	festival:false,
	minDate:"1900-01-01",              //最小日期
	maxDate:"2099-12-31",              //最大日期
	method:{
		choose:function (params) {
		}
	},
	format: "YYYY-MM-DD hh:mm:ss",
});
//切换
$('.back').click(function (){
	window.location = getRootPath();
});
//id格式化操作
function idFormatter(value,row,index){
	var page = $('input[name="page"]').val();
	return [
		index+1+(page-1)*pagesize
	];
}
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
				if(data.res.result=='accept'){
					var main='main_c';
					//显示状态
					$('.main_c').find('.'+id+'').html('');
					$('.main_c').find('.'+id+'').html('(恢复中...)');
					//轮询查状态(每隔10秒执行一次，查到返回显示页面上刷新)
					//interval=setInterval(function(){
					getStatus(main,id,data.uuid);
					//},10000);
				}else if(data.res.result=='busy'){
					//显示状态
					$('.main_c').find('.'+id+'').html('');
					$('.main_c').find('.'+id+'').html('('+data.res.info+')');
					//轮询查状态(每隔10秒执行一次，查到返回显示页面上刷新)
					//interval=setInterval(function(){
					getStatus(main,id,data.uuid);
					//},10000);
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
							window.location = getRootPath()+'index.php/BackUpCluster/backeUpCluster';
						}
					})
				}
			}
		}
	})
})
//信息管理操作
function operateFormat(value,row,index){
	return [
		'<a class="restore " href="javascript:void(0)" style="margin-left: 10px;" title="恢复集群">',
		'<i class="fa fa-window-restore"></i>',
		'</a>',
	].join('');
}
window.operateEvent = {
	//点击详情时,弹出住户详情框
	'click .restore':function(e,value,row,index) {
		var id = getCodeSelections();
		if(id.length===0){
			openLayer('请选择一个备份信息后操作!');
			return;
		}
		if(id[0]!==row.id){
			openLayer('所选集群和恢复操作不在同一行！');
			return;
		}
		$.ajax({
			url: getRootPath() + '/index.php/Cluster/getRestoreCluster',
			method: 'post',
			success:function (data){
				var data=JSON.parse(data);
				//先关闭之前的弹出层
				layer.close(layer.index);
				//备份集群名称
				var html_tmp='';
				for(var k=0;k<data.length;k++){
					var r = data[k];
					html_tmp += "<li><a href='javascript:;' data-ajax='"+r+"'>"+r+"</a></li>";
				}
				$('#restoreItem .restore_warp .ka_drop_list ul').empty();
				$('#restoreItem .restore_warp .ka_drop_list ul').append(html_tmp);
			}
		})
		//恢复时，本集群id赋值
		$('#restoreItem .cluster_id').html(row.id);
		$('#restoreItem .cluster_name').html(row.cluster_name);
		$('#restoreItem .backup_time').html(row.when_created);
		var now = getNowFormatDate();
		$('#restoreItem .restore_time').val(now);
		$('#restoreItem').modal('show');

	}

}
//获取勾选中的表格的code
function getCodeSelections() {
	return $.map($table.bootstrapTable('getSelections'), function (row) {
		return row.id
	});
}

