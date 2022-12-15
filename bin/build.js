const esbuild = require('esbuild')
const shouldWatch = process.argv.includes('--watch')

const packages = ['app', 'forms', 'notifications', 'support', 'tables']

packages.forEach((package) => {
    esbuild
        .build({
            define: {
                'process.env.NODE_ENV': shouldWatch
                    ? `'production'`
                    : `'development'`,
            },
            entryPoints: [`packages/${package}/resources/js/index.js`],
            outfile: `packages/${package}/dist/index.js`,
            bundle: true,
            platform: 'browser',
            mainFields: ['module', 'main'],
            watch: shouldWatch,
            minifySyntax: true,
            minifyWhitespace: true,
        })
        .catch(() => process.exit(1))
})

esbuild
    .build({
        define: {
            'process.env.NODE_ENV': shouldWatch
                ? `'production'`
                : `'development'`,
        },
        entryPoints: [`packages/app/resources/js/echo.js`],
        outfile: `packages/app/dist/echo.js`,
        bundle: true,
        platform: 'browser',
        mainFields: ['module', 'main'],
        watch: shouldWatch,
        minifySyntax: true,
        minifyWhitespace: true,
    })
    .catch(() => process.exit(1))

const formComponents = [
    'color-picker',
    'date-time-picker',
    'file-upload',
    'key-value',
    'markdown-editor',
    'rich-editor',
    'select',
    'tags-input',
    'text-input',
    'textarea',
]

formComponents.forEach((component) => {
    esbuild
        .build({
            define: {
                'process.env.NODE_ENV': shouldWatch
                    ? `'production'`
                    : `'development'`,
            },
            entryPoints: [
                `packages/forms/resources/js/components/${component}.js`,
            ],
            outfile: `packages/forms/dist/components/${component}.js`,
            bundle: true,
            platform: 'neutral',
            mainFields: ['module', 'main'],
            watch: shouldWatch,
            minifySyntax: true,
            minifyWhitespace: true,
        })
        .catch(() => process.exit(1))
})

esbuild
    .build({
        define: {
            'process.env.NODE_ENV': shouldWatch
                ? `'production'`
                : `'development'`,
        },
        entryPoints: [`packages/support/resources/js/async-alpine.js`],
        outfile: `packages/support/dist/async-alpine.js`,
        bundle: true,
        platform: 'neutral',
        mainFields: ['module', 'main'],
        watch: shouldWatch,
        minifySyntax: true,
        minifyWhitespace: true,
    })
    .catch(() => process.exit(1))

esbuild
    .build({
        define: {
            'process.env.NODE_ENV': shouldWatch
                ? `'production'`
                : `'development'`,
        },
        entryPoints: [`packages/widgets/resources/js/components/chart.js`],
        outfile: `packages/widgets/dist/components/chart.js`,
        bundle: true,
        platform: 'neutral',
        mainFields: ['module', 'main'],
        watch: shouldWatch,
        minifySyntax: true,
        minifyWhitespace: true,
    })
    .catch(() => process.exit(1))

esbuild
    .build({
        define: {
            'process.env.NODE_ENV': shouldWatch
                ? `'production'`
                : `'development'`,
        },
        entryPoints: [
            `packages/widgets/resources/js/components/stats-overview/card/chart.js`,
        ],
        outfile: `packages/widgets/dist/components/stats-overview/card/chart.js`,
        bundle: true,
        platform: 'neutral',
        mainFields: ['module', 'main'],
        watch: shouldWatch,
        minifySyntax: true,
        minifyWhitespace: true,
    })
    .catch(() => process.exit(1))
