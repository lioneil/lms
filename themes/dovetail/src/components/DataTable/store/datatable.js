export const state = () => ({
  datatable: {
    // Code
  },
})

export const getters = {
  datatable: state => state.datatable
}

export const mutations = {
  PROMPT_DIALOG (state, payload) {
    payload = Object.assign(state.datatable, payload)
    state.datatable = payload
  },

  emptyState () {
    this.replaceState({ datatable: null })
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations
}
