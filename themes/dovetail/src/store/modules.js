import * as _h from '@/core/helpers'

const modules = {}
const requireModules = require.context(
  // The relative path of the components folder
  '@',
  // Whether or not to look in subfolders
  true,
  // The regular expression used to match base module filenames
  // /store\/modules\/[a-z]\.js$/
  /(modules|components)\/\w+\/store\/\w+\.js/
)

requireModules.keys().forEach(filename => {
  const namespace = _h.strip_extension(_h.basename(filename, '/'))
  modules[namespace] = requireModules(filename).default || requireModules(filename)
})

export default modules
