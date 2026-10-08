import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'
import { test } from 'node:test'
import { runInNewContext } from 'node:vm'
import { transformSync } from 'esbuild'

const { code } = transformSync(
    readFileSync('packages/widgets/resources/js/components/chart.js', 'utf8'),
    { format: 'cjs' },
)

function mountChart() {
    const charts = new Map()
    const effects = new Set()
    const queuedEffects = []
    const nextTicks = []

    class ListenerTarget extends EventTarget {
        listeners = new Set()

        addEventListener(name, listener) {
            this.listeners.add(listener)
            super.addEventListener(name, listener)
        }

        removeEventListener(name, listener) {
            this.listeners.delete(listener)
            super.removeEventListener(name, listener)
        }
    }

    const chartDataEventTarget = new ListenerTarget()
    const themeMediaQuery = new ListenerTarget()
    let theme = 'light'
    let effectRuns = 0

    class Chart {
        static defaults = {
            animation: {},
            font: {},
            plugins: { legend: { labels: {} } },
        }

        static getChart(canvas) {
            return charts.get(canvas)
        }

        constructor(canvas, configuration) {
            this.canvas = canvas
            this.data = configuration.data
            this.updates = []
            this.destroyed = false
            charts.set(canvas, this)
        }

        update(mode) {
            assert.equal(this.destroyed, false)
            this.updates.push(mode)
        }

        destroy() {
            this.destroyed = true
            charts.delete(this.canvas)
        }
    }

    const Alpine = {
        effect(callback) {
            const runner = () => {
                effectRuns++
                callback()
            }
            effects.add(runner)
            runner()
            return runner
        },
        release(runner) {
            // Like Alpine, releasing an effect does not dequeue its runner.
            effects.delete(runner)
        },
        store() {
            return theme
        },
    }

    const module = { exports: {} }
    runInNewContext(code, {
        module,
        exports: module.exports,
        require: (name) => (name === 'chart.js/auto' ? Chart : {}),
        Alpine,
        window: { matchMedia: () => themeMediaQuery },
        getComputedStyle: () => ({
            color: '#123456',
            fontFamily: 'sans-serif',
        }),
    })

    const refs = {
        canvas: {},
        backgroundColorElement: {},
        borderColorElement: {},
        textColorElement: {},
        gridColorElement: {},
    }
    const createComponent = () => {
        const component = module.exports.default({
            cachedData: { labels: ['January', 'February'], datasets: [] },
            type: 'bar',
        })
        Object.assign(component, {
            $wire: { $el: chartDataEventTarget },
            $refs: refs,
            $el: {},
            $nextTick: (callback) => nextTicks.push(callback),
        })
        component.init()
        return component
    }
    const flushNextTicks = () => {
        while (nextTicks.length) nextTicks.shift()()
    }

    const component = createComponent()
    flushNextTicks()

    return {
        component,
        charts,
        effects,
        chartDataEventTarget,
        themeMediaQuery,
        createComponent,
        flushNextTicks,
        flushNextTick: () => nextTicks.shift()(),
        get effectRuns() {
            return effectRuns
        },
        queueTheme(value) {
            theme = value
            queuedEffects.push(...effects)
        },
        flushEffects() {
            while (queuedEffects.length) queuedEffects.shift()()
        },
        dispatchData(data) {
            chartDataEventTarget.dispatchEvent(
                new CustomEvent('updateChartData', { detail: { data } }),
            )
        },
    }
}

test('updates live data and ignores a missing chart', () => {
    const fixture = mountChart()
    const chart = fixture.component.getChart()
    const data = {
        labels: ['March', 'April'],
        datasets: [{ data: [9, 4] }],
    }
    fixture.dispatchData(data)
    assert.equal(chart.data, data)
    assert.deepEqual(chart.updates, ['resize'])

    chart.destroy()
    assert.doesNotThrow(() => fixture.dispatchData(data))
    fixture.component.destroy()
})

test('removes root and media listeners and releases the effect on repeated child replacement', () => {
    const fixture = mountChart()
    let component = fixture.component

    for (let cycle = 0; cycle < 3; cycle++) {
        const chart = component.getChart()
        const originalData = chart.data
        component.destroy()
        assert.equal(chart.destroyed, true)
        assert.equal(fixture.charts.size, 0)
        assert.equal(fixture.effects.size, 0)
        assert.equal(fixture.chartDataEventTarget.listeners.size, 0)
        assert.equal(fixture.themeMediaQuery.listeners.size, 0)

        component = fixture.createComponent()
        fixture.flushNextTicks()
        fixture.dispatchData({ datasets: [{ data: [2, 7] }] })
        assert.equal(chart.data, originalData)

        fixture.queueTheme('system')
        fixture.flushEffects()
        fixture.flushNextTicks()
        const replacementChart = component.getChart()
        fixture.themeMediaQuery.dispatchEvent(new Event('change'))
        fixture.flushNextTicks()
        assert.equal(replacementChart.destroyed, true)
        assert.equal(fixture.charts.size, 1)
    }

    component.destroy()
    const effectRuns = fixture.effectRuns
    fixture.queueTheme('dark')
    fixture.flushEffects()
    fixture.themeMediaQuery.dispatchEvent(new Event('change'))
    fixture.flushNextTicks()
    assert.equal(fixture.effectRuns, effectRuns)
    assert.equal(fixture.charts.size, 0)
})

test('queued effect runners cannot rebuild a replacement chart after destruction', () => {
    const fixture = mountChart()
    fixture.queueTheme('dark')
    fixture.component.destroy()
    const replacement = fixture.createComponent()
    fixture.flushNextTicks()
    const chart = replacement.getChart()

    fixture.flushEffects()
    fixture.flushNextTicks()
    assert.equal(replacement.getChart(), chart)
    assert.equal(chart.destroyed, false)
    replacement.destroy()
})

test('queued theme and media next-tick callbacks cannot touch a replacement chart', () => {
    const fixture = mountChart()
    fixture.queueTheme('system')
    fixture.flushEffects()
    fixture.themeMediaQuery.dispatchEvent(new Event('change'))
    fixture.component.destroy()
    const replacement = fixture.createComponent()
    const chart = replacement.getChart()

    // The old callbacks run before the replacement's initial theme callback.
    fixture.flushNextTick()
    fixture.flushNextTick()
    assert.equal(replacement.getChart(), chart)
    assert.equal(chart.destroyed, false)

    fixture.flushNextTicks()
    assert.equal(chart.destroyed, true)
    assert.equal(fixture.charts.size, 1)
    assert.equal(fixture.effectRuns, 3)
    replacement.destroy()
})

test('ignores system color-scheme changes when an explicit theme is selected', () => {
    const fixture = mountChart()
    const chart = fixture.component.getChart()
    fixture.themeMediaQuery.dispatchEvent(new Event('change'))
    fixture.flushNextTicks()
    assert.equal(fixture.component.getChart(), chart)
    fixture.component.destroy()
})
