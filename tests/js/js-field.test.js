import { test } from 'node:test'
import assert from 'node:assert/strict'
import jsField from '../../packages/forms/resources/js/components/js-field.js'
import schemaComponent from '../../packages/schemas/resources/js/components/component.js'

async function fixture(fieldConfiguration = {}, renderer) {
    let callbacks
    let watcher
    let commits = 0
    let disposals = 0
    const updates = []
    const field = jsField({
        state: { title: 'Original', enabled: true },
        fieldConfiguration,
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

test('renderer failure cancels a pending field commit and rejects late input callbacks', async (context) => {
    context.mock.timers.enable({ apis: ['setTimeout'] })
    context.mock.method(console, 'error', () => {})
    let callbacks
    let shouldFail = false
    const result = await fixture(
        { isLive: true, debounce: 300 },
        ({ props }) => {
            callbacks = props
            return {
                update() {
                    if (shouldFail) throw new Error('Update failed')
                },
                destroy() {},
            }
        },
    )
    result.field.$refs.host.replaceChildren = () => {}
    callbacks.onChange({ title: 'Pending', enabled: false })
    shouldFail = true
    result.flush()
    context.mock.timers.tick(300)
    callbacks.onChange({ title: 'Late callback', enabled: true })
    assert.equal(result.field.hasError, true)
    assert.equal(result.commits, 0)
    assert.deepEqual(result.field.state, { title: 'Pending', enabled: false })
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

for (const property of ['isDisabled', 'isReadOnly']) {
    test(`${property} updates cancel pending commits and reject writes without disposal`, async (context) => {
        context.mock.timers.enable({ apis: ['setTimeout'] })
        const result = await fixture({ isLive: true, debounce: 300 })
        result.callbacks.onChange({ title: 'Pending', enabled: false })
        result.field.updateRendererConfiguration({}, { [property]: true })
        context.mock.timers.tick(300)
        result.callbacks.onChange('Forbidden')
        assert.deepEqual(result.field.state, {
            title: 'Pending',
            enabled: false,
        })
        assert.equal(result.commits, 0)
        assert.equal(result.disposals, 0)
        result.field.updateRendererConfiguration({}, { [property]: false })
        result.callbacks.onChange('Allowed')
        context.mock.timers.tick(300)
        assert.equal(result.field.state, 'Allowed')
        assert.equal(result.commits, 1)
        result.field.destroy()
    })
}

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

test('passes through schema utilities and reads current scope state', async () => {
    let utilities
    const field = jsField({
        state: 'Initial',
        fieldConfiguration: {},
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
        fieldConfiguration: {},
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

test('updates renderer configuration without remounting or mutating state, including during initialization', async () => {
    let resolve
    let mounts = 0
    const updates = []
    const field = jsField({
        state: { title: 'Draft' },
        fieldConfiguration: { isDisabled: false },
        rendererConfiguration: { price: 24, obsolete: true },
        renderer: ({ props }) => {
            mounts++
            assert.equal(props.configuration.price, 24)
            return new Promise((callback) => {
                resolve = callback
            })
        },
    })
    field.$refs = { host: {} }
    field.$watch = () => {}
    const initialization = field.init()
    const next = { price: 18, isDisabled: true, value: 'Not field state' }
    field.updateRendererConfiguration(next, {
        isInvalid: true,
        isRequired: true,
    })
    next.price = 999
    resolve({ update: (props) => updates.push(props), destroy() {} })
    await initialization
    assert.equal(updates.at(-1).configuration.price, 18)
    assert.equal(updates.at(-1).configuration.obsolete, undefined)
    assert.equal(updates.at(-1).isDisabled, false)
    assert.equal(updates.at(-1).isInvalid, true)
    assert.equal(updates.at(-1).isRequired, true)
    assert.deepEqual(updates.at(-1).value, { title: 'Draft' })
    field.updateRendererConfiguration(
        { price: 30 },
        { isInvalid: false, isRequired: false },
    )
    assert.equal(updates.at(-1).configuration.price, 30)
    assert.equal(updates.at(-1).isInvalid, false)
    assert.equal(updates.at(-1).isRequired, false)
    assert.equal(mounts, 1)
    field.destroy()
    const count = updates.length
    field.updateRendererConfiguration({ price: 45 })
    assert.equal(updates.length, count)
})

test('reports initialization failure with field context without losing state', async (context) => {
    const renderer = 'data:text/javascript,throw new Error("Import failed")'
    const diagnostics = context.mock.method(console, 'error', () => {})
    let cleared = false
    const field = jsField({
        state: 'Keep this',
        fieldConfiguration: {},
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
        'JS field import failed:',
    )
    assert.deepEqual(diagnostics.mock.calls[0].arguments[1], {
        statePath: 'data.items.second.title',
        renderer,
    })
    field.destroy()
})
