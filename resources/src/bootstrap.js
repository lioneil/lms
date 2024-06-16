/*eslint-disable*/
import Vue from 'vue'
import Vuetify from 'vuetify'

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
  Vue.use(Vuetify, {
    iconfont: 'mdi'
  })
  Vue.config.productionTip = false
  window.Vue = require('vue')
} catch (e) {}
