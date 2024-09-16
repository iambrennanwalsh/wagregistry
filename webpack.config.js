const Encore = require('@symfony/webpack-encore')
const path = require('path')
const RunNodePlugin = require('run-node-webpack-plugin')

const env = process.env.NODE_ENV || 'development'

if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(env)
}

function createWebpackConfig(target) {
  Encore.reset()
  const config = Encore.addEntry(target, `./assets/${target}.tsx`)
    .setOutputPath(`public/build/${target}/`)
    .setPublicPath(`/build/${target}`)
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(target !== 'node')
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableReactPreset()
    .enableBabelTypeScriptPreset()
    .enablePostCssLoader()
    .configureCssLoader(options => {
      options.modules = undefined
    })
    .enableBuildNotifications(true, () => ({
      onlyOnError: true,
      contentImage: path.join(__dirname, 'public/images/favicon/favicon.ico')
    }))
    .configureImageRule({ type: 'asset' })
    .configureFontRule({ type: 'asset' })
    .addAliases({
      '@': path.resolve('assets')
    })
    .when(target == 'node' && env == 'development', Encore =>
      Encore.addPlugin(
        new RunNodePlugin({
          scriptToRun: './public/build/node/node.js',
          runOnlyOnChanges: false
        })
      )
    )
    .getWebpackConfig()
  config.target = target
  config.name = target
  return config
}

const webConfig = createWebpackConfig('web')
const nodeConfig = createWebpackConfig('node')

module.exports = [webConfig, nodeConfig]
