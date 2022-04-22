import axios from 'axios'
// import JSONbig from 'json-bigint'
import { Message } from 'element-ui'
import store from '@/store'
import { getCookie } from '@/utils/auth'
//引入加解密方法 
// import { decrypt } from '@/utils/crypto.js'
// const baseURL = JSON.parse(sessionStorage.getItem("response")).API_URL
//process.env.VUE_APP_INTERFACE_API
// create an axios instance
const service = axios.create({
  baseURL: process.env.VUE_APP_INTERFACE_API, //正常情况下用这个
  timeout: 60000, // request timeout
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
    let aiyunland_vue_token = sessionStorage.getItem('zettadb_vue_token');
    if(aiyunland_vue_token !== null && aiyunland_vue_token !== "undefined" && aiyunland_vue_token !== ''){
      // 让每个请求携带自定义token
      config.headers['accessToken'] = sessionStorage.getItem('zettadb_vue_token')
    }
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
