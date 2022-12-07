import request from '@/utils/request'

export function getOperationList(query) {
    return request({
        url: '/user/Operation/getOperationList',
        method: 'get',
        params:query
    });
}
export function getOptionList(){
  return request({
    url: `/user/Operation/getHomeOperationList`,
    method: 'get',
    params:''
  })
}
export function getOptionCount(){
  return request({
    url: `/user/Operation/getOptionCount`,
    method: 'get',
    params:''
  })
}
// export function addAccount(data) {
//     return request({
//       url: '/user/User/add',
//       method: 'post',
//       data
//     })
// }

// export function update(data) {
//     return request({
//       url: '/user/User/edit',
//       method: 'post',
//       data
//     })
// }

// export function delAccount(id) {
//     return request({
//       url: '/user/User/delete',
//       method: 'post',
//       data:{id:id}
//     })
// }
