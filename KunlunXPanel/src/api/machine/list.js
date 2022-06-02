import request from '@/utils/request'

export function getMachineList(query) {
    return request({
        url: '/user/Machine/getMachineList',
        method: 'get',
        params:query
    });
}

// export function addMachine(data) {
//     return request({
//       url: '/user/Machine/createMachine',
//       method: 'post',
//       data
//     })
// }

// export function update(data) {
//     return request({
//       url: '/user/Machine/editMachine',
//       method: 'post',
//       data
//     })
// }

// export function delMachine(data) {
//     return request({
//       url: '/user/Machine/deleteMachine',
//       method: 'post',
//       data
//     })
// }
export function getMachineNodesList(ip) {
  return request({
    url: '/user/Machine/getMachineNodesList',
    method: 'post',
    data:{ip:ip}
  })
}
export function getNodes(ip) {
  return request({
    url: '/user/Machine/getNodeCount',
    method: 'post',
    data:{ip:ip}
  })
}
export function getNodeList(data){
  return request({
    url: `/user/Machine/getNodeList`,
    method: 'post',
    data
  })
}
export function getUsedList(data){
  return request({
    url: `/user/Machine/getUsedList`,
    method: 'post',
    data
  })
}