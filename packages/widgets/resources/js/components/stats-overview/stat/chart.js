import {
    CategoryScale,
    Chart,
    Filler,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
} from 'chart.js'

import readCustomProperties from '../../../custom-properties'

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
            const { borderWidth, fill, lineTension } =
                this.getChartCustomProperties()

            new Chart(this.$refs.canvas, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            data: values,
                            borderWidth,
                            fill,
                            tension: lineTension,
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

            // The custom properties are re-read too: a theme may legitimately give light
            // and dark mode a different line.
            const { borderWidth, fill, lineTension } =
                this.getChartCustomProperties()

            chart.data.datasets[0].backgroundColor = backgroundColor
            chart.data.datasets[0].borderColor = borderColor
            chart.data.datasets[0].borderWidth = borderWidth
            chart.data.datasets[0].fill = fill
            chart.data.datasets[0].tension = lineTension

            // `'resize'` rather than `'none'`, for the reason given in the chart widget.
            chart.update('resize')
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

        // The parts of the sparkline that a stylesheet cannot otherwise reach, since
        // Chart.js paints them onto a bare `<canvas>`. Each fallback is the value the
        // sparkline has always used.
        getChartCustomProperties() {
            const { number, keyword } = readCustomProperties(this.$el)

            return {
                borderWidth: number('--stat-chart-border-width', 2),
                fill: keyword('--stat-chart-fill', 'start'),
                lineTension: number('--stat-chart-line-tension', 0.5),
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
