import Chart from 'chart.js/auto'

export default function statsOverviewStatChart({
    dataChecksum,
    labels,
    values,
}) {
    let isDestroyed = false

    return {
        dataChecksum,

        themeEffect: null,

        themeMediaQuery: null,

        themeMediaQueryChangeHandler: null,

        init: function () {
            this.themeEffect = Alpine.effect(() => {
                if (isDestroyed) {
                    return
                }

                Alpine.store('theme')

                const chart = this.getChart()

                if (chart) {
                    chart.destroy()
                }

                this.initChart()
            })

            this.themeMediaQuery = window.matchMedia(
                '(prefers-color-scheme: dark)',
            )
            this.themeMediaQueryChangeHandler = () => {
                if (Alpine.store('theme') !== 'system') {
                    return
                }

                this.$nextTick(() => {
                    if (isDestroyed) {
                        return
                    }

                    const chart = this.getChart()

                    if (chart) {
                        chart.destroy()
                    }

                    this.initChart()
                })
            }
            this.themeMediaQuery.addEventListener(
                'change',
                this.themeMediaQueryChangeHandler,
            )
        },

        destroy: function () {
            isDestroyed = true
            this.themeMediaQuery.removeEventListener(
                'change',
                this.themeMediaQueryChangeHandler,
            )
            Alpine.release(this.themeEffect)
            this.getChart()?.destroy()
        },

        initChart: function () {
            if (
                !this.$refs.canvas ||
                !this.$refs.backgroundColorElement ||
                !this.$refs.borderColorElement
            ) {
                return
            }

            return new Chart(this.$refs.canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            data: values,
                            borderWidth: 2,
                            fill: 'start',
                            tension: 0.5,
                            backgroundColor: getComputedStyle(
                                this.$refs.backgroundColorElement,
                            ).color,
                            borderColor: getComputedStyle(
                                this.$refs.borderColorElement,
                            ).color,
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
                    },
                    scales: {
                        x: {
                            display: false,
                        },
                        y: {
                            display: false,
                        },
                    },
                    tooltips: {
                        enabled: false,
                    },
                },
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
