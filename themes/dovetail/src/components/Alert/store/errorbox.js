import clone from 'lodash/clone'

export const initial = {
  border: 'top',
  color: null,
  dense: true,
  dismissible: true,
  icon: null,
  errors: [],
  outlined: true,
  prominent: true,
  show: false,
  text: 'An error occured.',
  type: 'error',
}

export const state = () => ({
  errorbox: clone(initial),
})

export const getters = {
  errorbox: state => state.errorbox,
  show: state => state.errorbox.show,
}

export const mutations = {
  'SET' (state, payload) {
    state.errorbox = Object.assign({}, state.errorbox, payload)
  },

  'SHOW' (state, payload) {
    state.errorbox = Object.assign({}, state.errorbox, payload, { show: true })
  },

  'HIDE' (state, payload) {
    state.errorbox = Object.assign({}, state.errorbox, payload, { show: false })
  },

  'RESET_STATE' (state) {
    state.errorbox = clone(initial)
  },
}

export const actions = {
  set: ({ commit }, payload) => {
    commit('RESET_STATE')
    commit('SET', payload)
  },

  show: ({ commit }, payload) => {
    commit('RESET_STATE')
    commit('SHOW', Object.assign({}, payload, { errors: Object.values(payload.errors).flat() }))
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
