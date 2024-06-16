export default {
  list () {
    return '/api/v1/widgets'
  },

  refresh () {
    return '/api/v1/refresh'
  },

  show: function (id = null) {
    return `/api/v1/widgets/${id}`
  },

  store () {
    return '/api/v1/widgets'
  },

  role: {
    list: function () {
      return '/api/v1/users/roles'
    },
  }
}
