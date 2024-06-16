class Material {
  constructor () {
    this.loading = false
    this.isPrestine = true
    this.isValid = true
    this.errors = []

    this.data = {
      title: '',
      uri: null,
      pathname: null,
      coursewareable_id: '',
      coursewareable_type: 'Course\\Models\\Course',
      type: 'material',
      created: '',
    }
  }
}

export default Material
