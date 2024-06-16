import axios from 'axios'
import $api from '@/routes/api'
import $auth from '@/core/Auth/auth'

export const state = () => ({
  status: '',
  token: localStorage.getItem('token') || '',
  user: $auth.getUser() || {},
})

export const getters = {
  status: state => state.status,
  isLoggedIn: state => !!state.token,
  isAuthenticated: state => !!state.token,
  user: state => state.user,
}

export const mutations = {
  'SET_TOKEN' (state, token) {
    state.token = token
  },

  'AUTH_REQUEST' (state) {
    state.status = 'loading'
  },

  'AUTH_SUCCESS' (state, user) {
    state.status = 'success'
    state.user = user
  },

  'AUTH_ERROR' (state) {
    state.status = 'error'
  },

  'LOGOUT' (state) {
    state.status = ''
    state.token = ''
    state.user = {}
  },
}

export const actions = {
  login: ({ commit }, user) => {
    return new Promise((resolve, reject) => {
      commit('AUTH_REQUEST')
      axios({url: $api.login, data: user, method: 'post'})
        .then((response) => {
          $auth.authorize(response.data.token, response.data.user)
          commit('AUTH_SUCCESS', response.data.user)
          commit('SET_TOKEN', response.data.token)
          resolve(response)
        })
        .catch(err => {
          commit('AUTH_ERROR')
          localStorage.removeItem('token')
          reject(err)
        })
    })
  },

  socialite: ({ commit }, data) => {
    $auth.authorize(data.token, data.user)
    commit('AUTH_SUCCESS', data.user)
    commit('SET_TOKEN', data.token)
  },

  logout: ({ commit }) => {
    return new Promise((resolve, reject) => {
      commit('AUTH_REQUEST')
      axios({url: $api.logout, data: null, method: 'post'})
        .then((response) => {
          localStorage.removeItem('user')
          localStorage.removeItem('token')
          localStorage.removeItem('report:lang')
          resolve(response)
        })
        .catch(err => {
          commit('AUTH_ERROR')
          localStorage.removeItem('user')
          localStorage.removeItem('token')
          reject(err)
        })
    })
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
