import clone from 'lodash/clone'

export const initial = {
  border: 'top',
  color: null,
  dense: true,
  dismissible: true,
  icon: null,
  outlined: true,
  prominent: true,
  show: false,
  text: 'Item successfully updated.',
  type: 'success',
  buttons: {
    show: { to: null, text: 'Show Details', icon: 'mdi-pencil' },
    create: { to: null, text: 'Create New Item', icon: 'mdi-file-plus-outline' },
    utility: false
  },
}

export const state = () => ({
  successbox: clone(initial),
})

export const getters = {
  successbox: state => state.successbox,
  show: state => state.successbox.show,
}

export const mutations = {
  'SET' (state, payload) {
    state.successbox = Object.assign({}, state.successbox, payload)
  },

  'SHOW' (state, payload) {
    state.successbox = Object.assign({}, state.successbox, payload, { show: true })
  },

  'HIDE' (state, payload) {
    state.successbox = Object.assign({}, state.successbox, payload, { show: false })
  },

  'RESET_STATE' (state) {
    state.successbox = clone(initial)
  },
}

export const actions = {
  set: ({ commit }, payload) => {
    commit('RESET_STATE')
    commit('SET', payload)
  },

  show: ({ commit }, payload) => {
    commit('RESET_STATE')
    commit('SHOW', payload)
  },

  hide: ({ commit }, payload) => {
    commit('RESET_STATE')
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
