<?php
require 'top.php'
?>
<div class="top">
	<div class="top_wrap fr" >
		<a class="refresh" title="刷新" href="<?=base_url()?>"><i class="fa fa-refresh"></i></a>|
		<a class="operationlist" title="任务列表"  target="_blank"  href="<?=base_url().'index.php/Operation/operationList'?>"><i class="fa fa-tasks"></i></a>|
		<a class="backclusterlist" title="集群备份列表"  href="<?=base_url().'index.php/BackUpCluster/backeUpCluster'?>"><i class="fa fa-list-ol"></i></a>
<!--		<a class="addComputer" title="添加计算机"  href="--><?//=base_url().'index.php/Computer'?><!--"><i class="fa fa-plus-square"></i></a>-->
<!--		<a class="backupCluster" title="备份集群"  href="--><?//=base_url().'index.php/backCluster'?><!--"><i class="fa fa-window-restore"></i></a>-->
	</div>
</div>
<div class="main">
	<!--<div class="list">
		<div class="tisk">
			<div class="title">集群信息</div>
			<p class="ip_warp"><span>id：</span><span  class="ip">192.168.0.126</span></p>
			<p><span>集群名称：</span>3306<span></span></p>
			<p><span>创建时间：</span>v5.0.1版本<span></span></p>
		</div>
		<a href="javascript:;" class="option edit"><i class="icon fa fa-pencil-square-o"></i></a>
		<a href="javascript:;" class="option enble"><i class="fa fa-ban"></i></a>
		<a href="javascript:;" class="option enble"><i class="fa fa-pause-circle-o"></i></a>
		<a href="javascript:;" class="option del"><i class="fa fa-trash"></i></a>
		<a href="javascript:;" class="option btns"><i class="fa fa-arrow-right"></i></a>
	</div>
	<div class="list"></div>
	<div class="list"><a class="create" data-target="#addCluster" data-toggle="modal"><i class="fa fa-plus"></i></a></div>-->
	<div class="main_c clusters" id="main_c">
<!--		<div class="manage">集群管理</div>-->
<!--		<div class="list">-->
<!--			<div class="tisk">-->
<!--				<div class="title">集群信息</div>-->
<!--				<a class="cluster_img"></a>-->
<!--				<!--<p class="ip_warp"><span>id：</span><span  class="ip">192.168.0.126</span></p>-->-->
<!--				<p class="ip_warp">-->
<!--					<span>集群名称：</span>-->
<!--					<span>cluster_1640774320_000036</span></p>-->
<!--				<!--<p><span>创建时间：</span><span>v5.0.1版本</span></p>-->-->
<!--				<p class="message">(创建失败，请重试)</p>-->
<!--			</div>-->
<!--			<a href="javascript:;" class="option btns"><i class="fa fa-arrow-right"></i></a>-->
<!--			<a href="javascript:;" class="option edit"><i class="icon fa fa-pencil-square-o"></i></a>-->
<!--			<a href="javascript:;" class="option del"><i class="fa fa-trash"></i></a>-->
<!--		</div>-->
<!--		<div class="list"><a class="create" data-target="#addCluster" data-toggle="modal"><i class="fa fa-plus"></i></a></div>-->
<!--		<div class="more_warp more"><i class="fa fa-angle-double-down"></i></div>-->
<!--		<div class="more_warp back_more"><i class="fa fa-angle-double-up"></i></div>-->
	</div>
<!--	<div class="c_line hi"></div>-->
	<div class="main_p computers" style="margin-top: 20px;" id="main_p">
		<!--<div class="manage">计算机管理</div>
		<div class="list">
			<div class="tisk">
				<div class="title">集群信息</div>
				<p class="ip_warp"><span>id：</span><span  class="ip">192.168.0.126</span></p>
				<p><span>集群名称：</span>3306<span></span></p>
				<p><span>创建时间：</span>v5.0.1版本<span></span></p>
			</div>
			<a href="javascript:;" class="option edit"><i class="icon fa fa-pencil-square-o"></i></a>
			<a href="javascript:;" class="option enble"><i class="fa fa-ban"></i></a>
			<a href="javascript:;" class="option enble"><i class="fa fa-pause-circle-o"></i></a>
			<a href="javascript:;" class="option del"><i class="fa fa-trash"></i></a>
			<a href="javascript:;" class="option btns"><i class="fa fa-arrow-right"></i></a>
		</div>
		<div class="list"><a class="create" data-target="#addCluster" data-toggle="modal"><i class="fa fa-plus"></i></a></div>
		<div class="more_warp more"><i class="fa fa-angle-double-down"></i></div>
		<div class="more_warp back_more"><i class="fa fa-angle-double-up"></i></div>-->

	</div>
