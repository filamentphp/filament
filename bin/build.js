const shouldWatch = process.argv.includes('--watch')

require('esbuild').build({
    define: {
        'process.env.NODE_ENV': shouldWatch ? `'production'` : `'development'`,
    },
    entryPoints: ['packages/forms/resources/js/index.js'],
    outfile: 'packages/forms/dist/module.esm.js',
    bundle: true,
    platform: 'neutral',
    mainFields: ['module'],
    watch: shouldWatch,
}).catch(() => process.exit(1))
