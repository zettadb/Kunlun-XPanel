import request from '@/utils/request'

export function getRCRList(query) {
  return request({
      url: '/user/RCR/getRCRList',
      method: 'get',
      params:query
  });
}
export function getMetaMachine() {
  return request({
    url: '/user/RCR/getMetaMachine',
    method: 'get',
    params: ''
  })
}

export function getStandbyMeta(data) {
  return request({
    url: `/user/RCR/getStandbyMeta`,
    method: 'post',
    data
  })
}


export function addRCR(data) {
    return request({
      url: '/user/RCR/createRCR',
      method: 'post',
      data
    })
}

export function update(data) {
    return request({
      url: '/user/RCR/editMachine',
      method: 'post',
      data
    })
}

export function delMachine(data) {
    return request({
      url: '/user/RCR/deleteMachine',
      method: 'post',
      data
    })
}
export function createMetaTable(data) {
  return request({
      url: '/user/RCR/createMetaTable',
      method: 'post',
      data
  });
}
export function getMetaList(query) {
  return request({
    url: '/user/RCR/getMetaList',
    method: 'get',
    params: query
  })
}
export function actionRCR(data) {
  return request({
    url: '/user/RCR/deleteMachine',
    method: 'post',
    data
  })
}
export function setRCRMaxDalay(data) {
  return request({
    url: '/user/RCR/setRCRMaxDalay',
    method: 'post',
    data
  })
}
export function findName(query) {
  return request({
    url: '/user/RCR/findName',
    method: 'get',
    params: query
  })
}
export function addMetaList(data) {
  return request({
    url: '/user/RCR/addMetaList',
    method: 'post',
    data
  })
}
export function deleteMeta(data) {
  return request({
    url: '/user/RCR/deleteMeta',
    method: 'post',
    data
  })
}
export function findMetaDB(query) {
  return request({
    url: '/user/RCR/findMetaDB',
    method: 'get',
    params: query
  })
}
export function findMetaCluster(data) {
  return request({
    url: '/user/RCR/findMetaCluster',
    method: 'post',
    data
  })
}
export function getRCRRelater(query) {
  return request({
    url: '/user/RCR/getRCRRelater',
    method: 'get',
    params: query
  })
}
export function findRcrRelat(query) {
  return request({
    url: '/user/RCR/findRcrRelat',
    method: 'get',
    params: query
  })
}