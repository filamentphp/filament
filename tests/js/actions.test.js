import assert from 'node:assert/strict'
import { test } from 'node:test'
import { build } from 'esbuild'
import { createElement } from 'react'
import { renderToStaticMarkup } from 'react-dom/server'

const result = await build({
    entryPoints: ['packages/support/resources/js/react/Actions.tsx'],
    bundle: true,
    format: 'esm',
    write: false,
})
const { default: Actions } = await import(
    `data:text/javascript;base64,${Buffer.from(result.outputFiles[0].text).toString('base64')}`
)
const render = (props, children) =>
    renderToStaticMarkup(createElement(Actions, props, children))

test('actions matches Blade alignment normalization and full width precedence', () => {
    for (const alignment of [
        undefined,
        null,
        'start',
        'left',
        'center',
        'end',
        'right',
        'between',
        'justify',
        '',
        '  ',
        'custom-layout',
    ]) {
        const expected =
            alignment == null
                ? ' fi-align-start'
                : alignment.trim() === ''
                  ? ''
                  : alignment === 'custom-layout'
                    ? ' custom-layout'
                    : ` fi-align-${alignment}`
        assert.equal(
            render({ alignment }),
            `<div class="fi-ac${expected}"></div>`,
        )
        assert.equal(
            render({ alignment, fullWidth: true }),
            '<div class="fi-ac fi-width-full"></div>',
        )
    }
})

test('actions preserves native attributes and host-owned native children without adding semantics', () => {
    assert.equal(
        render(
            {
                className: 'custom',
                title: 'Actions',
                'aria-label': 'Project actions',
            },
            [
                createElement(
                    'button',
                    { key: 'save', type: 'submit', disabled: true },
                    'Save',
                ),
                createElement(
                    'a',
                    { key: 'link', href: '#projects' },
                    '<Projects>',
                ),
            ],
        ),
        '<div title="Actions" aria-label="Project actions" class="fi-ac fi-align-start custom"><button type="submit" disabled="">Save</button><a href="#projects">&lt;Projects&gt;</a></div>',
    )
})
