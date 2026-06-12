import Chart from 'chart.js/auto'
import { color } from 'chart.js/helpers'
import 'chartjs-adapter-luxon'

const darken = (value, amount) => {
    if (Array.isArray(value)) {
        return value.map((entry) => darken(entry, amount))
    }

    const parsed = color(value)

    return parsed.valid ? parsed.darken(amount).rgbString() : value
}

if (
    window.filamentChartJsGlobalPlugins &&
    Array.isArray(window.filamentChartJsGlobalPlugins) &&
    window.filamentChartJsGlobalPlugins.length > 0
) {
    Chart.register(...window.filamentChartJsGlobalPlugins)
}

Chart.defaults.plugins.legend.labels.boxWidth = 12
Chart.defaults.plugins.legend.position = 'bottom'

export default function chart({ cachedData, options, type }) {
    return {
        userBackgroundColor: options?.backgroundColor,
        userBorderColor: options?.borderColor,
        userTextColor: options?.color,
        userPointBackgroundColor: options?.pointBackgroundColor,
        userXGridColor: options?.scales?.x?.grid?.color,
        userYGridColor: options?.scales?.y?.grid?.color,
        userRadialGridColor: options?.scales?.r?.grid?.color,
        userRadialTicksColor: options?.scales?.r?.ticks?.color,

        init() {
            this.$wire.$on('updateChartData', ({ data }) =>
                this.updateChartData(data),
            )

            Alpine.effect(() => {
                Alpine.store('theme')

                this.$nextTick(() => this.updateChartTheme())
            })

            this.systemThemeMediaQuery = window.matchMedia(
                '(prefers-color-scheme: dark)',
            )
            this.systemThemeListener = () => {
                if (Alpine.store('theme') !== 'system') {
                    return
                }

                this.$nextTick(() => this.updateChartTheme())
            }
            this.systemThemeMediaQuery.addEventListener(
                'change',
                this.systemThemeListener,
            )

            // Defer `initChart()` to `$nextTick` so the `Alpine.effect` above runs its
            // mandatory first invocation before the chart exists. `updateChartTheme()` then
            // exits early on that first run; otherwise the effect would tear down and
            // recreate the chart on every mount.
            this.$nextTick(() => {
                this.initChart()

                this.resizeObserver = new ResizeObserver(() =>
                    this.getChart()?.resize(),
                )
                this.resizeObserver.observe(this.$el)

                this.dprChangeHandler = Alpine.debounce(
                    () => this.handleDprChange(),
                    250,
                )
                window.addEventListener('resize', this.dprChangeHandler)
            })
        },

        initChart() {
            if (
                !this.$refs.canvas ||
                !this.$refs.backgroundColorElement ||
                !this.$refs.borderColorElement ||
                !this.$refs.textColorElement ||
                !this.$refs.gridColorElement
            ) {
                return
            }

            const fontFamily = getComputedStyle(this.$el).fontFamily
            const hasMaxHeight = this.$refs.canvas.style.maxHeight !== '100%'

            options ??= {}
            options.animation ??= {}
            options.animation.duration ??= 0
            options.font ??= {}
            options.font.family ??= fontFamily
            options.borderWidth ??= 2
            options.responsive ??= false
            options.maintainAspectRatio ??= hasMaxHeight
            options.pointHitRadius ??= 4
            options.pointRadius ??= 2
            options.scales ??= {}
            options.scales.x ??= {}
            options.scales.x.border ??= {}
            options.scales.x.border.display ??= false
            options.scales.x.grid ??= {}
            options.scales.x.grid.display ??= false
            options.scales.y ??= {}
            options.scales.y.border ??= {}
            options.scales.y.border.display ??= false
            options.scales.y.grid ??= {}

            if (['doughnut', 'pie', 'polarArea'].includes(type)) {
                options.scales.x.display ??= false
                options.scales.y.display ??= false
                options.scales.y.grid.display ??= false

                options.elements ??= {}
                options.elements.arc ??= {}

                options.plugins ??= {}
                options.plugins.legend ??= {}
                options.plugins.legend.labels ??= {}
                options.plugins.legend.labels.generateLabels ??= (chart) => {
                    const labels =
                        Chart.overrides[
                            type
                        ].plugins.legend.labels.generateLabels(chart)

                    for (const label of labels) {
                        label.strokeStyle = darken(label.fillStyle, 0.2)
                    }

                    return labels
                }
            }

            if (type === 'polarArea') {
                options.scales.r ??= {}
                options.scales.r.grid ??= {}
                options.scales.r.ticks ??= {}
                options.scales.r.ticks.backdropColor ??= 'transparent'
            }

            this.applyChartColors(options)
            this.normalizeDatasets(cachedData)

            new Chart(this.$refs.canvas, {
                type,
                data: cachedData,
                options,
                plugins: window.filamentChartJsPlugins ?? [],
            })
        },

        updateChartData(newData) {
            const chart = this.getChart()

            if (!chart) {
                return
            }

            this.normalizeDatasets(newData)
            chart.data = newData
            chart.update('resize')
        },

        normalizeDatasets(data) {
            for (const dataset of data?.datasets ?? []) {
                if (type === 'bar' && dataset.backgroundColor !== undefined) {
                    dataset.borderColor ??= darken(dataset.backgroundColor, 0.2)
                    dataset.hoverBorderColor ??= darken(
                        dataset.backgroundColor,
                        0.3,
                    )
                }

                if (
                    type === 'scatter' &&
                    dataset.backgroundColor !== undefined
                ) {
                    dataset.borderColor ??= dataset.backgroundColor
                }

                if (
                    ['line', 'scatter'].includes(type) &&
                    dataset.borderColor !== undefined
                ) {
                    dataset.pointBackgroundColor ??= dataset.borderColor
                }
            }
        },

        updateChartTheme() {
            const chart = this.getChart()

            if (!chart) {
                return
            }

            this.applyChartColors(chart.options)
            chart.update('none')
        },

        applyChartColors(options) {
            const { backgroundColor, borderColor, textColor, gridColor } =
                this.getChartColors()

            const resolvedBorderColor = this.userBorderColor ?? borderColor

            options.backgroundColor =
                this.userBackgroundColor ?? backgroundColor
            options.borderColor = resolvedBorderColor
            options.color = this.userTextColor ?? textColor
            options.pointBackgroundColor =
                this.userPointBackgroundColor ?? resolvedBorderColor

            options.elements ??= {}
            options.elements.bar ??= {}
            options.elements.bar.borderColor = resolvedBorderColor

            if (['doughnut', 'pie', 'polarArea'].includes(type)) {
                options.elements.arc ??= {}
                options.elements.arc.borderColor =
                    this.getSurroundingBackgroundColor()
            }

            options.scales.x.grid.color = this.userXGridColor ?? gridColor
            options.scales.y.grid.color = this.userYGridColor ?? gridColor

            if (type === 'polarArea') {
                options.scales.r.grid.color =
                    this.userRadialGridColor ?? gridColor
                options.scales.r.ticks.color =
                    this.userRadialTicksColor ?? textColor
            }
        },

        handleDprChange() {
            const chart = this.getChart()

            if (!chart) {
                return
            }

            if (chart.currentDevicePixelRatio !== window.devicePixelRatio) {
                chart.resize()
            }
        },

        getChart() {
            if (!this.$refs.canvas) {
                return null
            }

            return Chart.getChart(this.$refs.canvas)
        },

        getSurroundingBackgroundColor() {
            let element = this.$el.parentElement

            while (element) {
                const backgroundColor =
                    getComputedStyle(element).backgroundColor

                if (
                    backgroundColor &&
                    backgroundColor !== 'rgba(0, 0, 0, 0)' &&
                    backgroundColor !== 'transparent'
                ) {
                    return backgroundColor
                }

                element = element.parentElement
            }

            return '#ffffff'
        },

        getChartColors() {
            return {
                backgroundColor: getComputedStyle(
                    this.$refs.backgroundColorElement,
                ).color,
                borderColor: getComputedStyle(this.$refs.borderColorElement)
                    .color,
                textColor: getComputedStyle(this.$refs.textColorElement).color,
                gridColor: getComputedStyle(this.$refs.gridColorElement).color,
            }
        },

        destroy() {
            this.resizeObserver?.disconnect()
            this.dprChangeHandler &&
                window.removeEventListener('resize', this.dprChangeHandler)
            this.systemThemeMediaQuery?.removeEventListener(
                'change',
                this.systemThemeListener,
            )
            this.getChart()?.destroy()
        },
    }
}
