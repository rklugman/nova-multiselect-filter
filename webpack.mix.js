let mix = require('laravel-mix')
let path = require('path')

mix.alias({
  'laravel-nova': path.join(__dirname, 'vendor/laravel/nova/resources/js/mixins/packages.js'),
  'laravel-nova-filterable': path.join(__dirname, 'vendor/laravel/nova/resources/js/mixins/Filterable.js'),
  'laravel-nova-interactswithquerystring': path.join(__dirname, 'vendor/laravel/nova/resources/js/mixins/InteractsWithQueryString.js'),
})

mix.js('resources/js/filter.js', 'js')
  .vue({ version: 3 })
  .alias({'@': path.join(__dirname, 'vendor/laravel/nova/resources/js/')})
  .webpackConfig({
    externals: {
      vue: 'Vue',
    },
    output: {
      uniqueName: 'vendor/nova-multiselect-filter',
    }
  })
  .setPublicPath('dist')

