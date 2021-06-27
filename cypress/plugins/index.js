const { startDevServer } = require('@cypress/webpack-dev-server')
// const webpackConfig = require('@vue/cli-service/webpack.config.js')
const { getWebpackConfig } = require('nuxt')

module.exports = (on, config) => {
  on('dev-server:start', async (options) => {
    let webpackConfig = await getWebpackConfig('client', {
      for: 'dev',
    })
    // delete webpackConfig.output
    // webpackConfig.plugins = webpackConfig.plugins.filter((a) => a.constructor.name !== 'HtmlWebpackPlugin')
    return startDevServer({
      options,
      webpackConfig
    })
  })

  return config
}