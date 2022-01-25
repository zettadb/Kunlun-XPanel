import Cookies from 'js-cookie'

// const TokenKey = 'aiyunland_vue_token'

// const NameKey = ' aiyunland_vue_name'

// const VillageNameKey = ' aiyunland_vue_villagename'

// const VillageIdNameKey = ' aiyunland_vue_villageid'

export function getCookie(key) {
  return Cookies.get(key)
}

export function setCookie(key,value) {
  return Cookies.set(key, value)
}

export function removeCookie(key) {
  return Cookies.remove(key)
}