import app from '@/config/app.js'

class Mail {
  constructor () {
    this.loading = false
    this.isPrestine = true
    this.isValid = true
    this.errors = []

    this.data = {
      'mail:name': '',
      'mail:address': '',
      'mail:driver': 'smtp',
      'mail:host': 'smtp.gmail.com',
      'mail:port': '465',
      'mail:username': '',
      'mail:password': '',
      'mail:encryption': 'ssl'
    }
  }
}

export default Mail
