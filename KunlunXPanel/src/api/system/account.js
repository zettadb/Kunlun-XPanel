import request from '@/utils/request'

export function getAccountList(query) {
    return request({
        url: '/user/User/userList',
        method: 'get',
        params:query
    });
}

export function addAccount(data) {
    return request({
      url: '/user/User/add',
      method: 'post',
      data
    })
}

export function update(data) {
    return request({
      url: '/user/User/edit',
      method: 'post',
      data
    })
}

export function delAccount(id) {
    return request({
      url: '/user/User/delete',
      method: 'post',
      data:{id:id}
    })
}