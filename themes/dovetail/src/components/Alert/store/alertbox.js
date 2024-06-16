import store from '@/store'

export const state = () => ({
  alertbox: {
    border: 'left',
    color: null,
    dense: true,
    dismissible: true,
    icon: null,
    list: [],
    outlined: true,
    prominent: true,
    show: false,
    text: null,
    type: 'success',
  }
})

export const getters = {
  alertbox: state => state.alertbox,
  show: state => state.alertbox.show,
}

export const mutations = {
  'SET' (state, payload) {
    state.alertbox = Object.assign({}, state.alertbox, payload)
  },

  'SHOW' (state, payload) {
    state.alertbox = Object.assign({}, state.alertbox, payload, { show: true })
  },

  'HIDE' (state, payload) {
    state.alertbox = Object.assign({}, state.alertbox, payload, { show: false })
  },
}

export const actions = {
  set: ({ commit }, payload) => {
    commit('SET', payload)
  },

  show: ({ commit }, payload) => {
    commit('SHOW', payload)
  },

  hide: ({ commit }, payload) => {
    store.dispatch('errorbox/hide')
    store.dispatch('successbox/hide')
    commit('HIDE', payload)
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