</div>
<!--新增集群-->
<div class="modal fade" id="addCluster" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog"  style="width: 600px;">
		<div class="modal-content model_wrap">
			<div class="model_content">
				<div class="building_header">
					<p>新增集群信息</p>
					<span class="modal_close" data-dismiss="modal">X</span>
				</div>
				<div class="modal-body building addCluster">
					<div class="select_wrap select_pull_down ha_mode_warp">
						<p>
							<span class="text_fl"><font class="red_star">*</font>复制模式:</span>
							<input type="text" class="model_input ha_mode ka_input3 text_fr" placeholder="请选择复制模式" name="ha_mode" data-ajax="" readonly="">
						</p>
						<div class="ka_drop">
							<div class="ka_drop_list node_type">
								<ul>
									<li><a href="javascript:;" data-ajax="101">mgr</a></li>
									<li><a href="javascript:;" data-ajax="102">no_rep</a></li>
									<!--<li><a href="javascript:;" data-ajax="103">rbr</a></li>-->
								</ul>
							</div>
						</div>
					</div>
					<div class="select_wrap select_pull_down shards_count_warp">
						<p>
							<span class="text_fl"><font class="red_star">*</font>存储分片总个数:</span>
							<input type="text" class="model_input shards_count ka_input3 text_fr" placeholder="请选择存储分片总个数" name="shards_count" data-ajax="" readonly="">
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
					<div class="select_wrap select_pull_down snode_count_warp">
						<p>
							<span class="text_fl"><font class="red_star">*</font>每分片含节点总个数:</span>
							<input type="text" class="model_input snode_count ka_input3 text_fr" placeholder="请选择每分片包含节点总个数" name="snode_count" data-ajax="" readonly="">
						</p>
						<div class="ka_drop">
							<div class="ka_drop_list">
								<ul>
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
					<p class="snode_warp hi">
						<span class="text_fl"><font class="red_star">*</font>每分片含节点总个数:</span>
						<span class="text_fr snode_count">1</span>
					</p>
					<!--<p>
						<span class="text_fl">所有分片的最大存储值:</span>
						<input class="model_input text_fr max_storage_size" name="max_storage_size"  placeholder="请输入所有存储分片的最大存储值"/>GB
					</p>-->
					<p>
						<span class="text_fl"><font class="red_star">*</font>计算节点总个数:</span>
						<input class="model_input text_fr comp_count" name="comp_count"  placeholder="请输入计算节点总个数"/>
					</p>
					<!--<p>
						<span class="text_fl"><font class="red_star">*</font>计算节点最大连接数:</span>
						<input  class="model_input text_fr max_connections" name="max_connections"  placeholder="请输入计算节点的最大连接数"/>
					</p>
					<p>
						<span class="text_fl"><font class="red_star">*</font>cpu核数:</span>
						<input  class="model_input text_fr cpu_cores" name="cpu_cores"  placeholder="请输入cpu核数"/>
					</p>-->
					<p>
						<span class="text_fl"><font class="red_star">*</font>缓冲池大小:</span>
						<input class="model_input text_fr buffer_pool" name="buffer_pool"  placeholder="请输入缓冲池大小"/>GB
					</p>
					<p class="more"><span>更多</span><span  class="more_icon"><i class="fa fa-angle-double-down"></i></span></p>
					<div class="optional hi">
						<p>
							<span class="text_fl">每计算节点最大连接数:</span>
							<input  class="model_input text_fr max_connections" name="max_connections"  placeholder="请输入计算节点的最大连接数"/>
						</p>
						<p>
							<span class="text_fl">每计算节点的cpu核数:</span>
							<input  class="model_input text_fr cpu_cores" name="cpu_cores"  placeholder="请输入cpu核数"/>
						</p>
						<p>
							<span class="text_fl">每计算节点最大的存储值:</span>
							<input class="model_input text_fr buffer_pool" name="buffer_pool"  placeholder="请输入每个计算节点最大的mem大小"/>GB
						</p>
						<p>
							<span class="text_fl">每存储节点的cpu核数:</span>
							<input  class="model_input text_fr cpu_cores" name="cpu_cores"  placeholder="请输入cpu核数"/>
						</p>
						<p>
							<span class="text_fl">每存储节点innodb缓冲池大小:</span>
							<input class="model_input text_fr buffer_pool" name="buffer_pool"  placeholder="请输入每个计算节点最大的mem大小"/>GB
						</p>
						<p>
							<span class="text_fl">每存储节点rocksdb缓冲池大小:</span>
							<input  class="model_input text_fr max_connections" name="max_connections"  placeholder="请输入计算节点的最大连接数"/>
						</p>
						<p>
							<span class="text_fl">每存储节点初始化存储大小:</span>
							<input  class="model_input text_fr max_connections" name="max_connections"  placeholder="请输入计算节点的最大连接数"/>
						</p>
						<p>
							<span class="text_fl">每存储节点最大存储值:</span>
							<input  class="model_input text_fr max_connections" name="max_connections"  placeholder="请输入计算节点的最大连接数"/>
						</p>
						<p>
							<span class="text_fl">集群名称:</span>
							<input  class="model_input text_fr max_connections" name="max_connections"  placeholder="请输入计算节点的最大连接数"/>
						</p>
						<p class="back"><span  class="more_icon"><i class="fa fa-angle-double-up"></i></span></p>
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

