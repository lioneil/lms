import Vue from 'vue'
import Vuetify, { VFileInput, VRadio, VRadioGroup } from 'vuetify/lib'
import * as theme from '../../theme.json'
import store from '@/store'

Vue.use(Vuetify, {
  component: { VFileInput, VRadio, VRadioGroup }
})

export default new Vuetify({
  rtl: localStorage.getItem('app:rtl') == 'true' || false,
  icons: {
    iconfont: 'mdi',
  },
  theme: {
    // dark: store.getters['theme/dark'],
    options: {
      minifyTheme: function (css) {
        return process.env.NODE_ENV === 'production'
          ? css.replace(/[\r\n|\r|\n]/g, '')
          : css
      },
    },
    themes: {
      light: theme.colors.light,
      dark: theme.colors.dark,
    },
  },
})
