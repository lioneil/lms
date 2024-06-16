import slugify from 'slugify'

window.slugify = function (string, separator = '-') {
  return slugify(string, {
    replacement: separator,
    lower: true
  })
}
