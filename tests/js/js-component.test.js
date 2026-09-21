import { test } from 'node:test'
import assert from 'node:assert/strict'
import jsComponent from '../../packages/schemas/resources/js/components/js-component.js'

function fixture(renderer) {
    const component = jsComponent({
        renderer,
        componentConfiguration: { id: 'component-id' },
        rendererConfiguration: {
            id: 'custom-id',
            message: 'Original',
            options: { enabled: false },
        },
    })
    component.$refs = { host: { replaceChildren() {} } }
    component.$el = { ownerDocument: { baseURI: 'https://example.test/' } }
    return component
}

test('configuration updates are snapshots and do not remount or expose field callbacks', async () => {
    let mounts = 0
    let disposals = 0
    const updates = []
    const component = fixture(({ props }) => {
        mounts++
        assert.deepEqual(Object.keys(props).sort(), ['configuration', 'id'])
        assert.equal(props.id, 'component-id')
        assert.equal(props.configuration.id, 'custom-id')
        props.configuration.options.enabled = true
        return {
            update: (props) => updates.push(props),
            destroy: () => disposals++,
        }
    })
    await component.init()
    assert.equal(updates[0].configuration.options.enabled, false)
    const configuration = { message: 'Changed', options: { enabled: true } }
    component.updateRendererConfiguration(configuration)
    configuration.message = 'Mutated outside'
    assert.equal(updates.at(-1).configuration.message, 'Changed')
    component.updateRendererConfiguration({})
    assert.deepEqual(updates.at(-1).configuration, {})
    assert.equal(updates.at(-1).id, 'component-id')
    assert.equal(mounts, 1)
    component.destroy()
    component.destroy()
    component.updateRendererConfiguration({ message: 'Too late' })
    assert.equal(disposals, 1)
    assert.deepEqual(updates.at(-1).configuration, {})
})

test('PHP props received during async mounting are delivered', async () => {
    let resolveMount
    const updates = []
    const component = fixture(
        () =>
            new Promise((resolve) => {
                resolveMount = resolve
            }),
    )
    const initialization = component.init()
    component.updateRendererConfiguration({ message: 'While mounting' })
    resolveMount({
        update: (props) => updates.push(props.configuration),
        destroy() {},
    })
    await initialization
    assert.deepEqual(updates, [{ message: 'While mounting' }])
})

test('schema utilities retain getters and exposed method identity', async () => {
    let utilities
    const component = fixture((context) => {
        utilities = context.utilities
        return { update() {}, destroy() {} }
    })
    component.$wire = { name: 'Original' }
    component.$state = { country: 'GB' }
    component.$schemaComponentMethods = { $load: () => 'loaded' }
    component.$get = (path) => component.$state[path]
    await component.init()
    component.$wire = { name: 'Updated' }
    component.$state = { country: 'FR' }
    assert.equal(utilities.$wire, component.$wire)
    assert.equal(utilities.$state, component.$state)
    assert.equal(utilities.$get('country'), 'FR')
    assert.equal(utilities.$load(), 'loaded')
})

test('renderer failure clears the schema host and exposes its error state', async (context) => {
    context.mock.method(console, 'error', () => {})
    let cleared = false
    const component = fixture(() => ({
        update() {
            throw new Error('update failed')
        },
        destroy() {},
    }))
    component.$refs.host.replaceChildren = () => {
        cleared = true
    }
    await component.init()
    assert.equal(component.hasError, true)
    assert.equal(cleared, true)
})
