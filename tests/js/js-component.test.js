import { test } from 'node:test'
import assert from 'node:assert/strict'
import jsComponent from '../../packages/schemas/resources/js/components/js-component.js'

function fixture(renderer) {
    const component = jsComponent({
        renderer,
        configuration: { id: null },
        rendererProps: { message: 'Original', options: { enabled: false } },
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
        assert.deepEqual(Object.keys(props).sort(), ['config', 'id'])
        props.config.options.enabled = true
        return {
            update: (props) => updates.push(props),
            destroy: () => disposals++,
        }
    })
    await component.init()
    assert.equal(updates[0].config.options.enabled, false)
    const configuration = { message: 'Changed', options: { enabled: true } }
    component.updateRendererProps(configuration)
    configuration.message = 'Mutated outside'
    assert.equal(updates.at(-1).config.message, 'Changed')
    component.updateRendererProps({})
    assert.deepEqual(updates.at(-1).config, {})
    assert.equal(mounts, 1)
    component.destroy()
    component.destroy()
    component.updateRendererProps({ message: 'Too late' })
    assert.equal(disposals, 1)
    assert.deepEqual(updates.at(-1).config, {})
})

test('props received during async mounting are delivered and late mounts are disposed', async () => {
    for (const isRemoved of [false, true]) {
        let resolveMount
        let disposals = 0
        const updates = []
        const component = fixture(
            () =>
                new Promise((resolve) => {
                    resolveMount = resolve
                }),
        )
        const initialization = component.init()
        component.updateRendererProps({ message: 'While mounting' })
        if (isRemoved) component.destroy()
        resolveMount({
            update: (props) => updates.push(props.config),
            destroy: () => disposals++,
        })
        await initialization
        assert.deepEqual(
            updates,
            isRemoved ? [] : [{ message: 'While mounting' }],
        )
        assert.equal(disposals, isRemoved ? 1 : 0)
    }
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

test('import, mount, update and cleanup failures are contained', async (context) => {
    context.mock.method(console, 'error', () => {})
    for (const phase of ['import', 'mount', 'update', 'cleanup']) {
        let disposals = 0
        let cleared = false
        const component = fixture(
            phase === 'import'
                ? 'data:text/javascript,export default 123'
                : () => {
                      if (phase === 'mount') throw new Error('mount failed')
                      return {
                          update() {
                              if (phase === 'update')
                                  return Promise.reject(
                                      new Error('update failed'),
                                  )
                          },
                          destroy() {
                              disposals++
                              if (phase === 'cleanup')
                                  return Promise.reject(
                                      new Error('cleanup failed'),
                                  )
                          },
                      }
                  },
        )
        component.$refs.host.replaceChildren = () => {
            cleared = true
        }
        await component.init()
        await Promise.resolve()
        component.destroy()
        await Promise.resolve()
        assert.equal(component.hasError, phase !== 'cleanup')
        assert.equal(cleared, phase !== 'cleanup')
        assert.equal(disposals, ['update', 'cleanup'].includes(phase) ? 1 : 0)
    }
})
