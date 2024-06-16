export const state = () => ({
  playerbar: {
    model: true,
    items: [],
  }
})

export const getters = {
  playerbar: state => state.playerbar,
  show: state => state.playerbar.model,
}

export const mutations = {
  'SET' (state, payload) {
    state.playerbar.items = payload.items
  },

  'TOGGLE' (state, payload) {
    state.playerbar.model = payload
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
