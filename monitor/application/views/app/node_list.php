<?php
require 'top.php'
?>
	<div class="top" id="top">
		<div class="top_wrap fr">
			<div class="node_type_wrap select_pull_down query_wrap col_37A fl" style="display: inline-block;">
				<div>
					<input type="text" class="model_input node_type ka_input3" placeholder="节点类型" name="node_type" data-ajax="" readonly>
				</div>
				<div class="ka_drop">
					<div class="ka_drop_list">
						<ul>
							<li><a href="javascript:;" data-ajax="101">计算节点</a></li>
							<li><a href="javascript:;" data-ajax="102">存储节点</a></li>
							<li><a href="javascript:;" data-ajax="103">元数据节点</a></li>
						</ul>
					</div>
				</div>
			</div>
			<a class="refresh" title="刷新" href="<?=base_url().'index.php/Node?id='.$_SESSION['cluster_id']?>"><i class="fa fa-refresh"></i></a>|
			<a class="top_text"><?php echo $_SESSION['cluster_name']?></a>|
			<a class="big_screen" title="主机信息视图" href="http://<?= $_SERVER['SERVER_NAME'] ?>:3000/d/c8W2b01Zz/node-exporter-for-prometheus-dashboard-zhong-wen-jian-rong-ban?orgId=1"><i class="icon fa fa-tv"></i></a>|
			<a class="login_out back" title="返回"><i class="fa fa-mail-reply"></i></a>
		</div>
	</div>
	<div class="main">
			<!--<div class="list">
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
		<div class="main_n" id="main_n">
			<!--<div class="manage">存储shard列表</div>
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
		<div class="main_d" id="main_d">
			<!--<div class="manage">元数据节点列表</div>
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

	<!--计算节点信息-->
	<div class="modal fade" id="calculate" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog"  style="width: 530px;">
			<div class="modal-content model_wrap">
				<div class="model_content">
					<div class="building_header">
						<p>计算节点信息</p>
						<span class="modal_close" data-dismiss="modal">X</span>
					</div>
					<div class="modal-body building add_Item">
						<p>
							<span class="text_fl">IP：</span>
							<span class="ip text_fr"></span>
						</p>
						<p>
							<span class="text_fl">端口号：</span>
							<span class="port text_fr"></span>
						</p>
						<p>
							<span class="text_fl">账号:</span>
							<input class="model_input text_fr username" name="username"  placeholder="请输入账号"/>
						</p>
						<p>
							<span class="text_fl">密码:</span>
							<input type="password" class="model_input text_fr pwd" name="pwd"  placeholder="请输入密码"/>
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
	<!-- 新增shards-->
	<div class="modal fade" id="addShards" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog"  style="width: 530px;">
			<div class="modal-content model_wrap">
				<div class="model_content">
					<div class="building_header">
						<p>新增Shard</p>
						<span class="modal_close" data-dismiss="modal">X</span>
					</div>
					<div class="modal-body building addShards auto_model" style="height: 250px;">
						<p>
							<span class="text_fl">集群名称：</span>
							<span class="text-fr cluster_name"></span>
						</p>
						<div class="select_wrap select_pull_down shards_count_warp">
							<p>
								<span class="text_fl"><font class="red_star">*</font>分片个数:</span>
									<input type="text" class="model_input shards_count ka_input3 text_fr" placeholder="请选择分片个数" name="shards_count" data-ajax="" readonly="">
							</p>
							<div class="ka_drop">
								<div class="ka_drop_list">
									<ul>
										<li><a href="javascript:;" data-ajax="1">1</a></li>
										<li><a href="javascript:;" data-ajax="2">2</a></li>
										<li><a href="javascript:;" data-ajax="3">3</a></li>
										<li><a href="javascript:;" data-ajax="4">4</a></li>
										<li><a href="javascript:;" data-ajax="5">5</a></li>
										<li><a href="javascript:;" data-ajax="6">6</a></li>
										<li><a href="javascript:;" data-ajax="7">7</a></li>
										<li><a href="javascript:;" data-ajax="8">8</a></li>
										<li><a href="javascript:;" data-ajax="9">9</a></li>
										<li><a href="javascript:;" data-ajax="10">10</a></li>
									</ul>
								</div>
							</div>
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
<!-- 新增计算节点-->
<div class="modal fade" id="addCompItem" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog"  style="width: 530px;">
		<div class="modal-content model_wrap">
			<div class="model_content">
				<div class="building_header">
					<p>新增计算节点</p>
					<span class="modal_close" data-dismiss="modal">X</span>
				</div>
				<div class="modal-body building addShards auto_model" style="height: 250px;">
					<p>
						<span class="text_fl">集群名称：</span>
						<span class="text-fr cluster_name"></span>
					</p>
					<div class="select_wrap select_pull_down comp_count_warp">
						<p>
							<span class="text_fl"><font class="red_star">*</font>节点个数:</span>
							<input type="text" class="model_input comp_count ka_input3 text_fr" placeholder="请选择节点个数" name="comp_count" data-ajax="" readonly="">
						</p>
						<div class="ka_drop">
							<div class="ka_drop_list">
								<ul>
									<li><a href="javascript:;" data-ajax="1">1</a></li>
									<li><a href="javascript:;" data-ajax="2">2</a></li>
									<li><a href="javascript:;" data-ajax="3">3</a></li>
									<li><a href="javascript:;" data-ajax="4">4</a></li>
									<li><a href="javascript:;" data-ajax="5">5</a></li>
									<li><a href="javascript:;" data-ajax="6">6</a></li>
									<li><a href="javascript:;" data-ajax="7">7</a></li>
									<li><a href="javascript:;" data-ajax="8">8</a></li>
									<li><a href="javascript:;" data-ajax="9">9</a></li>
									<li><a href="javascript:;" data-ajax="10">10</a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal_footer bg_eee">
				<p class="tac pb17">
					<span class="col_37A confirm">保存</span>
					<span class="col_FFA cancle"  data-dismiss="modal">取消</span>
				</p>
			</div>
		</div>
	</div>
</div>
<!-- 新增元数据节点-->
<div class="modal fade" id="addMetaItem" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog"  style="width: 530px;">
		<div class="modal-content model_wrap">
			<div class="model_content">
				<div class="building_header">
					<p>新增元数据节点</p>
					<span class="modal_close" data-dismiss="modal">X</span>
				</div>
				<div class="modal-body building addShardNode auto_model">
					<p>
						<span class="text_fl">集群名称：</span>
						<span class="text-fr cluster_name"></span>
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
					<span class="col_37A confirm">保存</span>
					<span class="col_FFA cancle"  data-dismiss="modal">取消</span>
				</p>
			</div>
		</div>
	</div>
</div>
<input type="hidden" value='<?php echo $_SESSION['cluster_id'];?>' name="cluster_id" />
<input type="hidden" value='<?php echo $_SESSION['cluster_name'];?>' name="cluster_name" />
</body>

<script src="<?=base_url().'application/views/plugin/app/js/node_list.js'?>"></script>
</html>
