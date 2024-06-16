export const state = () => ({
  header: {
    title: 'Page Title',
  }
})

export const getters = {
  header: state => state.header
}

export const mutations = {
  emptyState () {
    this.replaceState({ header: null })
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations
}
