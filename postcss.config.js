module.exports = {
  plugins: [
    require('postcss-import')({
      path: ['public/assets/css/src']
    }),
    require('postcss-custom-properties')({
      preserve: true
    }),
    require('autoprefixer')({
      overrideBrowserslist: ['> 1%', 'last 2 versions', 'not dead']
    }),
    require('cssnano')({
      preset: ['default', {
        discardComments: { removeAll: true },
        normalizeWhitespace: true,
        minifySelectors: true,
        minifyParams: true
      }]
    })
  ]
};