class Assessment {
  constructor () {
    this.loading = false
    this.isPrestine = true
    this.isValid = true
    this.errors = []

    this.data = {
      title: '',
      subtitle: '',
      slug: '',
      url: '/',
      method: 'POST',
      type: 'assessment',
      description: '',
      fields: [],
    }
  }
}

export default Assessment
