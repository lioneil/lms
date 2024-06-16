import merge from 'lodash/merge'

export const state = () => ({
  metatag: {
    title: null,
    description: null,
  },
})

export const getters = {
  metatag: state => state.metatag,
  title: state => state.metatag.title,
  description: state => state.metatag.description,
}

export const mutations = {
  'SET' (state, payload) {
    state.metatag = merge({}, state.metatag, payload)
  },
}

export const actions = {
  set: ({ commit }, payload) => {
    commit('SET', payload)
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
