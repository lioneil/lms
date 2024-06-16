/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap'

import Vue from 'vue'
import App from './App.vue'

import store from '@/store'

import vuetify from './plugins/vuetify'
import router from './plugins/router'
import i18n from './plugins/i18n'
import './plugins/ckeditor'
import './plugins/inputmask'
import './plugins/portal-vue'
import './plugins/scrollto'
import './plugins/shortkey'
import './plugins/slugify'
import './plugins/vee-validate'
import './plugins/vue-sortable'

import '@/mixins'

import '@/components'

import 'grapesjs/dist/css/grapes.min.css';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.config.productionTip = false;

const app = new Vue({
  i18n,
  store,
  router,
  vuetify,
  render: h => h(App),
}).$mount('#app')
