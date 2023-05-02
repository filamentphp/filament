import * as esbuild from 'esbuild'
import postcss from 'esbuild-postcss'

const shouldWatch = process.argv.includes('--watch')

async function compile(options) {
    const ctx = await esbuild.context(options)
    if (shouldWatch) {
        await ctx.watch()
    } else {
        await ctx.rebuild()
        await ctx.dispose()
    }
}


const esm = {
    define: {
        'process.env.NODE_ENV': shouldWatch ? `'production'` : `'development'`,
    },
    bundle: true,
    mainFields: ['module', 'main'],
    platform: 'neutral', //output format is set to esm, which uses the export syntax introduced with ECMAScript 2015 (i.e. ES6
    sourcemap: shouldWatch,
    treeShaking: true, //removes unused code
    target: ['es2020'], //or es2015? what should Filament support?
    minify: shouldWatch, //includes whitespace, identifiers and syntax
    loader: { // convert image urls to embedded images, no need to publish css images
        '.jpg': 'dataurl',
        '.png': 'dataurl',
        '.svg': 'text',
        '.gif': 'dataurl',
        '.woff': 'file',
        '.woff2': 'file',
    },
    plugins: [
        postcss(), //remove if you don't import css files in your js, else: create a postcss.config.js file
        {
            name: 'watchPlugin',
            setup(build) {
                build.onStart(() => {
                    console.log('Build starting:' + new Date(Date.now()).toLocaleTimeString())
                })
                build.onEnd(result => {
                    if (result.errors.length > 0) {
                        console.log('Build failed:' + new Date(Date.now()).toLocaleTimeString(), result.errors)
                    } else {
                        console.log('Build succeeded:' + new Date(Date.now()).toLocaleTimeString())
                    }
                })
            }
        }
    ]
}

const invoked = {
    ...esm,
    target: ['es2020', 'chrome112', 'edge112', 'firefox113', 'safari16'], //Which browser versions should Filament support?
    platform: 'browser', //'wraps the generated JavaScript code in an immediately-invoked function expression to prevent variables from leaking into the global scope
}

//ESM Modules -------------------------------------------------------------------------------------

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
    esm.entryPoints = `packages/forms/resources/js/components/${component}.js`
    esm.outfile = `packages/forms/dist/components/${component}.js`
    compile(esm)
})

esm.entryPoints = [`packages/support/resources/js/async-alpine.js`]
esm.outfile = `packages/support/dist/async-alpine.js`
compile(esm)


esm.entryPoints = [`packages/widgets/resources/js/components/chart.js`]
esm.outfile = `packages/widgets/dist/components/chart.js`
compile(esm)

esm.entryPoints = [`packages/widgets/resources/js/components/stats-overview/card/chart.js`]
esm.outfile = `packages/widgets/dist/components/stats-overview/card/chart.js`
compile(esm)

//--------------------------------------------------------------------------------------------------

//window scripts -----------------------------------------------------------------------------------

const pkgs = ['app', 'forms', 'notifications', 'support', 'tables']
//renamed package to pkg, because package is a reserved word in JS
packages.forEach((pkg) => {
    invoked.entryPoints = [`packages/${pkg}/resources/js/index.js`]
    invoked.outfile = `packages/${pkg}/dist/index.js`
    compile(invoked)
})

invoked.entryPoints = [`packages/app/resources/js/echo.js`]
invoked.outfile = `packages/app/dist/echo.js`
compile(invoked)

//--------------------------------------------------------------------------------------------------
