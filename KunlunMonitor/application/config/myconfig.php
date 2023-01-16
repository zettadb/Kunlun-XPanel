<?php
$config['post_url'] = 'http://192.168.0.113:54002/HttpService/Emit';
$config['key'] = 'zetta@509';
$config['pg_username'] = 'abc';
$config['default_username'] = 'player';
$config['pg_database'] = 'postgres';
$config['db_prefix'] = 'kunlundb_';
$config['grafana_key'] = 'Bearer eyJrIjoidlRYZUdFVEJVbkxmUlRVYmU0dzVFOGdNMTlhbVJ4S0giLCJuIjoiYXBpa2V5Y3VybF8xMTAxIiwiaWQiOjF9';
$config['job_type'] = [
	['code' => 'create_cluster', 'name' => '新增集群'],
	['code' => 'delete_cluster', 'name' => '删除集群'],
	['code' => 'add_shards', 'name' => '新增shard'],
	['code' => 'delete_shard', 'name' => '删除shard'],
	['code' => 'backup_cluster', 'name' => '备份集群'],
	['code' => 'restore_new_cluster', 'name' => '恢复集群'],
	['code' => 'add_comps', 'name' => '增加计算节点'],
	['code' => 'delete_comp', 'name' => '删除计算节点'],
	['code' => 'add_nodes', 'name' => '增加存储节点'],
	['code' => 'delete_node', 'name' => '删除存储节点'],
	['code' => 'mysqld_exporter', 'name' => '监控存储节点'],
	['code' => 'postgres_exporter', 'name' => '监控计算节点'],
	['code' => 'update_prometheus', 'name' => '重置prometheus'],
	['code' => 'update_machine', 'name' => '编辑计算机'],
	['code' => 'delete_machine', 'name' => '删除计算机'],
	['code' => 'control_instance', 'name' => '控制实例'],
	['code' => 'delete_backup_storage', 'name' => '删除备份存储目标'],
	['code' => 'create_backup_storage', 'name' => '新增备份存储目标'],
	['code' => 'update_backup_storage', 'name' => '编辑备份存储目标'],
	['code' => 'manual_switch', 'name' => '主备切换'],
	['code' => 'rebuild_node', 'name' => '重做备机节点'],
	['code' => 'create_machine', 'name' => '新增计算机'],
	['code' => 'cluster_restore', 'name' => '回档集群'],
	['code' => 'expand_cluster', 'name' => '集群扩容'],
	['code' => 'manual_backup_cluster', 'name' => '全量备份'],
	['code' => 'table_repartition', 'name' => '表重分布'],
];
