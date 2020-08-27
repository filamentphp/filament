const mix = require('laravel-mix')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JS files.
 |
 */

mix
  .setPublicPath('dist')
  .setResourceRoot('/resources/')
  .js('resources/js/app.js', 'js')
  .postCss('resources/css/app.css', 'css', [
    require('postcss-import'),
    require('tailwindcss'),
    require('postcss-nested'),
  ])
  .version()
