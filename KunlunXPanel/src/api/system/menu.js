import request from '@/utils/request'

export function permissionlist() {
  return request({
    url: '/user/web/permission/list',
    method: 'get',
    params: ''
  })
}

export function addMenu(data) {
  return request({
    url: '/user/web/permission/add',
    method: 'post',
    data
  })
}

export function updateMenu(data) {
  return request({
    url: '/user/web/permission/edit',
    method: 'post',
    data
  })
}

export function delMenu(id) {
  return request({
    url: `/user/web/permission/delete?id=${id}`,
    method: 'delete',
    param: ''
  })
}
