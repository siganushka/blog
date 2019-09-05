var Encore = require('@symfony/webpack-encore')

if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev')
}

Encore
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .enableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enableBuildNotifications()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
  .enableSassLoader()
  .autoProvidejQuery()
  .configureBabel(() => {}, {
    useBuiltIns: 'usage',
    corejs: 3
  })
  .configureTerserPlugin((options) => {
    options.cache = true
    options.terserOptions = {
      output: { comments: false }
    }
  })
  .configureOptimizeCssPlugin((options) => {
    options.cssProcessor = require('cssnano')
    options.cssProcessorPluginOptions = {
      preset: ['default', { discardComments: { removeAll: true } }]
    }
  })
  .addEntry('app', ['./assets/js/app.js', './assets/scss/app.scss'])

module.exports = Encore.getWebpackConfig()
