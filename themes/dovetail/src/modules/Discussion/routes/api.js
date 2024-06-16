export default {
  list: function () {
    return '/api/v1/threads'
  },

  store: function () {
    return '/api/v1/threads'
  },

  delete: function (id = null) {
    return `/api/v1/threads/delete/${id}`
  },

  restore: function (id = null) {
    return `/api/v1/threads/restore/${id}`
  },

  trashed: function () {
    return '/api/v1/threads/trashed'
  },

  show: function (id = null) {
    return `/api/v1/threads/${id}`
  },

  update: function (id = null) {
    return `/api/v1/threads/${id}`
  },

  upload: function () {
    return `/api/v1/threads/upload`
  },

  destroy: function (id = null) {
    return `/api/v1/threads/${id}`
  },

  comments: {
    store: function () {
      return `/api/v1/comments`
    },
  },

  category: {
    list: function () {
      return '/api/v1/threads/categories'
    },

    show: function (id = null) {
      return `/api/v1/threads/categories/${id}`
    },

    store: function () {
      return '/api/v1/threads/categories'
    },

    delete: function (id = null) {
      return `/api/v1/threads/categories/delete/${id}`
    },

    restore: function (id = null) {
      return `/api/v1/threads/categories/restore/${id}`
    },

    trashed: function () {
      return '/api/v1/threads/categories/trashed'
    },

    update: function (id = null) {
      return `/api/v1/threads/categories/${id}`
    },

    destroy: function (id = null) {
      return `/api/v1/threads/categories/${id}`
    },
  },
}
