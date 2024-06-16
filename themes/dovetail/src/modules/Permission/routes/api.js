export default {
  list: function () {
    return '/api/v1/users/permissions'
  },

  refresh: function () {
    return '/api/v1/users/permissions/refresh'
  },

  reset: function () {
    return '/api/v1/users/permissions/reset'
  },
}
