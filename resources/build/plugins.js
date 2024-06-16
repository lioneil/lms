'use strict';

const VueLoaderPlugin = require('vue-loader/lib/plugin')
const BrowserSyncPlugin = require('browser-sync-webpack-plugin')
const CopyWebpackPlugin = require('copy-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const FixStyleOnlyEntriesPlugin = require("webpack-fix-style-only-entries");
const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const path = require('path');
const Visualizer = require('webpack-visualizer-plugin');
const webpack = require('webpack');
const theme = require('../theme.json');
const StyleLintPlugin = require('stylelint-webpack-plugin');

module.exports = [
  /**
   *--------------------------------------------------------------------------
   * Vue Loader
   *--------------------------------------------------------------------------
   *
   */
  new VueLoaderPlugin(),

  /**
   *--------------------------------------------------------------------------
   * CSS Plugin
   *--------------------------------------------------------------------------
   *
   */
  new FixStyleOnlyEntriesPlugin(),

  new MiniCssExtractPlugin({
    filename: 'css/[name].css',
    chunkFilename: 'css/[id].css',
  }),

  new ExtractTextPlugin({filename: 'css/[name].css'}),

  /**
   *--------------------------------------------------------------------------
   * Copy Images
   *--------------------------------------------------------------------------
   *
   */
  // new CopyWebpackPlugin([{
  //   from: 'src/assets/img/',
  //   to: 'img/[name].[ext]',
  //   toType: 'template',
  // }]),

  /**
   *--------------------------------------------------------------------------
   * Copy Logos
   *--------------------------------------------------------------------------
   *
   */
  // new CopyWebpackPlugin([{
  //   from: 'src/assets/logos/',
  //   to: 'logos/[name].[ext]',
  //   toType: 'template',
  // }]),

  /**
   *--------------------------------------------------------------------------
   * Browser Sync
   *--------------------------------------------------------------------------
   * browse to http://localhost:3000/ during development,
   * <root>/public directory is being served
   */
  new BrowserSyncPlugin({
    host: 'localhost',
    port: 3000,
    open: false,
    proxy: 'http://localhost:8000/',
  }),

  /**
   *--------------------------------------------------------------------------
   * Style Lint Plugin
   *--------------------------------------------------------------------------
   *
   * Stats for nerds.
   * @see  dist/stats.html
   *
   */
  new StyleLintPlugin({
    files: ['**/*.{vue,html,css,scss,sass,styl}'],
    fix: true,
  }),

  /**
   *--------------------------------------------------------------------------
   * Visualizer Plugin
   *--------------------------------------------------------------------------
   *
   * Stats for nerds.
   * @see  dist/stats.html
   *
   */
  new Visualizer(),
];
