import request from '@/utils/request'

export function getAlarmRecordList(query) {
  return request({
    url: '/user/AlarmRecord/getAlarmRecordList',
    method: 'get',
    params: query
  });
}
export function update(data) {
  return request({
    url: '/user/AlarmRecord/update',
    method: 'post',
    data
  })
}

export function updateAlarmSet(data) {
  return request({
    url: '/user/AlarmRecord/updateAlarmSet',
    method: 'post',
    data
  })
}

export function DeleteAlarmItem(data) {
  return request({
    url: '/user/AlarmRecord/deleteAlarmItem',
    method: 'post',
    data
  })
}


export function updateAlarmConfig(data) {
  return request({
    url: '/user/AlarmRecord/updateAlarmConfig',
    method: 'post',
    data
  })
}


export function getAlarmConfig(query) {
  return request({
    url: '/user/AlarmRecord/getAlarmConfig',
    method: 'get',
    params: query
  });
}

export function getAlarmSetList(query) {
  return request({
    url: '/user/AlarmRecord/getAlarmSetList',
    method: 'get',
    params: query
  });
}