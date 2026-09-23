import assert from 'node:assert/strict'
import { test } from 'node:test'
import { build } from 'esbuild'
import { createElement } from 'react'
import { renderToStaticMarkup } from 'react-dom/server'

await build({
    entryPoints: ['packages/support/resources/js/react/Badge.tsx'],
    bundle: true,
    format: 'esm',
    packages: 'external',
    outfile: 'build/badge-test.mjs',
})
const { default: Badge } = await import('../../build/badge-test.mjs')
const render = (props) =>
    renderToStaticMarkup(createElement(Badge, props, 'Priority <projects>'))

test('badge renders escaped content with Blade hooks and registered color names', () => {
    assert.equal(
        render({}),
        '<span class="fi-badge fi-size-md fi-color fi-color-primary fi-text-color-700 dark:fi-text-color-200"><span class="fi-badge-label-ctn"><span class="fi-badge-label">Priority &lt;projects&gt;</span></span></span>',
    )
    assert.match(
        render({ color: 'brand', size: 'xs', title: 'Projects' }),
        /title="Projects" class="fi-badge fi-size-xs fi-color fi-color-brand fi-text-color-700 dark:fi-text-color-200"/,
    )
    assert.doesNotMatch(render({ color: 'gray' }), /fi-color/)
})
test('badge loading replaces only the decorative icon and respects delete precedence', () => {
    const icon = createElement('svg', { 'data-icon': 'star' })
    assert.match(
        render({ icon, iconPosition: 'after' }),
        /<\/span><span aria-hidden="true" class="fi-icon fi-size-sm"/,
    )
    assert.doesNotMatch(render({ icon, loading: true }), /data-icon/)
    assert.match(
        render({ icon, loading: true }),
        /aria-busy="true".*fi-loading-indicator/,
    )
    const deletion = render({
        icon,
        iconPosition: 'after',
        onDelete: () => {},
        deleteLoading: true,
        deleteLabel: 'Remove priority',
    })
    assert.doesNotMatch(deletion, /data-icon/)
    assert.match(deletion, /disabled="" aria-busy="true".*fi-size-xs/)
    assert.match(deletion, /fi-sr-only">Remove priority/)
    assert.match(
        render({ onDelete: () => {}, deleteLabel: '   ' }),
        /fi-sr-only">Delete/,
    )
    assert.throws(
        () => render({ tag: 'a', onDelete: () => {} }),
        /Deletable badges/,
    )
})
test('badge blocks native activation without losing disabled tooltip focusability', () => {
    assert.doesNotMatch(
        render({ tag: 'a', href: '/projects', disabled: true }),
        /href=/,
    )
    assert.match(
        render({
            tag: 'button',
            type: 'submit',
            form: 'projects',
            disabled: true,
        }),
        /form="projects" type="submit" disabled=""/,
    )
    const tooltip = render({
        tag: 'button',
        disabled: true,
        tooltip: '<b>Locked</b>',
    })
    assert.doesNotMatch(tooltip, / disabled=/)
    assert.match(
        tooltip,
        /aria-disabled="true" tabindex="0" style="pointer-events:auto"/,
    )
})
