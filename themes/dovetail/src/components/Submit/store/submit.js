export const state = () => ({
  dataset: {
    class: 'primary',
    label: 'Test',
    color: 'primary'
  }
})

export const getters = {
  submit: state => state.submit
}

export const mutations = {
  PROMPT_DIALOG (state, payload) {
    payload = Object.assign(state.submit, payload)
    state.submit = payload
  },

  emptyState () {
    this.replaceState({ submit: null })
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations
}
