import assert from 'node:assert/strict'
import { test } from 'node:test'
import { build } from 'esbuild'
import { createElement } from 'react'
import { renderToStaticMarkup } from 'react-dom/server'

const result = await build({
    entryPoints: ['packages/support/resources/js/react/EmptyState.tsx'],
    bundle: true,
    format: 'esm',
    write: false,
})
const { default: EmptyState } = await import(
    `data:text/javascript;base64,${Buffer.from(result.outputFiles[0].text).toString('base64')}`
)
const render = (props, children) =>
    renderToStaticMarkup(createElement(EmptyState, props, children))

test('empty state omits blank optional text but escapes meaningful text', () => {
    for (const description of [undefined, null, false, '', '  ']) {
        assert.equal(
            render({ heading: '<Projects>', description, footer: description }),
            '<div class="fi-empty-state"><div class="fi-empty-state-content"><div class="fi-empty-state-text-ctn"><h2 class="fi-empty-state-heading">&lt;Projects&gt;</h2></div></div></div>',
        )
    }
    assert.match(
        render({ description: 0 }),
        /<p class="fi-empty-state-description">0<\/p>/,
    )
})

test('empty state renders rich content, native attributes, icon options and footer precedence', () => {
    const html = render(
        {
            compact: true,
            contained: false,
            headingTag: 'h3',
            heading: createElement('strong', null, 'Projects'),
            description: createElement('em', null, 'Start here'),
            footer: createElement('a', { href: '#create' }, 'Create'),
            icon: createElement('svg', { 'aria-hidden': true }),
            iconColor: 'gray',
            iconSize: 'sm',
            title: 'Projects',
            className: 'custom',
        },
        'Ignored',
    )
    assert.match(
        html,
        /title="Projects" class="fi-empty-state fi-compact fi-empty-state-not-contained custom"/,
    )
    assert.match(
        html,
        /class="fi-empty-state-icon-bg"><span class="fi-icon fi-size-sm"/,
    )
    assert.match(
        html,
        /<h3 class="fi-empty-state-heading"><strong>Projects<\/strong><\/h3>/,
    )
    assert.match(
        html,
        /<p class="fi-empty-state-description"><em>Start here<\/em><\/p>/,
    )
    assert.match(
        html,
        /<footer class="fi-empty-state-footer"><a href="#create">Create<\/a><\/footer>/,
    )
    assert.doesNotMatch(html, /Ignored|fi-color/)
    assert.match(
        render({ icon: '☆' }, createElement('button', null, 'Create')),
        /fi-icon fi-size-lg fi-color fi-color-primary/,
    )
    assert.match(
        render({}, 'Create'),
        /<footer class="fi-empty-state-footer">Create<\/footer>/,
    )
})
