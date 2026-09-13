import { test } from 'node:test'
import assert from 'node:assert/strict'
import jsWidget from '../../packages/widgets/resources/js/components/js-widget.js'

function fixture(renderer, rendererProps = {}) {
    const widget = jsWidget({ renderer, rendererProps })
    widget.$refs = { host: { replaceChildren() {} } }
    widget.$el = { ownerDocument: { baseURI: import.meta.url } }
    widget.$wire = { refreshTotal: () => 'refreshed' }
    return widget
}

test('PHP props update independently without remounting and preserve the owning `$wire`', async () => {
    let mounts = 0
    let disposals = 0
    let utilities
    const updates = []
    const widget = fixture(
        (context) => {
            mounts++
            utilities = context.utilities
            context.props.config.filters.startDate = 'mutated'
            return {
                update: (props) => updates.push(props.config),
                destroy: () => disposals++,
            }
        },
        { filters: { startDate: '2026-01-01' }, total: 19 },
    )
    await widget.init()
    assert.equal(updates[0].filters.startDate, '2026-01-01')
    const next = { filters: { startDate: null }, total: 0 }
    widget.updateRendererProps(next)
    next.total = 999
    assert.deepEqual(updates.at(-1), { filters: { startDate: null }, total: 0 })
    assert.equal(utilities.$wire, widget.$wire)
    assert.equal(utilities.$wire.refreshTotal(), 'refreshed')
    assert.equal(mounts, 1)
    widget.destroy()
    widget.destroy()
    widget.updateRendererProps({ total: 32 })
    assert.equal(disposals, 1)
    assert.equal(updates.length, 2)
})

test('props received during asynchronous mounting reach the mounted instance', async () => {
    let resolveMount
    let latest
    const widget = fixture(
        () =>
            new Promise((resolve) => {
                resolveMount = resolve
            }),
    )
    const initialization = widget.init()
    widget.updateRendererProps({ total: 17 })
    resolveMount({
        update: (props) => {
            latest = props.config
        },
        destroy() {},
    })
    await initialization
    assert.deepEqual(latest, { total: 17 })
})

test('a late asynchronous mount is disposed after removal without updating', async () => {
    let resolveMount
    let disposals = 0
    const widget = fixture(
        () =>
            new Promise((resolve) => {
                resolveMount = resolve
            }),
    )
    const initialization = widget.init()
    widget.destroy()
    resolveMount({
        update: () => assert.fail('updated after removal'),
        destroy: () => disposals++,
    })
    await initialization
    assert.equal(disposals, 1)
    assert.equal(widget.hasError, false)
})

for (const asynchronous of [false, true]) {
    test(`failed ${asynchronous ? 'asynchronous' : 'synchronous'} updates clear and dispose the renderer`, async () => {
        let disposals = 0
        let cleared = 0
        const errors = []
        const widget = fixture(() => ({
            update: () => {
                if (asynchronous)
                    return Promise.reject(new Error('update failed'))
                throw new Error('update failed')
            },
            destroy: () => disposals++,
        }))
        widget.reportError = (phase) => errors.push(phase)
        widget.$refs.host.replaceChildren = () => cleared++
        await widget.init()
        await Promise.resolve()
        widget.destroy()
        assert.equal(widget.hasError, true)
        assert.equal(disposals, 1)
        assert.equal(cleared, 1)
        assert.deepEqual(errors, ['update'])
    })
}

test('invalid renderer contracts and failed imports show a controlled error', async () => {
    for (const renderer of [
        () => ({}),
        './missing-widget-renderer.js',
        'data:text/javascript,export default 42',
    ]) {
        const widget = fixture(renderer)
        widget.reportError = () => {}
        await widget.init()
        assert.equal(widget.hasError, true)
    }
})
