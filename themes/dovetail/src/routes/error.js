import metatags from '@/routes/middleware/metatags'
import multiguard from 'vue-router-multiguard'

export default [
  {
    path: '*',
    component: () => import('@/components/Errors/404.vue'),
    beforeEnter: multiguard([metatags]),
    meta: {
      title: '404 Not Found',
      excludeInMenu: true,
      external: true,
      excludeFromRoot: true,
      authenticatable: false,
      metatags: [{
        name: 'robots',
        content: 'noindex'
      }],
    },
  },
  {
    path: '/403',
    name: 'error.403',
    component: () => import('@/components/Errors/403.vue'),
    meta: {
      title: '403 Unauthorized',
      excludeInMenu: true,
      external: true,
      excludeFromRoot: true,
      authenticatable: false,
    },
  },
  {
    path: '/404',
    name: 'error.404',
    component: () => import('@/components/Errors/404.vue'),
    beforeEnter: multiguard([metatags]),
    meta: {
      title: '404 Not Found',
      excludeInMenu: true,
      external: true,
      excludeFromRoot: true,
      authenticatable: false,
      metatags: [{
        name: 'robots',
        content: 'noindex'
      }],
    },
  }
]
