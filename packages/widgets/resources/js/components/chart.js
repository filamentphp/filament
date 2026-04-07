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

            this.$nextTick(() => this._initChart())

            this.resizeObserver = new ResizeObserver(
                Alpine.debounce(() => this.whenChart((chart) => chart.resize()), 250)
            )
            this.$nextTick(() => this.resizeObserver.observe(this.$el))
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

            const chartOptions = { ...(options ?? {}) }
            chartOptions.backgroundColor ??= backgroundColor
            chartOptions.borderColor ??= borderColor
            chartOptions.color ??= textColor
            chartOptions.font ??= {}
            chartOptions.font.family ??= fontFamily
            chartOptions.borderWidth ??= 2
            chartOptions.responsive ??= false
            chartOptions.maintainAspectRatio ??= hasMaxHeight
            chartOptions.pointBackgroundColor ??= borderColor
            chartOptions.pointHitRadius ??= 4
            chartOptions.pointRadius ??= 2
            chartOptions.scales ??= {}
            chartOptions.scales.x ??= {}
            chartOptions.scales.x.border ??= {}
            chartOptions.scales.x.border.display ??= false
            chartOptions.scales.x.grid ??= {}
            chartOptions.scales.x.grid.color ??= gridColor
            chartOptions.scales.x.grid.display ??= false
            chartOptions.scales.y ??= {}
            chartOptions.scales.y.border ??= {}
            chartOptions.scales.y.border.display ??= false
            chartOptions.scales.y.grid ??= {}
            chartOptions.scales.y.grid.color ??= gridColor

            if (['doughnut', 'pie', 'polarArea'].includes(type)) {
                chartOptions.scales.x.display ??= false
                chartOptions.scales.y.display ??= false
                chartOptions.scales.y.grid.display ??= false
            }

            if (type === 'polarArea') {
                chartOptions.scales.r ??= {}
                chartOptions.scales.r.grid ??= {}
                chartOptions.scales.r.grid.color ??= gridColor
                chartOptions.scales.r.ticks ??= {}
                chartOptions.scales.r.ticks.color ??= textColor
                chartOptions.scales.r.ticks.backdropColor ??= 'transparent'
            }

            new Chart(this.$refs.canvas, {
                type,
                data: cachedData,
                options: chartOptions,
                plugins: window.filamentChartJsPlugins ?? [],
            })
        },

        updateChartData(newData) {
            this.whenChart((chart) => {
                if (
                    typeof newData !== 'object' ||
                    Object.keys(newData).length === 0
                ) {
                    chart.data = {}
                    chart.update()

                    cachedData = {}

                    return
                }

                const newDatasets = Array.isArray(newData.datasets) ? newData.datasets : []
                const cachedDatasets = Array.isArray(cachedData?.datasets) ? cachedData.datasets : []

                const rootKeys = new Set([
                    ...Object.keys(cachedData || {}),
                    ...Object.keys(newData),
                ])

                rootKeys.forEach((key) => {
                    if (key === 'datasets') {
                        return
                    }

                    if (!(key in newData)) {
                        delete chart.data[key]
                    } else {
                        chart.data[key] = newData[key]
                    }
                })

                if (newDatasets.length === 0) {
                    chart.data.datasets.length = 0
                    chart.update()

                    cachedData = { ...newData }

                    return
                }

                for (let i = cachedDatasets.length - 1; i >= 0; i--) {
                    if (!newDatasets[i] && chart.data.datasets[i]) {
                        chart.data.datasets.splice(i, 1)
                    }
                }

                newDatasets.forEach((newDs, index) => {
                    if (!cachedDatasets[index]) {
                        chart.data.datasets[index] = { ...newDs }
                    }
                })

                newDatasets.forEach((newDs, index) => {
                    const cachedDs = cachedDatasets[index]
                    const currentDs = chart.data.datasets[index]

                    if (!cachedDs || !currentDs) return

                    const dsKeys = new Set([
                        ...Object.keys(cachedDs),
                        ...Object.keys(newDs),
                    ])

                    dsKeys.forEach((key) => {
                        if (!(key in newDs)) {
                            delete currentDs[key]

                            return
                        }

                        if (key === 'data') {
                            const newArr = Array.isArray(newDs.data) ? newDs.data : []

                            if (!Array.isArray(currentDs.data)) {
                                currentDs.data = []
                            }

                            currentDs.data.length = newArr.length

                            for (let i = 0; i < newArr.length; i++) {
                                currentDs.data[i] = newArr[i]
                            }

                            return
                        }

                        currentDs[key] = newDs[key]
                    })
                })

                chart.update()
                cachedData = { ...newData }
            })
        },

        updateChartTheme() {
            this.whenChart((chart) => {
                const { backgroundColor, borderColor, textColor, gridColor } = this.getChartFallbackColors()

                chart.options.backgroundColor = options?.backgroundColor ?? backgroundColor
                chart.options.borderColor = options?.borderColor ?? borderColor
                chart.options.color = options?.color ?? textColor
                chart.options.pointBackgroundColor = options?.pointBackgroundColor ?? borderColor
                chart.options.scales.x.grid.color = options?.scales?.x?.grid?.color ?? gridColor
                chart.options.scales.y.grid.color = options?.scales?.y?.grid?.color ?? gridColor

                if (type === 'polarArea') {
                    chart.options.scales.r.grid.color = options?.scales?.r?.grid?.color ?? gridColor
                    chart.options.scales.r.ticks.color = options?.scales?.r?.ticks?.color ?? textColor
                }

                chart.update('none')
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
            if (this.resizeObserver) {
                this.resizeObserver.disconnect()
            }

            this.getChart()?.destroy()
        },
    }
}
