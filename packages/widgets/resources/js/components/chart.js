import Chart from 'chart.js/auto'
import 'chartjs-adapter-luxon'

export default function chart({ cachedData, options, type }) {
    return {
        init() {
            this.initChart()

            this.$wire.$on('updateChartData', ({ data }) => {
                chart = this.getChart()
                chart.data = data
                chart.update('resize')
            })

            Alpine.effect(() => {
                Alpine.store('theme')

                this.$nextTick(() => {
                    if (!this.getChart()) {
                        return
                    }

                    this.getChart().destroy()
                    this.initChart()
                })
            })

            window
                .matchMedia('(prefers-color-scheme: dark)')
                .addEventListener('change', () => {
                    if (Alpine.store('theme') !== 'system') {
                        return
                    }

                    this.$nextTick(() => {
                        this.getChart().destroy()
                        this.initChart()
                    })
                })
        },

        initChart(data = null) {
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
            options.scales.x.border ??= {}
            options.scales.x.border.display ??= false
            options.scales.x.grid ??= {}
            options.scales.x.grid.color ??= gridColor
            options.scales.x.grid.display ??= false
            options.scales.y ??= {}
            options.scales.y.border ??= {}
            options.scales.y.border.display ??= false
            options.scales.y.grid ??= {}
            options.scales.y.grid.color ??= gridColor

            if (['doughnut', 'pie'].includes(type)) {
                options.scales.x.display ??= false
                options.scales.y.display ??= false
                options.scales.y.grid.display ??= false
            }

            return new Chart(this.$refs.canvas, {
                type,
                data: data ?? cachedData,
                options,
                plugins: window.filamentChartJsPlugins ?? [],
            })
        },

        getChart() {
            if (!this.$refs.canvas) {
                return null
            }

            return Chart.getChart(this.$refs.canvas)
        },
    }
}
