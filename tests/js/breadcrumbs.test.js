import assert from 'node:assert/strict'
import { test } from 'node:test'
import { build } from 'esbuild'
import { createElement } from 'react'
import { renderToStaticMarkup } from 'react-dom/server'

const result = await build({
    entryPoints: ['packages/support/resources/js/react/Breadcrumbs.tsx'],
    bundle: true,
    format: 'esm',
    write: false,
})
const { default: Breadcrumbs } = await import(
    `data:text/javascript;base64,${Buffer.from(result.outputFiles[0].text).toString('base64')}`
)

test('breadcrumbs default to an empty named navigation', () => {
    assert.equal(
        renderToStaticMarkup(createElement(Breadcrumbs)),
        '<nav aria-label="Breadcrumbs" class="fi-breadcrumbs"><ol class="fi-breadcrumbs-list"></ol></nav>',
    )
})

test('breadcrumbs escape labels, preserve entry order and mark only the last entry current', () => {
    const markup = renderToStaticMarkup(
        createElement(Breadcrumbs, {
            'aria-label': 'Location',
            className: 'custom',
            breadcrumbs: [
                { label: '<Home>', href: '#home', title: 'Home & dashboard' },
                { label: 'Category' },
                {
                    label: 'Current',
                    href: '#current',
                    target: '_blank',
                    rel: 'noopener',
                },
            ],
        }),
    )
    assert.match(markup, /aria-label="Location" class="fi-breadcrumbs custom"/)
    assert.match(
        markup,
        /<a href="#home" title="Home &amp; dashboard" class="fi-breadcrumbs-item-label">&lt;Home&gt;<\/a>/,
    )
    assert.match(
        markup,
        /<span class="fi-breadcrumbs-item-label">Category<\/span>/,
    )
    assert.match(
        markup,
        /<a href="#current" target="_blank" rel="noopener" aria-current="page" class="fi-breadcrumbs-item-label">Current<\/a>/,
    )
    assert.equal((markup.match(/aria-current/g) ?? []).length, 1)
    assert.equal((markup.match(/<svg/g) ?? []).length, 4)
    assert.ok(markup.indexOf('&lt;Home&gt;') < markup.indexOf('Category'))
    assert.ok(markup.indexOf('Category') < markup.indexOf('>Current<'))
})

test('single unlinked breadcrumbs have no separator and custom separators remain decorative', () => {
    const single = renderToStaticMarkup(
        createElement(Breadcrumbs, { breadcrumbs: [{ label: 'Home' }] }),
    )
    assert.match(
        single,
        /<span aria-current="page" class="fi-breadcrumbs-item-label">Home<\/span>/,
    )
    assert.ok(!single.includes('<svg'))
    const custom = renderToStaticMarkup(
        createElement(Breadcrumbs, {
            breadcrumbs: [{ label: 'Home' }, { label: 'Current' }],
            separator: '/',
            separatorRtl: '\\',
        }),
    )
    assert.ok(!custom.includes('<svg'))
    assert.match(
        custom,
        /<span aria-hidden="true" class="fi-icon fi-size-md fi-breadcrumbs-item-separator fi-ltr">\/<\/span>/,
    )
    assert.match(
        custom,
        /<span aria-hidden="true" class="fi-icon fi-size-md fi-breadcrumbs-item-separator fi-rtl">\\<\/span>/,
    )
})
