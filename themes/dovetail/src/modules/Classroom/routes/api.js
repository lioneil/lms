export default {
  list: function () {
    return '/api/v1/classrooms'
  },

  store: function () {
    return '/api/v1/classrooms'
  },

  delete: function (id = null) {
    return `/api/v1/classrooms/delete/${id}`
  },

  restore: function (id = null) {
    return `/api/v1/classrooms/restore/${id}`
  },

  trashed: function () {
    return '/api/v1/classrooms/trashed'
  },

  show: function (id = null) {
    return `/api/v1/classrooms/${id}`
  },

  update: function (id = null) {
    return `/api/v1/classrooms/${id}`
  },

  destroy: function (id = null) {
    return `/api/v1/classrooms/${id}`
  },

  attach: function (id = null) {
    return `/api/v1/classrooms/${id}/attach`
  },

  detach: function (id = null) {
    return `/api/v1/classrooms/${id}/detach`
  },

  courses: {
    list: function () {
      return '/api/v1/courses'
    },
  }
}
