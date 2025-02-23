import * as esbuild from 'esbuild'

const isDev = process.argv.includes('--dev')

async function compile(options) {
    const context = await esbuild.context(options)

    if (isDev) {
        await context.watch()
    } else {
        await context.rebuild()
        await context.dispose()
    }
}

const defaultOptions = {
    define: {
        'process.env.NODE_ENV': isDev ? `'development'` : `'production'`,
    },
    bundle: true,
    mainFields: ['module', 'main'],
    platform: 'neutral',
    sourcemap: isDev ? 'inline' : false,
    sourcesContent: isDev,
    treeShaking: true,
    target: ['es2020'],
    minify: !isDev,
    loader: {
        '.jpg': 'dataurl',
        '.png': 'dataurl',
        '.svg': 'text',
        '.gif': 'dataurl',
        '.woff': 'file',
        '.woff2': 'file',
        '.data': 'base64',
    },
    plugins: [
        {
            name: 'watchPlugin',
            setup: function (build) {
                build.onStart(() => {
                    console.log(
                        `Build started at ${new Date(
                            Date.now(),
                        ).toLocaleTimeString()}: ${
                            build.initialOptions.outfile
                        }`,
                    )
                })

                build.onEnd((result) => {
                    if (result.errors.length > 0) {
                        console.log(
                            `Build failed at ${new Date(
                                Date.now(),
                            ).toLocaleTimeString()}: ${
                                build.initialOptions.outfile
                            }`,
                            result.errors,
                        )
                    } else {
                        console.log(
                            `Build finished at ${new Date(
                                Date.now(),
                            ).toLocaleTimeString()}: ${
                                build.initialOptions.outfile
                            }`,
                        )
                    }
                })
            },
        },
    ],
}

const corePackages = [
    'actions',
    'forms',
    'notifications',
    'panels',
    'schemas',
    'support',
    'tables',
]

corePackages.forEach((packageName) => {
    compile({
        ...defaultOptions,
        platform: 'browser',
        entryPoints: [`./packages/${packageName}/resources/js/index.js`],
        outfile: `./packages/${packageName}/dist/index.js`,
    })
})

compile({
    ...defaultOptions,
    platform: 'browser',
    entryPoints: [`./packages/panels/resources/js/echo.js`],
    outfile: `./packages/panels/dist/echo.js`,
})

compile({
    ...defaultOptions,
    platform: 'browser',
    entryPoints: [`./packages/panels/resources/js/fonts/inter.js`],
    outfile: `./packages/panels/dist/fonts/inter/index.js`,
})

const formComponents = [
    'checkbox-list',
    'color-picker',
    'date-time-picker',
    'file-upload',
    'key-value',
    'markdown-editor',
    'rich-editor',
    'select',
    'tags-input',
    'textarea',
]

formComponents.forEach((component) => {
    compile({
        ...defaultOptions,
        entryPoints: [
            `./packages/forms/resources/js/components/${component}.js`,
        ],
        outfile: `./packages/forms/dist/components/${component}.js`,
    })
})

compile({
    ...defaultOptions,
    entryPoints: [`./packages/tables/resources/js/components/table.js`],
    outfile: `./packages/tables/dist/components/table.js`,
})

const schemaComponents = ['actions', 'tabs', 'wizard']

schemaComponents.forEach((component) => {
    compile({
        ...defaultOptions,
        entryPoints: [
            `./packages/schemas/resources/js/components/${component}.js`,
        ],
        outfile: `./packages/schemas/dist/components/${component}.js`,
    })
})

const tableColumns = ['checkbox', 'select', 'text-input', 'toggle']

tableColumns.forEach((column) => {
    compile({
        ...defaultOptions,
        entryPoints: [
            `./packages/tables/resources/js/components/columns/${column}.js`,
        ],
        outfile: `./packages/tables/dist/components/columns/${column}.js`,
    })
})

const widgets = ['chart', 'stats-overview/stat/chart']

widgets.forEach((widget) => {
    compile({
        ...defaultOptions,
        entryPoints: [
            `./packages/widgets/resources/js/components/${widget}.js`,
        ],
        outfile: `./packages/widgets/dist/components/${widget}.js`,
    })
})
