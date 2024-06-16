'use strict';

const ExtractTextPlugin = require('extract-text-webpack-plugin');
const globImporter = require('node-sass-glob-importer');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const NodeSassJsonImporter = require('node-sass-json-importer');
const theme = require('../theme.json');

module.exports = [
  /**
   *--------------------------------------------------------------------------
   * Vue Loader
   *--------------------------------------------------------------------------
   *
   */
  {
    test: /\.vue$/,
    loader: 'vue-loader',
  },

  /**
   *--------------------------------------------------------------------------
   * Stylus Loader
   *--------------------------------------------------------------------------
   *
   */
  {
    test: /\.styl$/,
    use: [
      process.env.NODE_ENV !== 'production' ? 'style-loader' : MiniCssExtractPlugin.loader,
      'css-loader',
      'postcss-loader',
      'stylus-loader',
    ]
  },

  /**
   *--------------------------------------------------------------------------
   * Babel Loader
   *--------------------------------------------------------------------------
   *
   */
  {
    test: /\.js$/,
    exclude: /node_modules/,
    use: {
      loader: 'babel-loader',
      options: {
        plugins: [
          "@babel/plugin-syntax-dynamic-import"
        ]
      }
    },
  },

  /**
   *--------------------------------------------------------------------------
   * Css/Sass Loader
   *--------------------------------------------------------------------------
   *
   * Extract and Minify css/scss/sass files.
   *
   */
  {
    test: /\.css$/,
    use: [
      'style-loader',
      'css-loader',
      'postcss-loader',
      'import-glob-loader',
      {
        loader: 'sass-loader', // compiles Sass to CSS
        options: {
          importer: [globImporter(), NodeSassJsonImporter()],
        },
      },
    ]
  },
  {
    test: /\.scss$/,
    use: [
      'style-loader',
      'css-loader',
      'import-glob-loader',
      {
        loader: 'sass-loader', // compiles Sass to CSS
        options: {
          importer: [globImporter(), NodeSassJsonImporter()],
        },
      },
      {
        // Reads Sass vars from files or inlined in the options property
        loader: "@epegzz/sass-vars-loader",
        options: {
          syntax: 'scss',
          vars: {
            colors: theme.colors,
            primary: theme.colors.primary,
            secondary: theme.colors.secondary,
            accent: theme.colors.accent,
            success: theme.colors.success,
            info: theme.colors.info,
            warning: theme.colors.warning,
            danger: theme.colors.danger,
            light: theme.colors.light,
            dark: theme.colors.dark,
            text: theme.colors.text,
            card: theme.colors.card,
            sidebar: theme.colors.sidebar,
            workspace: theme.colors.workspace,
          },
        },
      },
    ]
  },

  /**
   *--------------------------------------------------------------------------
   * HTML Loader
   *--------------------------------------------------------------------------
   *
   * Perform html optimizations.
   *
   */
  {
    test: /\.html$/,
    use: [
      {
        loader: 'html-loader',
        options: { minimize: true },
      },
    ],
  },

  /**
   *--------------------------------------------------------------------------
   * ESLint
   *--------------------------------------------------------------------------
   *
   */
  // {
  //   test: /\.(js|vue)$/,
  //   exclude: /node_modules/,
  //   use: ["babel-loader", "eslint-loader"]
  // },
  {
    test: /\.js$/,
    exclude: /node_modules/,
    loader: "babel-loader"
  },
  {
    enforce: "pre",
    test: /\.js$/,
    exclude: /node_modules/,
    loader: "eslint-loader",
    options: {
      fix: true,
      formatter: require('eslint').CLIEngine.getFormatter('stylish'),
      formatter: require("eslint-friendly-formatter"),
    }
  },

  /**
   *--------------------------------------------------------------------------
   * Fonts Loader
   *--------------------------------------------------------------------------
   *
   */
  {
    test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
    loader: 'url-loader',
    options: {
      limit: 10000,
      name: '[name].[hash:7].[ext]',
      outputPath: '/fonts',
      publicPath: '/theme/dist/fonts',
    },
  },

  /**
   *--------------------------------------------------------------------------
   * File Loader
   *--------------------------------------------------------------------------
   *
   */
  {
    test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
    loader: 'file-loader',
    options: {
      limit: 10000,
      name: 'assets/img/[name].[ext]',
    },
  },
];
