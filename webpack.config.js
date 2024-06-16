'use strict';

const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const config = require('./resources/build/config.js');
const rules = require('./resources/build/rules.js');
const plugins = require('./resources/build/plugins.js');
const path = require('path');

module.exports = (env, arg) => {
  return {
    entry: {
      app: path.resolve(config.themepath, 'app.js'),
      vendor: path.resolve(config.themepath, 'vendor.js'),
      fonts: path.resolve(config.themepath, 'sass/fonts.scss'),
      critical: path.resolve(config.themepath, 'sass/critical.scss'),
    },
    output: {
      path: path.resolve(config.distpath),
      filename: 'js/[name].js',
    },
    cache: true,
    devtool: 'source-map',
    plugins: plugins,
    module: {
      rules,
    },
    resolve: {
      extensions: ['.js', '.css', '.scss', '.json'],
      alias: {
        '@': path.join(__dirname, 'resources/src'),
        vue: 'vue/dist/vue.esm.js'
      },
    },
  }
}
