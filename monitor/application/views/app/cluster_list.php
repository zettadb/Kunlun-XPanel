<?php
require 'top.php'
?>
<div class="top">
	<div class="top_wrap fr" >
		<a class="refresh" title="刷新" href="<?=base_url().'index.php/Cluster/clusterList'?>"><i class="fa fa-refresh"></i></a>|
<!--		<a class="addComputer" title="添加计算机"  href="--><?//=base_url().'index.php/Computer'?><!--"><i class="fa fa-plus-square"></i></a>|-->
<!--		<a class="backupCluster" title="备份集群"  href="--><?//=base_url().'index.php/backCluster'?><!--"><i class="fa fa-window-restore"></i></a>|-->
		<a class="login_out back" title="返回"><i class="fa fa-mail-reply"></i></a>
	</div>
</div>
<div class="main main_c">
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
</div>
<!--新增集群-->
<div class="modal fade" id="addCluster" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog"  style="width: 530px;">
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
						<input class="model_input text_fr buffer_pool" name="buffer_pool"  placeholder="请输入缓冲池大小"/>MB
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
<!--<div class="modal fade" id="restoreItem" tabindex="-1" role="dialog" aria-hidden="true">-->
<!--	<div class="modal-dialog"  style="width: 530px;">-->
<!--		<div class="modal-content model_wrap">-->
<!--			<div class="model_content">-->
<!--				<div class="building_header">-->
<!--					<p>恢复集群信息</p>-->
<!--					<span class="modal_close" data-dismiss="modal">X</span>-->
<!--				</div>-->
<!--				<div class="modal-body building restore_Item auto_model" style="height: 350px;">-->
<!--					<!--<div class="select_wrap select_pull_down backup_warp">-->
<!--						<p>-->
<!--							<span class="text_fl"><font class="red_star">*</font>备份集群名称：</span>-->
<!--							<input type="text" class="model_input backup ka_input3 text_fr" placeholder="请选择备份集群名称" name="backup" data-ajax="" readonly="">-->
<!--						</p>-->
<!--						<div class="ka_drop">-->
<!--							<div class="ka_drop_list backup">-->
<!--								<ul>-->
<!--								</ul>-->
<!--							</div>-->
<!--						</div>-->
<!--					</div>-->-->
<!--					<p>-->
<!--						<span class="text_fl">原集群名称：</span>-->
<!--						<span class="text_fr cluster_name"></span>-->
<!--					</p>-->
<!--					<p>-->
<!--						<span class="text_fl"><font class="red_star">*</font>恢复时间：</span>-->
<!--						<input type="text" class="jeinput restore_time date form-control text_fr" name="restore_time" placeholder="请选择一个月内恢复时间">-->
<!--					</p>-->
<!--					<div class="select_wrap select_pull_down restore_warp">-->
<!--						<p>-->
<!--							<span class="text_fl"><font class="red_star">*</font>恢复集群名称：</span>-->
<!--							<input type="text" class="model_input restore ka_input3 text_fr" placeholder="请选择恢复集群名称" name="restore" data-ajax="" readonly="">-->
<!--						</p>-->
<!--						<div class="ka_drop">-->
<!--							<div class="ka_drop_list">-->
<!--								<ul>-->
<!--								</ul>-->
<!--							</div>-->
<!--						</div>-->
<!--					</div>-->
<!--					<p>-->
<!--						<span class="text_fl">最早备份时间：</span>-->
<!--						<span class="text_fr backup_time"></span>-->
<!--						<span class="cluster_id hi"></span>-->
<!--					</p>-->
<!---->
<!--				</div>-->
<!--			</div>-->
<!--			<div class="modal_footer bg_eee">-->
<!--				<p class="tac pb17">-->
<!--					<span class="col_37A confirm">确定</span>-->
<!--					<span class="col_FFA cancle"  data-dismiss="modal">取消</span>-->
<!--				</p>-->
<!--			</div>-->
<!--		</div>-->
<!--	</div>-->
<!--</div>-->
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
<script src="<?=base_url().'application/views/plugin/app/js/cluster_list.js'?>"></script>
</html>
