export default {
  list: function () {
    return '/api/v1/pages'
  },

  store: function () {
    return '/api/v1/pages'
  },

  delete: function (id = null) {
    return `/api/v1/pages/delete/${id}`
  },

  restore: function (id = null) {
    return `/api/v1/pages/restore/${id}`
  },

  trashed: function () {
    return '/api/v1/pages/trashed'
  },

  draft: function (id) {
    return `/api/v1/pages/${id}/draft`
  },

  publish: function (id) {
    return `/api/v1/pages/${id}/publish`
  },

  show: function (id = null) {
    return `/api/v1/pages/${id}`
  },

  unpublish: function (id) {
    return `/api/v1/pages/${id}/unpublish`
  },

  update: function (id = null) {
    return `/api/v1/pages/${id}`
  },

  upload: function (id = null) {
    return `/api/v1/pages/upload`
  },

  destroy: function (id = null) {
    return `/api/v1/pages/${id}`
  },

  category: {
    list: function () {
      return '/api/v1/pages/categories'
    },

    show: function (id = null) {
      return `/api/v1/pages/categories/${id}`
    },

    store: function () {
      return '/api/v1/pages/categories'
    },

    delete: function (id = null) {
      return `/api/v1/pages/categories/delete/${id}`
    },

    restore: function (id = null) {
      return `/api/v1/pages/categories/restore/${id}`
    },

    trashed: function () {
      return '/api/v1/pages/categories/trashed'
    },

    update: function (id = null) {
      return `/api/v1/pages/categories/${id}`
    },

    destroy: function (id = null) {
      return `/api/v1/pages/categories/${id}`
    },
  },
}
