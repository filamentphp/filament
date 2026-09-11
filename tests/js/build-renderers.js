import { build } from 'esbuild'
import { readFile } from 'node:fs/promises'
import { compile, compileModule } from 'svelte/compiler'
import { parse, compileScript } from 'vue/compiler-sfc'
import { execFileSync } from 'node:child_process'

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

await build({
    entryPoints: {
        react: 'tests/js/renderers/react.jsx',
        vue: 'tests/js/renderers/vue.js',
        svelte: 'tests/js/renderers/svelte.svelte.js',
    },
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
                    { filter: /\.(vue|svelte|svelte\.js)$/ },
                    async ({ path }) => {
                        const source = await readFile(path, 'utf8')
                        if (path.endsWith('.vue')) {
                            const { descriptor } = parse(source, {
                                filename: path,
                            })
                            return {
                                contents: compileScript(descriptor, {
                                    id: 'test-field',
                                    inlineTemplate: true,
                                }).content,
                            }
                        }
                        return {
                            contents: (path.endsWith('.svelte.js')
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
