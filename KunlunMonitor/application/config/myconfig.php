<?php 
	$config['post_url'] = 'http://192.168.0.111:54002/HttpService/Emit';
	$config['key']='zetta@509';
	$config['pg_username'] = 'abc';
	$config['default_username'] = 'player';
	$config['pg_database'] = 'postgres';
	$config['db_prefix'] = 'kunlundb_';
	$config['grafana_key'] = '';
	$config['job_type'] = array(array('code'=>'create_cluster','name'=>'新增集群'),array('code'=>'delete_cluster','name'=>'删除集群'),array('code'=>'add_shards','name'=>'新增shard'),array('code'=>'delete_shard','name'=>'删除shard'),array('code'=>'backup_cluster','name'=>'备份集群'),array('code'=>'restore_new_cluster','name'=>'恢复集群'),array('code'=>'add_comps','name'=>'增加计算节点'),array('code'=>'delete_comp','name'=>'删除计算节点'),array('code'=>'add_nodes','name'=>'增加存储节点'),array('code'=>'delete_node','name'=>'删除存储节点'),array('code'=>'mysqld_exporter','name'=>'监控存储节点'),array('code'=>'postgres_exporter','name'=>'监控计算节点'),array('code'=>'update_prometheus','name'=>'重置prometheus'),array('code'=>'update_machine','name'=>'编辑计算机'),array('code'=>'delete_machine','name'=>'删除计算机'),array('code'=>'control_instance','name'=>'控制实例'),array('code'=>'delete_backup_storage','name'=>'删除备份存储目标'),array('code'=>'create_backup_storage','name'=>'新增备份存储目标'),array('code'=>'update_backup_storage','name'=>'编辑备份存储目标'),array('code'=>'manual_switch','name'=>'主备切换'),array('code'=>'rebuild_node','name'=>'重做备机节点'),array('code'=>'create_machine','name'=>'新增计算机'),array('code'=>'cluster_restore','name'=>'回档集群'),array('code'=>'expand_cluster','name'=>'集群扩容'));
