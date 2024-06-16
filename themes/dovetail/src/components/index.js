import camelCase from 'lodash/camelCase'
import upperFirst from 'lodash/upperFirst'
import Vue from 'vue'

const components = {
  install (Vue) {
    const requireComponent = require.context(
      // The relative path of the components folder
      '.',
      // Whether or not to look in subfolders
      true,
      // The regular expression used to match base component filenames
      /[A-Z]\w+\.(vue|js)$/
    )

    requireComponent.keys().forEach(fileName => {
      const componentConfig = requireComponent(fileName)

      const componentName = componentConfig.default.name || upperFirst(
        camelCase(
          fileName
            .split('/')
            .pop()
            .replace(/\.[^/.]+$/, '')
            .replace(/^\.\/(.*)\.\w+$/, '$1')
        )
      )

      Vue.component(
        componentName,
        // Look for the component options on `.default`, which will
        // exist if the component was exported with `export default`,
        // otherwise fall back to module's root.
        componentConfig.default || componentConfig
      )
    })
  }
}

Vue.use(components)
