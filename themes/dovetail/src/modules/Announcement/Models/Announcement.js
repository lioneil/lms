class Announcement {
  constructor () {
    this.loading = false
    this.isPrestine = true
    this.isValid = true
    this.errors = []

    this.data = {
      title: '',
      slug: '',
      body: '',
      type: 'announcement',
      photo: '',
      created: '',
      modified: '',
      updated_at: '',
      published_at: '',
      category: '',
      category_id: null,
      search: null,
      metadata: {},
    }
  }
}

export default Announcement
