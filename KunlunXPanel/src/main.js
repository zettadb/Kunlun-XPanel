import Vue from 'vue'

import 'normalize.css/normalize.css' // A modern alternative to CSS resets

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import locale from 'element-ui/lib/locale/lang/en' // lang i18n

import '@/styles/index.scss' // global css

import App from './App'
import store from './store'
import router from './router/index'

import '@/icons' // icon
import '@/permission' // permission control

import iView from 'iview';
import './assets/less/index.less';
//import echarts from 'echarts'
import * as echarts from 'echarts'
import img from './lib/img'
import utils from "./lib/utils";
Vue.prototype.$echarts = function (el) {
  return echarts.init(el, null, {renderer: 'svg'})
}
Vue.prototype.$images = img
Vue.config.productionTip = false;
Vue.use(iView);
Vue.use(utils)
//uuid
import { v4 as uuidv4 } from 'uuid';

/**
 * If you don't want to use mock-server
 * you want to use MockJs for mock api
 * you can execute: mockXHR()
 *
 * Currently MockJs will be used in the production environment,
 * please remove it before going online ! ! !
 */
if (process.env.NODE_ENV === 'production') {
  const { mockXHR } = require('../mock')
  mockXHR()
}

// set ElementUI lang to EN
//Vue.use(ElementUI, { locale })
// 如果想要中文版 element-ui，按如下方式声明
 Vue.use(ElementUI)

Vue.config.productionTip = false

new Vue({
  //el: '#app',
  router,
  store,
  render: h => h(App)
}).$mount('#app')
