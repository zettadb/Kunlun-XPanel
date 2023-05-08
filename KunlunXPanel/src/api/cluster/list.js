import request from '@/utils/request'

export function getClusterList(query) {
  return request({
    url: '/user/Cluster/clusterList',
    method: 'get',
    params: query
  })
}

export function getEffectComp(data) {
  return request({
    url: '/user/Cluster/getEffectComp',
    method: 'post',
    data
  })
}

export function getExperience(data) {
  return request({
    url: '/user/Cluster/getExperience',
    method: 'post',
    data
  })
}

export function createCluster(data) {
  return request({
    url: '/user/Cluster/createCluster',
    method: 'post',
    data
  })
}

export function delCluster(data) {
  return request({
    url: '/user/Cluster/delCluster',
    method: 'post',
    data
  })
}

export function backeUpCluster(data) {
  return request({
    url: '/user/Cluster/backUpCluster',
    method: 'post',
    data
  })
}

export function getBackUpList(query) {
  return request({
    url: '/user/Cluster/getBackUpList',
    method: 'get',
    params: query
  })
}

export function ifBackUp(id) {
  return request({
    url: '/user/Cluster/ifBackUp',
    method: 'post',
    data: { id: id }
  })
}

export function restoreCluster(data) {
  return request({
    url: '/user/Cluster/restoreCluster',
    method: 'post',
    data
  })
}

export function getAllMachine() {
  return request({
    url: '/user/Cluster/getAllMachine',
    method: 'get',
    params: ''
  })
}

export function getShards(id) {
  return request({
    url: '/user/Cluster/getShards',
    method: 'post',
    data: { id: id }
  })
}

export function addShards(data) {
  return request({
    url: '/user/Cluster/addShards',
    method: 'post',
    data
  })
}

export function addComps(data) {
  return request({
    url: '/user/Cluster/addComps',
    method: 'post',
    data
  })
}

export function addNodes(data) {
  return request({
    url: '/user/Cluster/addNodes',
    method: 'post',
    data
  })
}

export function getEvStatus(data) {
  return request({
    url: '/user/Cluster/getStatus',
    method: 'post',
    data
  })
}

export function getClusterNodesList(data) {
  return request({
    url: '/user/Cluster/getClusterNodesList',
    method: 'post',
    data
  })
}

export function pgEnable(data) {
  return request({
    url: '/user/Cluster/postgresEnable',
    method: 'post',
    data
  })
}

export function pEnable(data) {
  return request({
    url: '/user/Cluster/prometheusEnable',
    method: 'post',
    data
  })
}

export function myEnable(data) {
  return request({
    url: '/user/Cluster/mysqlEnable',
    method: 'post',
    data
  })
}

export function delShard(data) {
  return request({
    url: '/user/Cluster/delShard',
    method: 'post',
    data
  })
}

export function delComp(data) {
  return request({
    url: '/user/Cluster/delComp',
    method: 'post',
    data
  })
}

export function delSnode(data) {
  return request({
    url: '/user/Cluster/delSnode',
    method: 'post',
    data
  })
}

export function startComp(data) {
  return request({
    url: '/user/Cluster/controlInstance',
    method: 'post',
    data
  })
}

export function startSnode(data) {
  return request({
    url: '/user/Cluster/controlInstance',
    method: 'post',
    data
  })
}

export function stopComp(data) {
  return request({
    url: '/user/Cluster/controlInstance',
    method: 'post',
    data
  })
}

export function stopSnode(data) {
  return request({
    url: '/user/Cluster/controlInstance',
    method: 'post',
    data
  })
}

export function restartComp(data) {
  return request({
    url: '/user/Cluster/controlInstance',
    method: 'post',
    data
  })
}

export function restartSnode(data) {
  return request({
    url: '/user/Cluster/controlInstance',
    method: 'post',
    data
  })
}

export function getEffectCluster(data) {
  return request({
    url: `/user/Cluster/getEffectCluster`,
    method: 'post',
    data
  })
}

