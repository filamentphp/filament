import { test } from 'node:test'
import assert from 'node:assert/strict'
import schemaComponent from '../../packages/schemas/resources/js/components/component.js'

test('named and general calls bind to each instance and preserve promises and failures', async () => {
    const calls = []
    const result = Promise.resolve('Café')
    const $wire = {
        callSchemaComponentMethod: (...parameters) => {
            calls.push(parameters)
            return result
        },
    }
    const first = schemaComponent({
        key: 'form.items.0.custom',
        exposedMethods: ['search'],
        $wire,
    })
    const second = schemaComponent({
        key: 'form.items.1.custom',
        exposedMethods: ['search'],
        $wire,
    })
    const { $search } = second
    assert.equal($search({ query: 'café', limit: 3 }), result)
    assert.equal(first.$search(), result)
    assert.equal(
        second.$callSchemaComponentMethod('other', { enabled: false }),
        result,
    )
    assert.deepEqual(calls, [
        ['form.items.1.custom', 'search', { query: 'café', limit: 3 }],
        ['form.items.0.custom', 'search', {}],
        ['form.items.1.custom', 'other', { enabled: false }],
    ])
    const failure = new Error('Rejected')
    $wire.callSchemaComponentMethod = () => Promise.reject(failure)
    await assert.rejects($search(), (error) => error === failure)
})

test('reserved names never override utilities or Alpine magics and remain callable by name', () => {
    const calls = []
    const component = schemaComponent({
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
            'callSchemaComponentMethod',
            'schemaComponentMethods',
            'search',
        ],
        $wire: {
            $get: (path) => path,
            $set: (...parameters) => calls.push(parameters),
            callSchemaComponentMethod: (...parameters) =>
                calls.push(parameters),
        },
    })
    assert.deepEqual(Object.keys(component.$schemaComponentMethods), [
        '$search',
    ])
    assert.equal(component.$state, 'data.items.1.title')
    assert.equal(component.$get('../0.title'), 'data.items.0.title')
    assert.equal(component.$get('../../caption'), 'data.caption')
    assert.equal(component.$get('/data.caption'), 'data.caption')
    assert.equal(component.$get('data.caption', true), 'data.caption')
    assert.equal(component.$get(''), 'data.items.1')
    component.$set('../0.title', false, false, true)
    component.$callSchemaComponentMethod('get', { path: 'remote' })
    assert.deepEqual(calls, [
        ['data.items.0.title', false, true],
        ['form.custom', 'get', { path: 'remote' }],
    ])
})
