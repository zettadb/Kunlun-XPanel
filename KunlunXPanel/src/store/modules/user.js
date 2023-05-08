import { login as login2 ,getInfo} from '@/api/login/user'
import { getCookie, removeCookie } from '@/utils/auth'

const getDefaultState = () => {
  return {
    token: getCookie('zettadb_vue_token'),
    name: '',
    avatar: '',
  }
}

const state = getDefaultState()

const mutations = {
  RESET_STATE: (state) => {
    Object.assign(state, getDefaultState())
  },
  SET_TOKEN: (state, token) => {
    state.token = token
  },
  SET_NAME: (state, name) => {
    state.name = name
  },
  SET_VNAME: (state, villagename) => {
    state.villagename = villagename
  },
  SET_VID: (state, villageid) => {
    state.villageid = villageid
  },
  SET_AVATAR: (state, avatar) => {
    state.avatar = avatar
  }
}

const actions = {
  // user login
  login({ commit }, userInfo) {
    return new Promise((resolve, reject) => {
      login2(userInfo).then(response => {
        resolve(response);
      }).catch(error => {
        reject(error)
      })
    })
  },
  // get user info
  getInfo({ commit, state }) {
    return new Promise((resolve, reject) => {
      // getInfo(state.token).then(response => {
      //   const { data } =response
      //   //console.log(data);
      //   if (!data) {
      //     reject('Verification failed, please Login again.')
      //   }

      //   const { roles, name, avatar, introduction } = data

      //   // roles must be a non-empty array
      //   if (!roles || roles.length <= 0) {
      //     reject('getInfo: roles must be a non-null array!')
      //   }

      //   commit('SET_ROLES', roles)
      //   commit('SET_NAME', name)
      //   commit('SET_AVATAR', avatar)
      //   commit('SET_INTRODUCTION', introduction)
      //   resolve(data)
      // }).catch(error => {
      //   reject(error)
      // })
     })
  },
  // remove token
  resetToken({ commit }) {
    return new Promise(resolve => {
      removeCookie('zettadb_vue_token') // must remove  token  first
      removeCookie('zettadb_vue_name')
      commit('RESET_STATE')
      resolve()
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}

