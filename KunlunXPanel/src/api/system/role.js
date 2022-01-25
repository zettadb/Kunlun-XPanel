import request from '@/utils/request'

export function rolelist(query) {
    return request({
        url: '/user/Role/roleList',
        method: 'get',
        params:query
    });
}

export function addRole(data) {
    return request({
      url: '/user/Role/add',
      method: 'post',
      data
    })
}

export function update(data) {
    return request({
      url: '/user/Role/edit',
      method: 'post',
      data
    })
}

export function delRole(id) {
    return request({
      url: `/user/Role/delete`,
      method: 'post',
      data:{id:id}
    })
}

// export function getRoutes() {
//   return request({
//     url: `/user/role/queryTreeList`,
//     method: 'get',
//     param:''
//   })
// }

export function getAllRole() {
  return request({
      url: '/user/Role/queryall',
      method: 'get',
      params:''
  });
}

// export function getUserPermissionByToken() {
//   return request({
//     url: `/user/permission/getUserPermissionByToken`,
//     method: 'get',
//     param:''
//   })
// }

// export function queryRolePermission(roleId) {
//   return request({
//     url: `/user/permission/queryRolePermission?roleId=${roleId}`,
//     method: 'get',
//     param:''
//   })
// }

// export function saveRolePermission(data) {
//   return request({
//     url: '/user/permission/saveRolePermission',
//     method: 'post',
//     data
//   })
// }