import axios from 'axios'
// import JSONbig from 'json-bigint'
import { Message } from 'element-ui'
import store from '@/store'
import { getCookie } from '@/utils/auth'
//引入加解密方法 
// import { decrypt } from '@/utils/crypto.js'

// create an axios instance
const service = axios.create({
  // baseURL: '/api', // 代理服务器,本地运行可以跨域访问
  baseURL: process.env.VUE_APP_BASE_API, //正常情况下用这个
  timeout: 60000, // request timeout
  //解决长整型转换后丢失精度的问题
  // transformResponse: [function (data) {
  //   try {
  //     // 如果转换成功则返回转换的数据结果
  //     let data_arr = JSONbig.parse(data)
  //     return data_arr
  //   } catch (err) {
  //     // 如果转换失败，则包装为统一数据格式并返回
  //     return {
  //       data
  //     }
  //   }
  // }],
})

const err = (error) => {
  if (error.response) {
    let data = error.response.data
    const token = getCookie('zettadb_vue_token');
    console.log("------异常响应------",token)
    console.log("------异常响应------",error.response.status)
    switch (error.response.status) {
      case 403:
        Message({ message:'拒绝访问', type: 'error', duration: 4 })
        break
      case 404:
        Message({ message: '系统提示',  type: 'error',duration: 4})
        break
      case 504:
        Message({ message:'网络超时', type: 'error', duration: 4 })
        break
      case 401:
        Message({ message:'未授权，请重新登录', type: 'error', duration: 4 })
        if (token) {
          store.dispatch('Logout').then(() => {
            setTimeout(() => {
              window.location.reload()
            }, 1500)
          })
        }
        break
      default:
        Message({ message:data.message, type: 'error', duration: 4 })
        break
    }
  }
  return Promise.reject(error)
};

// request(请求)拦截器
service.interceptors.request.use(
  config => {
    // console.log("请求拦截中的:cCode",sessionStorage.getItem('cCode'));
    // console.log("请求拦截中的:token",sessionStorage.getItem('aiyunland_vue_token'));
    let aiyunland_vue_token = sessionStorage.getItem('zettadb_vue_token');
    if(aiyunland_vue_token !== null && aiyunland_vue_token !== "undefined" && aiyunland_vue_token !== ''){
      // 让每个请求携带自定义token
      config.headers['accessToken'] = sessionStorage.getItem('zettadb_vue_token')
    }
    // let aiyunland_cCode = sessionStorage.getItem('cCode');
    // if(aiyunland_cCode !== null && aiyunland_cCode !== "undefined" && aiyunland_cCode !== ''){
    //   // 让每个请求携带自定义token
    //   config.headers['customerCode'] = sessionStorage.getItem('cCode')
    // }
    // config.headers['X-Ca-Stage'] = 'TEST'
    return config
  },
  error => {
    console.log(error) // for debug
    return Promise.reject(error)
  }
)

// response(响应)拦截器
service.interceptors.response.use(
  response => {
    // console.log(response);
    let res = response.data
    res.code += '';
    return res
  },
  error => {
    Message({
      message: error.message,
      type: 'error',
      duration: 5 * 1000
    })
    return Promise.reject(error)
  }
)

export default service
