import assert from 'node:assert/strict'
import { test } from 'node:test'
import { build } from 'esbuild'
import { createElement } from 'react'
import { renderToStaticMarkup } from 'react-dom/server'

const result = await build({
    entryPoints: ['packages/support/resources/js/react/Fieldset.tsx'],
    bundle: true,
    format: 'esm',
    write: false,
})
const { default: Fieldset } = await import(
    `data:text/javascript;base64,${Buffer.from(result.outputFiles[0].text).toString('base64')}`
)

test('fieldset defaults and blank labels omit the legend and required marker', () => {
    for (const label of [undefined, null, '', '  ', false]) {
        assert.equal(
            renderToStaticMarkup(
                createElement(Fieldset, { label, required: true }, 'Content'),
            ),
            '<fieldset class="fi-fieldset">Content</fieldset>',
        )
    }
})

test('fieldset preserves native attributes, children, classes and escaped labels', () => {
    assert.equal(
        renderToStaticMarkup(
            createElement(
                Fieldset,
                {
                    label: '<Address>',
                    required: true,
                    labelHidden: true,
                    contained: false,
                    className: 'custom',
                    disabled: true,
                    name: 'address',
                    form: 'profile',
                },
                createElement('input', { 'aria-label': 'Street' }),
            ),
        ),
        '<fieldset disabled="" name="address" form="profile" class="fi-fieldset fi-fieldset-label-hidden fi-fieldset-not-contained custom"><legend>&lt;Address&gt;<sup class="fi-fieldset-label-required-mark">*</sup></legend><input aria-label="Street"/></fieldset>',
    )
})

test('fieldset supports rich and zero labels without adding a required marker by default', () => {
    assert.equal(
        renderToStaticMarkup(
            createElement(Fieldset, {
                label: createElement('strong', null, 'Address'),
            }),
        ),
        '<fieldset class="fi-fieldset"><legend><strong>Address</strong></legend></fieldset>',
    )
    assert.equal(
        renderToStaticMarkup(createElement(Fieldset, { label: '0' })),
        '<fieldset class="fi-fieldset"><legend>0</legend></fieldset>',
    )
})
