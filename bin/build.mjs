import * as esbuild from 'esbuild'

const isDev = process.argv.includes('--dev')

async function compile(options) {
    const ctx = await esbuild.context(options)
    if (isDev) {
        await ctx.watch()
    } else {
        await ctx.rebuild()
        await ctx.dispose()
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
    plugins: [{
        name: 'watchPlugin',
        setup: function (build) {
            build.onStart(() => {
                console.log(`Build started at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
            })

            build.onEnd((result) => {
                if (result.errors.length > 0) {
                    console.log(`Build failed at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`, result.errors)
                } else {
                    console.log(`Build finished at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
                }
            })
        }
    }],
}

const corePackages = [
    'app',
    'forms',
    'notifications',
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
    entryPoints: [`./node_modules/async-alpine/dist/async-alpine.script.js`],
    outfile: `./packages/support/dist/async-alpine.js`,
})

compile({
    ...defaultOptions,
    platform: 'browser',
    entryPoints: [`./packages/app/resources/js/echo.js`],
    outfile: `./packages/app/dist/echo.js`,
})

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

formComponents.forEach((componentName) => {
    compile({
        ...defaultOptions,
        entryPoints: [`./packages/forms/resources/js/components/${componentName}.js`],
        outfile: `./packages/forms/dist/components/${componentName}.js`,
    })
})

compile({
    ...defaultOptions,
    entryPoints: [`./packages/widgets/resources/js/components/chart.js`],
    outfile: `./packages/widgets/dist/components/chart.js`,
})

compile({
    ...defaultOptions,
    entryPoints: [`./packages/widgets/resources/js/components/stats-overview/card/chart.js`],
    outfile: `./packages/widgets/dist/components/stats-overview/card/chart.js`,
})
