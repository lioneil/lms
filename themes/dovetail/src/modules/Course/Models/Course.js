class Course {
  constructor () {
    this.loading = false
    this.isPrestine = true
    this.isValid = true
    this.errors = []

    this.data = {
      title: '',
      subtitle: '',
      slug: '',
      code: '',
      description: '',
      icon: '',
      image: '',
      created: '',
      modified: '',
      updated_at: '',
      published_at: '',
      category: '',
      category_id: null,
      tags: [],
      search: null,
      metadata: {},
      contents: [],
      meta: {
        lessons: {
          first: {}
        }
      },
    }
  }
}

export default Course
