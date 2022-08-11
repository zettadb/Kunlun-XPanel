import request from '@/utils/request'

export function getMetaClusterList(query) {
    return request({
        url: '/user/MetaCluster/getMetaClusterList',
        method: 'get',
        params:query
    });
}
export function getMetaPrimary() {
  return request({
    url: `/user/MetaCluster/getMetaPrimary`,
    method: 'get',
    param:''
  })
}
export function getMetaStandbyNode() {
  return request({
    url: `/user/MetaCluster/getMetaStandbyNode`,
    method: 'get',
    param:''
  })
}