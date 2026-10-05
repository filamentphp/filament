import assert from 'node:assert/strict'
import { fileURLToPath } from 'node:url'
import { test } from 'node:test'
import { build } from 'esbuild'

const { outputFiles } = await build({
    stdin: {
        contents: `
            export { default } from './packages/forms/resources/js/components/select.js'
            export { mergeProxies } from 'alpinejs/src/scope.js'
        `,
        resolveDir: fileURLToPath(new URL('../../../', import.meta.url)),
    },
    bundle: true,
    format: 'esm',
    write: false,
    plugins: [
        {
            name: 'mock-choices',
            setup(build) {
                build.onResolve({ filter: /^choices\.js$/ }, () => ({
                    path: 'choices',
                    namespace: 'mock-choices',
                }))
                build.onLoad(
                    { filter: /.*/, namespace: 'mock-choices' },
                    () => ({
                        contents: `export default class Choices {
                        setChoiceByValue() {}
                        destroy() { this.destroyed = true }
                    }`,
                    }),
                )
            },
        },
    ],
})

const { default: selectFormComponent, mergeProxies } = await import(
    `data:text/javascript;base64,${Buffer.from(outputFiles[0].text).toString('base64')}`
)

const eventName = 'filament-forms::select.refreshSelectedOptionLabel'

function createSelect(
    { isMultiple = false, statePath = 'data.first' } = {},
    parent = {},
) {
    const refreshes = []
    const component = selectFormComponent({
        isMultiple,
        livewireId: 'component',
        statePath,
        state: isMultiple ? [] : 'selected',
    })

    Object.assign(component, {
        $refs: { input: new EventTarget() },
        $watch() {},
        refreshPlaceholder() {},
        async refreshChoices(options) {
            refreshes.push(options)
        },
    })

    return { component: mergeProxies([component, parent]), refreshes }
}

function dispatchRefresh(livewireId, statePath) {
    const event = new Event(eventName)
    event.detail = { livewireId, statePath }
    window.dispatchEvent(event)
}

test('refreshes only the matching select and stops refreshing after destruction', async () => {
    globalThis.window = new EventTarget()
    const { component, refreshes } = createSelect()
    await component.init()
    refreshes.length = 0

    dispatchRefresh('other-component', 'data.first')
    dispatchRefresh('component', 'data.other')
    assert.equal(refreshes.length, 0)

    dispatchRefresh('component', 'data.first')
    assert.deepEqual(refreshes, [{ withInitialOptions: false }])

    const choices = component.select
    component.destroy()
    assert.equal(choices.destroyed, true)
    assert.equal(component.select, null)

    dispatchRefresh('component', 'data.first')
    assert.equal(refreshes.length, 1)
})

test('cleans up independent selects sharing an Alpine parent scope across repeated mounts', async () => {
    globalThis.window = new EventTarget()
    const parent = {}

    for (let visit = 0; visit < 3; visit++) {
        const first = createSelect({}, parent)
        const second = createSelect({ statePath: 'data.second' }, parent)
        await Promise.all([first.component.init(), second.component.init()])
        first.refreshes.length = 0
        second.refreshes.length = 0

        first.component.destroy()
        dispatchRefresh('component', 'data.first')
        dispatchRefresh('component', 'data.second')
        assert.equal(first.refreshes.length, 0)
        assert.equal(second.refreshes.length, 1)

        second.component.destroy()
        dispatchRefresh('component', 'data.second')
        assert.equal(second.refreshes.length, 1)
    }
})

test('does not register a refresh listener for a multiple select', async () => {
    globalThis.window = new EventTarget()
    const { component, refreshes } = createSelect({ isMultiple: true })
    await component.init()
    refreshes.length = 0

    dispatchRefresh('component', 'data.first')
    assert.equal(refreshes.length, 0)

    component.destroy()
})
