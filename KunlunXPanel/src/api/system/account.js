import request from '@/utils/request'

export function getAccountList(query) {
  return request({
    url: '/user/User/userList',
    method: 'get',
    params: query
  })
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
    data: { id: id }
  })
}

export function findMobile(mobile) {
  return request({
    url: '/user/User/checkMobile',
    method: 'post',
    data: { phone_number: mobile }
  })
}

export function findUserName(username) {
  return request({
    url: '/user/User/checkUserName',
    method: 'post',
    data: { username: username }
  })
}
