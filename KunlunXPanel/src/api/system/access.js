import request from '@/utils/request'

export function accesslist(query) {
    return request({
        url: '/user/Access/accessList',
        method: 'get',
        params:query
    });
}

export function addAccess(data) {
    return request({
      url: '/user/Access/add',
      method: 'post',
      data
    })
}

export function update(data) {
    return request({
      url: '/user/Access/edit',
      method: 'post',
      data
    })
}

export function delAccess(user_id,role_id) {
    return request({
      url: `/user/Access/delete`,
      method: 'post',
      data:{user_id:user_id,role_id:role_id}
    })
}

export function getAllCluster() {
  return request({
    url: `/user/Access/getCluster`,
    method: 'get',
    param:''
  })
}
// export function getAllRole() {
//   return request({
//       url: '/user/Role/queryall',
//       method: 'get',
//       params:''
//   });
// }

// export function getAllUser() {
//   return request({
//       url: '/user/Role/getAllUser',
//       method: 'get',
//       params:''
//   });
// }

