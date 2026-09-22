import assert from 'node:assert/strict'
import { test } from 'node:test'
import { readFileSync } from 'node:fs'
import { build } from 'esbuild'
import { createElement } from 'react'
import { renderToStaticMarkup } from 'react-dom/server'

const result = await build({
    entryPoints: ['packages/support/resources/js/react/LoadingIndicator.tsx'],
    bundle: true,
    format: 'esm',
    write: false,
})
const { default: LoadingIndicator } = await import(
    `data:text/javascript;base64,${Buffer.from(result.outputFiles[0].text).toString('base64')}`
)

test('loading indicator renders the default PHP paths and decorative SVG attributes', () => {
    const markup = renderToStaticMarkup(createElement(LoadingIndicator))
    assert.match(
        markup,
        /fill="none" viewBox="0 0 24 24" xmlns="http:\/\/www.w3.org\/2000\/svg" aria-hidden="true" class="fi-icon fi-loading-indicator fi-size-md"/,
    )
    const source = readFileSync(
        'packages/support/src/View/DefaultLoadingIndicator.php',
        'utf8',
    )
    const paths = [...source.matchAll(/<path\s+([\s\S]*?)><\/path>/g)].map(
        ([, attributes]) =>
            `<path ${attributes.trim().replace(/\s+/g, ' ')}></path>`,
    )
    assert.equal(paths.length, 2)
    assert.ok(markup.endsWith(`${paths.join('')}</svg>`))
})

test('loading indicator sizes and native overrides preserve its paths and hooks', () => {
    for (const size of ['xs', 'sm', 'md', 'lg', 'xl', '2xl']) {
        const markup = renderToStaticMarkup(
            createElement(LoadingIndicator, {
                size,
                className: 'custom',
                'aria-hidden': false,
                viewBox: '0 0 32 32',
                fill: 'red',
                'data-state': '<waiting>',
            }),
        )
        assert.ok(
            markup.includes(
                `class="fi-icon fi-loading-indicator fi-size-${size} custom"`,
            ),
        )
        assert.match(markup, /aria-hidden="false"/)
        assert.match(markup, /viewBox="0 0 32 32"/)
        assert.match(markup, /fill="red"/)
        assert.match(markup, /data-state="&lt;waiting&gt;"/)
        assert.equal((markup.match(/<path /g) ?? []).length, 2)
    }
})
