import assert from 'node:assert/strict'
import { test } from 'node:test'
import { build } from 'esbuild'
import { createElement } from 'react'
import { renderToStaticMarkup } from 'react-dom/server'

const result = await build({
    entryPoints: ['packages/support/resources/js/react/Icon.tsx'],
    bundle: true,
    format: 'esm',
    write: false,
})
const { default: Icon } = await import(
    `data:text/javascript;base64,${Buffer.from(result.outputFiles[0].text).toString('base64')}`
)

test('icons are empty without content and wrap host-owned components without raw HTML', () => {
    assert.equal(renderToStaticMarkup(createElement(Icon)), '')
    const HostIcon = () =>
        createElement(
            'svg',
            { width: 24, height: 24, viewBox: '0 0 24 24' },
            createElement('path', { d: 'M4 12h16' }),
        )
    for (const size of ['xs', 'sm', 'md', 'lg', 'xl', '2xl']) {
        const markup = renderToStaticMarkup(
            createElement(
                Icon,
                {
                    size,
                    className: 'custom',
                    role: 'img',
                    'aria-label': '<Saved>',
                },
                createElement(HostIcon),
            ),
        )
        assert.equal(
            markup,
            `<span role="img" aria-label="&lt;Saved&gt;" class="fi-icon fi-size-${size} custom"><svg width="24" height="24" viewBox="0 0 24 24"><path d="M4 12h16"></path></svg></span>`,
        )
    }
})

test('image source takes precedence, defaults to decorative and forwards native attributes', () => {
    assert.equal(
        renderToStaticMarkup(
            createElement(
                Icon,
                { src: '/icon.svg', loading: 'lazy' },
                'ignored',
            ),
        ),
        '<img loading="lazy" class="fi-icon fi-size-md" src="/icon.svg" alt=""/>',
    )
    assert.equal(
        renderToStaticMarkup(
            createElement(Icon, {
                src: '/icon.svg',
                loading: 'lazy',
                alt: 'Saved',
                width: 32,
                'data-state': 'ready',
            }),
        ),
        '<img loading="lazy" width="32" data-state="ready" class="fi-icon fi-size-md" src="/icon.svg" alt="Saved"/>',
    )
})
