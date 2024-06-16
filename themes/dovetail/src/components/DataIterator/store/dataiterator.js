export const state = () => ({
  dataiterator: {
    // Pagination
    itemsPerPage: 4,

    // Card
    hover: false,

    // Thumbnail
    thumbnailHeight: '160px'
  }
})

export const getters = {
  dataiterator: state => state.dataiterator
}

export const mutations = {
  PROMPT_DIALOG (state, payload) {
    payload = Object.assign(state.dataiterator, payload)
    state.dataiterator = payload
  },

  emptyState () {
    this.replaceState({ dataiterator: null })
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations
}
