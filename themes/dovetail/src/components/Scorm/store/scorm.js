import { scorm } from '@gamestdio/scorm';

export const state = () => ({
  scorm: {
    api: scorm,
  },
})

export const getters = {
  scorm: state => state.scorm,
}

export const mutations = {
  SET_CONFIGURATION (state, payload = {}) {
    state.scorm.api.configure(payload)
  }
}

export const actions = {
  configure: ({ commit }, payload = {}) => {
    commit('SET_CONFIGURATION', payload)
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
