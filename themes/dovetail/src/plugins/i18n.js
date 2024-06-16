import Vue from 'vue'
import VueI18n from 'vue-i18n'
import en from '@/lang/en.json'

Vue.use(VueI18n)

export const messages = { en }

const i18n = new VueI18n({
  locale: localStorage.getItem('app:locale') || null,
  fallbackLocale: window.$app.fallback_locale,
  silentFallbackWarn: true,
  silentTranslationWarn: true,
  messages
})

window.$trans = function (text, options = null) {
  return i18n.tc(text, 1, options)
}

window.trans = function (text, options = null) {
  return i18n.tc(text, 1, options)
}

window.trans_choice = function (text, count = 1, options = null) {
  return i18n.tc(text, count, options)
}

export default i18n
