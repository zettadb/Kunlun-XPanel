import request from '@/utils/request'

// access
export function getPremisson() {
  return request({
    url: `/user/Access/getPremisson`,
    method: 'get',
    params: ''
  })
}

// user
export function getUserPremisson() {
  return request({
    url: `/user/Access/getUserPremisson`,
    method: 'get',
    params: ''
  })
}

// user
export function getRolePremisson() {
  return request({
    url: `/user/Access/getRolePremisson`,
    method: 'get',
    params: ''
  })
}
