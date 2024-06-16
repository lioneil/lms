import app from '@/config/app.js'

class General {
  constructor () {
    this.loading = false
    this.isPrestine = true
    this.isValid = true
    this.errors = []

    this.data = {
      file: app.logo
    }
  }
}

export default General
