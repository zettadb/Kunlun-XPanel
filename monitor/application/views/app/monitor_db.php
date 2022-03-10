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

			<a class="refresh" title="刷新" href="<?=base_url().'index.php/Main?id='.$_SESSION['cluster_id']?>"><i class="fa fa-refresh"></i></a>|
			<a class="top_text"><?php echo $_SESSION['cluster_name']?></a>|
<!--			<a class="addCluster" title="创建集群" data-target="#addCluster" data-toggle="modal"><i class="fa fa-plus-square"></i></a>|-->
<!--			<a data-target="#upload" data-toggle="modal"  class="upload" title="上传文件"><i class="fa fa-upload"></i></a>|-->
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
	</div>

<!--上传文件-->
<div class="modal fade" id="upload" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog"  style="width: 630px;">
		<div class="modal-content model_wrap">
			<div class="model_content">
				<div class="building_header">
					<p>请选择json文件</p>
					<span class="modal_close" data-dismiss="modal">X</span>
				</div>
				<div class="modal-body building add_Item">

				</div>
			</div>
			<form role="form" method="post" style="margin-left:60px;width:535px;margin-bottom: 20px;"
				  　　enctype="multipart/form-data" style="margin-bottom: 20px;margin-top: 20px;">
				<input type="file" class="ka_input fileloading" id="cardnumber" name="cardnumber" >
			</form>
			<div class="modal_footer bg_eee">
				<p class="tac pb17">
					<span class="col_37A confirm">确定</span>
					<span class="col_FFA cancle"  data-dismiss="modal">取消</span>
				</p>
			</div>
		</div>
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
	<!-- 新增节点-->
	<div class="modal fade" id="addItem" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog"  style="width: 530px;">
			<div class="modal-content model_wrap">
				<div class="model_content">
					<div class="building_header">
						<p>新增信息</p>
						<span class="modal_close" data-dismiss="modal">X</span>
					</div>
					<div class="modal-body building add_Item">
						<div class="select_wrap select_pull_down node_type_warp">
							<p>
								<span class="text_fl"><font class="red_star">*</font>节点类型：</span>
								<input type="text" class="model_input node_type ka_input3 text_fr" placeholder="请选择节点类型" name="node_type" data-ajax="" readonly="">
							</p>
							<div class="ka_drop">
								<div class="ka_drop_list node_type">
									<ul>
										<li><a href="javascript:;" data-ajax="101">计算节点</a></li>
										<li><a href="javascript:;" data-ajax="102">存储节点</a></li>
										<li><a href="javascript:;" data-ajax="103">元数据节点</a></li>
									</ul>
								</div>
							</div>
						</div>
						<div class="select_wrap select_pull_down node_shard_warp hi">
							<p>
								<span class="text_fl"><font class="red_star">*</font>shard：</span>
								<input type="text" class="model_input node_shard ka_input3 text_fr" placeholder="请选择所属shard" name="node_shard" data-ajax="" readonly="">
								<span class="add_btn"><i class="fa fa-plus-square"></i>添加shard</span>
							</p>
							<div class="ka_drop">
								<div class="ka_drop_list">
									<ul>
										<!--<li><a href="javascript:;" data-ajax="101">计算节点</a></li>
										<li><a href="javascript:;" data-ajax="102">存储节点</a></li>
										<li><a href="javascript:;" data-ajax="103">元数据节点</a></li>-->
									</ul>
								</div>
							</div>
						</div>

						<p>
							<span class="text_fl"><font class="red_star">*</font>IP：</span>
							<input class="model_input text_fr ip" name="ip"  placeholder="请输入ip"/>
						</p>
						<p>
							<span class="text_fl"><font class="red_star">*</font>port：</span>
							<input class="model_input text_fr port" name="port"  placeholder="请输入port" onblur="autoUrl();"/>
						</p>
						<p class="xport_warp hi">
							<span class="text_fl"><font class="red_star">*</font>xport：</span>
							<input class="model_input text_fr xport" name="xport"  placeholder="请输入xport"/>
						</p>
						<p class="mgr_port_warp hi">
							<span class="text_fl"><font class="red_star">*</font>mgr_port：</span>
							<input class="model_input text_fr mgr_port" name="mgr_port"  placeholder="请输入mgr_port"/>
						</p>
						<p class="user_warp">
							<span class="text_fl"><font class="red_star">*</font>账号：</span>
							<input class="model_input text_fr username" name="username"  placeholder="请输入账号"/>
						</p>
						<p class="pwd_warp">
							<span class="text_fl"><font class="red_star">*</font>密码：</span>
							<input type="password" class="model_input text_fr pwd" name="pwd"  placeholder="请输入密码"/>
						</p>

						<p class="if_primary_wrap hi">
							<span class="text_fl"><font class="red_star">*</font>主节点：</span>
							<span style="margin-left: 10px;">
								<input type="radio" id="if_primary-1" name="if_primary" class="regular-radio">
								<label for="if_receipt-1"></label>
								是
							</span>
								<span  style="margin-left: 60px;">
								<input type="radio" id="if_primary-2" name="if_primary" class="regular-radio" checked="">
								<label for="if_primary-2"></label>
								否
							</span>
						</p>
						<p>
							<span class="text_fl">数据存放目录：</span>
							<input class="model_input text_fr dataurl" name="dataurl"  placeholder="请输入数据存放目录"/>
						</p>
						<p class="logurl_warp hi">
							<span class="text_fl">日志存放目录：</span>
							<input class="model_input text_fr logurl" name="logurl"  placeholder="请输入日志存放目录"/>
						</p>
						<p class="innodburl_warp hi">
							<span class="text_fl">innodb日志存放目录：</span>
							<input class="model_input text_fr innodburl" name="innodburl"  placeholder="请输入innodb日志存放目录"/>
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
						<p>新增Shards</p>
						<span class="modal_close" data-dismiss="modal">X</span>
					</div>
					<div class="modal-body building addShards auto_model">
						<p>
							<span class="text_fl"><font class="red_star">*</font>IP：</span>
							<input class="model_input text_fr ip" name="ip"  placeholder="请输入ip"/>
						</p>
						<p>
							<span class="text_fl"><font class="red_star">*</font>port：</span>
							<input class="model_input text_fr port" name="port"  placeholder="请输入port" onblur="autoUrlShard();"/>
						</p>
						<p class="xport_warp">
							<span class="text_fl"><font class="red_star">*</font>xport：</span>
							<input class="model_input text_fr xport" name="xport"  placeholder="请输入xport"/>
						</p>
						<p class="mgr_port_warp">
							<span class="text_fl"><font class="red_star">*</font>mgr_port：</span>
							<input class="model_input text_fr mgr_port" name="mgr_port"  placeholder="请输入mgr_port"/>
						</p>
						<p class="if_primary_wrap">
							<span class="text_fl"><font class="red_star">*</font>主节点：</span>
							<span style="margin-left: 10px;">
								<input type="radio" id="if_primary-3" name="if_primary" class="regular-radio">
								<label for="if_receipt-3"></label>
								是
							</span>
							<span  style="margin-left: 60px;">
								<input type="radio" id="if_primary-4" name="if_primary" class="regular-radio" checked="">
								<label for="if_primary-4"></label>
								否
							</span>
						</p>
						<p>
							<span class="text_fl">数据存放目录：</span>
							<input class="model_input text_fr dataurl" name="dataurl"  placeholder="请输入数据存放目录"/>
						</p>
						<p class="logurl_warp">
							<span class="text_fl">日志存放目录：</span>
							<input class="model_input text_fr logurl" name="logurl"  placeholder="请输入日志存放目录"/>
						</p>
						<p class="innodburl_warp">
							<span class="text_fl">innodb日志存放目录：</span>
							<input class="model_input text_fr innodburl" name="innodburl"  placeholder="请输入innodb日志存放目录"/>
						</p>
						<p>
							<span class="add_btn text_fl"><i class="fa fa-plus-square"></i>添加节点</span>
						<div class="person_building_data" style="margin-left: 148px;">
							<ul>
