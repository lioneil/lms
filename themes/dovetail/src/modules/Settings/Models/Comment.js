import app from '@/config/app.js'

class Comment {
  constructor () {
    this.loading = false
    this.isPrestine = true
    this.isValid = true
    this.errors = []

    this.data = {
      'commenting:enable': '0',
      'blacklisted:words': '',
      'blacklisted:exact': '0'
    }
  }
}

export default Comment
