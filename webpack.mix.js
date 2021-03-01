let mix = require('laravel-mix')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your application.
 |
 */

const SRC = 'resources'
const DIST = 'dist'

mix.disableSuccessNotifications()
mix.setPublicPath(DIST)
mix.setResourceRoot(`/${SRC}/`)

/**
 * Process Javascript
 *
 * @link https://laravel.com/docs/master/mix#working-with-scripts
 */
mix.js(`${SRC}/js/filament.js`, 'js')

/**
 * Process CSS via PostCSS
 *
 *
 * @link https://laravel.com/docs/master/mix#postcss
 */
mix.postCss(`${SRC}/css/filament.css`, 'css').options({
  processCssUrls: false,
})

/**
 * Sourcemaps
 *
 * Provide extra debugging information to your browser's
 * developer tools when using compiled assets.
 *
 * @link https://laravel.com/docs/master/mix#css-source-maps
 */
mix.sourceMaps()

if (mix.inProduction()) {
  /**
   * Versioning / Cache Busting
   *
   * After generating the versioned file, you won't know the exact file name.
   * So, you should use Laravel's global mix function within your views
   * to load the appropriately hashed asset.
   *
   * @link https://laravel.com/docs/master/mix#versioning-and-cache-busting
   */
  mix.version()
}
