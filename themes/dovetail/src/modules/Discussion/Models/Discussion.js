class Discussion {
  constructor () {
    this.loading = false
    this.isPrestine = true
    this.isValid = true
    this.errors = []

    this.data = {
      title: '',
      slug: '',
      body: '',
      type: 'thread',
      user_id: '',
      user: {},
      delete: null,
      category: '',
      category_id: null,
      locked_at: null,
      metadata: {
        lockable: true,
      },
    }
  }
}

export default Discussion
