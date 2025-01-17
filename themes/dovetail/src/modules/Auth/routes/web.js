import store from '@/store'
import router from 'vue-router'

export default [
  {
    path: '/auth',
    name: 'auth',
    redirect: { name: 'login' },
    component: () => import('@/components/Layouts/Auth.vue'),
    meta: {
      title: 'Login',
      sort: 0,
      authenticatable: false,
      icon: 'mdi-account-key',
    },
    children: [
      {
        path: '/:lang?/login',
        name: 'login',
        component: () => import('../Signin.vue'),
        meta: {
          title: 'Sign In',
          sort: 0,
          icon: 'mdi-account-key',
        },
        beforeEnter: (to, from, next) => {
          const isAuthenticated = store.getters['auth/isAuthenticated']
          if (isAuthenticated) {
            let from = to.query.from || {name: 'dashboard'}
            return next(from)
          } else {
            // store.dispatch(
            //   'auth/logout'
            // ).then(response => {
              return next()
            // })
          }
        },
      },
      {
        path: '/logout',
        name: 'logout',
        beforeEnter: (to, from, next) => {
          store.dispatch('auth/logout')
            .then(response => {
              // next({name: 'login'})
              window.location.reload()
            })
        },
      },
    ],
  },
];
