const mix = require('laravel-mix')

const purgecss = require('@fullhuman/postcss-purgecss')({
  content: [
    './config/*.php',
    './src/**/*.php',
    './resources/views/**/*.php',
    './resources/js/**/*.js',
  ],
  defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || [],
  whitelistPatterns: [/alert/],
})

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
    ...(process.env.NODE_ENV === 'production' ? [purgecss] : []),
  ])
  .version()
