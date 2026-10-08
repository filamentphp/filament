import assert from 'node:assert/strict'
import test from 'node:test'
import { runInNewContext } from 'node:vm'
import { buildSync } from 'esbuild'

const source = buildSync({
    entryPoints: [
        'packages/notifications/resources/js/components/notification.js',
    ],
    bundle: true,
    write: false,
    format: 'iife',
    globalName: 'notificationModule',
}).outputFiles[0].text

function createHarness() {
    let factory
    let nextTimeout = 0
    let hooks = []
    const timers = new Map()
    const frames = []
    const events = []
    const effects = new Set()
    const alpine = {
        data(name, callback) {
            factory = callback
        },
        effect(callback) {
            effects.add(callback)
            callback()
            return callback
        },
        release(callback) {
            effects.delete(callback)
        },
        mutateDom(callback) {
            callback()
        },
    }

    runInNewContext(`${source}; notificationModule.default(Alpine)`, {
        Alpine: alpine,
        Livewire: {
            hook(name, callback) {
                hooks.push(callback)
                return () => {
                    hooks = hooks.filter((hook) => hook !== callback)
                }
            },
        },
        window: {
            getComputedStyle: () => ({
                display: 'block',
                transitionDuration: '0.3s',
                transitionTimingFunction: 'ease',
            }),
            dispatchEvent: (event) => events.push(event.detail.id),
        },
        CustomEvent: class {
            constructor(name, options) {
                this.detail = options.detail
            }
        },
        setTimeout(callback, delay) {
            timers.set(++nextTimeout, { callback, delay })
            return nextTimeout
        },
        clearTimeout: (timeout) => timers.delete(timeout),
        requestAnimationFrame: (callback) => frames.push(callback),
    })

    return {
        timers,
        frames,
        events,
        effects,
        get hookCount() {
            return hooks.length
        },
        create(id, duration = 'persistent') {
            const component = factory({ notification: { id, duration } })
            const observations = {
                measurements: 0,
                animations: [],
                finished: 0,
            }
            let top = 80
            let hovered = false
            const listeners = {}
            component.$el = {
                style: { setProperty() {} },
                matches: () => hovered,
                addEventListener: (name, callback) =>
                    (listeners[name] = callback),
                getBoundingClientRect() {
                    observations.measurements++
                    return { top }
                },
                getAnimations: () => [
                    { finish: () => observations.finished++ },
                ],
                animate: (keyframes) => observations.animations.push(keyframes),
                _x_toggleAndCascadeWithTransitions(element, value, show, hide) {
                    value ? show() : hide()
                },
            }
            component.init()
            return {
                component,
                observations,
                setTop: (value) => (top = value),
                hover: () => (hovered = true),
                leave: () => listeners.mouseleave(),
            }
        },
        commit(isNotifications = true) {
            const respond = []
            const succeed = []
            hooks.forEach((callback) =>
                callback({
                    component: {
                        snapshot: {
                            data: {
                                isFilamentNotificationsComponent:
                                    isNotifications,
                            },
                        },
                    },
                    respond: (callback) => respond.push(callback),
                    succeed: (callback) => succeed.push(callback),
                }),
            )
            return {
                respond: () => respond.forEach((callback) => callback()),
                succeed: () => succeed.forEach((callback) => callback({})),
            }
        },
        flushFrames() {
            frames.splice(0).forEach((callback) => callback())
        },
        fire(timeout) {
            const timer = timers.get(timeout)
            timers.delete(timeout)
            timer?.callback()
        },
    }
}

test('`close()` still dispatches dismissal if pagination destroys the notification', () => {
    const harness = createHarness()
    const { component } = harness.create('database-notification')
    component.close()
    const timeout = component.closeTimeout
    assert.equal(harness.timers.get(timeout).delay, 300)
    component.destroy()
    harness.fire(timeout)
    assert.deepEqual(harness.events, ['database-notification'])
    assert.equal(harness.hookCount, 0)
    assert.equal(harness.effects.size, 0)
})

test('`destroy()` cancels expiry and skips an already queued frame', () => {
    const harness = createHarness()
    const { component, observations } = harness.create('removed', 1000)
    harness.commit()
    component.destroy()
    harness.flushFrames()
    harness.fire(component.durationTimeout)
    assert.equal(observations.measurements, 0)
    assert.equal(harness.timers.size, 0)
    assert.deepEqual(harness.events, [])
    assert.equal(harness.hookCount, 0)
    assert.equal(harness.effects.size, 0)
})

for (const stage of ['before response', 'before success']) {
    test('`destroy()` guards callbacks registered ' + stage, () => {
        const harness = createHarness()
        const { component, observations } = harness.create('removed')
        const commit = harness.commit()
        harness.flushFrames()
        if (stage === 'before success') commit.respond()
        component.destroy()
        if (stage === 'before response') commit.respond()
        commit.succeed()
        assert.equal(observations.measurements, 1)
        assert.equal(observations.animations.length, 0)
        assert.equal(observations.finished, stage === 'before success' ? 1 : 0)
    })
}

test('`destroy()` releases and guards an already queued transition effect', () => {
    const harness = createHarness()
    const { component } = harness.create('removed')
    const runner = component.transitionEffect
    component.destroy()
    component.$el._x_toggleAndCascadeWithTransitions = () =>
        assert.fail('stale transition')
    runner()
    assert.equal(harness.effects.size, 0)
})

test('destroying one instance leaves another subscribed and repositioning', () => {
    const harness = createHarness()
    const removed = harness.create('removed')
    const survivor = harness.create('survivor', 1000)
    removed.component.destroy()
    assert.equal(harness.hookCount, 1)
    assert.equal(harness.effects.size, 1)
    harness.commit(false)
    assert.equal(harness.frames.length, 0)
    const commit = harness.commit()
    harness.flushFrames()
    commit.respond()
    survivor.setTop(35)
    commit.succeed()
    assert.equal(
        survivor.observations.animations[0][0].transform,
        'translateY(45px)',
    )
    assert.equal(removed.observations.measurements, 0)
    harness.fire(survivor.component.durationTimeout)
    harness.fire(survivor.component.closeTimeout)
    assert.deepEqual(harness.events, ['survivor'])
})

test('hover expiry and manual `close()` produce only one dismissal', () => {
    const harness = createHarness()
    const { component, hover, leave } = harness.create('hovered', 1000)
    hover()
    harness.fire(component.durationTimeout)
    assert.equal(component.isShown, true)
    leave()
    const supersededTimeout = component.closeTimeout
    component.close()
    harness.fire(supersededTimeout)
    assert.deepEqual(harness.events, [])
    harness.fire(component.closeTimeout)
    assert.deepEqual(harness.events, ['hovered'])
})

test('manual `close()` cancels automatic expiry and persistent notifications have no expiry', () => {
    const harness = createHarness()
    const persistent = harness.create('persistent')
    assert.equal(persistent.component.durationTimeout, null)
    const { component } = harness.create('timed', 1000)
    component.close()
    assert.equal(harness.timers.has(component.durationTimeout), false)
    harness.fire(component.durationTimeout)
    harness.fire(component.closeTimeout)
    assert.deepEqual(harness.events, ['timed'])
})
