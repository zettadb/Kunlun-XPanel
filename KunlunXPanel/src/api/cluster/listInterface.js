import request from '@/utils/requestInterface'
//import qs from 'qs'

export function addShards(data) {
    return request({
      url: '/web',
      method: 'post',
      data
    })
}
