<?php
require 'top.php'
?>
<div class="top">
	<div class="top_wrap fr" >
		<a class="refresh" title="刷新" href="<?=base_url().'index.php/Computer'?>"><i class="fa fa-refresh"></i></a>|
     	<a class="login back" title="返回"><i class="fa fa-mail-reply"></i></a>
	</div>
</div>
<div class="main">
	<!--<div class="list">
		<div class="tisk">
			<div class="title">计算机信息</div>
			<p class="ip_warp"><span>IP地址：</span><span  class="ip">192.168.0.126</span></p>
			<p><span>数据目录：</span><span>3306</span></p>
			<p><span>日志目录：</span><span>v5.0.1版本</span></p>
			<p><span>wal日志目录：</span><span>v5.0.1版本</span></p>
			<p><span>计算节点数据目录：</span><span>v5.0.1版本</span></p>
			<p><span class="text_fl">创建时间:</span><span>v5.0.1版本</span></p>
		</div>
		<a href="javascript:;" class="option edit"><i class="icon fa fa-pencil-square-o"></i></a>
		<a href="javascript:;" class="option enble"><i class="fa fa-ban"></i></a>
		<a href="javascript:;" class="option enble"><i class="fa fa-pause-circle-o"></i></a>
		<a href="javascript:;" class="option del"><i class="fa fa-trash"></i></a>
		<a href="javascript:;" class="option btns"><i class="fa fa-arrow-right"></i></a>
	</div>
	<div class="list"><a class="create" data-target="#addCluster" data-toggle="modal"><i class="fa fa-plus"></i></a></div>
-->
</div>
<!--新增计算机信息-->
<div class="modal fade" id="addComputer" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog"  style="width: 530px;">
		<div class="modal-content model_wrap">
			<div class="model_content">
				<div class="building_header">
					<p>新增计算机信息</p>
					<span class="modal_close" data-dismiss="modal">X</span>
				</div>
				<div class="modal-body building addCluster">
					<p>
						<span class="text_fl"><font class="red_star">*</font>IP地址:</span>
						<input class="model_input text_fr ip" name="ip"  placeholder="请输入ipv4/ipv6域名的IP地址" onblur="autoUrl();"/>
					</p>
					<p class="datadir_warp">
						<span class="text_fl"><font class="red_star">*</font>数据目录:</span>
						<input class="model_input text_fr datadir" name="datadir"  placeholder="请输入存储节点的数据目录"/>
						<span class="add_btn"><i class="fa fa-plus-square"></i></span>
					</p>
					<div class="datadir_data" style="margin-left: 164px;">
						<ul></ul>
					</div>
					<p class="logdir_warp">
						<span class="text_fl"><font class="red_star">*</font>日志目录:</span>
						<input class="model_input text_fr logdir" name="logdir"  placeholder="请输入存储节点的日志目录"/>
						<span class="add_btn"><i class="fa fa-plus-square"></i></span>
					</p>
					<div class="logdir_data" style="margin-left: 164px;">
						<ul></ul>
					</div>
					<p class="wal_log_dir_warp">
						<span class="text_fl"><font class="red_star">*</font>wal目录:</span>
						<input class="model_input text_fr wal_log_dir" name="wal_log_dir"  placeholder="请输入存储节点wal日志目录"/>
						<span class="add_btn"><i class="fa fa-plus-square"></i></span>
					</p>
					<div class="wal_data" style="margin-left: 164px;">
						<ul></ul>
					</div>
					<p class="comp_datadir_warp">
						<span class="text_fl"><font class="red_star">*</font>计算节点数据目录:</span>
						<input class="model_input text_fr comp_datadir" name="comp_datadir"  placeholder="请输入计算节点的数据目录"/>
						<span class="add_btn"><i class="fa fa-plus-square"></i></span>
					</p>
					<div class="comp_data" style="margin-left: 164px;">
						<ul></ul>
					</div>
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

</body>
<script src="<?=base_url().'application/views/plugin/app/js/computer_list.js'?>"></script>
</html>
