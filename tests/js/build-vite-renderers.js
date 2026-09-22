import assert from 'node:assert/strict'
import { resolve } from 'node:path'
import { build } from 'vite'
import vue from '@vitejs/plugin-vue'
import { svelte } from '@sveltejs/vite-plugin-svelte'

export async function buildViteRenderers(
    entries,
    outDir = 'build/js-renderer-starters',
) {
    const result = await build({
        configFile: false,
        plugins: [vue(), svelte()],
        css: { postcss: { plugins: [] } },
        build: {
            outDir,
            rolldownOptions: {
                input: entries,
                preserveEntrySignatures: 'exports-only',
                output: {
                    entryFileNames: '[name].js',
                    chunkFileNames: 'chunks/[name]-[hash].js',
                    assetFileNames: '[name][extname]',
                },
            },
        },
    })

    const chunks = result.output.filter((output) => output.type === 'chunk')
    for (const [name, entry] of Object.entries(entries)) {
        const chunk = chunks.find(
            (chunk) => chunk.facadeModuleId === resolve(entry),
        )
        assert.ok(chunk?.isEntry, `${name} must produce an entry chunk`)
        assert.ok(
            chunk.exports.includes('default'),
            `${name} must export its renderer`,
        )
    }
}
