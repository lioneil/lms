class Assignment {
  constructor () {
    this.loading = false
    this.isPrestine = true
    this.isValid = true
    this.errors = []

    this.data = {
      title: '',
      uri: '',
      pathname: null,
      file: null,
      coursewareable_id: '',
      coursewareable_type: 'Course\\Models\\Course',
      type: 'assignment',
      created: '',
    }
  }
}

export default Assignment
