<?php
require 'top.php'
?>
<div class="top">
	<div class="top_wrap fr" >
		<a class="refresh" title="刷新" href="<?=base_url().'index.php/Operation/operationList'?>"><i class="fa fa-refresh"></i></a>|
		<a class="top_text">任务操作列表</a>|
		<a class="login_out back" title="返回"><i class="fa fa-mail-reply"></i></a>
	</div>
</div>
<div class="col-sm-12 main_wrap">
<!--	<div class="searc_bar search_wrap" id="search_wrap">-->
		<!--<div class="household_type_wrap select_pull_down query_wrap col_37A fl">
			<div>
				<input type="text" class="model_input household_type ka_input3" placeholder="住户类别" name="household_type" data-ajax="" readonly>
			</div>
			<div class="ka_drop">
				<div class="ka_drop_list">
					<ul>
						<li><a href="javascript:;" data-ajax="101">户主</a></li>
						<li><a href="javascript:;" data-ajax="102">家庭成员</a></li>
						<li><a href="javascript:;" data-ajax="103">长期访客</a></li>
						<li><a href="javascript:;" data-ajax="104">租客</a></li>
					</ul>
				</div>
			</div>
		</div>-->
		<!--<div class="search_room">
			<p>
				<input type="text" class="searc_room_text" name="keyword" placeholder="可输入姓名、手机号、身份证件号码" value="" required title="">
				<a id="clear" href="javascript:;">X</a>
			</p>
			<button type="submit"><i class="fa fa-search"></i></button>
		</div>-->
<!--	</div>-->

	<div class="table_wrap">
		<div class="table_bar"></div>
		<table id="table"
			   data-toolbar="#toolbar">
			<thead>
			<tr>
				<th data-title="序号" data-align="center" data-formatter="idFormatter"></th>
				<th data-field="opration_name" data-title="任务名称" data-align="center"></th>
				<th data-field="when_created" data-title="执行时间" data-align="center" data-sortable="true"></th>
				<th data-field="result" data-title="状态" data-align="center"></th>
				<th data-field="info" data-title="结果信息" data-align="center"></th>
<!--				<th  data-title="操作" data-align="center" data-formatter="operateFormatter" data-events="operateEvents"></th>-->
			</tr>
			</thead>

		</table>

	</div>

	<!--分页-->
	<ul class="pager" page='<? $page ?>'>
		<?php
		$first=base_url().'index.php/Operation/operationList?page=1&keyword='.$keyword.'&status='.$status;
		echo  " <li><a href='".$first."' id='first'>首 页</a></li>";
		if($page>1) {
			$url=base_url().'index.php/Operation/operationList?page='.($page-1).'&keyword='.$keyword.'&status='.$status;
			echo "<li class=\"active\"><a href='".$url."' id='prev' >上一页</a></li>";
		}else{
			echo "<li class=\"disabled\" ><a id='prev' href='javascript:void(0);'>上一页</a></li>";
		}
		echo "<li class=\"disabled\"><a href='javascript:void(0);' id='current'>".$page."/".$total."</a></li>";
		if($page<$total) {
			$url=base_url().'index.php/Operation/operationList?page='.($page+1).'&keyword='.$keyword.'&status='.$status;
			echo "<li class=\"active\"><a href='".$url."' id='next' >下一页</a></li>";
		}else{
			echo "<li class=\"disabled\"  ><a  id='next' href='javascript:void(0);'>下一页</a></li>";
		}
		$last=base_url().'index.php/Operation/operationList?page='.$total.'&keyword='.$keyword.'&status='.$status;
		echo  " <li><a href='".$last."' id='last'>尾 页</a></li>";
		echo  " <li><input type='text' class='fenye_input' name='fenye_input'  /> </li>";
		echo  "<li><a href='#'  class='fenye_btn'>GO</a></li>";
		?>
	</ul>

</div>
<input type="hidden" value='<?php echo $page;?>' name="page" />
<input type="hidden" value='<?php echo $keyword;?>' name="keywords" />
<input type="hidden" value='<?php echo $pagesize;?>' name="pagesize" />
<script>
	$(function() {
		var page = getUrlParam('page');
		page = page?page:'1';
		var search_keyword = getUrlParam('keyword');
		var search_status = getUrlParam('status');

		$('#table').bootstrapTable({
			method: "get",
			undefinedText: ' ',
			url: getRootPath() + '/index.php/Operation/getOperationList?page='+page+'&keyword='+search_keyword+'&status='+search_status,
			dataType: 'json',
			responseHandler: function (res) {
				//用于处理后端返回数据
				return res;
			},
			onLoadSuccess: function (data) {  //加载成功时执行
			},
			onLoadError: function () {  //加载失败时执行
				console.info("加载数据失败");
			}
		})
		//点击分页go,判断页面跳转
		$('.fenye_btn').click(function(){
			var page = $('input[name="fenye_input"]').val();
			if(!/^[0-9]*$/.test(page)){
				openLayer('请输入数字');
				$('input[name="fenye_input"]').val('');
				return;
			}
			var pagenumber=Number(page)+"";
			var myCurrent = $('#current').text().split('/')[0];
			var myTotal = $('#current').text().split('/')[1];
			if(page!=pagenumber)
			{
				$('input[name="fenye_input"]').val(pagenumber);
				page=pagenumber;
			}
			if(Number(page)>Number(myTotal))
			{
				$('input[name="fenye_input"]').val(myTotal);
				page=myTotal;
			}
			if(Number(page)<1)
			{
				openLayer('请输入合法页数');
				$('input[name="fenye_input"]').val('');
				return;
			}
			window.location.href="operationList?keyword="+search_keyword+"&page="+page+"&status="+search_status;
		})
	})
</script>
</body>
<script src="<?=base_url().'application/views/plugin/app/js/operation_list.js'?>"></script>
</html>
