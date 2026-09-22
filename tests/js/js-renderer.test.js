import { test } from 'node:test'
import assert from 'node:assert/strict'
import createJsRenderer, {
    snapshot,
} from '../../packages/support/resources/js/js-renderer.js'

function fixture(renderer) {
    const events = []
    const source = { config: { caption: 'Initial café', enabled: true } }
    const lifecycle = createJsRenderer({
        renderer,
        getBaseUrl: () => 'https://example.test/',
        getContext: () => {
            events.push('context')
            return { host: {}, props: snapshot(source), utilities: {} }
        },
        getProps: () => snapshot(source),
        onError: (phase, error) => events.push([phase, error.message]),
        onFailure: () => events.push('failure'),
        onDestroy: () => events.push('stop'),
    })
    return { lifecycle, source, events }
}

test('removal during import never constructs context or reports a late import rejection', async () => {
    for (const source of [
        'export default () => { throw new Error("Must not mount") }',
        'throw new Error("Late import failure")',
    ]) {
        const { lifecycle, events } = fixture(
            `data:text/javascript,${encodeURIComponent(source)}`,
        )
        const initialization = lifecycle.init()
        lifecycle.destroy()
        await initialization
        lifecycle.destroy()
        assert.deepEqual(events, ['stop'])
    }
})

test('late mounts are disposed once, while late mount rejections leave the removed host alone', async () => {
    for (const rejects of [false, true]) {
        let resolveMount, rejectMount
        let disposals = 0
        const { lifecycle, events } = fixture(
            () =>
                new Promise((resolve, reject) => {
                    resolveMount = resolve
                    rejectMount = reject
                }),
        )
        const initialization = lifecycle.init()
        lifecycle.destroy()
        if (rejects) {
            rejectMount(new Error('Late mount failure'))
        } else {
            resolveMount({
                update: () => assert.fail('Updated removed renderer'),
                destroy: () => disposals++,
            })
        }
        await initialization
        lifecycle.update()
        lifecycle.destroy()
        assert.equal(disposals, rejects ? 0 : 1)
        assert.deepEqual(events, ['context', 'stop'])
    }
})

test('async mounts receive the latest props without sharing mutable snapshots', async () => {
    let resolveMount
    let initialProps
    const updates = []
    const { lifecycle, source } = fixture(({ props }) => {
        initialProps = props
        return new Promise((resolve) => {
            resolveMount = resolve
        })
    })
    const initialization = lifecycle.init()
    source.config = { caption: 'Replacement', enabled: false }
    lifecycle.update()
    resolveMount({ update: (props) => updates.push(props), destroy() {} })
    await initialization
    assert.deepEqual(initialProps.config, {
        caption: 'Initial café',
        enabled: true,
    })
    assert.deepEqual(updates, [
        { config: { caption: 'Replacement', enabled: false } },
    ])
    updates[0].config.caption = 'Renderer mutation'
    assert.equal(source.config.caption, 'Replacement')
    lifecycle.destroy()
})

test('failure stops adapter work before diagnostics, disposal and host clearing, exactly once', async () => {
    let shouldFail = false
    const { lifecycle, events } = fixture(() => ({
        update() {
            if (shouldFail) throw new Error('Update failed')
        },
        destroy() {
            events.push('dispose')
            throw new Error('Cleanup failed')
        },
    }))
    await lifecycle.init()
    shouldFail = true
    lifecycle.update()
    lifecycle.update()
    lifecycle.destroy()
    assert.deepEqual(events, [
        'context',
        'stop',
        ['update', 'Update failed'],
        'dispose',
        ['cleanup', 'Cleanup failed'],
        'failure',
    ])
})

test('an update rejected after removal cannot clear or fail its former host', async () => {
    let rejectUpdate
    const { lifecycle, events } = fixture(() => ({
        update: () =>
            new Promise((resolve, reject) => {
                rejectUpdate = reject
            }),
        destroy: () => events.push('dispose'),
    }))
    await lifecycle.init()
    lifecycle.destroy()
    rejectUpdate(new Error('Late update failure'))
    await new Promise((resolve) => setImmediate(resolve))
    assert.deepEqual(events, ['context', 'stop', 'dispose'])
})

for (const [name, phase, renderer] of [
    [
        'import rejection',
        'import',
        'data:text/javascript,throw new Error("Import failed")',
    ],
    [
        'invalid default export',
        'import',
        'data:text/javascript,export default 42',
    ],
    [
        'mount rejection',
        'mount',
        () => Promise.reject(new Error('Mount failed')),
    ],
    ['invalid instance', 'mount', () => ({})],
]) {
    test(`${name} stops the renderer and reports its failure once`, async () => {
        const { lifecycle, events } = fixture(renderer)
        await lifecycle.init()
        lifecycle.update()
        lifecycle.destroy()
        assert.deepEqual(
            events.filter(Array.isArray).map(([phase]) => phase),
            [phase],
        )
        assert.equal(events.filter((event) => event === 'stop').length, 1)
        assert.equal(events.at(-1), 'failure')
    })
}

for (const phase of ['update', 'cleanup']) {
    for (const asynchronous of [false, true]) {
        test(`${asynchronous ? 'rejected' : 'thrown'} ${phase} errors dispose once`, async () => {
            let shouldFail = false
            let disposals = 0
            const fail = () => {
                const error = new Error('Renderer failed')
                if (asynchronous) return Promise.reject(error)
                throw error
            }
            const { lifecycle, events } = fixture(() => ({
                update() {
                    if (shouldFail && phase === 'update') return fail()
                },
                destroy() {
                    disposals++
                    if (phase === 'cleanup') return fail()
                },
            }))
            await lifecycle.init()
            shouldFail = true
            if (phase === 'update') lifecycle.update()
            else lifecycle.destroy()
            await new Promise((resolve) => setImmediate(resolve))
            lifecycle.update()
            lifecycle.destroy()
            assert.equal(disposals, 1)
            assert.equal(events.filter((event) => event === 'stop').length, 1)
            assert.deepEqual(events.filter(Array.isArray), [
                [phase, 'Renderer failed'],
            ])
            assert.equal(events.includes('failure'), phase === 'update')
        })
    }
}
