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
            const { borderWidth, fill, tension } = this.getChartVars()

            new Chart(this.$refs.canvas, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            data: values,
                            borderWidth,
                            fill,
                            tension,
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

            // The custom properties are re-read too: a theme may legitimately
            // give light and dark mode a different line.
            const { borderWidth, fill, tension } = this.getChartVars()

            chart.data.datasets[0].backgroundColor = backgroundColor
            chart.data.datasets[0].borderColor = borderColor
            chart.data.datasets[0].borderWidth = borderWidth
            chart.data.datasets[0].fill = fill
            chart.data.datasets[0].tension = tension
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

        // The sparkline paints onto a bare `<canvas>`, so the shape of its line
        // is unreachable from a stylesheet. These custom properties are the
        // bridge; unset, each falls back to the value the sparkline has always
        // used.
        getChartVars() {
            const styles = getComputedStyle(this.$el)

            const read = (property) =>
                styles.getPropertyValue(property).trim() || null

            const number = (value, fallback) => {
                const parsed = parseFloat(value)

                return Number.isNaN(parsed) ? fallback : parsed
            }

            const fill = read('--stat-chart-fill')

            return {
                borderWidth: number(read('--stat-chart-border-width'), 2),
                // Chart.js needs boolean `false` to drop the area fill, and a
                // custom property can only ever yield the string `'false'`,
                // which is truthy — hence the `none` keyword.
                fill: fill === null ? 'start' : fill === 'none' ? false : fill,
                tension: number(read('--stat-chart-line-tension'), 0.5),
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
