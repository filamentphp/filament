import assert from 'node:assert/strict'
import { test } from 'node:test'
import { execFileSync } from 'node:child_process'
import { build } from 'esbuild'
import { createElement } from 'react'
import { renderToStaticMarkup } from 'react-dom/server'

async function load(path) {
    const result = await build({
        entryPoints: [path],
        bundle: true,
        format: 'esm',
        write: false,
    })
    return import(
        `data:text/javascript;base64,${Buffer.from(result.outputFiles[0].text).toString('base64')}`
    )
}
const { default: LoadingSection } = await load(
    'packages/support/resources/js/react/LoadingSection.tsx',
)
const { getLoadingSectionLayout } = await load(
    'packages/support/resources/js/components/loading-section.ts',
)
const render = (props) =>
    renderToStaticMarkup(createElement(LoadingSection, props))

test('loading section matches Blade default semantics without spinner artwork', () => {
    assert.equal(
        render({}),
        '<div role="status" aria-busy="true" class="fi-section fi-loading-section fi-grid-col" style="height:8rem"><span class="fi-sr-only">Loading...</span></div>',
    )
    assert.equal(render({ height: null, loadingLabel: null }), render({}))
    assert.match(
        render({
            loadingLabel: '<Projects>',
            title: 'Projects',
            style: { height: '9rem' },
            'aria-busy': false,
            role: 'presentation',
        }),
        /role="presentation" aria-busy="false" title="Projects".*height:9rem.*&lt;Projects&gt;/,
    )
    assert.match(
        render({ loadingLabel: '' }),
        /<span class="fi-sr-only"><\/span>/,
    )
})

test('loading section normalizes scalar and responsive grid values like Blade', () => {
    assert.deepEqual(
        getLoadingSectionLayout({ columnSpan: 2, columnStart: 3 }),
        {
            className:
                'fi-section fi-loading-section fi-grid-col lg:fi-grid-col-span lg:fi-grid-col-start',
            style: {
                height: '8rem',
                '--col-span-lg': 'span 2 / span 2',
                '--col-start-lg': '3',
            },
        },
    )
    assert.deepEqual(
        getLoadingSectionLayout({
            columnSpan: {
                default: 'hidden',
                md: 'full',
                '@lg': 2,
                '!xl': 3,
                sm: 0,
            },
            columnStart: { default: 2, lg: null, xl: '0' },
            height: '14rem',
        }),
        {
            className:
                'fi-section fi-loading-section fi-grid-col fi-hidden md:fi-grid-col-span @lg:fi-grid-col-span !xl:fi-grid-col-span fi-grid-col-start',
            style: {
                height: '14rem',
                '--col-span-default': 'span hidden / span hidden',
                '--col-span-md': '1 / -1',
                '--col-span-clg': 'span 2 / span 2',
                '--col-span-nxl': 'span 3 / span 3',
                '--col-start-default': '2',
            },
        },
    )
    assert.deepEqual(
        getLoadingSectionLayout({ columnSpan: null, columnStart: '' }),
        getLoadingSectionLayout({}),
    )
})

test('column starts match PHP integer coercion and reject nonnumeric span keywords', () => {
    for (const columnStart of [NaN, Infinity, -Infinity]) {
        assert.throws(() => getLoadingSectionLayout({ columnStart }), TypeError)
    }
    const values = [
        '1e1',
        '2.9',
        '-2.9',
        '  +03  ',
        '.5',
        '0.0',
        2.9,
        0.5,
        0,
        '0',
        null,
        '',
        'full',
        'hidden',
        '0x10',
        ' ',
        'Infinity',
        '1e40',
        '\t3\n',
        '\u00a03',
        '3\u00a0',
    ]
    const expected = JSON.parse(
        execFileSync(
            'php',
            [
                '-r',
                `
        require 'vendor/autoload.php';
        $results = [];
        foreach (json_decode($argv[1]) as $value) {
            foreach ([$value, ['lg' => $value]] as $start) {
                try {
                    $results[] = (new Filament\\Support\\View\\ComponentAttributeBag)->gridColumn([], $start)->get('style', '');
                } catch (TypeError $exception) {
                    $results[] = 'TypeError';
                }
            }
        }
        echo json_encode($results);
    `,
                JSON.stringify(values),
            ],
            { encoding: 'utf8' },
        ),
    )
    // Check the independent PHP reference's significant coercions explicitly.
    assert.equal(expected[0], '--col-start-lg: 10;')
    assert.equal(expected[4], '--col-start-lg: -2;')
    assert.equal(expected[24], 'TypeError')
    values
        .flatMap((value) => [value, { lg: value }])
        .forEach((columnStart, index) => {
            if (expected[index] === 'TypeError') {
                assert.throws(
                    () => getLoadingSectionLayout({ columnStart }),
                    TypeError,
                )
            } else {
                const { style } = getLoadingSectionLayout({ columnStart })
                assert.equal(
                    style['--col-start-lg'] === undefined
                        ? ''
                        : `--col-start-lg: ${style['--col-start-lg']};`,
                    expected[index],
                )
            }
        })
})
