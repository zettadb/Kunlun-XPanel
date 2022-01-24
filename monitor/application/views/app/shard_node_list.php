<?php
require 'top.php'
?>
	<div class="top" id="top">
		<div class="top_wrap fr">

			<a class="refresh" title="刷新" href="<?=base_url().'index.php/Node/shardNode?id='.$_SESSION['shard_id']?>"><i class="fa fa-refresh"></i></a>|
			<a class="top_text"><?php echo $_SESSION['cluster_name']?>(<?php echo $_SESSION['shard_name']?>)</a>|
			<a class="login_out back" title="返回"><i class="fa fa-mail-reply"></i></a>
		</div>
	</div>
	<div class="main">
		<div class="main_c" id="main_c">
			<!--<div class="manage">计算节点列表</div>
			<div class="list">
				<div class="tisk">
					<div class="title">计算节点</div>
					<p class="ip_warp"><span>IP：</span><span  class="ip">192.168.0.126</span></p>
					<p><span>端口：</span>3306<span></span></p>
					<p><span>版本信息：</span>v5.0.1版本<span></span></p>
				</div>
				<a href="javascript:;" class="option edit"><i class="icon fa fa-pencil-square-o"></i></a>
				<a href="javascript:;" class="option enble"><i class="fa fa-ban"></i></a>
				<a href="javascript:;" class="option enble"><i class="fa fa-pause-circle-o"></i></a>
				<a href="javascript:;" class="option del"><i class="fa fa-trash"></i></a>
				<a href="javascript:;" class="option btns"><i class="fa fa-arrow-right"></i></a>
			</div>
			<div class="list"><a class="create"><i class="fa fa-plus"></i></a></div>-->
		</div>
	</div>

	<!--删除节点-->
	<div class="modal fade" id="delItem" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog"  style="width: 530px;">
			<div class="modal-content model_wrap">
				<div class="model_content">
					<div class="building_header">
						<p>新增节点信息</p>
						<span class="modal_close" data-dismiss="modal">X</span>
					</div>
					<div class="modal-body building del_Item">
						<p>
							<span class="text_fl">确定要删除此节点么？</span>
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

<!-- 新增存储节点-->
<div class="modal fade" id="addShardNode" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog"  style="width: 530px;">
		<div class="modal-content model_wrap">
			<div class="model_content">
				<div class="building_header">
					<p>新增存储节点</p>
					<span class="modal_close" data-dismiss="modal">X</span>
				</div>
				<div class="modal-body building addShardNode auto_model">
					<p>
						<span class="text_fl">集群名称：</span>
						<span class="text-fr cluster_name"></span>
					</p>
					<p>
						<span class="text_fl">存储分片名称：</span>
						<span class="text-fr shard_name"></span>
					</p>
					<p>
						<span class="text_fl">端口：</span>
						<input class="model_input text_fr port" name="port"  placeholder="请输入port"/>
					</p>
					<p>
						<span class="text_fl"><font class="red_star">*</font>缓冲池大小：</span>
						<input class="model_input text_fr buffer_pool_size" name="buffer_pool_size"  placeholder="请输入缓冲池大小"/>
					</p>
					<p>
						<span class="text_fl"><font class="red_star">*</font>cpu核数：</span>
						<input class="model_input text_fr cpu_cores" name="cpu_cores"  placeholder="请输入cpu核数"/>
					</p>
					<p>
						<span class="text_fl"><font class="red_star">*</font>初始化的存储值：</span>
						<input class="model_input text_fr  init_storage_size" name="init_storage_size"  placeholder="请输入初始化的存储值"/>MB
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
<input type="hidden" value='<?php echo $_SESSION['shard_id'];?>' name="shard_id" />
<input type="hidden" value='<?php echo $_SESSION['shard_name'];?>' name="shard_name" />
<input type="hidden" value='<?php echo $_SESSION['cluster_id'];?>' name="cluster_id" />
<input type="hidden" value='<?php echo $_SESSION['cluster_name'];?>' name="cluster_name" />
</body>

<script src="<?=base_url().'application/views/plugin/app/js/shard_node_list.js'?>"></script>
</html>
