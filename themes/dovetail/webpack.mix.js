require('laravel-mix-favicon');

const mix = require('laravel-mix');
const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin');
const webpack = require('webpack');

const CKEditorWebpackPlugin = require( '@ckeditor/ckeditor5-dev-webpack-plugin' );
const CKEStyles = require('@ckeditor/ckeditor5-dev-utils').styles;
const CKERegex = {
    svg: /ckeditor5-[^/\\]+[/\\]theme[/\\]icons[/\\][^/\\]+\.svg$/,
    css: /ckeditor5-[^/\\]+[/\\]theme[/\\].+\.css/,
};

Mix.listen('configReady', webpackConfig => {
    const rules = webpackConfig.module.rules;
    const targetSVG = /(\.(png|jpe?g|gif|webp)$|^((?!font).)*\.svg$)/;
    const targetFont = /(\.(woff2?|ttf|eot|otf)$|font.*\.svg$)/;
    const targetCSS = /\.css$/;

    // exclude CKE regex from mix's default rules
    // if there's a better way to loop/change this, open to suggestions
    for (let rule of rules) {
      if (rule.test.toString() === targetSVG.toString()) {
        rule.exclude = CKERegex.svg;
      }
      else if (rule.test.toString() === targetFont.toString()) {
        rule.exclude = CKERegex.svg;
      }
      else if (rule.test.toString() === targetCSS.toString()) {
        rule.exclude = CKERegex.css;
      }
    }
});

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.config.fileLoaderDirs.fonts = 'dist/fonts';
// mix.setPublicPath('dist/');
mix.setResourceRoot('/theme');

mix.sourceMaps();

mix
  .webpackConfig({
    output: {
      chunkFilename: 'dist/js/[name].js',
      publicPath: '/theme/',
    },
    plugins: [
      new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
      new VuetifyLoaderPlugin(),
      new CKEditorWebpackPlugin({
        language: 'en',
        translationsOutputFile: /app/,
        buildAllTranslationsToSeparateFiles: true,
      }),
    ],
    module: {
      rules: [
        {
          test: /\.scss/,
          loader: 'import-glob-loader'
        },
        {
          test: /\.sass/,
          loader: 'import-glob-loader'
        },
        {
          test: CKERegex.svg,
          use: [ 'raw-loader' ]
        },
        {
          test: CKERegex.css,
          use: [
            {
              loader: 'style-loader',
              options: {
                singleton: true
              }
            },
            {
              loader: 'postcss-loader',
              options: CKEStyles.getPostCssConfig({
                themeImporter: {
                  themePath: require.resolve('@ckeditor/ckeditor5-theme-lark'),
                },
                minify: true
              })
            },
          ]
        }
      ]
    },
    resolve: {
      extensions: ['.js', '.vue', '.json'],
      alias: {
        // 'vue$': 'vue/dist/vue.esm.js',
        '@': __dirname + '/src',
      },
    },
  })

  .autoload({
    // E.g. jquery: ['$', 'window.jQuery', 'window.$'],
  })

  .js('src/app.js', 'dist/js')

  .sass('src/sass/app.scss', 'dist/css')
  .sass('src/sass/fonts.scss', 'dist/css')

  .options({
    extractVueStyles: true,
  })

  .browserSync({
    proxy: 'http://localhost:8000/',
    files: [
      'dist/css/{*,**/*}.css',
      'dist/js/{*,**/*}.js',
      'templates/{*,**/*}.html.twig'
    ],
    open: false
  })
