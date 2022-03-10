<?php
require 'top.php'
?>
<div class="top">
	<div class="top_wrap fr" >
		<a class="refresh" title="刷新" href="<?=base_url().'index.php/Computer/nodeList'?>"><i class="fa fa-refresh"></i></a>|
		<a class="top_text"><?php echo $_SESSION['ip']?></a>|
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
	<div class="list"><a class="create" data-target="#addCluster" data-toggle="modal"><i class="fa fa-plus"></i></a></div>-->

</div>

</body>
<script src="<?=base_url().'application/views/plugin/app/js/comp_node_list.js'?>"></script>
</html>
