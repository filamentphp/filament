import { build, transform } from 'esbuild'
import { existsSync, readFileSync } from 'node:fs'
import { mkdir, readFile, writeFile } from 'node:fs/promises'
import { resolve } from 'node:path'
import { compile, compileModule } from 'svelte/compiler'
import { parse, compileScript, registerTS } from 'vue/compiler-sfc'
import typescript from 'typescript'
import { execFileSync } from 'node:child_process'

registerTS(() => typescript)

execFileSync(
    process.execPath,
    [
        'node_modules/typescript/bin/tsc',
        '--strict',
        '--noEmit',
        '--target',
        'es2020',
        '--moduleResolution',
        'bundler',
        '--module',
        'esnext',
        'tests/js/js-field-types.ts',
    ],
    { stdio: 'inherit' },
)

const generatedEntryPoints = {}

for (const framework of ['js', 'react', 'vue', 'svelte']) {
    for (const isTypeScript of [false, true]) {
        const name = framework + (isTypeScript ? '-ts' : '')
        const directory = `build/generated-field-sources/${name}`
        await mkdir(directory, { recursive: true })
        const extension =
            framework === 'react'
                ? isTypeScript
                    ? 'tsx'
                    : 'jsx'
                : (framework === 'svelte' ? 'svelte.' : '') +
                  (isTypeScript ? 'ts' : 'js')
        const prefix =
            framework[0].toUpperCase() +
            framework.slice(1) +
            (isTypeScript ? 'TypeScript' : '')
        const files = { [`${prefix}FieldRenderer`]: `field.${extension}` }
        if (framework === 'vue' || framework === 'svelte') {
            files[`${prefix}FieldComponent`] = `GeneratedField.${framework}`
        }
        for (const [stub, filename] of Object.entries(files)) {
            const source = await readFile(
                `packages/forms/stubs/${stub}.stub`,
                'utf8',
            )
            await writeFile(
                `${directory}/${filename}`,
                source.replaceAll('{{ componentName }}', 'GeneratedField'),
            )
        }
        generatedEntryPoints[`generated-${name}`] =
            `${directory}/field.${extension}`

        if (isTypeScript) {
            const configuration = {
                compilerOptions: {
                    strict: true,
                    noEmit: true,
                    target: 'ES2020',
                    module: 'ESNext',
                    moduleResolution: 'Bundler',
                    jsx: 'preserve',
                    paths: {
                        '@filament/forms/js-field': [
                            resolve(
                                'packages/forms/resources/js/types/js-field.d.ts',
                            ),
                        ],
                    },
                },
                include: ['./**/*'],
            }
            await writeFile(
                `${directory}/tsconfig.json`,
                JSON.stringify(configuration),
            )
            const command =
                framework === 'svelte'
                    ? [
                          'node_modules/svelte-check/bin/svelte-check',
                          '--tsconfig',
                          `${directory}/tsconfig.json`,
                          '--fail-on-warnings',
                      ]
                    : [
                          framework === 'vue'
                              ? 'node_modules/vue-tsc/bin/vue-tsc.js'
                              : 'node_modules/typescript/bin/tsc',
                          '--project',
                          `${directory}/tsconfig.json`,
                      ]
            execFileSync(process.execPath, command, { stdio: 'inherit' })
        }
    }
}

// Build the CSS fixtures separately from the unstyled starters to avoid empty
// shared chunks, which Pest's asset server cannot serve.
for (const entryPoints of [
    {
        react: 'tests/js/renderers/react.jsx',
        vue: 'tests/js/renderers/vue.js',
        svelte: 'tests/js/renderers/svelte.svelte.js',
    },
    generatedEntryPoints,
]) {
    await build({
        entryPoints,
        outdir: 'build/js-field-renderers',
        bundle: true,
        splitting: true,
        chunkNames: 'chunks/[name]-[hash]',
        format: 'esm',
        minify: true,
        define: { 'process.env.NODE_ENV': '"production"' },
        plugins: [
            {
                name: 'framework-test-components',
                setup(builder) {
                    builder.onLoad(
                        { filter: /\.(vue|svelte|svelte\.(js|ts))$/ },
                        async ({ path }) => {
                            let source = await readFile(path, 'utf8')
                            if (path.endsWith('.vue')) {
                                const { descriptor } = parse(source, {
                                    filename: path,
                                })
                                return {
                                    contents: compileScript(descriptor, {
                                        id: 'test-field',
                                        inlineTemplate: true,
                                        fs: {
                                            fileExists: existsSync,
                                            readFile: (filename) =>
                                                readFileSync(filename, 'utf8'),
                                        },
                                    }).content,
                                    loader: 'ts',
                                }
                            }
                            if (path.endsWith('.svelte.ts')) {
                                source = (
                                    await transform(source, { loader: 'ts' })
                                ).code
                            }
                            return {
                                contents: (/\.svelte\.(js|ts)$/.test(path)
                                    ? compileModule(source, {
                                          filename: path,
                                          generate: 'client',
                                      })
                                    : compile(source, {
                                          filename: path,
                                          generate: 'client',
                                      })
                                ).js.code,
                            }
                        },
                    )
                },
            },
        ],
    })
}
