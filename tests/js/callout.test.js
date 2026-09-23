import assert from 'node:assert/strict'
import { test } from 'node:test'
import { build } from 'esbuild'
import { createElement } from 'react'
import { renderToStaticMarkup } from 'react-dom/server'

const result = await build({
    entryPoints: ['packages/support/resources/js/react/Callout.tsx'],
    bundle: true,
    format: 'esm',
    write: false,
})
const { default: Callout } = await import(
    `data:text/javascript;base64,${Buffer.from(result.outputFiles[0].text).toString('base64')}`
)
const render = (props, children) =>
    renderToStaticMarkup(createElement(Callout, props, children))

test('callout defaults to empty gray markup without icon or announcement role', () => {
    for (const value of [undefined, null, false, '', '  ']) {
        assert.equal(
            render({
                heading: value,
                description: value,
                footer: value,
                controls: value,
                icon: value,
            }),
            '<div class="fi-callout"></div>',
        )
    }
    assert.equal(
        render({ heading: '<Notice>', description: 0 }),
        '<div class="fi-callout"><div class="fi-callout-main"><div class="fi-callout-text"><h4 class="fi-callout-heading">&lt;Notice&gt;</h4><p class="fi-callout-description">0</p></div></div></div>',
    )
})
test('callout supports independent colors, rich regions, footer precedence and native attributes', () => {
    const html = render(
        {
            color: 'brand',
            iconColor: 'success',
            iconSize: 'sm',
            icon: createElement('svg'),
            heading: createElement('strong', null, 'Notice'),
            description: createElement('em', null, 'Review'),
            footer: createElement('button', null, 'Continue'),
            controls: createElement('a', { href: '#help' }, 'Help'),
            title: 'Notice',
            role: 'status',
            className: 'custom',
        },
        'Ignored',
    )
    assert.match(
        html,
        /role="status" class="fi-callout fi-color fi-color-brand custom"/,
    )
    assert.match(
        html,
        /aria-hidden="true" class="fi-icon fi-size-sm fi-callout-icon fi-color fi-color-success"/,
    )
    assert.match(
        html,
        /<h4 class="fi-callout-heading"><strong>Notice<\/strong>/,
    )
    assert.match(html, /<p class="fi-callout-description"><em>Review<\/em>/)
    assert.match(
        html,
        /<div class="fi-callout-footer"><button>Continue<\/button>/,
    )
    assert.match(
        html,
        /<div class="fi-callout-controls"><a href="#help">Help<\/a>/,
    )
    assert.doesNotMatch(html, /Ignored/)
    assert.match(
        render({ color: 'info', icon: '☆' }),
        /fi-callout-icon fi-color fi-color-info/,
    )
    assert.doesNotMatch(render({ icon: '☆' }), /fi-color/)
    assert.equal(
        render({}, 'Continue'),
        '<div class="fi-callout"><div class="fi-callout-main"><div class="fi-callout-footer">Continue</div></div></div>',
    )
})
