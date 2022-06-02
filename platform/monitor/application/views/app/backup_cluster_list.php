<?php
require 'top.php'
?>
<div class="top">
	<div class="top_wrap fr" >
		<a class="refresh" title="刷新" href="<?=base_url().'index.php/BackUpCluster/backeUpCluster'?>"><i class="fa fa-refresh"></i></a>|
		<a class="top_text">集群备份列表</a>|
		<a class="login_out back" title="返回"><i class="fa fa-mail-reply"></i></a>
	</div>
</div>
<div class="col-sm-12 main_wrap">
	<div class="searc_bar search_wrap" id="search_wrap">

	</div>

	<div class="table_wrap">
		<div class="table_bar"></div>
		<table id="table"
			   data-toolbar="#toolbar">
			<thead>
			<tr>
<!--				<th data-checkbox="true"></th>-->
				<th data-title="序号" data-align="center" data-formatter="idFormatter"></th>
				<th data-field="cluster_name" data-title="集群名称" data-align="center"></th>
				<th data-field="when_created" data-title="全量备份完成时间" data-align="center"></th>
<!--				<th  data-title="操作" data-align="center" data-formatter="operateFormat" data-events="operateEvent"></th>-->
			</tr>
			</thead>

		</table>

	</div>

	<!--分页-->
	<ul class="pager" page='<? $page ?>'>
		<?php
		$first=base_url().'index.php/BackUpCluster/backeUpCluster?page=1&keyword='.$keyword;
		echo  " <li><a href='".$first."' id='first'>首 页</a></li>";
		if($page>1) {
			$url=base_url().'index.php/BackUpCluster/backeUpCluster?page='.($page-1).'&keyword='.$keyword;
			echo "<li class=\"active\"><a href='".$url."' id='prev' >上一页</a></li>";
		}else{
			echo "<li class=\"disabled\" ><a id='prev' href='javascript:void(0);'>上一页</a></li>";
		}
		echo "<li class=\"disabled\"><a href='javascript:void(0);' id='current'>".$page."/".$total."</a></li>";
		if($page<$total) {
			$url=base_url().'index.php/BackUpCluster/backeUpCluster?page='.($page+1).'&keyword='.$keyword;
			echo "<li class=\"active\"><a href='".$url."' id='next' >下一页</a></li>";
		}else{
			echo "<li class=\"disabled\"  ><a  id='next' href='javascript:void(0);'>下一页</a></li>";
		}
		$last=base_url().'index.php/BackUpCluster/backeUpCluster?page='.$total.'&keyword='.$keyword;
		echo  " <li><a href='".$last."' id='last'>尾 页</a></li>";
		echo  " <li><input type='text' class='fenye_input' name='fenye_input'  /> </li>";
		echo  "<li><a href='#'  class='fenye_btn'>GO</a></li>";
		?>
	</ul>
</div>
<!--恢复集群-->
<div class="modal fade" id="restoreItem" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog"  style="width: 530px;">
		<div class="modal-content model_wrap">
			<div class="model_content">
				<div class="building_header">
					<p>恢复集群信息</p>
					<span class="modal_close" data-dismiss="modal">X</span>
				</div>
				<div class="modal-body building restore_Item auto_model" style="height: 350px;">
					<!--<div class="select_wrap select_pull_down backup_warp">
						<p>
							<span class="text_fl"><font class="red_star">*</font>备份集群名称：</span>
							<input type="text" class="model_input backup ka_input3 text_fr" placeholder="请选择备份集群名称" name="backup" data-ajax="" readonly="">
						</p>
						<div class="ka_drop">
							<div class="ka_drop_list backup">
								<ul>
								</ul>
							</div>
						</div>
					</div>-->
					<p>
						<span class="text_fl">原集群名称：</span>
						<span class="text_fr cluster_name"></span>
					</p>
					<p>
						<span class="text_fl"><font class="red_star">*</font>恢复时间：</span>
						<input type="text" class="restore_time date form-control text_fr" name="restore_time" placeholder="请选择一个月内恢复时间">
					</p>
					<div class="select_wrap select_pull_down restore_warp">
						<p>
							<span class="text_fl"><font class="red_star">*</font>恢复集群名称：</span>
							<input type="text" class="model_input restore ka_input3 text_fr" placeholder="请选择恢复集群名称" name="restore" data-ajax="" readonly="">
						</p>
						<div class="ka_drop">
							<div class="ka_drop_list">
								<ul>
								</ul>
							</div>
						</div>
					</div>
					<p>
						<span class="text_fl">最早备份时间：</span>
						<span class="text_fr backup_time"></span>
						<span class="cluster_id hi"></span>
					</p>

				</div>
			</div>
			<div class="modal_footer bg_eee">
				<p class="tac pb17">
					<span class="col_37A confirm">确定</span>
					<span class="col_FFA cancle"  data-dismiss="modal">取消</span>
				</p>
			</div>
		</div>
	</div>
</div>
<input type="hidden" value='<?php echo $page;?>' name="page" />
<input type="hidden" value='<?php echo $keyword;?>' name="keywords" />
<input type="hidden" value='<?php echo $pagesize;?>' name="pagesize" />
<script>
	$(function() {
		var page = getUrlParam('page');
		page = page?page:'1';
		var search_keyword = getUrlParam('keyword');
		$('#table').bootstrapTable({
			method: "get",
			undefinedText: ' ',
			url: getRootPath() + '/index.php/BackUpCluster/getBUClusterList?page='+page+'&keyword='+search_keyword,
			dataType: 'json',
			singleSelect:true,
			checkbox:true,
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
			window.location.href="backeUpCluster?keyword="+search_keyword+"&page="+page;
		})
	})
</script>
</body>
<script src="<?=base_url().'application/views/plugin/app/js/backup_cluster_list.js'?>"></script>
</html>
