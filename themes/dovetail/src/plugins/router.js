import Vue from 'vue'
import Router from 'vue-router'
import routes from '@/routes'
import store from '@/store'
import metatags from '@/routes/helpers/metatags'

Vue.use(Router);

const router = new Router({
  mode: 'history',
  base: process.env.BASE_URL || '/',
  routes,
});

/**
 * Modify data before routes render into page.
 *
 * @param to, from, next
 * @return void
 */
router.beforeEach((to, from, next) => {
  /**
   * This goes through the matched routes from last to first,
   * finding the closest route with a title.
   * eg. if we have /some/deep/nested/route
   * and /some, /deep, and /nested have titles, nested's will be chosen.
   */
  metatags.set(to, from, next)

  next({ params: { lang: store.getters['app/locale'], from: window.location.pathname } })
});

export default router;
