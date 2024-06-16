import merge from 'lodash/merge'
import store from '@/store'

export const state = () => ({
  exportfile: {
    // Toggle
    show: false,
    loading: false,

    color: 'primary',
    title: 'Select format to download',
    text: 'Export data to a specific file type.',

    // Illustration
    iconwidth: 120,
    iconheight: 120,

    // Buttons
    buttons: {
      action: {
        show: true,
        color: 'primary',
        text: 'Export',
        callback: () => {
          store.dispatch('exportfile/close')
        },
      },

      cancel: {
        show: true,
        color: 'dark',
        text: 'Cancel',
        callback: () => {
          store.dispatch('exportfile/close')
        },
      },
    },
  }
})

export const getters = {
  exportfile: state => state.exportfile
}

export const mutations = {
  PROMPT_EXPORT (state, payload) {
    state.exportfile = merge({}, state.exportfile, payload, {loading: false})
  },
}

export const actions = {
  prompt: (context, payload) => {
    context.commit('PROMPT_EXPORT', payload)
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
