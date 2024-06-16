import clone from 'lodash/clone'
import merge from 'lodash/merge'
import store from '@/store'

export const initials = {
  // Toggle
  show: false,
  loading: false,

  // Settings
  persistent: false,
  width: 420,
  maxWidth: 800,
  color: null,
  light: null,
  dark: null,

  // Illustration
  illustration: null,
  illustrationWidth: 300,
  illustrationHeight: 300,

  // Text
  title: false,
  text: false,

  // Alignment
  alignment: false, // e.g. 'center'

  // Buttons
  buttons: {
    action: {
      show: true,
      color: 'primary',
      text: 'Okay',
      callback: () => {
        store.dispatch('dialog/close')
      },
    },

    cancel: {
      show: true,
      color: 'dark',
      text: 'Cancel',
      callback: () => {
        store.dispatch('dialog/close')
      },
    },
  },
}

export const state = () => ({
  dialog: clone(initials)
})

export const getters = {
  dialog: state => state.dialog
}

export const mutations = {
  PROMPT_DIALOG (state, payload) {
    state.dialog = merge({}, state.dialog, payload, {loading: false})
  },

  CLOSE_DIALOG (state) {
    state.dialog.show = false
    state.dialog.loading = false
  },

  RESET_INITIAL_STATE (state) {
    state.dialog = initials
  },

  SET_LOADING (state, loading) {
    state.dialog.loading = loading
  },

  PROMPT_ERROR (state, payload) {
    state.dialog = merge({}, state.dialog, {
      illustration: () => import('@/components/Icons/ErrorIcon.vue'),
      loading: false,
      show: true,
      buttons: {
        cancel: { show: false },
        action: {
          text: 'Okay',
          color: null,
          callback: () => {
            store.dispatch('dialog/error', {show: false})
          },
        }
      }
    }, payload)
  },
}

export const actions = {
  prompt: (context, payload) => {
    context.commit('RESET_INITIAL_STATE')
    context.commit('PROMPT_DIALOG', payload)
  },

  error: (context, payload) => {
    context.commit('RESET_INITIAL_STATE')
    context.commit('PROMPT_ERROR', payload)
  },

  open: (context, payload) => {
    context.commit('RESET_INITIAL_STATE')
    context.commit('PROMPT_DIALOG', Object.assign(payload, { show: true }))
  },

  show: (context, payload) => {
    context.commit('RESET_INITIAL_STATE')
    context.commit('PROMPT_DIALOG', Object.assign(payload, { show: true }))
  },

  hide: (context) => {
    context.commit('CLOSE_DIALOG')
  },

  close: (context) => {
    context.commit('CLOSE_DIALOG')
  },

  loading: (context, loading) => {
    context.commit('SET_LOADING', loading)
  },

  reset: (context) => {
    context.commit('RESET_INITIAL_STATE')
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
}
