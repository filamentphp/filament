import { test } from 'node:test'
import assert from 'node:assert/strict'
import { mkdtemp, readFile, rm, writeFile } from 'node:fs/promises'
import { join, resolve } from 'node:path'
import { tmpdir } from 'node:os'
import { execFileSync } from 'node:child_process'
import { build } from 'esbuild'
import { parse, compileScript } from 'vue/compiler-sfc'
import { compile, compileModule } from 'svelte/compiler'

const documentation = await readFile(
    new URL('../../packages/forms/docs/22-custom-fields.md', import.meta.url),
    'utf8',
)

function examples(heading) {
    const section = documentation
        .split(`### ${heading}\n`)[1]
        ?.split('\n### ')[0]
    assert.ok(section, `Missing documentation section: ${heading}`)

    return [
        ...section.matchAll(/```(?:jsx|js|vue|svelte|ts)\n([\s\S]*?)\n```/g),
    ].map((match) => match[1])
}

for (const framework of ['React', 'Vue', 'Svelte']) {
    test(`the documented ${framework} field compiles to an ES module with a default export`, async () => {
        const [component, entry = component] = examples(
            `Creating a ${framework} field`,
        )
        const filename =
            framework === 'Svelte'
                ? 'location-picker.svelte.js'
                : 'location-picker.jsx'
        const result = await build({
            stdin: {
                contents:
                    framework === 'Svelte'
                        ? compileModule(entry, { filename, generate: 'client' })
                              .js.code
                        : entry,
                loader: 'jsx',
                resolveDir: process.cwd(),
                sourcefile: filename,
            },
            bundle: true,
            format: 'esm',
            write: false,
            metafile: true,
            minify: true,
            define: { 'process.env.NODE_ENV': '"production"' },
            plugins: [
                {
                    name: 'documented-components',
                    setup(builder) {
                        builder.onResolve(
                            { filter: /^\.\/LocationPicker\.(vue|svelte)$/ },
                            ({ path }) => ({
                                path,
                                namespace: 'documentation',
                            }),
                        )
                        builder.onLoad(
                            { filter: /.*/, namespace: 'documentation' },
                            ({ path }) => {
                                const contents =
                                    framework === 'Vue'
                                        ? compileScript(
                                              parse(component, {
                                                  filename: path,
                                              }).descriptor,
                                              {
                                                  id: 'documented-location-picker',
                                                  inlineTemplate: true,
                                              },
                                          ).content
                                        : compile(component, {
                                              filename: path,
                                              generate: 'client',
                                          }).js.code

                                return { contents, resolveDir: process.cwd() }
                            },
                        )
                    },
                },
            ],
        })

        assert.deepEqual(result.errors, [])
        assert.deepEqual(result.warnings, [])
        assert.deepEqual(Object.values(result.metafile.outputs)[0].exports, [
            'default',
        ])
        assert.deepEqual(Object.values(result.metafile.outputs)[0].imports, [])
    })
}

test('the documented renderer and exposed method types match the Composer declarations', async () => {
    const directory = await mkdtemp(join(tmpdir(), 'filament-docs-types-'))

    try {
        await writeFile(
            join(directory, 'renderer.ts'),
            examples('Typing renderers').join('\n'),
        )
        await writeFile(
            join(directory, 'tsconfig.json'),
            JSON.stringify({
                compilerOptions: {
                    strict: true,
                    noEmit: true,
                    target: 'es2020',
                    module: 'esnext',
                    moduleResolution: 'bundler',
                    paths: {
                        '@filament/forms/js-field': [
                            resolve(
                                'packages/forms/resources/js/types/js-field.d.ts',
                            ),
                        ],
                    },
                },
                files: ['renderer.ts'],
            }),
        )

        execFileSync(
            process.execPath,
            [
                'node_modules/typescript/bin/tsc',
                '--project',
                join(directory, 'tsconfig.json'),
            ],
            { stdio: 'inherit' },
        )
    } finally {
        await rm(directory, { recursive: true, force: true })
    }
})
