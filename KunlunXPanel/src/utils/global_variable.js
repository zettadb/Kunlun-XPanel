const role_type_arr = [{value:'super_dba_role',label:'超级DBA'},{value:'ordinary_dba',label:'普通DBA'},{value:'ordinary_user',label:'普通用户'},{value:'role_user',label:'角色'}];
const valid_period = [{value:'permanent',label:'永久'},{value:'from_to',label:'时间段'}];
//const priv_type_arr = [{value:'user_add',label:'增加用户'},{value:'user_drop',label:'删除用户'},{value:'user_grant',label:'用户授权'},{value:'role_add',label:'添加角色'},{value:'role_drop',label:'删除角色'},{value:'role_edit',label:'编辑角色'},{value:'cluster_creata',label:'创建集群'},{value:'cluster_drop',label:'删除集群'},{value:'shard_create',label:'创建分片'},{value:'shard_drop',label:'删除分片'},{value:'storage_node_create',label:'创建存储节点'},{value:'storage_node_drop',label:'删除存储节点'},{value:'compute_node_create',label:'创建计算节点'},{value:'compute_node_drop',label:'删除计算节点'},{value:'machine_add',label:'创建计算机'},{value:'machine_drop',label:'删除计算机'}];
const priv_type_arr = ['增加用户','删除用户','用户授权','添加角色','删除角色','编辑角色','创建集群','删除集群','创建分片','删除分片','创建存储节点','删除存储节点','创建计算节点','删除计算节点','创建计算机','删除计算机','启用备份服务','禁用备份服务','启用存储节点','禁用存储节点','启用计算节点','禁用计算节点','备份','恢复','集群扩容','集群缩容'];


export {
  role_type_arr,
  valid_period,
  priv_type_arr,
}