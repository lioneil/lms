export const state = () => ({
  toolbar: {
    model: '',
    title: '',
    tooltipName: '',
    filter: false,
    sort: false,
    listGridView: true,
    toggleview: true,
    showBulk: false,
    verticaldiv: true,
  },
})

export const getters = {
  toolbar: state => state.toolbar
}

export const mutations = {
  emptyState () {
    this.replaceState({ toolbar: null })
  },

  'UPDATE' (state, payload) {
    state.toolbar = Object.assign(state.toolbar, payload)
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
