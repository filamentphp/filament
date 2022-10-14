const mix = require('laravel-mix')

mix.disableSuccessNotifications()
mix.options({
    terser: {
        extractComments: false,
    },
})
mix.setPublicPath('packages/admin/dist')
mix.setResourceRoot('packages/admin/resources')
mix.version()

mix.js('packages/admin/resources/js/index.js', 'packages/admin/dist')
