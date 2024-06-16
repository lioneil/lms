import merge from 'lodash/merge'

export const state = () => ({
  grouprepeater: {
    template: {
      group: '',
      group_arabic: '',
      type: '',
      children: [{
        title: '',
        code: '',
        total: '',
        wts: '',
        categories: [],
      }],
    },
    items: [],
  },
})

export const getters = {
  items: state => state.grouprepeater.items,
  template: state => state.grouprepeater.template,
}

export const mutations = {
  'ADD' (state) {
    (state.grouprepeater.items || []).push(state.grouprepeater.template)
  },

  'REMOVE' (state, payload) {
    state.grouprepeater.items.splice(payload, 1)
  },

  'SET' (state, payload) {
    state.grouprepeater.items = merge([], payload)
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
