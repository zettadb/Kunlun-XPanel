//后端设置的分页参数
var pagesize = $('input[name="pagesize"]').val();
//切换
$('.back').click(function (){
	window.location = getRootPath();
})
//id格式化操作
function idFormatter(value,row,index){
	var page = $('input[name="page"]').val();
	return [
		index+1+(page-1)*pagesize
	];
}
