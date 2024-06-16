export const state = () => ({
  navbar: {
    model: true,
    items: [],
  }
})

export const getters = {
  navbar: state => state.navbar,
  show: state => state.navbar.model,
}

export const mutations = {
  'SET' (state, payload) {
    state.navbar.items = payload.items
  },

  'TOGGLE' (state, payload) {
    state.navbar.model = payload
  },
}

export const actions = {
  set: ({ commit }, payload) => {
    commit('SET', payload)
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
