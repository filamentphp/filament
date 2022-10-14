const mix = require('laravel-mix')

mix.disableSuccessNotifications()
mix.options({
    terser: {
        extractComments: false,
    },
})
mix.setPublicPath('packages/admin/dist')
mix.setResourceRoot('packages/admin/resources')

mix.js('packages/admin/resources/js/index.js', 'packages/admin/dist')
mix.js('packages/admin/resources/js/echo.js', 'packages/admin/dist')
