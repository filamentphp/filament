import { test } from 'node:test'
import assert from 'node:assert/strict'
import jsField from '../../packages/forms/resources/js/components/js-field.js'
import schemaComponent from '../../packages/schemas/resources/js/components/component.js'

async function fixture(configuration = {}, renderer) {
    let callbacks
    let watcher
    let commits = 0
    let disposals = 0
    const updates = []
    const field = jsField({
        state: { title: 'Original', enabled: true },
        configuration,
        renderer:
            renderer ??
            (({ host, props }) => {
                callbacks = props
                return {
                    update: (props) => updates.push(props.value),
                    destroy: () => disposals++,
                }
            }),
    })
    field.$refs = { host: {} }
    field.$el = { ownerDocument: { baseURI: 'https://example.test/form' } }
    field.$wire = { $commit: () => commits++ }
    field.$watch = (key, callback) => {
        watcher = callback
    }
    await field.init()

    return {
        field,
        callbacks,
        updates,
        get commits() {
            return commits
        },
        get disposals() {
            return disposals
        },
        flush: () => watcher(field.state),
    }
}

test('deferred edits and server updates are snapshots, not feedback loops', async () => {
    const result = await fixture()
    const value = { title: '', enabled: false }
    result.callbacks.onChange(value)
    value.title = 'Mutated outside'
    result.flush()
    assert.deepEqual(result.field.state, { title: '', enabled: false })
    result.field.state = null
    result.flush()
    assert.equal(result.updates.at(-1), null)
    assert.equal(result.commits, 0)
    const updateCount = result.updates.length
    result.field.destroy()
    result.callbacks.onChange({ title: 'Late callback' })
    result.flush()
    assert.equal(result.updates.length, updateCount)
    assert.equal(result.field.state, null)
    assert.equal(result.disposals, 1)
})

test('debounce commits only the latest edit and cancels on server replacement and disposal', async (context) => {
    context.mock.timers.enable({ apis: ['setTimeout'] })
    const result = await fixture({ isLive: true, debounce: 300 })
    result.callbacks.onChange({ title: 'First', enabled: false })
    result.flush()
    context.mock.timers.tick(200)
    result.callbacks.onChange({ title: 'Latest', enabled: true })
    result.flush()
    context.mock.timers.tick(299)
    assert.equal(result.commits, 0)
    context.mock.timers.tick(1)
    assert.equal(result.commits, 1)

    result.callbacks.onChange({ title: 'Pending', enabled: false })
    result.flush()
    result.field.state = { title: 'Server reset', enabled: true }
    result.flush()
    context.mock.timers.tick(300)
    assert.equal(result.commits, 1)
    result.callbacks.onChange({ title: 'Removed', enabled: false })
    result.field.destroy()
    context.mock.timers.tick(300)
    assert.equal(result.commits, 1)
})

test('blur commits once and unchanged values never commit', async () => {
    const result = await fixture({ isLive: true, isLiveOnBlur: true })
    result.callbacks.onChange({ title: 'Changed', enabled: false })
    result.flush()
    assert.equal(result.commits, 0)
    result.callbacks.onBlur()
    result.callbacks.onBlur()
    result.callbacks.onChange({ title: 'Changed', enabled: false })
    result.callbacks.onBlur()
    assert.equal(result.commits, 1)
})

test('conditionally disabled blur binding stays deferred', async () => {
    const result = await fixture({ isLive: false, isLiveOnBlur: true })
    result.callbacks.onChange({ title: 'Deferred blur', enabled: false })
    result.flush()
    result.callbacks.onBlur()
    assert.deepEqual(result.field.state, {
        title: 'Deferred blur',
        enabled: false,
    })
    assert.equal(result.commits, 0)
})

test('a removed repeater path cancels pending edits before Alpine disposes the host', async (context) => {
    context.mock.timers.enable({ apis: ['setTimeout'] })
    const result = await fixture({ isLive: true, debounce: 300 })
    result.callbacks.onChange({ title: 'Pending', enabled: false })
    result.flush()
    const updateCount = result.updates.length
    result.field.state = undefined
    result.flush()
    context.mock.timers.tick(300)
    assert.equal(result.updates.length, updateCount)
    assert.equal(result.commits, 0)
    result.field.destroy()
    assert.equal(result.disposals, 1)
})

for (const property of ['disabled', 'readOnly']) {
    test(`${property} fields reject renderer writes`, async () => {
        const result = await fixture({ [property]: true, isLive: true })
        result.callbacks.onChange('Forbidden')
        assert.deepEqual(result.field.state, {
            title: 'Original',
            enabled: true,
        })
        assert.equal(result.commits, 0)
    })
}

