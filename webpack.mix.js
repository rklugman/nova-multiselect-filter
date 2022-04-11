let mix = require('laravel-mix')
let path = require('path')

mix.alias({
  'laravel-nova': path.join(__dirname, '../../vendor/laravel/nova/resources/js/mixins/packages.js'),
})

mix.js('resources/js/filter.js', 'js').vue({ version: 3 })
  .webpackConfig({
    externals: {
      vue: 'Vue',
    },
    output: {
      uniqueName: 'vendor/nova-multiselect-filter',
    }
  })
  .setPublicPath('dist')

