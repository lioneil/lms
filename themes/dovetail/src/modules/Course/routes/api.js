export default {
  list: function () {
    return '/api/v1/courses'
  },

  store: function () {
    return '/api/v1/courses'
  },

  delete: function (id = null) {
    return `/api/v1/courses/delete/${id}`
  },

  restore: function (id = null) {
    return `/api/v1/courses/restore/${id}`
  },

  trashed: function () {
    return '/api/v1/courses/trashed'
  },

  show: function (id = null) {
    return `/api/v1/courses/${id}`
  },

  update: function (id = null) {
    return `/api/v1/courses/${id}`
  },

  destroy: function (id = null) {
    return `/api/v1/courses/${id}`
  },

  subscribe: function (id) {
    return `/api/v1/courses/${id}/subscribe`
  },

  unsubscribe: function (id) {
    return `/api/v1/courses/${id}/unsubscribe`
  },

  all: function () {
    return '/api/v1/web/courses'
  },

  single: function (courseslug) {
    return `/api/v1/web/courses/${courseslug}`
  },

  lesson: function (courseslug, contentslug) {
    return `/api/v1/web/courses/${courseslug}/contents/${contentslug}`
  },

  publish: function (id) {
    return `/api/v1/courses/${id}/publish`
  },

  unpublish: function (id) {
    return `/api/v1/courses/${id}/unpublish`
  },

  assignment: {
    list: function () {
      return '/api/v1/assignments'
    },

    show: function (id = null) {
      return `/api/v1/assignments/${id}`
    },

    owned: function () {
      return '/api/v1/assignments/owned'
    },

    store: function () {
      return '/api/v1/assignments'
    },

    delete: function (id = null) {
      return `/api/v1/assignments/delete/${id}`
    },

    restore: function (id = null) {
      return `/api/v1/assignments/restore/${id}`
    },

    trashed: function () {
      return '/api/v1/assignments/trashed'
    },

    update: function (id = null) {
      return `/api/v1/assignments/${id}`
    },

    destroy: function (id = null) {
      return `/api/v1/assignments/${id}`
    },
  },

  category: {
    list: function () {
      return '/api/v1/courses/categories'
    },

    show: function (id = null) {
      return `/api/v1/courses/categories/${id}`
    },

    store: function () {
      return '/api/v1/courses/categories'
    },

    delete: function (id = null) {
      return `/api/v1/courses/categories/delete/${id}`
    },

    restore: function (id = null) {
      return `/api/v1/courses/categories/restore/${id}`
    },

    trashed: function () {
      return '/api/v1/courses/categories/trashed'
    },

    update: function (id = null) {
      return `/api/v1/courses/categories/${id}`
    },

    destroy: function (id = null) {
      return `/api/v1/courses/categories/${id}`
    },
  },

  content: {
    list: function () {
      return '/api/v1/courses/contents'
    },

    reorder: function () {
      return '/api/v1/courses/contents/reorder'
    },

    store: function () {
      return '/api/v1/courses/contents'
    },

    delete: function (id = null) {
      return `/api/v1/courses/contents/delete/${id}`
    },

    restore: function (id = null) {
      return `/api/v1/courses/contents/restore/${id}`
    },

    trashed: function () {
      return '/api/v1/courses/contents/trashed'
    },

    show: function (id = null) {
      return `/api/v1/courses/contents/${id}`
    },

    update: function (id = null) {
      return `/api/v1/courses/contents/${id}`
    },

    destroy: function (id = null) {
      return `/api/v1/courses/contents/${id}`
    },

    owned: function () {
      return '/api/v1/courses/contents/owned'
    },

    clone: function (id) {
      return `/api/v1/courses/contents/${id}/clone`
    },

    upload: function () {
      return '/api/v1/courses/contents/upload'
    },

    complete: function (id) {
      return `/api/v1/courses/contents/${id}/complete`
    },
  },

  comments: {
    store: function () {
      return `/api/v1/comments`
    },
  },

  material: {
    list: function () {
      return '/api/v1/materials'
    },

    show: function (id = null) {
      return `/api/v1/materials/${id}`
    },

    owned: function () {
      return '/api/v1/materials/owned'
    },

    store: function () {
      return '/api/v1/materials'
    },

    delete: function (id = null) {
      return `/api/v1/materials/delete/${id}`
    },

    restore: function (id = null) {
      return `/api/v1/materials/restore/${id}`
    },

    trashed: function () {
      return '/api/v1/materials/trashed'
    },

    update: function (id = null) {
      return `/api/v1/materials/${id}`
    },

    destroy: function (id = null) {
      return `/api/v1/materials/${id}`
    },
  },

  tag: {
    list: function () {
      return '/api/v1/tags'
    },

    show: function (id = null) {
      return `/api/v1/tags/${id}`
    },

    store: function () {
      return '/api/v1/tags'
    },

    update: function (id = null) {
      return `/api/v1/tags/${id}`
    },

    destroy: function (id = null) {
      return `/api/v1/tags/${id}`
    },
  },
}
