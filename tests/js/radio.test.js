import assert from 'node:assert/strict'
import { test } from 'node:test'
import { build } from 'esbuild'
import { createElement } from 'react'
import { renderToStaticMarkup } from 'react-dom/server'

const result = await build({
    entryPoints: ['packages/support/resources/js/react/Radio.tsx'],
    bundle: true,
    format: 'esm',
    write: false,
})
const { default: Radio } = await import(
    `data:text/javascript;base64,${Buffer.from(result.outputFiles[0].text).toString('base64')}`
)

test('radio defaults match Blade without a valid hook', () => {
    assert.equal(
        renderToStaticMarkup(createElement(Radio)),
        '<input type="radio" class="fi-radio-input"/>',
    )
})

test('radio preserves native attributes and forces its type', () => {
    const markup = renderToStaticMarkup(
        createElement(Radio, {
            type: 'checkbox',
            valid: false,
            defaultChecked: true,
            className: 'custom',
            name: 'delivery',
            value: '<express>',
            form: 'order',
            disabled: true,
            required: true,
        }),
    )
    for (const attribute of [
        'type="radio"',
        'class="fi-radio-input fi-invalid custom"',
        'name="delivery"',
        'value="&lt;express&gt;"',
        'form="order"',
        'disabled=""',
        'required=""',
        'checked=""',
    ]) {
        assert.ok(markup.includes(attribute), attribute)
    }
    assert.ok(!markup.includes('valid='))
})
