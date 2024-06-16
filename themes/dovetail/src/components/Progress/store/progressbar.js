export const state = () => ({
  progressbar: {
    loading: true,
    progress: null,
    indeterminate: true,
    isUploading: false,
  },
})

export const getters = {
  progressbar: state => state.progressbar,
}

export const mutations = {
  'SET_PROGRESS_STATUS' (state, payload) {
    state.progressbar.isUploading = payload.isUploading
    state.progressbar.progress = payload.progress || 0
    state.progressbar.indeterminate = payload.indeterminate || false
  },

  'TOGGLE_PROGRESSBAR' (state) {
    state.progressbar.loading = !state.progressbar.loading
  },

  'HIDE_PROGRESSBAR' (state) {
    state.progressbar.loading = false
  },

  'SHOW_PROGRESSBAR' (state) {
    state.progressbar.loading = true
  },
}

export const actions = {
  setProgressStatus: ({ commit }, payload) => {
    commit('SET_PROGRESS_STATUS', payload)
  },

  toggleProgressbar: ({ commit }) => {
    commit('TOGGLE_PROGRESSBAR')
  },

  hideProgressbar: ({ commit }) => {
    commit('HIDE_PROGRESSBAR')
  },

  showProgressbar: ({ commit }) => {
    commit('SHOW_PROGRESSBAR')
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
}