export function uAssign(data) {
  return request({
    url: `/user/Cluster/updateAssign`,
    method: 'post',
    data
  })
}

export function dAssign(data) {
  return request({
    url: `/user/Cluster/delAssign`,
    method: 'post',
    data
  })
}

export function getAllClusterStatus(data) {
  return request({
    url: '/user/Cluster/clusterStatus',
    method: 'post',
    data
  })
}

export function getAllMachineStatus(data) {
  return request({
    url: '/user/Cluster/machineStatus',
    method: 'post',
    data
  })
}

export function getNodes(data) {
  return request({
    url: '/user/Cluster/getNode',
    method: 'post',
    data
  })
}

export function getSnodeTotal(data) {
  return request({
    url: '/user/Cluster/getShardCount',
    method: 'post',
    data
  })
}

export function getStandbyNode(data) {
  return request({
    url: `/user/Cluster/getStandbyNode`,
    method: 'post',
    data
  })
}

export function getClusterShards(id) {
  return request({
    url: '/user/Cluster/getClusterShards',
    method: 'post',
    data: { id: id }
  })
}

export function getSwitcheOverList(query) {
  return request({
    url: '/user/Cluster/getSwitcheOverList',
    method: 'get',
    params: query
  })
}

export function getTaskList(query) {
  return request({
    url: '/user/Cluster/getTaskList',
    method: 'get',
    params: query
  })
}

export function getShardPrimary(data) {
  return request({
    url: `/user/Cluster/getShardPrimary`,
    method: 'post',
    data
  })
}

export function getStroMachine() {
  return request({
    url: '/user/Cluster/getStroMachine',
    method: 'get',
    params: ''
  })
}

export function getCompMachine() {
  return request({
    url: '/user/Cluster/getCompMachine',
    method: 'get',
    params: ''
  })
}

export function getBackUpStorage(data) {
  return request({
    url: '/user/Cluster/getBackUpStorage',
    method: 'post',
    data
  })
}

export function getClusterDetail(data) {
  return request({
    url: '/user/Cluster/getClusterDetail',
    method: 'post',
    data
  })
}

export function switchShard(data) {
  return request({
    url: '/user/Cluster/switchShard',
    method: 'post',
    data
  })
}

export function SetCpuCgroup(data) {
  return request({
    url: '/user/Cluster/SetCpuCgroup',
    method: 'post',
    data
  })
}

export function getStorageList(data) {
  return request({
    url: '/user/Cluster/getStorageList',
    method: 'post',
    data
  })
}

export function addStorage(data) {
  return request({
    url: '/user/Cluster/addStorage',
    method: 'post',
    data
  })
}

export function updateStorage(data) {
  return request({
    url: '/user/Cluster/updateStorage',
    method: 'post',
    data
  })
}

export function delStorage(data) {
  return request({
    url: '/user/Cluster/delStorage',
    method: 'post',
    data
  })
}

export function rebuildNode(data) {
  return request({
    url: '/user/Cluster/rebuildNode',
    method: 'post',
    data
  })
}

export function getMetaPrimary(data) {
  return request({
    url: '/user/Cluster/getMetaPrimary',
    method: 'post',
    data
  })
}

export function getShardsJobLog(data) {
  return request({
    url: '/user/Cluster/getShardsJobLog',
    method: 'post',
    data
  })
}

export function getShardsCount(query) {
  return request({
    url: '/user/Cluster/getShardsCount',
    method: 'get',
    params: query
  })
}

export function getCompsCount(query) {
  return request({
    url: '/user/Cluster/getCompsCount',
    method: 'get',
    params: query
  })
}

export function getNodesCount(query) {
  return request({
    url: '/user/Cluster/getNodesCount',
    method: 'get',
    params: query
  })
}

export function getOldCluster(query) {
  return request({
    url: '/user/Cluster/getOldCluster',
    method: 'get',
    params: query
  })
}

export function getBackupStorageList() {
  return request({
    url: '/user/Cluster/getBackupStorageList',
    method: 'get',
    params: ''
  })
}

