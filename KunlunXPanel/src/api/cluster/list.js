import request from '@/utils/request'

export function getClusterList(query) {
    return request({
        url: '/user/Cluster/clusterList',
        method: 'get',
        params:query
    });
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
// export function createCluster(data) {
//     return request({
//       url: '/user/Cluster/createCluster',
//       method: 'post',
//       data
//     })
// }

// export function delCluster(data) {
//     return request({
//       url: '/user/Cluster/delCluster',
//       method: 'post',
//       data
//     })
// }
// export function backeUpCluster(data) {
//   return request({
//     url: '/user/Cluster/backUpCluster',
//     method: 'post',
//     data
//   })
// }
export function getBackUpList(query) {
  return request({
    url: '/user/Cluster/getBackUpList',
    method: 'get',
    params:query
  })
}
export function ifBackUp(id) {
  return request({
    url: '/user/Cluster/ifBackUp',
    method: 'post',
    data:{id:id}
  })
}
// export function restoreCluster(data) {
//   return request({
//     url: '/user/Cluster/restoreCluster',
//     method: 'post',
//     data
//   })
// }
export function getAllMachine() {
  return request({
    url: '/user/Cluster/getAllMachine',
    method: 'get',
    params:''
  })
}
export function getShards(id) {
  return request({
    url: '/user/Cluster/getShards',
    method: 'post',
    data:{id:id}
  })

}
// export function addShards(data) {
//   return request({
//     url: '/user/Cluster/addShards',
//     method: 'post',
//     data
//   })
// } 
// export function addComps(data) {
//   return request({
//     url: '/user/Cluster/addComps',
//     method: 'post',
//     data
//   })
// } 

// export function addNodes(data) {
//   return request({
//     url: '/user/Cluster/addNodes',
//     method: 'post',
//     data
//   })
// } 
// export function getEvStatus(uuid) {
//   return request({
//     url: '/user/Cluster/getStatus',
//     method: 'post',
//     data:{uuid:uuid}
//   })
// } 
export function getClusterNodesList(id) {
  return request({
    url: '/user/Cluster/getClusterNodesList',
    method: 'post',
    data:{id:id}
  })
}
// export function pgEnable(data) {
//   return request({
//     url: '/user/Cluster/postgresEnable',
//     method: 'post',
//     data
//   })
// } 
export function pEnable(data) {
  return request({
    url: '/user/Cluster/prometheusEnable',
    method: 'post',
    data
  })
} 
// export function myEnable(data) {
//   return request({
//     url: '/user/Cluster/mysqlEnable',
//     method: 'post',
//     data
//   })
// } 
// export function delShard(data) {
//   return request({
//     url: '/user/Cluster/delShard',
//     method: 'post',
//     data
//   })
// } 
// export function delComp(data) {
//   return request({
//     url: '/user/Cluster/delComp',
//     method: 'post',
//     data
//   })
// } 
// export function delSnode(data) {
//   return request({
//     url: '/user/Cluster/delSnode',
//     method: 'post',
//     data
//   })
// } 
// export function startComp(data) {
//   return request({
//     url: '/user/Cluster/controlInstance',
//     method: 'post',
//     data
//   })
// } 
// export function startSnode(data) {
//   return request({
//     url: '/user/Cluster/controlInstance',
//     method: 'post',
//     data
//   })
// } 
// export function stopComp(data) {
//   return request({
//     url: '/user/Cluster/controlInstance',
//     method: 'post',
//     data
//   })
// } 
// export function stopSnode(data) {
//   return request({
//     url: '/user/Cluster/controlInstance',
//     method: 'post',
//     data
//   })
// } 
// export function restartComp(data) {
//   return request({
//     url: '/user/Cluster/controlInstance',
//     method: 'post',
//     data
//   })
// } 
// export function restartSnode(data) {
//   return request({
//     url: '/user/Cluster/controlInstance',
//     method: 'post',
//     data
//   })
// } 

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
  });
}
export function getAllMachineStatus(data) {
  return request({
    url: '/user/Cluster/machineStatus',
    method: 'post',
    data
  });
}
export function getNodes(data) {
  return request({
    url: '/user/Cluster/getNode',
    method: 'post',
    data
  });
}
export function getSnodeTotal(data) {
  return request({
    url: '/user/Cluster/getShardCount',
    method: 'post',
    data
  });
}
export function getStandbyNode(data) {
  return request({
    url: `/user/Cluster/getStandbyNode`,
    method: 'post',
    data
  })
}