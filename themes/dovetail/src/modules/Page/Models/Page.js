class Page {
  constructor () {
    this.loading = true
    this.isPrestine = true
    this.isValid = true
    this.errors = []

    this.data = {
      title: '',
      code: '',
      slug: '',
      template_id: '',
      feature: [],
      category_id: null,
      metadata: {},
      published_at: '',
      drafted_at: ''
    }
  }
}

export default Page
