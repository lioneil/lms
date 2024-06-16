export default {
  list: function () {
    return '/api/v1/announcements'
  },

  store: function () {
    return '/api/v1/announcements'
  },

  delete: function (id = null) {
    return `/api/v1/announcements/delete/${id}`
  },

  restore: function (id = null) {
    return `/api/v1/announcements/restore/${id}`
  },

  trashed: function () {
    return '/api/v1/announcements/trashed'
  },

  show: function (id = null) {
    return `/api/v1/announcements/${id}`
  },

  update: function (id = null) {
    return `/api/v1/announcements/${id}`
  },

  upload: function (id = null) {
    return `/api/v1/files/upload`
  },

  destroy: function (id = null) {
    return `/api/v1/announcements/${id}`
  },

  category: {
    list: function () {
      return '/api/v1/announcements/categories'
    },

    show: function (id = null) {
      return `/api/v1/announcements/categories/${id}`
    },

    store: function () {
      return '/api/v1/announcements/categories'
    },

    delete: function (id = null) {
      return `/api/v1/announcements/categories/delete/${id}`
    },

    restore: function (id = null) {
      return `/api/v1/announcements/categories/restore/${id}`
    },

    trashed: function () {
      return '/api/v1/announcements/categories/trashed'
    },

    update: function (id = null) {
      return `/api/v1/announcements/categories/${id}`
    },

    destroy: function (id = null) {
      return `/api/v1/announcements/categories/${id}`
    },
  },
}
