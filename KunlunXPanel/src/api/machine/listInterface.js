import request from '@/utils/requestInterface'

// export function getMachineList(query) {
//     return request({
//         url: '/user/Machine/getMachineList',
//         method: 'get',
//         params:query
//     });
// }

export function addMachine(data) {
    return request({
      url: '/user/Machine/createMachine',
      method: 'post',
      data
    })
}

export function update(data) {
    return request({
      url: '/user/Machine/editMachine',
      method: 'post',
      data
    })
}

export function delMachine(data) {
    return request({
      url: '/user/Machine/deleteMachine',
      method: 'post',
      data
    })
}
export function getEvStatus(data) {
  return request({
    url: '/user/Machine/getStatus',
    method: 'post',
    data
  })
} 
export function pEnable(data) {
  return request({
    url: '/user/Machine/prometheusEnable',
    method: 'post',
    data
  })
} 
// export function getMachineNodesList(ip) {
//   return request({
//     url: '/user/Machine/getMachineNodesList',
//     method: 'post',
//     data:{ip:ip}
//   })
// }