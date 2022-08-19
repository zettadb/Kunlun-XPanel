import request from '@/utils/request'

export function mysqlDashboard(data) {
  return request({
      url: '/user/Grafana/mysqlDashboard',
      method: 'post',
      data
  });
}
export function pgsqlDashboard(data) {
  return request({
      url: '/user/Grafana/pgsqlDashboard',
      method: 'post',
      data
  });
}
export function nodeDashboard(data) {
  return request({
      url: '/user/Grafana/nodeDashboard',
      method: 'post',
      data:data
  });
}