<!--								 <li data-ip="张" data-port="三" data-xport="1004" data-mgr_port="101" data-primary="101" data-dataurl="101" data-logurl="101" data-innodburl="101"><p class="li_p"><span>IP:</span><span>192.168.0.126</span><span class="del_i"><i class="fa fa-close"></i></span></p><p class="li_p"><span>port:</span><span>6001</span></p><p class="li_p"><span>xport:</span><span>6002</span></p><p class="li_p"><span>mgr_port:</span><span>6003</span></p><p class="li_p"><span>主节点:</span><span>是</span></p><p class="li_p"><span>数据存放目录:</span><span>/home/kunlun/data_sn/port</span></p><p class="li_p"><span>日志存放目录:</span><span>/home/kunlun/data_sn/port</span></p><p class="li_p"><span>innodb日志存放目录:</span><span>/home/kunlun/data_sn/port</span></p></li>-->
							</ul>
						</div>
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
	<!--新增集群-->
	<div class="modal fade" id="addCluster" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog"  style="width: 530px;">
			<div class="modal-content model_wrap">
				<div class="model_content">
					<div class="building_header">
						<p>新增集群</p>
						<span class="modal_close" data-dismiss="modal">X</span>
					</div>
					<div class="modal-body building add_Item">
						<p>
							<span class="text_fl">集群名称:</span>
							<input class="model_input text_fr cluster_name" name="cluster_name"  placeholder="请输入集群名称"/>
						</p>
						<p>
							<span class="text_fl">存储分片总个数:</span>
							<input class="model_input text_fr shards_count" name="shards_count"  placeholder="请输入存储分片总个数"/>
						</p>
						<p>
							<span class="text_fl">每分片包含节点总个数:</span>
							<input class="model_input text_fr snode_count" name="snode_count"  placeholder="请输入每分片包含节点总个数"/>
						</p>
						<p>
							<span class="text_fl">所有分片的最大存储值:</span>
							<input class="model_input text_fr max_storage_size" name="max_storage_size"  placeholder="请输入所有存储分片的最大存储值"/>GB
						</p>
						<p>
							<span class="text_fl">计算节点总个数:</span>
							<input class="model_input text_fr comp_count" name="comp_count"  placeholder="请输入计算节点总个数"/>
						</p>
						<p>
							<span class="text_fl">计算节点最大连接数:</span>
							<input  class="model_input text_fr max_connections" name="max_connections"  placeholder="请输入计算节点的最大连接数"/>
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
	<input type="hidden" name="upfile" />
