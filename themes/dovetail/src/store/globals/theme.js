import app from '@/config/app'

export const state = () => ({
  theme: {
    dark: app.theme['theme:dark'] == 1 || false,
  },
})

export const getters = {
  isDark: state => state.theme.dark,
  dark: state => state.theme.dark,
}

export const mutations = {
  'TOGGLE_THEME' (state, payload) {
    state.theme.dark = payload.dark
    state.theme.isDark = payload.dark
  },
}

export const actions = {
  toggle: ({ commit }, payload) => {
    let data = {
      'theme:dark': payload.dark || false
    }
    axios.post('/api/v1/settings', data)
    .then(response => {
      payload.vm.theme.dark = payload.dark || false
      commit('TOGGLE_THEME', payload)
    })
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
}