test('a renderer resolving after removal is disposed without an update', async () => {
    let resolve
    let disposals = 0
    const field = jsField({
        state: null,
        configuration: {},
        renderer: () =>
            new Promise((callback) => {
                resolve = callback
            }),
    })
    field.$refs = { host: {} }
    field.$watch = () => {}
    const initialization = field.init()
    field.destroy()
    resolve({
        update: () => assert.fail('Updated after removal'),
        destroy: () => disposals++,
    })
    await initialization
    assert.equal(disposals, 1)
})

test('loads a module default export and updates it with current state', async () => {
    const result = await fixture(
        {},
        `data:text/javascript,${encodeURIComponent(`
        export default function ({ host, props }) {
            host.initial = props.value
            return {
                update: (props) => host.value = props.value,
                destroy: () => host.removed = true,
            }
        }
    `)}`,
    )
    assert.deepEqual(result.field.$refs.host.initial, {
        title: 'Original',
        enabled: true,
    })
    result.field.state = ['server', 'replacement']
    result.flush()
    assert.deepEqual(result.field.$refs.host.value, ['server', 'replacement'])
    result.field.destroy()
    assert.equal(result.field.$refs.host.removed, true)
})

test('does not mount a module when removed while the import is pending', async () => {
    const renderer = `data:text/javascript,${encodeURIComponent(`
        export let mounts = 0
        export default () => {
            mounts++
            return { update() {}, destroy() {} }
        }
    `)}`
    const field = jsField({
        state: null,
        configuration: {},
        renderer,
    })
    field.$el = { ownerDocument: { baseURI: 'https://example.test/form' } }
    field.$refs = { host: {} }
    field.$watch = () => {}
    const initialization = field.init()
    field.destroy()
    await initialization
    const module = await import(renderer)
    assert.equal(module.mounts, 0)
    const mounted = await fixture({}, renderer)
    assert.equal(module.mounts, 1)
    mounted.field.destroy()
})

test('passes through schema utilities and reads current scope state', async () => {
    let utilities
    const field = jsField({
        state: 'Initial',
        configuration: {},
        renderer: ({ utilities: context }) => {
            utilities = context
            return { update() {}, destroy() {} }
        },
    })
    const calls = []
    field.$get = (...args) => {
        calls.push(args)
        return 'Sibling'
    }
    field.$set = (...args) => {
        calls.push(args)
        return Promise.resolve()
    }
    field.$statePath = 'data.items.first.title'
    field.$state = 'Initial'
    field.$refs = { host: {} }
    field.$watch = () => {}
    await field.init()

    assert.equal(utilities.$get, field.$get)
    assert.equal(utilities.$set, field.$set)
    const { $get, $set } = utilities
    assert.equal($get('../country', false), 'Sibling')
    await $set('data.total', 17, true, true)
    assert.deepEqual(calls, [
        ['../country', false],
        ['data.total', 17, true, true],
    ])
    assert.equal(utilities.$statePath, 'data.items.first.title')
    assert.equal(utilities.$state, 'Initial')
    field.$state = 'Server replacement'
    assert.equal(utilities.$state, 'Server replacement')
})

test('binds component calls to the schema key and preserves `$wire`, promises and errors', async () => {
    let utilities
    const field = jsField({
        state: null,
        configuration: {},
        renderer: (context) => {
            utilities = context.utilities
            return { update() {}, destroy() {} }
        },
    })
    const calls = []
    const result = Promise.resolve({ title: 'Returned café' })
    field.$wire = {
        $get: () => null,
        callSchemaComponentMethod(...parameters) {
            calls.push(parameters)
            return result
        },
    }
    Object.assign(
        field,
        schemaComponent({
            key: 'form.items.second.custom-key',
            exposedMethods: ['search'],
            $wire: field.$wire,
        }),
    )
    field.$statePath = 'data.items.second.title'
    field.$refs = { host: {} }
    field.$watch = () => {}
    await field.init()
    assert.equal(utilities.$wire, field.$wire)
    assert.equal(utilities.$search, field.$schemaComponentMethods.$search)
    assert.equal(
        utilities.$callSchemaComponentMethod,
        field.$callSchemaComponentMethod,
    )
    assert.equal(
        typeof Object.getOwnPropertyDescriptor(utilities, '$wire').get,
        'function',
    )
    const { $callSchemaComponentMethod } = utilities
    assert.equal(
        $callSchemaComponentMethod('search', { query: 'café' }),
        result,
    )
    await $callSchemaComponentMethod('refreshOptions')
    assert.deepEqual(calls, [
        ['form.items.second.custom-key', 'search', { query: 'café' }],
        ['form.items.second.custom-key', 'refreshOptions', {}],
    ])
    const failure = new Error('Request failed')
    field.$wire.callSchemaComponentMethod = () => Promise.reject(failure)
    await assert.rejects(
        $callSchemaComponentMethod('search'),
        (error) => error === failure,
    )
})

