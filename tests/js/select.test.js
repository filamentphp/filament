import { after, before, test } from 'node:test'
import assert from 'node:assert/strict'
import { build } from 'esbuild'
import { chromium, expect } from 'playwright/test'

let browser
let source

before(async () => {
    source = (
        await build({
            entryPoints: ['packages/support/resources/js/utilities/select.js'],
            bundle: true,
            write: false,
            format: 'iife',
            globalName: 'FilamentSelect',
        })
    ).outputFiles[0].text
    browser = await chromium.launch()
})

after(async () => browser?.close())

const fallback = '<svg onload="window.fallbackMarker = true"></svg> & literal'

test('single fallback values remain text with absent, empty, or failing label lookups', async () => {
    const page = await browser.newPage()
    try {
        await page.setContent('<main></main>')
        await page.addScriptTag({ content: source })
        for (const lookup of ['absent', 'null', 'failure']) {
            await page.evaluate(
                ({ fallback, lookup }) => {
                    document.querySelector('main').replaceChildren()
                    const element = document.createElement('div')
                    document.querySelector('main').append(element)
                    new FilamentSelect.Select({
                        element,
                        ariaLabel: 'Single selection',
                        state: fallback,
                        initialState: fallback,
                        options: [],
                        isHtmlAllowed: true,
                        getOptionLabelUsing:
                            lookup === 'absent'
                                ? null
                                : async () => {
                                      if (lookup === 'failure')
                                          throw new Error('Lookup failed')
                                      return null
                                  },
                    })
                },
                { fallback, lookup },
            )
            await expect(page.getByRole('combobox')).toHaveText(fallback)
            assert.equal(
                await page.evaluate(() => window.fallbackMarker),
                undefined,
            )
            await expect(page.locator('main svg')).toHaveCount(0)
        }
    } finally {
        await page.close()
    }
})

test('multiple badges distinguish initial and fetched HTML labels from missing values and preserve accessible names', async () => {
    const page = await browser.newPage()
    try {
        await page.setContent('<main></main>')
        await page.addScriptTag({ content: source })
        for (const lookup of ['initial', 'fetch', 'failure', 'text']) {
            await page.evaluate(
                ({ fallback, lookup }) => {
                    document.querySelector('main').replaceChildren()
                    const element = document.createElement('div')
                    document.querySelector('main').append(element)
                    new FilamentSelect.Select({
                        element,
                        ariaLabel: 'Multiple selection',
                        state: ['known', fallback, 42],
                        initialState:
                            lookup === 'initial' ? ['known', fallback, 42] : [],
                        initialOptionLabels:
                            lookup === 'initial'
                                ? [
                                      {
                                          value: 'known',
                                          label: '<b>Dan &amp; Alex</b>',
                                      },
                                  ]
                                : [],
                        options: [],
                        isMultiple: true,
                        isHtmlAllowed: lookup !== 'text',
                        getOptionLabelsUsing: async () => {
                            if (lookup === 'failure')
                                throw new Error('Lookup failed')
                            return [
                                {
                                    value: 'known',
                                    label: '<b>Dan &amp; Alex</b>',
                                },
                            ]
                        },
                    })
                },
                { fallback, lookup },
            )
            await expect(
                page.getByRole('button', {
                    name: `Remove ${fallback}`,
                    exact: true,
                }),
            ).toBeAttached()
            await expect(
                page.getByRole('button', { name: 'Remove 42', exact: true }),
            ).toBeAttached()
            if (lookup === 'text') {
                await expect(page.locator('main b')).toHaveCount(0)
                await expect(
                    page.getByRole('button', {
                        name: 'Remove <b>Dan &amp; Alex</b>',
                        exact: true,
                    }),
                ).toBeAttached()
            } else if (lookup !== 'failure') {
                await expect(page.locator('main b')).toHaveText('Dan & Alex')
                await expect(
                    page.getByRole('button', {
                        name: 'Remove Dan & Alex',
                        exact: true,
                    }),
                ).toBeAttached()
            }
            assert.equal(
                await page.evaluate(() => window.fallbackMarker),
                undefined,
            )
            await expect(page.locator('main svg')).toHaveCount(0)
        }
    } finally {
        await page.close()
    }
})

test('a refreshed selection can lose its configured HTML label without trusting its value', async () => {
    const page = await browser.newPage()
    try {
        await page.setContent('<main></main>')
        await page.addScriptTag({ content: source })
        await page.evaluate((fallback) => {
            new FilamentSelect.Select({
                element: document.querySelector('main'),
                ariaLabel: 'Refreshed selection',
                state: fallback,
                initialState: fallback,
                initialOptionLabel: '<b>Initial &amp; trusted</b>',
                options: [],
                isHtmlAllowed: true,
                livewireId: 'select-owner',
                statePath: 'data.value',
                getOptionLabelUsing: async () => null,
            })
        }, fallback)
        await expect(page.locator('main b')).toHaveText('Initial & trusted')
        await page.evaluate(() =>
            window.dispatchEvent(
                new CustomEvent(
                    'filament-forms::select.refreshSelectedOptionLabel',
                    {
                        detail: {
                            livewireId: 'select-owner',
                            statePath: 'data.value',
                        },
                    },
                ),
            ),
        )
        await expect(page.getByRole('combobox')).toHaveText(fallback)
        assert.equal(
            await page.evaluate(() => window.fallbackMarker),
            undefined,
        )
        await expect(page.locator('main svg')).toHaveCount(0)
    } finally {
        await page.close()
    }
})
