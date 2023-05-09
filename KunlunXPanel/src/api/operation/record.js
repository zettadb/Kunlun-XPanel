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
export function delTable(data) {
    return request({
      url: '/user/Operation/delTable',
      method: 'post',
      data
    })
}