test('updates PHP props without remounting or mutating state, including during initialization', async () => {
    let resolve
    let mounts = 0
    const updates = []
    const field = jsField({
        state: { title: 'Draft' },
        configuration: { disabled: false },
        rendererProps: { price: 24, obsolete: true },
        renderer: ({ props }) => {
            mounts++
            assert.equal(props.config.price, 24)
            return new Promise((callback) => {
                resolve = callback
            })
        },
    })
    field.$refs = { host: {} }
    field.$watch = () => {}
    const initialization = field.init()
    const next = { price: 18, disabled: true, value: 'Not field state' }
    field.updateRendererProps(next)
    next.price = 999
    resolve({ update: (props) => updates.push(props), destroy() {} })
    await initialization
    assert.equal(updates.at(-1).config.price, 18)
    assert.equal(updates.at(-1).config.obsolete, undefined)
    assert.equal(updates.at(-1).disabled, false)
    assert.deepEqual(updates.at(-1).value, { title: 'Draft' })
    field.updateRendererProps({ price: 30 })
    assert.equal(updates.at(-1).config.price, 30)
    assert.equal(mounts, 1)
    field.destroy()
    const count = updates.length
    field.updateRendererProps({ price: 45 })
    assert.equal(updates.length, count)
})

for (const [name, renderer] of [
    ['import failure', 'data:text/javascript,throw new Error("Import failed")'],
    ['invalid default export', 'data:text/javascript,export default 42'],
    [
        'mount failure',
        () => {
            throw new Error('Mount failed')
        },
    ],
    ['invalid return', () => ({})],
]) {
    test(`reports ${name} without leaving a partial field`, async (context) => {
        const diagnostics = context.mock.method(console, 'error', () => {})
        let cleared = false
        const field = jsField({
            state: 'Keep this',
            configuration: {},
            renderer,
        })
        field.$el = { ownerDocument: { baseURI: 'https://example.test/' } }
        field.$refs = {
            host: {
                replaceChildren: () => {
                    cleared = true
                },
            },
        }
        field.$watch = () => {}
        field.$statePath = 'data.items.second.title'
        await field.init()
        assert.equal(field.hasError, true)
        assert.equal(cleared, true)
        assert.equal(field.state, 'Keep this')
        assert.equal(diagnostics.mock.callCount(), 1)
        assert.equal(
            diagnostics.mock.calls[0].arguments[0],
            `JS field ${name.startsWith('import') || name.startsWith('invalid default') ? 'import' : 'mount'} failed:`,
        )
        assert.deepEqual(diagnostics.mock.calls[0].arguments[1], {
            statePath: 'data.items.second.title',
            renderer,
        })
        field.destroy()
    })
}

for (const phase of ['update', 'cleanup']) {
    for (const asynchronous of [false, true]) {
        test(`reports ${asynchronous ? 'rejected' : 'thrown'} ${phase} errors with field context and disposes once`, async (context) => {
            const diagnostics = context.mock.method(console, 'error', () => {})
            let shouldFail = false
            let disposals = 0
            const failure = new Error('Renderer failed')
            const fail = () => {
                if (asynchronous) return Promise.reject(failure)
                throw failure
            }
            const result = await fixture({}, () => ({
                update: () => {
                    if (shouldFail && phase === 'update') return fail()
                },
                destroy: () => {
                    disposals++
                    if (phase === 'cleanup') return fail()
                },
            }))
            result.field.$statePath = 'data.details'
            result.field.$refs.host.replaceChildren = () => {}
            shouldFail = true
            if (phase === 'update') {
                result.field.state = { title: 'Preserve this', enabled: false }
                result.flush()
                await new Promise((resolve) => setImmediate(resolve))
                assert.equal(result.field.hasError, true)
                assert.deepEqual(result.field.state, {
                    title: 'Preserve this',
                    enabled: false,
                })
            }
            result.field.destroy()
            result.field.destroy()
            await new Promise((resolve) => setImmediate(resolve))
            assert.equal(disposals, 1)
            assert.equal(diagnostics.mock.callCount(), 1)
            assert.equal(
                diagnostics.mock.calls[0].arguments[0],
                `JS field ${phase} failed:`,
            )
            assert.equal(
                diagnostics.mock.calls[0].arguments[1].statePath,
                'data.details',
            )
            assert.equal(diagnostics.mock.calls[0].arguments[2], failure)
        })
    }
}
