import axios from 'axios'
// import JSONbig from 'json-bigint'
import {Message} from 'element-ui'
import store from '@/store'
import {getCookie} from '@/utils/auth'
// 引入加解密方法
// import { decrypt } from '@/utils/crypto.js'
//docker环境
// const baseURL=JSON.parse(sessionStorage.getItem("response")).BASE_URL
// baseURL:baseURL+'/KunlunMonitor/index.php'
// 普通环境
// baseURL:process.env.VUE_APP_BASE_API

// create an axios instance
let baseUrl = window.document.location.href.substring(0, window.document.location.href.indexOf(window.document.location.pathname)) + '/KunlunMonitor/index.php';
if (process.env.NODE_ENV === 'development') {
  baseUrl = process.env.VUE_APP_BASE_API

}
const service = axios.create({
  baseURL: baseUrl,
  timeout: 60000
})

const err = (error) => {//eslint-disable-line
  if (error.response) {
    let data = error.response.data;//eslint-disable-line
    const token = getCookie('zettadb_vue_token');//eslint-disable-line
    console.log('------异常响应------', token)
    console.log('------异常响应------', error.response.status)
    switch (error.response.status) {
      case 403:
        Message({message: '拒绝访问', type: 'error', duration: 4})
        break
      case 404:
        Message({message: '系统提示', type: 'error', duration: 4})
        break
      case 504:
        Message({message: '网络超时', type: 'error', duration: 4})
        break
      case 401:
        Message({message: '未授权，请重新登录', type: 'error', duration: 4})
        if (token) {
          store.dispatch('Logout').then(() => {
            setTimeout(() => {
              window.location.reload()
            }, 1500)
          })
        }
        break
      default:
        Message({message: data.message, type: 'error', duration: 4})
        break
    }
  }
  return Promise.reject(error)
}

// request(请求)拦截器
service.interceptors.request.use(
  config => {
    let aiyunland_vue_token = sessionStorage.getItem('zettadb_vue_token');//eslint-disable-line
    if (aiyunland_vue_token !== null && aiyunland_vue_token !== 'undefined' && aiyunland_vue_token !== '') {
      // 让每个请求携带自定义token
      config.headers['Token'] = sessionStorage.getItem('zettadb_vue_token')
      // config.headers['Name'] = sessionStorage.getItem('login_username')
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
    //console.log(response);
    let res = response.data;//eslint-disable-line
    // res.code += ''
    if (res.code !== undefined && res.code !== 200) {
      Message({
        'title': '错误',
        'message': res.message,
        'type': 'error',
        'dangerouslyUseHTMLString': true,
      });
    }
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
