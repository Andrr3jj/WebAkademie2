// vue.config.js
module.exports = {
  configureWebpack: {
    resolve: {
      alias: {
        // Pomôž Webpacku nájsť ESM bundler build (stabilné na Vue CLI)
        'vue-i18n': 'vue-i18n/dist/vue-i18n.esm-bundler.js',
        '@': require('path').resolve(__dirname, 'src'),
      },
    },
  },
};
