import Chart from 'chart.js/auto'
import 'chartjs-adapter-luxon'

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
            this.$wire.$on('updateChartData', ({ data }) => this.updateChartData(data))

            Alpine.effect(() => {
                Alpine.store('theme')

                this.$nextTick(() => this.updateChartTheme())
            })

            window
                .matchMedia('(prefers-color-scheme: dark)')
                .addEventListener('change', () => {
                    if (Alpine.store('theme') !== 'system') {
                        return
                    }

                    this.$nextTick(() => this.updateChartTheme())
                })

            this.$nextTick(() => {
                this._initChart()
                this.resizeObserver = new ResizeObserver(() => this.resizeChart())
                this.resizeObserver.observe(this.$el)
            })
        },

        _initChart() {
            if (
                !this.$refs.canvas ||
                !this.$refs.backgroundColorElement ||
                !this.$refs.borderColorElement ||
                !this.$refs.textColorElement ||
                !this.$refs.gridColorElement
            ) {
                return
            }

            const { backgroundColor, borderColor, textColor, gridColor } = this.getChartFallbackColors()
            const fontFamily = getComputedStyle(this.$el).fontFamily
            const hasMaxHeight = this.$refs.canvas.style.maxHeight !== '100%'

            options ??= {}
            options.backgroundColor = this.userBackgroundColor ?? backgroundColor
            options.borderColor = this.userBorderColor ?? borderColor
            options.color = this.userTextColor ?? textColor
            options.font ??= {}
            options.font.family ??= fontFamily
            options.borderWidth ??= 2
            options.responsive ??= false
            options.maintainAspectRatio ??= hasMaxHeight
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
                options.scales.r ??= {}
                options.scales.r.grid ??= {}
                options.scales.r.grid.color =
                    this.userRadialGridColor ?? gridColor
                options.scales.r.ticks ??= {}
                options.scales.r.ticks.color =
                    this.userRadialTicksColor ?? textColor
                options.scales.r.ticks.backdropColor ??= 'transparent'
            }

            new Chart(this.$refs.canvas, {
                type,
                data: cachedData,
                options,
                plugins: window.filamentChartJsPlugins ?? [],
            })
        },

        updateChartData(newData) {
            this.whenChart((chart) => {
                cachedData = newData
                chart.data = cachedData
                chart.update('resize')
            })
        },

        updateChartTheme() {
            this.whenChart((chart) => {
                const { backgroundColor, borderColor, textColor, gridColor } = this.getChartFallbackColors()

                chart.options.backgroundColor = this.userBackgroundColor ?? backgroundColor
                chart.options.borderColor = this.userBorderColor ?? borderColor
                chart.options.color = this.userTextColor ?? textColor
                chart.options.pointBackgroundColor = this.userPointBackgroundColor ?? borderColor
                chart.options.scales.x.grid.color = this.userXGridColor ?? gridColor
                chart.options.scales.y.grid.color = this.userYGridColor ?? gridColor

                if (type === 'polarArea') {
                    chart.options.scales.r.grid.color = this.userRadialGridColor ?? gridColor
                    chart.options.scales.r.ticks.color = this.userRadialTicksColor ?? textColor
                }

                chart.update('none')
            })
        },

        resizeChart() {
            this.whenChart((chart) => {
                chart.resize()
            })
        },

        whenChart(callback) {
            const chart = this.getChart()

            if (!chart) {
                return
            }

            callback(chart)
        },

        getChart() {
            if (!this.$refs.canvas) {
                return null
            }

            return Chart.getChart(this.$refs.canvas)
        },

        getChartFallbackColors() {
            return {
                backgroundColor: getComputedStyle(this.$refs.backgroundColorElement).color,
                borderColor: getComputedStyle(this.$refs.borderColorElement).color,
                textColor: getComputedStyle(this.$refs.textColorElement).color,
                gridColor: getComputedStyle(this.$refs.gridColorElement).color,
            }
        },

        destroy() {
            this.resizeObserver?.disconnect()
            this.getChart()?.destroy()
        },
    }
}
