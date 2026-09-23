import assert from 'node:assert/strict'
import { test } from 'node:test'
import { build } from 'esbuild'
import { createElement } from 'react'
import { renderToStaticMarkup } from 'react-dom/server'
const result = await build({
    entryPoints: ['packages/support/resources/js/react/Select.tsx'],
    bundle: true,
    format: 'esm',
    write: false,
})
const { default: Select } = await import(
    `data:text/javascript;base64,${Buffer.from(result.outputFiles[0].text).toString('base64')}`
)
test('Select preserves native options, defaults and Blade hooks', () => {
    assert.equal(
        renderToStaticMarkup(createElement(Select)),
        '<select class="fi-select-input"></select>',
    )
    const markup = renderToStaticMarkup(
        createElement(
            Select,
            {
                multiple: true,
                defaultValue: ['0', 'archived'],
                inlinePrefix: true,
                className: 'custom',
                name: 'workshops',
                required: true,
            },
            [
                createElement('option', { key: 'empty', value: '' }, 'Choose'),
                createElement(
                    'option',
                    { key: 'zero', value: '0' },
                    'Free & open',
                ),
                createElement(
                    'option',
                    { key: 'archived', value: 'archived', disabled: true },
                    'Archived',
                ),
            ],
        ),
    )
    assert.match(
        markup,
        /class="fi-select-input fi-select-input-has-inline-prefix custom"/,
    )
    assert.match(
        markup,
        /<option value="0" selected="">Free &amp; open<\/option>/,
    )
    assert.match(
        markup,
        /<option value="archived" disabled="" selected="">Archived<\/option>/,
    )
    assert.match(markup, /<option value="">Choose<\/option>/)
})
