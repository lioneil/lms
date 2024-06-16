export default {
  list: function () {
    return '/api/v1/users/roles'
  },

  store: function () {
    return '/api/v1/users/roles'
  },

  delete: function (id = null) {
    return `/api/v1/users/roles/delete/${id}`
  },

  restore: function (id = null) {
    return `/api/v1/users/roles/restore/${id}`
  },

  trashed: function () {
    return '/api/v1/users/roles/trashed'
  },

  show: function (id = null) {
    return `/api/v1/users/roles/${id}`
  },

  update: function (id = null) {
    return `/api/v1/users/roles/${id}`
  },

  destroy: function (id = null) {
    return `/api/v1/users/roles/${id}`
  },

  permissions: {
    list: function () {
      return '/api/v1/users/permissions'
    },
  }
}
