require('dotenv').config()
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

// Config
const SRC = 'resources'
const DIST = 'dist'

// set paths
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
mix.postCss(`${SRC}/css/filament.css`, 'css')

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

/**
 * BrowserSync
 *
 * Automatically monitor your files for changes,
 * and inject your changes into the browser without requiring a manual refresh
 *
 * @link https://laravel.com/docs/master/mix#browsersync-reloading
 */
mix.browserSync({
  proxy: process.env.BROWSERSYNC_PROXY || 'http://127.0.0.1:8000',
  https: process.env.BROWSERSYNC_HTTPS || false,
})
