import merge from 'lodash/merge'

export const state = () => ({
  repeater: {
    template: {key: '', value: ''},
    items: [],
  },
})

export const getters = {
  items: state => state.repeater.items,
  template: state => state.repeater.template,
}

export const mutations = {
  'ADD' (state) {
    (state.repeater.items || []).push(state.repeater.template)
  },

  'REMOVE' (state, payload) {
    state.repeater.items.splice(payload, 1)
  },

  'SET' (state, payload) {
    state.repeater.items = merge([], payload)
  }
}

export const actions = {
  add: ({ commit }) => {
    commit('ADD')
  },

  remove: ({ commit }, payload) => {
    commit('REMOVE', payload)
  },

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
