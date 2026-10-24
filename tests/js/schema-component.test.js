import { test } from 'node:test'
import assert from 'node:assert/strict'
import schemaComponent from '../../packages/schemas/resources/js/components/component.js'

test('named and general calls bind to each instance and preserve promises and failures', async (context) => {
    const originalLivewire = globalThis.Livewire
    context.after(() => {
        globalThis.Livewire = originalLivewire
    })
    const calls = []
    const metadata = []
    const result = Promise.resolve('Café')
    const $wire = { __instance: {} }
    globalThis.Livewire = {
        fireAction: (component, method, parameters, options) => {
            assert.equal(component, $wire.__instance)
            assert.equal(method, 'callSchemaComponentMethod')
            calls.push(parameters)
            metadata.push(options)
            return result
        },
    }
    const first = schemaComponent.call(
        { $wire },
        {
            key: 'form.items.0.custom',
            exposedMethods: ['search'],
        },
    )
    const second = schemaComponent.call(
        { $wire },
        {
            key: 'form.items.1.custom',
            exposedMethods: ['search'],
        },
    )
    const { $search } = second
    assert.equal($search({ query: 'café', limit: 3 }), result)
    assert.equal(first.$search(), result)
    assert.equal(
        second.$callSchemaComponentMethod('other', { enabled: false }),
        result,
    )
    assert.equal(
        second.$callSchemaComponentMethod(
            'search',
            { query: 'async' },
            { async: true },
        ),
        result,
    )
    assert.deepEqual(calls, [
        ['form.items.1.custom', 'search', { query: 'café', limit: 3 }],
        ['form.items.0.custom', 'search', {}],
        ['form.items.1.custom', 'other', { enabled: false }],
        ['form.items.1.custom', 'search', { query: 'async' }],
    ])
    assert.deepEqual(metadata, [{}, {}, {}, { async: true }])
    const failure = new Error('Rejected')
    globalThis.Livewire.fireAction = () => Promise.reject(failure)
    await assert.rejects($search(), (error) => error === failure)
})

test('reserved names never override utilities or Alpine magics and remain callable by name', (context) => {
    const originalLivewire = globalThis.Livewire
    context.after(() => {
        globalThis.Livewire = originalLivewire
    })
    const calls = []
    globalThis.Livewire = {
        fireAction: (component, method, parameters) => calls.push(parameters),
    }
    const component = schemaComponent.call(
        {
            $wire: {
                $get: (path) => path,
                $set: (...parameters) => calls.push(parameters),
            },
        },
        {
            key: 'form.custom',
            path: 'data.items.1.title',
            containerPath: 'data.items.1',
            exposedMethods: [
                'get',
                'set',
                'state',
                'statePath',
                'wire',
                'el',
                'refs',
                'store',
                'watch',
                'dispatch',
                'nextTick',
                'root',
                'data',
                'id',
                'event',
                'focus',
                'persist',
                'tooltip',
                'js',
                'anchor',
                'queryString',
                'callSchemaComponentMethod',
                'schemaComponentMethods',
                'search',
            ],
        },
    )
    assert.deepEqual(Object.keys(component.$schemaComponentMethods), [
        '$search',
    ])
    const { $get, $set } = component
    assert.equal(component.$state, 'data.items.1.title')
    assert.equal($get('../0.title'), 'data.items.0.title')
    assert.equal(component.$get('../../caption'), 'data.caption')
    assert.equal(component.$get('/data.caption'), 'data.caption')
    assert.equal(component.$get('data.caption', true), 'data.caption')
    assert.equal(component.$get(''), 'data.items.1')
    $set('../0.title', false, false, true)
    component.$callSchemaComponentMethod('get', { path: 'remote' })
    assert.deepEqual(calls, [
        ['data.items.0.title', false, true],
        ['form.custom', 'get', { path: 'remote' }],
    ])
})
