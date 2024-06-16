export const state = () => ({
  uploadavatar: {

  },
})

export const getters = {
  uploadavatar: state => state.uploadavatar
}

export const mutations = {
  emptyState () {
    this.replaceState({ uploadavatar: null })
  },

  'UPDATE' (state, payload) {
    state.uploadavatar = Object.assign(state.uploadavatar, payload)
  },
}

export const actions = {
  update: ({ commit }, payload) => {
    commit('UPDATE', payload)
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
}
