export default {
  list: function () {
    return '/api/v1/assessments'
  },

  store: function () {
    return '/api/v1/assessments'
  },

  delete: function (id = null) {
    return `/api/v1/assessments/delete/${id}`
  },

  restore: function (id = null) {
    return `/api/v1/assessments/restore/${id}`
  },

  trashed: function () {
    return '/api/v1/assessments/trashed'
  },

  show: function (id = null) {
    return `/api/v1/assessments/${id}`
  },

  update: function (id = null) {
    return `/api/v1/assessments/${id}`
  },

  destroy: function (id = null) {
    return `/api/v1/assessments/${id}`
  },

  export: function () {
    return '/api/v1/assessments/export'
  },

  import: function () {
    return '/api/v1/assessments/import'
  },

  field: {
    list: function () {
      return '/api/v1/fields'
    },

    clone: function (id) {
      return `/api/v1/fields/${id}/clone`
    },

    store: function () {
      return '/api/v1/fields'
    },

    show: function (id = null) {
      return `/api/v1/fields/${id}`
    },

    update: function (id = null) {
      return `/api/v1/fields/${id}`
    },

    delete: function (id = null) {
      return `/api/v1/fields/${id}`
    },

    restore: function (id = null) {
      return `/api/v1/fields/restore/${id}`
    },

    reorder: function (id = null) {
      return `/api/v1/fields/reorder`
    },

    trashed: function () {
      return '/api/v1/fields/trashed'
    },

    upload: function () {
      return '/api/v1/fields/upload'
    }
  }
}
