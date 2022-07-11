import request from '@/utils/requestInterface'
//import qs from 'qs'

// export function addShards(data) {
//     return request({
//       url: '/user',
//       method: 'post',
//       data
//     })
// }
export function getAllClusterStatus(data) {
  return request({
    url: '/user/allClusterStatus',
    method: 'post',
    data
  })
}
export function getAllMachineStatus(data) {
  return request({
    url: '/user/allMachineStatus',
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
export function restoreCluster(data) {
  return request({
    url: '/user/Cluster/restoreCluster',
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
export function getMetaMode(data) {
  return request({
    url: '/user/Cluster/getMetaMode',
    method: 'post',
    data
  })
} 
export function getBackUpStorage(data) {
  return request({
    url: '/user/Cluster/getBackUpStorage',
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

export function getMetaPrimary(data) {
  return request({
    url: '/Meta/getMetaPrimary',
    method: 'POST',
    data
  })
}
export function getClusterDetail(data) {
  return request({
    url: '/Meta/getClusterDetail',
    method: 'post',
    data
  })
}
export function switchShard(data){
  return request({
    url:'/Meta/switchShard',
    method:'post',
    data
  })
}
export function rebuildNode(data){
  return request({
    url:'/Meta/rebuildNode',
    method:'post',
    data
  })
}