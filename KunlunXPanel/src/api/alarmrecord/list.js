import request from '@/utils/request'

export function getAlarmRecordList(query) {
    return request({
        url: '/user/AlarmRecord/getAlarmRecordList',
        method: 'get',
        params:query
    });
}
export function update(data) {
    return request({
      url: '/user/AlarmRecord/update',
      method: 'post',
      data
    })
}