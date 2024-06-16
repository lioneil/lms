import merge from 'lodash/merge'

export const state = () => ({
  shortkey: {
    ctrlIsPressed: false,
    altIsPressed: false,
  },
})

export const getters = {
  shortkey: state => state.shortkey,
  ctrlIsPressed: state => state.shortkey.ctrlIsPressed,
  altIsPressed: state => state.shortkey.altIsPressed,
}

export const mutations = {
  'PRESS' (state, payload) {
    state.shortkey = merge({}, state.shortkey, payload)
  },
}

export const actions = {
  press: ({ commit }, payload) => {
    commit('PRESS', payload)
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
}