<!--新增计算机信息-->
<div class="modal fade" id="addComputer" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog"  style="width: 530px;">
		<div class="modal-content model_wrap">
			<div class="model_content">
				<div class="building_header">
					<p>新增计算机信息</p>
					<span class="modal_close" data-dismiss="modal">X</span>
				</div>
				<div class="modal-body building addComputer">
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
<!--修改计算机信息-->
<div class="modal fade" id="editComputer" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog"  style="width: 530px;">
		<div class="modal-content model_wrap">
			<div class="model_content">
				<div class="building_header">
					<p>新增计算机信息</p>
					<span class="modal_close" data-dismiss="modal">X</span>
				</div>
				<div class="modal-body building editComputer">
					<p>
						<span class="text_fl"><font class="red_star">*</font>IP地址:</span>
						<span class="text_fr ip"></span>
						<span class="text_fr id" style="display: none"></span>
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
<!--备份集群-->
<div class="modal fade" id="backupItem" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog"  style="width: 530px;">
		<div class="modal-content model_wrap">
			<div class="model_content">
				<div class="building_header">
					<p>备份集群信息</p>
					<span class="modal_close" data-dismiss="modal">X</span>
				</div>
				<div class="modal-body building backupItem">
					<p>
						<span class="text_fl">备份集群名称：</span>
						<input class="model_input text_fr backup_name" name="backup_name" placeholder="请输入备份集群名称"/>
						<span class="cluster_name hi"></span>
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
					<!--<input type="text" class="restore_time date form-control text_fr" name="restore_time" placeholder="请选择一个月内恢复时间">-->
					<input type="text" class="jeinput restore_time date form-control text_fr"  name="restore_time" placeholder="请选择一个月内恢复时间">
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
<!--集群回档-->
<div class="modal fade" id="retreatedItem" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog"  style="width: 530px;">
		<div class="modal-content model_wrap">
			<div class="model_content">
				<div class="building_header">
					<p>集群回档信息</p>
					<span class="modal_close" data-dismiss="modal">X</span>
				</div>
				<div class="modal-body building retreatedItem auto_model" style="height:365px;">
					<p>
						<span class="text_fl">原集群名称：</span>
						<span class="text_fr cluster_name"></span>
					</p>
					<p>
						<span class="text_fl"><font class="red_star">*</font>回档时间：</span>
						<input type="text" class="jeinput restore_time date form-control text_fr" name="restore_time" placeholder="请选择一个月内恢复时间">
					</p>
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

</body>
<script>

</script>
<script src="<?=base_url().'application/views/plugin/app/js/manage_list.js'?>"></script>
</html>
