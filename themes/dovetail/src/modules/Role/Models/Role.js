class Role {
  constructor () {
    this.loading = false
    this.isPrestine = true
    this.isValid = true
    this.errors = []
    this.permissions = []
    this.selected = []

    this.data = {
      name: '',
      alias: '',
      code: '',
      description: '',
      created: '',
      permissions: [],
      selected: [],
    }
  }
}

export default Role
