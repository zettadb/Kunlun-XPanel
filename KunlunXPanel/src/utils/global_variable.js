//docker环境
//const ip = JSON.parse(sessionStorage.getItem("response")).BASE_URL.split(':')[1].slice(2);
//普通环境
//const ip=process.env.VUE_APP_INTERFACE_API.split(':')[1].slice(2);
//获取当前的ip
const ip = window.document.location.href.substring(0, window.document.location.href.indexOf(window.document.location.pathname));
const role_type_arr = [{value:'super_dba_role',label:'超级DBA'},{value:'ordinary_dba',label:'普通DBA'},{value:'ordinary_user',label:'普通用户'},{value:'role_user',label:'角色'}];
const valid_period = [{value:'permanent',label:'永久'},{value:'from_to',label:'时间段'}];
//const priv_type_arr = [{value:'user_add',label:'增加用户'},{value:'user_drop',label:'删除用户'},{value:'user_grant',label:'用户授权'},{value:'role_add',label:'添加角色'},{value:'role_drop',label:'删除角色'},{value:'role_edit',label:'编辑角色'},{value:'cluster_creata',label:'创建集群'},{value:'cluster_drop',label:'删除集群'},{value:'shard_create',label:'创建分片'},{value:'shard_drop',label:'删除分片'},{value:'storage_node_create',label:'创建存储节点'},{value:'storage_node_drop',label:'删除存储节点'},{value:'compute_node_create',label:'创建计算节点'},{value:'compute_node_drop',label:'删除计算节点'},{value:'machine_add',label:'创建计算机'},{value:'machine_drop',label:'删除计算机'}];
const priv_type_arr = ['增加用户','删除用户','用户授权','添加角色','删除角色','编辑角色','创建集群','删除集群','创建分片','删除分片','创建存储节点','删除存储节点','创建计算节点','删除计算节点','创建计算机','删除计算机','启用备份服务','禁用备份服务','启用存储节点','禁用存储节点','启用计算节点','禁用计算节点','备份','恢复','集群扩容','集群缩容'];
//const datatree_arr=[{id:'user',label:'用户权限',children:[{id:'user_add',label:'新增用户'},{id:'user_edit',label: '编辑用户'},{id: 'user_del',label: '删除用户'},{id: 'user_grant',label: '用户授权'}]}, {id: 'role',label: '角色权限',children: [{id: 'role_add',label: '新增角色'}, {id: 'role_edit',label: '编辑角色'}, {id: 'role_del',label: '删除角色'}]}, {id: 'cluster',label: '集群权限',children: [{id: 'cluster_add',label: '新增集群'}, {id: 'cluster_del',label: '删除集群'}, {id: 'cluster_backeup',label: '备份集群'}, {id: 'cluster_restore',label: '恢复集群'}, {id: 'cluster_expand',label: '集群扩容'}, {id: 'cluster_shrink',label: '集群缩容'},{id: 'backup_service_en',label: '启/禁用备份服务'}, {id: 'storage',label: '存储节点权限',children: [{id:'storage_node_create',label:'新增存储节点'},{id:'storage_node_drop',label:'删除存储节点'},{id:'storage_en',label:'启/禁用存储节点'}]},{id: 'comp',label: '计算节点权限',children: [{id:'comp_add',label:'新增计算节点'},{id:'comp_del',label:'删除计算节点'},{id:'comp_en',label:'启/禁用计算节点'}]},{id: 'shard',label: '存储shard权限',children: [{id:'shard_add',label:'新增存储shard'},{id:'shard_del',label:'删除存储shard'}]}]}, {id: 'equip',label: '计算机权限',children: [{id: 'equip_add',label: '新增计算机'}, {id: 'equip_edit',label: '编辑计算机'}, {id: 'equip_del',label: '删除计算机'}]}];
const datatree_arr=[{id: 'cluster',label: '集群权限',children: [{id: 'cluster_add',label: '新增集群'}, {id: 'cluster_del',label: '删除集群'}, {id: 'cluster_backeup',label: '备份集群'}, {id: 'cluster_restore',label: '恢复集群'}, {id: 'cluster_expand',label: '集群扩容'}, {id: 'cluster_shrink',label: '集群缩容'},{id: 'backup_service_en',label: '启/禁用备份服务'}, {id: 'storage',label: '存储节点权限',children: [{id:'storage_node_create',label:'新增存储节点'},{id:'storage_node_drop',label:'删除存储节点'},{id:'storage_en',label:'启/禁用存储节点'}]},{id: 'comp',label: '计算节点权限',children: [{id:'comp_add',label:'新增计算节点'},{id:'comp_del',label:'删除计算节点'},{id:'comp_en',label:'启/禁用计算节点'}]},{id: 'shard',label: '存储shard权限',children: [{id:'shard_add',label:'新增存储shard'},{id:'shard_del',label:'删除存储shard'}]}]}, {id: 'equip',label: '计算机权限',children: [{id: 'equip_add',label: '新增计算机'}, {id: 'equip_edit',label: '编辑计算机'}, {id: 'equip_del',label: '删除计算机'}]}];
//const ha_mode_arr=[{id:'mgr',label:'mgr'},{id:'rbr',label:'rbr'},{id:'no_rep',label:'no_rep'}];
const ha_mode_arr=[{id:'rbr',label:'rbr'}];
const c_ha_mode_arr=[{id:'mgr',label:'mgr'}];
const shards_arr=[{id:'1',label:'1'},{id:'2',label:'2'},{id:'3',label:'3'},{id:'4',label:'4'},{id:'5',label:'5'},{id:'6',label:'6'},{id:'7',label:'7'},{id:'8',label:'8'},{id:'9',label:'9'},{id:'10',label:'10'}];
const norepshards_arr=[{id:'1',label:'1'}];;
const per_shard_arr=[{id:'3',label:'3'},{id:'4',label:'4'},{id:'5',label:'5'},{id:'6',label:'6'},{id:'7',label:'7'},{id:'8',label:'8'},{id:'9',label:'9'},{id:'10',label:'10'}];
const node_type_arr=[{id:'add_shards',label:'shard'},{id:'add_comps',label:'计算节点'},{id:'add_nodes',label:'存储节点'}];
const c_node_type_arr=[{id:'add_shards',label:'shard'},{id:'add_comps',label:'计算节点'}];
const machine_type_arr=[{id:'storage',label:'储存'},{id:'computer',label:'计算'}];
const version_arr=[{ver:'1.0'}];
const storage_type_arr=[{id:'HDFS',name:'HDFS'}];
const ip_arr=[{ip:ip}];
//const ip_arr=[{ip:'http://'+ip+':3000'}];
const timestamp_arr=[{time:new Date().getTime()}];
const policy_arr=[{id:'top_hit',label:'按热点排序'},{id:'top_size',label:'按数据量排序'}];
const node_stats_arr=[{id:'running',label:'在线',color:'#00ed37'},{id:'idle',label:'不允许再装实例',color:'#c7c9d1'},{id:'dead',label:'离线',color:'red'}];
//const alarm_type_arr=[{id:'create_cluster',label:'新增集群'},{id:'delete_cluster',label:'删除集群'},{id:'add_shards',label:'新增shard'},{id:'delete_shard',label:'删除shard'},{id:'add_comps',label:'增加计算节点'},{id:'delete_comp',label:'删除计算节点'},{id:'add_nodes',label:'增加存储节点'},{id:'delete_node',label:'删除存储节点'},{id:'manual_switch',label:'主备切换'},{id:'rebuild_node',label:'重做备机节点'},{id:'cluster_restore',label:'回档集群'},{id:'expand_cluster',label:'集群扩容'},{id:'manual_backup_cluster',label:'全量备份'},{id:'manual_backup_cluster',label:'全量备份'},{id:'standby_delay',label:'主备延迟过大'},{id:'storage_node_exception',label:'存储节点异常'},{id:'comp_node_exception',label:'计算节点异常'},{id:'machine_offline',label:'设备离线'}];
const alarm_type_arr=[{id:'standby_delay',label:'主备延迟过大'},{id:'storage_node_exception',label:'存储节点异常'},{id:'comp_node_exception',label:'计算节点异常'},{id:'machine_offline',label:'设备离线'},{id:'manual_switch',label:'主备切换'},{id:'expand_cluster',label:'集群扩容'},{id:'shard_coldbackup',label:'自动备份'},{id:'manual_backup_cluster',label:'全量备份'},{id:'create_cluster',label:'新增集群'},{id:'delete_cluster',label:'删除集群'},{id:'add_shards',label:'新增shard'},{id:'delete_shard',label:'删除shard'},{id:'add_comps',label:'增加计算节点'},{id:'delete_comp',label:'删除计算节点'},{id:'add_nodes',label:'增加存储节点'},{id:'delete_node',label:'删除存储节点'}];
const alarm_level_arr=[{id:'FATAL',label:'FATAL'},{id:'ERROR',label:'ERROR'},{id:'WARNING',label:'WARNING'}];
export {
  role_type_arr,
  valid_period,
  priv_type_arr,
  datatree_arr,
  ha_mode_arr,
  c_ha_mode_arr,
  shards_arr,
  per_shard_arr,
  norepshards_arr,
  node_type_arr,
  c_node_type_arr,
  version_arr,
  ip_arr,
  storage_type_arr,
  timestamp_arr,
  machine_type_arr,
  policy_arr,
  node_stats_arr,
  alarm_type_arr,
  alarm_level_arr
}