export function clusterListError(query) {
  return request({
    url: '/user/Cluster/clusterListError',
    method: 'get',
    params: query
  })
}

export function getShardsName(query) {
  return request({
    url: '/user/Cluster/getShardsName',
    method: 'get',
    params: query
  })
}

export function getCompDBName(query) {
  return request({
    url: '/user/Cluster/getCompDBName',
    method: 'get',
    params: query
  })
}

export function getCompDBTable(query) {
  return request({
    url: '/user/Cluster/getCompDBTable',
    method: 'get',
    params: query
  })
}

export function getShardTable(query) {
  return request({
    url: '/user/Cluster/getShardTable',
    method: 'get',
    params: query
  })
}

export function getOtherShards(query) {
  return request({
    url: '/user/Cluster/getOtherShards',
    method: 'get',
    params: query
  })
}

export function expandCluster(data) {
  return request({
    url: '/user/Cluster/expandCluster',
    method: 'post',
    data
  })
}

export function getExpandTableList(data) {
  return request({
    url: '/user/Cluster/getExpandTableList',
    method: 'post',
    data
  })
}

export function setMaxDalay(data) {
  return request({
    url: '/user/Cluster/setMaxDalay',
    method: 'post',
    data
  })
}

export function getMaxDalay(data) {
  return request({
    url: '/user/Cluster/getMaxDalay',
    method: 'post',
    data
  })
}

export function getMetaCluster(query) {
  return request({
    url: '/user/Cluster/getMetaCluster',
    method: 'get',
    params: query
  })
}

export function getBackStorageList(query) {
  return request({
    url: '/user/Cluster/getBackStorageList',
    method: 'get',
    params: query
  })
}

export function setVariable(data) {
  return request({
    url: '/user/Cluster/setVariable',
    method: 'post',
    data
  })
}

export function getVariable(data) {
  return request({
    url: '/user/Cluster/getVariable',
    method: 'post',
    data
  })
}

export function getWorkMode(data) {
  return request({
    url: '/user/Cluster/getWorkMode',
    method: 'post',
    data
  })
}

export function getOneBackUpList(query) {
  return request({
    url: '/user/Cluster/getOneBackUpList',
    method: 'get',
    params: query
  })
}

export function computeList(query) {
  return request({
    url: '/user/Cluster/computeList',
    method: 'get',
    params: query
  })
}

export function shardList(query) {
  return request({
    url: '/user/Cluster/shardList',
    method: 'get',
    params: query
  })
}

export function getNoSwitchList(data) {
  return request({
    url: '/user/Cluster/getNoSwitchList',
    method: 'post',
    data
  })
}

export function setNoSwitch(data) {
  return request({
    url: '/user/Cluster/setNoSwitch',
    method: 'post',
    data
  })
}

export function delSwitch(data) {
  return request({
    url: '/user/Cluster/delSwitch',
    method: 'post',
    data
  })
}

export function controlInstance(data) {
  return request({
    url: '/user/Cluster/controlInstance',
    method: 'post',
    data
  })
}

export function getThisShardNodes(data) {
  return request({
    url: `/user/Cluster/getThisShardNodes`,
    method: 'post',
    data
  })
}

export function getClusterMonitor(query) {
  return request({
    url: '/user/Cluster/getClusterMonitor',
    method: 'get',
    params: query
  })
}

// 表重分布
export function tableRepartition(data) {
  return request({
    url: '/user/ClusterSetting/tableRepartition',
    method: 'post',
    data
  })
}

// getPGTableList 获取PG数据表列表
export function getPGTableList(data) {
  return request({
    url: '/user/ClusterSetting/getPGTableList',
    method: 'get',
    params: data
  })
}

// clusterOptions 获取集群选项列表
export function clusterOptions(data) {
  return request({
    url: '/user/ClusterSetting/clusterOptions',
    method: 'get',
    params: data
  })
}
export function getLogicalBackUpList(query) {
  return request({
    url: '/user/Cluster/getLogicalBackUpList',
    method: 'get',
    params: query
  })
}
