import Chart from 'chart.js/auto'

export default function statsOverviewStatChart({
    dataChecksum,
    labels,
    values,
}) {
    return {
        dataChecksum,

        init: function () {
            Alpine.effect(() => {
                Alpine.store('theme')

                const chart = this.getChart()

                if (chart) {
                    chart.destroy()
                }

                this.initChart()
            })

            window
                .matchMedia('(prefers-color-scheme: dark)')
                .addEventListener('change', () => {
                    if (Alpine.store('theme') !== 'system') {
                        return
                    }

                    this.$nextTick(() => {
                        const chart = this.getChart()

                        if (chart) {
                            chart.destroy()
                        }

                        this.initChart()
                    })
                })
        },

        initChart: function () {
            Chart.defaults.backgroundColor = getComputedStyle(
                this.$refs.backgroundColorElement,
            ).color

            Chart.defaults.borderColor = getComputedStyle(
                this.$refs.borderColorElement,
            ).color

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
            return Chart.getChart(this.$refs.canvas)
        },
    }
}
