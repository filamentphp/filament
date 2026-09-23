import assert from 'node:assert/strict'
import { test } from 'node:test'
import { build } from 'esbuild'
import { createElement } from 'react'
import { renderToStaticMarkup } from 'react-dom/server'

const result = await build({
    entryPoints: ['packages/support/resources/js/react/Input.tsx'],
    bundle: true,
    format: 'esm',
    write: false,
})
const { default: Input } = await import(
    `data:text/javascript;base64,${Buffer.from(result.outputFiles[0].text).toString('base64')}`
)
const render = (props) => renderToStaticMarkup(createElement(Input, props))

test('Input preserves native defaults, zero and empty values without adding wrapper semantics', () => {
    assert.equal(render({}), '<input class="fi-input"/>')
    assert.equal(
        render({ type: 'number', defaultValue: 0, min: 0, readOnly: true }),
        '<input type="number" min="0" readOnly="" class="fi-input" value="0"/>',
    )
    assert.equal(
        render({
            defaultValue: '',
            disabled: true,
            inlinePrefix: true,
            inlineSuffix: true,
            className: 'custom',
            'aria-label': 'Price',
        }),
        '<input disabled="" aria-label="Price" class="fi-input fi-input-has-inline-prefix fi-input-has-inline-suffix custom" value=""/>',
    )
    assert.match(
        render({
            type: 'email',
            required: true,
            name: 'email',
            defaultValue: 'a&b@example.com',
        }),
        /value="a&amp;b@example.com"/,
    )
})
