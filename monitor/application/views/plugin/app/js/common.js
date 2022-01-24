function getRootPath(){
	//获取当前网址，如： http://localhost:8083/proj/meun.jsp
	var curWwwPath = window.document.location.href;
	//获取主机地址之后的目录，如： proj/meun.jsp
	var pathName = window.document.location.pathname;
	var pos = curWwwPath.indexOf(pathName);
	//获取主机地址，如： http://localhost:8083
	var localhostPath = curWwwPath.substring(0, pos);
	//获取带"/"的项目名，如：/proj
	var projectName = pathName.substring(0, pathName.substr(1).indexOf('/')+1);
	return(localhostPath + projectName);
}
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
//下拉菜单
$(function(){
	$('.building>p').click(function(e){
		e.stopPropagation();
		$(this).closest('.building').find('.ka_drop').hide();
	})
	//下拉框选项
	$(document).on('click','.select_pull_down',function(){
		$(this).find('.ka_drop').slideToggle();
		$(this).find('.sub_ka_drop').slideToggle();
	})

	//下拉框赋值
	$(document).on('click','.ka_drop li',function(){
		var data_ajax = $(this).find('a').data('ajax');
		$(this).parents('.select_pull_down').find('.ka_input3').val($(this).text());
		$(this).parents('.select_pull_down').find('.ka_input3').data('ajax',data_ajax);
	})
	//鼠标移开下拉框时,下拉框自动隐藏
	$(document).on('mouseleave','.select_pull_down',function(){
		$(this).find('.ka_drop').hide();
		$(this).find('.sub_ka_drop').hide();
	})
	//二级下拉框第一级赋值
	$(document).on('click','.sub_ka_drop .first_nav',function(){
		var data_ajax = $(this).find('a').data('ajax');
		$(this).parents('.select_pull_down').find('.ka_input3').val($(this).text());
		$(this).parents('.select_pull_down').find('.ka_input3').data('ajax',data_ajax);
	})
	//点击显示二级菜单
	$(document).on('click','.sub_ka_drop .subNavWrap',function(e){
		e.stopPropagation();
		$(this).find('.subNav').slideToggle();
	})
	//第二级下拉框赋值
	$(document).on('click','.sub_ka_drop .subNav li',function(){
		var data_ajax = $(this).find('a').data('ajax');
		$(this).closest('.select_pull_down').find('.ka_input3').val($(this).text());
		$(this).closest('.select_pull_down').find('.ka_input3').data('ajax',data_ajax);
		//赋值之后隐藏下拉框
		$(this).closest('.sub_ka_drop').hide();
	})
})
//验证ip
function isValidIP(ip) {
	var reg = /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/
	return reg.test(ip);
}
function viewAll(value, row, index){
	if(value){
		var short_str = value.substring(0,15) + "...";
		// 统一修改为超过15个字,用省略号代替,点击再显示
		if(value.length>15) {
			return "<div style=\"\" title='"+value+"'><p onclick=openLayer('"+value+"')>"+short_str+"</p></div>";
		}
		else{
			return "<div style=\"\">" +value+ "</div>";
		}
	}
	else{
		return "<div style=\"\"></div>";
	}
}
//获得当前的年-月-日-时-分信息
function getNowFormatDate() {
	var date = new Date();
	var seperator1 = "-";//设置成自己想要的日期格式 年/月/日
	var seperator2 = ":";//设置成自己想要的时间格式 时:分:秒
	var month = date.getMonth() + 1;//月
	var strDate = date.getDate();//日
	var hour = date.getHours();//小时
	var min = date.getMinutes();//分钟
	var sec = date.getSeconds();//分钟
	if (month >= 1 && month <= 9)
	{
		month = "0" + month;
	}
	if (strDate >= 0 && strDate <= 9)
	{
		strDate = "0" + strDate;
	}
	if (hour >= 0 && hour <= 9)
	{
		hour = "0" + hour;
	}
	if (min >= 0 && min <= 9)
	{
		min = "0" + min;
	}
	if (sec >= 0 && sec <= 9)
	{
		sec = "0" + sec;
	}
	var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
		+ " " + hour + seperator2 +min+seperator2+sec;
	return currentdate;
}
//当前时间减3days
var year;
var Month;
var Day;
function GetPMDate(nowDate,Monnum) {
	year = nowDate.getFullYear();
	Month = nowDate.getMonth()+1;
	Day = nowDate.getDate();
	var MonthStr = "";
	var DayStr = "";
	Month = Month + Monnum;

	if (Day < 1) {
		SetDay(Day);
	}
	if (Month < 10) {
		MonthStr = '0' + Month;
	} else {
		MonthStr = Month;
	}
	if (Day < 10) {
		DayStr = '0' + Day;
	} else {
		DayStr = Day;
	}
	return year + "-" + MonthStr     + "-" + DayStr
}
function getUrlParam(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
	var r = window.location.search.substr(1).match(reg);  //匹配目标参数
	if (r != null) return decodeURIComponent(r[2]); return ""; //返回参数值
}

