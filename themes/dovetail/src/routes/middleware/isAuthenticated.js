import store from '@/store'

export default function isAuthenticated(to, from, next) {
  const isAuthenticated = store.getters['auth/isAuthenticated']

  if (isAuthenticated) {
    return next()
  }

  return next({name: 'login', query: { from: window.location.pathname }})
}
