import Chart from 'chart.js/auto'
import 'chartjs-adapter-luxon'

export default function chart({ cachedData, options, type }) {
    return {
        isDestroyed: false,

        chartDataEventTarget: null,

        chartDataListener: null,

        themeEffect: null,

        themeMediaQuery: null,

        themeMediaQueryChangeHandler: null,

        init: function () {
            this.initChart()

            this.chartDataEventTarget = this.$wire.$el
            this.chartDataListener = ({ detail: { data } }) => {
                const chart = this.getChart()
                if (!chart) {
                    return
                }

                chart.data = data
                chart.update('resize')
            }
            this.chartDataEventTarget.addEventListener(
                'updateChartData',
                this.chartDataListener,
            )

            this.themeEffect = Alpine.effect(() => {
                if (this.isDestroyed) {
                    return
                }

                Alpine.store('theme')

                this.$nextTick(() => {
                    if (this.isDestroyed || !this.getChart()) {
                        return
                    }

                    this.getChart().destroy()
                    this.initChart()
                })
            })

            this.themeMediaQuery = window.matchMedia(
                '(prefers-color-scheme: dark)',
            )
            this.themeMediaQueryChangeHandler = () => {
                if (Alpine.store('theme') !== 'system') {
                    return
                }

                this.$nextTick(() => {
                    if (this.isDestroyed || !this.getChart()) {
                        return
                    }

                    this.getChart().destroy()
                    this.initChart()
                })
            }
            this.themeMediaQuery.addEventListener(
                'change',
                this.themeMediaQueryChangeHandler,
            )
        },

        destroy: function () {
            this.isDestroyed = true

            this.themeMediaQuery.removeEventListener(
                'change',
                this.themeMediaQueryChangeHandler,
            )
            this.chartDataEventTarget.removeEventListener(
                'updateChartData',
                this.chartDataListener,
            )
            Alpine.release(this.themeEffect)
            this.getChart()?.destroy()
        },

        initChart: function (data = null) {
            if (
                !this.$refs.canvas ||
                !this.$refs.backgroundColorElement ||
                !this.$refs.borderColorElement ||
                !this.$refs.textColorElement ||
                !this.$refs.gridColorElement
            ) {
                return
            }

            Chart.defaults.animation.duration = 0

            Chart.defaults.backgroundColor = getComputedStyle(
                this.$refs.backgroundColorElement,
            ).color

            const borderColor = getComputedStyle(
                this.$refs.borderColorElement,
            ).color

            Chart.defaults.borderColor = borderColor

            Chart.defaults.color = getComputedStyle(
                this.$refs.textColorElement,
            ).color

            Chart.defaults.font.family = getComputedStyle(this.$el).fontFamily

            Chart.defaults.plugins.legend.labels.boxWidth = 12
            Chart.defaults.plugins.legend.position = 'bottom'

            const gridColor = getComputedStyle(
                this.$refs.gridColorElement,
            ).color

            options ??= {}
            options.borderWidth ??= 2
            options.pointBackgroundColor ??= borderColor
            options.pointHitRadius ??= 4
            options.pointRadius ??= 2
            options.scales ??= {}
            options.scales.x ??= {}
            options.scales.x.grid ??= {}
            options.scales.x.grid.color ??= gridColor
            options.scales.x.grid.display ??= false
            options.scales.x.grid.drawBorder ??= false
            options.scales.y ??= {}
            options.scales.y.grid ??= {}
            options.scales.y.grid.color ??= gridColor
            options.scales.y.grid.drawBorder ??= false

            return new Chart(this.$refs.canvas, {
                type: type,
                data: data ?? cachedData,
                options: options,
                plugins: window.filamentChartJsPlugins ?? [],
            })
        },

        getChart: function () {
            if (!this.$refs.canvas) {
                return null
            }

            return Chart.getChart(this.$refs.canvas)
        },
    }
}
