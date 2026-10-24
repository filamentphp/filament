import { build } from 'esbuild'
import assert from 'node:assert/strict'
import { execFileSync } from 'node:child_process'
import { buildRendererStarters } from './build-renderer-starters.js'

const { widget: generatedEntryPoints } = await buildRendererStarters({
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
        'tests/js/js-widget-types.ts',
    ],
    { stdio: 'inherit' },
)

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
