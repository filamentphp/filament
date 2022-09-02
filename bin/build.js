const esbuild = require('esbuild')
const shouldWatch = process.argv.includes('--watch')

const packages = ['forms', 'notifications']

packages.forEach((package) => {
    esbuild
        .build({
            define: {
                'process.env.NODE_ENV': shouldWatch
                    ? `'production'`
                    : `'development'`,
            },
            entryPoints: [`packages/${package}/resources/js/index.js`],
            outfile: `packages/${package}/dist/module.esm.js`,
            bundle: true,
            platform: 'neutral',
            mainFields: ['module', 'main'],
            watch: shouldWatch,
        })
        .catch(() => process.exit(1))
})