</body>
<script>
	$(function() {
		//初始化fileinput
		var fileInput = new FileInput();
		fileInput.Init("cardnumber", getRootPath()+'/index.php/Main/upload');
	});
</script>
<script>
	//初始化fileinput
	var FileInput = function() {
		var oFile = new Object();
		//初始化fileinput控件（第一次初始化）
		oFile.Init = function(ctrlName, uploadUrl) {
			var control = $('#' + ctrlName);
			//初始化上传控件的样式
			control.fileinput({
				language: 'zh', //设置语言
				uploadUrl: uploadUrl, //上传的地址
				allowedFileExtensions: ['json'], //接收的文件后缀
				uploadAsync: true, //默认异步上传
				showUpload: true, //是否显示上传按钮
				showRemove: false, //显示移除按钮
				showCaption: true, //是否显示标题
				dropZoneEnabled: false, //是否显示拖拽区域
				minFileCount: 1,
				maxFileCount: 1, //表示允许同时上传的最大文件个数
				uploadExtraData:function(){
					return {'code':$('.add_Item .code').html()}
				},   //传给后端的参数
				/*  maxImageWidth:'',
				  maxImageHeight:'',*/
				maxFileSize:2048,
				enctype: 'multipart/form-data',
				previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
			});
			//文件上传完成之后发生的事件
			control.on("fileuploaded", function(event, data, previewId, index) {
				//传递图片地址给后端
				response = data.response;
				name= response[1].name;
				$('input[name="upfile"]').val(name)
				picReady = true;
				$('.btn-file,.fileinput-upload-button').hide();
			});
			//图片上传成功后点击删除图片事件
			control.on("filesuccessremove", function(event, data, previewId, index) {
				picReady = false;
				$('.btn-file,.fileinput-upload-button').show();
			});
			//点击右上角关闭按钮后的事件
			control.on("filecleared", function(event, data, previewId, index) {
				picReady = false;
				$('.btn-file,.fileinput-upload-button').show();
			});
		}
		return oFile; //这里必须返回oFile对象，否则FileInput组件初始化不成功
	};</script>
<script src="<?=base_url().'application/views/plugin/app/js/monitor_db.js'?>"></script>
</html>
