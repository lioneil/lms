export const state = () => ({
  breadcrumbs: {
    show: true,
  }
})

export const getters = {
  breadcrumbs: state => state.breadcrumbs,
}

export const mutations = {
  'SET' (state, payload) {
    state.breadcrumbs.items = payload.items
  },

  'TOGGLE' (state, payload) {
    state.breadcrumbs.show = payload.show
  },
}

export const actions = {
  set: ({ commit }, payload) => {
    commit('SET', payload)
  },

  text: ({ commit }, payload) => {
    commit('SET_TEXT', payload)
  },

  toggle: ({ commit }, payload) => {
    commit('TOGGLE', payload)
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
