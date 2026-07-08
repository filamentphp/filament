import {
    CategoryScale,
    Chart,
    Filler,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
} from 'chart.js'

Chart.register(
    CategoryScale,
    Filler,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
)

export default function statsOverviewStatChart({ key, labels, values }) {
    return {
        key,

        init() {
            this.$wire.$on('updateStatsOverviewChartData', (event) => {
                if (event.key === this.key) {
                    this.updateChartData(event.data)
                }
            })

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
            // exits early on that first run.
            this.$nextTick(() => this.initChart())
        },

        initChart() {
            if (
                !this.$refs.canvas ||
                !this.$refs.backgroundColorElement ||
                !this.$refs.borderColorElement
            ) {
                return
            }

            // Defensively tear down any pre-existing chart bound to this canvas before
            // constructing a new one (the canvas is reused if the component re-initializes).
            this.getChart()?.destroy()

            const { backgroundColor, borderColor } = this.getChartColors()

            new Chart(this.$refs.canvas, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            data: values,
                            borderWidth: 2,
                            fill: 'start',
                            tension: 0.5,
                            backgroundColor,
                            borderColor,
                        },
                    ],
                },
                options: {
                    animation: {
                        duration: 0,
                    },
                    elements: {
                        point: {
                            radius: 0,
                        },
                    },
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            enabled: false,
                        },
                    },
                    scales: {
                        x: {
                            display: false,
                        },
                        y: {
                            display: false,
                        },
                    },
                },
            })
        },

        updateChartData(newValues) {
            const chart = this.getChart()

            if (!chart) {
                return
            }

            chart.data.labels = newValues.map((value, index) => index)
            chart.data.datasets[0].data = newValues
            chart.update('none')
        },

        updateChartTheme() {
            const chart = this.getChart()

            if (!chart) {
                return
            }

            const { backgroundColor, borderColor } = this.getChartColors()

            chart.data.datasets[0].backgroundColor = backgroundColor
            chart.data.datasets[0].borderColor = borderColor
            chart.update('none')
        },

        getChart() {
            if (!this.$refs.canvas) {
                return null
            }

            return Chart.getChart(this.$refs.canvas)
        },

        getChartColors() {
            return {
                backgroundColor: getComputedStyle(
                    this.$refs.backgroundColorElement,
                ).color,
                borderColor: getComputedStyle(this.$refs.borderColorElement)
                    .color,
            }
        },

        destroy() {
            this.systemThemeMediaQuery?.removeEventListener(
                'change',
                this.systemThemeListener,
            )
            this.getChart()?.destroy()
        },
    }
}
