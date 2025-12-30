import Chart from 'chart.js/auto'
import 'chartjs-adapter-luxon'

if (
    window.filamentChartJsGlobalPlugins &&
    Array.isArray(window.filamentChartJsGlobalPlugins) &&
    window.filamentChartJsGlobalPlugins.length > 0
) {
    Chart.register(...window.filamentChartJsGlobalPlugins)
}

export default function chart({ cachedData, options, type }) {
    return {
        userPointBackgroundColor: options?.pointBackgroundColor,
        userXGridColor: options?.scales?.x?.grid?.color,
        userYGridColor: options?.scales?.y?.grid?.color,
        userRadialGridColor: options?.scales?.r?.grid?.color,
        userRadialTicksColor: options?.scales?.r?.ticks?.color,

        init() {
            this.initChart()

            this.$wire.$on('updateChartData', ({ data }) => {
                cachedData = data
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

            this.resizeHandler = Alpine.debounce(() => {
                this.getChart().destroy()
                this.initChart()
            }, 250)

            window.addEventListener('resize', this.resizeHandler)

            this.resizeObserver = new ResizeObserver(() => this.resizeHandler())
            this.resizeObserver.observe(this.$el)
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
            options.maintainAspectRatio ??= false
            options.pointBackgroundColor =
                this.userPointBackgroundColor ?? borderColor
            options.pointHitRadius ??= 4
            options.pointRadius ??= 2
            options.scales ??= {}
            options.scales.x ??= {}
            options.scales.x.border ??= {}
            options.scales.x.border.display ??= false
            options.scales.x.grid ??= {}
            options.scales.x.grid.color = this.userXGridColor ?? gridColor
            options.scales.x.grid.display ??= false
            options.scales.y ??= {}
            options.scales.y.border ??= {}
            options.scales.y.border.display ??= false
            options.scales.y.grid ??= {}
            options.scales.y.grid.color = this.userYGridColor ?? gridColor

            if (['doughnut', 'pie', 'polarArea'].includes(type)) {
                options.scales.x.display ??= false
                options.scales.y.display ??= false
                options.scales.y.grid.display ??= false
            }

            if (type === 'polarArea') {
                const textColor = getComputedStyle(
                    this.$refs.textColorElement,
                ).color

                options.scales.r ??= {}
                options.scales.r.grid ??= {}
                options.scales.r.grid.color =
                    this.userRadialGridColor ?? gridColor
                options.scales.r.ticks ??= {}
                options.scales.r.ticks.color =
                    this.userRadialTicksColor ?? textColor
                options.scales.r.ticks.backdropColor ??= 'transparent'
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

        destroy() {
            window.removeEventListener('resize', this.resizeHandler)

            if (this.resizeObserver) {
                this.resizeObserver.disconnect()
            }

            this.getChart()?.destroy()
        },
    }
}
