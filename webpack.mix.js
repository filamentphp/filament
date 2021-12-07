const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');

mix.disableSuccessNotifications()
mix.options({
    terser: {
        extractComments: false,
    },
})
mix.setPublicPath('packages/admin/dist')
mix.setResourceRoot('packages/admin/resources')
mix.sourceMaps()
mix.version()

mix.js('packages/admin/resources/js/app.js', 'packages/admin/dist')

mix
    .postCss('packages/admin/resources/css/app.css', 'packages/admin/dist', [
        tailwindcss('packages/admin/tailwind.config.js'),
    ]).options({
        processCssUrls: false,
    })
