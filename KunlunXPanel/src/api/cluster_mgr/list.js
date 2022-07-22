import request from '@/utils/request'

export function getClusterMgrList() {
  return request({
      url: '/user/ClusterMgr/getClusterMgrList',
      method: 'get',
      params:''
  });
}
export function raftClusterMgr(data) {
  return request({
      url: '/user/ClusterMgr/raftClusterMgr',
      method: 'post',
      data
  });
}