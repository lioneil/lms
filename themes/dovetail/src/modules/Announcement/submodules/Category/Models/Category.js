class Category {
  constructor () {
    this.loading = false
    this.isPrestine = true
    this.isValid = true
    this.errors = []

    this.data = {
      name: '',
      alias: '',
      code: '',
      description: '',
      icon: '',
      created: '',
      type: 'announcement',
      search: null,
    }
  }
}

export default Category
