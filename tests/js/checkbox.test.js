import assert from 'node:assert/strict'
import { test } from 'node:test'
import { build } from 'esbuild'
import { createElement } from 'react'
import { renderToStaticMarkup } from 'react-dom/server'

const result = await build({
    entryPoints: ['packages/support/resources/js/react/Checkbox.tsx'],
    bundle: true,
    format: 'esm',
    write: false,
})
const { default: Checkbox } = await import(
    `data:text/javascript;base64,${Buffer.from(result.outputFiles[0].text).toString('base64')}`
)

test('checkbox defaults to a native unchecked, valid input', () => {
    assert.equal(
        renderToStaticMarkup(createElement(Checkbox)),
        '<input type="checkbox" class="fi-checkbox-input fi-valid"/>',
    )
})

test('checkbox preserves native form attributes and initial checked state', () => {
    const markup = renderToStaticMarkup(
        createElement(Checkbox, {
            valid: false,
            defaultChecked: true,
            className: 'custom',
            name: 'permission',
            value: '<admin>',
            form: 'profile',
            disabled: true,
            required: true,
        }),
    )
    for (const attribute of [
        'type="checkbox"',
        'class="fi-checkbox-input fi-invalid custom"',
        'name="permission"',
        'value="&lt;admin&gt;"',
        'form="profile"',
        'disabled=""',
        'required=""',
        'checked=""',
    ])
        assert.ok(markup.includes(attribute), attribute)
    assert.ok(!markup.includes('valid='))
})
