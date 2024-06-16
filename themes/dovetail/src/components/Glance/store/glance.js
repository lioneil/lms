export const state = () => ({
  glance: {
    title: 'Glance',
    count: '0',
    icon: 'mdi-account-outline'
  }
})

export const getters = {
  glance: state => state.glance
}

export const mutations = {
  emptyState () {
    this.replaceState({ glance: null })
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations
}
