import request from '@/utils/request'

export function login(data) {
 return request({
    url: `/Login/userCheck`,
    method: 'post',
    data
  });
}

// export function getRandomImage(currdatetime) {
//  return request({
//     url: `/login/web/randomImage/${currdatetime}`,
//     // url: `/login/timesweb/random/${currdatetime}`,
//     method: 'get',
//     params:''
//   });
// }

export function changePassword(data) {
 return request({
    url: '/Login/changePassword',
    method: 'post',
    data
  });
}

export function getUserPermissionByToken() {
  return request({
    url: '/user/web/permission/getUserPermissionByToken',
    method: 'get',
    params: ''
  })
}

export function getRoleVillageList() {
  return request({
    url: '/user/web/queryRoleVillageList',
    method: 'get',
    params: ''
  })
}

//使用auth登录方式
export function loginByAuth(data) {
  return request({
     url: '/login/timesweb/auth',
     method: 'post',
     data
   });
 }

//通过customerCode获取到项目信息
export function getCustomer() {
  return request({
    url: `/baseservice/customer/getCustomer?customerCode=${sessionStorage.getItem('cCode')}`,
    method: 'get',
    params: ''
  })
}

//测试用接口
// export function test() {
//   return request({
//      url: '/health/check',
//      method: 'get',
//      params: ''
//    });
//  }