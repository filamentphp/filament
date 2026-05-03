import Chart from 'chart.js/auto'

export default function statsOverviewStatChart({
    dataChecksum,
    labels,
    values,
}) {
    return {
        dataChecksum,

        init() {
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

            // Alpine re-initializes this component when `dataChecksum` changes (on data
            // updates from Livewire polling). The canvas is reused, so any prior Chart.js
            // instance must be torn down before constructing a new one.
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
