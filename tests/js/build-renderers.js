import { build } from 'esbuild'
import assert from 'node:assert/strict'
import { execFileSync } from 'node:child_process'
import { buildRendererStarters } from './build-renderer-starters.js'
import { buildViteRenderers } from './build-vite-renderers.js'

const { field: generatedEntryPoints } = await buildRendererStarters({
    field: 'forms',
    component: {
        packageName: 'schemas',
        stubName: 'SchemaComponent',
        componentStubName: 'SchemaComponent',
    },
    widget: 'widgets',
})

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
        'tests/js/js-widget-types.ts',
    ],
    { stdio: 'inherit' },
)

await buildViteRenderers(
    {
        react: 'tests/js/renderers/react.jsx',
        vue: 'tests/js/renderers/vue.js',
        svelte: 'tests/js/renderers/svelte.svelte.js',
    },
    'build/js-field-renderers',
)

await buildViteRenderers(
    { avatar: 'tests/js/renderers/avatar.svelte.js' },
    'build/js-avatar-renderers',
)

await buildViteRenderers(
    { breadcrumbs: 'tests/js/renderers/breadcrumbs.svelte.js' },
    'build/js-breadcrumbs-renderers',
)

await buildViteRenderers(
    { fieldset: 'tests/js/renderers/fieldset.svelte.js' },
    'build/js-fieldset-renderers',
)

await buildViteRenderers(
    { 'loading-indicator': 'tests/js/renderers/loading-indicator.svelte.js' },
    'build/js-loading-indicator-renderers',
)

await buildViteRenderers(
    { checkbox: 'tests/js/renderers/checkbox.svelte.js' },
    'build/js-checkbox-renderers',
)

await buildViteRenderers(
    { radio: 'tests/js/renderers/radio.svelte.js' },
    'build/js-radio-renderers',
)

await buildViteRenderers(
    { icon: 'tests/js/renderers/icon.svelte.js' },
    'build/js-icon-renderers',
)

await buildViteRenderers(
    { 'input-wrapper': 'tests/js/renderers/input-wrapper.svelte.js' },
    'build/js-input-wrapper-renderers',
)

await buildViteRenderers(
    { input: 'tests/js/renderers/input.svelte.js' },
    'build/js-input-renderers',
)

for (const component of [
    'input',
    'input-wrapper',
    'icon',
    'avatar',
    'breadcrumbs',
    'checkbox',
    'radio',
    'fieldset',
    'loading-indicator',
]) {
    const configuration = `tests/js/${component}.tsconfig.json`
    for (const [command, arguments_] of [
        ['node_modules/typescript/bin/tsc', ['--project', configuration]],
        ['node_modules/vue-tsc/bin/vue-tsc.js', ['--project', configuration]],
        [
            'node_modules/svelte-check/bin/svelte-check',
            ['--tsconfig', configuration, '--fail-on-warnings'],
        ],
    ]) {
        execFileSync(process.execPath, [command, ...arguments_], {
            stdio: 'inherit',
        })
    }
}

// Plugin authors can also bundle plain JavaScript and React without Vite.
const result = await build({
    entryPoints: [
        generatedEntryPoints['generated-js'],
        generatedEntryPoints['generated-react'],
    ],
    outdir: 'build/esbuild-renderer-smoke',
    bundle: true,
    format: 'esm',
    write: false,
    metafile: true,
    define: { 'process.env.NODE_ENV': '"production"' },
})
for (const output of Object.values(result.metafile.outputs)) {
    assert.ok(output.exports.includes('default'))
}
