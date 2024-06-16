import $api from '@/routes/api'
import isAuthenticated from '@/routes/middleware/isAuthenticated'
import metatags from '@/routes/middleware/metatags'
import multiguard from 'vue-router-multiguard'
import permissions from '@/routes/middleware/permissions'
import store from '@/store'
import tokenVerifier from '@/routes/middleware/tokenVerifier'

const routes = []
const requireRoute = require.context(
  // The relative path of the routes folder
  '@/modules',
  // Whether or not to look in subfolders
  true,
  // The regular expression used to match base route filenames
  /routes\/admin\.js$/
)

requireRoute.keys().forEach(route => {
  const routeConfig = requireRoute(route)

  routeConfig.default.forEach(route => {
    routes.push(route)
  })
})

export default {
  path: '/admin',
  name: 'admin',
  redirect: { name: 'dashboard' },
  component: () => import('@/components/Layouts/Dashboard.vue'),
  meta: { title: 'Admin', authenticatable: true },
  children: routes,
  beforeEnter: multiguard([metatags, tokenVerifier, isAuthenticated, permissions]),
}
