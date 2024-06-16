class Classroom {
  constructor () {
    this.loading = false
    this.isPrestine = true
    this.isValid = true
    this.errors = []

    this.data = {
      name: '',
      code: '',
      description: '',
      courses: [],
      selected: [],
    }
  }
}

export default Classroom
