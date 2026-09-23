import assert from 'node:assert/strict'
import { test } from 'node:test'
import { build } from 'esbuild'
import { createElement } from 'react'
import { renderToStaticMarkup } from 'react-dom/server'

const result = await build({
    entryPoints: ['packages/support/resources/js/react/InputWrapper.tsx'],
    bundle: true,
    format: 'esm',
    write: false,
})
const { default: InputWrapper } = await import(
    `data:text/javascript;base64,${Buffer.from(result.outputFiles[0].text).toString('base64')}`
)
const render = (props) =>
    renderToStaticMarkup(
        createElement(
            InputWrapper,
            props,
            createElement('input', { required: true, name: 'amount' }),
        ),
    )

test('wrapper styling never supplies input validation or disabled semantics', () => {
    assert.equal(
        render({
            disabled: true,
            valid: false,
            className: 'custom',
            title: 'Amount',
            tabIndex: -1,
        }),
        '<div title="Amount" tabindex="-1" class="fi-input-wrp fi-disabled fi-invalid custom"><div class="fi-input-wrp-content-ctn"><input required="" name="amount"/></div></div>',
    )
    for (const prefix of [undefined, null, false, '', '  '])
        assert.equal(
            render({ prefix }),
            '<div class="fi-input-wrp"><div class="fi-input-wrp-content-ctn"><input required="" name="amount"/></div></div>',
        )
    assert.match(render({ prefix: 0 }), /fi-input-wrp-label">0<\/span>/)
})

test('affixes preserve Blade order, label flags, escaping and independent inline states', () => {
    assert.equal(
        render({
            prefix: '<£>',
            suffix: 'GBP',
            inlinePrefix: true,
            prefixIcon: createElement('i'),
            suffixIcon: createElement('b'),
            prefixActions: createElement(
                'button',
                { type: 'button' },
                'Before',
            ),
            suffixActions: createElement('button', { type: 'button' }, 'After'),
        }),
        '<div class="fi-input-wrp"><div class="fi-input-wrp-prefix fi-input-wrp-prefix-has-content fi-inline fi-input-wrp-prefix-has-label"><div class="fi-input-wrp-actions"><button type="button">Before</button></div><i></i><span class="fi-input-wrp-label">&lt;£&gt;</span></div><div class="fi-input-wrp-content-ctn"><input required="" name="amount"/></div><div class="fi-input-wrp-suffix fi-input-wrp-suffix-has-label"><span class="fi-input-wrp-label">GBP</span><b></b><div class="fi-input-wrp-actions"><button type="button">After</button></div></div></div>',
    )
    assert.equal(
        render({ suffixIcon: createElement('i'), inlineSuffix: true }),
        '<div class="fi-input-wrp"><div class="fi-input-wrp-content-ctn"><input required="" name="amount"/></div><div class="fi-input-wrp-suffix fi-inline"><i></i></div></div>',
    )
})
